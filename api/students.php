<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

try {
    $pdo = db();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?: [];

    if ($method === 'GET') {
        $q = trim((string)($_GET['q'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));
        $sql = "SELECT s.*, b.name AS batch_name FROM students s LEFT JOIN batches b ON b.id=s.batch_id WHERE 1=1";
        $params = [];
        if ($q !== '') {
            $sql .= " AND (s.student_code LIKE :q OR s.first_name LIKE :q OR s.last_name LIKE :q OR s.mobile LIKE :q)";
            $params['q'] = "%{$q}%";
        }
        if ($status !== '') {
            $sql .= " AND s.status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY s.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        jsonResponse(true, $stmt->fetchAll());
    }

    if ($method === 'POST') {
        $required = ['first_name'];
        foreach ($required as $field) {
            if (trim((string)($input[$field] ?? '')) === '') jsonResponse(false, null, "$field is required", 422);
        }

        $code = trim((string)($input['student_code'] ?? ''));
        if ($code === '') $code = 'ST-' . date('ymdHis') . random_int(10, 99);

        $sql = "INSERT INTO students
            (student_code,first_name,last_name,gender,date_of_birth,mobile,email,address,admission_date,batch_id,belt,emergency_contact_name,emergency_contact_mobile,status,notes)
            VALUES (:student_code,:first_name,:last_name,:gender,:date_of_birth,:mobile,:email,:address,:admission_date,:batch_id,:belt,:emergency_contact_name,:emergency_contact_mobile,:status,:notes)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'student_code'=>$code,
            'first_name'=>trim((string)$input['first_name']),
            'last_name'=>nullIfEmpty($input['last_name'] ?? null),
            'gender'=>nullIfEmpty($input['gender'] ?? null),
            'date_of_birth'=>nullIfEmpty($input['date_of_birth'] ?? null),
            'mobile'=>nullIfEmpty($input['mobile'] ?? null),
            'email'=>nullIfEmpty($input['email'] ?? null),
            'address'=>nullIfEmpty($input['address'] ?? null),
            'admission_date'=>nullIfEmpty($input['admission_date'] ?? null) ?? date('Y-m-d'),
            'batch_id'=>nullInt($input['batch_id'] ?? null),
            'belt'=>nullIfEmpty($input['belt'] ?? null) ?? 'White Belt',
            'emergency_contact_name'=>nullIfEmpty($input['emergency_contact_name'] ?? null),
            'emergency_contact_mobile'=>nullIfEmpty($input['emergency_contact_mobile'] ?? null),
            'status'=>nullIfEmpty($input['status'] ?? null) ?? 'Active',
            'notes'=>nullIfEmpty($input['notes'] ?? null)
        ]);
        jsonResponse(true, ['id'=>(int)$pdo->lastInsertId(),'student_code'=>$code], 'Student created', 201);
    }

    if ($method === 'PUT') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) jsonResponse(false, null, 'Valid student id is required', 422);

        $sql = "UPDATE students SET first_name=:first_name,last_name=:last_name,gender=:gender,date_of_birth=:date_of_birth,
            mobile=:mobile,email=:email,address=:address,admission_date=:admission_date,batch_id=:batch_id,belt=:belt,
            emergency_contact_name=:emergency_contact_name,emergency_contact_mobile=:emergency_contact_mobile,status=:status,notes=:notes
            WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'=>$id,
            'first_name'=>trim((string)($input['first_name'] ?? '')),
            'last_name'=>nullIfEmpty($input['last_name'] ?? null),
            'gender'=>nullIfEmpty($input['gender'] ?? null),
            'date_of_birth'=>nullIfEmpty($input['date_of_birth'] ?? null),
            'mobile'=>nullIfEmpty($input['mobile'] ?? null),
            'email'=>nullIfEmpty($input['email'] ?? null),
            'address'=>nullIfEmpty($input['address'] ?? null),
            'admission_date'=>nullIfEmpty($input['admission_date'] ?? null) ?? date('Y-m-d'),
            'batch_id'=>nullInt($input['batch_id'] ?? null),
            'belt'=>nullIfEmpty($input['belt'] ?? null) ?? 'White Belt',
            'emergency_contact_name'=>nullIfEmpty($input['emergency_contact_name'] ?? null),
            'emergency_contact_mobile'=>nullIfEmpty($input['emergency_contact_mobile'] ?? null),
            'status'=>nullIfEmpty($input['status'] ?? null) ?? 'Active',
            'notes'=>nullIfEmpty($input['notes'] ?? null)
        ]);
        jsonResponse(true, null, 'Student updated');
    }

    if ($method === 'DELETE') {
        $id = (int)($_GET['id'] ?? $input['id'] ?? 0);
        if ($id <= 0) jsonResponse(false, null, 'Valid student id is required', 422);
        $stmt = $pdo->prepare("DELETE FROM students WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        jsonResponse(true, null, $stmt->rowCount() ? 'Student deleted' : 'Student not found');
    }

    jsonResponse(false, null, 'Method not allowed', 405);
} catch (Throwable $e) {
    error_log($e->getMessage());
    jsonResponse(false, null, 'Database/API error. Check PHP error log.', 500);
}

function nullIfEmpty($value) {
    if ($value === null) return null;
    $value = trim((string)$value);
    return $value === '' ? null : $value;
}
function nullInt($value) {
    return ($value === null || $value === '') ? null : (int)$value;
}

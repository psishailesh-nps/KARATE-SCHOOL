<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'YOUR_DATABASE_NAME';
const DB_USER = 'YOUR_DATABASE_USER';
const DB_PASS = 'YOUR_DATABASE_PASSWORD';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    return $pdo;
}

function jsonResponse(bool $success, $data=null, string $message='', int $status=200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['success'=>$success,'data'=>$data,'message'=>$message], JSON_UNESCAPED_UNICODE);
    exit;
}

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode','1');
    ini_set('session.use_only_cookies','1');
    ini_set('session.cookie_httponly','1');
    ini_set('session.cookie_samesite','Lax');
    session_start();
}

function requireLogin(): void {
    startSecureSession();
    if (empty($_SESSION['user_id'])) jsonResponse(false,null,'Authentication required',401);
}

function nullIfEmpty($v) {
    if ($v === null) return null;
    $v = trim((string)$v);
    return $v === '' ? null : $v;
}

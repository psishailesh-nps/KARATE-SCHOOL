<?php
declare(strict_types=1);

/*
 * Jetpur Karate Team - MySQL configuration
 * Upload this file to your PHP hosting and replace the placeholders.
 * Keep this file outside public access if your hosting supports it.
 */
const DB_HOST = 'localhost';
const DB_NAME = 'YOUR_DATABASE_NAME';
const DB_USER = 'YOUR_DATABASE_USER';
const DB_PASS = 'YOUR_DATABASE_PASSWORD';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function jsonResponse(bool $success, $data = null, string $message = '', int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode(['success'=>$success, 'data'=>$data, 'message'=>$message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') jsonResponse(true, null, 'OK');

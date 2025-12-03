<?php
// config.php

// Đọc từ ENV trước (Render), nếu không có thì dùng giá trị mặc định (local)
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: 3306;
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'lichviet';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Không thể kết nối CSDL: ' . $conn->connect_error
    ]);
    exit;
}

$conn->set_charset('utf8mb4');

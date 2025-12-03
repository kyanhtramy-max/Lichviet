<?php

$DB_HOST = getenv('DB_HOST');
$DB_PORT = getenv('DB_PORT') ?: '5432';
$DB_NAME = getenv('DB_NAME');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_SSLMODE = getenv('DB_SSLMODE') ?: 'require';

// Format connection string
$conn_string = sprintf(
    "host=%s port=%s dbname=%s user=%s password=%s sslmode=%s",
    $DB_HOST,
    $DB_PORT,
    $DB_NAME,
    $DB_USER,
    $DB_PASS,
    $DB_SSLMODE
);

$conn = pg_connect($conn_string);

if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '❌ Không thể kết nối PostgreSQL',
        'error' => pg_last_error()
    ]);
    exit;
}

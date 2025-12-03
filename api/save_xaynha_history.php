<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$ownerYear = $input['owner_year'] ?? 0;
$buildYear = $input['build_year'] ?? 0;
$kimlau = $input['kimlau'] ?? 0;
$hoangoc = $input['hoangoc'] ?? 0;
$tamtai = $input['tamtai'] ?? 0;
$evaluation = $input['evaluation'] ?? '';
$detail = $input['detail'] ?? '';

try {
    $stmt = $conn->prepare("
        INSERT INTO history_xaynha 
        (user_id, owner_year, build_year, kimlau, hoangoc, tamtai, evaluation, detail) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiiisss", $userId, $ownerYear, $buildYear, $kimlau, $hoangoc, $tamtai, $evaluation, $detail);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã lưu lịch sử']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu lịch sử']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
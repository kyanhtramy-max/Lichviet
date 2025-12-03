<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

// Nhận dữ liệu từ POST request
$input = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$type = $input['type'] ?? '';
$title = $input['title'] ?? '';
$description = $input['description'] ?? '';
$data = $input['data'] ?? [];

if (empty($type) || empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

try {
    // Lưu vào search history (có thể dùng bảng riêng hoặc bảng hiện có)
    // Ở đây chúng ta sẽ cập nhật last_activity trong user_profiles
    $updateStmt = $conn->prepare("
        UPDATE user_profiles 
        SET updated_at = NOW() 
        WHERE user_id = ?
    ");
    $updateStmt->bind_param("i", $userId);
    $updateStmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Đã cập nhật hoạt động']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
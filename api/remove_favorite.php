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
$id = $input['id'] ?? 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
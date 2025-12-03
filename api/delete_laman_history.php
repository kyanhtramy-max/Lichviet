<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;
$userId = $_SESSION['user_id'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // Kiểm tra xem bản ghi có thuộc về người dùng hiện tại không
    $checkStmt = $conn->prepare("SELECT id FROM history_laman WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $id, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy lịch sử hoặc không có quyền xóa']);
        exit;
    }
    
    // Xóa bản ghi
    $stmt = $conn->prepare("DELETE FROM history_laman WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Đã xóa lịch sử làm ăn thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Lỗi khi xóa lịch sử'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
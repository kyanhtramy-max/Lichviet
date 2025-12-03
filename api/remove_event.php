<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$eventId = $data['eventId'] ?? $data['id'] ?? 0;
$userId = $_SESSION['user_id'];

if ($eventId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID sự kiện không hợp lệ']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM user_events WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $eventId, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Đã xóa sự kiện'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Lỗi khi xóa sự kiện: ' . $stmt->error
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
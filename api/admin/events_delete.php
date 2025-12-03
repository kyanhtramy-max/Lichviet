<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Check admin permission
if (!isset($_SESSION['user_id']) || $_SESSION['user']['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

header('Content-Type: application/json');

try {
    $id = intval($input['id']);
    
    // Check if event exists
    $check_sql = "SELECT id FROM system_events WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        throw new Exception('Sự kiện không tồn tại');
    }
    
    $sql = "DELETE FROM system_events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Xóa sự kiện thành công'
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xóa sự kiện: ' . $e->getMessage()
    ]);
}
?>
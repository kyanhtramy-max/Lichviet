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
    $title = $conn->real_escape_string(trim($input['title']));
    $description = $conn->real_escape_string(trim($input['description'] ?? ''));
    $content = $conn->real_escape_string(trim($input['content'] ?? ''));
    $event_type = $conn->real_escape_string($input['event_type']);
    $status = $conn->real_escape_string($input['status']);
    $start_date = $conn->real_escape_string($input['start_date'] ?? null);
    $end_date = $conn->real_escape_string($input['end_date'] ?? null);
    $location = $conn->real_escape_string(trim($input['location'] ?? ''));
    $image_url = $conn->real_escape_string(trim($input['image_url'] ?? ''));
    $is_featured = isset($input['is_featured']) ? intval($input['is_featured']) : 0;

    // Validation
    if (empty($title)) {
        throw new Exception('Vui lòng nhập tiêu đề sự kiện');
    }

    // Validate event type
    $allowed_types = ['community', 'promotion', 'system_update', 'announcement', 'other'];
    if (!in_array($event_type, $allowed_types)) {
        throw new Exception('Loại sự kiện không hợp lệ');
    }

    // Validate status
    $allowed_statuses = ['draft', 'published', 'archived'];
    if (!in_array($status, $allowed_statuses)) {
        throw new Exception('Trạng thái không hợp lệ');
    }

    // Get current status to check if we need to update published_at
    $current_stmt = $conn->prepare("SELECT status FROM system_events WHERE id = ?");
    $current_stmt->bind_param('i', $id);
    $current_stmt->execute();
    $current_result = $current_stmt->get_result();
    $current_event = $current_result->fetch_assoc();
    
    $published_at = null;
    if ($status === 'published' && $current_event['status'] !== 'published') {
        $published_at = date('Y-m-d H:i:s');
    }

    if ($published_at) {
        $sql = "UPDATE system_events SET title = ?, description = ?, content = ?, event_type = ?, status = ?, 
                start_date = ?, end_date = ?, location = ?, image_url = ?, is_featured = ?, published_at = ?, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssssisi', $title, $description, $content, $event_type, $status, $start_date, $end_date, $location, $image_url, $is_featured, $published_at, $id);
    } else {
        $sql = "UPDATE system_events SET title = ?, description = ?, content = ?, event_type = ?, status = ?, 
                start_date = ?, end_date = ?, location = ?, image_url = ?, is_featured = ?, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssssii', $title, $description, $content, $event_type, $status, $start_date, $end_date, $location, $image_url, $is_featured, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Cập nhật sự kiện thành công'
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi cập nhật sự kiện: ' . $e->getMessage()
    ]);
}
?>
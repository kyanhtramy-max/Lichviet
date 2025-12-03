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

if (!$input) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

header('Content-Type: application/json');

try {
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
    $created_by = $_SESSION['user_id'];

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

    // Set published_at if status is published
    $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    $sql = "INSERT INTO system_events (title, description, content, event_type, status, start_date, end_date, location, image_url, is_featured, created_by, published_at, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssssiis', $title, $description, $content, $event_type, $status, $start_date, $end_date, $location, $image_url, $is_featured, $created_by, $published_at);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thêm sự kiện thành công',
            'id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi thêm sự kiện: ' . $e->getMessage()
    ]);
}
?>
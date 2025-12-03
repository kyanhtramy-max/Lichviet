<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../config.php";

// Log để debug
error_log("=== ADD_EVENT.PHP CALLED ===");

if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

// Get JSON data
$input = file_get_contents('php://input');
error_log("Raw input: " . $input);

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Dữ liệu JSON không hợp lệ']);
    exit;
}

error_log("Decoded data: " . print_r($data, true));

$userId = $_SESSION['user_id'];
$title = trim($data['title'] ?? '');
$eventDate = $data['event_date'] ?? $data['date'] ?? '';
$eventTime = $data['event_time'] ?? $data['time'] ?? null;
$description = trim($data['description'] ?? '');
$color = $data['color'] ?? '#3498db';

error_log("Parsed values - User: $userId, Title: $title, Date: $eventDate, Time: " . ($eventTime ?? 'NULL'));

// Validation
if (empty($title)) {
    error_log("Validation failed: Title empty");
    echo json_encode(['success' => false, 'message' => 'Tiêu đề không được để trống']);
    exit;
}

if (empty($eventDate)) {
    error_log("Validation failed: Date empty");
    echo json_encode(['success' => false, 'message' => 'Ngày không được để trống']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) {
    error_log("Validation failed: Invalid date format");
    echo json_encode(['success' => false, 'message' => 'Định dạng ngày không hợp lệ']);
    exit;
}

try {
    // Prepare SQL
    $sql = "INSERT INTO user_events (user_id, title, event_date, event_time, description, color) VALUES (?, ?, ?, ?, ?, ?)";
    error_log("SQL: $sql");
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Handle empty time
    if ($eventTime === '' || $eventTime === null) {
        $eventTime = null;
    }
    
    $stmt->bind_param("isssss", $userId, $title, $eventDate, $eventTime, $description, $color);
    
    if ($stmt->execute()) {
        $eventId = $stmt->insert_id;
        error_log("Event inserted successfully. ID: $eventId");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đã thêm sự kiện thành công!',
            'event_id' => $eventId
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
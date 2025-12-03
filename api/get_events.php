<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("
        SELECT id, title, event_date as date, event_time as time, description, color, created_at 
        FROM user_events 
        WHERE user_id = ? 
        ORDER BY event_date DESC, event_time DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        // Format date for consistency
        $row['date'] = $row['date'];
        $events[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events
    ]);

} catch (Exception $e) {
    error_log("get_events error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
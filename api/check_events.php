<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['has_events' => false]);
    exit;
}

$userId = $_SESSION['user_id'];
$date = $_GET['date'] ?? '';

if (empty($date)) {
    echo json_encode(['has_events' => false]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT COUNT(*) as event_count FROM user_events WHERE user_id = ? AND event_date = ?");
    $stmt->bind_param("is", $userId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'has_events' => $row['event_count'] > 0,
        'count' => $row['event_count']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'has_events' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
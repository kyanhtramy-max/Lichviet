<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['exists' => false]);
    exit;
}

$userId = $_SESSION['user_id'];
$date = $_GET['date'] ?? '';

if (empty($date)) {
    echo json_encode(['exists' => false]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND solar_date = ?");
    $stmt->bind_param("is", $userId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['exists' => true, 'favoriteId' => $row['id']]);
    } else {
        echo json_encode(['exists' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("
        SELECT id, solar_date, lunar_date, rating_text, score, created_at 
        FROM favorites 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $favorites = [];
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'favorites' => $favorites
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
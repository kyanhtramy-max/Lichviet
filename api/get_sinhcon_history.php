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
    // Lấy lịch sử sinh con của user, giới hạn 10 bản ghi mới nhất
    $stmt = $conn->prepare("
        SELECT id, father_year, mother_year, child_year, score, evaluation, detail, created_at 
        FROM history_sinhcon 
        WHERE user_id = ? 
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        // Giải mã JSON detail nếu có
        if (!empty($row['detail'])) {
            $row['detail_decoded'] = json_decode($row['detail'], true);
        } else {
            $row['detail_decoded'] = [];
        }
        $history[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'history' => $history,
        'count' => count($history)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
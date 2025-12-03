<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = $_SESSION['user_id'];
$historyId = $_GET['id'] ?? 0;

if ($historyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT id, father_year, mother_year, child_year, score, evaluation, detail, created_at 
        FROM history_sinhcon 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $historyId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // Giải mã JSON detail
        if (!empty($item['detail'])) {
            $item['detail_decoded'] = json_decode($item['detail'], true);
        } else {
            $item['detail_decoded'] = [];
        }
        
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy lịch sử']);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
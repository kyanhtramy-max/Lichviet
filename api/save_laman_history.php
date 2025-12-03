<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = $_SESSION['user_id'];
$selfYear = $_POST['self_year'] ?? '';
$partnerYear = $_POST['partner_year'] ?? '';
$score = $_POST['score'] ?? 0;
$evaluation = $_POST['evaluation'] ?? '';
$detail = $_POST['detail'] ?? '';

if (empty($selfYear) || empty($partnerYear)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

try {
    // Kiểm tra xem đã có lịch sử cho cặp năm này chưa
    $checkStmt = $conn->prepare("SELECT id FROM history_laman WHERE user_id = ? AND self_year = ? AND partner_year = ?");
    $checkStmt->bind_param("iii", $userId, $selfYear, $partnerYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Cập nhật bản ghi hiện có
        $updateStmt = $conn->prepare("UPDATE history_laman SET score = ?, evaluation = ?, detail = ?, created_at = NOW() WHERE user_id = ? AND self_year = ? AND partner_year = ?");
        $updateStmt->bind_param("issiii", $score, $evaluation, $detail, $userId, $selfYear, $partnerYear);
        $updateStmt->execute();
    } else {
        // Thêm bản ghi mới
        $stmt = $conn->prepare("INSERT INTO history_laman (user_id, self_year, partner_year, score, evaluation, detail, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiiss", $userId, $selfYear, $partnerYear, $score, $evaluation, $detail);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Đã lưu lịch sử']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
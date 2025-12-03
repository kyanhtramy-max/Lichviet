<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$maleYear = intval($input['male_year'] ?? 0);
$femaleYear = intval($input['female_year'] ?? 0);
$score = intval($input['score'] ?? 0);
$evaluation = $conn->real_escape_string($input['evaluation'] ?? '');
$remedies = $conn->real_escape_string($input['remedies'] ?? '');
$detail = $conn->real_escape_string($input['detail'] ?? '');

if ($maleYear <= 0 || $femaleYear <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    // Kiểm tra xem đã có lịch sử cho cặp tuổi này chưa
    $checkStmt = $conn->prepare("SELECT id FROM history_kethon WHERE user_id = ? AND male_year = ? AND female_year = ?");
    $checkStmt->bind_param("iii", $userId, $maleYear, $femaleYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Cập nhật bản ghi hiện có
        $updateStmt = $conn->prepare("UPDATE history_kethon SET score = ?, evaluation = ?, remedies = ?, detail = ?, created_at = NOW() WHERE user_id = ? AND male_year = ? AND female_year = ?");
        $updateStmt->bind_param("isssiii", $score, $evaluation, $remedies, $detail, $userId, $maleYear, $femaleYear);
        $updateStmt->execute();
        $historyId = $checkResult->fetch_assoc()['id'];
    } else {
        // Thêm bản ghi mới
        $insertStmt = $conn->prepare("INSERT INTO history_kethon (user_id, male_year, female_year, score, evaluation, remedies, detail, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("iiiisss", $userId, $maleYear, $femaleYear, $score, $evaluation, $remedies, $detail);
        $insertStmt->execute();
        $historyId = $conn->insert_id;
    }
    
    echo json_encode([
        'success' => true,
        'history_id' => $historyId,
        'message' => 'Đã lưu lịch sử phân tích kết hôn'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
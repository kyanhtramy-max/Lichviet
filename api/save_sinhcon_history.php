<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

// Nhận dữ liệu từ POST request
$input = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$fatherYear = $input['father_year'] ?? '';
$motherYear = $input['mother_year'] ?? '';
$childYear = $input['child_year'] ?? '';
$score = $input['score'] ?? 0;
$evaluation = $input['evaluation'] ?? '';
$detail = $input['detail'] ?? '';

// Validate dữ liệu
if (empty($fatherYear) || empty($motherYear) || empty($childYear)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}

try {
    // Kiểm tra xem đã có bản ghi tương tự chưa (trong vòng 1 ngày)
    $checkStmt = $conn->prepare("
        SELECT id FROM history_sinhcon 
        WHERE user_id = ? AND father_year = ? AND mother_year = ? AND child_year = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
        LIMIT 1
    ");
    $checkStmt->bind_param("iiii", $userId, $fatherYear, $motherYear, $childYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Đã tồn tại, cập nhật bản ghi cũ
        $existing = $checkResult->fetch_assoc();
        $updateStmt = $conn->prepare("
            UPDATE history_sinhcon 
            SET score = ?, evaluation = ?, detail = ?, created_at = NOW() 
            WHERE id = ?
        ");
        $updateStmt->bind_param("issi", $score, $evaluation, $detail, $existing['id']);
        
        if ($updateStmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Đã cập nhật lịch sử tra cứu sinh con',
                'history_id' => $existing['id']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật lịch sử: ' . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        // Chưa tồn tại, tạo bản ghi mới
        $insertStmt = $conn->prepare("
            INSERT INTO history_sinhcon 
            (user_id, father_year, mother_year, child_year, score, evaluation, detail, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $insertStmt->bind_param("iiiiiss", $userId, $fatherYear, $motherYear, $childYear, $score, $evaluation, $detail);
        
        if ($insertStmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Đã lưu lịch sử tra cứu sinh con',
                'history_id' => $insertStmt->insert_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu lịch sử: ' . $insertStmt->error]);
        }
        $insertStmt->close();
    }
    
    $checkStmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
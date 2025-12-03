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
$ownerYear = $input['owner_year'] ?? '';
$gender = $input['gender'] ?? '';
$cungPhi = $input['cung_phi'] ?? '';
$goodDirections = $input['good_directions'] ?? '';
$badDirections = $input['bad_directions'] ?? '';

if (empty($ownerYear) || empty($gender)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin năm sinh hoặc giới tính']);
    exit;
}

try {
    // Chuẩn bị dữ liệu summary
    $summary = "Xem hướng nhà - Năm sinh: {$ownerYear}, Giới tính: " . ($gender === 'male' ? 'Nam' : 'Nữ') . 
               ", Cung phi: {$cungPhi}. Hướng tốt: {$goodDirections}. Hướng xấu: {$badDirections}";
    
    // Chuẩn bị current_direction (lấy hướng tốt đầu tiên)
    $goodDirectionsArray = explode(', ', $goodDirections);
    $currentDirection = !empty($goodDirectionsArray) ? $goodDirectionsArray[0] : '';

    // Kiểm tra xem đã có tra cứu trùng chưa (trong vòng 1 phút)
    $checkStmt = $conn->prepare("
        SELECT id FROM history_huongnha 
        WHERE user_id = ? AND owner_year = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    ");
    $checkStmt->bind_param("ii", $userId, $ownerYear);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Đã lưu lịch sử tra cứu']);
        exit;
    }

    // Lưu vào database
    $stmt = $conn->prepare("
        INSERT INTO history_huongnha 
        (user_id, owner_year, current_direction, good_directions, bad_directions, summary, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iissss", $userId, $ownerYear, $currentDirection, $goodDirections, $badDirections, $summary);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã lưu lịch sử tra cứu thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu lịch sử tra cứu']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
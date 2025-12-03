<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$userId = $_SESSION['user_id'];
$solar = $data['solar'] ?? '';
$lunar = $data['lunar'] ?? '';
$rating = $data['rating'] ?? '';
$score = $data['score'] ?? 0;

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Format lại ngày dương lịch từ DD-MM-YYYY sang YYYY-MM-DD
    $solarDateFormatted = '';
    if (!empty($solar) && strpos($solar, '-') !== false) {
        $parts = explode('-', $solar);
        if (count($parts) === 3) {
            if (strlen($parts[0]) === 2) { // DD-MM-YYYY
                $solarDateFormatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            } else if (strlen($parts[0]) === 4) { // YYYY-MM-DD
                $solarDateFormatted = $solar;
            }
        }
    }
    
    // Format lại ngày âm lịch
    $lunarDateFormatted = $lunar; // Giữ nguyên format DD-MM-YYYY

    // 1. Thêm vào favorites
    $stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_id, solar_date, lunar_date, rating_text, score) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $userId, $solarDateFormatted, $lunarDateFormatted, $rating, $score);
    $stmt->execute();
    
    $favoriteAdded = $stmt->affected_rows > 0;
    
    // 2. LUÔN LUÔN thêm vào history_xemngay khi thêm vào yêu thích
    if ($favoriteAdded && !empty($solarDateFormatted)) {
        // Kiểm tra xem đã có trong history chưa
        $checkStmt = $conn->prepare("SELECT id FROM history_xemngay WHERE user_id = ? AND query_date = ?");
        $checkStmt->bind_param("is", $userId, $solarDateFormatted);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        // Nếu chưa có thì thêm mới
        if ($checkResult->num_rows === 0) {
            // Parse thông tin từ rating text để lấy đánh giá
            $ratingEnum = 'binh_thuong';
            if (strpos($rating, 'tốt') !== false || strpos($rating, 'TỐT') !== false || stripos($rating, 'cát lợi') !== false) {
                $ratingEnum = 'tot';
            } else if (strpos($rating, 'xấu') !== false || strpos($rating, 'XẤU') !== false || stripos($rating, 'bất lợi') !== false) {
                $ratingEnum = 'xau';
            }
            
            // Thêm vào history_xemngay
            $historyStmt = $conn->prepare("
                INSERT INTO history_xemngay 
                (user_id, query_date, lunar_date, rating, note, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $historyStmt->bind_param("issss", $userId, $solarDateFormatted, $lunarDateFormatted, $ratingEnum, $rating);
            $historyStmt->execute();
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'added' => $favoriteAdded,
        'message' => $favoriteAdded ? 'Đã thêm vào yêu thích và cập nhật lịch sử' : 'Đã có trong yêu thích'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
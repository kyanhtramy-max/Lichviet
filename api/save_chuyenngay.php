<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$userId         = $_SESSION['user_id'];
$input_type     = $input['input_type'] ?? '';
$input_date     = $input['input_date'] ?? '';
$converted_type = $input['converted_type'] ?? '';
$converted_value= $input['converted_value'] ?? '';
$note           = $input['note'] ?? '';

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Format lại ngày tháng cho đúng với database
    $inputDateFormatted = '';
    $convertedValueFormatted = $converted_value;
    
    // Format input_date từ YYYY-MM-DD (nếu cần)
    if (!empty($input_date)) {
        $inputDateFormatted = $input_date;
    }
    
    // Format converted_value nếu là ngày dương
    if ($converted_type === 'duong' && !empty($converted_value)) {
        if (strpos($converted_value, '-') !== false) {
            $parts = explode('-', $converted_value);
            if (count($parts) === 3) {
                if (strlen($parts[0]) === 2) { // DD-MM-YYYY -> YYYY-MM-DD
                    $convertedValueFormatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
            }
        }
    }

    // 1. Lưu vào history_chuyenngay
    $stmt = $conn->prepare("
        INSERT INTO history_chuyenngay 
        (user_id, input_type, input_date, converted_type, converted_value, note) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssss", $userId, $input_type, $inputDateFormatted, $converted_type, $convertedValueFormatted, $note);
    $stmt->execute();
    
    // 2. LUÔN LUÔN thêm vào history_xemngay khi chuyển đổi (cả Dương->Âm và Âm->Dương)
    $query_date = '';
    $lunar_date = '';
    
    if ($input_type === 'duong' && $converted_type === 'am') {
        // Dương -> Âm: input_date là dương lịch, converted_value là âm lịch
        $query_date = $inputDateFormatted;
        $lunar_date = $converted_value;
    } else if ($input_type === 'am' && $converted_type === 'duong') {
        // Âm -> Dương: converted_value là dương lịch, input_date là âm lịch
        $query_date = $convertedValueFormatted;
        $lunar_date = $input_date;
    }
    
    // Nếu có dữ liệu để lưu vào history_xemngay
    if (!empty($query_date) && !empty($lunar_date)) {
        // Kiểm tra xem đã có trong history_xemngay chưa
        $checkStmt = $conn->prepare("SELECT id FROM history_xemngay WHERE user_id = ? AND query_date = ?");
        $checkStmt->bind_param("is", $userId, $query_date);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        // Nếu chưa có thì thêm mới
        if ($checkResult->num_rows === 0) {
            // Parse đánh giá từ note
            $ratingEnum = 'binh_thuong';
            if (strpos($note, 'tốt') !== false || strpos($note, 'TỐT') !== false || stripos($note, 'cát lợi') !== false) {
                $ratingEnum = 'tot';
            } else if (strpos($note, 'xấu') !== false || strpos($note, 'XẤU') !== false || stripos($note, 'bất lợi') !== false) {
                $ratingEnum = 'xau';
            }
            
            // Thêm vào history_xemngay
            $historyStmt = $conn->prepare("
                INSERT INTO history_xemngay 
                (user_id, query_date, lunar_date, rating, note, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $historyStmt->bind_param("issss", $userId, $query_date, $lunar_date, $ratingEnum, $note);
            $historyStmt->execute();
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã lưu lịch sử chuyển đổi'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
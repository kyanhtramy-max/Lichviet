<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Chưa đăng nhập',
        'debug' => 'No user_id in session',
        'history' => []
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
error_log("🔍 GET Birth History - User ID: $userId");

try {
    // Kiểm tra kết nối database
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? 'Unknown'));
    }

    // Kiểm tra bảng tồn tại
    $tableCheck = $conn->query("SHOW TABLES LIKE 'history_ngaysinh'");
    if ($tableCheck->num_rows == 0) {
        error_log("❌ Bảng history_ngaysinh không tồn tại");
        echo json_encode([
            'success' => true,
            'message' => 'Bảng không tồn tại',
            'debug' => 'Table not found',
            'history' => []
        ]);
        exit;
    }

    // Kiểm tra dữ liệu trong bảng
    $countQuery = $conn->query("SELECT COUNT(*) as total FROM history_ngaysinh WHERE user_id = $userId");
    $countData = $countQuery->fetch_assoc();
    error_log("📊 Tổng số bản ghi cho user $userId: " . $countData['total']);

    // Lấy dữ liệu
    $stmt = $conn->prepare("
        SELECT id, birth_date, lunar_date, zodiac, destiny, summary, created_at 
        FROM history_ngaysinh 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    error_log("✅ Trả về " . count($history) . " bản ghi cho user: $userId");
    
    // DEBUG: Log một bản ghi mẫu
    if (count($history) > 0) {
        error_log("📝 Bản ghi mẫu: " . json_encode($history[0]));
    }
    
    echo json_encode([
        'success' => true,
        'history' => $history,
        'count' => count($history),
        'debug' => [
            'user_id' => $userId,
            'table_exists' => true,
            'records_found' => count($history)
        ]
    ]);

} catch (Exception $e) {
    error_log("❌ Lỗi get_birth_history: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'history' => [],
        'debug' => 'Exception caught'
    ]);
}

$conn->close();
?>
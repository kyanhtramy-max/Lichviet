<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Load config
$configPaths = [
    __DIR__ . '/../config.php',
    __DIR__ . '/../../config.php', 
];

foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

// Kiểm tra kết nối database
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$userId = $_SESSION['user_id'];
$birthDate = $input['birth_date'] ?? '';
$lunarDate = $input['lunar_date'] ?? '';
$zodiac = $input['zodiac'] ?? '';
$napAm = $input['nap_am'] ?? '';
$summary = $input['summary'] ?? '';

error_log("Lưu lịch sử - User: $userId, Date: $birthDate");

try {
    // Kiểm tra bảng tồn tại
    $tableCheck = $conn->query("SHOW TABLES LIKE 'history_ngaysinh'");
    if ($tableCheck->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Bảng lịch sử chưa được tạo']);
        exit;
    }

    // Kiểm tra cấu trúc bảng
    $columnsCheck = $conn->query("SHOW COLUMNS FROM history_ngaysinh LIKE 'lunar_date'");
    if ($columnsCheck->num_rows == 0) {
        error_log("Cột lunar_date không tồn tại trong bảng");
        echo json_encode(['success' => false, 'message' => 'Cấu trúc bảng không hợp lệ - thiếu cột lunar_date']);
        exit;
    }

    // Sửa câu lệnh SQL cho đúng với cấu trúc bảng
    $stmt = $conn->prepare("
        INSERT INTO history_ngaysinh 
        (user_id, birth_date, lunar_date, zodiac, destiny, summary, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("isssss", $userId, $birthDate, $lunarDate, $zodiac, $napAm, $summary);
    
    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
        error_log("✅ Đã lưu lịch sử - ID: $insertId");
        echo json_encode([
            'success' => true, 
            'message' => 'Đã lưu lịch sử', 
            'id' => $insertId
        ]);
    } else {
        throw new Exception("Lỗi execute: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("❌ Lỗi: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
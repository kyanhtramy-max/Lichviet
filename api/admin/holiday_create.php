<?php
// Bật báo lỗi chi tiết
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Đảm bảo không có output nào trước header
if (ob_get_length()) ob_clean();

session_start();
require_once __DIR__ . '/../../config.php';

// Check database connection FIRST
if (!isset($conn) || $conn === null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . ($db_connection_error ?? 'Unknown error')
    ]);
    exit;
}

// Check admin permission
if (!isset($_SESSION['user_id']) || $_SESSION['user']['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

header('Content-Type: application/json');

try {
    $name = trim($input['name']);
    $description = trim($input['description'] ?? '');
    $type = $input['type'];
    $is_lunar = $input['is_lunar'] ? 1 : 0;
    $is_recurring = 1;
    $is_active = 1;

    // Validate required fields
    if (empty($name)) {
        throw new Exception('Tên ngày lễ không được để trống');
    }

    // Validate type
    $allowed_types = ['national', 'religious', 'traditional', 'other'];
    if (!in_array($type, $allowed_types)) {
        throw new Exception('Loại ngày lễ không hợp lệ');
    }

    if ($is_lunar) {
        // Xử lý ngày âm lịch
        $lunar_month = intval($input['lunar_month']);
        $lunar_day = intval($input['lunar_day']);
        
        // Validate lunar date
        if ($lunar_month < 1 || $lunar_month > 12) {
            throw new Exception('Tháng âm lịch không hợp lệ (1-12)');
        }
        if ($lunar_day < 1 || $lunar_day > 30) {
            throw new Exception('Ngày âm lịch không hợp lệ (1-30)');
        }
        
        // Đối với âm lịch, solar_date = NULL, chỉ lưu lunar_month và lunar_day
        $sql = "INSERT INTO holidays (name, description, type, is_lunar, lunar_month, lunar_day, is_recurring, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiiiii', $name, $description, $type, $is_lunar, $lunar_month, $lunar_day, $is_recurring, $is_active);
    } else {
        // Xử lý ngày dương lịch
        $solar_date = $input['solar_date'];
        
        if (empty($solar_date)) {
            throw new Exception('Vui lòng chọn ngày dương lịch');
        }
        
        // Tách năm ra, chỉ lưu tháng và ngày
        $date_parts = explode('-', $solar_date);
        if (count($date_parts) !== 3) {
            throw new Exception('Định dạng ngày không hợp lệ');
        }
        
        $solar_month = intval($date_parts[1]);
        $solar_day = intval($date_parts[2]);
        
        // Validate solar date
        if ($solar_month < 1 || $solar_month > 12) {
            throw new Exception('Tháng dương lịch không hợp lệ');
        }
        if ($solar_day < 1 || $solar_day > 31) {
            throw new Exception('Ngày dương lịch không hợp lệ');
        }
        
        // Kiểm tra ngày hợp lệ
        if (!checkdate($solar_month, $solar_day, 2000)) { // Dùng năm nhuận để kiểm tra
            throw new Exception('Ngày tháng không hợp lệ');
        }
        
        $sql = "INSERT INTO holidays (name, description, type, is_lunar, solar_month, solar_day, is_recurring, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiiiii', $name, $description, $type, $is_lunar, $solar_month, $solar_day, $is_recurring, $is_active);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thêm ngày lễ thành công',
            'id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi thêm ngày lễ: ' . $e->getMessage()
    ]);
}
?>
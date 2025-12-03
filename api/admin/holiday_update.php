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

if (!$input || !isset($input['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

header('Content-Type: application/json');

try {
    $id = intval($input['id']);
    $name = trim($input['name']);
    $description = trim($input['description'] ?? '');
    $type = $input['type'];
    $is_lunar = $input['is_lunar'] ? 1 : 0;

    // Validate required fields
    if (empty($name)) {
        throw new Exception('Tên ngày lễ không được để trống');
    }

    // Validate type
    $allowed_types = ['national', 'religious', 'traditional', 'other'];
    if (!in_array($type, $allowed_types)) {
        throw new Exception('Loại ngày lễ không hợp lệ');
    }

    // Check if holiday exists
    $check_sql = "SELECT id FROM holidays WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        throw new Exception('Ngày lễ không tồn tại');
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
        
        $sql = "UPDATE holidays SET name = ?, description = ?, type = ?, is_lunar = ?, 
                lunar_month = ?, lunar_day = ?, solar_month = NULL, solar_day = NULL, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiiii', $name, $description, $type, $is_lunar, $lunar_month, $lunar_day, $id);
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
        if (!checkdate($solar_month, $solar_day, 2000)) {
            throw new Exception('Ngày tháng không hợp lệ');
        }
        
        $sql = "UPDATE holidays SET name = ?, description = ?, type = ?, is_lunar = ?, 
                solar_month = ?, solar_day = ?, lunar_month = NULL, lunar_day = NULL, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiiii', $name, $description, $type, $is_lunar, $solar_month, $solar_day, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Cập nhật ngày lễ thành công'
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi cập nhật ngày lễ: ' . $e->getMessage()
    ]);
}
?>
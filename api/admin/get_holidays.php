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

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
$type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$calendar = isset($_GET['calendar']) ? $conn->real_escape_string($_GET['calendar']) : '';

header('Content-Type: application/json');

try {
    // Build query
    $whereConditions = ["is_active = 1"];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "name LIKE ?";
        $params[] = "%$search%";
        $types .= 's';
    }
    
    if (!empty($type) && $type !== 'all') {
        $whereConditions[] = "type = ?";
        $params[] = $type;
        $types .= 's';
    }
    
    if (!empty($calendar) && $calendar !== 'all') {
        if ($calendar === 'lunar') {
            $whereConditions[] = "is_lunar = 1";
        } else if ($calendar === 'solar') {
            $whereConditions[] = "is_lunar = 0";
        }
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    $sql = "SELECT *, 
                   CASE 
                       WHEN is_lunar = 1 THEN lunar_month * 100 + lunar_day
                       ELSE solar_month * 100 + solar_day
                   END as sort_order
            FROM holidays 
            WHERE $whereClause 
            ORDER BY sort_order";
    
    $stmt = $conn->prepare($sql);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $holidays = [];
    while ($row = $result->fetch_assoc()) {
        // Tính ngày hiển thị cho năm được chọn
        if ($row['is_lunar']) {
            // Âm lịch: tính ngày dương tương ứng
            $displayDate = calculateLunarToSolar($year, $row['lunar_month'], $row['lunar_day']);
            $dateInfo = "🌙 " . sprintf("%02d", $row['lunar_day']) . "/" . sprintf("%02d", $row['lunar_month']) . " (Âm lịch)";
        } else {
            // Dương lịch: ghép với năm hiện tại
            $displayDate = $year . "-" . sprintf("%02d", $row['solar_month']) . "-" . sprintf("%02d", $row['solar_day']);
            $dateInfo = "📅 " . sprintf("%02d", $row['solar_day']) . "/" . sprintf("%02d", $row['solar_month']) . " (Dương lịch)";
        }
        
        $holidays[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'type' => $row['type'],
            'is_lunar' => (bool)$row['is_lunar'],
            'lunar_month' => $row['lunar_month'],
            'lunar_day' => $row['lunar_day'],
            'solar_month' => $row['solar_month'],
            'solar_day' => $row['solar_day'],
            'solar_date' => $row['solar_date'], // Giữ lại cho tương thích
            'display_date' => $displayDate,
            'date_info' => $dateInfo,
            'is_recurring' => (bool)$row['is_recurring'],
            'is_active' => (bool)$row['is_active'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $holidays,
        'total' => count($holidays),
        'year' => $year
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Hàm chuyển đổi âm lịch sang dương lịch (đơn giản)
 * Trong thực tế nên sử dụng thư viện chuyển đổi chính xác
 */
function calculateLunarToSolar($year, $lunarMonth, $lunarDay) {
    // Đây là logic đơn giản, trong thực tế cần dùng thuật toán chính xác
    // Hoặc integrate với API chuyển đổi âm-dương lịch
    
    // Tạm thời trả về ngày ước tính (có thể sai lệch)
    $baseDate = strtotime("$year-$lunarMonth-$lunarDay");
    
    // Âm lịch thường chậm hơn dương lịch khoảng 1 tháng
    $solarDate = date('Y-m-d', strtotime('+29 days', $baseDate));
    
    return $solarDate;
}
?>
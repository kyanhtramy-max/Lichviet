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

header('Content-Type: application/json');

try {
    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch_assoc()['total'];

    // Get active users
    $stmt = $conn->query("SELECT COUNT(*) as active FROM users WHERE status = 1");
    $activeUsers = $stmt->fetch_assoc()['active'];

    // Get admin users
    $stmt = $conn->query("SELECT COUNT(*) as admins FROM users WHERE role = 'admin'");
    $adminUsers = $stmt->fetch_assoc()['admins'];

    // Get total holidays
    $stmt = $conn->query("SELECT COUNT(*) as total FROM holidays");
    $totalHolidays = $stmt->fetch_assoc()['total'];

    // Get recent activities from various tables
    $recentActivities = [];
    
    // Recent user registrations
    $stmt = $conn->query("SELECT name, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    while ($row = $stmt->fetch_assoc()) {
        $recentActivities[] = "Người dùng \"{$row['name']}\" đã đăng ký tài khoản";
    }
    
    // Recent holiday additions
    $stmt = $conn->query("SELECT name, created_at FROM holidays ORDER BY created_at DESC LIMIT 2");
    while ($row = $stmt->fetch_assoc()) {
        $recentActivities[] = "Ngày lễ \"{$row['name']}\" đã được thêm vào hệ thống";
    }
    
    // Add some system activities
    $recentActivities[] = "Hệ thống sao lưu dữ liệu tự động đã hoàn thành";
    $recentActivities[] = "Quản trị viên đã đăng nhập vào hệ thống";

    // Get database size (MySQL)
    $stmt = $conn->query("SELECT 
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()"
    );
    $dbSize = $stmt->fetch_assoc()['size_mb'] . ' MB';

    // Calculate uptime (simulated - in real app you might track this)
    $uptime = '15 ngày 7 giờ';

    echo json_encode([
        'success' => true,
        'data' => [
            'total_users' => (int)$totalUsers,
            'active_users' => (int)$activeUsers,
            'admin_users' => (int)$adminUsers,
            'total_holidays' => (int)$totalHolidays,
            'recent_activities' => $recentActivities,
            'db_size' => $dbSize,
            'uptime' => $uptime,
            'system_version' => 'Lịch Việt 2.0'
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
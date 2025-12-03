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

// Đảm bảo chỉ trả về JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    if (!$stmt) {
        throw new Exception('Lỗi query users: ' . $conn->error);
    }
    $totalUsers = $stmt->fetch_assoc()['total'];

    // Get active users
    $stmt = $conn->query("SELECT COUNT(*) as active FROM users WHERE status = 1");
    if (!$stmt) {
        throw new Exception('Lỗi query active users: ' . $conn->error);
    }
    $activeUsers = $stmt->fetch_assoc()['active'];

    // Get admin users
    $stmt = $conn->query("SELECT COUNT(*) as admins FROM users WHERE role = 'admin'");
    if (!$stmt) {
        throw new Exception('Lỗi query admin users: ' . $conn->error);
    }
    $adminUsers = $stmt->fetch_assoc()['admins'];

    // Get total holidays
    $totalHolidays = 0;
    $stmt = $conn->query("SELECT COUNT(*) as total FROM holidays");
    if ($stmt) {
        $totalHolidays = $stmt->fetch_assoc()['total'];
    }

    // Get total events
    $totalEvents = 0;
    $publishedEvents = 0;
    $featuredEvents = 0;
    $stmt = $conn->query("SELECT COUNT(*) as total FROM system_events");
    if ($stmt) {
        $totalEvents = $stmt->fetch_assoc()['total'];
    }
    
    $stmt = $conn->query("SELECT COUNT(*) as published FROM system_events WHERE status = 'published'");
    if ($stmt) {
        $publishedEvents = $stmt->fetch_assoc()['published'];
    }
    
    $stmt = $conn->query("SELECT COUNT(*) as featured FROM system_events WHERE is_featured = 1");
    if ($stmt) {
        $featuredEvents = $stmt->fetch_assoc()['featured'];
    }

    // Get recent activities
    $recentActivities = [];
    
    // Recent user registrations (last 3)
    $stmt = $conn->query("SELECT name, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $time = date('H:i', strtotime($row['created_at']));
            $recentActivities[] = [
                'time' => $time,
                'message' => "Người dùng \"{$row['name']}\" đã đăng ký tài khoản mới"
            ];
        }
    }

    // Get database size (MySQL)
    $dbSize = 'N/A';
    $stmt = $conn->query("SELECT 
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()"
    );
    if ($stmt && $result = $stmt->fetch_assoc()) {
        $dbSize = $result['size_mb'] . ' MB';
    }

    // System uptime (simulated)
    $uptime = 'Không xác định';

    $response = [
        'success' => true,
        'data' => [
            'total_users' => (int)$totalUsers,
            'active_users' => (int)$activeUsers,
            'admin_users' => (int)$adminUsers,
            'total_holidays' => (int)$totalHolidays,
            'total_events' => (int)$totalEvents,
            'published_events' => (int)$publishedEvents,
            'featured_events' => (int)$featuredEvents,
            'recent_activities' => $recentActivities,
            'db_size' => $dbSize,
            'uptime' => $uptime
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'message' => 'Lỗi tải thống kê: ' . $e->getMessage()
    ];
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
}
?>
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
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $role = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : 'all';
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'all';

    // Build query
    $whereConditions = ["1=1"];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    
    if ($role !== 'all') {
        $whereConditions[] = "role = ?";
        $params[] = $role;
        $types .= 's';
    }
    
    if ($status !== 'all') {
        $whereConditions[] = "status = ?";
        $params[] = $status;
        $types .= 'i';
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    $sql = "SELECT id, name, email, role, status, created_at 
            FROM users 
            WHERE $whereClause 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'status' => (bool)$row['status'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $users,
        'total' => count($users)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
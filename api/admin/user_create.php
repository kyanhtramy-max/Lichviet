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
    $name = $conn->real_escape_string(trim($input['name']));
    $email = $conn->real_escape_string(trim($input['email']));
    $password = $input['password'];
    $role = $conn->real_escape_string($input['role']);
    $status = isset($input['status']) ? intval($input['status']) : 1;

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email không hợp lệ');
    }

    if (strlen($password) < 6) {
        throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        throw new Exception('Email đã tồn tại trong hệ thống');
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (name, email, password_hash, role, status, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $name, $email, $password_hash, $role, $status);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thêm người dùng thành công',
            'id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi thêm người dùng: ' . $e->getMessage()
    ]);
}
?>
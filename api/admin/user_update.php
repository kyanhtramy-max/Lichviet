<?php
// Không in lỗi ra trình duyệt (tránh phá JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../../config.php';

// Kiểm tra CSDL
if (!isset($conn) || $conn === null || $conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($db_connection_error ?? ($conn->connect_error ?? 'Unknown error'))
    ]);
    exit;
}

// Chỉ admin được quyền sửa
if (!isset($_SESSION['user_id']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Chỉ cho phép POST (fetch)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Đọc JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $id     = (int)$input['id'];
    $name   = trim($input['name'] ?? '');
    $email  = trim($input['email'] ?? '');
    $role   = $input['role'] ?? 'user';
    $status = isset($input['status']) ? (int)$input['status'] : 1;

    if ($id <= 0 || $name === '' || $email === '') {
        throw new Exception('Vui lòng nhập đầy đủ thông tin');
    }

    // Kiểm tra email trùng (ngoại trừ chính user đang sửa)
    $check_sql  = "SELECT id FROM users WHERE email = ? AND id <> ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception('Lỗi prepare (check email): ' . $conn->error);
    }
    $check_stmt->bind_param('si', $email, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        throw new Exception('Email đã tồn tại trong hệ thống');
    }

    // Có đổi mật khẩu?
    if (!empty($input['password'])) {
        if (strlen($input['password']) < 6) {
            throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
        }

        $password_hash = password_hash($input['password'], PASSWORD_DEFAULT);
        $sql  = "UPDATE users 
                 SET name = ?, email = ?, password_hash = ?, role = ?, status = ?, updated_at = NOW() 
                 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Lỗi prepare (update with pass): ' . $conn->error);
        }
        $stmt->bind_param('ssssii', $name, $email, $password_hash, $role, $status, $id);
    } else {
        $sql  = "UPDATE users 
                 SET name = ?, email = ?, role = ?, status = ?, updated_at = NOW() 
                 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Lỗi prepare (update no pass): ' . $conn->error);
        }
        $stmt->bind_param('sssii', $name, $email, $role, $status, $id);
    }

    if (!$stmt->execute()) {
        throw new Exception('Lỗi execute: ' . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật người dùng thành công'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi cập nhật người dùng: ' . $e->getMessage()
    ]);
}

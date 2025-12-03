<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ');
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        throw new Exception('Vui lòng nhập email và mật khẩu');
    }

    $stmt = $conn->prepare("
        SELECT u.id, u.name, u.email, u.password_hash, u.role, u.status, u.created_at,
               up.phone, up.dob as birthday, up.gender
        FROM users u
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE u.email = ?
        LIMIT 1
    ");
    
    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception('Email hoặc mật khẩu không đúng');
    }

    if ((int)$user['status'] !== 1) {
        throw new Exception('Tài khoản đã bị khóa hoặc chưa kích hoạt');
    }

    if (!password_verify($password, $user['password_hash'])) {
        throw new Exception('Email hoặc mật khẩu không đúng');
    }

    // QUAN TRỌNG: Xóa session cũ và tạo mới
    session_regenerate_id(true);
    
    // Đăng nhập thành công - SET SESSION
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user'] = [
        'id'       => (int)$user['id'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'role'     => $user['role'],
        'status'   => (int)$user['status']
    ];

    // Đảm bảo session được lưu
    session_write_close();

    $response['success'] = true;
    $response['message'] = 'Đăng nhập thành công';
    $response['user'] = [
        'id'       => (int)$user['id'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'phone'    => $user['phone'] ?? null,
        'birthday' => $user['birthday'] ?? null,
        'gender'   => $user['gender'] ?? null,
        'role'     => $user['role'],
        'joined'   => $user['created_at'],
    ];

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
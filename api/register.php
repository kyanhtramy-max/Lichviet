<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ');
    }

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        throw new Exception('Vui lòng nhập đầy đủ thông tin');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email không hợp lệ');
    }

    if (strlen($password) < 6) {
        throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
    }

    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception('Email đã được sử dụng');
    }
    $stmt->close();

    // Hash mật khẩu
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $role = 'user';

    // SỬA LỖI: Thêm created_at và tạo profile
    $created_at = date('Y-m-d H:i:s');
    $role = 'user';

    // BỎ COMMENT dòng hash mật khẩu (dòng ~49)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password_hash, role, created_at)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị câu lệnh INSERT: ' . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $email, $hash, $role, $created_at);
    $stmt->execute();

    $userId = $stmt->insert_id;
    $stmt->close();

    // TẠO BẢN GHI TRONG user_profiles
    $stmt = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // (tuỳ chọn) tạo bản ghi rỗng trong user_profiles nếu bạn có bảng này
    // $stmt = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
    // $stmt->bind_param("i", $userId);
    // $stmt->execute();
    // $stmt->close();

    $_SESSION['user_id'] = $userId;

    $response['success'] = true;
    $response['message'] = 'Đăng ký thành công!';
    $response['user'] = [
        'id'       => $userId,
        'name'     => $name,
        'email'    => $email,
        'role'     => $role,
        'phone'    => null,
        'birthday' => null,
        'gender'   => null,
        // tạm thời cho front-end hiển thị, còn ngày thật lấy từ created_at khi get_current_user
        'joined'   => date('Y-m-d H:i:s'),
    ];

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

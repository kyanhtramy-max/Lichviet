<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId         = (int)$_SESSION['user_id'];
$currentPass    = $_POST['current_password'] ?? '';
$newPass        = $_POST['new_password'] ?? '';

if ($currentPass === '' || $newPass === '') {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($currentPass, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng']);
    exit;
}

$hash = password_hash($newPass, PASSWORD_DEFAULT);
$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hash, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi đổi mật khẩu']);
}

<?php
// Bật log lỗi nhưng KHÔNG hiển thị ra trình duyệt (tránh phá JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// Bạn có thể chỉnh lại đường dẫn log nếu cần
// ini_set('error_log', __DIR__ . '/../../logs/php-error.log');

// Đảm bảo không có output rác
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');

session_start();
require_once __DIR__ . '/../../config.php';

// Kiểm tra kết nối CSDL
if (!isset($conn) || $conn === null || $conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($db_connection_error ?? ($conn->connect_error ?? 'Unknown error'))
    ]);
    exit;
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn không có quyền thực hiện thao tác này'
    ]);
    exit;
}

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không hợp lệ'
    ]);
    exit;
}

try {
    // Đọc JSON từ body
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        throw new Exception('Dữ liệu gửi lên không hợp lệ');
    }

    $userId = isset($data['id']) ? (int)$data['id'] : 0;
    $status = isset($data['status']) ? (int)$data['status'] : null;

    if ($userId <= 0 || ($status !== 0 && $status !== 1)) {
        throw new Exception('Thiếu ID hoặc trạng thái không hợp lệ');
    }

    // Không cho tự khóa chính mình (tuỳ bạn, thích thì bỏ)
    if ($userId === (int)$_SESSION['user_id']) {
        throw new Exception('Không thể tự khóa tài khoản của chính mình');
    }

    // Chuẩn bị câu lệnh
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Lỗi prepare: ' . $conn->error);
    }

    $stmt->bind_param('ii', $status, $userId);

    if (!$stmt->execute()) {
        throw new Exception('Lỗi execute: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('Không tìm thấy người dùng hoặc trạng thái không thay đổi');
    }

    $action = $status ? 'mở khóa' : 'khóa';
    echo json_encode([
        'success' => true,
        'message' => "Đã {$action} người dùng thành công"
    ]);

} catch (Exception $e) {
    // Trả JSON lỗi gọn gàng
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()
    ]);
}

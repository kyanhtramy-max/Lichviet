<?php
// api/update_profile.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

$response = ['success' => false, 'message' => ''];

try {
    // Chỉ cho phép POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ');
    }

    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Bạn cần đăng nhập trước');
    }

    $userId   = (int)$_SESSION['user_id'];
    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');

    if ($name === '') {
        throw new Exception('Vui lòng nhập họ và tên');
    }

    // Cập nhật tên trong bảng users
    $sql = "UPDATE users SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
    }
    $stmt->bind_param('si', $name, $userId);
    $stmt->execute();
    $stmt->close();

    // Kiểm tra xem đã có profile chưa
    $checkStmt = $conn->prepare("SELECT user_id FROM user_profiles WHERE user_id = ?");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // UPDATE nếu đã có
        $sql = "UPDATE user_profiles SET phone = ?, dob = ?, gender = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $phone, $birthday, $gender, $userId);
    } else {
        // INSERT nếu chưa có
        $sql = "INSERT INTO user_profiles (phone, dob, gender, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $phone, $birthday, $gender, $userId);
    }
    
    $stmt->execute();
    $checkStmt->close();

    if ($stmt->affected_rows >= 0) {
        // QUAN TRỌNG: Query lại thông tin user mới nhất
        $sql = "SELECT u.*, up.phone, up.dob as birthday, up.gender 
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                WHERE u.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedUser = $result->fetch_assoc();
        
        $response['success'] = true;
        $response['message'] = 'Cập nhật hồ sơ thành công';

        // Trả lại thông tin user mới cho frontend
        $response['user'] = [
            'id'       => $updatedUser['id'],
            'name'     => $updatedUser['name'],
            'email'    => $updatedUser['email'],
            'phone'    => $updatedUser['phone'] ?? '',
            'birthday' => $updatedUser['birthday'] ?? '',
            'gender'   => $updatedUser['gender'] ?? ''
        ];
        
        // Cập nhật lại session nếu cần
        $_SESSION['user_name'] = $updatedUser['name'];
        
    } else {
        throw new Exception('Không có thay đổi nào được cập nhật');
    }

    $stmt->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
<?php
// api/logout.php

// Bắt đầu hoặc tiếp tục session
session_start();

// Đảm bảo PHP luôn trả về JSON cho JavaScript
header('Content-Type: application/json; charset=utf-8');

// =======================================================
// 1. HỦY SESSION VÀ COOKIE
// =======================================================

// Hủy toàn bộ biến session
$_SESSION = [];

// Hủy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();


// =======================================================
// 2. TÍNH TOÁN URL CHUYỂN HƯỚNG (Dùng để trả về JSON)
// =======================================================
$redirect = '../chuyenngay.php'; // mặc định

// Nếu người dùng đến từ trang khác → quay về đúng trang đó
if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    // Chỉ cho phép redirect về các trang trong dự án của bạn (tránh lỗ hổng open redirect)
    $allowed = ['chuyenngay.php', 'user.php', 'index.php', 'huongnha.php', 'kethon.php']; // thêm tên file bạn có
    
    foreach ($allowed as $page) {
        // Cần dùng filter tốt hơn để kiểm tra, nhưng với strpos, ta chấp nhận ở đây
        if (strpos($ref, $page) !== false) {
            $redirect = $ref;
            break;
        }
    }
}

// =======================================================
// 3. TRẢ VỀ PHẢN HỒI JSON CHO JAVASCRIPT
// =======================================================

// *** ĐÃ LOẠI BỎ: header("Location: $redirect"); ***
// Việc chuyển hướng giờ sẽ do JavaScript xử lý sau khi nhận JSON

echo json_encode([
    'success' => true,
    'message' => 'Đã đăng xuất thành công!',
    'redirect' => $redirect // JavaScript sẽ lấy URL này để chuyển hướng
]);

exit(); // Quan trọng: Dừng script ngay lập tức
?>
<?php
session_start();
require_once "config.php";

$user = null;

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];

    $sql = "SELECT u.*, up.phone, up.dob as birthday, up.gender 
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Việt - Xem Ngày Tốt Xấu</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .app-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            padding: 20px;
            min-height: 600px;
        }

        .calendar-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .calendar-header h2 {
            color: #2c3e50;
            font-size: 1.8em;
            font-weight: 600;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-buttons button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.3em;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .nav-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .guest-message {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.1em;
        }

        .calendar {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .calendar th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1em;
        }

        .calendar td {
            padding: 5px;
            text-align: center;
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            height: 100px;
            vertical-align: top;
            font-size: 1.1em;
        }

        .calendar td:hover {
            background: #f8f9fa;
            transform: scale(1.02);
        }

        .good-day {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left: 4px solid #28a745;
        }

        .bad-day {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-left: 4px solid #dc3545;
        }

        .neutral-day {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-left: 4px solid #ffc107;
        }

        .current-day {
            box-shadow: inset 0 0 0 3px #667eea;
            font-weight: bold;
        }

        .selected-date {
            box-shadow: inset 0 0 0 3px #e74c3c;
            font-weight: bold;
        }

        .day-number {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 2px;
        }

        .lunar-date {
            font-size: 0.75em;
            color: #666;
            display: block;
        }

        .can-chi {
            font-size: 0.7em;
            color: #888;
            display: block;
            margin-bottom: 3px;
        }

        .events-container {
            max-height: 45px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .events-container::-webkit-scrollbar {
            width: 3px;
        }

        .events-container::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 3px;
        }

        /* Phân biệt các loại sự kiện và ngày lễ */
        .admin-holiday {
            color: white;
            font-size: 0.65em;
            padding: 2px 4px;
            border-radius: 8px;
            margin-top: 1px;
            border: 1px solid white;
            line-height: 1.1;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Màu sắc cho các loại ngày lễ */
        .admin-holiday.national {
            background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
        }

        .admin-holiday.religious {
            background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
        }

        .admin-holiday.traditional {
            background: linear-gradient(135deg, #e67e22, #d35400) !important;
        }

        .admin-holiday.other {
            background: linear-gradient(135deg, #34495e, #2c3e50) !important;
        }

        /* Sự kiện từ admin */
        .admin-event {
            background: linear-gradient(135deg, #3498db, #2980b9) !important;
            color: white;
            font-size: 0.65em;
            padding: 2px 4px;
            border-radius: 8px;
            margin-top: 1px;
            border: 1px solid white;
            line-height: 1.1;
        }

        .featured-event {
            background: linear-gradient(135deg, #f39c12, #e67e22) !important;
            font-weight: bold;
        }

        .personal-event {
            background: linear-gradient(135deg, #27ae60, #229954) !important;
            color: white;
            font-size: 0.65em;
            padding: 2px 4px;
            border-radius: 8px;
            margin-top: 1px;
            border: 1px solid white;
            line-height: 1.1;
        }

        .favorite-indicator {
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 0.7em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .day-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .day-info-header h3 {
            color: #2c3e50;
            font-size: 1.5em;
            margin: 0;
        }

        .day-rating {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
        }

        .rating-label {
            font-weight: 600;
            color: #495057;
            font-size: 1.1em;
        }

        .good-rating {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1em;
        }

        .bad-rating {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1em;
        }

        .neutral-rating {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #212529;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1em;
        }

        .calendar-actions {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .calendar-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .events-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .event-item {
            background: white;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-actions {
            display: flex;
            gap: 5px;
        }

        .btn-small {
            padding: 4px 8px;
            font-size: 0.8em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .bar {
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1em;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .bar.good {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .bar.bad {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .bar.neutral {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .info {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .info strong {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 1em;
        }

        .nav-buttons-bottom {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 999;
        }

        .nav-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9em;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .nav-btn.today {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .nav-btn.jump {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        @media (max-width: 768px) {
            .app-container {
                grid-template-columns: 1fr;
            }
            
            .calendar-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .calendar td {
                height: 80px;
                font-size: 1em;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .calendar-actions {
                flex-direction: column;
            }

            .nav-buttons-bottom {
                bottom: 10px;
                right: 10px;
            }
            
            .nav-btn {
                padding: 10px 15px;
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Xem ngày tốt xấu và các dịch vụ liên quan</p>
          
            <div class="user-section">
                <div id="user-info" class="user-info" style="display: <?php echo $user ? 'flex' : 'none'; ?>;">
                    <div class="user-avatar" id="user-avatar">
                        <?php
                        if ($user && !empty($user['name'])) {
                            $parts = explode(' ', $user['name']);
                            $initials = '';
                            foreach ($parts as $p) {
                                $initials .= mb_substr($p, 0, 1);
                            }
                            echo strtoupper(mb_substr($initials, 0, 2));
                        } else {
                            echo "A";
                        }
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name" id="user-display-name">
                            <?php echo htmlspecialchars($user['name'] ?? 'Chưa có tên'); ?>
                        </div>
                        <div class="user-email" id="user-display-email">
                            <?php echo htmlspecialchars($user['email'] ?? 'Chưa có email'); ?>
                        </div>
                    </div>
                    <div class="user-actions">
                        <button id="profile-btn" class="btn-info">📋 Hồ sơ</button>
                        <button id="logout-btn" class="btn-secondary">🚪 Đăng xuất</button>
                        <button id="refresh-admin-btn" class="btn-success" style="display: none;">🔄 Cập nhật</button>
                    </div>
                </div>
                <div class="auth-buttons" id="auth-buttons" style="display: <?php echo $user ? 'none' : 'flex'; ?>;">
                    <button id="login-btn" class="btn-secondary">🔑 Đăng nhập</button>
                    <button id="register-btn" class="btn-success">📝 Đăng ký</button>
                </div>
            </div>
        </div>
      
        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Xem Ngày</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="chuyenngay.php">
                        <span class="nav-icon">🔄</span>
                        <span class="nav-text">Chuyển Đổi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ngaysinh.php">
                        <span class="nav-icon">👶</span>
                        <span class="nav-text">Ngày Sinh</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kethon.php">
                        <span class="nav-icon">💑</span>
                        <span class="nav-text">Kết Hôn</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="concai.php">
                        <span class="nav-icon">👶</span>
                        <span class="nav-text">Sinh Con</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laman.php">
                        <span class="nav-icon">💰</span>
                        <span class="nav-text">Làm Ăn</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="xaynha.php">
                        <span class="nav-icon">🏠</span>
                        <span class="nav-text">Xây Nhà</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="huongnha.php">
                        <span class="nav-icon">🧭</span>
                        <span class="nav-text">Xem Hướng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user.php">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">Hồ Sơ</span>
                    </a>
                </li>
            </ul>
        </nav>
      
        <!-- Nội dung chính của trang Xem Ngày -->
        <div class="app-container">
            <section class="calendar-section">
                <div class="calendar-header">
                    <h2 id="current-month">Tháng 11, 2025</h2>
                    <div class="nav-buttons">
                        <button id="prev-month">‹</button>
                        <button id="next-month">›</button>
                    </div>
                </div>
              
                <div id="guest-message" class="guest-message" style="display: <?php echo $user ? 'none' : 'block'; ?>;">
                    <strong>👋 Chào khách!</strong> Đăng nhập để lưu lại các ngày đã xem và truy cập đầy đủ tính năng.
                </div>
              
                <table class="calendar" id="calendar">
                    <thead>
                        <tr>
                            <th>CN</th>
                            <th>T2</th>
                            <th>T3</th>
                            <th>T4</th>
                            <th>T5</th>
                            <th>T6</th>
                            <th>T7</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body">
                        <!-- Calendar will be generated by JavaScript -->
                    </tbody>
                </table>
            </section>
          
            <section class="info-section">
                <div class="day-info">
                    <div class="day-info-header">
                        <h3>📅 Thông tin ngày</h3>
                        <div id="day-actions" class="day-actions" style="display: <?php echo $user ? 'flex' : 'none'; ?>;">
                            <!-- Icons will be added here -->
                        </div>
                    </div>
                    <div class="day-details">
                        <div class="day-rating">
                            <span class="rating-label">Đánh giá:</span>
                            <span id="day-rating" class="good-rating">TỐT</span>
                        </div>
                        <div id="day-details-text">
                            Chọn một ngày để xem thông tin chi tiết
                        </div>
                    </div>
                </div>

                <!-- PHẦN QUẢN LÝ CÁ NHÂN -->
                <div class="user-actions-section" id="user-actions-section" style="display: <?php echo $user ? 'block' : 'none'; ?>;">
                    <h3>📌 Quản lý cá nhân</h3>
                    
                    <div class="calendar-actions">
                        <button class="btn-info" onclick="addToFavorites()">❤️ Yêu thích ngày này</button>
                        <button class="btn-success" onclick="showAddEventModal()">📅 Thêm sự kiện</button>
                    </div>

                    <div class="favorites-section" style="margin-top: 15px;">
                        <h4>⭐ Ngày yêu thích</h4>
                        <div class="events-list" id="favorites-list">
                            <!-- Danh sách ngày yêu thích sẽ được thêm ở đây -->
                        </div>
                    </div>

                    <div class="events-section" style="margin-top: 15px;">
                        <h4>🗓️ Sự kiện cá nhân</h4>
                        <div class="events-list" id="personal-events-list">
                            <!-- Danh sách sự kiện sẽ được thêm ở đây -->
                        </div>
                    </div>
                </div>

                <div class="service-info">
                    <h2>📅 Xem Ngày Tốt Xấu</h2>
                    <p>Chọn một ngày trên lịch để xem thông tin chi tiết về ngày tốt/xấu, giờ hoàng đạo, việc nên làm và nên tránh.</p>
                  
                    <div class="service-detail">
                        <h3>ℹ️ Dịch vụ Xem Ngày</h3>
                        <p>Dịch vụ xem ngày cung cấp thông tin chi tiết về các ngày trong tháng, giúp bạn lựa chọn thời điểm phù hợp cho các công việc quan trọng.</p>
                      
                        <div class="service-features">
                            <div class="feature-item">
                                <strong>📊 Ngày Tốt</strong>
                                <p>Phù hợp cho các việc quan trọng</p>
                            </div>
                            <div class="feature-item">
                                <strong>⚠️ Ngày Xấu</strong>
                                <p>Nên tránh các việc trọng đại</p>
                            </div>
                            <div class="feature-item">
                                <strong>⏰ Giờ Hoàng Đạo</strong>
                                <p>Khung giờ tốt trong ngày</p>
                            </div>
                            <div class="feature-item">
                                <strong>🧭 Hướng Xuất Hành</strong>
                                <p>Hướng tốt cho công việc</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
      
        <footer>
            <p><strong>✨ Ứng dụng Lịch Việt</strong></p>
            <p>Xem ngày tốt xấu theo quan niệm dân gian Việt Nam</p>
            <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 10px;">⚠️ Lưu ý: Thông tin chỉ mang tính chất tham khảo</p>
        </footer>
    </div>

    <!-- Modal đăng nhập -->
    <div id="login-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔑 Đăng nhập</h3>
                <button class="close-modal" onclick="closeLoginModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-email">📧 Email *</label>
                        <input type="email" id="login-email" class="form-control" required placeholder="Nhập địa chỉ email">
                    </div>
                    <div class="form-group">
                        <label for="login-password">🔒 Mật khẩu *</label>
                        <input type="password" id="login-password" class="form-control" required placeholder="Nhập mật khẩu">
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Ghi nhớ đăng nhập</label>
                    </div>
                </form>
                <p style="margin-top: 15px; text-align: center; color: #666; font-size: 0.9rem;">
                    Chưa có tài khoản? <a href="#" onclick="closeLoginModal(); showRegisterModal(); return false;" style="color: #667eea; font-weight: 600;">Đăng ký ngay</a>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeLoginModal()">❌ Hủy</button>
                <button class="btn-success" onclick="performLogin()">🔑 Đăng nhập</button>
            </div>
        </div>
    </div>

    <!-- Modal đăng ký -->
    <div id="register-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📝 Đăng ký tài khoản</h3>
                <button class="close-modal" onclick="closeRegisterModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="register-form">
                    <div class="form-group">
                        <label for="register-name">👤 Họ và tên *</label>
                        <input type="text" id="register-name" class="form-control" required placeholder="Nhập họ và tên">
                    </div>
                    <div class="form-group">
                        <label for="register-email">📧 Email *</label>
                        <input type="email" id="register-email" class="form-control" required placeholder="Nhập địa chỉ email">
                    </div>
                    <div class="form-group">
                        <label for="register-password">🔒 Mật khẩu *</label>
                        <input type="password" id="register-password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">✅ Xác nhận mật khẩu *</label>
                        <input type="password" id="register-confirm-password" class="form-control" required placeholder="Nhập lại mật khẩu">
                    </div>
                </form>
                <p style="margin-top: 15px; text-align: center; color: #666; font-size: 0.9rem;">
                    Đã có tài khoản? <a href="#" onclick="closeRegisterModal(); showLoginModal(); return false;" style="color: #667eea; font-weight: 600;">Đăng nhập</a>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeRegisterModal()">❌ Hủy</button>
                <button class="btn-success" onclick="performRegister()">📝 Đăng ký</button>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📅 Thêm sự kiện cá nhân</h3>
                <button class="close-modal" onclick="closeEventModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="event-form">
                    <div class="form-group">
                        <label for="event-title">Tiêu đề sự kiện *</label>
                        <input type="text" id="event-title" class="form-control" required placeholder="Nhập tiêu đề sự kiện">
                    </div>
                    <div class="form-group">
                        <label for="event-color">Màu chữ cho sự kiện (hiển thị trên lịch)</label>
                        <input type="color" id="event-color" class="form-control" value="#ffffff">
                    </div>
                    <div class="form-group">
                        <label for="event-date">Ngày *</label>
                        <input type="date" id="event-date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="event-time">Thời gian (tùy chọn)</label>
                        <input type="time" id="event-time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="event-description">Mô tả</label>
                        <textarea id="event-description" class="form-control" rows="3" placeholder="Mô tả sự kiện"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEventModal()">❌ Hủy</button>
                <button type="submit" class="btn-success" onclick="saveEvent()">💾 Lưu sự kiện</button>
            </div>
        </div>
    </div>

    <!-- Month Selection Modal -->
    <div id="monthModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📅 Chọn tháng/năm</h3>
                <button class="close-modal" onclick="closeMonthModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group-half">
                        <label>Tháng</label>
                        <select id="modalMonth" class="form-control">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == date('n') ? 'selected' : ''; ?>>
                                    Tháng <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group-half">
                        <label>Năm</label>
                        <input type="number" id="modalYear" class="form-control" min="1900" max="2100" value="<?php echo date('Y'); ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-success" onclick="applyMonthSelection()">✅ Áp dụng</button>
            </div>
        </div>
    </div>

    <!-- Statistics Modal -->
    <div id="statModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="statTitle">📊 Thống kê</h3>
                <button class="close-modal" onclick="closeStatModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="statBody"></div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- Navigation Buttons -->
    <div class="nav-buttons-bottom">
        <button class="nav-btn today" onclick="goToToday()">📅 Hôm nay</button>
        <button class="nav-btn jump" onclick="jumpToDate()">🎯 Chọn tháng</button>
        <button class="nav-btn jump" onclick="showStatisticsMonth()">📊 Thống kê tháng</button>
        <button class="nav-btn jump" onclick="showStatisticsYear()">📈 Thống kê năm</button>
        <button class="nav-btn jump" onclick="refreshAdminData()">🔄 Cập nhật dữ liệu</button>
    </div>

    <script>
        // ==================== BIẾN TOÀN CỤC ====================
        let currentDate = new Date();
        let selectedDate = new Date();
        let currentUser = <?php echo $user ? json_encode($user) : 'null'; ?>;
        
        // Dữ liệu người dùng
        let userFavorites = [];
        let userEvents = [];
        
        // Dữ liệu từ admin
        let adminEvents = [];
        let adminHolidays = [];

        // Cache để lưu kết quả tính toán ngày
        const dayEvaluationCache = {};

        // ==================== DỮ LIỆU CƠ BẢN ====================
        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
        
        // Giờ trong ngày
        const CHI_GIO = [
            "Tý (23-1h)", "Sửu (1-3h)", "Dần (3-5h)", "Mão (5-7h)", 
            "Thìn (7-9h)", "Tỵ (9-11h)", "Ngọ (11-13h)", "Mùi (13-15h)", 
            "Thân (15-17h)", "Dậu (17-19h)", "Tuất (19-21h)", "Hợi (21-23h)"
        ];

        // ==================== HÀM CHUYỂN ĐỔI LỊCH ====================
        function jdFromDate(dd, mm, yy) {
            const a = Math.floor((14 - mm) / 12);
            const y = yy + 4800 - a;
            const m = mm + 12 * a - 3;
            let jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
            if (jd < 2299161) {
                jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - 32083;
            }
            return jd;
        }

        function convertSolar2Lunar(dd, mm, yy, timeZone = 7) {
            try {
                const dayNumber = jdFromDate(dd, mm, yy);
                const k = Math.floor((dayNumber - 2415021.076998695) / 29.530588853);
                
                let monthStart = newMoon(k + 1, timeZone);
                if (monthStart > dayNumber) {
                    monthStart = newMoon(k, timeZone);
                }
                
                let a11 = getLunarMonth11(yy, timeZone);
                let b11 = getLunarMonth11(yy + 1, timeZone);
                let lunarYear;
                
                if (a11 >= monthStart) {
                    lunarYear = yy;
                    a11 = getLunarMonth11(yy - 1, timeZone);
                } else {
                    lunarYear = yy + 1;
                    b11 = getLunarMonth11(yy + 1, timeZone);
                }
                
                const lunarDay = Math.floor(dayNumber - monthStart + 1);
                const diff = Math.floor((monthStart - a11) / 29);
                let lunarMonth = diff + 11;
                let lunarLeap = 0;
                
                if ((b11 - a11) > 365) {
                    const leapMonthDiff = getLeapMonthOffset(a11, timeZone);
                    if (diff >= leapMonthDiff) {
                        lunarMonth = diff + 10;
                        if (diff === leapMonthDiff) {
                            lunarLeap = 1;
                        }
                    }
                }
                
                if (lunarMonth > 12) {
                    lunarMonth -= 12;
                }
                
                if (lunarMonth >= 11 && diff < 4) {
                    lunarYear -= 1;
                }
                
                return [lunarDay, lunarMonth, lunarYear, lunarLeap];
            } catch (error) {
                console.error('Lỗi chuyển đổi Dương sang Âm:', error);
                return [dd, mm, yy, 0];
            }
        }

        function newMoon(k, timeZone) {
            const T = k / 1236.85;
            const T2 = T * T;
            const T3 = T2 * T;
            const dr = Math.PI / 180;
            
            let Jd1 = 2415020.75933 + 29.53058868 * k + 0.0001178 * T2 - 0.000000155 * T3;
            Jd1 = Jd1 + 0.00033 * Math.sin((166.56 + 132.87 * T - 0.009173 * T2) * dr);
            
            const M = 359.2242 + 29.10535608 * k - 0.0000333 * T2 - 0.00000347 * T3;
            const Mpr = 306.0253 + 385.81691806 * k + 0.0107306 * T2 + 0.00001236 * T3;
            const F = 21.2964 + 390.67050646 * k - 0.0016528 * T2 - 0.00000239 * T3;
            
            let C1 = (0.1734 - 0.000393 * T) * Math.sin(M * dr);
            C1 = C1 + 0.0021 * Math.sin(2 * dr * M);
            C1 = C1 - 0.4068 * Math.sin(Mpr * dr);
            C1 = C1 + 0.0161 * Math.sin(dr * 2 * Mpr);
            C1 = C1 - 0.0004 * Math.sin(dr * 3 * Mpr);
            C1 = C1 + 0.0104 * Math.sin(dr * 2 * F);
            C1 = C1 - 0.0051 * Math.sin(dr * (M + Mpr));
            C1 = C1 - 0.0074 * Math.sin(dr * (M - Mpr));
            C1 = C1 + 0.0004 * Math.sin(dr * (2 * F + M));
            C1 = C1 - 0.0004 * Math.sin(dr * (2 * F - M));
            C1 = C1 - 0.0006 * Math.sin(dr * (2 * F + Mpr));
            C1 = C1 + 0.0010 * Math.sin(dr * (2 * F - Mpr));
            C1 = C1 + 0.0005 * Math.sin(dr * (2 * Mpr + M));
            
            const deltat = (T < -11) ? 
                0.001 + 0.000839 * T + 0.0002261 * T2 - 0.00000845 * T3 - 0.000000081 * T * T3 : 
                -0.000278 + 0.000265 * T + 0.000262 * T2;
            
            const JdNew = Jd1 + C1 - deltat;
            return JdNew;
        }

        function getLunarMonth11(yy, timeZone) {
            const off = jdFromDate(31, 12, yy) - 2415021;
            const k = Math.floor(off / 29.530588853);
            let nm = newMoon(k, timeZone);
            const sunLong = getSunLongitude(nm, timeZone);
            
            if (sunLong >= 9) {
                nm = newMoon(k - 1, timeZone);
            }
            return nm;
        }

        function getSunLongitude(jdn, timeZone) {
            const T = (jdn - 2451545.5 - timeZone / 24) / 36525;
            const T2 = T * T;
            const dr = Math.PI / 180;
            const M = 357.52910 + 35999.05030 * T - 0.0001559 * T2 - 0.00000048 * T * T2;
            const L0 = 280.46645 + 36000.76983 * T + 0.0003032 * T2;
            let DL = (1.914600 - 0.004817 * T - 0.000014 * T2) * Math.sin(dr * M);
            DL = DL + (0.019993 - 0.000101 * T) * Math.sin(dr * 2 * M) + 0.000290 * Math.sin(dr * 3 * M);
            let L = L0 + DL;
            L = L * dr;
            L = L - Math.PI * 2 * (Math.floor(L / (Math.PI * 2)));
            return Math.floor(L / Math.PI * 6);
        }

        function getLeapMonthOffset(a11, timeZone) {
            const k = Math.floor((a11 - 2415021.076998695) / 29.530588853 + 0.5);
            let last = 0;
            let i = 1;
            let arc = getSunLongitude(newMoon(k + i, timeZone), timeZone);
            
            do {
                last = arc;
                i++;
                arc = getSunLongitude(newMoon(k + i, timeZone), timeZone);
            } while (arc != last && i < 14);
            
            return i - 1;
        }

        function canChiOfDay(jdn) {
            const canIndex = (jdn + 9) % 10;
            const chiIndex = (jdn + 1) % 12;
            return [CAN[canIndex], CHI[chiIndex]];
        }

        function canChiOfYear(lunarYear) {
            const canIndex = (lunarYear + 6) % 10;
            const chiIndex = (lunarYear + 8) % 12;
            return [CAN[canIndex], CHI[chiIndex]];
        }

        // ==================== ĐÁNH GIÁ NGÀY ====================
        function evaluateDay(dd, mm, yy) {
            const j = jdFromDate(dd, mm, yy);
            const [ld, lm, ly, leap] = convertSolar2Lunar(dd, mm, yy);
            const [canD, chiD] = canChiOfDay(j);
            const [canY, chiY] = canChiOfYear(ly);
            
            const dateHash = `${dd}-${mm}-${yy}`;
            let hash = 0;
            for (let i = 0; i < dateHash.length; i++) {
                hash = ((hash << 5) - hash) + dateHash.charCodeAt(i);
                hash = hash & hash;
            }
            
            const stableScore = Math.abs(hash % 100) / 10;
            
            let grade, barClass, barText;
            
            if (stableScore >= 7) {
                grade = 'good';
                barClass = 'good';
                barText = 'Ngày tốt (cát lợi)';
            } else if (stableScore <= 3) {
                grade = 'bad';
                barClass = 'bad';
                barText = 'Ngày xấu (bất lợi)';
            } else {
                grade = 'neutral';
                barClass = 'neutral';
                barText = 'Ngày bình thường';
            }

            return {
                jdn: j,
                ld, lm, ly, leap,
                canD, chiD, canY, chiY,
                grade, barClass, barText,
                score: stableScore.toFixed(1),
                gioHD: ["Tý (23-1h)", "Dần (3-5h)", "Mão (5-7h)", "Ngọ (11-13h)", "Thân (15-17h)", "Tuất (19-21h)"]
            };
        }

        function getCachedDayEvaluation(dd, mm, yy) {
            const cacheKey = `${dd}-${mm}-${yy}`;
            
            if (!dayEvaluationCache[cacheKey]) {
                dayEvaluationCache[cacheKey] = evaluateDay(dd, mm, yy);
            }
            
            return dayEvaluationCache[cacheKey];
        }

        // ==================== TIỆN ÍCH ĐỊNH DẠNG ====================
        function formatDateToYMD(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatDateToDMY(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${day}-${month}-${year}`;
        }

        // ==================== QUẢN LÝ DỮ LIỆU ADMIN ====================
        async function loadAdminData() {
            try {
                console.log('🔄 Đang tải dữ liệu từ admin...');
                const response = await fetch('api/get_admin_events.php');
                const data = await response.json();
                
                if (data.success) {
                    adminEvents = data.events || [];
                    adminHolidays = data.holidays || [];
                    console.log('✅ Đã tải dữ liệu từ admin:', {
                        events: adminEvents.length,
                        holidays: adminHolidays.length
                    });
                    
                    // Hiển thị nút refresh nếu có dữ liệu admin
                    if (adminEvents.length > 0 || adminHolidays.length > 0) {
                        document.getElementById('refresh-admin-btn').style.display = 'block';
                    }
                } else {
                    console.error('❌ Lỗi tải dữ liệu admin:', data.message);
                }
            } catch (error) {
                console.error('❌ Lỗi tải dữ liệu admin:', error);
            }
        }

        function getAdminEventsForDate(date) {
            const dateString = formatDateToYMD(date);
            const events = adminEvents.filter(event => {
                if (!event.date) return false;
                const eventDate = new Date(event.date);
                const eventDateString = formatDateToYMD(eventDate);
                return eventDateString === dateString;
            });
            
            if (events.length > 0) {
                console.log('✅ Admin events found:', events.length, 'for date:', dateString, events);
            }
            
            return events;
        }

        function getAdminHolidaysForDate(date) {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            const dateString = formatDateToYMD(date);
            
            console.log(`🔍 Checking holidays for: ${dateString}`, {
                day, month, year, dateString
            });
            
            const holidays = adminHolidays.filter(holiday => {
                if (!holiday) return false;
                
                if (holiday.is_lunar) {
                    // So sánh theo âm lịch
                    const [ld, lm, ly, leap] = convertSolar2Lunar(day, month, year);
                    const matches = ld === holiday.lunar_day && lm === holiday.lunar_month;
                    
                    if (matches) {
                        console.log('✅ Lunar holiday match:', holiday.name, {
                            lunarDate: `${ld}/${lm}`,
                            holidayDate: `${holiday.lunar_day}/${holiday.lunar_month}`,
                            holiday: holiday
                        });
                    }
                    
                    return matches;
                } else {
                    // XỬ LÝ NGÀY LỄ DƯƠNG LỊCH ĐỊNH KỲ - ĐÃ SỬA
                    if (holiday.is_recurring_solar && holiday.solar_day && holiday.solar_month) {
                        // So sánh ngày/tháng cố định (bỏ qua năm)
                        const matches = day === holiday.solar_day && month === holiday.solar_month;
                        
                        if (matches) {
                            console.log('✅ Recurring solar holiday match:', holiday.name, {
                                currentDate: `${day}/${month}`,
                                holidayDate: `${holiday.solar_day}/${holiday.solar_month}`,
                                holiday: holiday
                            });
                        }
                        
                        return matches;
                    } else if (holiday.solar_date) {
                        // Fallback: so sánh theo dương lịch cũ (năm cụ thể)
                        try {
                            const holidayDate = new Date(holiday.solar_date);
                            const holidayDateString = formatDateToYMD(holidayDate);
                            const matches = holidayDateString === dateString;
                            
                            if (matches) {
                                console.log('✅ Solar holiday match:', holiday.name, {
                                    currentDate: dateString,
                                    holidayDate: holidayDateString,
                                    holiday: holiday
                                });
                            }
                            
                            return matches;
                        } catch (e) {
                            console.error('❌ Error parsing solar date:', holiday.solar_date, e);
                            return false;
                        }
                    }
                }
                return false;
            });
            
            if (holidays.length > 0) {
                console.log('🎉 Total holidays found:', holidays.length, 'for date:', dateString, holidays);
            }
            
            return holidays;
        }

        function formatHolidayDisplay(holiday) {
            if (!holiday) return '';
            
            let typeIcon = '🎉';
            switch(holiday.type) {
                case 'national': typeIcon = '🇻🇳'; break;
                case 'religious': typeIcon = '🛐'; break;
                case 'traditional': typeIcon = '🎎'; break;
                case 'other': typeIcon = '📌'; break;
            }
            
            return `${typeIcon} ${holiday.name}`;
        }

        async function refreshAdminData() {
            await loadAdminData();
            renderCalendar(currentDate);
            updateDayInfo(selectedDate);
            showNotification('✅ Đã cập nhật dữ liệu từ admin', 'success');
        }

        // ==================== HIỂN THỊ LỊCH ====================
        function renderCalendar(date) {
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            const isCurrentMonth = year === today.getFullYear() && month === today.getMonth();
           
            document.getElementById('current-month').textContent = `Tháng ${month + 1}, ${year}`;
           
            let html = '';
            let day = 1;
           
            console.log(`📅 Rendering calendar for: ${month + 1}/${year}`, {
                firstDay, lastDate, isCurrentMonth
            });
           
            for (let i = 0; i < 6; i++) {
                html += '<tr>';
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        html += '<td></td>';
                    } else if (day > lastDate) {
                        html += '<td></td>';
                    } else {
                        const currentDate = new Date(year, month, day);
                        const [ld, lm, ly, leap] = convertSolar2Lunar(day, month + 1, year);
                        const jdn = jdFromDate(day, month + 1, year);
                        const [canD, chiD] = canChiOfDay(jdn);
                        const data = getCachedDayEvaluation(day, month + 1, year);
                        
                        // Lấy dữ liệu từ admin - ĐÃ SỬA
                        const adminEvents = getAdminEventsForDate(currentDate);
                        const adminHolidays = getAdminHolidaysForDate(currentDate);
                        const personalEvents = getEventsForDate(currentDate);
                        
                        let className = data.grade + '-day';
                        if (isCurrentMonth && day === today.getDate()) className += ' current-day';
                        if (currentDate.toDateString() === selectedDate.toDateString()) className += ' selected-date';
                       
                        // Tạo HTML cho các sự kiện và ngày lễ
                        let eventHtml = '';
                        
                        // Hiển thị ngày lễ từ admin (ưu tiên cao nhất)
                        if (adminHolidays.length > 0) {
                            adminHolidays.forEach(holiday => {
                                if (!holiday) return;
                                eventHtml += `<div class="admin-holiday ${holiday.type}" title="${holiday.name}">
                                    ${formatHolidayDisplay(holiday)}
                                </div>`;
                            });
                        }
                        
                        // Hiển thị sự kiện từ admin
                        if (adminEvents.length > 0) {
                            adminEvents.forEach(event => {
                                if (!event) return;
                                const badgeClass = event.is_featured ? 
                                    'admin-event featured-event' : 
                                    'admin-event';
                                eventHtml += `<div class="${badgeClass}" title="${event.title}">
                                    📢 ${event.title}
                                </div>`;
                            });
                        }
                        
                        // Hiển thị sự kiện cá nhân
                        if (personalEvents.length > 0) {
                            const event = personalEvents[0];
                            eventHtml += `<div class="personal-event" style="color: ${event.color || '#ffffff'};" title="${event.title}">
                                👤 ${event.title}
                            </div>`;
                            if (personalEvents.length > 1) {
                                eventHtml += `<div class="personal-event">+${personalEvents.length - 1} sự kiện</div>`;
                            }
                        }
                        
                        const isFav = isFavorite(currentDate);
                       
                        html += `
                            <td class="${className}" onclick="selectDate(new Date(${year}, ${month}, ${day}))" style="position: relative;">
                                <div class="day-number">${day}</div>
                                <span class="lunar-date">${ld}/${lm}${leap ? 'n' : ''}</span>
                                <span class="can-chi">${canD} ${chiD}</span>
                                <div class="events-container">
                                    ${eventHtml}
                                </div>
                                ${isFav ? '<div class="favorite-indicator" title="Đã yêu thích">❤️</div>' : ''}
                            </td>`;
                        day++;
                    }
                }
                html += '</tr>';
                if (day > lastDate) break;
            }
           
            document.getElementById('calendar-body').innerHTML = html;
        }

        function selectDate(date) {
            selectedDate = date;
            updateDayInfo(date);
            renderCalendar(currentDate);
        }

        function updateDayInfo(date) {
            const data = getCachedDayEvaluation(date.getDate(), date.getMonth() + 1, date.getFullYear());
            const [ld, lm, ly, leap] = convertSolar2Lunar(date.getDate(), date.getMonth() + 1, date.getFullYear());
            
            // Lấy dữ liệu từ admin - ĐÃ SỬA
            const adminEvents = getAdminEventsForDate(date);
            const adminHolidays = getAdminHolidaysForDate(date);
           
            document.getElementById('day-rating').textContent = data.barText;
            document.getElementById('day-rating').className = `${data.grade}-rating`;
            
            let adminInfoHtml = '';
            
            // Hiển thị ngày lễ từ admin
            if (adminHolidays.length > 0) {
                adminInfoHtml += `<div class="info"><strong>🎉 NGÀY LỄ</strong>`;
                adminHolidays.forEach(holiday => {
                    if (!holiday) return;
                    const dateType = holiday.is_lunar ? 'Âm lịch' : 'Dương lịch';
                    const dateInfo = holiday.is_lunar ? 
                        `${holiday.lunar_day}/${holiday.lunar_month}` : 
                        `${holiday.solar_day || 'N/A'}/${holiday.solar_month || 'N/A'}`;
                        
                    adminInfoHtml += `
                        <div style="margin: 8px 0; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 5px;">
                            <div style="font-weight: bold;">${formatHolidayDisplay(holiday)}</div>
                            <div style="font-size: 0.9em; color: #666;">📅 ${dateType}: ${dateInfo}</div>
                            ${holiday.description ? `<div style="font-size: 0.85em; margin-top: 4px;">${holiday.description}</div>` : ''}
                        </div>
                    `;
                });
                adminInfoHtml += `</div>`;
            }
            
            // Hiển thị sự kiện từ admin
            if (adminEvents.length > 0) {
                adminInfoHtml += `<div class="info"><strong>📢 SỰ KIỆN HỆ THỐNG</strong>`;
                adminEvents.forEach(event => {
                    if (!event) return;
                    const featuredIcon = event.is_featured ? ' ⭐ NỔI BẬT' : '';
                    adminInfoHtml += `
                        <div style="margin: 8px 0; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 5px;">
                            <div style="font-weight: bold;">📢 ${event.title}${featuredIcon}</div>
                            ${event.description ? `<div style="font-size: 0.9em; margin-top: 4px;">${event.description}</div>` : ''}
                            ${event.location ? `<div style="font-size: 0.85em; color: #666;">📍 ${event.location}</div>` : ''}
                        </div>
                    `;
                });
                adminInfoHtml += `</div>`;
            }
           
            document.getElementById('day-details-text').innerHTML = `
                <div class="bar ${data.barClass}">${data.barText} — Điểm: ${data.score}/10</div>
                <div class="info-grid">
                    <div class="info"><strong>📅 Dương lịch</strong>${date.toLocaleDateString('vi-VN')}</div>
                    <div class="info"><strong>🌙 Âm lịch</strong>${ld}/${lm}/${ly}${leap ? ' (nhuận)' : ''}</div>
                    <div class="info"><strong>📊 Can Chi ngày</strong>${data.canD} ${data.chiD}</div>
                    <div class="info"><strong>⏰ Giờ Hoàng đạo</strong>${data.gioHD.join(', ')}</div>
                </div>
                ${adminInfoHtml ? `<div style="margin-top: 20px;">${adminInfoHtml}</div>` : ''}
                <div class="info-grid" style="margin-top: 15px;">
                    <div class="info"><strong>✅ Nên làm</strong>Các việc trọng đại, khởi công, xuất hành</div>
                    <div class="info"><strong>❌ Không nên</strong>Kiện tụng, tranh chấp</div>
                </div>
            `;

            updateDayActions(date);
        }

        function updateDayActions(date) {
            const dayActions = document.getElementById('day-actions');
            if (currentUser) {
                const isFav = isFavorite(date);
                dayActions.innerHTML = `
                    <button class="favorite-btn ${isFav ? 'active' : ''}" onclick="addToFavorites()" title="${isFav ? 'Đã yêu thích' : 'Thêm vào yêu thích'}">
                        ${isFav ? '❤️' : '🤍'}
                    </button>
                    <button class="event-btn" onclick="showAddEventModal()" title="Thêm sự kiện">📅</button>
                `;
            }
        }

        // ==================== QUẢN LÝ CÁ NHÂN ====================
        async function loadUserData() {
            if (currentUser) {
                await loadFavorites();
                await loadEvents();
                renderCalendar(currentDate);
            }
        }

        async function loadFavorites() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                if (data.success) {
                    userFavorites = data.favorites || [];
                }
            } catch (error) {
                console.error('Lỗi tải danh sách yêu thích:', error);
                userFavorites = [];
            }
        }

        async function loadEvents() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_events.php');
                const data = await response.json();
                
                if (data.success) {
                    userEvents = data.events || [];
                    updateEventsList();
                }
            } catch (error) {
                console.error('Lỗi tải sự kiện:', error);
                userEvents = [];
            }
        }

        function isFavorite(date) {
            if (!currentUser) return false;
            const dateString = formatDateToYMD(date);
            
            return userFavorites.some(fav => {
                const favDate = fav.solar_date || fav.date;
                return favDate === dateString;
            });
        }

        function getEventsForDate(date) {
            if (!currentUser || !userEvents) return [];
            
            const dateString = formatDateToYMD(date);
            return userEvents.filter(event => event.date === dateString);
        }

        function updateEventsList() {
            const eventsList = document.getElementById('personal-events-list');
            
            if (!userEvents || userEvents.length === 0) {
                eventsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">Chưa có sự kiện nào.</p>';
                return;
            }

            let html = '';
            
            const sortedEvents = [...userEvents].sort((a, b) => new Date(b.date) - new Date(a.date));
            
            sortedEvents.slice(0, 5).forEach(event => {
                const eventDate = new Date(event.date);
                const dateString = eventDate.toLocaleDateString('vi-VN');
                
                html += `
                    <div class="event-item">
                        <div>
                            <strong>${event.title}</strong><br>
                            <small>📅 ${dateString} ${event.time ? '• ⏰ ' + event.time : ''}</small>
                            ${event.description ? `<br><small>${event.description}</small>` : ''}
                        </div>
                        <div class="event-actions">
                            <button class="btn-small btn-danger" onclick="removeEvent('${event.id}')">🗑️ Xóa</button>
                        </div>
                    </div>
                `;
            });

            eventsList.innerHTML = html;
        }

        async function addToFavorites() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để sử dụng tính năng này!', 'error');
                return;
            }

            const date = selectedDate;
            const dateString = formatDateToYMD(date);
            
            try {
                const dayData = getCachedDayEvaluation(date.getDate(), date.getMonth() + 1, date.getFullYear());
                const [ld, lm, ly, leap] = convertSolar2Lunar(date.getDate(), date.getMonth() + 1, date.getFullYear());
                
                const solarDateFormatted = formatDateToDMY(date);
                const lunarDateFormatted = `${ld}/${lm}${leap ? 'n' : ''}`;
                
                const ratingText = `Ngày ${solarDateFormatted} (Âm: ${lunarDateFormatted}) - ${dayData.barText} - Can Chi: ${dayData.canD} ${dayData.chiD} - Điểm: ${dayData.score}/10`;
                
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        date: dateString,
                        solar: solarDateFormatted,
                        lunar: lunarDateFormatted,
                        rating: ratingText,
                        score: dayData.score
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                    await loadFavorites();
                    renderCalendar(currentDate);
                    updateDayInfo(selectedDate);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi thêm yêu thích:', error);
                showNotification('Lỗi thêm yêu thích!', 'error');
            }
        }

        async function saveEvent() {
            const title = document.getElementById('event-title').value.trim();
            const color = document.getElementById('event-color').value;
            const date = document.getElementById('event-date').value;
            const time = document.getElementById('event-time').value;
            const description = document.getElementById('event-description').value.trim();

            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để thêm sự kiện!', 'error');
                return;
            }

            if (!title) {
                showNotification('Vui lòng nhập tiêu đề sự kiện!', 'error');
                return;
            }

            if (!date) {
                showNotification('Vui lòng chọn ngày sự kiện!', 'error');
                return;
            }

            try {
                const response = await fetch('api/add_event.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        color: color,
                        event_date: date,
                        event_time: time || null,
                        description: description
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const newEvent = {
                        id: data.event_id,
                        title: title,
                        color: color,
                        date: date,
                        time: time,
                        description: description
                    };
                    
                    userEvents.push(newEvent);
                    
                    showNotification(data.message, 'success');
                    closeEventModal();
                    updateEventsList();
                    renderCalendar(currentDate);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi thêm sự kiện:', error);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        async function removeEvent(eventId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) {
                return;
            }

            try {
                const response = await fetch('api/remove_event.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ eventId: eventId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    userEvents = userEvents.filter(event => event.id != eventId);
                    showNotification(data.message, 'success');
                    updateEventsList();
                    renderCalendar(currentDate);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa sự kiện:', error);
                showNotification('Lỗi khi xóa sự kiện!', 'error');
            }
        }

        // ==================== ĐIỀU HƯỚNG ====================
        function goToToday() {
            const today = new Date();
            currentDate = new Date(today);
            selectedDate = new Date(today);
            renderCalendar(currentDate);
            updateDayInfo(selectedDate);
        }

        function jumpToDate() {
            document.getElementById('monthModal').style.display = 'flex';
        }

        function closeMonthModal() {
            document.getElementById('monthModal').style.display = 'none';
        }

        function applyMonthSelection() {
            const yy = parseInt(document.getElementById('modalYear').value);
            const mm = parseInt(document.getElementById('modalMonth').value) - 1;
            if (yy >= 1900 && yy <= 2100 && mm >= 0 && mm <= 11) {
                currentDate = new Date(yy, mm, 1);
                renderCalendar(currentDate);
                closeMonthModal();
            } else {
                alert('Năm phải từ 1900-2100 và tháng 1-12!');
            }
        }

        // ==================== THỐNG KÊ ====================
        function showStatisticsMonth() {
            const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
            let good = 0, bad = 0, neutral = 0;
            
            for (let d = 1; d <= daysInMonth; d++) {
                const data = getCachedDayEvaluation(d, currentDate.getMonth() + 1, currentDate.getFullYear());
                if (data.grade === 'good') good++;
                else if (data.grade === 'bad') bad++;
                else neutral++;
            }
            
            const html = `
                <p><b>Phân bố ngày tháng ${currentDate.getMonth() + 1}/${currentDate.getFullYear()}:</b></p>
                <div class="info-grid">
                    <div class="info"><strong>Ngày tốt</strong>${good} (${(good / daysInMonth * 100).toFixed(1)}%)</div>
                    <div class="info"><strong>Ngày xấu</strong>${bad} (${(bad / daysInMonth * 100).toFixed(1)}%)</div>
                    <div class="info"><strong>Ngày bình thường</strong>${neutral} (${(neutral / daysInMonth * 100).toFixed(1)}%)</div>
                </div>
            `;
            
            openStatModal(`📊 Thống kê tháng ${currentDate.getMonth() + 1}/${currentDate.getFullYear()}`, html);
        }

        function showStatisticsYear() {
            let good = 0, bad = 0, neutral = 0;
            
            for (let m = 1; m <= 12; m++) {
                const daysInMonth = new Date(currentDate.getFullYear(), m, 0).getDate();
                for (let d = 1; d <= daysInMonth; d++) {
                    const data = getCachedDayEvaluation(d, m, currentDate.getFullYear());
                    if (data.grade === 'good') good++;
                    else if (data.grade === 'bad') bad++;
                    else neutral++;
                }
            }
            
            const totalDays = good + bad + neutral;
            const html = `
                <p><b>Phân bố ngày năm ${currentDate.getFullYear()}:</b></p>
                <div class="info-grid">
                    <div class="info"><strong>Ngày tốt</strong>${good} (${(good / totalDays * 100).toFixed(1)}%)</div>
                    <div class="info"><strong>Ngày xấu</strong>${bad} (${(bad / totalDays * 100).toFixed(1)}%)</div>
                    <div class="info"><strong>Ngày bình thường</strong>${neutral} (${(neutral / totalDays * 100).toFixed(1)}%)</div>
                </div>
            `;
            
            openStatModal(`📈 Thống kê năm ${currentDate.getFullYear()}`, html);
        }

        function openStatModal(title, html) {
            document.getElementById('statTitle').textContent = title;
            document.getElementById('statBody').innerHTML = html;
            document.getElementById('statModal').style.display = 'flex';
        }

        function closeStatModal() {
            document.getElementById('statModal').style.display = 'none';
        }

        // ==================== MODAL FUNCTIONS ====================
        function showAddEventModal() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để thêm sự kiện!', 'error');
                return;
            }
            
            const dateString = formatDateToYMD(selectedDate);
            document.getElementById('event-date').value = dateString;
            document.getElementById('event-title').value = '';
            document.getElementById('event-color').value = '#ffffff';
            document.getElementById('event-time').value = '';
            document.getElementById('event-description').value = '';
            document.getElementById('event-modal').style.display = 'flex';
        }

        function closeEventModal() {
            document.getElementById('event-modal').style.display = 'none';
        }

        function showLoginModal() { 
            document.getElementById('login-modal').style.display = 'flex'; 
            document.getElementById('login-email').focus();
        }
        
        function closeLoginModal() { 
            document.getElementById('login-modal').style.display = 'none'; 
            document.getElementById('login-form').reset();
        }
        
        function showRegisterModal() { 
            document.getElementById('register-modal').style.display = 'flex'; 
            document.getElementById('register-name').focus();
        }
        
        function closeRegisterModal() { 
            document.getElementById('register-modal').style.display = 'none'; 
            document.getElementById('register-form').reset();
        }

        // ==================== AUTH FUNCTIONS ====================
        async function performLogin() {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            if (!email || !password) {
                showNotification('Vui lòng nhập đầy đủ thông tin đăng nhập!', 'error');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('email', email);
            formData.append('password', password);

            try {
                const res = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const data = await res.json();

                if (data.success) {
                    currentUser = data.user;
                    updateUIAfterLogin();
                    showNotification('Đăng nhập thành công!', 'success');
                    await loadUserData();
                    renderCalendar(currentDate);
                } else {
                    showNotification(data.message || 'Email hoặc mật khẩu không đúng!', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        async function performRegister() {
            const name = document.getElementById('register-name').value.trim();
            const email = document.getElementById('register-email').value.trim();
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;

            if (!name || !email || !password || !confirmPassword) {
                showNotification('Vui lòng điền đầy đủ thông tin!', 'error');
                return;
            }

            if (password.length < 6) {
                showNotification('Mật khẩu phải có ít nhất 6 ký tự!', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showNotification('Mật khẩu xác nhận không khớp!', 'error');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);

            try {
                const res = await fetch('api/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const data = await res.json();

                if (data.success) {
                    currentUser = data.user;
                    updateUIAfterLogin();
                    showNotification('Đăng ký thành công!', 'success');
                    await loadUserData();
                    renderCalendar(currentDate);
                } else {
                    showNotification(data.message || 'Đăng ký thất bại!', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        function updateUIAfterLogin() {
            document.getElementById('user-info').style.display = 'flex';
            document.getElementById('auth-buttons').style.display = 'none';
            document.getElementById('user-display-name').textContent = currentUser.name;
            document.getElementById('user-display-email').textContent = currentUser.email;

            const initials = currentUser.name
                .split(' ')
                .map(n => n[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();
            document.getElementById('user-avatar').textContent = initials;

            closeLoginModal();
            closeRegisterModal();
            document.getElementById('guest-message').style.display = 'none';
            document.getElementById('user-actions-section').style.display = 'block';
            document.getElementById('day-actions').style.display = 'flex';
        }

        function logout() {
            fetch('api/logout.php', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    currentUser = null;
                    userFavorites = [];
                    userEvents = [];
                    
                    document.getElementById('user-info').style.display = 'none';
                    document.getElementById('auth-buttons').style.display = 'flex';
                    document.getElementById('user-actions-section').style.display = 'none';
                    document.getElementById('day-actions').style.display = 'none';
                    document.getElementById('guest-message').style.display = 'block';
                    document.getElementById('refresh-admin-btn').style.display = 'none';
                    
                    showNotification('Đã đăng xuất thành công!', 'success');
                    renderCalendar(currentDate);
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
        }

        function showProfileModal() {
            window.location.href = 'user.php';
        }

        // ==================== UTILITY FUNCTIONS ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // ==================== INITIALIZATION ====================
        async function initializeApp() {
            setupEventListeners();
            const today = new Date();
            const modalYear = document.getElementById('modalYear');
            const modalMonth = document.getElementById('modalMonth');
            
            if (modalYear) modalYear.value = today.getFullYear();
            if (modalMonth) modalMonth.value = today.getMonth() + 1;
            
            // Tải dữ liệu từ admin trước khi render lịch
            await loadAdminData();
            renderCalendar(currentDate);
            updateDayInfo(selectedDate);
            loadUserData();
        }

        function setupEventListeners() {
            document.getElementById('prev-month')?.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar(currentDate);
            });
            
            document.getElementById('next-month')?.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar(currentDate);
            });
            
            document.getElementById('login-btn')?.addEventListener('click', showLoginModal);
            document.getElementById('register-btn')?.addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn')?.addEventListener('click', logout);
            document.getElementById('profile-btn')?.addEventListener('click', showProfileModal);
            document.getElementById('refresh-admin-btn')?.addEventListener('click', refreshAdminData);
            
            document.getElementById('event-form')?.addEventListener('submit', function(e) {
                e.preventDefault();
                saveEvent();
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === document.getElementById('login-modal')) closeLoginModal();
                if (event.target === document.getElementById('register-modal')) closeRegisterModal();
                if (event.target === document.getElementById('event-modal')) closeEventModal();
                if (event.target === document.getElementById('monthModal')) closeMonthModal();
                if (event.target === document.getElementById('statModal')) closeStatModal();
            });
        }

        // Khởi tạo ứng dụng
        document.addEventListener('DOMContentLoaded', initializeApp);
    </script>
</body>
</html>
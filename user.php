<?php
session_start();
require_once "config.php";
// DEBUG SESSION
echo "<!-- USER.PHP SESSION DEBUG -->";
echo "<!-- User ID: " . ($_SESSION['user_id'] ?? 'NULL') . " -->";
echo "<!-- User Role: " . ($_SESSION['user']['role'] ?? 'NULL') . " -->";
echo "<!-- Full Session: " . json_encode($_SESSION['user'] ?? []) . " -->";

$user = null;

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];

    $sql  = "SELECT u.*, up.phone, up.dob as birthday, up.gender 
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
    <title>Hồ Sơ Cá Nhân - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .profile-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .profile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .profile-field:last-child {
            border-bottom: none;
        }

        .profile-label {
            font-weight: 600;
            color: #333;
            min-width: 150px;
        }

        .profile-value {
            color: #666;
            text-align: right;
            flex: 1;
        }

        .user-role {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .role-admin {
            background: #ffeaa7;
            color: #e17055;
        }

        .role-user {
            background: #dfe6e9;
            color: #636e72;
        }

        .history-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .history-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f1f1;
            flex-wrap: wrap;
        }

        .history-tab {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .history-tab.active {
            background: #667eea;
            color: white;
        }

        .history-tab:hover:not(.active) {
            background: #f8f9fa;
        }

        .history-content {
            display: none;
        }

        .history-content.active {
            display: block;
        }

        .history-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .history-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .history-details {
            flex: 1;
        }

        .history-type {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 5px;
        }

        .history-data {
            color: #555;
            line-height: 1.4;
        }

        .history-date {
            color: #888;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .history-actions {
            display: flex;
            gap: 5px;
        }

        .good-rating {
            color: #28a745;
            font-weight: 600;
        }

        .bad-rating {
            color: #dc3545;
            font-weight: 600;
        }

        .neutral-rating {
            color: #ffc107;
            font-weight: 600;
        }

        .empty-history {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .saved-accounts {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            width: 100%;
            display: none;
        }

        .saved-account-item {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.3s ease;
        }

        .saved-account-item:hover {
            background: #f8f9fa;
        }

        .saved-account-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }

        .saved-account-details {
            flex: 1;
        }

        .saved-account-name {
            font-weight: 600;
            color: #333;
        }

        .saved-account-email {
            font-size: 0.85rem;
            color: #666;
        }

        .remove-account {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
        }

        .remove-account:hover {
            background: #dc3545;
            color: white;
        }

        .guest-message {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .guest-message-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .event-item {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            position: relative;
        }

        .event-item.past {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        }

        .event-date {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .event-title {
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .event-description {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .event-time {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }

        @media (max-width: 768px) {
            .profile-field {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-value {
                text-align: left;
                margin-top: 5px;
            }

            .history-tabs {
                flex-direction: column;
            }

            .history-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .history-actions {
                margin-top: 10px;
                align-self: flex-end;
            }

            .event-time {
                position: static;
                margin-top: 10px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Quản lý hồ sơ cá nhân của bạn</p>
          
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
                        <?php if ($user && $user['role'] === 'admin'): ?>
                            <button id="admin-btn" class="btn-warning" onclick="goToAdmin()">👑 Quản trị</button>
                        <?php endif; ?>
                        <button id="profile-btn" class="btn-info">📋 Hồ sơ</button>
                        <button id="logout-btn" class="btn-secondary">🚪 Đăng xuất</button>
                    </div>
                </div>
                <div class="auth-buttons" id="auth-buttons" style="display: <?php echo $user ? 'none' : 'flex'; ?>;">
                    <button id="login-btn" class="btn-secondary">🔑 Đăng nhập</button>
                    <button id="register-btn" class="btn-success">📝 Đăng ký</button>
                </div>
            </div>
        </div>

        <nav class="nav-menu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
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
                    <a class="nav-link active" href="user.php">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">Hồ Sơ</span>
                    </a>
                </li>
            </ul>
        </nav>
      
        <div class="app-container">
            <section class="info-section">
                <h2>👤 Hồ sơ cá nhân</h2>
                
                <div id="stats-section" class="stats-grid" style="display: none;">
                    <div class="stat-card">
                        <div class="stat-number" id="stat-searches">0</div>
                        <div class="stat-label">Lượt tra cứu</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-favorites">0</div>
                        <div class="stat-label">Yêu thích</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-events">0</div>
                        <div class="stat-label">Sự kiện</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-kethon">0</div>
                        <div class="stat-label">Kết hôn</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-laman">0</div>
                        <div class="stat-label">Làm ăn</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-xaynha">0</div>
                        <div class="stat-label">Xây nhà</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="stat-sinhcon">0</div>
                        <div class="stat-label">Sinh con</div>
                    </div>
                </div>
                
                <div id="profile-content" class="profile-content" style="display: none;">
                    <div class="profile-info">
                        <div class="profile-field">
                            <div class="profile-label">👤 Họ và tên</div>
                            <div class="profile-value" id="profile-name">—</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">📧 Email</div>
                            <div class="profile-value" id="profile-email">—</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">📱 Số điện thoại</div>
                            <div class="profile-value" id="profile-phone">—</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">🎂 Ngày sinh</div>
                            <div class="profile-value" id="profile-birthday">—</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">⚧ Giới tính</div>
                            <div class="profile-value" id="profile-gender">—</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">🎭 Vai trò</div>
                            <div class="profile-value">
                                <span id="profile-role" class="user-role">—</span>
                            </div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">📅 Ngày đăng ký</div>
                            <div class="profile-value" id="profile-joined">—</div>
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button class="btn-info" onclick="showEditProfileModal()">✏️ Chỉnh sửa</button>
                        <button class="btn-secondary" onclick="showChangePasswordModal()">🔒 Đổi mật khẩu</button>
                    </div>
                </div>

                <div id="guest-message" class="guest-message">
                    <div class="guest-message-icon">🔐</div>
                    <h3 style="margin-bottom: 15px; color: #667eea;">Chào mừng đến với Lịch Việt!</h3>
                    <p style="font-size: 1.1rem; margin-bottom: 20px;">Vui lòng <strong>đăng nhập</strong> để xem thông tin hồ sơ cá nhân và truy cập đầy đủ tính năng.</p>
                    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 25px;">
                        <button class="btn-success" onclick="showLoginModal()">🔑 Đăng nhập ngay</button>
                        <button class="btn-info" onclick="showRegisterModal()">📝 Tạo tài khoản</button>
                    </div>
                </div>

                <div id="history-section" class="history-section" style="display: none;">
                    <h3>📚 Lịch sử tra cứu</h3>
                    <div class="history-tabs">
                        <button class="history-tab active" data-tab="recent">🕐 Gần đây</button>
                        <button class="history-tab" data-tab="favorites">❤️ Yêu thích</button>
                        <button class="history-tab" data-tab="events">🗓️ Sự kiện</button>
                        <button class="history-tab" data-tab="kethon">💑 Kết hôn</button>
                        <button class="history-tab" data-tab="laman">💰 Làm ăn</button>
                        <button class="history-tab" data-tab="xaynha">🏠 Xây nhà</button>
                        <button class="history-tab" data-tab="sinhcon">👶 Sinh con</button>
                        <button class="history-tab" data-tab="all">📋 Tất cả</button>
                    </div>
                    
                    <div id="recent-history" class="history-content active">
                        <div class="history-list" id="recent-list"></div>
                    </div>
                    
                    <div id="favorites-history" class="history-content">
                        <div class="history-list" id="favorites-list"></div>
                    </div>
                    
                    <div id="events-history" class="history-content">
                        <div class="history-list" id="events-list"></div>
                    </div>
                    
                    <div id="kethon-history" class="history-content">
                        <div class="history-list" id="kethon-list"></div>
                    </div>
                    
                    <div id="laman-history" class="history-content">
                        <div class="history-list" id="laman-list"></div>
                    </div>
                    
                    <div id="xaynha-history" class="history-content">
                        <div class="history-list" id="xaynha-list"></div>
                    </div>
                    
                    <div id="sinhcon-history" class="history-content">
                        <div class="history-list" id="sinhcon-list"></div>
                    </div>
                    
                    <div id="all-history" class="history-content">
                        <div class="history-list" id="all-list"></div>
                    </div>
                    
                    <div class="modal-buttons" style="margin-top: 25px;">
                        <button class="btn-warning" onclick="clearHistory()">🗑️ Xóa lịch sử</button>
                        <button class="btn-secondary" onclick="exportHistory()">💾 Xuất dữ liệu</button>
                    </div>
                </div>

                <div class="service-detail">
                    <h3>ℹ️ Thông tin về dịch vụ</h3>
                    <p style="color: #555; line-height: 1.8; margin-bottom: 20px;">
                        Quản lý hồ sơ cá nhân giúp bạn lưu trữ và cập nhật thông tin cá nhân, theo dõi lịch sử sử dụng dịch vụ và tùy chỉnh trải nghiệm của bạn trên nền tảng Lịch Việt.
                    </p>
                  
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>📊 Thông tin cá nhân</strong>
                            <p>Lưu trữ và quản lý thông tin cơ bản một cách an toàn và bảo mật</p>
                        </div>
                        <div class="feature-item">
                            <strong>📖 Lịch sử sử dụng</strong>
                            <p>Theo dõi các dịch vụ đã sử dụng để tiện tra cứu lại</p>
                        </div>
                        <div class="feature-item">
                            <strong>🗓️ Quản lý sự kiện</strong>
                            <p>Lưu trữ và quản lý các sự kiện cá nhân quan trọng</p>
                        </div>
                        <div class="feature-item">
                            <strong>✏️ Cập nhật linh hoạt</strong>
                            <p>Chỉnh sửa thông tin cá nhân dễ dàng bất cứ lúc nào</p>
                        </div>
                        <div class="feature-item">
                            <strong>🔐 Bảo mật cao</strong>
                            <p>Đảm bảo an toàn tuyệt đối cho thông tin cá nhân của bạn</p>
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

    <!-- Modal chỉnh sửa hồ sơ -->
    <div id="edit-profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✏️ Chỉnh sửa hồ sơ</h3>
                <button class="close-modal" onclick="closeEditProfileModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="edit-profile-form">
                    <div class="form-group">
                        <label for="edit-name">👤 Họ và tên *</label>
                        <input type="text" id="edit-name" class="form-control" required placeholder="Nhập họ và tên">
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">📱 Số điện thoại</label>
                        <input type="tel" id="edit-phone" class="form-control" placeholder="Nhập số điện thoại">
                    </div>
                    <div class="form-group">
                        <label for="edit-birthday">🎂 Ngày sinh</label>
                        <input type="date" id="edit-birthday" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit-gender">⚧ Giới tính</label>
                        <select id="edit-gender" class="form-control">
                            <option value="">Chọn giới tính</option>
                            <option value="nam">Nam</option>
                            <option value="nu">Nữ</option>
                            <option value="khac">Khác</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeEditProfileModal()">❌ Hủy</button>
                <button class="btn-success" onclick="saveProfileChanges()">💾 Lưu thay đổi</button>
            </div>
        </div>
    </div>

    <!-- Modal đổi mật khẩu -->
    <div id="change-password-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔒 Đổi mật khẩu</h3>
                <button class="close-modal" onclick="closeChangePasswordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="change-password-form">
                    <div class="form-group">
                        <label for="current-password">🔑 Mật khẩu hiện tại *</label>
                        <input type="password" id="current-password" class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    <div class="form-group">
                        <label for="new-password">🆕 Mật khẩu mới *</label>
                        <input type="password" id="new-password" class="form-control" required placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">✅ Xác nhận mật khẩu mới *</label>
                        <input type="password" id="confirm-password" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeChangePasswordModal()">❌ Hủy</button>
                <button class="btn-success" onclick="savePasswordChanges()">🔒 Đổi mật khẩu</button>
            </div>
        </div>
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
                        <input type="email" id="login-email" class="form-control" required placeholder="Nhập địa chỉ email" onfocus="showSavedAccounts()">
                        <div id="saved-accounts" class="saved-accounts"></div>
                    </div>
                    <div class="form-group">
                        <label for="login-password">🔒 Mật khẩu *</label>
                        <input type="password" id="login-password" class="form-control" required placeholder="Nhập mật khẩu">
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Ghi nhớ đăng nhập</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#" onclick="showForgotPasswordModal(); return false;">Quên mật khẩu?</a>
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

    <!-- Modal quên mật khẩu -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔐 Quên mật khẩu</h3>
                <button class="close-modal" onclick="closeForgotPasswordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="forgot-password-form">
                    <div class="form-group">
                        <label for="forgot-email">📧 Email *</label>
                        <input type="email" id="forgot-email" class="form-control" required placeholder="Nhập địa chỉ email của bạn">
                    </div>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 15px;">
                        Chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu đến email của bạn.
                    </p>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeForgotPasswordModal()">❌ Hủy</button>
                <button class="btn-success" onclick="sendPasswordReset()">📧 Gửi yêu cầu</button>
            </div>
        </div>
    </div>

    <!-- Thông báo -->
    <div id="notification" class="notification"></div>

    <script>
        let currentUser = <?php echo $user ? json_encode($user) : 'null'; ?>;
        let users = JSON.parse(localStorage.getItem('calendarUsers')) || [];
        let savedAccounts = [];
        
        let allHistoryData = {
            xemNgay: [],
            huongNha: [],
            favorites: [],
            events: [],
            kethon: [],
            laman: [],
            xaynha: [],
            sinhcon: []
        };

        // ==================== LOAD DATA FROM DATABASE ====================

        async function loadAllHistoryData() {
            if (!currentUser) return;
            
            console.log('Đang tải tất cả lịch sử từ database...');
            
            // Load tất cả dữ liệu song song
            await Promise.all([
                loadFavoritesData(),
                loadEventsData(),
                loadMarriageHistory(),
                loadXemNgayHistory(),
                loadHuongNhaHistory(),
                loadLamanHistory(),
                loadXaynhaHistory(),
                loadSinhConHistory()
            ]);
            
            console.log('Dữ liệu đã tải từ database:', {
                favorites: allHistoryData.favorites?.length || 0,
                events: allHistoryData.events?.length || 0,
                kethon: allHistoryData.kethon?.length || 0,
                xemNgay: allHistoryData.xemNgay?.length || 0
            });
            
            // Load dữ liệu gần đây sau khi đã có tất cả
            await loadRecentData();
            updateStats();
        }

        // Load lịch sử kết hôn từ database
        async function loadMarriageHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_marriage_history.php?limit=20');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.kethon = data.history;
                    return data.history;
                } else {
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử kết hôn:', error);
                return [];
            }
        }

        // Load danh sách yêu thích từ database
        async function loadFavoritesData() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                if (data.success && data.favorites) {
                    allHistoryData.favorites = data.favorites;
                    renderFavoritesList(data.favorites);
                } else {
                    renderFavoritesList([]);
                }
            } catch (error) {
                console.error('Lỗi tải danh sách yêu thích:', error);
                renderFavoritesList([]);
            }
        }

        // Load sự kiện từ database
        async function loadEventsData() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_events.php');
                const data = await response.json();
                
                if (data.success && data.events) {
                    allHistoryData.events = data.events;
                    renderEventsList(data.events);
                } else {
                    renderEventsList([]);
                }
            } catch (error) {
                console.error('Lỗi tải sự kiện:', error);
                renderEventsList([]);
            }
        }

        // Load lịch sử xem ngày từ database
        async function loadXemNgayHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_history_xemngay.php');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.xemNgay = data.history;
                    return data.history;
                } else {
                    console.log('Không có lịch sử xem ngày:', data.message);
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử xem ngày:', error);
                return [];
            }
        }

        // Load lịch sử hướng nhà từ database
        async function loadHuongNhaHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_huongnha_history_user.php?limit=20');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.huongNha = data.history;
                    return data.history;
                } else {
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử xem hướng nhà:', error);
                return [];
            }
        }

        // Load lịch sử làm ăn từ database
        async function loadLamanHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_laman_history_user.php');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.laman = data.history;
                    return data.history;
                } else {
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử làm ăn:', error);
                return [];
            }
        }

        // Load lịch sử xây nhà từ database
        async function loadXaynhaHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_xaynha_history_user.php?limit=20');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.xaynha = data.history;
                    return data.history;
                } else {
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử xây nhà:', error);
                return [];
            }
        }

        // Load lịch sử sinh con từ database
        async function loadSinhConHistory() {
            if (!currentUser) return [];
            
            try {
                const response = await fetch('api/get_sinhcon_history_user.php?limit=20');
                const data = await response.json();
                
                if (data.success && data.history) {
                    allHistoryData.sinhcon = data.history;
                    return data.history;
                } else {
                    return [];
                }
            } catch (error) {
                console.error('Lỗi tải lịch sử sinh con:', error);
                return [];
            }
        }

        // ==================== RENDER FUNCTIONS ====================

        function renderFavoritesList(favorites) {
            const favoritesList = document.getElementById('favorites-list');
            
            if (!favorites || favorites.length === 0) {
                favoritesList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">❤️</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có mục nào trong danh sách yêu thích</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thêm các kết quả vào yêu thích từ các trang dịch vụ!</p>
                    </div>
                `;
                return;
            }
            
            favoritesList.innerHTML = favorites.map(favorite => {
                let solarDate = favorite.solar_date || 'N/A';
                const lunarDate = favorite.lunar_date || 'N/A';
                const ratingText = favorite.rating_text || 'Không có đánh giá';
                const score = favorite.score || 0;
                
                if (solarDate !== 'N/A' && solarDate.includes('-')) {
                    const parts = solarDate.split('-');
                    if (parts.length === 3 && parts[0].length === 4) {
                        solarDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
                
                let ratingClass = 'neutral-rating';
                if (score >= 7) ratingClass = 'good-rating';
                else if (score <= 3) ratingClass = 'bad-rating';
                
                let favoriteType = '📅 Xem ngày';
                let actionHandler = `viewFavoriteDetail('${solarDate}')`;
                
                if (ratingText.includes('Xem hướng nhà') || ratingText.includes('Cung')) {
                    favoriteType = '🧭 Xem hướng';
                    const yearMatch = ratingText.match(/Năm (\d+)/);
                    if (yearMatch && yearMatch[1]) {
                        actionHandler = `loadHuongNhaFromHistory(${yearMatch[1]})`;
                    } else {
                        actionHandler = `viewHuongNhaFavorite(${favorite.id})`;
                    }
                } else if (ratingText.includes('Sinh con:')) {
                    favoriteType = '👶 Sinh con';
                    const match = ratingText.match(/Cha (\d+) - Mẹ (\d+)/);
                    if (match && match[1] && match[2]) {
                        actionHandler = `loadSinhConFromHistory('${match[1]}', '${match[2]}')`;
                    } else {
                        actionHandler = `viewSinhConFavorite(${favorite.id})`;
                    }
                } else if (ratingText.includes('Xây nhà:')) {
                    favoriteType = '🏠 Xây nhà';
                    actionHandler = `viewXaynhaFavorite(${favorite.id})`;
                } else if (ratingText.includes('Kết hôn:')) {
                    favoriteType = '💑 Kết hôn';
                    const match = ratingText.match(/Nam (\d+) - Nữ (\d+)/);
                    if (match && match[1] && match[2]) {
                        actionHandler = `loadMarriageFromHistory('${match[1]}', '${match[2]}')`;
                    } else {
                        actionHandler = `viewMarriageFavorite(${favorite.id})`;
                    }
                } else if (ratingText.includes('Làm ăn:')) {
                    favoriteType = '💰 Làm ăn';
                    const match = ratingText.match(/Tuổi (\d+) & (\d+)/);
                    if (match && match[1] && match[2]) {
                        actionHandler = `loadLamanFromHistory('${match[1]}', '${match[2]}')`;
                    } else {
                        actionHandler = `viewLamanFavorite(${favorite.id})`;
                    }
                }
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">${favoriteType}</div>
                            <div class="history-data">
                                <strong>${favoriteType === '🧭 Xem hướng' ? 'Thông tin: ' + ratingText : favoriteType === '👶 Sinh con' ? 'Thông tin: ' + ratingText : favoriteType === '🏠 Xây nhà' ? 'Thông tin: ' + ratingText : favoriteType === '💑 Kết hôn' ? 'Thông tin: ' + ratingText : favoriteType === '💰 Làm ăn' ? 'Thông tin: ' + ratingText : 'Dương: ' + solarDate}</strong><br>
                                ${favoriteType === '📅 Xem ngày' ? `<small>Âm: ${lunarDate}</small><br>` : ''}
                                ${favoriteType === '📅 Xem ngày' ? `<small>Đánh giá: <span class="${ratingClass}">${getRatingDisplay(score)}</span></small><br>` : ''}
                                ${favoriteType === '📅 Xem ngày' ? `<small>Điểm: ${score}/10</small>` : ''}
                            </div>
                            <div class="history-date">${formatDateTime(favorite.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="${actionHandler}" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="removeFavoriteFromAPI(${favorite.id})" title="Xóa khỏi yêu thích">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Hiển thị danh sách sự kiện
        function renderEventsList(events) {
            const eventsList = document.getElementById('events-list');
            
            if (!events || events.length === 0) {
                eventsList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">🗓️</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có sự kiện nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thêm sự kiện từ trang Xem Ngày!</p>
                    </div>
                `;
                return;
            }
            
            eventsList.innerHTML = events.map(event => {
                const eventDate = new Date(event.date);
                const dateString = eventDate.toLocaleDateString('vi-VN');
                const timeString = event.time ? ` • ${event.time}` : '';
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">🗓️ Sự kiện</div>
                            <div class="history-data">
                                <strong>${event.title}</strong><br>
                                <small>📅 ${dateString}${timeString}</small>
                                ${event.description ? `<br><small>${event.description}</small>` : ''}
                            </div>
                            <div class="history-date">${formatDateTime(event.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewEventDetail(${event.id})" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="deleteEvent(${event.id})" title="Xóa sự kiện">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Xem chi tiết sự kiện
        function viewEventDetail(eventId) {
            const event = allHistoryData.events.find(e => e.id === eventId);
            if (event) {
                // Chuyển đến trang chính và chọn ngày có sự kiện
                window.location.href = `index.php?date=${event.date}`;
            }
        }

        // Xóa sự kiện
        async function deleteEvent(eventId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) return;
            
            try {
                const response = await fetch('api/remove_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ eventId: eventId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa sự kiện!', 'success');
                    loadEventsData(); // Tải lại danh sách
                    updateStats(); // Cập nhật thống kê
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa sự kiện:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        function getRatingDisplay(score) {
            if (score >= 7) return 'TỐT';
            else if (score <= 3) return 'XẤU';
            return 'BÌNH THƯỜNG';
        }

        function loadMarriageData() {
            if (!currentUser) return;
            
            const marriageList = document.getElementById('kethon-list');
            
            if (allHistoryData.kethon.length === 0) {
                marriageList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">💑</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có tra cứu kết hôn nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thực hiện phân tích tuổi kết hôn trên trang Kết Hôn!</p>
                    </div>
                `;
                return;
            }
            
            marriageList.innerHTML = allHistoryData.kethon.map(item => {
                let ratingClass = 'neutral-rating';
                if (item.score >= 7) ratingClass = 'good-rating';
                else if (item.score <= 3) ratingClass = 'bad-rating';
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">💑 Kết hôn</div>
                            <div class="history-data">
                                <strong>Nam: ${item.male_year} - Nữ: ${item.female_year}</strong><br>
                                <small>Đánh giá: <span class="${ratingClass}">${item.evaluation}</span></small><br>
                                <small>Điểm: ${item.score}/10</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewMarriageDetail(${item.id})" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="deleteMarriageHistory(${item.id})" title="Xóa lịch sử">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function loadLamanData() {
            if (!currentUser) return;
            
            const lamanList = document.getElementById('laman-list');
            
            if (allHistoryData.laman.length === 0) {
                lamanList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">💰</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có tra cứu làm ăn nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thực hiện tra cứu hợp tác làm ăn trên trang Làm Ăn!</p>
                    </div>
                `;
                return;
            }
            
            lamanList.innerHTML = allHistoryData.laman.map(item => {
                const detail = typeof item.detail === 'string' ? JSON.parse(item.detail) : item.detail;
                let ratingClass = 'neutral-rating';
                if (item.score >= 3) ratingClass = 'good-rating';
                else if (item.score <= 0) ratingClass = 'bad-rating';
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">💰 Làm ăn</div>
                            <div class="history-data">
                                <strong>Hợp tác: ${item.self_year} & ${item.partner_year}</strong><br>
                                <small>Đánh giá: <span class="${ratingClass}">${item.evaluation}</span></small><br>
                                <small>Điểm: ${item.score}/5</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewLamanDetail(${item.id})" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="deleteLamanHistory(${item.id})" title="Xóa lịch sử">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function loadXaynhaData() {
            if (!currentUser) return;
            
            const xaynhaList = document.getElementById('xaynha-list');
            
            if (allHistoryData.xaynha.length === 0) {
                xaynhaList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">🏠</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có tra cứu xây nhà nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thực hiện tra cứu tuổi xây nhà trên trang Xây Nhà!</p>
                    </div>
                `;
                return;
            }
            
            xaynhaList.innerHTML = allHistoryData.xaynha.map(item => {
                const detail = typeof item.detail === 'string' ? JSON.parse(item.detail) : item.detail;
                let ratingClass = 'neutral-rating';
                if (item.evaluation === 'NÊN LÀM') ratingClass = 'good-rating';
                else if (item.evaluation === 'KHÔNG NÊN') ratingClass = 'bad-rating';
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">🏠 Xây nhà</div>
                            <div class="history-data">
                                <strong>Gia chủ: ${item.owner_year} → Năm xây: ${item.build_year}</strong><br>
                                <small>Đánh giá: <span class="${ratingClass}">${item.evaluation}</span></small><br>
                                <small>Kim Lâu: ${item.kimlau ? '❌ Có' : '✅ Không'} | Hoang Ốc: ${item.hoangoc ? '❌ Có' : '✅ Không'} | Tam Tai: ${item.tamtai ? '❌ Có' : '✅ Không'}</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewXaynhaDetail(${item.id})" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="deleteXaynhaHistory(${item.id})" title="Xóa lịch sử">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function loadSinhConData() {
            if (!currentUser) return;
            
            const sinhconList = document.getElementById('sinhcon-list');
            
            if (allHistoryData.sinhcon.length === 0) {
                sinhconList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">👶</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có tra cứu sinh con nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy thực hiện tra cứu tuổi sinh con trên trang Sinh Con!</p>
                    </div>
                `;
                return;
            }
            
            sinhconList.innerHTML = allHistoryData.sinhcon.map(item => {
                const detail = typeof item.detail === 'string' ? JSON.parse(item.detail) : item.detail;
                let ratingClass = 'neutral-rating';
                if (item.score >= 7) ratingClass = 'good-rating';
                else if (item.score <= 3) ratingClass = 'bad-rating';
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">👶 Sinh con</div>
                            <div class="history-data">
                                <strong>Cha: ${item.father_year} - Mẹ: ${item.mother_year}</strong><br>
                                <small>Năm con: ${item.child_year}</small><br>
                                <small>Đánh giá: <span class="${ratingClass}">${item.evaluation}</span></small><br>
                                <small>Điểm: ${item.score}/10</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.created_at)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewSinhConDetail(${item.id})" title="Xem chi tiết">👁️</button>
                            <button class="btn-danger" onclick="deleteSinhConHistory(${item.id})" title="Xóa lịch sử">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // ==================== RECENT DATA ====================

        async function loadRecentData() {
            if (!currentUser) return;
            
            let allRecentItems = [];
            
            // Kết hợp tất cả lịch sử từ các loại
            if (allHistoryData.xemNgay && allHistoryData.xemNgay.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.xemNgay.map(item => ({
                    ...item,
                    type: 'xem_ngay',
                    timestamp: item.created_at,
                    title: `Xem ngày: ${formatDisplayDate(item.query_date)}`,
                    description: `Âm lịch: ${item.lunar_date || 'N/A'} | Đánh giá: ${getRatingText(item.rating)}`
                })));
            }
            
            // THÊM PHẦN SỰ KIỆN VÀO RECENT
            if (allHistoryData.events && allHistoryData.events.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.events.map(item => ({
                    ...item,
                    type: 'event',
                    timestamp: item.created_at,
                    title: `🗓️ Sự kiện: ${item.title}`,
                    description: `Ngày: ${formatDisplayDate(item.date)}${item.time ? ' • Giờ: ' + item.time : ''}`
                })));
            }
            
            if (allHistoryData.kethon && allHistoryData.kethon.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.kethon.map(item => ({
                    ...item,
                    type: 'kethon',
                    timestamp: item.created_at,
                    title: `💑 Kết hôn: Nam ${item.male_year} - Nữ ${item.female_year}`,
                    description: `Điểm: ${item.score}/10 | Đánh giá: ${item.evaluation}`
                })));
            }
            
            if (allHistoryData.favorites && allHistoryData.favorites.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.favorites.map(item => {
                    const solarDate = formatDisplayDate(item.solar_date);
                    return {
                        ...item,
                        type: 'favorite',
                        timestamp: item.created_at,
                        title: '❤️ Yêu thích',
                        description: `Dương: ${solarDate} | Âm: ${item.lunar_date} | Điểm: ${item.score || 'N/A'}/10`
                    };
                }));
            }
            
            if (allHistoryData.laman && allHistoryData.laman.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.laman.map(item => ({
                    ...item,
                    type: 'laman',
                    timestamp: item.created_at,
                    title: `💰 Làm ăn: ${item.self_year} & ${item.partner_year}`,
                    description: `Điểm: ${item.score}/5 | Đánh giá: ${item.evaluation}`
                })));
            }
            
            if (allHistoryData.xaynha && allHistoryData.xaynha.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.xaynha.map(item => ({
                    ...item,
                    type: 'xaynha',
                    timestamp: item.created_at,
                    title: `🏠 Xây nhà: ${item.owner_year} → ${item.build_year}`,
                    description: `Đánh giá: ${item.evaluation} | Kim Lâu: ${item.kimlau ? 'Có' : 'Không'} | Hoang Ốc: ${item.hoangoc ? 'Có' : 'Không'}`
                })));
            }
            
            if (allHistoryData.sinhcon && allHistoryData.sinhcon.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.sinhcon.map(item => ({
                    ...item,
                    type: 'sinhcon',
                    timestamp: item.created_at,
                    title: `👶 Sinh con: Cha ${item.father_year} - Mẹ ${item.mother_year}`,
                    description: `Năm con: ${item.child_year} | Điểm: ${item.score}/10 | Đánh giá: ${item.evaluation}`
                })));
            }
            
            if (allHistoryData.huongNha && allHistoryData.huongNha.length > 0) {
                allRecentItems = allRecentItems.concat(allHistoryData.huongNha.map(item => ({
                    ...item,
                    type: 'huong_nha',
                    timestamp: item.created_at,
                    title: `🧭 Xem hướng nhà - Năm sinh: ${item.owner_year}`,
                    description: `Hướng tốt: ${item.good_directions?.substring(0, 100) || 'N/A'}`
                })));
            }
            
            // Sắp xếp theo thời gian mới nhất
            allRecentItems.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            
            // Chỉ lấy 10 mục gần nhất
            const recentItems = allRecentItems.slice(0, 10);
            
            renderRecentList(recentItems);
            renderAllList(allRecentItems);
        }

        function renderRecentList(items) {
            const recentList = document.getElementById('recent-list');
            
            if (!items || items.length === 0) {
                recentList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">📝</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có hoạt động nào gần đây</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy bắt đầu sử dụng các dịch vụ của Lịch Việt!</p>
                    </div>
                `;
                return;
            }
            
            recentList.innerHTML = items.map(item => {
                const icon = getHistoryIcon(item.type);
                const typeText = getHistoryTypeText(item.type);
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">${icon} ${typeText}</div>
                            <div class="history-data">
                                <strong>${item.title}</strong><br>
                                <small>${item.description}</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.timestamp)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewRecentItem('${item.type}', ${item.id})">👁️ Xem</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderAllList(items) {
            const allList = document.getElementById('all-list');
            
            if (!items || items.length === 0) {
                allList.innerHTML = `
                    <div class="empty-history">
                        <div class="empty-icon">📝</div>
                        <p style="font-size: 1.1rem; color: #999;">Chưa có hoạt động nào</p>
                        <p style="font-size: 0.9rem; color: #aaa; margin-top: 10px;">Hãy bắt đầu sử dụng các dịch vụ của Lịch Việt!</p>
                    </div>
                `;
                return;
            }
            
            allList.innerHTML = items.map(item => {
                const icon = getHistoryIcon(item.type);
                const typeText = getHistoryTypeText(item.type);
                
                return `
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-type">${icon} ${typeText}</div>
                            <div class="history-data">
                                <strong>${item.title}</strong><br>
                                <small>${item.description}</small>
                            </div>
                            <div class="history-date">${formatDateTime(item.timestamp)}</div>
                        </div>
                        <div class="history-actions">
                            <button class="btn-info" onclick="viewRecentItem('${item.type}', ${item.id})">👁️ Xem</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // ==================== VIEW FUNCTIONS ====================

        function viewRecentItem(type, id) {
            switch(type) {
                case 'xem_ngay':
                    const xemNgayItem = allHistoryData.xemNgay.find(item => item.id === id);
                    if (xemNgayItem) {
                        viewXemNgayDetail(xemNgayItem.query_date);
                    }
                    break;
                case 'huong_nha':
                    const huongNhaItem = allHistoryData.huongNha.find(item => item.id === id);
                    if (huongNhaItem) {
                        loadHuongNhaFromHistory(huongNhaItem.owner_year);
                    }
                    break;
                case 'favorite':
                    const favoriteItem = allHistoryData.favorites.find(item => item.id === id);
                    if (favoriteItem) {
                        if (favoriteItem.rating_text?.includes('Xem hướng nhà')) {
                            const yearMatch = favoriteItem.rating_text.match(/Năm (\d+)/);
                            if (yearMatch && yearMatch[1]) {
                                loadHuongNhaFromHistory(yearMatch[1]);
                            } else {
                                window.location.href = 'huongnha.php';
                            }
                        } else if (favoriteItem.rating_text?.includes('Sinh con:')) {
                            const match = favoriteItem.rating_text.match(/Cha (\d+) - Mẹ (\d+)/);
                            if (match && match[1] && match[2]) {
                                loadSinhConFromHistory(match[1], match[2]);
                            } else {
                                window.location.href = 'concai.php';
                            }
                        } else if (favoriteItem.rating_text?.includes('Xây nhà:')) {
                            viewXaynhaFavorite(id);
                        } else if (favoriteItem.rating_text?.includes('Kết hôn:')) {
                            const match = favoriteItem.rating_text.match(/Nam (\d+) - Nữ (\d+)/);
                            if (match && match[1] && match[2]) {
                                loadMarriageFromHistory(match[1], match[2]);
                            } else {
                                window.location.href = 'kethon.php';
                            }
                        } else if (favoriteItem.rating_text?.includes('Làm ăn:')) {
                            const match = favoriteItem.rating_text.match(/Tuổi (\d+) & (\d+)/);
                            if (match && match[1] && match[2]) {
                                loadLamanFromHistory(match[1], match[2]);
                            } else {
                                window.location.href = 'laman.php';
                            }
                        } else {
                            viewFavoriteDetail(favoriteItem.solar_date);
                        }
                    }
                    break;
                case 'event':
                    const eventItem = allHistoryData.events.find(item => item.id === id);
                    if (eventItem) {
                        viewEventDetail(id);
                    }
                    break;
                case 'kethon':
                    const marriageItem = allHistoryData.kethon.find(item => item.id === id);
                    if (marriageItem) {
                        viewMarriageDetail(id);
                    }
                    break;
                case 'laman':
                    const lamanItem = allHistoryData.laman.find(item => item.id === id);
                    if (lamanItem) {
                        viewLamanDetail(id);
                    }
                    break;
                case 'xaynha':
                    const xaynhaItem = allHistoryData.xaynha.find(item => item.id === id);
                    if (xaynhaItem) {
                        viewXaynhaDetail(id);
                    }
                    break;
                case 'sinhcon':
                    const sinhconItem = allHistoryData.sinhcon.find(item => item.id === id);
                    if (sinhconItem) {
                        viewSinhConDetail(id);
                    }
                    break;
                default:
                    showNotification('Tính năng đang được phát triển', 'info');
            }
        }

        function viewMarriageDetail(id) {
            const marriageItem = allHistoryData.kethon.find(item => item.id === id);
            if (marriageItem) {
                window.location.href = `kethon.php?loadHistory=${id}`;
            }
        }

        function viewLamanDetail(id) {
            const lamanItem = allHistoryData.laman.find(item => item.id === id);
            if (lamanItem) {
                window.location.href = `laman.php?loadHistory=${id}`;
            }
        }

        function viewXaynhaDetail(id) {
            const xaynhaItem = allHistoryData.xaynha.find(item => item.id === id);
            if (xaynhaItem) {
                window.location.href = `xaynha.php?loadHistory=${id}`;
            }
        }

        function viewSinhConDetail(id) {
            const sinhconItem = allHistoryData.sinhcon.find(item => item.id === id);
            if (sinhconItem) {
                window.location.href = `concai.php?loadHistory=${id}`;
            }
        }

        function viewXemNgayDetail(queryDate) {
            window.location.href = `index.php?date=${queryDate}`;
        }

        function viewFavoriteDetail(solarDate) {
            let formattedDate = solarDate;
            
            if (solarDate.includes('-')) {
                const parts = solarDate.split('-');
                if (parts.length === 3) {
                    if (parts[0].length === 4) {
                        formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    } else {
                        formattedDate = solarDate;
                    }
                }
            }
            
            window.location.href = `chuyenngay.php?loadDate=${encodeURIComponent(formattedDate)}`;
        }

        function loadHuongNhaFromHistory(year) {
            window.location.href = `huongnha.php?loadYear=${year}`;
        }

        function loadSinhConFromHistory(fatherYear, motherYear) {
            window.location.href = `concai.php?fatherYear=${fatherYear}&motherYear=${motherYear}`;
        }

        function loadMarriageFromHistory(maleYear, femaleYear) {
            window.location.href = `kethon.php?maleYear=${maleYear}&femaleYear=${femaleYear}`;
        }

        function loadLamanFromHistory(selfYear, partnerYear) {
            window.location.href = `laman.php?selfYear=${selfYear}&partnerYear=${partnerYear}`;
        }

        // ==================== DELETE FUNCTIONS ====================

        async function removeFavoriteFromAPI(favoriteId) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục này khỏi danh sách yêu thích?')) return;
            
            try {
                const response = await fetch('api/remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: favoriteId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa khỏi danh sách yêu thích!', 'success');
                    loadFavoritesData();
                    updateStats();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa yêu thích:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        async function deleteMarriageHistory(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa lịch sử tra cứu kết hôn này?')) return;
            
            try {
                const response = await fetch('api/delete_marriage_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa lịch sử kết hôn!', 'success');
                    loadMarriageHistory();
                    loadMarriageData();
                    updateStats();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa lịch sử kết hôn:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        async function deleteLamanHistory(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa lịch sử tra cứu làm ăn này?')) return;
            
            try {
                const response = await fetch('api/delete_laman_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa lịch sử làm ăn!', 'success');
                    loadLamanHistory();
                    loadLamanData();
                    updateStats();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa lịch sử làm ăn:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        async function deleteXaynhaHistory(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa lịch sử tra cứu xây nhà này?')) return;
            
            try {
                const response = await fetch('api/delete_xaynha_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa lịch sử xây nhà!', 'success');
                    loadXaynhaHistory();
                    loadXaynhaData();
                    updateStats();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa lịch sử xây nhà:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        async function deleteSinhConHistory(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa lịch sử tra cứu sinh con này?')) return;
            
            try {
                const response = await fetch('api/delete_sinhcon_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa lịch sử sinh con!', 'success');
                    loadSinhConHistory();
                    loadSinhConData();
                    updateStats();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa lịch sử sinh con:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        // ==================== HELPER FUNCTIONS ====================

        function getHistoryIcon(type) {
            const icons = {
                'xem_ngay': '📅',
                'huong_nha': '🧭',
                'favorite': '❤️',
                'event': '🗓️',
                'kethon': '💑',
                'laman': '💰',
                'xaynha': '🏠',
                'sinhcon': '👶',
                'default': '📝'
            };
            return icons[type] || icons.default;
        }

        function getHistoryTypeText(type) {
            const texts = {
                'xem_ngay': 'Xem ngày',
                'huong_nha': 'Xem hướng',
                'favorite': 'Yêu thích',
                'event': 'Sự kiện',
                'kethon': 'Kết hôn',
                'laman': 'Làm ăn',
                'xaynha': 'Xây nhà',
                'sinhcon': 'Sinh con',
                'default': 'Tra cứu'
            };
            return texts[type] || texts.default;
        }

        function getRatingText(rating) {
            switch(rating) {
                case 'tot': return 'TỐT';
                case 'xau': return 'XẤU';
                case 'binh_thuong': return 'BÌNH THƯỜNG';
                default: return rating || 'N/A';
            }
        }

        function formatDisplayDate(dateString) {
            if (!dateString) return 'N/A';
            
            if (dateString.includes('-')) {
                const parts = dateString.split('-');
                if (parts.length === 3) {
                    if (parts[0].length === 4) {
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    } else {
                        return dateString;
                    }
                }
            }
            return dateString;
        }

        function formatDateForDisplay(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // ==================== STATS AND INITIALIZATION ====================

        function updateStats() {
            if (!currentUser) return;
            
            const stats = {
                totalSearches: allHistoryData.xemNgay.length + allHistoryData.kethon.length + 
                              allHistoryData.laman.length + allHistoryData.xaynha.length + 
                              allHistoryData.sinhcon.length + allHistoryData.huongNha.length,
                totalFavorites: allHistoryData.favorites.length,
                totalEvents: allHistoryData.events.length,
                kethonAnalyses: allHistoryData.kethon.length,
                lamanAnalyses: allHistoryData.laman.length,
                xaynhaAnalyses: allHistoryData.xaynha.length,
                sinhconAnalyses: allHistoryData.sinhcon.length
            };
            
            document.getElementById('stat-searches').textContent = stats.totalSearches;
            document.getElementById('stat-favorites').textContent = stats.totalFavorites;
            document.getElementById('stat-events').textContent = stats.totalEvents;
            document.getElementById('stat-kethon').textContent = stats.kethonAnalyses;
            document.getElementById('stat-laman').textContent = stats.lamanAnalyses;
            document.getElementById('stat-xaynha').textContent = stats.xaynhaAnalyses;
            document.getElementById('stat-sinhcon').textContent = stats.sinhconAnalyses;
        }

        function updateProfileDisplay() {
            const profileContent = document.getElementById('profile-content');
            const guestMessage = document.getElementById('guest-message');
            const historySection = document.getElementById('history-section');
            const statsSection = document.getElementById('stats-section');
            
            if (currentUser) {
                profileContent.style.display = 'block';
                guestMessage.style.display = 'none';
                historySection.style.display = 'block';
                statsSection.style.display = 'grid';
                
                document.getElementById('profile-name').textContent = currentUser.name || '—';
                document.getElementById('profile-email').textContent = currentUser.email || '—';
                document.getElementById('profile-phone').textContent = currentUser.phone || '—';
                document.getElementById('profile-birthday').textContent = currentUser.birthday ? formatDate(currentUser.birthday) : '—';
                document.getElementById('profile-gender').textContent = getGenderText(currentUser.gender);
                document.getElementById('profile-role').textContent = currentUser.role === 'admin' ? '👑 Quản trị viên' : '👤 Người dùng';
                document.getElementById('profile-role').className = `user-role ${currentUser.role === 'admin' ? 'role-admin' : 'role-user'}`;
                document.getElementById('profile-joined').textContent = currentUser.created_at ? formatDate(currentUser.created_at) : '—';
                
                updateStats();
                loadAllHistoryData();
            } else {
                profileContent.style.display = 'none';
                guestMessage.style.display = 'block';
                historySection.style.display = 'none';
                statsSection.style.display = 'none';
            }
        }

        // ==================== HEADER UPDATE FUNCTION ====================

        function updateHeaderDisplay() {
            if (!currentUser) return;
            
            // Cập nhật header
            document.getElementById('user-display-name').textContent = currentUser.name;
            document.getElementById('user-display-email').textContent = currentUser.email;
            
            // Cập nhật avatar
            const initials = currentUser.name
                .split(' ')
                .map(n => n[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();
            document.getElementById('user-avatar').textContent = initials;
            
            // Hiển thị/ẩn nút admin dựa trên role
            const adminBtn = document.getElementById('admin-btn');
            if (adminBtn) {
                if (currentUser.role === 'admin') {
                    adminBtn.style.display = 'inline-block';
                } else {
                    adminBtn.style.display = 'none';
                }
            }
            
            // Cập nhật saved accounts trong localStorage
            saveAccountToLocal();
        }

        function initializeEventListeners() {
            document.getElementById('login-btn').addEventListener('click', showLoginModal);
            document.getElementById('register-btn').addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn').addEventListener('click', logout);
            document.getElementById('profile-btn').addEventListener('click', showProfileModal);
            
            // History tabs
            document.querySelectorAll('.history-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.history-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.history-content').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(`${tabId}-history`).classList.add('active');
                    
                    if (tabId === 'favorites') {
                        loadFavoritesData();
                    } else if (tabId === 'events') {
                        loadEventsData();
                    } else if (tabId === 'kethon') {
                        loadMarriageData();
                    } else if (tabId === 'laman') {
                        loadLamanData();
                    } else if (tabId === 'xaynha') {
                        loadXaynhaData();
                    } else if (tabId === 'sinhcon') {
                        loadSinhConData();
                    } else if (tabId === 'all') {
                        loadRecentData();
                    } else if (tabId === 'recent') {
                        loadRecentData();
                    }
                });
            });
            
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });
            
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#login-email') && !e.target.closest('#saved-accounts')) {
                    document.getElementById('saved-accounts').style.display = 'none';
                }
            });
        }
            // ==================== ADMIN FUNCTIONS ====================
            function goToAdmin() {
                if (currentUser && currentUser.role === 'admin') {
                    // Redirect đến admin panel
                    window.location.href = 'admin.php';
                } else {
                    showNotification('Bạn không có quyền truy cập trang quản trị!', 'error');
                }
            }
        // ==================== AUTH FUNCTIONS ====================

        function loadSavedAccounts() {
            const saved = localStorage.getItem('savedAccounts');
            if (saved) {
                savedAccounts = JSON.parse(saved);
            }
        }

        function showSavedAccounts() {
            const savedAccountsContainer = document.getElementById('saved-accounts');
            
            if (savedAccounts.length === 0) {
                savedAccountsContainer.style.display = 'none';
                return;
            }
            
            savedAccountsContainer.innerHTML = savedAccounts.map(account => `
                <div class="saved-account-item" onclick="selectSavedAccount('${account.email}', '${account.name}')">
                    <div class="saved-account-avatar">${account.avatar}</div>
                    <div class="saved-account-details">
                        <div class="saved-account-name">${account.name}</div>
                        <div class="saved-account-email">${account.email}</div>
                    </div>
                    <button class="remove-account" onclick="event.stopPropagation(); removeSavedAccount('${account.email}')">&times;</button>
                </div>
            `).join('');
            
            savedAccountsContainer.style.display = 'block';
        }

        function selectSavedAccount(email, name) {
            document.getElementById('login-email').value = email;
            document.getElementById('saved-accounts').style.display = 'none';
            document.getElementById('login-password').focus();
        }

        function removeSavedAccount(email) {
            savedAccounts = savedAccounts.filter(acc => acc.email !== email);
            localStorage.setItem('savedAccounts', JSON.stringify(savedAccounts));
            showSavedAccounts();
        }

        function saveAccountToLocal() {
            if (currentUser) {
                const saved = localStorage.getItem('savedAccounts');
                let savedAccounts = saved ? JSON.parse(saved) : [];
                
                const existingIndex = savedAccounts.findIndex(acc => acc.email === currentUser.email);
                
                const userAvatar = currentUser.name
                    .split(' ')
                    .map(n => n[0])
                    .join('')
                    .substring(0, 2)
                    .toUpperCase();
                    
                if (existingIndex === -1) {
                    savedAccounts.push({
                        name: currentUser.name,
                        email: currentUser.email,
                        avatar: userAvatar
                    });
                } else {
                    savedAccounts[existingIndex].name = currentUser.name;
                    savedAccounts[existingIndex].avatar = userAvatar;
                }
                
                localStorage.setItem('savedAccounts', JSON.stringify(savedAccounts));
            }
        }

        async function performLogin() {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            const rememberMe = document.getElementById('remember-me').checked;

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

                    if (rememberMe) {
                        saveAccountToLocal();
                    }

                    document.getElementById('user-info').style.display = 'flex';
                    document.getElementById('auth-buttons').style.display = 'none';
                    
                    // Sử dụng hàm updateHeaderDisplay mới
                    updateHeaderDisplay();

                    closeLoginModal();
                    updateProfileDisplay();
                    showNotification(data.message || 'Đăng nhập thành công!', 'success');
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

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Email không hợp lệ!', 'error');
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

                    saveAccountToLocal();

                    document.getElementById('user-info').style.display = 'flex';
                    document.getElementById('auth-buttons').style.display = 'none';
                    
                    // Sử dụng hàm updateHeaderDisplay mới
                    updateHeaderDisplay();

                    closeRegisterModal();
                    updateProfileDisplay();
                    showNotification(data.message || 'Đăng ký thành công!', 'success');
                } else {
                    showNotification(data.message || 'Đăng ký thất bại!', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        function logout() {
            if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) return;

            fetch('api/logout.php', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    currentUser = null;
                    document.getElementById('user-info').style.display = 'none';
                    document.getElementById('auth-buttons').style.display = 'flex';
                    updateProfileDisplay();
                    showNotification(data.message || 'Đã đăng xuất thành công!', 'success');
                    
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
        }

        // ==================== PROFILE UPDATE FUNCTIONS ====================

        async function saveProfileChanges() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập!', 'error');
                return;
            }

            const name = document.getElementById('edit-name').value.trim();
            const phone = document.getElementById('edit-phone').value.trim();
            const birthday = document.getElementById('edit-birthday').value;
            const gender = document.getElementById('edit-gender').value;

            // Validation
            if (!name) {
                showNotification('Vui lòng nhập họ và tên!', 'error');
                return;
            }

            try {
                // Tạo FormData để gửi dữ liệu
                const formData = new FormData();
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('birthday', birthday);
                formData.append('gender', gender);

                const response = await fetch('api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // 1. Cập nhật currentUser với dữ liệu mới từ server
                    currentUser = {
                        ...currentUser,
                        name: data.user.name,
                        phone: data.user.phone,
                        birthday: data.user.birthday,
                        gender: data.user.gender
                    };

                    // 2. Cập nhật hiển thị header
                    updateHeaderDisplay();
                    
                    // 3. Cập nhật hiển thị profile
                    updateProfileDisplay();
                    
                    // 4. Hiển thị thông báo và đóng modal
                    showNotification('Cập nhật hồ sơ thành công!', 'success');
                    closeEditProfileModal();
                    
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi cập nhật profile:', error);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        async function savePasswordChanges() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập!', 'error');
                return;
            }

            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                showNotification('Vui lòng điền đầy đủ thông tin!', 'error');
                return;
            }

            if (newPassword.length < 6) {
                showNotification('Mật khẩu mới phải có ít nhất 6 ký tự!', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showNotification('Mật khẩu xác nhận không khớp!', 'error');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('current_password', currentPassword);
                formData.append('new_password', newPassword);
                formData.append('confirm_password', confirmPassword);

                const response = await fetch('api/change_password.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showNotification('Đổi mật khẩu thành công!', 'success');
                    closeChangePasswordModal();
                    document.getElementById('change-password-form').reset();
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi đổi mật khẩu:', error);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        // ==================== MODAL FUNCTIONS ====================

        function showEditProfileModal() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để chỉnh sửa hồ sơ', 'error');
                return;
            }
            
            const modal = document.getElementById('edit-profile-modal');
            modal.style.display = 'flex';
            
            document.getElementById('edit-name').value = currentUser.name || '';
            document.getElementById('edit-phone').value = currentUser.phone || '';
            document.getElementById('edit-birthday').value = currentUser.birthday || '';
            document.getElementById('edit-gender').value = currentUser.gender || '';
        }

        function closeEditProfileModal() {
            document.getElementById('edit-profile-modal').style.display = 'none';
        }

        function showChangePasswordModal() {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để đổi mật khẩu', 'error');
                return;
            }
            
            document.getElementById('change-password-modal').style.display = 'flex';
        }

        function closeChangePasswordModal() {
            document.getElementById('change-password-modal').style.display = 'none';
            document.getElementById('change-password-form').reset();
        }

        function showLoginModal() {
            document.getElementById('login-modal').style.display = 'flex';
            document.getElementById('saved-accounts').style.display = 'none';
            loadSavedAccounts();
        }

        function closeLoginModal() {
            document.getElementById('login-modal').style.display = 'none';
            document.getElementById('login-form').reset();
            document.getElementById('saved-accounts').style.display = 'none';
        }

        function showRegisterModal() {
            document.getElementById('register-modal').style.display = 'flex';
        }

        function closeRegisterModal() {
            document.getElementById('register-modal').style.display = 'none';
            document.getElementById('register-form').reset();
        }

        function showProfileModal() {
            // Đã ở trang profile
        }

        function showForgotPasswordModal() {
            closeLoginModal();
            document.getElementById('forgot-password-modal').style.display = 'flex';
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgot-password-modal').style.display = 'none';
            document.getElementById('forgot-password-form').reset();
        }

        function sendPasswordReset() {
            const email = document.getElementById('forgot-email').value.trim();
            
            if (!email) {
                showNotification('Vui lòng nhập địa chỉ email!', 'error');
                return;
            }
            
            showNotification(`Đã gửi hướng dẫn đặt lại mật khẩu đến ${email}`, 'success');
            closeForgotPasswordModal();
        }

        // ==================== UTILITY FUNCTIONS ====================

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3500);
        }

        function formatDate(dateString) {
            if (!dateString) return '—';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            } catch (e) {
                return dateString;
            }
        }

        function formatDateTime(dateString) {
            if (!dateString) return '—';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            } catch (e) {
                return dateString;
            }
        }

        function getGenderText(gender) {
            if (!gender) return '—';
            
            // Xử lý cả 2 định dạng: tiếng Anh và tiếng Việt
            switch(gender.toLowerCase()) {
                case 'male': 
                case 'nam': 
                    return '👨 Nam';
                case 'female': 
                case 'nu': 
                    return '👩 Nữ';
                case 'other': 
                case 'khac': 
                    return '⚧ Khác';
                default: 
                    return '—';
            }
        }

        function clearHistory() {
            if (confirm('Bạn có chắc chắn muốn xóa toàn bộ lịch sử tra cứu? Hành động này không thể hoàn tác!')) {
                showNotification('Tính năng đang được phát triển', 'info');
            }
        }

        function exportHistory() {
            if (!currentUser) return;
            
            const allData = {
                xemNgay: allHistoryData.xemNgay,
                kethon: allHistoryData.kethon,
                laman: allHistoryData.laman,
                xaynha: allHistoryData.xaynha,
                sinhcon: allHistoryData.sinhcon,
                favorites: allHistoryData.favorites,
                events: allHistoryData.events,
                exportDate: new Date().toISOString(),
                user: {
                    name: currentUser.name,
                    email: currentUser.email
                }
            };
            
            if (allHistoryData.xemNgay.length === 0 && allHistoryData.kethon.length === 0 && 
                allHistoryData.laman.length === 0 && allHistoryData.xaynha.length === 0 && 
                allHistoryData.sinhcon.length === 0 && allHistoryData.favorites.length === 0 && 
                allHistoryData.events.length === 0) {
                showNotification('Không có dữ liệu để xuất', 'error');
                return;
            }
            
            const dataStr = JSON.stringify(allData, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `lichviet-data-${new Date().getTime()}.json`;
            link.click();
            URL.revokeObjectURL(url);
            
            showNotification('Đã xuất dữ liệu thành công', 'success');
        }

        // ==================== INITIALIZATION ====================

        document.addEventListener('DOMContentLoaded', function() {
            initializeEventListeners();
            updateProfileDisplay();
            loadSavedAccounts();
        });
    </script>
</body>
</html>
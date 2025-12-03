<?php
session_start();
require_once "config.php";

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT u.id, u.name, u.email, u.role, u.created_at,
               up.phone, up.dob as birthday, up.gender
        FROM users u
        LEFT JOIN user_profiles up ON u.id = up.user_id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentUser = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyển Đổi Ngày - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        /* CSS RIÊNG CHO TRANG CHUYỂN ĐỔI */
        .app-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            padding: 24px;
        }

        @media (max-width: 900px) {
            .app-container {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #f8f9fa;
            padding: 22px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }

        .panel-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .form-row.three {
            grid-template-columns: repeat(3, 1fr);
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-weight: 600;
            color: #555;
        }

        .field input {
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: .2s;
            background: #fff;
        }

        .field input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,.1);
        }

        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .result {
            position: relative;
            margin-top: 16px;
            background: #fff;
            border-left: 4px solid #667eea;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        .close-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #efefef;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 6px 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .close-btn:hover {
            background: #e9e9e9;
        }

        .bar {
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            text-align: center;
            margin: 10px 0;
        }

        .bar.good {
            background: #d1e7dd;
            color: #0f5132;
            border: 2px solid #198754;
        }

        .bar.neutral {
            background: #fff3cd;
            color: #664d03;
            border: 2px solid #ffc107;
        }

        .bar.bad {
            background: #f8d7da;
            color: #842029;
            border: 2px solid #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 9px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        td.points {
            text-align: center;
            width: 80px;
            font-weight: 700;
        }

        .color-hop { color: #28a745; font-weight: bold; }
        .color-ky { color: #dc3545; font-weight: bold; }

        /* Favorites Section */
        .favorites-section {
            margin: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .favorites-list {
            list-style-type: none;
            padding-left: 0;
        }

        .favorite-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .favorite-item:last-child {
            border-bottom: none;
        }

        .favorite-date {
            font-weight: 600;
        }

        .favorite-actions {
            display: flex;
            gap: 5px;
        }

        .favorite-actions button {
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }

        .info {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            border-radius: 8px;
            padding: 10px;
        }

        .direction-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }

        .direction-card {
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .direction-good {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
        }

        .direction-bad {
            background: #ffebee;
            border-left: 4px solid #dc3545;
        }

        .direction-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .direction-icon {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        .direction-list {
            list-style-type: none;
            padding-left: 0;
        }

        .direction-list li {
            padding: 5px 0;
            display: flex;
            align-items: center;
        }

        .direction-list li:before {
            content: "•";
            margin-right: 8px;
            font-weight: bold;
        }

        .direction-good .direction-list li:before {
            color: #28a745;
        }

        .direction-bad .direction-list li:before {
            color: #dc3545;
        }

        .btn-favorite {
            background: #ffc107;
            color: #333;
        }

        .btn-favorite:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LỊCH VIỆT</h1>
            <p class="subtitle">Chuyển đổi ngày Dương lịch - Âm lịch</p>
            
            <div class="user-section">
                <?php if ($currentUser): ?>
                    <div class="user-info" id="user-info">
                        <div class="user-avatar" id="user-avatar">
                            <?php
                            $parts = explode(' ', $currentUser['name']);
                            $initials = '';
                            foreach ($parts as $p) {
                                $initials .= mb_substr($p, 0, 1);
                            }
                            echo strtoupper(mb_substr($initials, 0, 2));
                            ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name" id="user-display-name"><?= htmlspecialchars($currentUser['name']) ?></div>
                            <div class="user-email" id="user-display-email"><?= htmlspecialchars($currentUser['email']) ?></div>
                        </div>
                        <div class="user-actions">
                            <button id="profile-btn" class="btn-info">📋 Hồ sơ</button>
                            <button id="logout-btn" class="btn-secondary">🚪 Đăng xuất</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons" id="auth-buttons">
                        <button id="login-btn" class="btn-secondary">🔑 Đăng nhập</button>
                        <button id="register-btn" class="btn-success">📝 Đăng ký</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Xem Ngày</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="chuyenngay.php">
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
      
        <div class="app-container" id="chuyen-doi-container">
            <!-- Dương -> Âm -->
            <div class="panel">
                <div class="panel-title">🌞 Dương lịch → Âm lịch</div>
                <div class="form-row three">
                    <div class="field">
                        <label>Ngày</label>
                        <input type="number" id="solarDay" min="1" max="31" value="<?= date('d') ?>">
                    </div>
                    <div class="field">
                        <label>Tháng</label>
                        <input type="number" id="solarMonth" min="1" max="12" value="<?= date('m') ?>">
                    </div>
                    <div class="field">
                        <label>Năm</label>
                        <input type="number" id="solarYear" min="1900" max="2100" value="<?= date('Y') ?>">
                    </div>
                </div>
                <div class="btn-row">
                    <button class="btn btn-info" onclick="handleSolarToLunar()">🔄 Chuyển đổi</button>
                </div>
                <div id="solarResult"></div>
            </div>
            
            <!-- Âm -> Dương -->
            <div class="panel">
                <div class="panel-title">🌙 Âm lịch → Dương lịch</div>
                <div class="form-row three">
                    <div class="field">
                        <label>Ngày (Âm)</label>
                        <input type="number" id="lunarDay" min="1" max="30" value="1">
                    </div>
                    <div class="field">
                        <label>Tháng (Âm)</label>
                        <input type="number" id="lunarMonth" min="1" max="12" value="8">
                    </div>
                    <div class="field">
                        <label>Năm (Âm)</label>
                        <input type="number" id="lunarYear" min="1900" max="2100" value="<?= date('Y') ?>">
                    </div>
                </div>
                <div class="form-row" style="margin-top:10px">
                    <div class="field">
                        <label><input type="checkbox" id="isLeapMonth"> Tháng nhuận</label>
                    </div>
                </div>
                <div class="btn-row">
                    <button class="btn btn-info" onclick="handleLunarToSolar()">🔄 Chuyển đổi</button>
                </div>
                <div id="lunarResult"></div>
            </div>
        </div>

        <!-- Favorites Section -->
        <div class="favorites-section" id="favorites-section" style="display: none;">
            <h3>📌 Kết quả đã lưu</h3>
            <ul class="favorites-list" id="favorites-list"></ul>
        </div>
      
        <footer>
            <p>Ứng dụng Lịch Việt - Xem ngày tốt xấu theo quan niệm dân gian</p>
            <p>Lưu ý: Thông tin chỉ mang tính chất tham khảo</p>
        </footer>
    </div>

    <div id="notification" class="notification"></div>

    <script>
        // ==================== DỮ LIỆU VÀ HÀM CƠ BẢN ====================
        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];

        const HOANG_DAO = {
            1: ["Tý", "Sửu", "Thìn", "Tỵ", "Mùi", "Tuất"],
            2: ["Dần", "Mão", "Ngọ", "Mùi", "Dậu", "Tý"],
            3: ["Thìn", "Tỵ", "Thân", "Dậu", "Hợi", "Dần"],
            4: ["Ngọ", "Mùi", "Tuất", "Hợi", "Sửu", "Thìn"],
            5: ["Thân", "Dậu", "Tý", "Sửu", "Mão", "Ngọ"],
            6: ["Tuất", "Hợi", "Dần", "Mão", "Tỵ", "Thân"],
            7: ["Tý", "Sửu", "Thìn", "Tỵ", "Mùi", "Tuất"],
            8: ["Dần", "Mão", "Ngọ", "Mùi", "Dậu", "Tý"],
            9: ["Thìn", "Tỵ", "Thân", "Dậu", "Hợi", "Dần"],
            10: ["Ngọ", "Mùi", "Tuất", "Hợi", "Sửu", "Thìn"],
            11: ["Thân", "Dậu", "Tý", "Sửu", "Mão", "Ngọ"],
            12: ["Tuất", "Hợi", "Dần", "Mão", "Tỵ", "Thân"]
        };

        const HANH_CAN = { 
            Giáp: "Mộc", Ất: "Mộc", Bính: "Hỏa", Đinh: "Hỏa", 
            Mậu: "Thổ", Kỷ: "Thổ", Canh: "Kim", Tân: "Kim", 
            Nhâm: "Thủy", Quý: "Thủy" 
        };

        const MAU_HOP_KY = { 
            "Mộc": ["Xanh lá", "Trắng"], 
            "Hỏa": ["Đỏ", "Đen"], 
            "Thổ": ["Vàng", "Xanh lá"], 
            "Kim": ["Trắng", "Đỏ"], 
            "Thủy": ["Xanh dương/Đen", "Vàng"] 
        };

        const CHI_GIO = ["Tý (23h-01h)", "Sửu (01h-03h)", "Dần (03h-05h)", "Mão (05h-07h)", "Thìn (07h-09h)", "Tỵ (09h-11h)", "Ngọ (11h-13h)", "Mùi (13h-15h)", "Thân (15h-17h)", "Dậu (17h-19h)", "Tuất (19h-21h)", "Hợi (21h-23h)"];

        const GIO_HD_BY_CHI_NGAY = {
            "Tý": ["Tý", "Sửu", "Mão", "Ngọ", "Thân", "Dậu"],
            "Ngọ": ["Tý", "Sửu", "Mão", "Ngọ", "Thân", "Dậu"],
            "Sửu": ["Dần", "Mão", "Tỵ", "Thân", "Tuất", "Hợi"],
            "Mùi": ["Dần", "Mão", "Tỵ", "Thân", "Tuất", "Hợi"],
            "Dần": ["Tý", "Sửu", "Thìn", "Tỵ", "Mùi", "Tuất"],
            "Thân": ["Tý", "Sửu", "Thìn", "Tỵ", "Mùi", "Tuất"],
            "Mão": ["Dần", "Mão", "Ngọ", "Mùi", "Dậu", "Tuất"],
            "Dậu": ["Dần", "Mão", "Ngọ", "Mùi", "Dậu", "Tuất"],
            "Thìn": ["Tý", "Thìn", "Tỵ", "Thân", "Dậu", "Hợi"],
            "Tuất": ["Tý", "Thìn", "Tỵ", "Thân", "Dậu", "Hợi"],
            "Tỵ": ["Sửu", "Thìn", "Ngọ", "Mùi", "Tuất", "Hợi"],
            "Hợi": ["Sửu", "Thìn", "Ngọ", "Mùi", "Tuất", "Hợi"]
        };

        const LUC_DIEU = ["Đại An", "Lưu Niên", "Tốc Hỷ", "Xích Khẩu", "Tiểu Cát", "Không Vong"];
        const TRUC = ["Kiến", "Trừ", "Mãn", "Bình", "Định", "Chấp", "Phá", "Nguy", "Thành", "Thu", "Khai", "Bế"];
        const TU = ["Giác", "Cang", "Đê", "Phòng", "Tâm", "Vĩ", "Cơ", "Đẩu", "Ngưu", "Nữ", "Hư", "Nguy", "Thất", "Bích", "Khuê", "Lâu", "Vị", "Mão", "Tất", "Chủy", "Sâm", "Tỉnh", "Quỷ", "Liễu", "Tinh", "Trương", "Dực", "Chẩn"];

        const ADVICE_BY_TRUC = {
            "Kiến": { ok: ["bắt đầu việc mới", "xuất hành", "khai trương"], avoid: ["an táng lớn", "phá dỡ"] },
            "Trừ": { ok: ["dọn dẹp", "giải trừ", "chữa bệnh"], avoid: ["cưới hỏi", "khởi công lớn"] },
            "Mãn": { ok: ["cầu tài", "ký kết", "nhập kho"], avoid: ["khởi công mới", "kiện tụng"] },
            "Bình": { ok: ["việc thường, nhỏ", "họp hành"], avoid: ["đại sự cần may mắn lớn"] },
            "Định": { ok: ["an vị", "ký kết", "đính ước"], avoid: ["động thổ", "phá dỡ"] },
            "Chấp": { ok: ["sửa chữa", "bảo trì"], avoid: ["khai trương", "xuất hành xa"] },
            "Phá": { ok: ["phá dỡ", "hủy bỏ"], avoid: ["cưới hỏi", "khai trương", "khởi công"] },
            "Nguy": { ok: ["cầu an", "cúng lễ"], avoid: ["mọi việc trọng đại", "xuất hành xa"] },
            "Thành": { ok: ["cưới hỏi", "nhập trạch", "khai trương"], avoid: ["kiện tụng"] },
            "Thu": { ok: ["thu hoạch", "thu nợ", "nhập kho"], avoid: ["xuất hành", "khai trương"] },
            "Khai": { ok: ["khai trương", "mở kho", "xuất hành"], avoid: ["an táng"] },
            "Bế": { ok: ["kết thúc", "đóng sổ", "thu hồi"], avoid: ["khai trương", "cưới hỏi", "xuất hành"] }
        };

        // ==================== HỆ THỐNG ĐĂNG NHẬP/ĐĂNG XUẤT ====================
        let currentUser = <?= $currentUser ? json_encode($currentUser) : 'null' ?>;

        // Hàm kiểm tra trạng thái đăng nhập
        async function checkAuthStatus() {
            try {
                const response = await fetch('api/get_current_user.php');
                const data = await response.json();
                
                if (data.logged_in && data.user) {
                    currentUser = data.user;
                    updateUIForLoggedInUser();
                } else {
                    currentUser = null;
                    updateUIForGuest();
                }
            } catch (error) {
                console.error('Lỗi kiểm tra đăng nhập:', error);
                currentUser = null;
                updateUIForGuest();
            }
        }

        // Cập nhật giao diện khi đã đăng nhập
        function updateUIForLoggedInUser() {
            const userInfo = document.getElementById('user-info');
            const authButtons = document.getElementById('auth-buttons');
            const favoritesSection = document.getElementById('favorites-section');
            
            if (userInfo) userInfo.style.display = 'flex';
            if (authButtons) authButtons.style.display = 'none';
            if (favoritesSection) favoritesSection.style.display = 'block';
            
            // Cập nhật thông tin user
            if (currentUser) {
                const nameElement = document.getElementById('user-display-name');
                const emailElement = document.getElementById('user-display-email');
                const avatarElement = document.getElementById('user-avatar');
                
                if (nameElement) nameElement.textContent = currentUser.name;
                if (emailElement) emailElement.textContent = currentUser.email;
                
                // Cập nhật avatar
                if (avatarElement && currentUser.name) {
                    const initials = currentUser.name
                        .split(' ')
                        .map(n => n[0])
                        .join('')
                        .substring(0, 2)
                        .toUpperCase();
                    avatarElement.textContent = initials;
                }
            }
            
            loadFavoritesFromAPI();
        }

        // Cập nhật giao diện khi là khách
        function updateUIForGuest() {
            const userInfo = document.getElementById('user-info');
            const authButtons = document.getElementById('auth-buttons');
            const favoritesSection = document.getElementById('favorites-section');
            
            if (userInfo) userInfo.style.display = 'none';
            if (authButtons) authButtons.style.display = 'flex';
            if (favoritesSection) favoritesSection.style.display = 'none';
        }

        // Hàm đăng xuất
        async function logout() {
            if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) return;
            
            try {
                const response = await fetch('api/logout.php', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã đăng xuất thành công!', 'success');
                    // Chuyển hướng sau 1 giây
                    setTimeout(() => {
                        window.location.href = data.redirect || 'chuyenngay.php';
                    }, 1000);
                }
            } catch (error) {
                console.error('Lỗi đăng xuất:', error);
                showNotification('Lỗi đăng xuất!', 'error');
            }
        }

        // Hiển thị modal đăng nhập
        function showLoginModal() {
            createAuthModals();
            const modal = document.getElementById('login-modal');
            if (modal) modal.style.display = 'flex';
        }

        // Hiển thị modal đăng ký
        function showRegisterModal() {
            createAuthModals();
            const modal = document.getElementById('register-modal');
            if (modal) modal.style.display = 'flex';
        }

        // Tạo modal đăng nhập/đăng ký động
        function createAuthModals() {
            if (document.getElementById('login-modal')) return;
            
            const modalHTML = `
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
            </div>`;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            initializeModalEvents();
        }

        // Khởi tạo sự kiện cho modal
        function initializeModalEvents() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });
        }

        // Đóng modal
        function closeLoginModal() {
            const modal = document.getElementById('login-modal');
            const form = document.getElementById('login-form');
            if (modal) modal.style.display = 'none';
            if (form) form.reset();
        }

        function closeRegisterModal() {
            const modal = document.getElementById('register-modal');
            const form = document.getElementById('register-form');
            if (modal) modal.style.display = 'none';
            if (form) form.reset();
        }

        // Xử lý đăng nhập
        async function performLogin() {
            const email = document.getElementById('login-email')?.value.trim();
            const password = document.getElementById('login-password')?.value;

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
                    closeLoginModal();
                    updateUIForLoggedInUser();
                    showNotification('Đăng nhập thành công!', 'success');
                    loadFavoritesFromAPI();
                } else {
                    showNotification(data.message || 'Email hoặc mật khẩu không đúng!', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        // Xử lý đăng ký
        async function performRegister() {
            const name = document.getElementById('register-name')?.value.trim();
            const email = document.getElementById('register-email')?.value.trim();
            const password = document.getElementById('register-password')?.value;
            const confirmPassword = document.getElementById('register-confirm-password')?.value;

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
                    closeRegisterModal();
                    updateUIForLoggedInUser();
                    showNotification('Đăng ký thành công!', 'success');
                } else {
                    showNotification(data.message || 'Đăng ký thất bại!', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        // ==================== XỬ LÝ YÊU THÍCH VỚI API ====================
        // Tải danh sách yêu thích từ API
        async function loadFavoritesFromAPI() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                if (data.success) {
                    displayFavorites(data.favorites);
                } else {
                    console.log('Không có dữ liệu yêu thích:', data.message);
                    displayFavorites([]);
                }
            } catch (error) {
                console.error('Lỗi tải danh sách yêu thích:', error);
                displayFavorites([]);
            }
        }

        // Hiển thị danh sách yêu thích
        function displayFavorites(favorites) {
            const favoritesList = document.getElementById('favorites-list');
            const favoritesSection = document.getElementById('favorites-section');
            
            if (favorites && favorites.length > 0) {
                favoritesSection.style.display = 'block';
                favoritesList.innerHTML = favorites.map(fav => {
                    let solarDate = fav.solar_date || 'N/A';
                    const lunarDate = fav.lunar_date || 'N/A';
                    const ratingText = fav.rating_text || 'Không có đánh giá';
                    const score = fav.score || 0;
                    
                    // Format lại ngày dương từ YYYY-MM-DD sang DD-MM-YYYY để hiển thị
                    if (solarDate !== 'N/A' && solarDate.includes('-')) {
                        const parts = solarDate.split('-');
                        if (parts.length === 3 && parts[0].length === 4) {
                            solarDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // YYYY-MM-DD -> DD-MM-YYYY
                        }
                    }
                    
                    // Xác định class rating dựa trên điểm số
                    let ratingClass = 'neutral-rating';
                    if (score >= 7) ratingClass = 'good-rating';
                    else if (score <= 3) ratingClass = 'bad-rating';
                    
                    // Xác định text rating
                    let ratingDisplay = 'BÌNH THƯỜNG';
                    if (score >= 7) ratingDisplay = 'TỐT';
                    else if (score <= 3) ratingDisplay = 'XẤU';
                    
                    return `
                        <li class="favorite-item">
                            <div class="favorite-date">
                                <strong>${solarDate}</strong> 
                                (${lunarDate})<br>
                                <small><span class="${ratingClass}">${ratingDisplay}</span> - Điểm: ${score}</small>
                            </div>
                            <div class="favorite-actions">
                                <button class="btn-info" onclick="loadFavorite('${solarDate}')">👁️ Xem</button>
                                <button class="btn-danger" onclick="removeFavoriteFromAPI(${fav.id})">🗑️ Xóa</button>
                            </div>
                        </li>
                    `;
                }).join('');
            } else {
                favoritesSection.style.display = 'none';
                favoritesList.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Chưa có kết quả nào được lưu</p>';
            }
        }

        // Tải kết quả từ yêu thích
        function loadFavorite(solarDate) {
            if (!solarDate) return;
            
            // Xử lý nhiều định dạng ngày (DD-MM-YYYY hoặc YYYY-MM-DD)
            let solarDay, solarMonth, solarYear;
            
            if (solarDate.includes('-')) {
                const parts = solarDate.split('-');
                if (parts.length === 3) {
                    // Định dạng: DD-MM-YYYY
                    if (parts[0].length === 2) {
                        solarDay = parseInt(parts[0]);
                        solarMonth = parseInt(parts[1]);
                        solarYear = parseInt(parts[2]);
                    } 
                    // Định dạng: YYYY-MM-DD  
                    else if (parts[0].length === 4) {
                        solarYear = parseInt(parts[0]);
                        solarMonth = parseInt(parts[1]);
                        solarDay = parseInt(parts[2]);
                    }
                }
            }
            
            if (solarDay && solarMonth && solarYear) {
                // Cập nhật form Dương lịch
                document.getElementById('solarDay').value = solarDay;
                document.getElementById('solarMonth').value = solarMonth;
                document.getElementById('solarYear').value = solarYear;
                
                // Thực hiện chuyển đổi
                handleSolarToLunar();
                
                showNotification('Đã tải kết quả từ danh sách yêu thích!', 'success');
            } else {
                showNotification('Định dạng ngày không hợp lệ: ' + solarDate, 'error');
            }
        }

        // Xóa khỏi danh sách yêu thích qua API
        async function removeFavoriteFromAPI(favoriteId) {
            if (!confirm('Bạn có chắc chắn muốn xóa khỏi danh sách yêu thích?')) return;
            
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
                    // Tải lại danh sách
                    loadFavoritesFromAPI();
                    triggerDataUpdate();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa yêu thích:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        // Thêm vào danh sách yêu thích qua API
        async function addToFavorites(solar, lunar, rating, score) {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để sử dụng tính năng này!', 'error');
                showLoginModal();
                return;
            }

            if (!confirm('Thêm ngày này vào danh sách yêu thích?')) return;

            const data = {
                solar: solar,
                lunar: lunar,
                rating: rating,
                score: parseFloat(score)
            };

            try {
                const res = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const json = await res.json();

                if (json.success) {
                    showNotification('❤️ ' + (json.message || 'Đã thêm vào yêu thích!'), 'success');
                    // Tải lại danh sách
                    loadFavoritesFromAPI();
                    // Kích hoạt cập nhật dữ liệu để user.php cập nhật
                    triggerDataUpdate();
                } else {
                    showNotification('Lỗi: ' + (json.message || 'Không thể thêm'), 'error');
                }
            } catch (e) {
                console.error('Lỗi thêm yêu thích:', e);
                showNotification('Lỗi kết nối!', 'error');
            }
        }

        // ==================== HÀM CHUYỂN ĐỔI NGÀY ====================
        // Hàm thiên văn
        function jdFromDate(dd, mm, yy) {
            const a = Math.floor((14 - mm) / 12);
            const y = yy + 4800 - a;
            const m = mm + 12 * a - 3;
            let jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
            if (jd < 2299161) jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - 32083;
            return jd;
        }

        function jdToDate(jd) {
            let a, b, c;
            if (jd > 2299160) {
                a = jd + 32044;
                b = Math.floor((4 * a + 3) / 146097);
                c = a - Math.floor(146097 * b / 4);
            } else {
                b = 0;
                c = jd + 32082;
            }
            const d = Math.floor((4 * c + 3) / 1461);
            const e = c - Math.floor((1461 * d) / 4);
            const m = Math.floor((5 * e + 2) / 153);
            const day = e - Math.floor((153 * m + 2) / 5) + 1;
            const month = m + 3 - 12 * Math.floor(m / 10);
            const year = 100 * b + d - 4800 + Math.floor(m / 10);
            return [day, month, year];
        }

        function newMoon(k, timeZone = 7) {
            const T = k / 1236.85, T2 = T * T, T3 = T2 * T, dr = Math.PI / 180;
            let Jd1 = 2415020.75933 + 29.53058868 * k + 0.0001178 * T2 - 0.000000155 * T3;
            Jd1 += 0.00033 * Math.sin((166.56 + 132.87 * T - 0.009173 * T2) * dr);
            const M = 359.2242 + 29.10535608 * k - 0.0000333 * T2 - 0.00000347 * T3;
            const Mpr = 306.0253 + 385.81691806 * k + 0.0107306 * T2 + 0.00001236 * T3;
            const F = 21.2964 + 390.67050646 * k - 0.0016528 * T2 - 0.00000239 * T3;
            let C1 = (0.1734 - 0.000393 * T) * Math.sin(M * dr);
            C1 += 0.0021 * Math.sin(2 * M * dr) - 0.4068 * Math.sin(Mpr * dr) + 0.0161 * Math.sin(2 * Mpr * dr);
            C1 -= 0.0004 * Math.sin(3 * Mpr * dr) + 0.0104 * Math.sin(2 * F * dr) - 0.0051 * Math.sin((M + Mpr) * dr);
            C1 -= 0.0074 * Math.sin((M - Mpr) * dr) + 0.0004 * Math.sin((2 * F + M) * dr) - 0.0004 * Math.sin((2 * F - M) * dr);
            C1 -= 0.0006 * Math.sin((2 * F + Mpr) * dr) + 0.0010 * Math.sin((2 * F - Mpr) * dr) + 0.0005 * Math.sin((2 * M + Mpr) * dr);
            let deltat;
            if (T < -11) { deltat = 0.001 + 0.000839 * T + 0.0002261 * T * T - 0.00000845 * T * T * T - 0.000000081 * T * T * T * T; } else { deltat = -0.000278 + 0.000265 * T + 0.000262 * T * T; }
            const JdNew = Jd1 + C1 - deltat;
            return Math.floor(JdNew + 0.5 + timeZone / 24);
        }

        function sunLongitudeSector(jdn, timeZone = 7) {
            const T = (jdn - 2451545.5 - timeZone / 24) / 36525, T2 = T * T, dr = Math.PI / 180;
            const M = 357.52910 + 35999.05030 * T - 0.0001559 * T2 - 0.00000048 * T * T * T;
            const L0 = 280.46645 + 36000.76983 * T + 0.0003032 * T2;
            let DL = (1.914600 - 0.004817 * T - 0.000014 * T2) * Math.sin(dr * M);
            DL += (0.019993 - 0.000101 * T) * Math.sin(2 * dr * M);
            DL += 0.000290 * Math.sin(3 * dr * M);
            let L = (L0 + DL) * dr;
            L %= 2 * Math.PI;
            return Math.floor(L / Math.PI * 6);
        }

        function getLunarMonth11(yy, timeZone = 7) {
            const off = jdFromDate(31, 12, yy) - 2415021;
            const k = Math.floor(off / 29.530588853);
            let nm = newMoon(k, timeZone);
            if (sunLongitudeSector(nm, timeZone) >= 9) nm = newMoon(k - 1, timeZone);
            return nm;
        }

        function getLeapMonthOffset(a11, timeZone = 7) {
            const k = Math.floor(0.5 + (a11 - 2415021.076998695) / 29.530588853);
            let i = 1; let last = sunLongitudeSector(newMoon(k + i, timeZone), timeZone);
            while (true) { i++; const arc = sunLongitudeSector(newMoon(k + i, timeZone), timeZone); if (arc === last || i > 14) break; last = arc; }
            return i - 1;
        }

        function convertSolar2Lunar(dd, mm, yy, timeZone = 7) {
            const dayNumber = jdFromDate(dd, mm, yy);
            const k = Math.floor((dayNumber - 2415021.076998695) / 29.530588853);
            let monthStart = newMoon(k + 1, timeZone);
            if (monthStart > dayNumber) monthStart = newMoon(k, timeZone);
            let a11 = getLunarMonth11(yy, timeZone);
            let b11 = getLunarMonth11(yy + 1, timeZone);
            let lunarYear;
            if (a11 >= monthStart) { lunarYear = yy; a11 = getLunarMonth11(yy - 1, timeZone); } else { lunarYear = yy + 1; b11 = getLunarMonth11(yy + 1, timeZone); }
            const lunarDay = dayNumber - monthStart + 1;
            const diff = Math.floor((monthStart - a11) / 29);
            let lunarMonth = diff + 11; let lunarLeap = 0;
            if ((b11 - a11) > 365) { const leapMonthDiff = getLeapMonthOffset(a11, timeZone); if (diff >= leapMonthDiff) { lunarMonth = diff + 10; if (diff === leapMonthDiff) lunarLeap = 1; } }
            if (lunarMonth > 12) lunarMonth -= 12;
            if (lunarMonth >= 11 && diff < 4) lunarYear -= 1;
            return [lunarDay, lunarMonth, lunarYear, lunarLeap];
        }

        function convertLunar2Solar(lunarDay, lunarMonth, lunarYear, lunarLeap = 0, timeZone = 7) {
            let a11, b11;
            if (lunarMonth < 11) { a11 = getLunarMonth11(lunarYear - 1, timeZone); b11 = getLunarMonth11(lunarYear, timeZone); } else { a11 = getLunarMonth11(lunarYear, timeZone); b11 = getLunarMonth11(lunarYear + 1, timeZone); }
            const k = Math.floor(0.5 + (a11 - 2415021.076998695) / 29.530588853);
            let off = lunarMonth - 11;
            if (off < 0) off += 12;
            if ((b11 - a11) > 365) { const leapOff = getLeapMonthOffset(a11, timeZone); let leapMonth = leapOff - 2; if (leapMonth < 0) leapMonth += 12; if (lunarLeap !== 0 && lunarMonth !== leapMonth) return [0, 0, 0]; if (lunarLeap !== 0 || off >= leapOff) off += 1; }
            const monthStart = newMoon(k + off, timeZone);
            return jdToDate(monthStart + lunarDay - 1);
        }

        function canChiOfDay(jdn) { return [CAN[(jdn + 9) % 10], CHI[(jdn + 1) % 12]]; }
        function canChiOfYear(lY) { return [CAN[(lY + 6) % 10], CHI[(lY + 8) % 12]]; }
        function canChiOfMonth(lM, lY) { const yIdx = (lY + 6) % 10; return [CAN[(yIdx * 2 + lM + 1) % 10], CHI[(lM + 1) % 12]]; }
        function gioHoangDao(chiNgay) { const good = new Set(GIO_HD_BY_CHI_NGAY[chiNgay] || []); return CHI_GIO.filter((label, idx) => good.has(CHI[idx])); }
        function lucDieu(jdn) { return LUC_DIEU[jdn % 6]; }
        function trucNgay(chiNgay, chiThang) { const iD = CHI.indexOf(chiNgay), iM = CHI.indexOf(chiThang); return TRUC[(iD - iM + 12) % 12]; }
        function nhiThapBatTu(jdn) { return TU[jdn % 28]; }
        function isNgaySoc(ld) { return ld === 1; }
        function isNgayNguyetKy(ld) { return [5, 14, 23].includes(ld); }
        function isNgayTamNuong(ld) { return [3, 7, 13, 18, 22, 27].includes(ld); }
        function isNgayNguyetTan(ld, lm, ly) { 
            const [sd, sm, sy] = convertLunar2Solar(ld, lm, ly, 0); 
            if (!sd) return false; 
            const j = jdFromDate(sd, sm, sy); 
            const [d2, m2, y2] = jdToDate(j + 1); 
            const [ld2, lm2] = convertSolar2Lunar(d2, m2, y2); 
            return lm2 !== lm; 
        }

        function adviceFromTrucAndLucDieu(truc, ld) { 
            const b = ADVICE_BY_TRUC[truc] || { ok: [], avoid: [] }; 
            const ok = new Set(b.ok), avoid = new Set(b.avoid); 
            if (ld === "Tốc Hỷ") ok.add("cưới hỏi/tiệc mừng"); 
            if (ld === "Đại An") ok.add("cầu an/đi xa"); 
            if (ld === "Tiểu Cát") ok.add("việc nhỏ, cầu tài"); 
            if (ld === "Xích Khẩu") avoid.add("tranh chấp, ký kết dễ cãi vã"); 
            if (ld === "Không Vong") avoid.add("đầu tư lớn, khai trương"); 
            return { should: [...ok], avoid: [...avoid] }; 
        }

        function evaluateDay(dd, mm, yy) {
            const j = jdFromDate(dd, mm, yy);
            const [ld, lm, ly, leap] = convertSolar2Lunar(dd, mm, yy);
            const [canD, chiD] = canChiOfDay(j);
            const [canM, chiM] = canChiOfMonth(lm, ly);
            const [canY, chiY] = canChiOfYear(ly);
            const rows = []; 
            let score = 0;

            // 1. Hoàng đạo - Hắc đạo
            const isHD = (HOANG_DAO[lm] || []).includes(chiD);
            score += isHD ? 2 : -1; 
            rows.push(["Hoàng đạo", `${chiD} ${isHD ? "(cát lợi)" : "(hắc đạo)"}`, isHD ? 2 : -1]);

            // 2. Nhị thập bát tú
            const tu = nhiThapBatTu(j); 
            rows.push(["Nhị thập bát tú", `${tu} (trung)`, 0]);

            // 3. Lục Diệu
            const ldieu = lucDieu(j); 
            const ptsLD = (["Đại An", "Tốc Hỷ", "Tiểu Cát"].includes(ldieu)) ? +0.5 : (["Xích Khẩu", "Không Vong"].includes(ldieu) ? -0.5 : 0);
            score += ptsLD; 
            rows.push(["Lục Diệu", `${ldieu} ${ptsLD > 0 ? "(cát)" : ptsLD < 0 ? "(hung)" : "(bình thường)"}`, ptsLD]);

            // 4. Trực ngày
            const truc = trucNgay(chiD, chiM); 
            const goodT = ["Khai", "Thành", "Mãn"], badT = ["Phá", "Nguy", "Bế"]; 
            let ptsT = 0; 
            if (goodT.includes(truc)) ptsT = +1; 
            else if (badT.includes(truc)) ptsT = -1;
            score += ptsT; 
            rows.push(["Trực ngày", `${truc}`, ptsT]);

            // 5. Ngày kỵ
            let ky = []; 
            if (isNgaySoc(ld)) ky.push("Sóc (mùng 1)"); 
            if (isNgayNguyetTan(ld, lm, ly)) ky.push("Nguyệt tận"); 
            if (isNgayNguyetKy(ld)) ky.push("Nguyệt kỵ (5,14,23)"); 
            if (isNgayTamNuong(ld)) ky.push("Tam Nương (3,7,13,18,22,27)");
            if (ky.length) score -= 1; 
            rows.push(["Ngày kỵ", ky.length ? `Phạm: ${ky.join(', ')}` : "Không phạm", ky.length ? -1 : 0]);

            // 6. Giờ Hoàng đạo
            const gioHD = gioHoangDao(chiD);

            // Xếp loại ngày
            let grade = 'neutral', barClass = 'neutral', barText = 'Ngày bình thường';
            if (score >= 1.5) { grade = 'good'; barClass = 'good'; barText = 'Ngày tốt (cát lợi)'; }
            else if (score <= -0.5) { grade = 'bad'; barClass = 'bad'; barText = 'Ngày xấu (bất lợi)'; }

            // Màu phong thủy
            const hanh = HANH_CAN[canD], pair = MAU_HOP_KY[hanh] || ['-', '-'];

            // Lời khuyên
            const adv = adviceFromTrucAndLucDieu(truc, ldieu);

            return { 
                jdn: j, ld, lm, ly, leap, canD, chiD, canM, chiM, canY, chiY, gioHD,
                grade, barClass, barText, score, rows, colors: { hanh, hop: pair[0], ky: pair[1] }, 
                advice: adv, ldieu, truc, tu 
            };
        }

        function formatDL(d, m, y) { return `${String(d).padStart(2, '0')}-${String(m).padStart(2, '0')}-${y}`; }
        function formatAL(d, m, y, leap) { return `${String(d).padStart(2, '0')}-${String(m).padStart(2, '0')}-${y}${leap ? ' (nhuận)' : ''}`; }

        function buildAdviceList(items) { 
            if (!items || !items.length) return "—"; 
            return `<ul>${items.map(t => `<li>${t}</li>`).join('')}</ul>`; 
        }

        // Hàm tự động lưu lịch sử khi đã đăng nhập
        async function saveConversionHistory(inputType, inputDate, convertedType, convertedValue, note = '') {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/save_chuyenngay.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        input_type: inputType,
                        input_date: inputDate,
                        converted_type: convertedType,
                        converted_value: convertedValue,
                        note: note
                    })
                });
                
                const data = await response.json();
                if (!data.success) {
                    console.log('Lưu lịch sử thất bại:', data.message);
                }
            } catch (e) {
                console.log('Lỗi kết nối khi lưu lịch sử:', e);
            }
        }

        // Hàm xử lý chuyển đổi Dương -> Âm
        function handleSolarToLunar() {
            const day = parseInt(document.getElementById('solarDay').value);
            const month = parseInt(document.getElementById('solarMonth').value);
            const year = parseInt(document.getElementById('solarYear').value);
        
            if (!day || !month || !year || day < 1 || day > 31 || month < 1 || month > 12 || year < 1900 || year > 2100) {
                showNotification('Vui lòng nhập ngày/tháng/năm hợp lệ!', 'error');
                return;
            }
        
            const [ld, lm, ly, leap] = convertSolar2Lunar(day, month, year);
            const data = evaluateDay(day, month, year);
            
            // Định dạng ngày cho API
            const inputDateSolar = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const lunarText = formatAL(ld, lm, ly, leap);
            
            // LUÔN LUÔN lưu lịch sử khi chuyển đổi
            saveConversionHistory('duong', inputDateSolar, 'am', lunarText, data.barText);
            
            const shouldHtml = buildAdviceList(data.advice.should);
            const avoidHtml = buildAdviceList(data.advice.avoid);
            const rowsHtml = data.rows.map(([k, t, p]) => {
                const icon = p > 0 ? '✅' : (p < 0 ? '❌' : '⚠️');
                const pts = p > 0 ? `+${p}` : `${p}`;
                return `<tr><td>${k}</td><td>${t}</td><td class="points">${icon} ${pts}</td></tr>`;
            }).join('');
        
            const resultHtml = `
                <div class="result">
                    <button class="close-btn" onclick="closeResult('solarResult')">Đóng ✕</button>
                    <strong>Ngày dương lịch:</strong> ${formatDL(day, month, year)}<br>
                    <strong>Ngày âm lịch:</strong> ${formatAL(ld, lm, ly, leap)}<br>
                    <strong>Can Chi ngày:</strong> ${data.canD} ${data.chiD}<br>
                    <strong>Can Chi tháng:</strong> ${data.canM} ${data.chiM}<br>
                    <strong>Can Chi năm:</strong> ${data.canY} ${data.chiY}<br>
                    <strong>Giờ Hoàng đạo:</strong> ${data.gioHD.join(', ') || '—'}<br>
                    <div class="bar ${data.barClass}">${data.barText} — Điểm: ${data.score.toFixed(1)}</div>
                    <div class="info-grid">
                        <div class="info">
                            <strong>🌸 Màu phong thủy</strong><br>
                            Hành: <b>${data.colors.hanh || '-'}</b><br>
                            Hợp: <b class="color-hop">${data.colors.hop}</b><br>
                            Kỵ: <b class="color-ky">${data.colors.ky}</b>
                        </div>
                        <div class="info">
                            <strong>📊 Thông tin khác</strong><br>
                            Lục Diệu: ${data.ldieu}<br>
                            Trực: ${data.truc}<br>
                            Nhị thập bát tú: ${data.tu}
                        </div>
                    </div>
                    <div class="direction-container">
                        <div class="direction-card direction-good">
                            <div class="direction-header">
                                <span class="direction-icon">✅</span>
                                Nên làm
                            </div>
                            ${shouldHtml}
                        </div>
                        <div class="direction-card direction-bad">
                            <div class="direction-header">
                                <span class="direction-icon">❌</span>
                                Không nên
                            </div>
                            ${avoidHtml}
                        </div>
                    </div>
                    <table><thead><tr><th>Tiêu chí</th><th>Kết quả</th><th class="points">Điểm</th></tr></thead><tbody>${rowsHtml}</tbody></table>
                    <button class="btn-favorite" onclick="addToFavorites('${formatDL(day, month, year)}', '${formatAL(ld, lm, ly, leap)}', '${data.barText}', ${data.score.toFixed(1)})" style="margin-top: 15px; width: 100%;">
                        ❤️ Thêm vào yêu thích
                    </button>
                </div>`;
        
            document.getElementById('solarResult').innerHTML = resultHtml;
            
            // Cập nhật form Âm lịch với kết quả
            document.getElementById('lunarDay').value = ld;
            document.getElementById('lunarMonth').value = lm;
            document.getElementById('lunarYear').value = ly;
            document.getElementById('isLeapMonth').checked = leap === 1;
        }
      
        // Hàm xử lý chuyển đổi Âm -> Dương
        function handleLunarToSolar() {
            const day = parseInt(document.getElementById('lunarDay').value);
            const month = parseInt(document.getElementById('lunarMonth').value);
            const year = parseInt(document.getElementById('lunarYear').value);
            const isLeap = document.getElementById('isLeapMonth').checked;
        
            if (!day || !month || !year || day < 1 || day > 30 || month < 1 || month > 12 || year < 1900 || year > 2100) {
                showNotification('Vui lòng nhập ngày/tháng/năm hợp lệ!', 'error');
                return;
            }
        
            const [sd, sm, sy] = convertLunar2Solar(day, month, year, isLeap ? 1 : 0);
            if (sd === 0) {
                showNotification('Ngày âm lịch không hợp lệ!', 'error');
                return;
            }
        
            const data = evaluateDay(sd, sm, sy);
            
            // Định dạng ngày cho API
            const inputDateLunar = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const solarText = `${sy}-${String(sm).padStart(2,'0')}-${String(sd).padStart(2,'0')}`;
            
            // LUÔN LUÔN lưu lịch sử khi chuyển đổi
            saveConversionHistory('am', inputDateLunar, 'duong', solarText, data.barText);
            
            const shouldHtml = buildAdviceList(data.advice.should);
            const avoidHtml = buildAdviceList(data.advice.avoid);
            const rowsHtml = data.rows.map(([k, t, p]) => {
                const icon = p > 0 ? '✅' : (p < 0 ? '❌' : '⚠️');
                const pts = p > 0 ? `+${p}` : `${p}`;
                return `<tr><td>${k}</td><td>${t}</td><td class="points">${icon} ${pts}</td></tr>`;
            }).join('');
        
            const resultHtml = `
                <div class="result">
                    <button class="close-btn" onclick="closeResult('lunarResult')">Đóng ✕</button>
                    <strong>Ngày âm lịch:</strong> ${formatAL(day, month, year, isLeap)}<br>
                    <strong>Ngày dương lịch:</strong> ${formatDL(sd, sm, sy)}<br>
                    <strong>Can Chi ngày:</strong> ${data.canD} ${data.chiD}<br>
                    <strong>Can Chi tháng:</strong> ${data.canM} ${data.chiM}<br>
                    <strong>Can Chi năm:</strong> ${data.canY} ${data.chiY}<br>
                    <strong>Giờ Hoàng đạo:</strong> ${data.gioHD.join(', ') || '—'}<br>
                    <div class="bar ${data.barClass}">${data.barText} — Điểm: ${data.score.toFixed(1)}</div>
                    <div class="info-grid">
                        <div class="info">
                            <strong>🌸 Màu phong thủy</strong><br>
                            Hành: <b>${data.colors.hanh || '-'}</b><br>
                            Hợp: <b class="color-hop">${data.colors.hop}</b><br>
                            Kỵ: <b class="color-ky">${data.colors.ky}</b>
                        </div>
                        <div class="info">
                            <strong>📊 Thông tin khác</strong><br>
                            Lục Diệu: ${data.ldieu}<br>
                            Trực: ${data.truc}<br>
                            Nhị thập bát tú: ${data.tu}
                        </div>
                    </div>
                    <div class="direction-container">
                        <div class="direction-card direction-good">
                            <div class="direction-header">
                                <span class="direction-icon">✅</span>
                                Nên làm
                            </div>
                            ${shouldHtml}
                        </div>
                        <div class="direction-card direction-bad">
                            <div class="direction-header">
                                <span class="direction-icon">❌</span>
                                Không nên
                            </div>
                            ${avoidHtml}
                        </div>
                    </div>
                    <table><thead><tr><th>Tiêu chí</th><th>Kết quả</th><th class="points">Điểm</th></tr></thead><tbody>${rowsHtml}</tbody></table>
                    <button class="btn-favorite" onclick="addToFavorites('${formatDL(sd, sm, sy)}', '${formatAL(day, month, year, isLeap)}', '${data.barText}', ${data.score.toFixed(1)})" style="margin-top: 15px; width: 100%;">
                        ❤️ Thêm vào yêu thích
                    </button>
                </div>`;
        
            document.getElementById('lunarResult').innerHTML = resultHtml;
            
            // Cập nhật form Dương lịch với kết quả
            document.getElementById('solarDay').value = sd;
            document.getElementById('solarMonth').value = sm;
            document.getElementById('solarYear').value = sy;
        }
      
        function closeResult(id) {
            document.getElementById(id).innerHTML = '';
        }

        // ==================== HÀM TIỆN ÍCH ====================
        function showNotification(message, type = 'info') {
            const notif = document.getElementById('notification');
            if (!notif) return;
            
            notif.textContent = message;
            notif.className = `notification ${type} show`;
            setTimeout(() => { notif.classList.remove('show'); }, 3000);
        }

        function setupNavigation() {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // ==================== HỆ THỐNG CẬP NHẬT DỮ LIỆU ====================

        // Kích hoạt cập nhật dữ liệu để các trang khác có thể lắng nghe
        function triggerDataUpdate() {
            // Lưu thời gian cập nhật để các trang khác có thể lắng nghe
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('lastDataUpdate', Date.now().toString());
            }
        }

        // Lắng nghe sự kiện cập nhật dữ liệu từ các trang khác
        function listenForDataUpdates() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'lastDataUpdate') {
                    // Cập nhật giao diện khi có thay đổi dữ liệu
                    if (currentUser) {
                        loadFavoritesFromAPI();
                    }
                }
            });
        }

        // Hàm load date từ URL parameter
        function loadDateFromParameter(dateStr) {
            if (!dateStr) return;
            
            // Xử lý định dạng DD-MM-YYYY
            const parts = dateStr.split('-');
            if (parts.length === 3 && parts[0].length === 2) {
                const day = parseInt(parts[0]);
                const month = parseInt(parts[1]);
                const year = parseInt(parts[2]);
                
                if (day && month && year) {
                    document.getElementById('solarDay').value = day;
                    document.getElementById('solarMonth').value = month;
                    document.getElementById('solarYear').value = year;
                    
                    // Tự động thực hiện chuyển đổi
                    setTimeout(() => {
                        handleSolarToLunar();
                    }, 500);
                }
            }
        }

        // Khởi tạo ứng dụng
        function initializeApp() {
            setupNavigation();
            checkAuthStatus();
            
            // Gán sự kiện cho các button
            document.getElementById('login-btn')?.addEventListener('click', showLoginModal);
            document.getElementById('register-btn')?.addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn')?.addEventListener('click', logout);
            document.getElementById('profile-btn')?.addEventListener('click', () => {
                window.location.href = 'user.php';
            });

            listenForDataUpdates();
            
            // Tự động load date từ URL parameter (nếu có)
            const urlParams = new URLSearchParams(window.location.search);
            const loadDate = urlParams.get('loadDate');
            if (loadDate) {
                loadDateFromParameter(loadDate);
            }
        }

        // Khởi tạo ứng dụng khi DOM đã tải xong
        document.addEventListener('DOMContentLoaded', initializeApp);
    </script>
</body>
</html>
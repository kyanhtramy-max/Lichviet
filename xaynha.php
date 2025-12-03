<?php
session_start();
require_once "config.php";

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
    <title>Xem Tuổi Xây Nhà - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .calculator-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }

        .field input {
            width: 100%;
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
            margin-top: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }

        .info {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            border-radius: 8px;
            padding: 12px;
        }

        .info strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .bar {
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            margin: 15px 0;
            font-size: 1.1rem;
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

        .favorite-btn {
            background: transparent;
            color: #ff6b6b;
            border: 2px solid #ff6b6b;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .favorite-btn:hover {
            background: #ff6b6b;
            color: white;
            transform: translateY(-2px);
        }

        .favorite-btn.active {
            background: #ff6b6b;
            color: white;
        }

        .favorites-section {
            margin-top: 30px;
        }

        .favorites-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .favorite-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,.08);
            border-left: 4px solid #ff6b6b;
            position: relative;
        }

        .favorite-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .favorite-title {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1rem;
        }

        .favorite-date {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .favorite-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .favorite-actions button {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .empty-favorites {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .analysis-details {
            margin: 15px 0;
        }

        .analysis-item {
            margin: 8px 0;
            padding: 8px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .analysis-good {
            background: #e8f5e8;
            color: #0f5132;
            border-left: 3px solid #28a745;
        }

        .analysis-bad {
            background: #fde8e6;
            color: #842029;
            border-left: 3px solid #dc3545;
        }

        .analysis-warning {
            background: #fef5e6;
            color: #664d03;
            border-left: 3px solid #ffc107;
        }

        .recommendation-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .recommendation-box h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .favorites-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Xem tuổi làm nhà hợp phong thủy</p>
          
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
                    <a class="nav-link active" href="xaynha.php">
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
      
        <div class="app-container">
            <section class="info-section">
                <h2>🏠 Xem Tuổi Xây Nhà</h2>
                
                <div class="panel">
                    <div class="panel-title">🔨 Xem tuổi làm nhà</div>
                    <div class="calculator-form">
                        <div class="form-row">
                            <div class="field">
                                <label>Năm sinh gia chủ</label>
                                <input type="number" id="ownerYear" min="1900" max="2100" placeholder="VD: 1975" value="1975">
                            </div>
                            <div class="field">
                                <label>Năm dự kiến xây nhà</label>
                                <input type="number" id="buildYear" min="2020" max="2100" placeholder="VD: 2024" value="2024">
                            </div>
                        </div>
                        <div class="btn-row">
                            <button class="btn-info" onclick="checkBuildCompatibility()">🔍 Xem tuổi làm nhà</button>
                        </div>
                        <div id="buildResult"></div>
                    </div>
                </div>

                <div class="favorites-section" id="favorites-section" style="display: none;">
                    <h3>❤️ Kết quả đã lưu</h3>
                    <div class="favorites-list" id="favorites-list"></div>
                </div>

                <div class="service-detail">
                    <h3>📚 Thông tin về dịch vụ</h3>
                    <p>Xem tuổi xây nhà giúp bạn chọn năm xây dựng nhà cửa phù hợp với tuổi gia chủ, tránh các hạn xấu và đem lại may mắn, tài lộc.</p>
                  
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>🏛️ Kim Lâu</strong>
                            <p>Kiểm tra hạn Kim Lâu theo tuổi gia chủ</p>
                        </div>
                        <div class="feature-item">
                            <strong>🏚️ Hoang Ốc</strong>
                            <p>Xem hạn Hoang Ốc khi làm nhà</p>
                        </div>
                        <div class="feature-item">
                            <strong>⚡ Tam Tai</strong>
                            <p>Kiểm tra năm Tam Tai cần tránh</p>
                        </div>
                        <div class="feature-item">
                            <strong>💡 Giải pháp</strong>
                            <p>Đề xuất cách hóa giải nếu phạm hạn</p>
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

    <!-- Thông báo -->
    <div id="notification" class="notification"></div>

    <script>
        let currentUser = <?php echo $user ? json_encode($user) : 'null'; ?>;
        let currentResult = null;

        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];

        function triggerDataUpdate() {
            localStorage.setItem('lastDataUpdate', Date.now().toString());
        }

        function listenForDataUpdates() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'lastDataUpdate') {
                    if (currentUser) {
                        loadFavorites();
                    }
                }
            });
        }

        function canChiOfYear(lY) { 
            return [CAN[(lY + 6) % 10], CHI[(lY + 8) % 12]]; 
        }

        function checkKimLau(tuoi) {
            const soCuoi = tuoi % 10;
            return [1, 3, 6, 8].includes(soCuoi);
        }

        function checkHoangOc(tuoi) {
            const cung = tuoi % 6;
            return [1, 2, 4].includes(cung);
        }

        function checkTamTai(chiNamSinh, namXay) {
            const nhomTamTai = {
                "Thân": [2020, 2021, 2022], "Tý": [2020, 2021, 2022], "Thìn": [2020, 2021, 2022],
                "Dần": [2023, 2024, 2025], "Ngọ": [2023, 2024, 2025], "Tuất": [2023, 2024, 2025],
                "Tỵ": [2026, 2027, 2028], "Dậu": [2026, 2027, 2028], "Sửu": [2026, 2027, 2028],
                "Hợi": [2029, 2030, 2031], "Mão": [2029, 2030, 2031], "Mùi": [2029, 2030, 2031]
            };
            
            const nhom = Object.entries(nhomTamTai).find(([chi, years]) => 
                years.includes(namXay)
            );
            
            return nhom && TU_XUNG[chiNamSinh]?.includes(nhom[0]);
        }

        const TU_XUNG = {
            "Tý": ["Ngọ", "Mão", "Dậu"],
            "Sửu": ["Mùi", "Thìn", "Tuất"],
            "Dần": ["Thân", "Tỵ", "Hợi"],
            "Mão": ["Dậu", "Tý", "Ngọ"],
            "Thìn": ["Tuất", "Sửu", "Mùi"],
            "Tỵ": ["Hợi", "Dần", "Thân"],
            "Ngọ": ["Tý", "Mão", "Dậu"],
            "Mùi": ["Sửu", "Thìn", "Tuất"],
            "Thân": ["Dần", "Tỵ", "Hợi"],
            "Dậu": ["Mão", "Tý", "Ngọ"],
            "Tuất": ["Thìn", "Sửu", "Mùi"],
            "Hợi": ["Tỵ", "Dần", "Thân"]
        };

        function initializeApp() {
            fetchCurrentUser();
            initializeEventListeners();
            listenForDataUpdates();
            loadFromHistory();
        }

        function fetchCurrentUser() {
            fetch('api/get_current_user.php')
                .then(res => res.json())
                .then(data => {
                    if (data.logged_in) {
                        currentUser = data.user;
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

                        document.getElementById('favorites-section').style.display = 'block';
                        loadFavorites();
                    } else {
                        currentUser = null;
                        document.getElementById('user-info').style.display = 'none';
                        document.getElementById('auth-buttons').style.display = 'flex';
                        document.getElementById('favorites-section').style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi lấy thông tin người dùng:', err);
                });
        }
        
        function initializeEventListeners() {
            document.getElementById('login-btn').addEventListener('click', showLoginModal);
            document.getElementById('register-btn').addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn').addEventListener('click', logout);
            document.getElementById('profile-btn').addEventListener('click', showProfileModal);
            
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3500);
        }

        function showLoginModal() {
            document.getElementById('login-modal').style.display = 'flex';
        }

        function closeLoginModal() {
            document.getElementById('login-modal').style.display = 'none';
            document.getElementById('login-form').reset();
        }

        function showRegisterModal() {
            document.getElementById('register-modal').style.display = 'flex';
        }

        function closeRegisterModal() {
            document.getElementById('register-modal').style.display = 'none';
            document.getElementById('register-form').reset();
        }

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
                    document.getElementById('favorites-section').style.display = 'block';
                    loadFavorites();
                    showNotification(data.message || 'Đăng nhập thành công!', 'success');
                    triggerDataUpdate();
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

                    closeRegisterModal();
                    document.getElementById('favorites-section').style.display = 'block';
                    loadFavorites();
                    showNotification(data.message || 'Đăng ký thành công!', 'success');
                    triggerDataUpdate();
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
                    document.getElementById('favorites-section').style.display = 'none';
                    showNotification(data.message || 'Đã đăng xuất thành công!', 'success');
                    triggerDataUpdate();
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
        }

        function showProfileModal() {
            window.location.href = 'user.php';
        }

        // Lưu lịch sử vào CSDL
        async function saveToHistory(result) {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/save_xaynha_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        owner_year: result.ownerYear,
                        build_year: result.buildYear,
                        kimlau: result.kimLau ? 1 : 0,
                        hoangoc: result.hoangOc ? 1 : 0,
                        tamtai: result.tamTai ? 1 : 0,
                        evaluation: result.danhGia,
                        detail: JSON.stringify({
                            canOwner: result.canOwner,
                            chiOwner: result.chiOwner,
                            canBuild: result.canBuild,
                            chiBuild: result.chiBuild,
                            tuoiOwner: result.tuoiOwner,
                            score: result.score,
                            details: result.details,
                            warnings: result.warnings
                        })
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    console.log('Đã lưu lịch sử tra cứu');
                    triggerDataUpdate();
                }
            } catch (error) {
                console.error('Lỗi khi lưu lịch sử:', error);
            }
        }

        // Thêm vào yêu thích
        async function addToFavorites(result) {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để lưu kết quả!', 'error');
                return;
            }
            
            try {
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        solar: `${result.buildYear}-01-01`,
                        lunar: `Xây nhà ${result.ownerYear} → ${result.buildYear}`,
                        rating: `Xây nhà: ${result.danhGia} - Điểm: ${result.score}`,
                        score: result.score
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                    triggerDataUpdate();
                } else {
                    showNotification(data.message || 'Lỗi khi thêm vào yêu thích', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi thêm vào yêu thích:', error);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        // Load favorites từ API
        async function loadFavorites() {
            if (!currentUser) return;
            
            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                const favoritesList = document.getElementById('favorites-list');
                
                if (data.success && data.favorites && data.favorites.length > 0) {
                    const xaynhaFavorites = data.favorites.filter(fav => 
                        fav.rating_text.includes('Xây nhà')
                    );
                    
                    if (xaynhaFavorites.length === 0) {
                        favoritesList.innerHTML = '<div class="empty-favorites">Chưa có kết quả xây nhà nào được lưu</div>';
                        return;
                    }
                    
                    favoritesList.innerHTML = xaynhaFavorites.map(fav => {
                        const match = fav.rating_text.match(/Xây nhà: (.+?) - Điểm: (.+)/);
                        const danhGia = match ? match[1] : 'N/A';
                        const score = match ? match[2] : '0';
                        
                        return `
                            <div class="favorite-item">
                                <div class="favorite-header">
                                    <div class="favorite-title">${fav.rating_text}</div>
                                    <div class="favorite-date">${new Date(fav.created_at).toLocaleDateString('vi-VN')}</div>
                                </div>
                                <div class="info">
                                    <strong>Đánh giá:</strong> ${danhGia}<br>
                                    <strong>Điểm:</strong> ${score}
                                </div>
                                <div class="favorite-actions">
                                    <button class="btn-info" onclick="loadFavorite(${fav.id})">👁️ Xem lại</button>
                                    <button class="btn-danger" onclick="removeFavorite(${fav.id})">🗑️ Xóa</button>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    favoritesList.innerHTML = '<div class="empty-favorites">Chưa có kết quả nào được lưu</div>';
                }
            } catch (error) {
                console.error('Lỗi tải danh sách yêu thích:', error);
            }
        }

        async function removeFavorite(favoriteId) {
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
                    loadFavorites();
                    triggerDataUpdate();
                } else {
                    showNotification(data.message || 'Lỗi khi xóa', 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa yêu thích:', error);
                showNotification('Lỗi kết nối server!', 'error');
            }
        }

        function loadFavorite(favoriteId) {
            // Tải lại trang với thông tin từ favorite
            showNotification('Đã tải thông tin từ yêu thích!', 'info');
        }

        function loadFromHistory() {
            const urlParams = new URLSearchParams(window.location.search);
            const historyId = urlParams.get('loadHistory');
            
            if (historyId && currentUser) {
                loadHistoryDetail(historyId);
            }
        }

        async function loadHistoryDetail(historyId) {
            try {
                const response = await fetch(`api/get_xaynha_history_detail.php?id=${historyId}`);
                const data = await response.json();
                
                if (data.success && data.history) {
                    const history = data.history;
                    document.getElementById('ownerYear').value = history.owner_year;
                    document.getElementById('buildYear').value = history.build_year;
                    checkBuildCompatibility();
                    showNotification('Đã tải kết quả từ lịch sử!', 'info');
                }
            } catch (error) {
                console.error('Lỗi tải chi tiết lịch sử:', error);
            }
        }

        function checkBuildCompatibility() {
            const ownerYear = parseInt(document.getElementById('ownerYear').value);
            const buildYear = parseInt(document.getElementById('buildYear').value);
            
            if (!ownerYear || !buildYear) {
                showNotification('Vui lòng nhập đầy đủ thông tin!', 'error');
                return;
            }
            
            const [canOwner, chiOwner] = canChiOfYear(ownerYear);
            const [canBuild, chiBuild] = canChiOfYear(buildYear);
            
            const tuoiOwner = buildYear - ownerYear;
            
            // Kiểm tra các hạn
            const kimLau = checkKimLau(tuoiOwner);
            const hoangOc = checkHoangOc(tuoiOwner);
            const tamTai = checkTamTai(chiOwner, buildYear);
            
            let score = 0;
            let details = [];
            let warnings = [];
            
            if (!kimLau) {
                score += 2;
                details.push("✅ Không phạm Kim Lâu");
            } else {
                score -= 2;
                warnings.push("❌ PHẠM KIM LÂU: Tránh làm nhà");
            }
            
            if (!hoangOc) {
                score += 1;
                details.push("✅ Không phạm Hoang Ốc");
            } else {
                score -= 1;
                warnings.push("❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ");
            }
            
            if (!tamTai) {
                score += 1;
                details.push("✅ Không phạm Tam Tai");
            } else {
                score -= 1;
                warnings.push("⚠️ Phạm Tam Tai: Nên tránh");
            }
            
            const danhGia = score >= 2 ? "NÊN LÀM" : score >= 0 ? "CÂN NHẮC" : "KHÔNG NÊN";
            
            // Lưu kết quả
            currentResult = {
                ownerYear,
                buildYear,
                canOwner,
                chiOwner,
                canBuild,
                chiBuild,
                tuoiOwner,
                kimLau,
                hoangOc,
                tamTai,
                score,
                danhGia,
                details,
                warnings
            };
            
            // Lưu lịch sử vào CSDL
            saveToHistory(currentResult);
            
            document.getElementById('buildResult').innerHTML = `
                <div class="result">
                    <h3>📊 Kết quả xem tuổi làm nhà</h3>
                    <div class="info-grid">
                        <div class="info"><strong>👤 Gia chủ</strong> ${ownerYear} - ${canOwner} ${chiOwner}</div>
                        <div class="info"><strong>🏠 Năm xây</strong> ${buildYear} - ${canBuild} ${chiBuild}</div>
                        <div class="info"><strong>🎂 Tuổi</strong> ${tuoiOwner} tuổi</div>
                    </div>
                    <div class="bar ${score >= 2 ? 'good' : score >= 0 ? 'neutral' : 'bad'}">
                        ${danhGia} - Điểm: ${score}
                    </div>
                    <div class="analysis-details">
                        <strong>📋 Kiểm tra hạn:</strong>
                        ${details.map(detail => `<div class="analysis-item analysis-good">${detail}</div>`).join('')}
                        ${warnings.map(warning => `<div class="analysis-item ${warning.includes('PHẠM') ? 'analysis-bad' : 'analysis-warning'}">${warning}</div>`).join('')}
                    </div>
                    ${warnings.length > 0 ? `
                    <div class="recommendation-box">
                        <h4>💡 Giải pháp hóa giải:</h4>
                        <ul>
                            <li>Mượn tuổi người khác làm nhà (người không phạm các hạn trên)</li>
                            <li>Chọn năm khác không phạm hạn để xây dựng</li>
                            <li>Làm lễ hóa giải trước khi động thổ</li>
                            <li>Nhờ thầy phong thủy chọn ngày giờ tốt</li>
                        </ul>
                    </div>` : ''}
                    <button class="favorite-btn" onclick="addToFavorites(currentResult)">
                        <span class="icon">❤️</span> Lưu vào yêu thích
                    </button>
                </div>
            `;
        }

        document.addEventListener('DOMContentLoaded', initializeApp);
    </script>
</body>
</html>
<?php
session_start();
require_once "config.php";

$user = null;

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];

    $sql  = "SELECT * FROM users WHERE id = ?";
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
    <title>Xem Hướng - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        /* Additional styles specific to huongnha.php */
        .calculator-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
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
            gap: 6px;
        }

        .field label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }

        .field input, .field select {
            padding: 10px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: .2s;
            background: #fff;
        }

        .field input:focus, .field select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,.1);
        }

        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        /* Result styles */
        .result {
            position: relative;
            margin-top: 16px;
            background: #fff;
            border-left: 4px solid #667eea;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
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

        .info strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 4px;
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

        /* Cải thiện hiển thị hướng tốt/xấu */
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

        /* Styles cho nút yêu thích */
        .favorite-btn {
            background: #ffd700;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 10px 0;
        }

        .favorite-btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

        .favorite-btn.favorited {
            background: #ff6b6b;
            color: white;
        }

        .favorite-btn.favorited:hover {
            background: #ff5252;
        }

        .favorites-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }

        .favorites-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .favorite-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            position: relative;
        }

        .favorite-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .favorite-title {
            font-weight: bold;
            color: #2c3e50;
        }

        .favorite-date {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .favorite-actions {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .favorite-actions button {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .empty-favorites {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .direction-container {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Xem hướng nhà hợp tuổi</p>
          
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
                    <a class="nav-link active" href="huongnha.php">
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
                <h2>🧭 Xem Hướng Nhà Hợp Tuổi</h2>
                
                <div class="panel">
                    <div class="panel-title">🏡 Xem hướng nhà hợp tuổi</div>
                    <div class="calculator-form">
                        <div class="form-row">
                            <div class="field">
                                <label>Năm sinh gia chủ</label>
                                <input type="number" id="directionYear" min="1900" max="2100" placeholder="VD: 1980" value="1980">
                            </div>
                            <div class="field">
                                <label>Giới tính</label>
                                <select id="directionGender">
                                    <option value="male">Nam</option>
                                    <option value="female">Nữ</option>
                                </select>
                            </div>
                        </div>
                        <div class="btn-row">
                            <button class="btn-success" onclick="calculateDirections()">🔍 Xem hướng hợp</button>
                        </div>
                        <div id="directionResult"></div>
                    </div>
                </div>

                <!-- Khu vực yêu thích -->
                <div class="favorites-section">
                    <div class="panel-title">⭐ Tra cứu đã lưu vào yêu thích</div>
                    <div id="favoritesList" class="favorites-list">
                        <!-- Danh sách yêu thích sẽ được hiển thị ở đây -->
                    </div>
                </div>

                <!-- Khu vực lịch sử tra cứu -->
                <div class="favorites-section">
                    <div class="panel-title">📚 Lịch sử tra cứu gần đây</div>
                    <div id="historyList" class="favorites-list">
                        <!-- Danh sách lịch sử sẽ được hiển thị ở đây -->
                    </div>
                </div>

                <div class="service-detail">
                    <h3>📚 Thông tin về dịch vụ</h3>
                    <p>Xem hướng giúp bạn xác định các hướng tốt và xấu cho nhà ở, bàn làm việc, giường ngủ dựa trên tuổi và giới tính của gia chủ.</p>
                  
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>🔮 Cung Phi</strong>
                            <p>Tính toán cung phi theo năm sinh</p>
                        </div>
                        <div class="feature-item">
                            <strong>✅ Hướng tốt</strong>
                            <p>Xác định các hướng mang lại may mắn</p>
                        </div>
                        <div class="feature-item">
                            <strong>❌ Hướng xấu</strong>
                            <p>Nhận diện các hướng cần tránh</p>
                        </div>
                        <div class="feature-item">
                            <strong>💡 Ứng dụng</strong>
                            <p>Hướng dẫn áp dụng vào thực tế</p>
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
        // Biến toàn cục
        let currentUser = <?php echo $user ? json_encode($user) : 'null'; ?>;
        let currentDirectionResult = null;

        // Dữ liệu cung phi và hướng
        const CUNG_PHI = {
            "male": {
                "1": "Khảm", "2": "Ly", "3": "Cấn", "4": "Đoài", 
                "5": "Càn", "6": "Khôn", "7": "Tốn", "8": "Chấn",
                "9": "Khôn"
            },
            "female": {
                "1": "Cấn", "2": "Càn", "3": "Đoài", "4": "Cấn",
                "5": "Ly", "6": "Khảm", "7": "Khôn", "8": "Chấn", 
                "9": "Tốn"
            }
        };

        const HUONG_TOT_XAU = {
            "Khảm": { 
                tot: ["Bắc", "Đông", "Đông Nam", "Nam"], 
                xau: ["Tây Bắc", "Tây Nam", "Đông Bắc", "Tây"] 
            },
            "Ly": { 
                tot: ["Nam", "Đông", "Đông Nam", "Bắc"], 
                xau: ["Tây Bắc", "Tây Nam", "Đông Bắc", "Tây"] 
            },
            "Chấn": { 
                tot: ["Đông", "Nam", "Bắc", "Đông Nam"], 
                xau: ["Tây", "Tây Bắc", "Tây Nam", "Đông Bắc"] 
            },
            "Tốn": { 
                tot: ["Đông Nam", "Bắc", "Đông", "Nam"], 
                xau: ["Đông Bắc", "Tây Nam", "Tây Bắc", "Tây"] 
            },
            "Càn": { 
                tot: ["Tây Bắc", "Tây Nam", "Đông Bắc", "Tây"], 
                xau: ["Nam", "Đông", "Đông Nam", "Bắc"] 
            },
            "Đoài": { 
                tot: ["Tây", "Tây Bắc", "Tây Nam", "Đông Bắc"], 
                xau: ["Đông", "Nam", "Bắc", "Đông Nam"] 
            },
            "Cấn": { 
                tot: ["Đông Bắc", "Tây Nam", "Tây Bắc", "Tây"], 
                xau: ["Đông Nam", "Bắc", "Đông", "Nam"] 
            },
            "Khôn": { 
                tot: ["Tây Nam", "Tây Bắc", "Tây", "Đông Bắc"], 
                xau: ["Bắc", "Đông", "Đông Nam", "Nam"] 
            }
        };

        // Khởi tạo ứng dụng
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        function initializeApp() {
            updateUserDisplay();
            initializeEventListeners();
            displayFavorites();
            displayHistory();
        }

        function updateUserDisplay() {
            const userInfo = document.getElementById('user-info');
            const authButtons = document.getElementById('auth-buttons');

            if (currentUser) {
                userInfo.style.display = 'flex';
                authButtons.style.display = 'none';
                
                document.getElementById('user-display-name').textContent = currentUser.name;
                document.getElementById('user-display-email').textContent = currentUser.email;
                
                const initials = currentUser.name
                    .split(' ')
                    .map(n => n[0])
                    .join('')
                    .substring(0, 2)
                    .toUpperCase();
                document.getElementById('user-avatar').textContent = initials;
            } else {
                userInfo.style.display = 'none';
                authButtons.style.display = 'flex';
            }
        }

        function initializeEventListeners() {
            document.getElementById('login-btn').addEventListener('click', showLoginModal);
            document.getElementById('register-btn').addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn').addEventListener('click', logout);
            document.getElementById('profile-btn').addEventListener('click', () => {
                window.location.href = 'user.php';
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            });
        }

        // Hàm hiển thị thông báo
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3500);
        }

        // Modal functions
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

        // Authentication functions
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
                    updateUserDisplay();
                    closeLoginModal();
                    showNotification(data.message || 'Đăng nhập thành công!', 'success');
                    location.reload();
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
                    updateUserDisplay();
                    closeRegisterModal();
                    showNotification(data.message || 'Đăng ký thành công!', 'success');
                    location.reload();
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
                    updateUserDisplay();
                    showNotification(data.message || 'Đã đăng xuất thành công!', 'success');
                    location.reload();
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
        }

        // ==================== PHẦN CHỨC NĂNG XEM HƯỚNG ====================

        // Hàm tính cung phi
        function calculateCungPhi(namSinh, gioiTinh) {
            const tongSo = Array.from(String(namSinh)).reduce((sum, digit) => sum + parseInt(digit), 0);
            const soCung = (tongSo % 9) || 9;
            return CUNG_PHI[gioiTinh][soCung.toString()];
        }

        // Hàm tính hướng
        async function calculateDirections() {
            const directionYear = parseInt(document.getElementById('directionYear').value);
            const directionGender = document.getElementById('directionGender').value;
            
            if (!directionYear) {
                showNotification('Vui lòng nhập năm sinh!', 'error');
                return;
            }
            
            const cungPhi = calculateCungPhi(directionYear, directionGender);
            const huong = HUONG_TOT_XAU[cungPhi];
            
            if (!huong) {
                document.getElementById('directionResult').innerHTML = `
                    <div class="result">
                        <div class="bar bad">Không tìm thấy thông tin cung phi</div>
                    </div>
                `;
                return;
            }
            
            // Lưu kết quả hiện tại
            currentDirectionResult = {
                year: directionYear,
                gender: directionGender,
                cungPhi: cungPhi,
                huongTot: huong.tot,
                huongXau: huong.xau
            };
            
            document.getElementById('directionResult').innerHTML = `
                <div class="result">
                    <button id="favoriteBtn" class="favorite-btn" onclick="saveToFavorites()">
                        <span>⭐</span> Lưu tra cứu này
                    </button>
                    <h3>🧭 Kết quả xem hướng nhà</h3>
                    <div class="info-grid">
                        <div class="info"><strong>👤 Năm sinh</strong> ${directionYear}</div>
                        <div class="info"><strong>⚧ Giới tính</strong> ${directionGender === 'male' ? 'Nam' : 'Nữ'}</div>
                        <div class="info"><strong>🔮 Cung phi</strong> ${cungPhi}</div>
                    </div>
                    
                    <div class="direction-container">
                        <div class="direction-card direction-good">
                            <div class="direction-header">
                                <span class="direction-icon">✅</span>
                                Hướng tốt (nên chọn)
                            </div>
                            <ul class="direction-list">
                                ${huong.tot.map(h => `<li>${h}</li>`).join('')}
                            </ul>
                        </div>
                        <div class="direction-card direction-bad">
                            <div class="direction-header">
                                <span class="direction-icon">❌</span>
                                Hướng xấu (nên tránh)
                            </div>
                            <ul class="direction-list">
                                ${huong.xau.map(h => `<li>${h}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                    
                    <div class="info">
                        <strong>💡 Ứng dụng thực tế:</strong><br>
                        - Cửa chính nên mở về hướng tốt<br>
                        - Bếp đặt ở hướng tốt<br>
                        - Giường ngủ quay đầu hướng tốt<br>
                        - Bàn làm việc hướng tốt
                    </div>
                </div>
            `;
            
            // TỰ ĐỘNG LƯU LỊCH SỬ TRA CỨU
            if (currentUser) {
                await saveToHistory();
            }
        }

        // Lưu vào danh sách yêu thích
        async function saveToFavorites() {
            if (!currentDirectionResult) {
                showNotification('Không có kết quả phân tích nào để lưu!', 'error');
                return;
            }

            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để lưu tra cứu!', 'error');
                showLoginModal();
                return;
            }

            try {
                // Tạo dữ liệu để lưu vào favorites
                const favoriteData = {
                    solar: new Date().toISOString().split('T')[0], // Ngày hiện tại
                    lunar: `Năm ${currentDirectionResult.year} - ${currentDirectionResult.gender === 'male' ? 'Nam' : 'Nữ'}`,
                    rating: `Xem hướng nhà - Cung ${currentDirectionResult.cungPhi}. Hướng tốt: ${currentDirectionResult.huongTot.join(', ')}`,
                    score: 8.5 // Điểm mặc định cho xem hướng
                };

                // Lưu vào favorites
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(favoriteData)
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã lưu tra cứu vào danh sách yêu thích!', 'success');
                    // Cập nhật danh sách yêu thích
                    displayFavorites();
                } else {
                    showNotification(data.message || 'Lỗi khi lưu tra cứu!', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi lưu:', error);
                showNotification('Lỗi kết nối khi lưu!', 'error');
            }
        }

        // Lưu lịch sử tra cứu vào database
        async function saveToHistory() {
            if (!currentDirectionResult || !currentUser) return;
            
            try {
                const historyData = {
                    owner_year: currentDirectionResult.year,
                    gender: currentDirectionResult.gender,
                    cung_phi: currentDirectionResult.cungPhi,
                    good_directions: currentDirectionResult.huongTot.join(', '),
                    bad_directions: currentDirectionResult.huongXau.join(', ')
                };

                const response = await fetch('api/save_huongnha.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(historyData)
                });

                const data = await response.json();
                
                if (data.success) {
                    // Cập nhật danh sách lịch sử
                    displayHistory();
                } else {
                    console.error('Lỗi khi lưu lịch sử:', data.message);
                }
            } catch (error) {
                console.error('Lỗi kết nối khi lưu lịch sử:', error);
            }
        }

        // Hiển thị danh sách yêu thích từ favorites
        async function displayFavorites() {
            if (!currentUser) return;

            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                const favoritesList = document.getElementById('favoritesList');
                
                if (!data.success || !data.favorites || data.favorites.length === 0) {
                    favoritesList.innerHTML = `
                        <div class="empty-favorites">
                            <p>Chưa có tra cứu nào được lưu vào yêu thích</p>
                            <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                        </div>
                    `;
                    return;
                }
                
                // Lọc chỉ các favorites liên quan đến xem hướng
                const directionFavorites = data.favorites.filter(fav => 
                    fav.rating_text && fav.rating_text.includes('Xem hướng nhà')
                );
                
                if (directionFavorites.length === 0) {
                    favoritesList.innerHTML = `
                        <div class="empty-favorites">
                            <p>Chưa có tra cứu xem hướng nào được lưu vào yêu thích</p>
                            <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                        </div>
                    `;
                    return;
                }
                
                favoritesList.innerHTML = directionFavorites.map(favorite => {
                    // Format lại ngày cho dễ đọc
                    let solarDate = favorite.solar_date || 'N/A';
                    if (solarDate !== 'N/A' && solarDate.includes('-')) {
                        const parts = solarDate.split('-');
                        if (parts.length === 3 && parts[0].length === 4) {
                            solarDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
                        }
                    }
                    
                    return `
                        <div class="favorite-item">
                            <div class="favorite-item-header">
                                <div class="favorite-title">🧭 Xem hướng nhà</div>
                                <div class="favorite-date">${new Date(favorite.created_at).toLocaleString('vi-VN')}</div>
                            </div>
                            <div class="favorite-details">
                                <p><strong>Thông tin:</strong> ${favorite.lunar_date || 'N/A'}</p>
                                <p><strong>Kết quả:</strong> ${favorite.rating_text || 'N/A'}</p>
                                <p><strong>Ngày lưu:</strong> ${solarDate}</p>
                                <p><strong>Đánh giá:</strong> ${favorite.score || 'N/A'}/10</p>
                            </div>
                            <div class="favorite-actions">
                                <button class="btn-info" onclick="loadFavorite(${favorite.id})">👁️ Xem lại</button>
                                <button class="btn-danger" onclick="deleteFavorite(${favorite.id})">🗑️ Xóa</button>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Lỗi khi tải danh sách yêu thích:', error);
                const favoritesList = document.getElementById('favoritesList');
                favoritesList.innerHTML = `
                    <div class="empty-favorites">
                        <p>Lỗi khi tải danh sách yêu thích</p>
                    </div>
                `;
            }
        }

        // Hiển thị lịch sử tra cứu từ database
        async function displayHistory() {
            if (!currentUser) return;

            try {
                const response = await fetch('api/get_huongnha_history.php?limit=5');
                const data = await response.json();
                
                const historyList = document.getElementById('historyList');
                
                if (!data.success || !data.history || data.history.length === 0) {
                    historyList.innerHTML = `
                        <div class="empty-favorites">
                            <p>Chưa có lịch sử tra cứu</p>
                            <p>Thực hiện phân tích để xem lịch sử tra cứu</p>
                        </div>
                    `;
                    return;
                }
                
                historyList.innerHTML = data.history.map(item => {
                    const createdDate = new Date(item.created_at).toLocaleString('vi-VN');
                    
                    return `
                        <div class="favorite-item">
                            <div class="favorite-item-header">
                                <div class="favorite-title">🧭 Xem hướng nhà</div>
                                <div class="favorite-date">${createdDate}</div>
                            </div>
                            <div class="favorite-details">
                                <p><strong>Năm sinh:</strong> ${item.owner_year || 'N/A'}</p>
                                <p><strong>Hướng tốt:</strong> ${item.good_directions || 'N/A'}</p>
                                <p><strong>Hướng xấu:</strong> ${item.bad_directions || 'N/A'}</p>
                                <p><strong>Ghi chú:</strong> ${item.summary || 'N/A'}</p>
                            </div>
                            <div class="favorite-actions">
                                <button class="btn-info" onclick="loadFromHistory(${item.owner_year})">👁️ Xem lại</button>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Lỗi khi tải lịch sử tra cứu:', error);
                const historyList = document.getElementById('historyList');
                historyList.innerHTML = `
                    <div class="empty-favorites">
                        <p>Lỗi khi tải lịch sử tra cứu</p>
                    </div>
                `;
            }
        }

        // Tải lại từ yêu thích
        function loadFavorite(id) {
            showNotification('Đã chọn tra cứu từ danh sách yêu thích!', 'info');
            window.scrollTo(0, 0);
        }

        // Tải lại từ lịch sử
        function loadFromHistory(year) {
            document.getElementById('directionYear').value = year;
            showNotification(`Đã tải tra cứu cho năm ${year}`, 'info');
            window.scrollTo(0, 0);
        }

        // Xóa tra cứu khỏi danh sách yêu thích
        async function deleteFavorite(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa tra cứu này khỏi danh sách yêu thích?')) {
                return;
            }
            
            try {
                const response = await fetch('api/remove_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Đã xóa tra cứu khỏi danh sách yêu thích!', 'success');
                    displayFavorites(); // Cập nhật lại danh sách
                } else {
                    showNotification('Lỗi khi xóa tra cứu!', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi xóa:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }
    </script>
</body>
</html>
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
    <title>Xem Tuổi Làm Ăn - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
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

        .result {
            position: relative;
            margin-top: 16px;
            background: #fff;
            border-left: 4px solid #667eea;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
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
            <p class="subtitle">Xem hợp tác làm ăn theo tuổi</p>
          
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
                    <a class="nav-link active" href="laman.php">
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
      
        <div class="app-container">
            <section class="info-section">
                <h2>💰 Xem Tuổi Làm Ăn</h2>
                
                <div class="panel">
                    <div class="panel-title">✅ Xem hợp tác làm ăn</div>
                    <div class="calculator-form">
                        <div class="form-row">
                            <div class="field">
                                <label>Năm sinh người A</label>
                                <input type="number" id="personAYear" min="1900" max="2100" placeholder="VD: 1980" value="1980">
                            </div>
                            <div class="field">
                                <label>Năm sinh người B</label>
                                <input type="number" id="personBYear" min="1900" max="2100" placeholder="VD: 1985" value="1985">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="field">
                                <label>Năm khởi sự (tùy chọn)</label>
                                <input type="number" id="businessYear" min="2020" max="2100" placeholder="VD: 2024" value="2024">
                            </div>
                        </div>
                        <div class="btn-row">
                            <button class="btn-success" onclick="checkBusinessCompatibility()">🔍 Xem hợp tác</button>
                        </div>
                        <div id="businessResult"></div>
                    </div>
                </div>

                <div class="favorites-section">
                    <div class="panel-title">⭐ Tra cứu đã lưu</div>
                    <div id="favoritesList" class="favorites-list"></div>
                </div>

                <div class="service-detail">
                    <h3>📚 Thông tin về dịch vụ</h3>
                    <p>Xem tuổi làm ăn giúp đánh giá mức độ hợp tác kinh doanh giữa các đối tác dựa trên các yếu tố phong thủy truyền thống.</p>
                  
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>🔮 Hợp mệnh</strong>
                            <p>Kiểm tra ngũ hành tương sinh tương khắc</p>
                        </div>
                        <div class="feature-item">
                            <strong>⚖️ Địa chi</strong>
                            <p>Xem tam hợp, tứ hành xung</p>
                        </div>
                        <div class="feature-item">
                            <strong>📅 Năm khởi sự</strong>
                            <p>Đánh giá năm bắt đầu kinh doanh</p>
                        </div>
                        <div class="feature-item">
                            <strong>💯 Điểm số</strong>
                            <p>Đánh giá tổng quan mức độ hợp tác</p>
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
        let currentBusinessResult = null;

        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];

        const HANH_CAN = { 
            Giáp: "Mộc", Ất: "Mộc", Bính: "Hỏa", Đinh: "Hỏa", 
            Mậu: "Thổ", Kỷ: "Thổ", Canh: "Kim", Tân: "Kim", 
            Nhâm: "Thủy", Quý: "Thủy" 
        };

        const NAP_AM = {
            "Giáp Tý": { ten: "Hải Trung Kim", hanh: "Kim" }, "Ất Sửu": { ten: "Hải Trung Kim", hanh: "Kim" },
            "Bính Dần": { ten: "Lư Trung Hỏa", hanh: "Hỏa" }, "Đinh Mão": { ten: "Lư Trung Hỏa", hanh: "Hỏa" },
            "Mậu Thìn": { ten: "Đại Lâm Mộc", hanh: "Mộc" }, "Kỷ Tỵ": { ten: "Đại Lâm Mộc", hanh: "Mộc" },
            "Canh Ngọ": { ten: "Lộ Bàng Thổ", hanh: "Thổ" }, "Tân Mùi": { ten: "Lộ Bàng Thổ", hanh: "Thổ" },
            "Nhâm Thân": { ten: "Kiếm Phong Kim", hanh: "Kim" }, "Quý Dậu": { ten: "Kiếm Phong Kim", hanh: "Kim" },
            "Giáp Tuất": { ten: "Sơn Đầu Hỏa", hanh: "Hỏa" }, "Ất Hợi": { ten: "Sơn Đầu Hỏa", hanh: "Hỏa" },
            "Bính Tý": { ten: "Giản Hạ Thủy", hanh: "Thủy" }, "Đinh Sửu": { ten: "Giản Hạ Thủy", hanh: "Thủy" },
            "Mậu Dần": { ten: "Thành Đầu Thổ", hanh: "Thổ" }, "Kỷ Mão": { ten: "Thành Đầu Thổ", hanh: "Thổ" },
            "Canh Thìn": { ten: "Bạch Lạp Kim", hanh: "Kim" }, "Tân Tỵ": { ten: "Bạch Lạp Kim", hanh: "Kim" },
            "Nhâm Ngọ": { ten: "Dương Liễu Mộc", hanh: "Mộc" }, "Quý Mùi": { ten: "Dương Liễu Mộc", hanh: "Mộc" },
            "Giáp Thân": { ten: "Tuyền Trung Thủy", hanh: "Thủy" }, "Ất Dậu": { ten: "Tuyền Trung Thủy", hanh: "Thủy" },
            "Bính Tuất": { ten: "Ốc Thượng Thổ", hanh: "Thổ" }, "Đinh Hợi": { ten: "Ốc Thượng Thổ", hanh: "Thổ" },
            "Mậu Tý": { ten: "Tích Lịch Hỏa", hanh: "Hỏa" }, "Kỷ Sửu": { ten: "Tích Lịch Hỏa", hanh: "Hỏa" },
            "Canh Dần": { ten: "Tùng Bách Mộc", hanh: "Mộc" }, "Tân Mão": { ten: "Tùng Bách Mộc", hanh: "Mộc" },
            "Nhâm Thìn": { ten: "Trường Lưu Thủy", hanh: "Thủy" }, "Quý Tỵ": { ten: "Trường Lưu Thủy", hanh: "Thủy" },
            "Giáp Ngọ": { ten: "Sa Trung Kim", hanh: "Kim" }, "Ất Mùi": { ten: "Sa Trung Kim", hanh: "Kim" },
            "Bính Thân": { ten: "Sơn Hạ Hỏa", hanh: "Hỏa" }, "Đinh Dậu": { ten: "Sơn Hạ Hỏa", hanh: "Hỏa" },
            "Mậu Tuất": { ten: "Bình Địa Mộc", hanh: "Mộc" }, "Kỷ Hợi": { ten: "Bình Địa Mộc", hanh: "Mộc" },
            "Canh Tý": { ten: "Bích Thượng Thổ", hanh: "Thổ" }, "Tân Sửu": { ten: "Bích Thượng Thổ", hanh: "Thổ" },
            "Nhâm Dần": { ten: "Kim Bạch Kim", hanh: "Kim" }, "Quý Mão": { ten: "Kim Bạch Kim", hanh: "Kim" },
            "Giáp Thìn": { ten: "Phúc Đăng Hỏa", hanh: "Hỏa" }, "Ất Tỵ": { ten: "Phúc Đăng Hỏa", hanh: "Hỏa" },
            "Bính Ngọ": { ten: "Thiên Hà Thủy", hanh: "Thủy" }, "Đinh Mùi": { ten: "Thiên Hà Thủy", hanh: "Thủy" },
            "Mậu Thân": { ten: "Đại Trạch Thổ", hanh: "Thổ" }, "Kỷ Dậu": { ten: "Đại Trạch Thổ", hanh: "Thổ" },
            "Canh Tuất": { ten: "Thoa Xuyến Kim", hanh: "Kim" }, "Tân Hợi": { ten: "Thoa Xuyến Kim", hanh: "Kim" },
            "Nhâm Tý": { ten: "Tang Đố Mộc", hanh: "Mộc" }, "Quý Sửu": { ten: "Tang Đố Mộc", hanh: "Mộc" },
            "Giáp Dần": { ten: "Đại Khê Thủy", hanh: "Thủy" }, "Ất Mão": { ten: "Đại Khê Thủy", hanh: "Thủy" },
            "Bính Thìn": { ten: "Sa Trung Thổ", hanh: "Thổ" }, "Đinh Tỵ": { ten: "Sa Trung Thổ", hanh: "Thổ" },
            "Mậu Ngọ": { ten: "Thiên Thượng Hỏa", hanh: "Hỏa" }, "Kỷ Mùi": { ten: "Thiên Thượng Hỏa", hanh: "Hỏa" },
            "Canh Thân": { ten: "Thạch Lựu Mộc", hanh: "Mộc" }, "Tân Dậu": { ten: "Thạch Lựu Mộc", hanh: "Mộc" },
            "Nhâm Tuất": { ten: "Đại Hải Thủy", hanh: "Thủy" }, "Quý Hợi": { ten: "Đại Hải Thủy", hanh: "Thủy" }
        };

        const TAM_HOP = {
            "Thân": ["Thân", "Tý", "Thìn"],
            "Tỵ": ["Tỵ", "Dậu", "Sửu"], 
            "Dần": ["Dần", "Ngọ", "Tuất"],
            "Hợi": ["Hợi", "Mão", "Mùi"]
        };

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

        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            displayFavorites();
        });

        function initializeApp() {
            updateUserDisplay();
            initializeEventListeners();
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

        function canChiOfYear(year) { 
            return [CAN[(year + 6) % 10], CHI[(year + 8) % 12]]; 
        }

        function checkTuongSinh(menh1, menh2) {
            const sinh = {
                "Mộc": "Hỏa", "Hỏa": "Thổ", "Thổ": "Kim", 
                "Kim": "Thủy", "Thủy": "Mộc"
            };
            return sinh[menh1] === menh2 || sinh[menh2] === menh1;
        }

        function checkTuongKhac(menh1, menh2) {
            const khac = {
                "Mộc": "Thổ", "Thổ": "Thủy", "Thủy": "Hỏa",
                "Hỏa": "Kim", "Kim": "Mộc"
            };
            return khac[menh1] === menh2 || khac[menh2] === menh1;
        }

        function checkTamHop(chi1, chi2) {
            return Object.values(TAM_HOP).some(group => 
                group.includes(chi1) && group.includes(chi2)
            );
        }

        function checkTuXung(chi1, chi2) {
            return TU_XUNG[chi1]?.includes(chi2) || false;
        }

        async function saveBusinessHistory(personAYear, personBYear, businessYear, result) {
            if (!currentUser) return;

            try {
                const formData = new URLSearchParams();
                formData.append('user_id', currentUser.id);
                formData.append('self_year', personAYear);
                formData.append('partner_year', personBYear);
                formData.append('score', result.score);
                formData.append('evaluation', result.danhGia);
                formData.append('detail', JSON.stringify(result));

                const response = await fetch('api/save_laman_history.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const data = await response.json();
                
                if (data.success) {
                    console.log('Đã lưu lịch sử làm ăn');
                    triggerDataUpdate();
                }
            } catch (error) {
                console.error('Lỗi khi lưu lịch sử:', error);
            }
        }

        function triggerDataUpdate() {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('lastDataUpdate', Date.now().toString());
            }
        }

        function checkBusinessCompatibility() {
            const personAYear = parseInt(document.getElementById('personAYear').value);
            const personBYear = parseInt(document.getElementById('personBYear').value);
            const businessYear = parseInt(document.getElementById('businessYear').value) || new Date().getFullYear();
            
            if (!personAYear || !personBYear) {
                showNotification('Vui lòng nhập năm sinh cả hai người!', 'error');
                return;
            }
            
            const [canA, chiA] = canChiOfYear(personAYear);
            const [canB, chiB] = canChiOfYear(personBYear);
            const [canBiz, chiBiz] = canChiOfYear(businessYear);
            
            const menhA = NAP_AM[`${canA} ${chiA}`]?.hanh || HANH_CAN[canA];
            const menhB = NAP_AM[`${canB} ${chiB}`]?.hanh || HANH_CAN[canB];
            const menhBiz = NAP_AM[`${canBiz} ${chiBiz}`]?.hanh || HANH_CAN[canBiz];
            
            let score = 0;
            let details = [];
            
            if (checkTuongSinh(menhA, menhB)) {
                score += 2;
                details.push("✅ Mệnh tương sinh: Hợp tác thuận lợi");
            } else if (checkTuongKhac(menhA, menhB)) {
                score -= 2;
                details.push("❌ Mệnh tương khắc: Dễ mâu thuẫn");
            } else {
                score += 1;
                details.push("⚠️ Mệnh bình hòa: Hợp tác ổn");
            }
            
            if (checkTamHop(chiA, chiB)) {
                score += 2;
                details.push("✅ Địa chi tam hợp: Đồng quan điểm");
            } else if (checkTuXung(chiA, chiB)) {
                score -= 1;
                details.push("❌ Địa chi xung khắc: Dễ bất đồng");
            }
            
            if (checkTuongSinh(menhA, menhBiz) && checkTuongSinh(menhB, menhBiz)) {
                score += 1;
                details.push("✅ Năm khởi sự hợp mệnh cả hai");
            }
            
            const danhGia = score >= 3 ? "RẤT TỐT" : score >= 1 ? "TỐT" : score >= -1 ? "BÌNH THƯỜNG" : "KHÔNG HỢP";
            
            currentBusinessResult = {
                personAYear: personAYear,
                personBYear: personBYear,
                businessYear: businessYear,
                canA: canA,
                chiA: chiA,
                menhA: menhA,
                canB: canB,
                chiB: chiB,
                menhB: menhB,
                canBiz: canBiz,
                chiBiz: chiBiz,
                menhBiz: menhBiz,
                score: score,
                danhGia: danhGia,
                details: details
            };
            
            saveBusinessHistory(personAYear, personBYear, businessYear, currentBusinessResult);
            
            document.getElementById('businessResult').innerHTML = `
                <div class="result">
                    <button id="favoriteBtn" class="favorite-btn" onclick="saveToFavorites()">
                        <span>⭐</span> Lưu tra cứu này
                    </button>
                    <h3>💰 Kết quả xem hợp tác làm ăn</h3>
                    <div class="info-grid">
                        <div class="info"><strong>👤 Người A</strong> ${personAYear} - ${canA} ${chiA} - ${menhA}</div>
                        <div class="info"><strong>👤 Người B</strong> ${personBYear} - ${canB} ${chiB} - ${menhB}</div>
                        <div class="info"><strong>📅 Năm khởi sự</strong> ${businessYear} - ${canBiz} ${chiBiz} - ${menhBiz}</div>
                    </div>
                    <div class="bar ${score >= 3 ? 'good' : score >= 1 ? 'neutral' : 'bad'}">
                        ${danhGia} - Điểm: ${score}
                    </div>
                    <div class="info">
                        <strong>📊 Đánh giá chi tiết:</strong><br>
                        ${details.join('<br>')}
                    </div>
                    <div class="info">
                        <strong>💡 Khuyến nghị:</strong><br>
                        ${score >= 3 ? 'Nên hợp tác, cơ hội thành công cao' : 
                          score >= 1 ? 'Có thể hợp tác nhưng cần thận trọng' : 
                          'Nên cân nhắc kỹ trước khi hợp tác'}
                    </div>
                </div>
            `;
        }

        function saveToFavorites() {
            if (!currentBusinessResult) {
                showNotification('Không có kết quả phân tích nào để lưu!', 'error');
                return;
            }

            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để lưu tra cứu!', 'error');
                showLoginModal();
                return;
            }

            const personAYear = document.getElementById('personAYear').value;
            const personBYear = document.getElementById('personBYear').value;
            const businessYear = document.getElementById('businessYear').value;
            
            const favorite = {
                id: Date.now(),
                title: `Hợp tác: ${currentBusinessResult.personAYear} & ${currentBusinessResult.personBYear}`,
                date: new Date().toLocaleString('vi-VN'),
                personAYear: currentBusinessResult.personAYear,
                personBYear: currentBusinessResult.personBYear,
                businessYear: currentBusinessResult.businessYear,
                score: currentBusinessResult.score,
                danhGia: currentBusinessResult.danhGia,
                details: currentBusinessResult.details
            };
            
            let favorites = JSON.parse(localStorage.getItem('businessFavorites')) || [];
            
            const existingIndex = favorites.findIndex(f => 
                f.personAYear === favorite.personAYear && 
                f.personBYear === favorite.personBYear
            );
            
            if (existingIndex !== -1) {
                favorites[existingIndex] = favorite;
            } else {
                favorites.push(favorite);
            }
            
            localStorage.setItem('businessFavorites', JSON.stringify(favorites));
            
            displayFavorites();
            
            showNotification('Đã lưu tra cứu vào danh sách yêu thích!', 'success');
        }

        function displayFavorites() {
            const favoritesList = document.getElementById('favoritesList');
            const favorites = JSON.parse(localStorage.getItem('businessFavorites')) || [];
            
            if (favorites.length === 0) {
                favoritesList.innerHTML = `
                    <div class="empty-favorites">
                        <p>Chưa có tra cứu nào được lưu</p>
                        <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                    </div>
                `;
                return;
            }
            
            favoritesList.innerHTML = favorites.map(favorite => `
                <div class="favorite-item">
                    <div class="favorite-item-header">
                        <div class="favorite-title">${favorite.title}</div>
                        <div class="favorite-date">${favorite.date}</div>
                    </div>
                    <div class="favorite-details">
                        <p><strong>Năm sinh:</strong> ${favorite.personAYear} & ${favorite.personBYear}</p>
                        <p><strong>Năm khởi sự:</strong> ${favorite.businessYear}</p>
                        <p><strong>Đánh giá:</strong> ${favorite.danhGia} (Điểm: ${favorite.score})</p>
                    </div>
                    <div class="favorite-actions">
                        <button class="btn-info" onclick="loadFavorite(${favorite.id})">👁️ Xem lại</button>
                        <button class="btn-danger" onclick="deleteFavorite(${favorite.id})">🗑️ Xóa</button>
                    </div>
                </div>
            `).join('');
        }

        function loadFavorite(id) {
            const favorites = JSON.parse(localStorage.getItem('businessFavorites')) || [];
            const favorite = favorites.find(f => f.id === id);
            
            if (!favorite) {
                showNotification('Không tìm thấy tra cứu đã lưu!', 'error');
                return;
            }
            
            document.getElementById('personAYear').value = favorite.personAYear;
            document.getElementById('personBYear').value = favorite.personBYear;
            document.getElementById('businessYear').value = favorite.businessYear;
            
            setTimeout(() => {
                checkBusinessCompatibility();
            }, 500);
            
            window.scrollTo(0, 0);
        }

        function deleteFavorite(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa tra cứu này khỏi danh sách yêu thích?')) {
                return;
            }
            
            let favorites = JSON.parse(localStorage.getItem('businessFavorites')) || [];
            favorites = favorites.filter(f => f.id !== id);
            localStorage.setItem('businessFavorites', JSON.stringify(favorites));
            
            displayFavorites();
            showNotification('Đã xóa tra cứu khỏi danh sách yêu thích!', 'success');
        }
    </script>
</body>
</html>
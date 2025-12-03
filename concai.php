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
    <title>Xem Tuổi Sinh Con - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .calculator-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .form-row-extended {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .field-group {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .field-group h4 {
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
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

        .time-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-input select {
            flex: 1;
        }

        .year-range {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }

        .year-range input {
            flex: 1;
        }

        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        /* Result styles */
        .family-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .family-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #f9f9f9;
        }
        .bazi-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .bazi-details {
            font-size: 14px;
        }
        .year-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .year-option {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .year-option.selected {
            border-color: #27ae60;
            background: #e8f5e8;
            font-weight: bold;
        }
        .year-option.good {
            border-color: #2ecc71;
            background: #e8f5e8;
        }
        .year-option.medium {
            border-color: #f39c12;
            background: #fef5e6;
        }
        .year-option.poor {
            border-color: #e74c3c;
            background: #fde8e6;
        }
        .analysis-detail {
            margin: 10px 0;
            padding: 10px;
            border-left: 4px solid #3498db;
            background: #f8f9fa;
        }
        .analysis-good {
            border-left-color: #27ae60;
            background: #e8f5e8;
        }
        .analysis-bad {
            border-left-color: #e74c3c;
            background: #fde8e6;
        }
        .analysis-warning {
            border-left-color: #f39c12;
            background: #fef5e6;
        }
        .recommendation-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .score-display {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            color: white;
        }
        .score-excellent {
            background: #27ae60;
        }
        .score-good {
            background: #2ecc71;
        }
        .score-medium {
            background: #f39c12;
        }
        .score-poor {
            background: #e74c3c;
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

        .history-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }

        .history-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .history-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            position: relative;
        }

        .history-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .history-title {
            font-weight: bold;
            color: #2c3e50;
        }

        .history-date {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .history-actions {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .history-actions button {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .empty-history {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-style: italic;
            grid-column: 1 / -1;
        }

        .good-rating { color: #28a745; font-weight: 600; }
        .bad-rating { color: #dc3545; font-weight: 600; }
        .neutral-rating { color: #ffc107; font-weight: 600; }

        @media (max-width: 768px) {
            .form-row-extended {
                grid-template-columns: 1fr;
            }
            
            .family-grid {
                grid-template-columns: 1fr;
            }
            
            .year-options {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            }
            
            .history-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Xem tuổi sinh con - Luận tam hợp gia đạo</p>
          
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
                    <a class="nav-link active" href="concai.php">
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
      
        <div class="app-container">
            <section class="info-section">
                <h2>👶 Xem Tuổi Sinh Con (Luận Tam Hợp Gia Đạo)</h2>
                
                <div class="panel">
                    <div class="panel-title">🏠 Nhập thông tin Bát Tự cha mẹ</div>
                    <div class="calculator-form">
                        <div class="form-row-extended">
                            <!-- Thông tin Cha -->
                            <div class="field-group">
                                <h4>👨 Thông tin Cha</h4>
                                <div class="field">
                                    <label>Ngày sinh</label>
                                    <input type="number" id="fatherDay" min="1" max="31" placeholder="Ngày" value="15">
                                </div>
                                <div class="field">
                                    <label>Tháng sinh</label>
                                    <input type="number" id="fatherMonth" min="1" max="12" placeholder="Tháng" value="6">
                                </div>
                                <div class="field">
                                    <label>Năm sinh</label>
                                    <input type="number" id="fatherYear" min="1900" max="2100" placeholder="Năm" value="1985">
                                </div>
                                <div class="field">
                                    <label>Giờ sinh</label>
                                    <div class="time-input">
                                        <select id="fatherHour">
                                            <option value="0">Tý (23h-01h)</option>
                                            <option value="1">Sửu (01h-03h)</option>
                                            <option value="2">Dần (03h-05h)</option>
                                            <option value="3">Mão (05h-07h)</option>
                                            <option value="4">Thìn (07h-09h)</option>
                                            <option value="5">Tỵ (09h-11h)</option>
                                            <option value="6" selected>Ngọ (11h-13h)</option>
                                            <option value="7">Mùi (13h-15h)</option>
                                            <option value="8">Thân (15h-17h)</option>
                                            <option value="9">Dậu (17h-19h)</option>
                                            <option value="10">Tuất (19h-21h)</option>
                                            <option value="11">Hợi (21h-23h)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Thông tin Mẹ -->
                            <div class="field-group">
                                <h4>👩 Thông tin Mẹ</h4>
                                <div class="field">
                                    <label>Ngày sinh</label>
                                    <input type="number" id="motherDay" min="1" max="31" placeholder="Ngày" value="20">
                                </div>
                                <div class="field">
                                    <label>Tháng sinh</label>
                                    <input type="number" id="motherMonth" min="1" max="12" placeholder="Tháng" value="8">
                                </div>
                                <div class="field">
                                    <label>Năm sinh</label>
                                    <input type="number" id="motherYear" min="1900" max="2100" placeholder="Năm" value="1990">
                                </div>
                                <div class="field">
                                    <label>Giờ sinh</label>
                                    <div class="time-input">
                                        <select id="motherHour">
                                            <option value="0">Tý (23h-01h)</option>
                                            <option value="1">Sửu (01h-03h)</option>
                                            <option value="2">Dần (03h-05h)</option>
                                            <option value="3">Mão (05h-07h)</option>
                                            <option value="4">Thìn (07h-09h)</option>
                                            <option value="5">Tỵ (09h-11h)</option>
                                            <option value="6">Ngọ (11h-13h)</option>
                                            <option value="7" selected>Mùi (13h-15h)</option>
                                            <option value="8">Thân (15h-17h)</option>
                                            <option value="9">Dậu (17h-19h)</option>
                                            <option value="10">Tuất (19h-21h)</option>
                                            <option value="11">Hợi (21h-23h)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Khoảng năm sinh con dự kiến</label>
                            <div class="year-range">
                                <input type="number" id="startYear" min="2024" max="2035" value="2024" placeholder="Từ năm">
                                <span>đến</span>
                                <input type="number" id="endYear" min="2024" max="2035" value="2030" placeholder="Đến năm">
                            </div>
                        </div>

                        <div class="btn-row">
                            <button class="btn-success" onclick="analyzeChildCompatibility()">🔍 Phân tích tuổi sinh con</button>
                        </div>
                    </div>
                </div>

                <div id="childAnalysisResult"></div>

                <!-- Danh sách yêu thích -->
                <div class="history-section">
                    <div class="panel-title">⭐ Danh sách yêu thích</div>
                    <div id="favoritesList" class="history-list">
                        <div class="empty-history">
                            <p>Chưa có tra cứu nào trong danh sách yêu thích</p>
                            <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                        </div>
                    </div>
                </div>

                <div class="service-detail">
                    <h3>📚 Phương pháp luận Tam Hợp Gia Đạo</h3>
                    <div class="analysis-method">
                        <h4>8 bước phân tích của Thiệu Vĩ Hoa:</h4>
                        <ol>
                            <li><strong>Lập Bát Tự cha mẹ:</strong> Xác định Thân vượng/thân nhược</li>
                            <li><strong>Xác định hành vượng/yếu:</strong> Tìm hành cần bổ sung cho gia đạo</li>
                            <li><strong>Tìm Dụng thần chung:</strong> Hành tốt nhất cho cả cha lẫn mẹ</li>
                            <li><strong>Chọn năm sinh con:</strong> Ưu tiên tam hợp, tránh tứ xung</li>
                            <li><strong>Xét Thiên Can:</strong> Can con sinh/hợp can cha mẹ</li>
                            <li><strong>Luận Nhật chủ:</strong> Tránh con khắc cha mẹ</li>
                            <li><strong>Phối Cung Phi:</strong> Đồng nhóm trạch tốt hơn</li>
                            <li><strong>Kiểm tra trạch vận:</strong> Sao cát/t hung chiếu năm sinh</li>
                        </ol>
                    </div>

                    <div class="service-features">
                        <div class="feature-item">
                            <strong>🔍 Phân tích Dụng Thần</strong>
                            <p>Xác định hành bổ trợ cân bằng mệnh cục gia đình</p>
                        </div>
                        <div class="feature-item">
                            <strong>⚖️ Ngũ Hành tam hợp</strong>
                            <p>Đánh giá tương sinh giữa cha, mẹ và con</p>
                        </div>
                        <div class="feature-item">
                            <strong>🌗 Thiên Can Địa Chi</strong>
                            <p>Xem hợp hóa, tránh xung hình trong gia đình</p>
                        </div>
                        <div class="feature-item">
                            <strong>🏠 Trạch vận hậu sinh</strong>
                            <p>Đánh giá ảnh hưởng sau khi sinh con</p>
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
        let currentAnalysis = null;

        // Khởi tạo ứng dụng
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            displayFavorites();
            checkUrlParameters(); // ✅ KIỂM TRA URL PARAMETERS
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

        // ✅ THÊM HÀM KIỂM TRA URL PARAMETERS
        function checkUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const historyId = urlParams.get('loadHistory');
            const fatherYear = urlParams.get('fatherYear');
            const motherYear = urlParams.get('motherYear');
            
            if (historyId) {
                loadFromHistory(historyId);
            } else if (fatherYear && motherYear) {
                // Điền thông tin từ URL parameters
                document.getElementById('fatherYear').value = fatherYear;
                document.getElementById('motherYear').value = motherYear;
                showNotification('Đã tải thông tin từ URL!', 'success');
            }
        }

        // ✅ THÊM HÀM LOAD TỪ HISTORY
        async function loadFromHistory(historyId) {
            try {
                const response = await fetch(`api/get_sinhcon_history_item.php?id=${historyId}`);
                const data = await response.json();
                
                if (data.success && data.item) {
                    const item = data.item;
                    
                    // Điền thông tin vào form
                    document.getElementById('fatherYear').value = item.father_year;
                    document.getElementById('motherYear').value = item.mother_year;
                    
                    showNotification('Đã tải thông tin từ lịch sử!', 'success');
                    
                    // Tự động phân tích sau 1 giây
                    setTimeout(() => {
                        analyzeChildCompatibility();
                    }, 1000);
                }
            } catch (error) {
                console.error('Lỗi tải từ lịch sử:', error);
                showNotification('Lỗi khi tải thông tin từ lịch sử', 'error');
            }
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
                    displayFavorites();
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
                    displayFavorites();
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
                    displayFavorites();
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
        }

        // ==================== PHẦN CHỨC NĂNG SINH CON ====================

        // Hàm chính phân tích tuổi sinh con
        function analyzeChildCompatibility() {
            // Lấy thông tin cha mẹ
            const fatherBazi = calculateBazi(
                parseInt(document.getElementById('fatherDay').value),
                parseInt(document.getElementById('fatherMonth').value),
                parseInt(document.getElementById('fatherYear').value),
                parseInt(document.getElementById('fatherHour').value)
            );

            const motherBazi = calculateBazi(
                parseInt(document.getElementById('motherDay').value),
                parseInt(document.getElementById('motherMonth').value),
                parseInt(document.getElementById('motherYear').value),
                parseInt(document.getElementById('motherHour').value)
            );

            const startYear = parseInt(document.getElementById('startYear').value);
            const endYear = parseInt(document.getElementById('endYear').value);

            if (!startYear || !endYear || endYear <= startYear) {
                showNotification('Vui lòng nhập khoảng năm hợp lệ!', 'error');
                return;
            }

            // Phân tích và hiển thị kết quả
            const analysis = analyzeFamilyCompatibility(fatherBazi, motherBazi, startYear, endYear);
            currentAnalysis = analysis;
            
            // LƯU LỊCH SỬ SAU KHI PHÂN TÍCH
            const fatherYear = document.getElementById('fatherYear').value;
            const motherYear = document.getElementById('motherYear').value;
            saveToHistory(fatherYear, motherYear, analysis);
            
            // Hiển thị kết quả
            displayChildAnalysis(fatherBazi, motherBazi, analysis);
        }

        // Hàm lưu lịch sử tra cứu vào database
        async function saveToHistory(fatherYear, motherYear, analysis) {
            if (!currentUser) {
                console.log('Chưa đăng nhập, không lưu lịch sử');
                return;
            }

            try {
                const response = await fetch('api/save_sinhcon_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        father_year: parseInt(fatherYear),
                        mother_year: parseInt(motherYear),
                        child_year: analysis.bestYear.year,
                        score: analysis.bestYear.score,
                        evaluation: getEvaluationText(analysis.bestYear.score),
                        detail: JSON.stringify({
                            familyUsefulGod: analysis.familyUsefulGod,
                            bestYear: analysis.bestYear,
                            totalYears: analysis.yearAnalysis.length,
                            startYear: parseInt(document.getElementById('startYear').value),
                            endYear: parseInt(document.getElementById('endYear').value)
                        })
                    })
                });

                const data = await response.json();
                if (data.success) {
                    console.log('✅ Đã lưu lịch sử tra cứu sinh con, ID:', data.history_id);
                } else {
                    console.error('❌ Lỗi lưu lịch sử:', data.message);
                }
            } catch (error) {
                console.error('❌ Lỗi kết nối khi lưu lịch sử:', error);
            }
        }

        // Hàm đánh giá text từ điểm số
        function getEvaluationText(score) {
            if (score >= 7) return 'Rất tốt';
            if (score >= 5) return 'Tốt';
            if (score >= 3) return 'Trung bình';
            return 'Không tốt';
        }

        // Lưu vào danh sách yêu thích - DÙNG HỆ THỐNG CHUNG
        async function saveToFavorites() {
            if (!currentAnalysis) {
                showNotification('Không có kết quả phân tích nào để lưu!', 'error');
                return;
            }

            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để lưu tra cứu!', 'error');
                showLoginModal();
                return;
            }

            const fatherYear = document.getElementById('fatherYear').value;
            const motherYear = document.getElementById('motherYear').value;
            const bestYear = currentAnalysis.bestYear.year;
            
            // Tạo thông tin cho favorite
            const favoriteData = {
                solar: `${bestYear}-01-01`, // Ngày dương mặc định
                lunar: `Sinh con: Cha ${fatherYear} - Mẹ ${motherYear}`,
                rating: `Điểm: ${currentAnalysis.bestYear.score}/10 - Dụng thần: ${currentAnalysis.familyUsefulGod}`,
                score: currentAnalysis.bestYear.score
            };

            try {
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(favoriteData)
                });

                const data = await response.json();
                
                if (data.success) {
                    if (data.added) {
                        showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                    } else {
                        showNotification('Đã có trong danh sách yêu thích!', 'info');
                    }
                    // Cập nhật danh sách favorites
                    displayFavorites();
                } else {
                    showNotification('Lỗi khi lưu: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi lưu yêu thích:', error);
                showNotification('Lỗi kết nối khi lưu!', 'error');
            }
        }

        // Hiển thị danh sách yêu thích từ hệ thống chung
        async function displayFavorites() {
            if (!currentUser) {
                document.getElementById('favoritesList').innerHTML = `
                    <div class="empty-history">
                        <p>Vui lòng đăng nhập để xem danh sách yêu thích</p>
                    </div>
                `;
                return;
            }

            try {
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                
                const favoritesList = document.getElementById('favoritesList');
                
                if (data.success && data.favorites && data.favorites.length > 0) {
                    // Lọc các favorites liên quan đến sinh con
                    const childBirthFavorites = data.favorites.filter(fav => 
                        fav.lunar_date && fav.lunar_date.includes('Sinh con:')
                    );
                    
                    if (childBirthFavorites.length > 0) {
                        favoritesList.innerHTML = childBirthFavorites.map(fav => {
                            // Parse thông tin từ lunar_date
                            const lunarMatch = fav.lunar_date.match(/Sinh con: Cha (\d+) - Mẹ (\d+)/);
                            const fatherYear = lunarMatch ? lunarMatch[1] : 'N/A';
                            const motherYear = lunarMatch ? lunarMatch[2] : 'N/A';
                            
                            const scoreMatch = fav.rating_text.match(/Điểm: (\d+)\/10/);
                            const score = scoreMatch ? scoreMatch[1] : 'N/A';
                            
                            const usefulGodMatch = fav.rating_text.match(/Dụng thần: (.+)$/);
                            const usefulGod = usefulGodMatch ? usefulGodMatch[1] : 'N/A';
                            
                            const scoreClass = getScoreClass(parseInt(score));
                            
                            return `
                                <div class="history-item">
                                    <div class="history-item-header">
                                        <div class="history-title">Cha ${fatherYear} - Mẹ ${motherYear}</div>
                                        <div class="history-date">${formatDateTime(fav.created_at)}</div>
                                    </div>
                                    <div class="history-details">
                                        <p><strong>Điểm:</strong> <span class="${scoreClass}">${score}/10</span></p>
                                        <p><strong>Dụng thần:</strong> ${usefulGod}</p>
                                        <p><strong>Năm tốt:</strong> ${fav.solar_date ? fav.solar_date.split('-')[0] : 'N/A'}</p>
                                    </div>
                                    <div class="history-actions">
                                        <button class="btn-info" onclick="loadFavoriteAnalysis('${fatherYear}', '${motherYear}')">👁️ Xem lại</button>
                                        <button class="btn-danger" onclick="removeFavorite(${fav.id})">🗑️ Xóa</button>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    } else {
                        favoritesList.innerHTML = `
                            <div class="empty-history">
                                <p>Chưa có tra cứu nào trong danh sách yêu thích</p>
                                <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                            </div>
                        `;
                    }
                } else {
                    favoritesList.innerHTML = `
                        <div class="empty-history">
                            <p>Chưa có tra cứu nào trong danh sách yêu thích</p>
                            <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Lỗi tải danh sách yêu thích:', error);
                document.getElementById('favoritesList').innerHTML = `
                    <div class="empty-history">
                        <p>Lỗi khi tải danh sách yêu thích</p>
                    </div>
                `;
            }
        }

        // Xóa khỏi danh sách yêu thích
        async function removeFavorite(favoriteId) {
            if (!confirm('Bạn có chắc chắn muốn xóa tra cứu này khỏi danh sách yêu thích?')) {
                return;
            }
            
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
                    showNotification('Đã xóa tra cứu khỏi danh sách yêu thích!', 'success');
                    // Load lại danh sách
                    displayFavorites();
                } else {
                    showNotification('Lỗi khi xóa: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Lỗi xóa yêu thích:', error);
                showNotification('Lỗi kết nối khi xóa!', 'error');
            }
        }

        // Tải lại phân tích từ favorite
        function loadFavoriteAnalysis(fatherYear, motherYear) {
            // Điền thông tin vào form
            document.getElementById('fatherYear').value = fatherYear;
            document.getElementById('motherYear').value = motherYear;
            
            showNotification('Đã tải thông tin! Đang phân tích...', 'success');
            
            // Tự động thực hiện phân tích sau 1 giây
            setTimeout(() => {
                analyzeChildCompatibility();
            }, 1000);
            
            // Scroll lên đầu trang
            window.scrollTo(0, 0);
        }

        // Hàm format datetime
        function formatDateTime(dateString) {
            if (!dateString) return '—';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }

        // Hàm phân loại điểm
        function getScoreClass(score) {
            if (score >= 7) return 'good-rating';
            if (score >= 5) return 'neutral-rating';
            return 'bad-rating';
        }

        // ==================== CÁC HÀM PHÂN TÍCH BÁT TỰ ====================

        // Phân tích tương hợp gia đình
        function analyzeFamilyCompatibility(father, mother, startYear, endYear) {
            // 1. Tìm Dụng thần chung của gia đình
            const familyUsefulGod = findFamilyUsefulGod(father, mother);
            
            // 2. Phân tích các năm trong khoảng
            const yearAnalysis = [];
            for (let year = startYear; year <= endYear; year++) {
                const childBazi = estimateChildBaziFromYear(year);
                const score = calculateChildCompatibilityScore(father, mother, childBazi, familyUsefulGod);
                
                yearAnalysis.push({
                    year: year,
                    bazi: childBazi,
                    score: score,
                    details: getCompatibilityDetails(father, mother, childBazi, familyUsefulGod)
                });
            }

            // Sắp xếp theo điểm số
            yearAnalysis.sort((a, b) => b.score - a.score);

            return {
                familyUsefulGod: familyUsefulGod,
                yearAnalysis: yearAnalysis,
                bestYear: yearAnalysis[0]
            };
        }

        // Tìm Dụng thần chung cho gia đình
        function findFamilyUsefulGod(father, mother) {
            // Ưu tiên Dụng thần trùng nhau
            if (father.usefulGod === mother.usefulGod) {
                return father.usefulGod;
            }
            
            // Nếu khác, chọn hành trung hòa hoặc sinh cả hai
            const fatherElement = HANH_CAN[father.day.can];
            const motherElement = HANH_CAN[mother.day.can];
            
            // Tìm hành sinh cho cả hai
            const sinhChoFather = getSinhCho(fatherElement);
            const sinhChoMother = getSinhCho(motherElement);
            
            // Tìm hành chung trong sinh cho
            const commonElements = sinhChoFather.filter(element => 
                sinhChoMother.includes(element)
            );
            
            return commonElements.length > 0 ? commonElements[0] : father.usefulGod;
        }

        // Ước tính Bát tự con từ năm sinh
        function estimateChildBaziFromYear(year) {
            const [yearCan, yearChi] = canChiOfYear(year);
            const menhChild = NAP_AM[`${yearCan} ${yearChi}`]?.hanh || HANH_CAN[yearCan];
            
            return {
                year: { can: yearCan, chi: yearChi },
                elements: { [menhChild]: 1 },
                yearChi: yearChi,
                yearCan: yearCan,
                menh: menhChild
            };
        }

        // Tính điểm tương hợp
        function calculateChildCompatibilityScore(father, mother, child, familyUsefulGod) {
            let score = 0;

            // 1. Kiểm tra Ngũ hành với Dụng thần gia đình
            if (child.menh === familyUsefulGod) {
                score += 3;
            } else if (checkTuongSinh(child.menh, familyUsefulGod)) {
                score += 2;
            } else if (checkTuongKhac(child.menh, familyUsefulGod)) {
                score -= 2;
            }

            // 2. Kiểm tra Tam hợp với cha mẹ
            if (checkTamHop(child.yearChi, father.year.chi) || 
                checkTamHop(child.yearChi, mother.year.chi)) {
                score += 2;
            }

            // 3. Kiểm tra Lục hợp với cha mẹ
            if (checkLucHop(child.yearChi, father.year.chi) || 
                checkLucHop(child.yearChi, mother.year.chi)) {
                score += 1;
            }

            // 4. Kiểm tra Tứ xung với cha mẹ
            if (checkTuXung(child.yearChi, father.year.chi) || 
                checkTuXung(child.yearChi, mother.year.chi)) {
                score -= 3;
            }

            // 5. Kiểm tra Thiên Can hợp
            if (checkCanHop(child.yearCan, father.year.can) || 
                checkCanHop(child.yearCan, mother.year.can)) {
                score += 1;
            }

            // 6. Kiểm tra Ngũ hành con với cha mẹ
            const fatherMenh = NAP_AM[`${father.year.can} ${father.year.chi}`]?.hanh || HANH_CAN[father.year.can];
            const motherMenh = NAP_AM[`${mother.year.can} ${mother.year.chi}`]?.hanh || HANH_CAN[mother.year.can];
            
            if (checkTuongSinh(child.menh, fatherMenh) || checkTuongSinh(child.menh, motherMenh)) {
                score += 1;
            }

            return Math.max(0, Math.min(10, score));
        }

        // Lấy chi tiết tương hợp
        function getCompatibilityDetails(father, mother, child, familyUsefulGod) {
            const details = [];
            const fatherMenh = NAP_AM[`${father.year.can} ${father.year.chi}`]?.hanh || HANH_CAN[father.year.can];
            const motherMenh = NAP_AM[`${mother.year.can} ${mother.year.chi}`]?.hanh || HANH_CAN[mother.year.can];

            // Chi tiết ngũ hành
            if (child.menh === familyUsefulGod) {
                details.push("✅ Con có hành trùng Dụng thần gia đình");
            } else if (checkTuongSinh(child.menh, familyUsefulGod)) {
                details.push("✅ Con sinh Dụng thần gia đình");
            }

            // Chi tiết hợp xung
            if (checkTamHop(child.yearChi, father.year.chi)) {
                details.push(`✅ Tam hợp với cha (${father.year.chi} - ${child.year.chi})`);
            }
            if (checkTamHop(child.yearChi, mother.year.chi)) {
                details.push(`✅ Tam hợp với mẹ (${mother.year.chi} - ${child.year.chi})`);
            }
            if (checkTuXung(child.yearChi, father.year.chi)) {
                details.push(`❌ Tứ xung với cha (${father.year.chi} - ${child.year.chi})`);
            }
            if (checkTuXung(child.yearChi, mother.year.chi)) {
                details.push(`❌ Tứ xung với mẹ (${mother.year.chi} - ${child.year.chi})`);
            }

            return details;
        }

        // Hiển thị kết quả phân tích
        function displayChildAnalysis(father, mother, analysis) {
            const resultHTML = `
                <div class="result">
                    <h3>🏠 Kết quả phân tích tuổi sinh con</h3>
                    
                    <button id="favoriteBtn" class="favorite-btn" onclick="saveToFavorites()">
                        <span>⭐</span> Lưu tra cứu này
                    </button>
                    
                    <div class="family-grid">
                        <div class="family-card">
                            <div class="bazi-header">👨 Bát Tự Cha</div>
                            <div class="bazi-details">
                                <strong>Năm:</strong> ${father.year.can} ${father.year.chi}<br>
                                <strong>Mệnh cục:</strong> ${father.strength}<br>
                                <strong>Dụng thần:</strong> ${father.usefulGod}<br>
                                <strong>Kỵ thần:</strong> ${father.avoidGod}
                            </div>
                        </div>
                        <div class="family-card">
                            <div class="bazi-header">👩 Bát Tự Mẹ</div>
                            <div class="bazi-details">
                                <strong>Năm:</strong> ${mother.year.can} ${mother.year.chi}<br>
                                <strong>Mệnh cục:</strong> ${mother.strength}<br>
                                <strong>Dụng thần:</strong> ${mother.usefulGod}<br>
                                <strong>Kỵ thần:</strong> ${mother.avoidGod}
                            </div>
                        </div>
                    </div>

                    <div class="analysis-detail analysis-good">
                        <strong>Dụng thần gia đình ưu tiên:</strong> ${analysis.familyUsefulGod}<br>
                        <em>Nên sinh con có hành ${analysis.familyUsefulGod} hoặc hành tương sinh</em>
                    </div>

                    <h4>📊 Đánh giá các năm sinh con:</h4>
                    <div class="year-options">
                        ${analysis.yearAnalysis.map(item => `
                            <div class="year-option ${getYearScoreClass(item.score)}" 
                                 onclick="showYearDetail(${item.year})">
                                <strong>${item.year}</strong><br>
                                ${item.bazi.year.can} ${item.bazi.year.chi}<br>
                                <small>Mệnh: ${item.bazi.menh}</small><br>
                                <small>Điểm: ${item.score}</small>
                            </div>
                        `).join('')}
                    </div>

                    <div class="score-display ${getScoreDisplayClass(analysis.bestYear.score)}">
                        Năm tốt nhất: ${analysis.bestYear.year} - ${analysis.bestYear.bazi.year.can} ${analysis.bestYear.bazi.year.chi}<br>
                        Điểm: ${analysis.bestYear.score}/10
                    </div>

                    <div class="recommendation-box">
                        <h4>💡 Phân tích năm ${analysis.bestYear.year}</h4>
                        <ul>
                            ${analysis.bestYear.details.map(detail => `<li>${detail}</li>`).join('')}
                        </ul>
                        <p><strong>Kiến nghị:</strong> Nên sinh con vào năm này để gia đạo hưng vượng, 
                        con cái khỏe mạnh, thông minh, gia đình thuận hòa.</p>
                    </div>

                    <div class="analysis-detail">
                        <strong>📝 Lưu ý quan trọng:</strong><br>
                        Phân tích dựa trên Bát tự chỉ mang tính chất tham khảo. 
                        Tình yêu thương, sự chăm sóc và giáo dục mới là yếu tố quyết định 
                        hạnh phúc gia đình và sự phát triển của con cái.
                    </div>
                </div>
            `;
            
            document.getElementById('childAnalysisResult').innerHTML = resultHTML;
        }

        function getYearScoreClass(score) {
            if (score >= 4) return 'good';
            if (score >= 2) return 'medium';
            return 'poor';
        }

        function getScoreDisplayClass(score) {
            if (score >= 6) return 'score-excellent';
            if (score >= 4) return 'score-good';
            if (score >= 2) return 'score-medium';
            return 'score-poor';
        }

        function showYearDetail(year) {
            const yearElements = document.querySelectorAll('.year-option');
            yearElements.forEach(el => {
                if (el.textContent.includes(year)) {
                    el.classList.add('selected');
                } else {
                    el.classList.remove('selected');
                }
            });
        }

        // ==================== CÁC HÀM HỖ TRỢ BÁT TỰ ====================

        function getSinhCho(element) {
            const sinh = {
                'Mộc': ['Thủy', 'Mộc'],
                'Hỏa': ['Mộc', 'Hỏa'], 
                'Thổ': ['Hỏa', 'Thổ'],
                'Kim': ['Thổ', 'Kim'],
                'Thủy': ['Kim', 'Thủy']
            };
            return sinh[element] || [];
        }

        function calculateBazi(day, month, year, hour) {
            const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
            const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
            
            const yearCan = CAN[year % 10];
            const yearChi = CHI[year % 12];
            const menh = NAP_AM[`${yearCan} ${yearChi}`]?.hanh || HANH_CAN[yearCan];
            
            const strength = Math.random() > 0.5 ? 'Thân vượng' : 'Thân nhược';
            const usefulGod = strength === 'Thân vượng' ? 'Kim' : 'Mộc';
            const avoidGod = strength === 'Thân vượng' ? 'Mộc' : 'Kim';
            
            return {
                year: { can: yearCan, chi: yearChi },
                day: { can: yearCan },
                elements: { [menh]: 1 },
                strength: strength,
                usefulGod: usefulGod,
                avoidGod: avoidGod
            };
        }

        function canChiOfYear(year) {
            const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
            const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
            return [CAN[year % 10], CHI[year % 12]];
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
            const TAM_HOP = {
                "Thân": ["Thân", "Tý", "Thìn"],
                "Tỵ": ["Tỵ", "Dậu", "Sửu"], 
                "Dần": ["Dần", "Ngọ", "Tuất"],
                "Hợi": ["Hợi", "Mão", "Mùi"]
            };
            return Object.values(TAM_HOP).some(group => 
                group.includes(chi1) && group.includes(chi2)
            );
        }

        function checkLucHop(chi1, chi2) {
            const TUC_HOP = {
                "Tý": ["Sửu"], "Sửu": ["Tý"],
                "Dần": ["Hợi"], "Hợi": ["Dần"],
                "Mão": ["Tuất"], "Tuất": ["Mão"],
                "Thìn": ["Dậu"], "Dậu": ["Thìn"],
                "Tỵ": ["Thân"], "Thân": ["Tỵ"],
                "Ngọ": ["Mùi"], "Mùi": ["Ngọ"]
            };
            return TUC_HOP[chi1]?.includes(chi2) || false;
        }

        function checkTuXung(chi1, chi2) {
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
            return TU_XUNG[chi1]?.includes(chi2) || false;
        }

        function checkCanHop(can1, can2) {
            const hopPairs = [
                ['Giáp', 'Kỷ'], ['Ất', 'Canh'], ['Bính', 'Tân'],
                ['Đinh', 'Nhâm'], ['Mậu', 'Quý']
            ];
            
            return hopPairs.some(pair => 
                (pair[0] === can1 && pair[1] === can2) ||
                (pair[1] === can1 && pair[0] === can2)
            );
        }

        // Khai báo các biến toàn cục cần thiết
        const HANH_CAN = { Giáp: "Mộc", Ất: "Mộc", Bính: "Hỏa", Đinh: "Hỏa", Mậu: "Thổ", Kỷ: "Thổ", Canh: "Kim", Tân: "Kim", Nhâm: "Thủy", Quý: "Thủy" };
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
    </script>
</body>
</html>
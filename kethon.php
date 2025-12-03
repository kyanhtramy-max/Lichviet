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
    <title>Xem Tuổi Kết Hôn - Lịch Việt</title>
    <link rel="stylesheet" href="css.css">
    <style>
        .bazi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .bazi-card {
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
        .analysis-section {
            margin: 15px 0;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .analysis-item {
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
        .score-display {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
        }
        .score-excellent {
            background: #27ae60;
            color: white;
        }
        .score-good {
            background: #2ecc71;
            color: white;
        }
        .score-medium {
            background: #f39c12;
            color: white;
        }
        .score-poor {
            background: #e74c3c;
            color: white;
        }
        .recommendation-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        /* Styles cho phần gợi ý ngày kết hôn */
        .wedding-date-suggestions {
            margin-top: 20px;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }
       
        .date-range-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
       
        .date-range-selector .field {
            margin-bottom: 10px;
        }
       
        .suggested-dates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
       
        .date-card {
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 15px;
            background: #fff;
            transition: all 0.3s ease;
            position: relative;
        }
       
        .date-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,.1);
        }
       
        .date-card.good {
            border-left: 4px solid #27ae60;
        }
       
        .date-card.excellent {
            border-left: 4px solid #3498db;
        }
       
        .date-card.perfect {
            border-left: 4px solid #9b59b6;
        }
       
        .date-header {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .date-score {
            background: #667eea;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
       
        .date-details {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 10px;
        }
       
        .date-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
       
        .favorite-btn {
            background: transparent;
            color: #ccc;
            border: 1px solid #ccc;
            padding: 4px 8px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }
       
        .favorite-btn:hover {
            color: #e74c3c;
            border-color: #e74c3c;
        }
       
        .favorite-btn.active {
            color: #e74c3c;
            border-color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }
       
        .saved-favorites {
            margin-top: 30px;
        }
       
        .favorites-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
       
        .empty-favorites {
            text-align: center;
            padding: 30px;
            color: #777;
            font-style: italic;
        }
       
        .date-badges {
            display: flex;
            gap: 5px;
            margin-top: 8px;
        }
       
        .date-badge {
            background: #f0f5ff;
            color: #667eea;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
        }
        /* Nút yêu thích cho kết quả phân tích */
        .favorite-analysis-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #ccc;
            transition: all 0.3s ease;
            padding: 5px;
        }
       
        .favorite-analysis-btn:hover {
            color: #e74c3c;
            transform: scale(1.1);
        }
       
        .favorite-analysis-btn.active {
            color: #e74c3c;
        }
       
        .saved-analyses {
            margin-top: 30px;
        }
       
        .saved-analysis-item {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            border-left: 4px solid #667eea;
            position: relative;
        }
       
        .saved-analysis-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
       
        .saved-analysis-title {
            font-weight: bold;
            font-size: 1.1rem;
        }
       
        .saved-analysis-date {
            font-size: 0.8rem;
            color: #777;
        }
       
        .saved-analysis-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
       
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        /* Form styles */
        .calculator-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-row-extended {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        .field-group {
            margin-bottom: 20px;
        }
        .field-group h4 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 1.2rem;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }
        .field label {
            font-weight: 600;
            color: #555;
            font-size: 0.95rem;
        }
        .field input, .field select {
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: .2s;
            background: #fff;
        }
        .field input:focus, .field select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102,126,234,.1);
        }
        .time-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .time-input select {
            flex: 1;
        }
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: center;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .analysis-info {
            background: #e3f2fd !important;
            border-left-color: #2196f3 !important;
        }
        @media (max-width: 768px) {
            .form-row-extended {
                grid-template-columns: 1fr;
            }
           
            .date-range-selector {
                grid-template-columns: 1fr;
            }
           
            .suggested-dates-grid {
                grid-template-columns: 1fr;
            }
           
            .bazi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Xem hợp tuổi kết hôn theo Bát Tự</p>
         
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
                    <a class="nav-link active" href="kethon.php">
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
     
        <div class="app-container">
            <section class="info-section">
                <h2>💑 Xem Tuổi Kết Hôn (Hợp Hôn Bát Tự)</h2>
               
                <div class="panel">
                    <div class="panel-title">📊 Nhập thông tin Bát Tự hai người</div>
                    <div class="calculator-form">
                        <div class="form-row-extended">
                            <!-- Thông tin Nam -->
                            <div class="field-group">
                                <h4>👨 Thông tin Nam</h4>
                                <div class="field">
                                    <label>Ngày sinh</label>
                                    <input type="number" id="husbandDay" min="1" max="31" placeholder="Ngày" value="18">
                                </div>
                                <div class="field">
                                    <label>Tháng sinh</label>
                                    <input type="number" id="husbandMonth" min="1" max="12" placeholder="Tháng" value="3">
                                </div>
                                <div class="field">
                                    <label>Năm sinh</label>
                                    <input type="number" id="husbandYear" min="1900" max="2100" placeholder="Năm" value="1996">
                                </div>
                                <div class="field">
                                    <label>Giờ sinh</label>
                                    <div class="time-input">
                                        <select id="husbandHour">
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
                            <!-- Thông tin Nữ -->
                            <div class="field-group">
                                <h4>👩 Thông tin Nữ</h4>
                                <div class="field">
                                    <label>Ngày sinh</label>
                                    <input type="number" id="wifeDay" min="1" max="31" placeholder="Ngày" value="15">
                                </div>
                                <div class="field">
                                    <label>Tháng sinh</label>
                                    <input type="number" id="wifeMonth" min="1" max="12" placeholder="Tháng" value="8">
                                </div>
                                <div class="field">
                                    <label>Năm sinh</label>
                                    <input type="number" id="wifeYear" min="1900" max="2100" placeholder="Năm" value="1998">
                                </div>
                                <div class="field">
                                    <label>Giờ sinh</label>
                                    <div class="time-input">
                                        <select id="wifeHour">
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
                       
                        <div class="btn-row">
                            <button class="btn-info" onclick="analyzeMarriageCompatibility()">🔍 Phân tích chi tiết</button>
                        </div>
                    </div>
                </div>
                <div id="marriageAnalysisResult"></div>
                <!-- Phần gợi ý ngày kết hôn -->
                <div class="wedding-date-suggestions">
                    <h3>📅 Gợi ý ngày kết hôn đẹp</h3>
                    <p>Chọn khoảng thời gian để nhận gợi ý những ngày tốt nhất cho việc kết hôn:</p>
                   
                    <div class="date-range-selector">
                        <div class="field">
                            <label>Từ ngày</label>
                            <input type="date" id="startDate" value="">
                        </div>
                        <div class="field">
                            <label>Đến ngày</label>
                            <input type="date" id="endDate" value="">
                        </div>
                    </div>
                   
                    <div class="btn-row">
                        <button class="btn-info" onclick="generateWeddingDateSuggestions()">🔮 Tìm ngày tốt</button>
                        <button class="btn-secondary" onclick="clearDateSuggestions()">🗑️ Xóa kết quả</button>
                    </div>
                   
                    <div id="dateSuggestionsResult"></div>
                   
                    <div class="saved-favorites" id="savedFavoritesSection" style="display: none;">
                        <h4>❤️ Ngày kết hôn đã lưu</h4>
                        <div id="favoritesList" class="favorites-list">
                            <!-- Danh sách ngày yêu thích sẽ được hiển thị ở đây -->
                        </div>
                    </div>
                </div>
                <!-- Phần kết quả phân tích đã lưu -->
                <div class="saved-analyses" id="savedAnalysesSection" style="display: none;">
                    <h4>📋 Kết quả phân tích đã lưu</h4>
                    <div id="savedAnalysesList">
                        <!-- Danh sách kết quả phân tích đã lưu sẽ được hiển thị ở đây -->
                    </div>
                </div>
                <div class="service-detail">
                    <h3>📚 Học lý phân tích theo Bát Tự</h3>
                    <div class="analysis-method">
                        <h4>7 bước luận giải hợp hôn:</h4>
                        <ol>
                            <li><strong>Lập Bát Tự:</strong> Dựa trên giờ, ngày, tháng, năm sinh</li>
                            <li><strong>Phân tích Ngũ Hành:</strong> Xác định hành vượng, hành nhược</li>
                            <li><strong>Xác định Dụng Thần:</strong> Tìm hành cân bằng mệnh cục</li>
                            <li><strong>So sánh Bát Tự:</strong> Ngũ hành, Thiên Can, Địa Chi, Nhật chủ</li>
                            <li><strong>Âm Dương phối hợp:</strong> Nam dương nữ âm là thuận</li>
                            <li><strong>Cung Phi Bát Trạch:</strong> Xem khí trạch sau hôn nhân</li>
                            <li><strong>Tổng hợp kết luận:</strong> Đánh giá tổng quan mức độ hợp</li>
                        </ol>
                    </div>
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>🔍 Phân tích Dụng Thần</strong>
                            <p>Xác định hành bổ trợ cân bằng mệnh cục</p>
                        </div>
                        <div class="feature-item">
                            <strong>⚖️ Ngũ Hành sinh khắc</strong>
                            <p>Đánh giá tương sinh, tương khắc chi tiết</p>
                        </div>
                        <div class="feature-item">
                            <strong>🌗 Thiên Can Địa Chi</strong>
                            <p>Xem hợp hóa, xung hình giữa hai mệnh</p>
                        </div>
                        <div class="feature-item">
                            <strong>🏠 Cung Phi Bát Trạch</strong>
                            <p>Định hướng nhà ở phù hợp sau hôn nhân</p>
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
    <!-- Thông báo -->
    <div id="notification" class="notification"></div>
    <script>
        // Dữ liệu cơ bản
        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
        const HANH_CAN = { Giáp: "Mộc", Ất: "Mộc", Bính: "Hỏa", Đinh: "Hỏa", Mậu: "Thổ", Kỷ: "Thổ", Canh: "Kim", Tân: "Kim", Nhâm: "Thủy", Quý: "Thủy" };
        // ==================== HỆ THỐNG QUẢN LÝ DỮ LIỆU ĐỒNG BỘ ====================
        let currentUser = <?php echo $user ? json_encode($user) : 'null'; ?>;
        let savedAccounts = [];
        // Lưu lịch sử tra cứu
        function saveSearchHistory(type, data) {
            if (!currentUser) return;
           
            const userEmail = currentUser.email;
            const searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || {};
            const userHistory = searchHistory[userEmail] || [];
           
            const searchRecord = {
                id: Date.now(),
                type: type,
                data: data,
                timestamp: new Date().toISOString(),
                ...data
            };
           
            userHistory.unshift(searchRecord);
           
            // Giới hạn lịch sử tối đa 100 mục
            if (userHistory.length > 100) {
                userHistory.splice(100);
            }
           
            searchHistory[userEmail] = userHistory;
            localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
           
            // Kích hoạt sự kiện cập nhật
            triggerDataUpdate();
        }
        // Lưu phân tích kết hôn vào database
        async function saveMarriageHistoryToDB(husbandInfo, wifeInfo, analysisResult) {
            if (!currentUser) return null;
           
            try {
                const response = await fetch('api/save_marriage_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        male_year: parseInt(husbandInfo.year),
                        female_year: parseInt(wifeInfo.year),
                        score: analysisResult.score,
                        evaluation: getEvaluationText(analysisResult.score),
                        remedies: getRemediesText(analysisResult),
                        detail: JSON.stringify({
                            husbandBazi: husbandInfo.bazi,
                            wifeBazi: wifeInfo.bazi,
                            analysis: analysisResult
                        })
                    })
                });
               
                const data = await response.json();
                return data.success ? data.history_id : null;
            } catch (error) {
                console.error('Lỗi lưu lịch sử kết hôn:', error);
                return null;
            }
        }
        function getEvaluationText(score) {
            if (score >= 8) return 'RẤT TỐT';
            if (score >= 6) return 'TỐT';
            if (score >= 4) return 'TRUNG BÌNH';
            return 'KHÔNG HỢP';
        }
        function getRemediesText(analysis) {
            const remedies = [];
           
            if (analysis.score < 6) {
                remedies.push('Chọn năm kết hôn phù hợp với Dụng thần cả hai');
                remedies.push('Sử dụng vật phẩm phong thủy bổ trợ');
                remedies.push('Chọn hướng nhà theo Cung Phi');
            } else {
                remedies.push('Hôn nhân hạnh phúc, tiếp tục duy trì sự thấu hiểu');
            }
           
            return remedies.join(', ');
        }
        // Lưu phân tích kết hôn
        async function saveMarriageAnalysis(husbandInfo, wifeInfo, analysisResult) {
            if (!currentUser) return;
           
            const analysisData = {
                id: 'marriage_' + Date.now(),
                type: 'marriage_analysis',
                husband: husbandInfo,
                wife: wifeInfo,
                analysis: analysisResult,
                score: analysisResult.score,
                timestamp: new Date().toISOString(),
                title: `Phân tích kết hôn: ${husbandInfo.year} & ${wifeInfo.year}`
            };
           
            // Lưu vào localStorage
            saveSearchHistory('marriage_analysis', analysisData);
           
            // Lưu vào database
            const historyId = await saveMarriageHistoryToDB(husbandInfo, wifeInfo, analysisResult);
            if (historyId) {
                analysisData.db_id = historyId;
            }
           
            // Cập nhật thống kê
            updateUserStats();
        }
        // Lưu ngày kết hôn yêu thích
        function saveFavoriteWeddingDate(dateData) {
            if (!currentUser) return;
           
            const userFavorites = JSON.parse(localStorage.getItem('userFavorites')) || {};
            const userFavoriteDates = userFavorites[currentUser.email] || [];
           
            const favoriteDate = {
                id: Date.now(),
                type: 'wedding_date',
                date: dateData.date,
                score: dateData.score,
                lunarDate: dateData.lunarDate,
                canChi: dateData.canChi,
                timestamp: new Date().toISOString(),
                title: `Ngày kết hôn: ${dateData.date} (${dateData.score}/10)`
            };
           
            // Kiểm tra xem đã tồn tại chưa
            const existingIndex = userFavoriteDates.findIndex(fav =>
                fav.type === 'wedding_date' && fav.date === dateData.date
            );
           
            if (existingIndex === -1) {
                userFavoriteDates.push(favoriteDate);
            } else {
                userFavoriteDates[existingIndex] = favoriteDate;
            }
           
            userFavorites[currentUser.email] = userFavoriteDates;
            localStorage.setItem('userFavorites', JSON.stringify(userFavorites));
           
            // Kích hoạt cập nhật
            triggerDataUpdate();
        }
        // Cập nhật thống kê người dùng
        function updateUserStats() {
            if (!currentUser) return;
           
            const userEmail = currentUser.email;
           
            // Lấy tất cả dữ liệu
            const searchHistory = JSON.parse(localStorage.getItem('searchHistory')) || {};
            const userFavorites = JSON.parse(localStorage.getItem('userFavorites')) || {};
            const marriageAnalyses = JSON.parse(localStorage.getItem('userAnalyses')) || {};
           
            const userHistory = searchHistory[userEmail] || [];
            const userFavs = userFavorites[userEmail] || [];
            const userMarriage = marriageAnalyses[userEmail] || [];
           
            // Tính toán thống kê
            const stats = {
                totalSearches: userHistory.length,
                totalFavorites: userFavs.length,
                marriageAnalyses: userMarriage.length,
                weddingDateFavorites: userFavs.filter(fav => fav.type === 'wedding_date').length,
                joinDate: currentUser.joined ? new Date(currentUser.joined) : new Date()
            };
           
            // Lưu thống kê
            const userStats = JSON.parse(localStorage.getItem('userStats')) || {};
            userStats[userEmail] = stats;
            localStorage.setItem('userStats', JSON.stringify(userStats));
           
            return stats;
        }
        // Kích hoạt cập nhật dữ liệu
        function triggerDataUpdate() {
            // Lưu thời gian cập nhật để các trang khác có thể lắng nghe
            localStorage.setItem('lastDataUpdate', Date.now().toString());
           
            // Cập nhật thống kê
            updateUserStats();
        }
        // Lắng nghe sự kiện cập nhật dữ liệu
        function listenForDataUpdates() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'lastDataUpdate') {
                    // Cập nhật giao diện khi có thay đổi dữ liệu
                    if (currentUser) {
                        updateUserStats();
                        loadFavorites();
                        loadSavedAnalyses();
                    }
                }
            });
           
            // Kiểm tra cập nhật mỗi 2 giây (dự phòng)
            setInterval(() => {
                const lastUpdate = localStorage.getItem('lastDataUpdate');
                if (lastUpdate && lastUpdate !== window.lastKnownUpdate) {
                    window.lastKnownUpdate = lastUpdate;
                    if (currentUser) {
                        updateUserStats();
                        loadFavorites();
                        loadSavedAnalyses();
                    }
                }
            }, 2000);
        }
        // ==================== HÀM TÍNH TOÁN BÁT TỰ ====================
        // Hàm thiên văn
        function jdFromDate(dd, mm, yy) {
            const a = Math.floor((14 - mm) / 12);
            const y = yy + 4800 - a;
            const m = mm + 12 * a - 3;
            let jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
            if (jd < 2299161) jd = dd + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - 32083;
            return jd;
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
        function canChiOfDay(jdn) { return [CAN[(jdn + 9) % 10], CHI[(jdn + 1) % 12]]; }
        function canChiOfYear(lY) { return [CAN[(lY + 6) % 10], CHI[(lY + 8) % 12]]; }
        function canChiOfMonth(lM, lY) { const yIdx = (lY + 6) % 10; return [CAN[(yIdx * 2 + lM + 1) % 10], CHI[(lM + 1) % 12]]; }
        // Hàm hỗ trợ cho xem tuổi sinh con
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
        function getKhacChe(element) {
            const khac = {
                'Mộc': ['Kim', 'Thổ'],
                'Hỏa': ['Thủy', 'Kim'],
                'Thổ': ['Mộc', 'Thủy'],
                'Kim': ['Hỏa', 'Mộc'],
                'Thủy': ['Thổ', 'Hỏa']
            };
            return khac[element] || [];
        }
        function calculateHourPillar(dayCan, hour) {
            const hourChi = CHI[hour];
            const startCanIndex = ((dayCan === 'Giáp' || dayCan === 'Kỷ') ? 0 :
                                 (dayCan === 'Ất' || dayCan === 'Canh') ? 2 :
                                 (dayCan === 'Bính' || dayCan === 'Tân') ? 4 :
                                 (dayCan === 'Đinh' || dayCan === 'Nhâm') ? 6 : 8);
            const hourCanIndex = (startCanIndex + Math.floor(hour / 2)) % 10;
            const hourCan = CAN[hourCanIndex];
            return [hourCan, hourChi];
        }
        function calculateElements(canArray, chiArray) {
            const elements = { Mộc: 0, Hỏa: 0, Thổ: 0, Kim: 0, Thủy: 0 };
           
            canArray.forEach(can => {
                const element = HANH_CAN[can];
                if (element) elements[element]++;
            });
           
            // Đơn giản hóa: mỗi Địa Chi tính 1 hành chính
            chiArray.forEach(chi => {
                const chiElements = {
                    'Tý': 'Thủy', 'Sửu': 'Thổ', 'Dần': 'Mộc', 'Mão': 'Mộc',
                    'Thìn': 'Thổ', 'Tỵ': 'Hỏa', 'Ngọ': 'Hỏa', 'Mùi': 'Thổ',
                    'Thân': 'Kim', 'Dậu': 'Kim', 'Tuất': 'Thổ', 'Hợi': 'Thủy'
                };
                const element = chiElements[chi];
                if (element) elements[element]++;
            });
           
            return elements;
        }
        function analyzeStrengthAndUsefulGod(dayCan, elements) {
            const dayElement = HANH_CAN[dayCan];
            let strength = 0;
           
            const sinhCho = getSinhCho(dayElement);
            const khacChe = getKhacChe(dayElement);
           
            sinhCho.forEach(element => strength += elements[element] || 0);
            khacChe.forEach(element => strength -= elements[element] || 0);
           
            const isStrong = strength >= 0;
           
            let usefulGod, avoidGod;
            if (isStrong) {
                usefulGod = khacChe[0];
                avoidGod = sinhCho[0];
            } else {
                usefulGod = sinhCho[0];
                avoidGod = khacChe[0];
            }
           
            return {
                strength: isStrong ? 'Thân vượng' : 'Thân nhược',
                usefulGod,
                avoidGod
            };
        }
        // Hàm tính Bát tự chính
        function calculateBazi(day, month, year, hour) {
            const jd = jdFromDate(day, month, year);
            const [lunarDay, lunarMonth, lunarYear, leap] = convertSolar2Lunar(day, month, year);
           
            const [yearCan, yearChi] = canChiOfYear(lunarYear);
            const [monthCan, monthChi] = canChiOfMonth(lunarMonth, lunarYear);
            const [dayCan, dayChi] = canChiOfDay(jd);
            const [hourCan, hourChi] = calculateHourPillar(dayCan, hour);
           
            const elements = calculateElements([yearCan, monthCan, dayCan, hourCan], [yearChi, monthChi, dayChi, hourChi]);
           
            const { strength, usefulGod, avoidGod } = analyzeStrengthAndUsefulGod(dayCan, elements);
           
            return {
                year: { can: yearCan, chi: yearChi },
                month: { can: monthCan, chi: monthChi },
                day: { can: dayCan, chi: dayChi },
                hour: { can: hourCan, chi: hourChi },
                elements,
                strength,
                usefulGod,
                avoidGod,
                lunar: { day: lunarDay, month: lunarMonth, year: lunarYear, leap }
            };
        }
        // ==================== HÀM PHÂN TÍCH KẾT HÔN ====================
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
        // Hàm tính Cung Phi chính xác hơn
        function getCungPhiNumber(lunarYear, gender) {
            const yearStr = lunarYear.toString();
            const sum = (parseInt(yearStr[2]) + parseInt(yearStr[3])) % 9;
            let cungNum = sum === 0 ? 9 : sum;
            if (gender === 'male') {
                cungNum = (10 - cungNum) % 9;
                if (cungNum === 0) cungNum = 9;
                if (cungNum === 5) cungNum = 2; // Nam mệnh 5 -> Khôn (2)
            } else {
                cungNum = (5 + cungNum) % 9;
                if (cungNum === 0) cungNum = 9;
                if (cungNum === 5) cungNum = 8; // Nữ mệnh 5 -> Cấn (8)
            }
            return cungNum;
        }
        function getCungPhiName(cungNum) {
            const cungMap = {
                1: 'Khảm',
                2: 'Khôn',
                3: 'Chấn',
                4: 'Tốn',
                6: 'Càn',
                7: 'Đoài',
                8: 'Cấn',
                9: 'Ly'
            };
            return cungMap[cungNum] || 'Không xác định';
        }
        function getCungPhiCompatibilityScore(husbandCung, wifeCung) {
            const compatibilityMap = {
                'Khảm': { 'Tốn': 2, 'Ly': 1, 'Chấn': 1, 'Khôn': -1, 'Càn': -2, 'Đoài': -1, 'Cấn': 0, 'Khảm': 0 },
                'Khôn': { 'Cấn': 2, 'Đoài': 1, 'Càn': 1, 'Chấn': -1, 'Khảm': -2, 'Tốn': -1, 'Ly': 0, 'Khôn': 0 },
                'Chấn': { 'Khảm': 2, 'Tốn': 1, 'Ly': 1, 'Đoài': -1, 'Cấn': -2, 'Khôn': -1, 'Càn': 0, 'Chấn': 0 },
                'Tốn': { 'Ly': 2, 'Khảm': 1, 'Chấn': 1, 'Càn': -1, 'Khôn': -2, 'Cấn': -1, 'Đoài': 0, 'Tốn': 0 },
                'Càn': { 'Tốn': 2, 'Chấn': 1, 'Khôn': 1, 'Ly': -1, 'Đoài': -2, 'Khảm': -1, 'Cấn': 0, 'Càn': 0 },
                'Đoài': { 'Khôn': 2, 'Cấn': 1, 'Càn': 1, 'Khảm': -1, 'Ly': -2, 'Chấn': -1, 'Tốn': 0, 'Đoài': 0 },
                'Cấn': { 'Đoài': 2, 'Khôn': 1, 'Càn': 1, 'Tốn': -1, 'Chấn': -2, 'Ly': -1, 'Khảm': 0, 'Cấn': 0 },
                'Ly': { 'Chấn': 2, 'Tốn': 1, 'Khảm': 1, 'Cấn': -1, 'Đoài': -2, 'Khôn': -1, 'Càn': 0, 'Ly': 0 }
            };
            return compatibilityMap[husbandCung]?.[wifeCung] || 0;
        }
        function isCanYang(can) {
            return ['Giáp', 'Bính', 'Mậu', 'Canh', 'Nhâm'].includes(can);
        }
        // Hàm chính cho xem tuổi kết hôn
        function analyzeMarriageCompatibility() {
            // Lấy thông tin từ form
            const husbandDay = parseInt(document.getElementById('husbandDay').value);
            const husbandMonth = parseInt(document.getElementById('husbandMonth').value);
            const husbandYear = parseInt(document.getElementById('husbandYear').value);
            const husbandHour = parseInt(document.getElementById('husbandHour').value);
           
            const wifeDay = parseInt(document.getElementById('wifeDay').value);
            const wifeMonth = parseInt(document.getElementById('wifeMonth').value);
            const wifeYear = parseInt(document.getElementById('wifeYear').value);
            const wifeHour = parseInt(document.getElementById('wifeHour').value);
            if (!husbandDay || !husbandMonth || !husbandYear || !wifeDay || !wifeMonth || !wifeYear) {
                showNotification('Vui lòng nhập đầy đủ thông tin ngày tháng năm sinh!', 'error');
                return;
            }
            // Tính Bát Tự cho cả hai
            const husbandBazi = calculateBazi(husbandDay, husbandMonth, husbandYear, husbandHour);
            const wifeBazi = calculateBazi(wifeDay, wifeMonth, wifeYear, wifeHour);
            // Phân tích chi tiết
            const analysis = analyzeBaziCompatibility(husbandBazi, wifeBazi);
            // Lưu thông tin tra cứu
            const husbandInfo = {
                day: husbandDay, month: husbandMonth, year: husbandYear,
                bazi: husbandBazi
            };
            const wifeInfo = {
                day: wifeDay, month: wifeMonth, year: wifeYear,
                bazi: wifeBazi
            };
           
            saveMarriageAnalysis(husbandInfo, wifeInfo, analysis);
            // Hiển thị kết quả
            displayMarriageAnalysis(husbandBazi, wifeBazi, analysis);
        }
        function analyzeBaziCompatibility(husband, wife) {
            let score = 0;
            const details = [];
           
            // 1. Phân tích Dụng thần
            if (husband.usefulGod === wife.usefulGod) {
                score += 2;
                details.push({
                    type: 'good',
                    text: `✅ Dụng thần tương đồng (${husband.usefulGod}) - Hỗ trợ lẫn nhau`
                });
            } else if (checkTuongSinh(husband.usefulGod, wife.usefulGod)) {
                score += 1.5;
                details.push({
                    type: 'good',
                    text: `✅ Dụng thần tương sinh (${husband.usefulGod} ↔ ${wife.usefulGod}) - Bổ trợ tốt`
                });
            } else if (checkTuongKhac(husband.usefulGod, wife.usefulGod)) {
                score -= 1;
                details.push({
                    type: 'bad',
                    text: `❌ Dụng thần tương khắc (${husband.usefulGod} → ${wife.usefulGod}) - Có xung đột`
                });
            } else {
                details.push({
                    type: 'warning',
                    text: `⚠️ Dụng thần không tương hỗ - Cần cân nhắc`
                });
            }
           
            // 2. Phân tích Kỵ thần
            if (husband.avoidGod && wife.avoidGod) {
                if (husband.avoidGod === wife.usefulGod || wife.avoidGod === husband.usefulGod) {
                    score += 1.5;
                    details.push({
                        type: 'good',
                        text: `✅ Kỵ thần được hóa giải bởi Dụng thần - Giảm thiểu bất lợi`
                    });
                } else if (checkTuongSinh(husband.avoidGod, wife.avoidGod)) {
                    score -= 1;
                    details.push({
                        type: 'warning',
                        text: `⚠️ Kỵ thần tương sinh - Có thể tăng cường bất lợi`
                    });
                }
            }
           
            // 3. Phân tích Âm Dương phối hợp (Nam dương Nữ âm)
            const husbandDayYang = isCanYang(husband.day.can);
            const wifeDayYang = isCanYang(wife.day.can);
            if (husbandDayYang && !wifeDayYang) {
                score += 1;
                details.push({
                    type: 'good',
                    text: `✅ Âm Dương phối hợp thuận (Nam dương - Nữ âm)`
                });
            } else if (!husbandDayYang && wifeDayYang) {
                score -= 1;
                details.push({
                    type: 'bad',
                    text: `❌ Âm Dương phối hợp nghịch (Nam âm - Nữ dương)`
                });
            } else {
                details.push({
                    type: 'warning',
                    text: `⚠️ Âm Dương đồng loại - Trung bình`
                });
            }
           
            // 4. Phân tích Nhật chủ (Can ngày)
            const husbandDayElement = HANH_CAN[husband.day.can];
            const wifeDayElement = HANH_CAN[wife.day.can];
           
            if (checkTuongSinh(husbandDayElement, wifeDayElement)) {
                score += 2;
                details.push({
                    type: 'good',
                    text: `✅ Nhật chủ tương sinh (${husbandDayElement} ↔ ${wifeDayElement}) - Hôn nhân hòa hợp`
                });
            } else if (checkTuongKhac(husbandDayElement, wifeDayElement)) {
                score -= 2;
                details.push({
                    type: 'bad',
                    text: `❌ Nhật chủ tương khắc (${husbandDayElement} → ${wifeDayElement}) - Có thể xung đột`
                });
            } else {
                score += 0.5;
                details.push({
                    type: 'neutral',
                    text: `ℹ️ Nhật chủ bình hòa - Không xung không khắc`
                });
            }
           
            // 5. Phân tích Thiên Can (tăng chi tiết, thêm kiểm tra xung)
            const canCompatibility = checkCanCompatibility(husband, wife);
            score += canCompatibility.score;
            details.push(canCompatibility.detail);
           
            // 6. Phân tích Địa Chi (tăng chi tiết)
            const chiCompatibility = checkChiCompatibility(husband, wife);
            score += chiCompatibility.score;
            details.push(chiCompatibility.detail);
           
            // 7. Phân tích Cung Phi Bát Trạch (sửa chính xác hơn)
            const husbandCungNum = getCungPhiNumber(husband.lunar.year, 'male');
            const wifeCungNum = getCungPhiNumber(wife.lunar.year, 'female');
            const husbandCung = getCungPhiName(husbandCungNum);
            const wifeCung = getCungPhiName(wifeCungNum);
            const cungPhiScore = getCungPhiCompatibilityScore(husbandCung, wifeCung);
            score += cungPhiScore;
            if (cungPhiScore > 0) {
                details.push({
                    type: 'good',
                    text: `✅ Cung Phi hợp (${husbandCung} - ${wifeCung}) - Tốt cho hậu vận (+${cungPhiScore})`
                });
            } else if (cungPhiScore < 0) {
                details.push({
                    type: 'bad',
                    text: `❌ Cung Phi khắc (${husbandCung} - ${wifeCung}) - Cần hóa giải (${cungPhiScore})`
                });
            } else {
                details.push({
                    type: 'warning',
                    text: `⚠️ Cung Phi trung bình (${husbandCung} - ${wifeCung})`
                });
            }
           
            // Giới hạn điểm từ 0 đến 10
            score = Math.max(0, Math.min(10, score));
           
            return { score: Math.round(score * 10) / 10, details };
        }
        function checkCanCompatibility(husband, wife) {
            const canPairs = [
                [husband.year.can, wife.year.can],
                [husband.month.can, wife.month.can],
                [husband.day.can, wife.day.can],
                [husband.hour.can, wife.hour.can]
            ];
           
            let compatiblePairs = 0;
            let conflictPairs = 0;
           
            for (let [can1, can2] of canPairs) {
                // Hợp hóa
                if ((can1 === 'Giáp' && can2 === 'Kỷ') || (can1 === 'Kỷ' && can2 === 'Giáp') ||
                    (can1 === 'Ất' && can2 === 'Canh') || (can1 === 'Canh' && can2 === 'Ất') ||
                    (can1 === 'Bính' && can2 === 'Tân') || (can1 === 'Tân' && can2 === 'Bính') ||
                    (can1 === 'Đinh' && can2 === 'Nhâm') || (can1 === 'Nhâm' && can2 === 'Đinh') ||
                    (can1 === 'Mậu' && can2 === 'Quý') || (can1 === 'Quý' && can2 === 'Mậu')) {
                    compatiblePairs++;
                }
                // Xung Can (thêm kiểm tra xung, ví dụ Can cách 5 vị trí)
                const canIndex1 = CAN.indexOf(can1);
                const canIndex2 = CAN.indexOf(can2);
                if (Math.abs(canIndex1 - canIndex2) === 5) {
                    conflictPairs++;
                }
            }
           
            const score = (compatiblePairs * 1.5) - (conflictPairs * 1);
           
            return {
                score,
                detail: {
                    type: score > 0 ? 'good' : score < 0 ? 'bad' : 'neutral',
                    text: score > 0 ?
                        `✅ ${compatiblePairs} cặp Thiên Can hợp hóa, ${conflictPairs} xung - Tốt tổng thể` :
                        score < 0 ?
                        `❌ ${conflictPairs} cặp Thiên Can xung, ${compatiblePairs} hợp - Cần chú ý` :
                        'ℹ️ Thiên Can trung lập'
                }
            };
        }
        function checkChiCompatibility(husband, wife) {
            const chiPairs = [
                [husband.year.chi, wife.year.chi],
                [husband.month.chi, wife.month.chi],
                [husband.day.chi, wife.day.chi],
                [husband.hour.chi, wife.hour.chi]
            ];
           
            let tamHop = 0, lucHop = 0, tuXung = 0;
           
            for (let [chi1, chi2] of chiPairs) {
                if (checkTamHop(chi1, chi2)) tamHop++;
                if (checkLucHop(chi1, chi2)) lucHop++;
                if (checkTuXung(chi1, chi2)) tuXung++;
            }
           
            let score = tamHop * 1.5 + lucHop * 1 - tuXung * 1.5;
            let text = '';
           
            if (tamHop > 0) text += `${tamHop} tam hợp `;
            if (lucHop > 0) text += `${lucHop} lục hợp `;
            if (tuXung > 0) text += `${tuXung} tứ hành xung `;
           
            if (!text) text = 'Địa Chi trung lập';
           
            return {
                score,
                detail: {
                    type: score > 1 ? 'good' : score < -1 ? 'bad' : 'neutral',
                    text: `${score > 1 ? '✅' : score < -1 ? '❌' : 'ℹ️'} ${text}`
                }
            };
        }
        function displayMarriageAnalysis(husband, wife, analysis) {
            const analysisId = 'analysis_' + Date.now();
           
            const resultHTML = `
                <div class="result" id="${analysisId}">
                    <button class="favorite-analysis-btn" onclick="toggleFavoriteAnalysis('${analysisId}')">❤️</button>
                    <h3>📊 Kết quả phân tích hợp hôn Bát Tự</h3>
                   
                    <div class="bazi-grid">
                        <div class="bazi-card">
                            <div class="bazi-header">👨 Bát Tự Nam</div>
                            <div class="bazi-details">
                                <strong>Năm:</strong> ${husband.year.can} ${husband.year.chi}<br>
                                <strong>Tháng:</strong> ${husband.month.can} ${husband.month.chi}<br>
                                <strong>Ngày:</strong> ${husband.day.can} ${husband.day.chi}<br>
                                <strong>Giờ:</strong> ${husband.hour.can} ${husband.hour.chi}<br>
                                <strong>Mệnh cục:</strong> ${husband.strength}<br>
                                <strong>Dụng thần:</strong> ${husband.usefulGod}<br>
                                <strong>Kỵ thần:</strong> ${husband.avoidGod}
                            </div>
                        </div>
                        <div class="bazi-card">
                            <div class="bazi-header">👩 Bát Tự Nữ</div>
                            <div class="bazi-details">
                                <strong>Năm:</strong> ${wife.year.can} ${wife.year.chi}<br>
                                <strong>Tháng:</strong> ${wife.month.can} ${wife.month.chi}<br>
                                <strong>Ngày:</strong> ${wife.day.can} ${wife.day.chi}<br>
                                <strong>Giờ:</strong> ${wife.hour.can} ${wife.hour.chi}<br>
                                <strong>Mệnh cục:</strong> ${wife.strength}<br>
                                <strong>Dụng thần:</strong> ${wife.usefulGod}<br>
                                <strong>Kỵ thần:</strong> ${wife.avoidGod}
                            </div>
                        </div>
                    </div>
                   
                    <div class="score-display ${getScoreClass(analysis.score)}">
                        Điểm đánh giá: ${analysis.score}/10
                    </div>
                   
                    <div class="analysis-section">
                        <h4>🔍 Phân tích chi tiết:</h4>
                        ${analysis.details.map(detail => `
                            <div class="analysis-item ${detail.type === 'good' ? 'analysis-good' : detail.type === 'bad' ? 'analysis-bad' : 'analysis-warning'}">
                                ${detail.text}
                            </div>
                        `).join('')}
                    </div>
                   
                    ${analysis.score < 6 ? `
                    <div class="recommendation-box">
                        <h4>💡 Kiến nghị hóa giải:</h4>
                        <ul>
                            <li>Chọn năm kết hôn phù hợp với Dụng thần cả hai</li>
                            <li>Sử dụng vật phẩm phong thủy tương ứng mệnh ${husband.usefulGod}</li>
                            <li>Chọn hướng nhà theo Cung Phi bổ trợ</li>
                            <li>Tích cực tu dưỡng bản thân, thấu hiểu đối phương</li>
                        </ul>
                    </div>` : ''}
                   
                    <div class="analysis-item analysis-info">
                        <button class="btn-secondary" onclick="viewInProfile()">📋 Xem trong Hồ sơ</button>
                    </div>
                    <div class="analysis-item analysis-good">
                        <strong>Lưu ý:</strong> Phân tích dựa trên Bát Tự chỉ mang tính chất tham khảo.
                        Hôn nhân hạnh phúc phụ thuộc vào sự thấu hiểu, tôn trọng và nỗ lực của cả hai phía.
                    </div>
                </div>
            `;
           
            document.getElementById('marriageAnalysisResult').innerHTML = resultHTML;
           
            // Lưu phân tích vào lịch sử
            saveAnalysisToHistory(analysisId, husband, wife, analysis);
        }
        function getScoreClass(score) {
            if (score >= 8) return 'score-excellent';
            if (score >= 6) return 'score-good';
            if (score >= 4) return 'score-medium';
            return 'score-poor';
        }
        // ==================== PHẦN GỢI Ý NGÀY KẾT HÔN ====================
       
        // Dữ liệu Hoàng Đạo - Hắc Đạo
        const HOANG_DAO = {
            1: ["Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi"],
            2: ["Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu"],
            3: ["Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"],
            4: ["Thân", "Dậu", "Tuất", "Hợi", "Tý", "Sửu"],
            5: ["Tuất", "Hợi", "Tý", "Sửu", "Dần", "Mão"],
            6: ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ"],
            7: ["Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi"],
            8: ["Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu"],
            9: ["Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"],
            10: ["Thân", "Dậu", "Tuất", "Hợi", "Tý", "Sửu"],
            11: ["Tuất", "Hợi", "Tý", "Sửu", "Dần", "Mão"],
            12: ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ"]
        };
        // Dữ liệu 12 Trực - cập nhật score theo prompt
        const TRUC_DATA = {
            "Kiến": { score: 0, good: false, bad: true },
            "Trừ": { score: -2, good: false, bad: true },
            "Mãn": { score: 1, good: true, bad: false },
            "Bình": { score: 1, good: true, bad: false },
            "Định": { score: 3, good: true, bad: false },
            "Chấp": { score: 0, good: false, bad: false },
            "Phá": { score: -2, good: false, bad: true },
            "Nguy": { score: -2, good: false, bad: true },
            "Thành": { score: 3, good: true, bad: false },
            "Thu": { score: 1, good: true, bad: false },
            "Khai": { score: 3, good: true, bad: false },
            "Bế": { score: -2, good: false, bad: true }
        };
        // Dữ liệu Lục Diệu
        const LUC_DIEU_DATA = {
            "Đại An": { score: 2, level: "tốt" },
            "Lưu Niên": { score: 0, level: "trung bình" },
            "Tốc Hỷ": { score: 2, level: "tốt" },
            "Xích Khẩu": { score: -1, level: "xấu" },
            "Tiểu Cát": { score: 2, level: "tốt" },
            "Không Vong": { score: -2, level: "rất xấu" }
        };
        // Dữ liệu Nhị Thập Bát Tú (28 sao)
        const NHI_THAP_BAT_TU = {
            "Giác": { score: 1, level: "cát" },
            "Cang": { score: 0, level: "bình" },
            "Đê": { score: -1, level: "hung" },
            "Phòng": { score: 1, level: "cát" },
            "Tâm": { score: -1, level: "hung" },
            "Vĩ": { score: 0, level: "bình" },
            "Cơ": { score: -1, level: "hung" },
            "Đẩu": { score: 1, level: "cát" },
            "Ngưu": { score: -1, level: "hung" },
            "Nữ": { score: 0, level: "bình" },
            "Hư": { score: -1, level: "hung" },
            "Nguy": { score: 0, level: "bình" },
            "Thất": { score: 1, level: "cát" },
            "Bích": { score: 1, level: "cát" },
            "Khuê": { score: 2, level: "đại cát" },
            "Lâu": { score: 1, level: "cát" },
            "Vị": { score: 0, level: "bình" },
            "Mão": { score: -1, level: "hung" },
            "Tất": { score: 1, level: "cát" },
            "Chủy": { score: -1, level: "hung" },
            "Sâm": { score: 0, level: "bình" },
            "Tỉnh": { score: 2, level: "đại cát" },
            "Quỷ": { score: 1, level: "cát" },
            "Liễu": { score: -1, level: "hung" },
            "Tinh": { score: -1, level: "hung" },
            "Trương": { score: 1, level: "cát" },
            "Dực": { score: 0, level: "bình" },
            "Chẩn": { score: -1, level: "hung" }
        };
        const TAM_HOP = {
            "Thân": ["Thân", "Tý", "Thìn"],
            "Tỵ": ["Tỵ", "Dậu", "Sửu"],
            "Dần": ["Dần", "Ngọ", "Tuất"],
            "Hợi": ["Hợi", "Mão", "Mùi"]
        };
        const LUC_XUNG = {
            "Tý": ["Ngọ"], "Ngọ": ["Tý"],
            "Sửu": ["Mùi"], "Mùi": ["Sửu"],
            "Dần": ["Thân"], "Thân": ["Dần"],
            "Mão": ["Dậu"], "Dậu": ["Mão"],
            "Thìn": ["Tuất"], "Tuất": ["Thìn"],
            "Tỵ": ["Hợi"], "Hợi": ["Tỵ"]
        };
        // Hàm tính Trực ngày
        function trucNgay(chiNgay, chiThang) {
            const TRUC_ORDER = ["Kiến", "Trừ", "Mãn", "Bình", "Định", "Chấp", "Phá", "Nguy", "Thành", "Thu", "Khai", "Bế"];
            const CHI_INDEX = { "Tý": 0, "Sửu": 1, "Dần": 2, "Mão": 3, "Thìn": 4, "Tỵ": 5, "Ngọ": 6, "Mùi": 7, "Thân": 8, "Dậu": 9, "Tuất": 10, "Hợi": 11 };
           
            const startIndex = CHI_INDEX[chiThang];
            const dayIndex = CHI_INDEX[chiNgay];
            const diff = (dayIndex - startIndex + 12) % 12;
           
            return TRUC_ORDER[diff];
        }
        // Hàm tính Lục Diệu
        function lucDieu(jdn) {
            const LUC_DIEU_ORDER = ["Đại An", "Lưu Niên", "Tốc Hỷ", "Xích Khẩu", "Tiểu Cát", "Không Vong"];
            const index = (jdn - 1) % 6;
            return LUC_DIEU_ORDER[index];
        }
        // Hàm tính Nhị Thập Bát Tú
        function nhiThapBatTu(jdn) {
            const STARS = ["Giác", "Cang", "Đê", "Phòng", "Tâm", "Vĩ", "Cơ", "Đẩu", "Ngưu", "Nữ", "Hư", "Nguy", "Thất", "Bích", "Khuê", "Lâu", "Vị", "Mão", "Tất", "Chủy", "Sâm", "Tỉnh", "Quỷ", "Liễu", "Tinh", "Trương", "Dực", "Chẩn"];
            const index = (jdn - 1) % 28;
            return STARS[index];
        }
        // Hàm tính điểm ngày theo 6 tiêu chí + 2 tiêu chí đặc thù cho kết hôn
        function calculateDayScore(day, month, year, husbandBazi, wifeBazi) {
            let score = 0;
            const details = [];
           
            const jd = jdFromDate(day, month, year);
            const [lunarDay, lunarMonth, lunarYear] = convertSolar2Lunar(day, month, year);
            const [canNgay, chiNgay] = canChiOfDay(jd);
           
            // ① HOÀNG ĐẠO - HẮC ĐẠO
            const isHoangDao = HOANG_DAO[lunarMonth]?.includes(chiNgay);
            if (isHoangDao) {
                score += 2;
                details.push("✅ Hoàng Đạo (+2)");
            } else {
                details.push("❌ Hắc Đạo (0)");
            }
           
            // ② TRỰC NGÀY
            const truc = trucNgay(chiNgay, CHI[lunarMonth - 1]);
            const trucInfo = TRUC_DATA[truc];
            score += trucInfo.score;
            details.push(`${trucInfo.score >= 0 ? '✅' : '❌'} Trực ${truc} (${trucInfo.score > 0 ? '+' : ''}${trucInfo.score})`);
           
            // ③ LỤC DIỆU
            const lucDieuStar = lucDieu(jd);
            const lucDieuInfo = LUC_DIEU_DATA[lucDieuStar];
            score += lucDieuInfo.score;
            details.push(`${lucDieuInfo.score >= 0 ? '✅' : '❌'} Lục Diệu ${lucDieuStar} (${lucDieuInfo.score > 0 ? '+' : ''}${lucDieuInfo.score})`);
           
            // ④ NHỊ THẬP BÁT TÚ
            const nhatTuStar = nhiThapBatTu(jd);
            const nhatTuInfo = NHI_THAP_BAT_TU[nhatTuStar];
            score += nhatTuInfo.score;
            details.push(`${nhatTuInfo.score >= 0 ? '✅' : '❌'} Sao ${nhatTuStar} (${nhatTuInfo.score > 0 ? '+' : ''}${nhatTuInfo.score})`);
           
            // ⑤ NGÀY HỢP - NGÀY KỴ THEO TUỔI
            let hopTuoiScore = 0;
            const husbandChi = husbandBazi.year.chi;
            const wifeChi = wifeBazi.year.chi;
            const husbandDayElement = HANH_CAN[husbandBazi.day.can];
            const wifeDayElement = HANH_CAN[wifeBazi.day.can];
            const dayElement = HANH_CAN[canNgay];
           
            // Tam hợp
            if (checkTamHop(chiNgay, husbandChi)) {
                hopTuoiScore += 1;
                details.push("✅ Tam hợp với chồng (+1)");
            }
            if (checkTamHop(chiNgay, wifeChi)) {
                hopTuoiScore += 1;
                details.push("✅ Tam hợp với vợ (+1)");
            }
           
            // Lục hợp
            if (checkLucHop(chiNgay, husbandChi)) {
                hopTuoiScore += 1;
                details.push("✅ Lục hợp với chồng (+1)");
            }
            if (checkLucHop(chiNgay, wifeChi)) {
                hopTuoiScore += 1;
                details.push("✅ Lục hợp với vợ (+1)");
            }
           
            // Lục xung
            if (LUC_XUNG[husbandChi]?.includes(chiNgay)) {
                hopTuoiScore -= 2;
                details.push("❌ Lục xung với chồng (-2)");
            }
            if (LUC_XUNG[wifeChi]?.includes(chiNgay)) {
                hopTuoiScore -= 2;
                details.push("❌ Lục xung với vợ (-2)");
            }
           
            score += hopTuoiScore;
           
            // ⑥ TIÊU CHÍ ĐẶC THÙ KẾT HÔN: HỢP MỆNH
            let hopMenScore = 0;
            if (checkTuongSinh(dayElement, husbandDayElement)) {
                hopMenScore += 1;
                details.push("✅ Ngày tương sinh với mệnh chồng (+1)");
            } else if (checkTuongKhac(dayElement, husbandDayElement)) {
                hopMenScore -= 2;
                details.push("❌ Ngày tương khắc với mệnh chồng (-2)");
            }
            if (checkTuongSinh(dayElement, wifeDayElement)) {
                hopMenScore += 1;
                details.push("✅ Ngày tương sinh với mệnh vợ (+1)");
            } else if (checkTuongKhac(dayElement, wifeDayElement)) {
                hopMenScore -= 2;
                details.push("❌ Ngày tương khắc với mệnh vợ (-2)");
            }
            // Hợp chi đã có ở trên, tam hợp cũng có
            // Nếu tổng hopMenScore + hopTuoiScore > 0, thêm +2 cho "hợp mệnh - hợp chi - hợp tam hợp", nhưng để tránh trùng, chỉ thêm hopMenScore
            score += hopMenScore;
           
            // ⑦ CUNG BÁT TRẠCH
            const husbandCungNum = getCungPhiNumber(husbandBazi.lunar.year, 'male');
            const dayCungNum = getCungPhiNumber(lunarYear, 'male'); // Giả sử ép theo nam, nhưng ngày không có gender, dùng male cho ngày
            const husbandCung = getCungPhiName(husbandCungNum);
            const dayCung = getCungPhiName(dayCungNum);
            const cungPhiScore = getCungPhiCompatibilityScore(husbandCung, dayCung); // So với ngày
            score += cungPhiScore;
            if (cungPhiScore > 0) {
                details.push(`✅ Cung Bát Trạch hợp với chồng (+${cungPhiScore})`);
            } else if (cungPhiScore < 0) {
                details.push(`❌ Cung Bát Trạch khắc với chồng (${cungPhiScore})`);
            }
           
            return {
                score: Math.max(0, Math.min(10, score)), // Giới hạn điểm 0-10
                details: details,
                analysis: {
                    hoangDao: isHoangDao,
                    truc: truc,
                    lucDieu: lucDieuStar,
                    nhatTu: nhatTuStar,
                    chiNgay: chiNgay,
                    lunarDate: `${lunarDay}/${lunarMonth}/${lunarYear}`
                }
            };
        }
        // Hàm chính tìm ngày đẹp kết hôn
        function generateWeddingDateSuggestions() {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
           
            if (!startDateInput.value || !endDateInput.value) {
                showNotification('Vui lòng chọn khoảng thời gian!', 'error');
                return;
            }
           
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
           
            if (startDate > endDate) {
                showNotification('Ngày bắt đầu phải trước ngày kết thúc!', 'error');
                return;
            }
           
            // Tính số ngày trong khoảng thời gian
            const daysDiff = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
           
            if (daysDiff > 365) {
                showNotification('Khoảng thời gian không được vượt quá 1 năm!', 'error');
                return;
            }
           
            // Lấy thông tin tuổi vợ chồng từ form
            const husbandDay = parseInt(document.getElementById('husbandDay').value);
            const husbandMonth = parseInt(document.getElementById('husbandMonth').value);
            const husbandYear = parseInt(document.getElementById('husbandYear').value);
            const husbandHour = parseInt(document.getElementById('husbandHour').value);
           
            const wifeDay = parseInt(document.getElementById('wifeDay').value);
            const wifeMonth = parseInt(document.getElementById('wifeMonth').value);
            const wifeYear = parseInt(document.getElementById('wifeYear').value);
            const wifeHour = parseInt(document.getElementById('wifeHour').value);
           
            if (!husbandYear || !wifeYear) {
                showNotification('Vui lòng nhập đầy đủ năm sinh vợ chồng để tính ngày hợp tuổi!', 'error');
                return;
            }
           
            const husbandBazi = calculateBazi(husbandDay || 1, husbandMonth || 1, husbandYear, husbandHour || 0);
            const wifeBazi = calculateBazi(wifeDay || 1, wifeMonth || 1, wifeYear, wifeHour || 0);
           
            // Hiển thị loading
            document.getElementById('dateSuggestionsResult').innerHTML = '<p>🔮 Đang phân tích ngày tốt theo 6 tiêu chí phong thủy...</p>';
           
            // Giả lập thời gian xử lý
            setTimeout(() => {
                const suggestedDates = calculateGoodWeddingDatesAdvanced(startDate, endDate, husbandBazi, wifeBazi);
                displayDateSuggestionsAdvanced(suggestedDates);
            }, 1500);
        }
        // Hàm tính toán ngày tốt nâng cao
        function calculateGoodWeddingDatesAdvanced(startDate, endDate, husbandBazi, wifeBazi) {
            const suggestedDates = [];
            const currentDate = new Date(startDate);
           
            while (currentDate <= endDate) {
                const day = currentDate.getDate();
                const month = currentDate.getMonth() + 1;
                const year = currentDate.getFullYear();
               
                // Tính điểm theo 6 tiêu chí + đặc thù
                const dayScore = calculateDayScore(day, month, year, husbandBazi, wifeBazi);
               
                // Chỉ thêm ngày có điểm từ 5 trở lên (Ngày ĐẸP)
                if (dayScore.score >= 5) {
                    const lunarDate = convertSolar2Lunar(day, month, year);
                    const lunarDay = lunarDate[0];
                    const lunarMonth = lunarDate[1];
                    const lunarYear = lunarDate[2];
                   
                    const jd = jdFromDate(day, month, year);
                    const [canNgay, chiNgay] = canChiOfDay(jd);
                   
                    suggestedDates.push({
                        date: new Date(currentDate),
                        score: dayScore.score,
                        details: dayScore.details,
                        analysis: dayScore.analysis,
                        lunarDay: lunarDay,
                        lunarMonth: lunarMonth,
                        lunarYear: lunarYear,
                        canChi: { can: canNgay, chi: chiNgay },
                        isFavorite: false
                    });
                }
               
                // Tăng ngày
                currentDate.setDate(currentDate.getDate() + 1);
            }
           
            // Sắp xếp theo điểm số giảm dần
            suggestedDates.sort((a, b) => b.score - a.score);
           
            // Giới hạn số lượng kết quả
            return suggestedDates.slice(0, 15);
        }
        // Hàm hiển thị kết quả nâng cao
        function displayDateSuggestionsAdvanced(dates) {
            if (dates.length === 0) {
                document.getElementById('dateSuggestionsResult').innerHTML =
                    '<div class="analysis-item analysis-warning">' +
                    '❌ Không tìm thấy ngày nào đạt tiêu chuẩn "NGÀY ĐẸP" (điểm ≥ 5) trong khoảng thời gian này.' +
                    '</div>';
                return;
            }
           
            let html = `<h4>🎯 Tìm thấy ${dates.length} NGÀY ĐẸP cho kết hôn:</h4>
                        <div class="suggested-dates-grid">`;
           
            dates.forEach(date => {
                const dateClass = getDateClassAdvanced(date.score);
                const dateStr = formatDate(date.date);
                const lunarStr = `Âm lịch: ${date.lunarDay}/${date.lunarMonth}/${date.lunarYear}`;
                const canChiStr = `${date.canChi.can} ${date.canChi.chi}`;
               
                // Kiểm tra xem ngày này đã được yêu thích chưa
                const isFav = checkIfDateIsFavorite(dateStr);
               
                html += `
                    <div class="date-card ${dateClass}" data-date="${dateStr}">
                        <div class="date-header">
                            <span>${dateStr}</span>
                            <span class="date-score">${date.score}/10</span>
                        </div>
                        <div class="date-details">
                            <div>${lunarStr}</div>
                            <div>${canChiStr}</div>
                            <div>${date.analysis.truc} • ${date.analysis.lucDieu}</div>
                            <div>${date.analysis.hoangDao ? '🌟 Hoàng Đạo' : '⚫ Hắc Đạo'} • ${date.analysis.nhatTu}</div>
                        </div>
                        <div class="date-badges">
                            ${getDateBadgesAdvanced(date)}
                        </div>
                        <div class="date-actions">
                            <button class="btn-info" onclick="viewDateDetailsAdvanced('${dateStr}', ${date.score})">👁️ Chi tiết</button>
                            <button class="favorite-btn ${isFav ? 'active' : ''}"
                                    onclick="toggleFavorite('${dateStr}', ${date.score}, '${lunarStr}', '${canChiStr}')">❤️</button>
                        </div>
                    </div>
                `;
            });
           
            html += `</div>`;
            document.getElementById('dateSuggestionsResult').innerHTML = html;
        }
        // Hàm lấy class CSS cho ngày dựa trên điểm số (nâng cao)
        function getDateClassAdvanced(score) {
            if (score >= 9) return 'perfect';
            if (score >= 8) return 'excellent';
            if (score >= 7) return 'good';
            return 'good'; // Mặc định
        }
        // Hàm lấy badge cho ngày (nâng cao)
        function getDateBadgesAdvanced(date) {
            const badges = [];
           
            if (date.score >= 9) {
                badges.push('<span class="date-badge">Tuyệt vời</span>');
            } else if (date.score >= 8) {
                badges.push('<span class="date-badge">Xuất sắc</span>');
            } else if (date.score >= 7) {
                badges.push('<span class="date-badge">Rất tốt</span>');
            } else {
                badges.push('<span class="date-badge">Tốt</span>');
            }
           
            if (date.analysis.hoangDao) {
                badges.push('<span class="date-badge">Hoàng đạo</span>');
            }
           
            if (date.analysis.truc === 'Thành' || date.analysis.truc === 'Định' || date.analysis.truc === 'Khai') {
                badges.push('<span class="date-badge">Trực tốt</span>');
            }
           
            if (date.analysis.lucDieu === 'Đại An' || date.analysis.lucDieu === 'Tiểu Cát' || date.analysis.lucDieu === 'Tốc Hỷ') {
                badges.push('<span class="date-badge">Lục Diệu tốt</span>');
            }
           
            return badges.join('');
        }
        // Hàm xem chi tiết ngày nâng cao
        function viewDateDetailsAdvanced(dateStr, score) {
            // Tách ngày, tháng, năm từ chuỗi
            const [day, month, year] = dateStr.split('/').map(Number);
           
            // Lấy thông tin tuổi vợ chồng
            const husbandYear = parseInt(document.getElementById('husbandYear').value);
            const wifeYear = parseInt(document.getElementById('wifeYear').value);
            const husbandLunarYear = convertSolar2Lunar(1, 1, husbandYear)[2];
            const wifeLunarYear = convertSolar2Lunar(1, 1, wifeYear)[2];
            const husbandBazi = calculateBazi(1, 1, husbandYear, 0);
            const wifeBazi = calculateBazi(1, 1, wifeYear, 0);
           
            // Tính điểm chi tiết
            const dayScore = calculateDayScore(day, month, year, husbandBazi, wifeBazi);
           
            const detailHTML = `
                <div class="analysis-section">
                    <h4>📅 Phân tích chi tiết ngày ${dateStr}</h4>
                    <div class="bazi-grid">
                        <div class="bazi-card">
                            <div class="bazi-header">Thông tin ngày</div>
                            <div class="bazi-details">
                                <strong>Dương lịch:</strong> ${dateStr}<br>
                                <strong>Âm lịch:</strong> ${dayScore.analysis.lunarDate}<br>
                                <strong>Can Chi:</strong> ${dayScore.analysis.chiNgay}<br>
                                <strong>Điểm tổng:</strong> ${dayScore.score}/10
                            </div>
                        </div>
                        <div class="bazi-card">
                            <div class="bazi-header">Kết quả phân tích</div>
                            <div class="bazi-details">
                                <strong>Hoàng Đạo:</strong> ${dayScore.analysis.hoangDao ? '✅ Có' : '❌ Không'}<br>
                                <strong>Trực:</strong> ${dayScore.analysis.truc}<br>
                                <strong>Lục Diệu:</strong> ${dayScore.analysis.lucDieu}<br>
                                <strong>Nhị Thập Bát Tú:</strong> ${dayScore.analysis.nhatTu}
                            </div>
                        </div>
                    </div>
                   
                    <div class="analysis-section">
                        <h4>🔍 Chi tiết tính điểm:</h4>
                        ${dayScore.details.map(detail => `
                            <div class="analysis-item ${detail.includes('✅') ? 'analysis-good' : detail.includes('❌') ? 'analysis-bad' : 'analysis-warning'}">
                                ${detail}
                            </div>
                        `).join('')}
                    </div>
                   
                    <div class="recommendation-box">
                        <h4>💡 Đánh giá:</h4>
                        <p>${
                            score >= 9 ? '🎉 NGÀY TUYỆT VỜI - Rất tốt cho việc kết hôn, mang lại hạnh phúc viên mãn' :
                            score >= 8 ? '⭐ NGÀY XUẤT SẮC - Tốt cho hôn nhân, gia đình hòa thuận' :
                            score >= 7 ? '👍 NGÀY RẤT TỐT - Phù hợp cho kết hôn, cuộc sống ổn định' :
                            '✅ NGÀY TỐT - Có thể tiến hành kết hôn'
                        }</p>
                    </div>
                </div>
            `;
           
            // Hiển thị chi tiết trong phần kết quả
            document.getElementById('dateSuggestionsResult').innerHTML += detailHTML;
        }
        // Hàm kiểm tra xem ngày đã được yêu thích chưa
        function checkIfDateIsFavorite(dateStr) {
            if (!currentUser) return false;
           
            const userFavorites = JSON.parse(localStorage.getItem('userFavorites')) || {};
            const userFavoriteDates = userFavorites[currentUser.email] || [];
           
            return userFavoriteDates.some(fav => fav.date === dateStr);
        }
       
        // Hàm thêm/xóa ngày yêu thích
        function toggleFavorite(dateStr, score, lunarStr, canChiStr) {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để sử dụng tính năng này!', 'error');
                return;
            }
           
            // Lấy danh sách yêu thích từ localStorage
            const userFavorites = JSON.parse(localStorage.getItem('userFavorites')) || {};
            const userFavoriteDates = userFavorites[currentUser.email] || [];
           
            // Kiểm tra xem ngày đã có trong danh sách yêu thích chưa
            const dateIndex = userFavoriteDates.findIndex(fav => fav.date === dateStr);
           
            if (dateIndex === -1) {
                // Thêm vào danh sách yêu thích
                const dateData = {
                    date: dateStr,
                    score: score,
                    lunarDate: lunarStr.replace('Âm lịch: ', ''),
                    canChi: canChiStr,
                    addedAt: new Date().toISOString()
                };
               
                userFavoriteDates.push(dateData);
               
                // Lưu vào hệ thống đồng bộ
                saveFavoriteWeddingDate(dateData);
               
                showNotification('Đã thêm vào danh sách yêu thích!', 'success');
            } else {
                // Xóa khỏi danh sách yêu thích
                userFavoriteDates.splice(dateIndex, 1);
                showNotification('Đã xóa khỏi danh sách yêu thích!', 'info');
            }
           
            // Lưu lại danh sách yêu thích
            userFavorites[currentUser.email] = userFavoriteDates;
            localStorage.setItem('userFavorites', JSON.stringify(userFavorites));
           
            // Cập nhật giao diện
            loadFavorites();
           
            // Cập nhật trạng thái nút yêu thích
            const favoriteBtn = document.querySelector(`.date-card[data-date="${dateStr}"] .favorite-btn`);
            if (favoriteBtn) {
                favoriteBtn.classList.toggle('active');
            }
        }
       
        // Hàm tải danh sách yêu thích
        function loadFavorites() {
            if (!currentUser) return;
           
            const userFavorites = JSON.parse(localStorage.getItem('userFavorites')) || {};
            const userFavoriteDates = userFavorites[currentUser.email] || [];
           
            if (userFavoriteDates.length === 0) {
                document.getElementById('savedFavoritesSection').style.display = 'none';
                return;
            }
           
            document.getElementById('savedFavoritesSection').style.display = 'block';
           
            let html = '';
            userFavoriteDates.forEach(fav => {
                html += `
                    <div class="date-card good">
                        <div class="date-header">
                            <span>${fav.date}</span>
                            <span class="date-score">${fav.score}/10</span>
                        </div>
                        <div class="date-details">
                            <div>Âm lịch: ${fav.lunarDate}</div>
                            <div>${fav.canChi}</div>
                            <div>Đã lưu: ${new Date(fav.addedAt).toLocaleDateString('vi-VN')}</div>
                        </div>
                        <div class="date-actions">
                            <button class="btn-info" onclick="viewDateDetailsAdvanced('${fav.date}', ${fav.score})">👁️ Xem lại</button>
                            <button class="favorite-btn active" onclick="toggleFavorite('${fav.date}', ${fav.score}, 'Âm lịch: ${fav.lunarDate}', '${fav.canChi}')">❤️</button>
                        </div>
                    </div>
                `;
            });
           
            document.getElementById('favoritesList').innerHTML = html;
        }
       
        // Hàm xóa kết quả gợi ý
        function clearDateSuggestions() {
            document.getElementById('dateSuggestionsResult').innerHTML = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
        }
       
        // Hàm định dạng ngày cho input type="date"
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        // Hàm định dạng ngày
        function formatDate(date) {
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
        // ==================== PHẦN LƯU KẾT QUẢ PHÂN TÍCH ====================
       
        // Hàm lưu phân tích vào lịch sử
        function saveAnalysisToHistory(analysisId, husband, wife, analysis) {
            if (!currentUser) return;
           
            const userAnalyses = JSON.parse(localStorage.getItem('userAnalyses')) || {};
            const userAnalysisList = userAnalyses[currentUser.email] || [];
           
            const analysisData = {
                id: analysisId,
                husband: {
                    day: document.getElementById('husbandDay').value,
                    month: document.getElementById('husbandMonth').value,
                    year: document.getElementById('husbandYear').value,
                    hour: document.getElementById('husbandHour').value,
                    bazi: husband
                },
                wife: {
                    day: document.getElementById('wifeDay').value,
                    month: document.getElementById('wifeMonth').value,
                    year: document.getElementById('wifeYear').value,
                    hour: document.getElementById('wifeHour').value,
                    bazi: wife
                },
                analysis: analysis,
                createdAt: new Date().toISOString()
            };
           
            userAnalysisList.push(analysisData);
            userAnalyses[currentUser.email] = userAnalysisList;
            localStorage.setItem('userAnalyses', JSON.stringify(userAnalyses));
           
            // Kích hoạt cập nhật
            triggerDataUpdate();
        }
       
        // Hàm thêm/xóa phân tích yêu thích
        async function toggleFavoriteAnalysis(analysisId) {
            if (!currentUser) {
                showNotification('Vui lòng đăng nhập để sử dụng tính năng này!', 'error');
                return;
            }
           
            const userAnalyses = JSON.parse(localStorage.getItem('userAnalyses')) || {};
            const userAnalysisList = userAnalyses[currentUser.email] || [];
            const analysisData = userAnalysisList.find(a => a.id === analysisId);
           
            if (!analysisData) return;
           
            const userFavAnalyses = JSON.parse(localStorage.getItem('userFavAnalyses')) || {};
            const userFavAnalysisList = userFavAnalyses[currentUser.email] || [];
           
            const analysisIndex = userFavAnalysisList.findIndex(fav => fav.id === analysisId);
           
            if (analysisIndex === -1) {
                // Thêm vào danh sách yêu thích
                userFavAnalysisList.push(analysisData);
               
                // Lưu vào database
                const success = await saveMarriageFavoriteToDB(analysisData);
                if (success) {
                    showNotification('Đã thêm vào danh sách yêu thích!', 'success');
                }
            } else {
                // Xóa khỏi danh sách yêu thích
                userFavAnalysisList.splice(analysisIndex, 1);
               
                // Xóa khỏi database (nếu có ID)
                if (analysisData.db_id) {
                    await removeMarriageFavoriteFromDB(analysisData.db_id);
                }
               
                showNotification('Đã xóa khỏi danh sách yêu thích!', 'info');
            }
           
            // Lưu lại danh sách yêu thích
            userFavAnalyses[currentUser.email] = userFavAnalysisList;
            localStorage.setItem('userFavAnalyses', JSON.stringify(userFavAnalyses));
           
            // Cập nhật giao diện
            loadSavedAnalyses();
           
            // Cập nhật trạng thái nút yêu thích
            const favoriteBtn = document.querySelector(`#${analysisId} .favorite-analysis-btn`);
            if (favoriteBtn) {
                favoriteBtn.classList.toggle('active');
            }
           
            // Kích hoạt cập nhật
            triggerDataUpdate();
        }
        // Hàm lưu yêu thích kết hôn vào database
        async function saveMarriageFavoriteToDB(analysisData) {
            if (!currentUser) return false;
           
            try {
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'kethon',
                        solar_date: new Date().toISOString().split('T')[0],
                        lunar_date: '',
                        rating_text: `Phân tích kết hôn: Nam ${analysisData.husband.year} - Nữ ${analysisData.wife.year} - Điểm: ${analysisData.analysis.score}/10`,
                        score: analysisData.analysis.score,
                        item_data: JSON.stringify(analysisData)
                    })
                });
               
                const data = await response.json();
                return data.success;
            } catch (error) {
                console.error('Lỗi lưu yêu thích kết hôn:', error);
                return false;
            }
        }
        // Hàm xóa yêu thích khỏi database
        async function removeMarriageFavoriteFromDB(favoriteId) {
            try {
                const response = await fetch('api/remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: favoriteId })
                });
               
                return response.json();
            } catch (error) {
                console.error('Lỗi xóa yêu thích kết hôn:', error);
                return { success: false };
            }
        }
        // Hàm tải lịch sử kết hôn từ database
        async function loadMarriageHistoryFromDB() {
            if (!currentUser) return [];
           
            try {
                const response = await fetch('api/get_marriage_history.php?limit=20');
                const data = await response.json();
               
                if (data.success && data.history) {
                    return data.history;
                }
                return [];
            } catch (error) {
                console.error('Lỗi tải lịch sử kết hôn:', error);
                return [];
            }
        }
       
        // Hàm tải danh sách phân tích đã lưu
        async function loadSavedAnalyses() {
            if (!currentUser) return;
           
            // Tải từ database
            const dbHistory = await loadMarriageHistoryFromDB();
            const userFavAnalyses = JSON.parse(localStorage.getItem('userFavAnalyses')) || {};
            const userFavAnalysisList = userFavAnalyses[currentUser.email] || [];
           
            // Kết hợp dữ liệu từ database và localStorage
            const allAnalyses = [...dbHistory.map(item => ({
                id: 'db_' + item.id,
                db_id: item.id,
                husband: { year: item.male_year },
                wife: { year: item.female_year },
                analysis: {
                    score: item.score,
                    details: JSON.parse(item.detail)?.analysis?.details || []
                },
                createdAt: item.created_at
            })), ...userFavAnalysisList];
           
            if (allAnalyses.length === 0) {
                document.getElementById('savedAnalysesSection').style.display = 'none';
                return;
            }
           
            document.getElementById('savedAnalysesSection').style.display = 'block';
           
            let html = '';
            allAnalyses.forEach(analysis => {
                const husbandInfo = `Nam: ${analysis.husband.year}`;
                const wifeInfo = `Nữ: ${analysis.wife.year}`;
                const score = analysis.analysis.score;
                const isFromDB = analysis.db_id;
               
                html += `
                    <div class="saved-analysis-item">
                        <div class="saved-analysis-header">
                            <div class="saved-analysis-title">Phân tích hợp hôn</div>
                            <div class="saved-analysis-date">${new Date(analysis.createdAt).toLocaleDateString('vi-VN')}</div>
                        </div>
                        <div class="saved-analysis-details">
                            <div>${husbandInfo}</div>
                            <div>${wifeInfo}</div>
                            <div class="score-display ${getScoreClass(score)}" style="font-size: 16px; padding: 8px; margin: 10px 0;">
                                Điểm: ${score}/10
                            </div>
                        </div>
                        <div class="saved-analysis-actions">
                            <button class="btn-info btn-small" onclick="loadMarriageAnalysis(${analysis.husband.year}, ${analysis.wife.year})">👁️ Xem lại</button>
                            ${!isFromDB ? `<button class="btn-danger btn-small" onclick="removeSavedAnalysis('${analysis.id}')">🗑️ Xóa</button>` : ''}
                        </div>
                    </div>
                `;
            });
           
            document.getElementById('savedAnalysesList').innerHTML = html;
        }
       
        // Hàm tải lại phân tích từ năm sinh
        function loadMarriageAnalysis(husbandYear, wifeYear) {
            // Điền thông tin vào form
            document.getElementById('husbandYear').value = husbandYear;
            document.getElementById('wifeYear').value = wifeYear;
           
            // Thực hiện phân tích lại
            analyzeMarriageCompatibility();
           
            // Cuộn đến form
            document.querySelector('.calculator-form').scrollIntoView({ behavior: 'smooth' });
           
            showNotification(`Đã tải thông tin phân tích: Nam ${husbandYear} - Nữ ${wifeYear}`, 'info');
        }
       
        // Hàm xóa phân tích đã lưu
        function removeSavedAnalysis(analysisId) {
            if (!currentUser) return;
           
            const userFavAnalyses = JSON.parse(localStorage.getItem('userFavAnalyses')) || {};
            const userFavAnalysisList = userFavAnalyses[currentUser.email] || [];
           
            const analysisIndex = userFavAnalysisList.findIndex(fav => fav.id === analysisId);
           
            if (analysisIndex !== -1) {
                userFavAnalysisList.splice(analysisIndex, 1);
                userFavAnalyses[currentUser.email] = userFavAnalysisList;
                localStorage.setItem('userFavAnalyses', JSON.stringify(userFavAnalyses));
               
                showNotification('Đã xóa phân tích khỏi danh sách yêu thích!', 'info');
                loadSavedAnalyses();
                triggerDataUpdate();
            }
        }
        // Hàm chuyển hướng đến trang hồ sơ
        function viewInProfile() {
            // Lưu thông tin để trang user.php có thể tải lại
            const husbandYear = document.getElementById('husbandYear').value;
            const wifeYear = document.getElementById('wifeYear').value;
           
            localStorage.setItem('loadMarriageAnalysis', JSON.stringify({
                husband: { year: husbandYear },
                wife: { year: wifeYear },
                timestamp: new Date().toISOString()
            }));
           
            // Chuyển hướng đến trang hồ sơ
            window.location.href = 'user.php';
        }
        // ==================== HỆ THỐNG ĐĂNG NHẬP/ĐĂNG KÝ ====================
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
                const existingIndex = savedAccounts.findIndex(acc => acc.email === currentUser.email);
               
                if (existingIndex === -1) {
                    savedAccounts.push({
                        name: currentUser.name,
                        email: currentUser.email,
                        avatar: currentUser.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
                    });
                } else {
                    savedAccounts[existingIndex].name = currentUser.name;
                    savedAccounts[existingIndex].avatar = currentUser.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
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
                    loadFavorites();
                    loadSavedAnalyses();
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
                    saveAccountToLocal();
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
                    loadFavorites();
                    loadSavedAnalyses();
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
                    showNotification(data.message || 'Đã đăng xuất thành công!', 'success');
                    triggerDataUpdate();
                   
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server!', 'error');
                });
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
            window.location.href = 'user.php';
        }
        function showForgotPasswordModal() {
            showNotification('Tính năng quên mật khẩu đang được phát triển', 'info');
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
        // ==================== KHỞI TẠO ỨNG DỤNG ====================
        // Initialize application
        function initializeApp() {
            setupNavigation();
            listenForDataUpdates();
            setupEventListeners();
           
            // Thiết lập ngày mặc định cho phần gợi ý
            const today = new Date();
            const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            const threeMonthsLater = new Date(today.getFullYear(), today.getMonth() + 4, 0);
           
            document.getElementById('startDate').value = formatDateForInput(nextMonth);
            document.getElementById('endDate').value = formatDateForInput(threeMonthsLater);
           
            // Kiểm tra nếu có dữ liệu từ user.php chuyển sang
            checkForDataFromUserPage();
           
            // Tải danh sách yêu thích nếu có người dùng
            if (currentUser) {
                loadFavorites();
                loadSavedAnalyses();
            }
        }
        function setupEventListeners() {
            document.getElementById('login-btn')?.addEventListener('click', showLoginModal);
            document.getElementById('register-btn')?.addEventListener('click', showRegisterModal);
            document.getElementById('logout-btn')?.addEventListener('click', logout);
            document.getElementById('profile-btn')?.addEventListener('click', showProfileModal);
           
            // Modal events
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
        // Kiểm tra dữ liệu từ user.php
        function checkForDataFromUserPage() {
            const loadMarriageAnalysis = localStorage.getItem('loadMarriageAnalysis');
            if (loadMarriageAnalysis) {
                try {
                    const analysisData = JSON.parse(loadMarriageAnalysis);
                    // Điền thông tin vào form
                    if (analysisData.husband) {
                        document.getElementById('husbandYear').value = analysisData.husband.year || '';
                    }
                    if (analysisData.wife) {
                        document.getElementById('wifeYear').value = analysisData.wife.year || '';
                    }
                   
                    // Xóa dữ liệu tạm
                    localStorage.removeItem('loadMarriageAnalysis');
                   
                    // Hiển thị thông báo
                    showNotification('Đã tải thông tin phân tích từ trang Hồ sơ!', 'success');
                } catch (e) {
                    console.error('Lỗi khi tải dữ liệu từ user.php:', e);
                }
            }
        }
        function setupNavigation() {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
        // Khởi tạo ứng dụng khi DOM đã tải xong
        document.addEventListener('DOMContentLoaded', initializeApp);
    </script>
</body>
</html>
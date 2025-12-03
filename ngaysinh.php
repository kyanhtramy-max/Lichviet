<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu Ngày Sinh - Lịch Việt</title>
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
            color: #2c3e50;
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

        .color-hop { color: #28a745; font-weight: bold; }
        .color-ky { color: #dc3545; font-weight: bold; }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .result-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .result-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #667eea;
        }

        .result-item h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .result-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #667eea;
        }

        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 20px 0;
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

        .tab-system {
            margin-top: 30px;
        }

        .tab-header {
            display: flex;
            border-bottom: 2px solid #e1e8ed;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 12px 24px;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            color: #667eea;
            background: #f8f9fa;
        }

        .tab-btn.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #667eea;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ LỊCH VIỆT ✨</h1>
            <p class="subtitle">Tra cứu thông tin ngày sinh</p>
          
            <div class="user-section">
                <div id="user-info" class="user-info" style="display: none;">
                    <div class="user-avatar" id="user-avatar">A</div>
                    <div class="user-details">
                        <div class="user-name" id="user-display-name">Nguyễn Văn A</div>
                        <div class="user-email" id="user-display-email">user@example.com</div>
                    </div>
                    <div class="user-actions">
                        <button id="profile-btn" class="btn-info">📋 Hồ sơ</button>
                        <button id="logout-btn" class="btn-secondary">🚪 Đăng xuất</button>
                    </div>
                </div>
                <div class="auth-buttons" id="auth-buttons">
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
                    <a class="nav-link active" href="ngaysinh.php">
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
      
        <div class="app-container">
            <section class="info-section">
                <h2>👶 Tra cứu ngày sinh</h2>
                <div class="panel">
                    <div class="panel-title">🔮 Tra cứu ngày tháng năm sinh</div>
                    <div class="calculator-form">
                        <div class="form-row three">
                            <div class="field">
                                <label>Ngày sinh</label>
                                <input type="number" id="birthDay" min="1" max="31" value="<?= date('d') ?>">
                            </div>
                            <div class="field">
                                <label>Tháng sinh</label>
                                <input type="number" id="birthMonth" min="1" max="12" value="<?= date('m') ?>">
                            </div>
                            <div class="field">
                                <label>Năm sinh</label>
                                <input type="number" id="birthYear" min="1900" max="2100" value="<?= date('Y') ?>">
                            </div>
                        </div>
                        <div class="btn-row">
                            <button class="btn-info" onclick="lookupBirth()">🔍 Xem thông tin</button>
                        </div>
                        <div id="birthResult"></div>
                    </div>
                </div>

                <div class="favorites-section">
                    <div class="panel-title">📚 Lịch sử & Yêu thích</div>
                    
                    <div class="tab-system">
                        <div class="tab-header">
                            <button class="tab-btn active" onclick="showTab('recent')">🕐 Gần đây</button>
                            <button class="tab-btn" onclick="showTab('favorites')">❤️ Yêu thích</button>
                        </div>
                        
                        <div class="tab-content active" id="recentTab">
                            <div id="recentHistorySection" class="favorites-list">
                                <div class="loading">🔄 Đang tải lịch sử tra cứu...</div>
                            </div>
                        </div>
                        
                        <div class="tab-content" id="favoritesTab">
                            <div id="favoritesList" class="favorites-list">
                                <div class="loading">🔄 Đang tải danh sách yêu thích...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="service-detail">
                    <h3>📚 Thông tin về dịch vụ</h3>
                    <p>Tra cứu ngày sinh cung cấp thông tin chi tiết về ngày tháng năm sinh của bạn theo cả Dương lịch và Âm lịch, cùng với các thông tin phong thủy liên quan.</p>
                  
                    <div class="service-features">
                        <div class="feature-item">
                            <strong>📅 Thông tin Âm lịch</strong>
                            <p>Xem ngày tháng năm sinh theo Âm lịch</p>
                        </div>
                        <div class="feature-item">
                            <strong>🌗 Can Chi</strong>
                            <p>Thông tin Can Chi ngày, tháng, năm</p>
                        </div>
                        <div class="feature-item">
                            <strong>⚖️ Mệnh ngũ hành</strong>
                            <p>Xác định mệnh theo năm sinh</p>
                        </div>
                        <div class="feature-item">
                            <strong>🔯 Cung hoàng đạo</strong>
                            <p>Xem cung hoàng đạo phương Tây</p>
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

    <div id="notification" class="notification"></div>

    <script>
        let currentUser = null;
        let currentBirthResult = null;

        // ==================== DỮ LIỆU PHONG THỦY ====================
        const CAN = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
        const CHI = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];

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

        const HUONG_HOP = {
            "Mộc": { tot: ["Đông", "Đông Nam"], ky: ["Tây", "Tây Bắc"] },
            "Hỏa": { tot: ["Nam", "Đông Nam"], ky: ["Bắc", "Đông"] },
            "Thổ": { tot: ["Tây Nam", "Đông Bắc"], ky: ["Đông", "Nam"] },
            "Kim": { tot: ["Tây", "Tây Bắc"], ky: ["Đông Nam", "Nam"] },
            "Thủy": { tot: ["Bắc", "Đông Nam"], ky: ["Tây Nam", "Tây"] }
        };

        // ==================== HỆ THỐNG LƯU TRỮ & API ====================

            async function saveToSearchHistory(result) {
            if (!currentUser) {
                console.log('❌ Chưa đăng nhập - bỏ qua lưu lịch sử');
                return;
            }
            
            try {
                console.log('📍 Bắt đầu lưu lịch sử...');
                
                const dataToSave = {
                    birth_date: `${result.year}-${String(result.month).padStart(2, '0')}-${String(result.day).padStart(2, '0')}`,
                    lunar_date: `${result.lunarDay}-${result.lunarMonth}-${result.lunarYear}`,
                    zodiac: result.zodiac,
                    nap_am: result.napAm,
                    summary: `Ngày sinh: ${result.day}/${result.month}/${result.year} - Mệnh: ${result.napAm}`
                };

                console.log('📤 Dữ liệu gửi đi:', dataToSave);
                console.log('🔗 URL API: api/save_birth_history.php');

                const response = await fetch('api/save_birth_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(dataToSave)
                });
                
                console.log('📥 HTTP Status:', response.status);
                console.log('📥 HTTP Status Text:', response.statusText);
                
                const responseText = await response.text();
                console.log('📥 Response Text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('❌ Lỗi parse JSON từ response:', e);
                    throw new Error('Response không phải JSON hợp lệ: ' + responseText);
                }
                
                console.log('📊 Response Data:', data);
                
                if (data.success) {
                    console.log('✅ Đã lưu lịch sử thành công');
                    showNotification('✅ Đã lưu vào lịch sử!', 'success');
                    loadRecentHistory();
                } else {
                    console.error('❌ Lỗi từ server:', data.message);
                    showNotification('❌ ' + data.message, 'error');
                }
                
            } catch (error) {
                console.error('💥 Lỗi kết nối:', error);
                console.error('💥 Error name:', error.name);
                console.error('💥 Error message:', error.message);
                showNotification('❌ Lỗi kết nối server: ' + error.message, 'error');
            }
        }

        async function getRecentHistory() {
            if (!currentUser) {
                console.log('Chưa đăng nhập - không thể lấy lịch sử');
                return [];
            }
            
            try {
                console.log('Đang gọi API get_birth_history.php...');
                const response = await fetch('api/get_birth_history.php');
                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.success) {
                    console.log(`✅ Nhận được ${data.history.length} bản ghi lịch sử`);
                    return data.history || [];
                } else {
                    console.error('❌ Lỗi API:', data.message);
                    return [];
                }
            } catch (error) {
                console.error('❌ Lỗi kết nối khi lấy lịch sử:', error);
                return [];
            }
        }

        async function getFavorites() {
            if (!currentUser) {
                console.log('Chưa đăng nhập - không thể lấy yêu thích');
                return [];
            }
            
            try {
                console.log('Đang gọi API get_favorites.php...');
                const response = await fetch('api/get_favorites.php');
                const data = await response.json();
                console.log('Favorites API Response:', data);
                
                if (data.success) {
                    console.log(`✅ Nhận được ${data.favorites.length} mục yêu thích`);
                    return data.favorites || [];
                } else {
                    console.error('❌ Lỗi API favorites:', data.message);
                    return [];
                }
            } catch (error) {
                console.error('❌ Lỗi kết nối khi lấy yêu thích:', error);
                return [];
            }
        }

        async function loadRecentHistory() {
            const recentSection = document.getElementById('recentHistorySection');
            if (!recentSection) return;

            recentSection.innerHTML = '<div class="loading">🔄 Đang tải lịch sử tra cứu...</div>';
            
            const history = await getRecentHistory();
            displayRecentHistory(history);
        }

        function displayRecentHistory(history) {
            const recentSection = document.getElementById('recentHistorySection');
            if (!recentSection) return;

            console.log('Displaying history:', history);

            if (!history || history.length === 0) {
                recentSection.innerHTML = `
                    <div class="empty-favorites">
                        <p>📝 Chưa có lịch sử tra cứu nào</p>
                        <p>Thực hiện tra cứu ngày sinh để xem lịch sử ở đây</p>
                    </div>
                `;
                return;
            }

            recentSection.innerHTML = history.map(item => {
                let formattedDate = 'N/A';
                try {
                    if (item.birth_date && item.birth_date !== '0000-00-00') {
                        const birthDate = new Date(item.birth_date + 'T00:00:00');
                        if (!isNaN(birthDate.getTime())) {
                            formattedDate = birthDate.toLocaleDateString('vi-VN');
                        }
                    }
                } catch (e) {
                    console.error('Lỗi format date:', e);
                }

                return `
                <div class="favorite-item">
                    <div class="favorite-item-header">
                        <div class="favorite-title">📅 Tra cứu ngày sinh</div>
                        <div class="favorite-date">${new Date(item.created_at).toLocaleDateString('vi-VN')}</div>
                    </div>
                    <div class="favorite-details">
                        <p><strong>📆 Dương lịch:</strong> ${formattedDate}</p>
                        <p><strong>🌙 Âm lịch:</strong> ${item.lunar_date || 'N/A'}</p>
                        <p><strong>⚖️ Mệnh:</strong> ${item.destiny || 'N/A'}</p>
                        <p><strong>🔯 Cung:</strong> ${item.zodiac || 'N/A'}</p>
                    </div>
                    <div class="favorite-actions">
                        <button class="btn-info" onclick="loadFromHistory('${item.birth_date}')">👁️ Xem lại</button>
                    </div>
                </div>
                `;
            }).join('');
        }

        async function displayFavorites() {
            const favoritesList = document.getElementById('favoritesList');
            
            if (!currentUser) {
                favoritesList.innerHTML = `
                    <div class="empty-favorites">
                        <p>🔐 Vui lòng đăng nhập để xem danh sách yêu thích</p>
                    </div>
                `;
                return;
            }
            
            favoritesList.innerHTML = '<div class="loading">🔄 Đang tải danh sách yêu thích...</div>';
            
            const favorites = await getFavorites();
            console.log('Displaying favorites:', favorites);
            
            if (favorites.length === 0) {
                favoritesList.innerHTML = `
                    <div class="empty-favorites">
                        <p>❤️ Chưa có tra cứu nào được lưu</p>
                        <p>Thực hiện phân tích và nhấn nút "⭐ Lưu tra cứu này" để lưu kết quả</p>
                    </div>
                `;
                return;
            }
            
            favoritesList.innerHTML = favorites.map(favorite => {
                let solarDate = favorite.solar_date;
                let displayDate = solarDate;
                
                if (solarDate && solarDate !== '0000-00-00') {
                    try {
                        const birthDate = new Date(solarDate + 'T00:00:00');
                        if (!isNaN(birthDate.getTime())) {
                            displayDate = birthDate.toLocaleDateString('vi-VN');
                        }
                    } catch (e) {
                        console.error('Lỗi parse date:', e);
                    }
                }
                
                return `
                <div class="favorite-item">
                    <div class="favorite-item-header">
                        <div class="favorite-title">⭐ ${favorite.rating_text || 'Ngày sinh'}</div>
                        <div class="favorite-date">${new Date(favorite.created_at).toLocaleDateString('vi-VN')}</div>
                    </div>
                    <div class="favorite-details">
                        <p><strong>📆 Dương lịch:</strong> ${displayDate}</p>
                        <p><strong>🌙 Âm lịch:</strong> ${favorite.lunar_date || 'N/A'}</p>
                        <p><strong>📝 Thông tin:</strong> ${favorite.rating_text || 'Không có'}</p>
                        ${favorite.score ? `<p><strong>🏆 Điểm:</strong> ${favorite.score}/10</p>` : ''}
                    </div>
                    <div class="favorite-actions">
                        <button class="btn-info" onclick="loadFavorite('${favorite.solar_date}')">👁️ Xem lại</button>
                        <button class="btn-danger" onclick="deleteFavorite(${favorite.id})">🗑️ Xóa</button>
                    </div>
                </div>
                `;
            }).join('');
        }

        async function deleteFavorite(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa tra cứu này khỏi danh sách yêu thích?')) {
                return;
            }
            
            try {
                const response = await fetch('api/remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                if (data.success) {
                    showNotification('✅ Đã xóa khỏi danh sách yêu thích!', 'success');
                    displayFavorites();
                } else {
                    showNotification('❌ ' + (data.message || 'Lỗi khi xóa yêu thích!'), 'error');
                }
            } catch (error) {
                console.error('Lỗi kết nối khi xóa yêu thích:', error);
                showNotification('❌ Lỗi kết nối server!', 'error');
            }
        }

        async function saveToFavorites() {
            if (!currentBirthResult) {
                showNotification('❌ Không có kết quả phân tích nào để lưu!', 'error');
                return;
            }

            if (!currentUser) {
                showNotification('❌ Vui lòng đăng nhập để lưu vào danh sách yêu thích!', 'error');
                showLoginModal();
                return;
            }
            
            try {
                const solarDate = `${currentBirthResult.year}-${String(currentBirthResult.month).padStart(2, '0')}-${String(currentBirthResult.day).padStart(2, '0')}`;
                const lunarDate = `${currentBirthResult.lunarDay}-${currentBirthResult.lunarMonth}-${currentBirthResult.lunarYear}`;
                const ratingText = `Ngày sinh: ${solarDate} - ${currentBirthResult.napAm} - ${currentBirthResult.zodiac}`;
                
                console.log('Đang lưu vào favorites:', { solarDate, lunarDate, ratingText });
                
                const response = await fetch('api/add_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        solar: solarDate,
                        lunar: lunarDate,
                        rating: ratingText,
                        score: 5.0
                    })
                });
                
                const data = await response.json();
                console.log('Kết quả lưu favorite:', data);
                
                if (data.success) {
                    showNotification(data.added ? '✅ Đã thêm vào danh sách yêu thích!' : 'ℹ️ Đã có trong danh sách yêu thích', 'success');
                    displayFavorites();
                } else {
                    showNotification('❌ ' + (data.message || 'Lỗi khi lưu yêu thích!'), 'error');
                }
            } catch (error) {
                console.error('Lỗi kết nối khi lưu yêu thích:', error);
                showNotification('❌ Lỗi kết nối server!', 'error');
            }
        }

        function loadFromHistory(solarDate) {
            loadFavorite(solarDate);
        }

        function loadFavorite(solarDate) {
            if (!solarDate || solarDate === '0000-00-00') {
                showNotification('❌ Không thể tải thông tin từ dữ liệu này!', 'error');
                return;
            }
            
            try {
                const dateParts = solarDate.split('-');
                if (dateParts.length === 3) {
                    const year = parseInt(dateParts[0]);
                    const month = parseInt(dateParts[1]);
                    const day = parseInt(dateParts[2]);
                    
                    document.getElementById('birthDay').value = day;
                    document.getElementById('birthMonth').value = month;
                    document.getElementById('birthYear').value = year;
                    
                    lookupBirth();
                    
                    window.scrollTo(0, 0);
                    showNotification('✅ Đã tải thông tin từ lịch sử!', 'success');
                } else {
                    showNotification('❌ Định dạng ngày không hợp lệ!', 'error');
                }
            } catch (error) {
                console.error('Lỗi khi tải dữ liệu:', error);
                showNotification('❌ Lỗi khi tải thông tin!', 'error');
            }
        }

        function showTab(tabName) {
            // Ẩn tất cả tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Hiển thị tab được chọn
            document.getElementById(tabName + 'Tab').style.display = 'block';
            
            // Cập nhật trạng thái active cho tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Load dữ liệu cho tab được chọn
            if (tabName === 'recent') {
                loadRecentHistory();
            } else if (tabName === 'favorites') {
                displayFavorites();
            }
        }

        // ==================== HÀM THIÊN VĂN ====================

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

        function formatDL(d, m, y) { return `${String(d).padStart(2, '0')}-${String(m).padStart(2, '0')}-${y}`; }
        function formatAL(d, m, y, leap) { return `${String(d).padStart(2, '0')}-${String(m).padStart(2, '0')}-${y}${leap ? ' (nhuận)' : ''}`; }

        function zodiacSign(d, m) {
            const z = [["Ma Kết", 20], ["Bảo Bình", 19], ["Song Ngư", 20], ["Bạch Dương", 20], ["Kim Ngưu", 21], ["Song Tử", 21], ["Cự Giải", 22], ["Sư Tử", 22], ["Xử Nữ", 22], ["Thiên Bình", 23], ["Bọ Cạp", 22], ["Nhân Mã", 21], ["Ma Kết", 31]];
            return (d <= z[m - 1][1]) ? z[m - 1][0] : z[m][0];
        }

        // ==================== HÀM TRA CỨU CHÍNH ====================

        function lookupBirth() {
            const d = parseInt(document.getElementById('birthDay').value);
            const m = parseInt(document.getElementById('birthMonth').value);
            const y = parseInt(document.getElementById('birthYear').value);
           
            if (!d || !m || !y || d < 1 || d > 31 || m < 1 || m > 12 || y < 1900 || y > 2100) {
                showNotification('❌ Vui lòng nhập ngày/tháng/năm sinh hợp lệ!', 'error');
                return;
            }
           
            const [ld, lm, ly, leap] = convertSolar2Lunar(d, m, y);
            const jdn = jdFromDate(d, m, y);
            const [canD, chiD] = canChiOfDay(jdn);
            const [canM, chiM] = canChiOfMonth(lm, ly);
            const [canY, chiY] = canChiOfYear(ly);
            const key = `${canY} ${chiY}`;
            const nap = NAP_AM[key] || { ten: "—", hanh: HANH_CAN[canY] };
            const hanh = nap.hanh;
            const pair = MAU_HOP_KY[hanh] || ['-', '-'];
            const zodiac = zodiacSign(d, m);
            const huong = HUONG_HOP[hanh] || { tot: ['—'], ky: ['—'] };
           
            currentBirthResult = {
                day: d,
                month: m,
                year: y,
                lunarDay: ld,
                lunarMonth: lm,
                lunarYear: ly,
                lunarLeap: leap,
                canD: canD,
                chiD: chiD,
                canM: canM,
                chiM: chiM,
                canY: canY,
                chiY: chiY,
                napAm: nap.ten,
                hanh: hanh,
                zodiac: zodiac,
                huongTot: huong.tot,
                huongKy: huong.ky,
                mauHop: pair[0],
                mauKy: pair[1]
            };
           
            console.log('Kết quả tra cứu:', currentBirthResult);
           
            // Lưu lịch sử nếu đã đăng nhập
            if (currentUser) {
                saveToSearchHistory(currentBirthResult);
            }
           
            document.getElementById('birthResult').innerHTML = `
                <div class="result">
                    <button class="close-btn" onclick="closeResult('birthResult')">Đóng ✕</button>
                    ${currentUser ? `<button id="favoriteBtn" class="favorite-btn" onclick="saveToFavorites()">
                        <span>⭐</span> Lưu tra cứu này
                    </button>` : '<p style="color: #666; font-style: italic;">🔐 Đăng nhập để lưu tra cứu này</p>'}
                    <h3>✨ Thông tin ngày sinh</h3>
                    <div class="result-grid">
                        <div class="result-column">
                            <div class="result-item">
                                <h4>📅 Dương lịch</h4>
                                <div class="result-value">${formatDL(d, m, y)}</div>
                            </div>
                            <div class="result-item">
                                <h4>📆 Can Chi năm</h4>
                                <div class="result-value">${canY} ${chiY}</div>
                            </div>
                            <div class="result-item">
                                <h4>📊 Can Chi ngày</h4>
                                <div class="result-value">${canD} ${chiD}</div>
                            </div>
                            <div class="result-item">
                                <h4>🌸 Niên mệnh</h4>
                                <div class="result-value">${nap.ten} - Hành: ${hanh}</div>
                            </div>
                        </div>
                        <div class="result-column">
                            <div class="result-item">
                                <h4>🌙 Âm lịch</h4>
                                <div class="result-value">${formatAL(ld, lm, ly, leap)}</div>
                            </div>
                            <div class="result-item">
                                <h4>📆 Can Chi tháng</h4>
                                <div class="result-value">${canM} ${chiM}</div>
                            </div>
                            <div class="result-item">
                                <h4>🔯 Cung hoàng đạo</h4>
                                <div class="result-value">${zodiac}</div>
                            </div>
                            <div class="result-item">
                                <h4>🌈 Màu hợp/kỵ</h4>
                                <div class="result-value">Hợp: <span class="color-hop">${pair[0]}</span><br>Kỵ: <span class="color-ky">${pair[1]}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="info">
                        <strong>🧭 Hướng hợp theo mệnh</strong>
                        <p>✅ Tốt: ${huong.tot.join(', ')}<br>❌ Kỵ: ${huong.ky.join(', ')}</p>
                    </div>
                    <div class="info">
                        <strong>💡 Lời khuyên:</strong><br>
                        - Nên sử dụng màu <span class="color-hop">${pair[0]}</span> trong trang phục, trang trí<br>
                        - Hướng tốt cho nhà ở, bàn làm việc: ${huong.tot.join(', ')}<br>
                        - Tránh sử dụng màu <span class="color-ky">${pair[1]}</span> làm màu chủ đạo
                    </div>
                </div>
            `;
        }

        function closeResult(id) {
            document.getElementById(id).innerHTML = '';
        }

        // ==================== HỆ THỐNG ĐĂNG NHẬP ====================

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3500);
        }

        function initializeApp() {
            fetchCurrentUser();
            initializeEventListeners();
            // Load dữ liệu ngay khi khởi tạo
            loadRecentHistory();
            displayFavorites();
        }

        function fetchCurrentUser() {
            fetch('api/get_current_user.php')
                .then(res => res.json())
                .then(data => {
                    console.log('User data:', data);
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

                        // Load lại dữ liệu khi đã có user
                        loadRecentHistory();
                        displayFavorites();
                    } else {
                        currentUser = null;
                        document.getElementById('user-info').style.display = 'none';
                        document.getElementById('auth-buttons').style.display = 'flex';
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
                showNotification('❌ Vui lòng nhập đầy đủ thông tin đăng nhập!', 'error');
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
                    // Load lại dữ liệu sau khi đăng nhập
                    loadRecentHistory();
                    displayFavorites();
                    showNotification('✅ ' + (data.message || 'Đăng nhập thành công!'), 'success');
                } else {
                    showNotification('❌ ' + (data.message || 'Email hoặc mật khẩu không đúng!'), 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('❌ Lỗi kết nối server!', 'error');
            }
        }
        
        async function performRegister() {
            const name = document.getElementById('register-name').value.trim();
            const email = document.getElementById('register-email').value.trim();
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;

            if (!name || !email || !password || !confirmPassword) {
                showNotification('❌ Vui lòng điền đầy đủ thông tin!', 'error');
                return;
            }

            if (password.length < 6) {
                showNotification('❌ Mật khẩu phải có ít nhất 6 ký tự!', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showNotification('❌ Mật khẩu xác nhận không khớp!', 'error');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('❌ Email không hợp lệ!', 'error');
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
                    // Load lại dữ liệu sau khi đăng ký
                    loadRecentHistory();
                    displayFavorites();
                    showNotification('✅ ' + (data.message || 'Đăng ký thành công!'), 'success');
                } else {
                    showNotification('❌ ' + (data.message || 'Đăng ký thất bại!'), 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('❌ Lỗi kết nối server!', 'error');
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
                    // Load lại dữ liệu sau khi đăng xuất
                    loadRecentHistory();
                    displayFavorites();
                    showNotification('✅ ' + (data.message || 'Đã đăng xuất thành công!'), 'success');
                })
                .catch(err => {
                    console.error(err);
                    showNotification('❌ Lỗi kết nối server!', 'error');
                });
        }

        function showProfileModal() {
            window.location.href = 'user.php';
        }

        // Khởi tạo ứng dụng khi trang load
        document.addEventListener('DOMContentLoaded', initializeApp);
    </script>
</body>
</html>
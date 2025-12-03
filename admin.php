<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// LẤY THÔNG TIN ADMIN
$admin_name = $_SESSION['user']['name'] ?? 'Quản trị viên';
$admin_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Việt - Trang Quản trị</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div id="toast-message"></div>
    </div>

    <!-- Modal for Holiday Management -->
    <div id="holiday-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="holiday-modal-title">Thêm ngày lễ</h3>
                <button class="close-btn" onclick="closeModal('holiday-modal')">&times;</button>
            </div>
            <form id="holiday-form">
                <div class="form-group">
                    <label for="holiday-name">📅 Tên ngày lễ</label>
                    <input type="text" id="holiday-name" class="form-control" placeholder="Nhập tên ngày lễ" required>
                </div>
                <div class="form-group">
                    <label for="holiday-calendar-type">🗓️ Loại lịch</label>
                    <select id="holiday-calendar-type" class="form-control" onchange="toggleDateInput()" required>
                        <option value="solar">Dương lịch (cố định ngày)</option>
                        <option value="lunar">Âm lịch (tự động tính)</option>
                    </select>
                </div>
                <div class="form-group" id="solar-date-group">
                    <label for="holiday-solar-date">📆 Ngày dương lịch</label>
                    <input type="date" id="holiday-solar-date" class="form-control">
                </div>
                <div class="form-group" id="lunar-date-group" style="display: none;">
                    <label for="holiday-lunar-date">🌙 Ngày âm lịch</label>
                    <div style="display: flex; gap: 10px;">
                        <select id="holiday-lunar-month" class="form-control">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>">Tháng <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select id="holiday-lunar-day" class="form-control">
                            <!-- Days will be populated by JavaScript -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="holiday-type">🎭 Loại ngày lễ</label>
                    <select id="holiday-type" class="form-control" required>
                        <option value="national">Quốc lễ</option>
                        <option value="religious">Tôn giáo</option>
                        <option value="traditional">Truyền thống</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="holiday-description">📝 Mô tả</label>
                    <textarea id="holiday-description" class="form-control" rows="3" placeholder="Nhập mô tả ngày lễ"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('holiday-modal')">❌ Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for User Management -->
    <div id="user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="user-modal-title">Thêm người dùng</h3>
                <button class="close-btn" onclick="closeModal('user-modal')">&times;</button>
            </div>
            <form id="user-form">
                <div class="form-group">
                    <label for="user-name">👤 Họ tên</label>
                    <input type="text" id="user-name" class="form-control" placeholder="Nhập họ tên" required>
                </div>
                <div class="form-group">
                    <label for="user-email">📧 Email</label>
                    <input type="email" id="user-email" class="form-control" placeholder="Nhập email" required>
                </div>
                <div class="form-group">
                    <label for="user-password">🔒 Mật khẩu</label>
                    <input type="password" id="user-password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
                <div class="form-group">
                    <label for="user-role">🎭 Vai trò</label>
                    <select id="user-role" class="form-control" required>
                        <option value="user">Người dùng</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('user-modal')">❌ Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Event Management -->
    <div id="event-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="event-modal-title">Thêm sự kiện</h3>
                <button class="close-btn" onclick="closeModal('event-modal')">&times;</button>
            </div>
            <form id="event-form">
                <div class="form-group">
                    <label for="event-title">📝 Tiêu đề sự kiện</label>
                    <input type="text" id="event-title" class="form-control" placeholder="Nhập tiêu đề sự kiện" required>
                </div>
                <div class="form-group">
                    <label for="event-description">📄 Mô tả ngắn</label>
                    <textarea id="event-description" class="form-control" rows="2" placeholder="Nhập mô tả ngắn về sự kiện"></textarea>
                </div>
                <div class="form-group">
                    <label for="event-content">📖 Nội dung chi tiết</label>
                    <textarea id="event-content" class="form-control" rows="4" placeholder="Nhập nội dung chi tiết về sự kiện"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="event-type">🎭 Loại sự kiện</label>
                        <select id="event-type" class="form-control" required>
                            <option value="community">Sự kiện cộng đồng</option>
                            <option value="promotion">Khuyến mãi</option>
                            <option value="system_update">Cập nhật hệ thống</option>
                            <option value="announcement">Thông báo</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event-status">📊 Trạng thái</label>
                        <select id="event-status" class="form-control" required>
                            <option value="draft">Nháp</option>
                            <option value="published">Đã công bố</option>
                            <option value="archived">Lưu trữ</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="event-start-date">📅 Ngày bắt đầu</label>
                        <input type="date" id="event-start-date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="event-end-date">📅 Ngày kết thúc</label>
                        <input type="date" id="event-end-date" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="event-location">📍 Địa điểm</label>
                    <input type="text" id="event-location" class="form-control" placeholder="Nhập địa điểm tổ chức">
                </div>
                <div class="form-group">
                    <label for="event-image-url">🖼️ URL hình ảnh</label>
                    <input type="url" id="event-image-url" class="form-control" placeholder="https://example.com/image.jpg">
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="event-featured"> 
                        🎯 Sự kiện nổi bật
                    </label>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('event-modal')">❌ Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu sự kiện</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>📅 <span>LỊCH VIỆT</span></h1>
            <p>Trang Quản trị</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="#" class="menu-item active" data-section="dashboard">
                <i>📊</i>
                <span>Tổng quan</span>
            </a>
            <a href="#" class="menu-item" data-section="users">
                <i>👥</i>
                <span>Người dùng</span>
            </a>
            <a href="#" class="menu-item" data-section="holidays">
                <i>📅</i>
                <span>Ngày lễ</span>
            </a>
            <a href="#" class="menu-item" data-section="events">
                <i>🎉</i>
                <span>Sự kiện</span>
            </a>
            
            <div class="menu-divider"></div>
            
            <a href="index.php" class="menu-item">
                <i>🏠</i>
                <span>Về trang chủ</span>
            </a>
        </div>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($admin_name, 0, 1)); ?></div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($admin_name); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <button class="logout-btn" onclick="logout()">
                <span>🚪 Đăng xuất</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <div>
                <h1 class="page-title" id="page-title">Tổng quan hệ thống</h1>
                <div class="breadcrumb">
                    <a href="index.php">Trang chủ</a> / <span id="breadcrumb-current">Tổng quan</span>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn btn-info" onclick="refreshAllData()">🔄 Làm mới</button>
            </div>
        </div>
        
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="admin-section active">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="total-users">0</div>
                    <div class="stat-label">👥 Tổng người dùng</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="total-holidays">0</div>
                    <div class="stat-label">📅 Ngày lễ</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="active-users">0</div>
                    <div class="stat-label">✅ Đang hoạt động</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="admin-users">0</div>
                    <div class="stat-label">👑 Quản trị viên</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="total-events">0</div>
                    <div class="stat-label">🎉 Sự kiện</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="published-events">0</div>
                    <div class="stat-label">✅ Sự kiện đã công bố</div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="chart-container">
                    <h4>📌 Hoạt động gần đây</h4>
                    <div class="log-container" id="recent-activities">
                        <div class="loading">Đang tải...</div>
                    </div>
                </div>
                <div class="chart-container">
                    <h4>📈 Thống kê hệ thống</h4>
                    <div class="stats-placeholder">
                        <div class="system-stat">
                            <span class="stat-label">Phiên bản hệ thống:</span>
                            <span class="stat-value">Lịch Việt 2.0</span>
                        </div>
                        <div class="system-stat">
                            <span class="stat-label">Thời gian hoạt động:</span>
                            <span class="stat-value" id="uptime">Đang tải...</span>
                        </div>
                        <div class="system-stat">
                            <span class="stat-label">Dung lượng database:</span>
                            <span class="stat-value" id="db-size">Đang tải...</span>
                        </div>
                        <div class="system-stat">
                            <span class="stat-label">Sự kiện nổi bật:</span>
                            <span class="stat-value" id="featured-events">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users Section -->
        <div id="users-section" class="admin-section">
            <div class="section-header">
                <h3 class="section-title">👥 Quản lý người dùng</h3>
                <div class="section-actions">
                    <button class="btn btn-success" onclick="openUserModal()">➕ Thêm người dùng</button>
                    <button class="btn btn-secondary" onclick="loadUsersData()">🔄 Làm mới</button>
                </div>
            </div>
            
            <div class="filters-row">
                <div class="search-box">
                    <input type="text" id="user-search" class="search-input" placeholder="🔍 Tìm kiếm người dùng...">
                </div>
                <select id="user-role-filter" class="filter-select" onchange="loadUsersData()">
                    <option value="all">Tất cả vai trò</option>
                    <option value="user">Người dùng</option>
                    <option value="admin">Quản trị viên</option>
                </select>
                <select id="user-status-filter" class="filter-select" onchange="loadUsersData()">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="1">Đang hoạt động</option>
                    <option value="0">Đã khóa</option>
                </select>
            </div>
            
            <div class="table-container">
                <table class="data-table" id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>👤 Họ tên</th>
                            <th>📧 Email</th>
                            <th>🎭 Vai trò</th>
                            <th>📊 Trạng thái</th>
                            <th>📅 Ngày đăng ký</th>
                            <th>⚡ Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="7" class="text-center">Đang tải dữ liệu...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Holidays Section -->
        <div id="holidays-section" class="admin-section">
            <div class="section-header">
                <h3 class="section-title">📅 Quản lý ngày lễ</h3>
                <div class="section-actions">
                    <button class="btn btn-success" onclick="openHolidayModal()">➕ Thêm ngày lễ</button>
                    <button class="btn btn-secondary" onclick="loadHolidaysData()">🔄 Làm mới</button>
                </div>
            </div>
            
            <div class="filters-row">
                <div class="year-selector">
                    <label for="year-select"><strong>Năm hiển thị:</strong></label>
                    <select id="year-select" class="year-select" onchange="loadHolidaysData()">
                        <!-- Years will be populated by JavaScript -->
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="holiday-search" class="search-input" placeholder="🔍 Tìm kiếm ngày lễ...">
                </div>
                <select id="holiday-type-filter" class="filter-select" onchange="loadHolidaysData()">
                    <option value="all">Tất cả loại</option>
                    <option value="national">Quốc lễ</option>
                    <option value="religious">Tôn giáo</option>
                    <option value="traditional">Truyền thống</option>
                    <option value="other">Khác</option>
                </select>
                <select id="holiday-calendar-filter" class="filter-select" onchange="loadHolidaysData()">
                    <option value="all">Tất cả lịch</option>
                    <option value="solar">Dương lịch</option>
                    <option value="lunar">Âm lịch</option>
                </select>
            </div>
            
            <div id="holidays-container">
                <div class="loading">Đang tải danh sách ngày lễ...</div>
            </div>
        </div>

        <!-- Events Section -->
        <div id="events-section" class="admin-section">
            <div class="section-header">
                <h3 class="section-title">🎉 Quản lý sự kiện</h3>
                <div class="section-actions">
                    <button class="btn btn-success" onclick="openEventModal()">➕ Thêm sự kiện</button>
                    <button class="btn btn-secondary" onclick="loadEventsData()">🔄 Làm mới</button>
                </div>
            </div>
            
            <div class="filters-row">
                <div class="year-selector">
                    <label for="event-year-select"><strong>Năm hiển thị:</strong></label>
                    <select id="event-year-select" class="year-select" onchange="loadEventsData()">
                        <!-- Years will be populated by JavaScript -->
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="event-search" class="search-input" placeholder="🔍 Tìm kiếm sự kiện...">
                </div>
                <select id="event-type-filter" class="filter-select" onchange="loadEventsData()">
                    <option value="all">Tất cả loại</option>
                    <option value="community">Cộng đồng</option>
                    <option value="promotion">Khuyến mãi</option>
                    <option value="system_update">Cập nhật hệ thống</option>
                    <option value="announcement">Thông báo</option>
                    <option value="other">Khác</option>
                </select>
                <select id="event-status-filter" class="filter-select" onchange="loadEventsData()">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="draft">Nháp</option>
                    <option value="published">Đã công bố</option>
                    <option value="archived">Lưu trữ</option>
                </select>
            </div>
            
            <div id="events-container">
                <div class="loading">Đang tải danh sách sự kiện...</div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentHolidays = [];
        let currentUsers = [];
        let currentEvents = [];

        // Initialize app
        document.addEventListener('DOMContentLoaded', () => {
            initEventListeners();
            initializeYearSelectors();
            populateLunarDays();
            loadDashboardData();
            loadUsersData();
            loadHolidaysData();
            loadEventsData();
        });

        // Initialize event listeners
        function initEventListeners() {
            // Sidebar menu
            const menuItems = document.querySelectorAll('.menu-item[data-section]');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    showSection(section);
                });
            });

            // Form submissions
            const holidayForm = document.getElementById('holiday-form');
            if (holidayForm) {
                holidayForm.addEventListener('submit', handleHolidayForm);
            }

            const userForm = document.getElementById('user-form');
            if (userForm) {
                userForm.addEventListener('submit', handleUserForm);
            }

            const eventForm = document.getElementById('event-form');
            if (eventForm) {
                eventForm.addEventListener('submit', handleEventForm);
            }

            // Search functionality
            const holidaySearch = document.getElementById('holiday-search');
            if (holidaySearch) {
                holidaySearch.addEventListener('input', debounce(() => loadHolidaysData(), 300));
            }

            const userSearch = document.getElementById('user-search');
            if (userSearch) {
                userSearch.addEventListener('input', debounce(() => loadUsersData(), 300));
            }

            const eventSearch = document.getElementById('event-search');
            if (eventSearch) {
                eventSearch.addEventListener('input', debounce(() => loadEventsData(), 300));
            }

            // Lunar month change
            const lunarMonthSelect = document.getElementById('holiday-lunar-month');
            if (lunarMonthSelect) {
                lunarMonthSelect.addEventListener('change', populateLunarDays);
            }

            // Close modal on backdrop click
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this.id);
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', handleKeyboardShortcuts);
        }

        // Keyboard shortcuts
        function handleKeyboardShortcuts(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const activeSection = document.querySelector('.admin-section.active');
                if (activeSection.id === 'holidays-section') {
                    document.getElementById('holiday-search').focus();
                } else if (activeSection.id === 'users-section') {
                    document.getElementById('user-search').focus();
                } else if (activeSection.id === 'events-section') {
                    document.getElementById('event-search').focus();
                }
            }
            
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (modal.style.display === 'flex') {
                        closeModal(modal.id);
                    }
                });
            }
        }

        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Initialize year selectors
        function initializeYearSelectors() {
            const yearSelect = document.getElementById('year-select');
            const eventYearSelect = document.getElementById('event-year-select');
            const currentYear = new Date().getFullYear();
            
            [yearSelect, eventYearSelect].forEach(select => {
                if (select) {
                    select.innerHTML = '';
                    for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                        const option = document.createElement('option');
                        option.value = year;
                        option.textContent = year;
                        if (year === currentYear) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    }
                }
            });
        }

        // Populate lunar days based on selected month
        function populateLunarDays() {
            const lunarMonth = document.getElementById('holiday-lunar-month').value;
            const lunarDaySelect = document.getElementById('holiday-lunar-day');
            
            lunarDaySelect.innerHTML = '';
            const daysInMonth = (lunarMonth == 2 ? 29 : 30);
            
            for (let day = 1; day <= daysInMonth; day++) {
                const option = document.createElement('option');
                option.value = day;
                option.textContent = `Ngày ${day}`;
                lunarDaySelect.appendChild(option);
            }
        }

        // Toggle date input based on calendar type
        function toggleDateInput() {
            const calendarType = document.getElementById('holiday-calendar-type').value;
            const solarDateGroup = document.getElementById('solar-date-group');
            const lunarDateGroup = document.getElementById('lunar-date-group');
            
            if (calendarType === 'solar') {
                solarDateGroup.style.display = 'block';
                lunarDateGroup.style.display = 'none';
            } else {
                solarDateGroup.style.display = 'none';
                lunarDateGroup.style.display = 'block';
            }
        }

        // Show section
        function showSection(sectionName) {
            // Update active menu item
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-section') === sectionName) {
                    item.classList.add('active');
                }
            });
            
            // Hide all sections
            const sections = document.querySelectorAll('.admin-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            const targetSection = document.getElementById(`${sectionName}-section`);
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            // Update page title and breadcrumb
            updatePageTitle(sectionName);
        }

        // Update page title
        function updatePageTitle(sectionName) {
            const titles = {
                'dashboard': 'Tổng quan hệ thống',
                'users': 'Quản lý người dùng',
                'holidays': 'Quản lý ngày lễ',
                'events': 'Quản lý sự kiện'
            };
            
            const breadcrumbs = {
                'dashboard': 'Tổng quan',
                'users': 'Người dùng',
                'holidays': 'Ngày lễ',
                'events': 'Sự kiện'
            };
            
            document.getElementById('page-title').textContent = titles[sectionName] || 'Trang quản trị';
            document.getElementById('breadcrumb-current').textContent = breadcrumbs[sectionName] || 'Trang chủ';
        }

        // Load dashboard data
        async function loadDashboardData() {
            try {
                // Simulate API call - replace with actual API
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // Update with actual data from API
                document.getElementById('total-users').textContent = '7';
                document.getElementById('total-holidays').textContent = '28';
                document.getElementById('active-users').textContent = '7';
                document.getElementById('admin-users').textContent = '2';
                document.getElementById('total-events').textContent = '3';
                document.getElementById('published-events').textContent = '2';
                document.getElementById('featured-events').textContent = '1';
                document.getElementById('uptime').textContent = new Date().toLocaleString('vi-VN');
                document.getElementById('db-size').textContent = '2.5 MB';
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showToast('Lỗi tải dữ liệu tổng quan', 'error');
            }
        }

        // Load users data
        async function loadUsersData() {
            try {
                showLoading('users-table-body', 7);
                
                // Simulate API call - replace with actual API
                await new Promise(resolve => setTimeout(resolve, 800));
                
                // Sample data - replace with actual API response
                const sampleUsers = [
                    { id: 1, name: 'Trần Quỳnh Nhi', email: 'nhi@example.com', role: 'user', status: 1, created_at: '2025-11-19 07:53:36' },
                    { id: 5, name: 'LZ', email: 'zcj@gmail.com', role: 'user', status: 1, created_at: '2025-11-19 10:02:07' },
                    { id: 6, name: 'hehehe', email: 'trqanchautran@gmail.com', role: 'admin', status: 1, created_at: '2025-11-19 10:12:20' },
                    { id: 7, name: 'eeee', email: 'GRRGRGR@gmail.com', role: 'user', status: 1, created_at: '2025-11-21 15:07:32' }
                ];
                
                currentUsers = sampleUsers;
                renderUsersTable(currentUsers);
                
            } catch (error) {
                console.error('Error loading users:', error);
                showToast('Lỗi tải danh sách người dùng', 'error');
                
                const tbody = document.getElementById('users-table-body');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center error-message">
                            ❌ Lỗi tải dữ liệu: ${error.message}
                            <br><button class="btn btn-secondary" onclick="loadUsersData()">🔄 Thử lại</button>
                        </td>
                    </tr>
                `;
            }
        }

        // Load holidays data
        async function loadHolidaysData() {
            try {
                showLoading('holidays-container');
                
                const year = document.getElementById('year-select').value;
                const search = document.getElementById('holiday-search').value;
                const type = document.getElementById('holiday-type-filter').value;
                const calendar = document.getElementById('holiday-calendar-filter').value;
                
                // Build API URL với các tham số lọc
                const params = new URLSearchParams({ year });
                if (search) params.append('search', search);
                if (type !== 'all') params.append('type', type);
                if (calendar !== 'all') params.append('calendar', calendar);
                
                // Gọi API thực tế
                const response = await fetch(`api/admin/get_holidays.php?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    currentHolidays = result.data;
                    renderHolidaysList(currentHolidays);
                } else {
                    throw new Error(result.message);
                }
                
            } catch (error) {
                console.error('Error loading holidays:', error);
                const container = document.getElementById('holidays-container');
                container.innerHTML = `
                    <div class="error-message">
                        <div>❌ Lỗi tải dữ liệu: ${error.message}</div>
                        <button class="btn btn-secondary" onclick="loadHolidaysData()">🔄 Thử lại</button>
                    </div>
                `;
            }
        }

        // Load events data
        async function loadEventsData() {
            try {
                showLoading('events-container');
                
                const year = document.getElementById('event-year-select').value;
                const search = document.getElementById('event-search').value;
                const event_type = document.getElementById('event-type-filter').value;
                const status = document.getElementById('event-status-filter').value;
                
                const params = new URLSearchParams({ year });
                if (search) params.append('search', search);
                if (event_type !== 'all') params.append('event_type', event_type);
                if (status !== 'all') params.append('status', status);
                
                const response = await fetch(`api/admin/events_list.php?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    currentEvents = result.data;
                    renderEventsList(currentEvents);
                } else {
                    throw new Error(result.message);
                }
                
            } catch (error) {
                console.error('Error loading events:', error);
                const container = document.getElementById('events-container');
                container.innerHTML = `
                    <div class="error-message">
                        <div>❌ Lỗi tải dữ liệu: ${error.message}</div>
                        <button class="btn btn-secondary" onclick="loadEventsData()">🔄 Thử lại</button>
                    </div>
                `;
            }
        }

        // Render users table
        function renderUsersTable(users) {
            const tbody = document.getElementById('users-table-body');
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">Không tìm thấy người dùng nào</td></tr>';
                return;
            }
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>
                        <div class="user-info-small">
                            <div class="user-avatar-small">${user.name.charAt(0).toUpperCase()}</div>
                            <div>${user.name}</div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="user-badge ${user.role}">
                            ${user.role === 'admin' ? '👑 Quản trị' : '👤 Người dùng'}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge ${user.status ? 'active' : 'inactive'}">
                            ${user.status ? '🟢 Đang hoạt động' : '🔴 Đã khóa'}
                        </span>
                    </td>
                    <td>${new Date(user.created_at).toLocaleDateString('vi-VN')}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-sm btn-info" onclick="editUser(${user.id})">✏️ Sửa</button>
                            <button class="btn-sm btn-danger" onclick="deleteUser(${user.id})">🗑️ Xóa</button>
                            ${user.status ? 
                                '<button class="btn-sm btn-warning" onclick="toggleUserStatus(' + user.id + ', 0)">🚫 Khóa</button>' :
                                '<button class="btn-sm btn-success" onclick="toggleUserStatus(' + user.id + ', 1)">✅ Mở khóa</button>'
                            }
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Render holidays list
        function renderHolidaysList(holidays) {
            const container = document.getElementById('holidays-container');
            
            if (holidays.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <h3>Không tìm thấy ngày lễ nào</h3>
                        <p>Hãy thử thay đổi bộ lọc hoặc thêm ngày lễ mới</p>
                        <button class="btn btn-success" onclick="openHolidayModal()">➕ Thêm ngày lễ</button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = holidays.map(holiday => {
                const typeClass = holiday.type;
                const typeLabels = {
                    'national': '🇻🇳 Quốc lễ',
                    'religious': '🛐 Tôn giáo', 
                    'traditional': '🎎 Truyền thống',
                    'other': '📌 Khác'
                };
                
                const typeLabel = typeLabels[holiday.type] || 'Khác';
                const calendarType = holiday.is_lunar ? 'Âm lịch' : 'Dương lịch';
                const dateInfo = holiday.is_lunar ? 
                    `🌙 ${holiday.lunar_day}/${holiday.lunar_month} âm lịch` :
                    `📅 ${new Date(holiday.solar_date).toLocaleDateString('vi-VN')}`;
                
                return `
                    <div class="holiday-item">
                        <div class="holiday-info">
                            <div class="holiday-name">${holiday.name}</div>
                            <div class="holiday-meta">
                                <span class="holiday-date">${dateInfo}</span>
                                <span class="holiday-type ${typeClass}">${typeLabel}</span>
                                <span class="holiday-calendar">${calendarType}</span>
                            </div>
                            ${holiday.description ? `<div class="holiday-description">${holiday.description}</div>` : ''}
                            ${holiday.is_recurring ? '<div class="holiday-recurring">🔄 Tự động lặp lại hàng năm</div>' : ''}
                        </div>
                        <div class="holiday-actions">
                            <button class="btn-sm btn-info" onclick="editHoliday(${holiday.id})">✏️ Sửa</button>
                            <button class="btn-sm btn-danger" onclick="deleteHoliday(${holiday.id})">🗑️ Xóa</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Render events list
        function renderEventsList(events) {
            const container = document.getElementById('events-container');
            
            if (events.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">🎉</div>
                        <h3>Không tìm thấy sự kiện nào</h3>
                        <p>Hãy thử thay đổi bộ lọc hoặc thêm sự kiện mới</p>
                        <button class="btn btn-success" onclick="openEventModal()">➕ Thêm sự kiện</button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = events.map(event => {
                const typeClass = event.event_type;
                const typeLabels = {
                    'community': 'Cộng đồng',
                    'promotion': 'Khuyến mãi', 
                    'system_update': 'Cập nhật',
                    'announcement': 'Thông báo',
                    'other': 'Khác'
                };
                
                const statusClass = event.status;
                const statusLabels = {
                    'draft': '📝 Nháp',
                    'published': '✅ Đã công bố',
                    'archived': '📁 Lưu trữ'
                };
                
                const typeLabel = typeLabels[event.event_type] || 'Khác';
                const statusLabel = statusLabels[event.status] || event.status;
                
                return `
                    <div class="event-item">
                        <div class="event-info">
                            <div class="event-header">
                                <div class="event-title">${event.title}</div>
                                <div class="event-meta">
                                    <span class="event-type ${typeClass}">${typeLabel}</span>
                                    <span class="event-status ${statusClass}">${statusLabel}</span>
                                    ${event.is_featured ? '<span class="event-featured">🎯 Nổi bật</span>' : ''}
                                </div>
                            </div>
                            ${event.description ? `<div class="event-description">${event.description}</div>` : ''}
                            <div class="event-details">
                                ${event.start_date ? `<span class="event-date">📅 ${new Date(event.start_date).toLocaleDateString('vi-VN')}</span>` : ''}
                                ${event.location ? `<span class="event-location">📍 ${event.location}</span>` : ''}
                                <span class="event-author">👤 ${event.author_name || 'System'}</span>
                                <span class="event-created">🕒 ${new Date(event.created_at).toLocaleDateString('vi-VN')}</span>
                            </div>
                        </div>
                        <div class="event-actions">
                            <button class="btn-sm btn-info" onclick="editEvent(${event.id})">✏️ Sửa</button>
                            <button class="btn-sm btn-danger" onclick="deleteEvent(${event.id})">🗑️ Xóa</button>
                            ${event.is_featured ? 
                                '<button class="btn-sm btn-warning" onclick="toggleEventFeatured(' + event.id + ', 0)">⭐ Bỏ nổi bật</button>' :
                                '<button class="btn-sm btn-success" onclick="toggleEventFeatured(' + event.id + ', 1)">⭐ Nổi bật</button>'
                            }
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Open holiday modal
        function openHolidayModal(holiday = null) {
            const modal = document.getElementById('holiday-modal');
            const title = document.getElementById('holiday-modal-title');
            const form = document.getElementById('holiday-form');
            
            if (holiday) {
                title.textContent = 'Chỉnh sửa ngày lễ';
                document.getElementById('holiday-name').value = holiday.name;
                document.getElementById('holiday-type').value = holiday.type;
                document.getElementById('holiday-description').value = holiday.description || '';
                
                if (holiday.is_lunar) {
                    document.getElementById('holiday-calendar-type').value = 'lunar';
                    document.getElementById('holiday-lunar-month').value = holiday.lunar_month;
                    populateLunarDays();
                    document.getElementById('holiday-lunar-day').value = holiday.lunar_day;
                } else {
                    document.getElementById('holiday-calendar-type').value = 'solar';
                    document.getElementById('holiday-solar-date').value = holiday.solar_date;
                }
                
                toggleDateInput();
                form.dataset.editId = holiday.id;
            } else {
                title.textContent = 'Thêm ngày lễ';
                form.reset();
                const currentYear = new Date().getFullYear();
                document.getElementById('holiday-solar-date').value = `${currentYear}-01-01`;
                document.getElementById('holiday-type').value = 'traditional';
                toggleDateInput();
                delete form.dataset.editId;
            }
            
            modal.style.display = 'flex';
        }

        // Open user modal
        function openUserModal(user = null) {
            const modal = document.getElementById('user-modal');
            const title = document.getElementById('user-modal-title');
            const form = document.getElementById('user-form');
            
            if (user) {
                title.textContent = 'Chỉnh sửa người dùng';
                document.getElementById('user-name').value = user.name;
                document.getElementById('user-email').value = user.email;
                document.getElementById('user-role').value = user.role;
                document.getElementById('user-password').value = '';
                document.getElementById('user-password').placeholder = 'Để trống nếu không đổi mật khẩu';
                form.dataset.editId = user.id;
            } else {
                title.textContent = 'Thêm người dùng';
                form.reset();
                document.getElementById('user-role').value = 'user';
                delete form.dataset.editId;
            }
            
            modal.style.display = 'flex';
        }

        // Open event modal
        function openEventModal(event = null) {
            const modal = document.getElementById('event-modal');
            const title = document.getElementById('event-modal-title');
            const form = document.getElementById('event-form');
            
            if (event) {
                title.textContent = 'Chỉnh sửa sự kiện';
                document.getElementById('event-title').value = event.title;
                document.getElementById('event-description').value = event.description || '';
                document.getElementById('event-content').value = event.content || '';
                document.getElementById('event-type').value = event.event_type;
                document.getElementById('event-status').value = event.status;
                document.getElementById('event-start-date').value = event.start_date || '';
                document.getElementById('event-end-date').value = event.end_date || '';
                document.getElementById('event-location').value = event.location || '';
                document.getElementById('event-image-url').value = event.image_url || '';
                document.getElementById('event-featured').checked = event.is_featured;
                
                form.dataset.editId = event.id;
            } else {
                title.textContent = 'Thêm sự kiện';
                form.reset();
                document.getElementById('event-type').value = 'community';
                document.getElementById('event-status').value = 'draft';
                const currentDate = new Date().toISOString().split('T')[0];
                document.getElementById('event-start-date').value = currentDate;
                delete form.dataset.editId;
            }
            
            modal.style.display = 'flex';
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Handle holiday form submission
        async function handleHolidayForm(e) {
            e.preventDefault();
            
            const form = e.target;
            const name = document.getElementById('holiday-name').value.trim();
            const calendarType = document.getElementById('holiday-calendar-type').value;
            const type = document.getElementById('holiday-type').value;
            const description = document.getElementById('holiday-description').value.trim();
            
            if (!name) {
                showToast('Vui lòng nhập tên ngày lễ', 'error');
                return;
            }
            
            const holidayData = {
                name,
                type,
                description,
                is_lunar: calendarType === 'lunar'
            };
            
            if (calendarType === 'solar') {
                const solarDate = document.getElementById('holiday-solar-date').value;
                if (!solarDate) {
                    showToast('Vui lòng chọn ngày dương lịch', 'error');
                    return;
                }
                holidayData.solar_date = solarDate;
            } else {
                const lunarMonth = parseInt(document.getElementById('holiday-lunar-month').value);
                const lunarDay = parseInt(document.getElementById('holiday-lunar-day').value);
                holidayData.lunar_month = lunarMonth;
                holidayData.lunar_day = lunarDay;
            }
            
            try {
                let result;
                if (form.dataset.editId) {
                    holidayData.id = parseInt(form.dataset.editId);
                    result = await updateHoliday(holidayData);
                } else {
                    result = await createHoliday(holidayData);
                }
                
                if (result.success) {
                    closeModal('holiday-modal');
                    showToast(result.message, 'success');
                    loadHolidaysData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        // Handle user form submission
        async function handleUserForm(e) {
            e.preventDefault();
            
            const form = e.target;
            const name = document.getElementById('user-name').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const password = document.getElementById('user-password').value;
            const role = document.getElementById('user-role').value;
            
            if (!name || !email) {
                showToast('Vui lòng nhập đầy đủ thông tin', 'error');
                return;
            }
            
            if (!form.dataset.editId && !password) {
                showToast('Vui lòng nhập mật khẩu', 'error');
                return;
            }
            
            const userData = { name, email, role, status: 1 };
            if (password) {
                userData.password = password;
            }
            
            try {
                let result;
                if (form.dataset.editId) {
                    userData.id = parseInt(form.dataset.editId);
                    result = await updateUser(userData);
                } else {
                    result = await createUser(userData);
                }
                
                if (result.success) {
                    closeModal('user-modal');
                    showToast(result.message, 'success');
                    loadUsersData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        // Handle event form submission
        async function handleEventForm(e) {
            e.preventDefault();
            
            const form = e.target;
            const title = document.getElementById('event-title').value.trim();
            const description = document.getElementById('event-description').value.trim();
            const content = document.getElementById('event-content').value.trim();
            const event_type = document.getElementById('event-type').value;
            const status = document.getElementById('event-status').value;
            const start_date = document.getElementById('event-start-date').value;
            const end_date = document.getElementById('event-end-date').value;
            const location = document.getElementById('event-location').value.trim();
            const image_url = document.getElementById('event-image-url').value.trim();
            const is_featured = document.getElementById('event-featured').checked ? 1 : 0;
            
            if (!title) {
                showToast('Vui lòng nhập tiêu đề sự kiện', 'error');
                return;
            }
            
            const eventData = {
                title,
                description,
                content,
                event_type,
                status,
                start_date: start_date || null,
                end_date: end_date || null,
                location,
                image_url,
                is_featured
            };
            
            try {
                let result;
                if (form.dataset.editId) {
                    eventData.id = parseInt(form.dataset.editId);
                    result = await updateEvent(eventData);
                } else {
                    result = await createEvent(eventData);
                }
                
                if (result.success) {
                    closeModal('event-modal');
                    showToast(result.message, 'success');
                    loadEventsData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        // API functions - Gọi API thực tế
        async function createHoliday(holidayData) {
            const response = await fetch('api/admin/holiday_create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(holidayData)
            });
            return await response.json();
        }

        async function updateHoliday(holidayData) {
            const response = await fetch('api/admin/holiday_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(holidayData)
            });
            return await response.json();
        }

        async function deleteHoliday(holidayId) {
            if (!confirm('Bạn có chắc chắn muốn xóa ngày lễ này?')) return;
            
            try {
                const response = await fetch('api/admin/holiday_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: holidayId })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Đã xóa ngày lễ thành công', 'success');
                    loadHolidaysData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        async function createUser(userData) {
            const response = await fetch('api/admin/user_create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            return await response.json();
        }

        async function updateUser(userData) {
            const response = await fetch('api/admin/user_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            return await response.json();
        }

        async function deleteUser(userId) {
            if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) return;
            
            try {
                const response = await fetch('api/admin/user_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: userId })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Đã xóa người dùng thành công', 'success');
                    loadUsersData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        async function toggleUserStatus(userId, status) {
            const action = status ? 'mở khóa' : 'khóa';
            if (!confirm(`Bạn có chắc chắn muốn ${action} người dùng này?`)) return;
            
            try {
                const response = await fetch('api/admin/user_toggle_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: userId, status: status })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(`Đã ${action} người dùng thành công`, 'success');
                    loadUsersData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        async function createEvent(eventData) {
            const response = await fetch('api/admin/events_create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(eventData)
            });
            return await response.json();
        }

        async function updateEvent(eventData) {
            const response = await fetch('api/admin/events_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(eventData)
            });
            return await response.json();
        }

        async function deleteEvent(eventId) {
            if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) return;
            
            try {
                const response = await fetch('api/admin/events_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: eventId })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Đã xóa sự kiện thành công', 'success');
                    loadEventsData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        async function toggleEventFeatured(eventId, is_featured) {
            try {
                const response = await fetch('api/admin/events_toggle_featured.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: eventId, is_featured: is_featured })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    loadEventsData();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Lỗi: ' + error.message, 'error');
            }
        }

        // Edit functions
        function editHoliday(holidayId) {
            const holiday = currentHolidays.find(h => h.id === holidayId);
            if (holiday) {
                openHolidayModal(holiday);
            }
        }

        function editUser(userId) {
            const user = currentUsers.find(u => u.id === userId);
            if (user) {
                openUserModal(user);
            }
        }

        function editEvent(eventId) {
            const event = currentEvents.find(e => e.id === eventId);
            if (event) {
                openEventModal(event);
            }
        }

        // Refresh all data
        function refreshAllData() {
            loadDashboardData();
            loadUsersData();
            loadHolidaysData();
            loadEventsData();
            showToast('Đã làm mới tất cả dữ liệu', 'success');
        }

        // Show loading indicator
        function showLoading(containerId, colspan = 1) {
            const container = document.getElementById(containerId);
            if (container.tagName === 'TBODY') {
                container.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="text-center">
                            <div class="loading-container">
                                <div class="loading"></div>
                                <div>Đang tải dữ liệu...</div>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                container.innerHTML = `
                    <div class="loading-container">
                        <div class="loading"></div>
                        <div>Đang tải dữ liệu...</div>
                    </div>
                `;
            }
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            toastMessage.textContent = message;
            toast.className = `toast ${type} show`;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Logout function
        function logout() {
            if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                fetch('logout.php')
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showToast('Đã đăng xuất thành công! 👋', 'success');
                            setTimeout(() => {
                                window.location.href = result.redirect || 'index.php';
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Logout error:', error);
                        window.location.href = 'index.php';
                    });
            }
        }

        // Auto-hide toast on click
        document.getElementById('toast').addEventListener('click', function() {
            this.classList.remove('show');
        });
    </script>
</body>
</html>
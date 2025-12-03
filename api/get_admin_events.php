<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

try {
    $current_year = date('Y');
    
    // Lấy sự kiện từ admin
    $events_sql = "SELECT * FROM system_events WHERE status = 'published' ORDER BY start_date ASC";
    $events_result = $conn->query($events_sql);
    $events = [];
    
    if ($events_result && $events_result->num_rows > 0) {
        while ($row = $events_result->fetch_assoc()) {
            $events[] = [
                'id' => 'admin_' . $row['id'],
                'title' => $row['title'],
                'date' => $row['start_date'],
                'type' => 'admin_event',
                'event_type' => $row['event_type'],
                'is_featured' => (bool)$row['is_featured'],
                'description' => $row['description'],
                'location' => $row['location'],
                'image_url' => $row['image_url']
            ];
        }
    }
    
    // Lấy ngày lễ - SỬA ĐỔI QUAN TRỌNG
    $holidays_sql = "SELECT * FROM holidays WHERE 1";
    $holidays_result = $conn->query($holidays_sql);
    $holidays = [];
    
    if ($holidays_result && $holidays_result->num_rows > 0) {
        while ($row = $holidays_result->fetch_assoc()) {
            $holiday_data = [
                'id' => 'holiday_' . $row['id'],
                'name' => $row['name'],
                'is_lunar' => (bool)$row['is_lunar'],
                'type' => $row['type'],
                'description' => $row['description'],
                'is_recurring' => (bool)($row['is_recurring'] ?? 1)
            ];
            
            if ($row['is_lunar']) {
                // Ngày lễ âm lịch - giữ nguyên
                $holiday_data['lunar_day'] = (int)$row['lunar_day'];
                $holiday_data['lunar_month'] = (int)$row['lunar_month'];
            } else {
                // Ngày lễ dương lịch - sử dụng ngày/tháng cố định
                if ($row['solar_day'] && $row['solar_month']) {
                    $holiday_data['solar_day'] = (int)$row['solar_day'];
                    $holiday_data['solar_month'] = (int)$row['solar_month'];
                    $holiday_data['is_recurring_solar'] = true; // Đánh dấu là ngày lễ định kỳ
                } else if ($row['solar_date']) {
                    // Fallback: sử dụng solar_date cũ
                    $holiday_data['solar_date'] = $row['solar_date'];
                }
            }
            
            $holidays[] = $holiday_data;
        }
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events,
        'holidays' => $holidays,
        'debug_info' => [
            'current_year' => $current_year,
            'events_count' => count($events),
            'holidays_count' => count($holidays),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Lỗi get_admin_events: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
?>
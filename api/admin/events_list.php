<?php
// Bật báo lỗi chi tiết để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Xóa mọi output buffer để chỉ trả JSON
while (ob_get_level()) ob_end_clean();

session_start();

// KẾT NỐI CSDL – dùng giống các file events_create / events_delete
require_once $_SERVER['DOCUMENT_ROOT'] . '/LichViet/config.php';

// Check database connection
if (!isset($conn) || $conn === null || $conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($db_connection_error ?? $conn->connect_error ?? 'Unknown error')
    ]);
    exit;
}

// Check admin permission
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $search     = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $event_type = isset($_GET['event_type']) ? $conn->real_escape_string($_GET['event_type']) : 'all';
    $status     = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'all';
    $year       = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Build query
    $whereConditions = ["1=1"];
    $params = [];
    $types  = '';

    if (!empty($search)) {
        $whereConditions[] = "(title LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types   .= 'ss';
    }

    if ($event_type !== 'all') {
        $whereConditions[] = "event_type = ?";
        $params[] = $event_type;
        $types   .= 's';
    }

    if ($status !== 'all') {
        $whereConditions[] = "status = ?";
        $params[] = $status;
        $types   .= 's';
    }

    // Filter theo năm tạo
    $whereConditions[] = "YEAR(e.created_at) = ?";
    $params[] = $year;
    $types   .= 'i';

    $whereClause = implode(" AND ", $whereConditions);

    $sql = "SELECT e.*, u.name AS author_name
            FROM system_events e 
            LEFT JOIN users u ON e.created_by = u.id
            WHERE $whereClause
            ORDER BY e.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Lỗi prepare: ' . $conn->error);
    }

    // ⚠ CHỖ LỖI Ở ĐÂY – dùng spread operator 3 chấm
    if ($params) {
        // PHP >= 5.6
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id'           => $row['id'],
            'title'        => $row['title'],
            'description'  => $row['description'],
            'content'      => $row['content'],
            'event_type'   => $row['event_type'],
            'status'       => $row['status'],
            'start_date'   => $row['start_date'],
            'end_date'     => $row['end_date'],
            'location'     => $row['location'],
            'image_url'    => $row['image_url'],
            'is_featured'  => (bool)$row['is_featured'],
            'author_name'  => $row['author_name'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'published_at' => $row['published_at'],
        ];
    }

    echo json_encode([
        'success' => true,
        'data'    => $events,
        'total'   => count($events),
        'year'    => $year
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi tải danh sách sự kiện: ' . $e->getMessage()
    ]);
}

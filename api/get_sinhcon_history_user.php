<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

try {
    $stmt = $conn->prepare("
        SELECT id, father_year, mother_year, child_year, score, evaluation, detail, created_at
        FROM history_sinhcon
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['detail'])) {
            $decoded = json_decode($row['detail'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $row['detail_parsed'] = $decoded;
            }
        }
        $history[] = $row;
    }

    echo json_encode([
        'success' => true,
        'history' => $history
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
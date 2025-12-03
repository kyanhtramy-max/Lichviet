<?php
// api/get_current_user.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => false
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.role, u.created_at,
           up.phone, up.dob as birthday, up.gender
    FROM users u
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE u.id = ?
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode([
        'logged_in' => false
    ]);
    exit;
}

echo json_encode([
    'logged_in' => true,
    'user'      => [
        'id'       => $user['id'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'phone'    => $user['phone'],
        'birthday' => $user['birthday'],
        'gender'   => $user['gender'],
        'role'     => $user['role'],
        'joined'   => $user['created_at']
    ]
]);

$conn->close();

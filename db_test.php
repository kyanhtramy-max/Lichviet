<?php
require_once "config.php";

echo "Kết nối CSDL thành công!<br>";

// Thử đếm số user trong bảng users
$sql = "SELECT COUNT(*) AS total FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo "Số user hiện có trong bảng users: " . $row['total'];

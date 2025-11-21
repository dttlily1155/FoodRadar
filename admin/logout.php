<?php
// Trang đăng xuất admin
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy session
session_destroy();

// Chuyển hướng về trang đăng nhập admin
header('Location: login.php');
exit;
?>

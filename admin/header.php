<?php
// File header cho trang admin
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Kết nối database
if (!isset($conn)) {
    require_once dirname(__FILE__) . '/../config/database.php';
}

// Lấy tên trang hiện tại
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - FoodRadar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-utensils"></i> FoodRadar</h3>
                <p>Quản trị hệ thống</p>
            </div>

            <ul class="list-unstyled components">
                <li class="<?php echo $current_page === 'users' ? 'active' : ''; ?>">
                    <a href="users.php"><i class="fas fa-users"></i> Quản lý người dùng</a>
                </li>
                <li class="<?php echo $current_page === 'restaurants' ? 'active' : ''; ?>">
                    <a href="restaurants.php"><i class="fas fa-store"></i> Quản lý nhà hàng</a>
                </li>
                <li class="<?php echo $current_page === 'reviews' ? 'active' : ''; ?>">
                    <a href="reviews.php"><i class="fas fa-comment-dots"></i> Quản lý đánh giá</a>
                </li>
                <li>
                    <a href="../index.php" target="_blank" class="btn btn-secondary float-right">
                        <i class="fas fa-eye"></i> Xem trang web
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../logout.php" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto">
                        <span class="navbar-text">
                            <i class="fas fa-user-shield"></i>
                            Xin chào, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong>
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid mt-4">
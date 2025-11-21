<?php
// Trang đăng ký tài khoản

// Kết nối tới file header
require_once 'includes/header.php';

// Kết nối tới file hàm tiện ích
require_once 'includes/functions.php';

// Kiểm tra nếu người dùng đã đăng nhập thì chuyển hướng về trang chủ
if (isLoggedIn()) {
    redirect('index.php');
}

// Khởi tạo biến thông báo
$error = '';
$success = '';

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra dữ liệu
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $email = mysqli_real_escape_string($conn, $email);
        $check_sql = "SELECT id FROM user WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email đã được sử dụng. Vui lòng chọn email khác.';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Thêm người dùng mới vào cơ sở dữ liệu
            $fullname = mysqli_real_escape_string($conn, $fullname);
            $insert_sql = "INSERT INTO user (fullname, email, password) VALUES ('$fullname', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $insert_sql)) {
                $success = 'Đăng ký tài khoản thành công. Bạn có thể đăng nhập ngay bây giờ.';
                
                // Chuyển hướng tới trang đăng nhập sau 2 giây
                header("refresh:2;url=login.php");
            } else {
                $error = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-container">
            <h2 class="form-title">Đăng ký tài khoản</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" id="register-form">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="form-text text-muted">Mật khẩu phải có ít nhất 6 ký tự.</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                    <small id="password-error" class="form-text text-danger"></small>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                </div>
                
                <p class="text-center">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
            </form>
        </div>
    </div>
</div>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
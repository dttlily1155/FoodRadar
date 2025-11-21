<?php
// Trang đăng nhập

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
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Kiểm tra dữ liệu
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } else {
        // Kiểm tra thông tin đăng nhập
        $email = mysqli_real_escape_string($conn, $email);
        $sql = "SELECT id, fullname, password, status, role FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Kiểm tra trạng thái tài khoản
            if ($user['status'] === 'locked') {
                $error = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
            } elseif (password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                
                // Chuyển hướng theo role
                if ($user['role'] === 'admin') {
                    redirect('admin/restaurants.php');
                } else {
                    redirect($redirect);
                }
            } else {
                $error = 'Mật khẩu không chính xác.';
            }
        } else {
            $error = 'Email không tồn tại.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-container">
            <h2 class="form-title">Đăng nhập</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </div>
                
                <p class="text-center">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
            </form>
        </div>
    </div>
</div>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
<?php
// Trang thông tin cá nhân

// Kết nối tới file header
require_once 'includes/header.php';

// Kết nối tới file hàm tiện ích
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Lấy thông tin người dùng
$userId = $_SESSION['user_id'];
$user = null;

$sql = "SELECT * FROM user WHERE id = '$userId'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin người dùng.</div>';
    require_once 'includes/footer.php';
    exit;
}

// Lấy danh sách đánh giá của người dùng
$reviews = [];
$review_sql = "SELECT r.*, rt.name as restaurant_name 
               FROM review r
               JOIN restaurant rt ON r.restaurant_id = rt.id
               WHERE r.user_id = '$userId'
               ORDER BY r.created_at DESC";
$review_result = mysqli_query($conn, $review_sql);

if ($review_result) {
    while ($row = mysqli_fetch_assoc($review_result)) {
        $reviews[] = $row;
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="form-container mb-4">
            <h2 class="form-title">Thông tin tài khoản</h2>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Họ và tên:</label>
                <p><?php echo htmlspecialchars($user['fullname']); ?></p>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Email:</label>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Ngày tham gia:</label>
                <p><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="form-container">
            <h2 class="form-title">Đánh giá của tôi</h2>
            
            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">Bạn chưa viết đánh giá nào.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nhà hàng</th>
                                <th>Số sao</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td>
                                        <a href="restaurant.php?id=<?php echo $review['restaurant_id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($review['restaurant_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo displayStarRating($review['star']); ?></td>
                                    <td><?php echo substr(htmlspecialchars($review['content']), 0, 50) . (strlen($review['content']) > 50 ? '...' : ''); ?></td>
                                    <td>
                                        <?php if ($review['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Đã duyệt</span>
                                        <?php elseif ($review['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Đang chờ</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Bị từ chối</span>
                                        <?php endif; ?>
                                    <td><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
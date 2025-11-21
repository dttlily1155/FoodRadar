<?php
// Trang chi tiết người dùng
require_once 'header.php';

// Kiểm tra ID
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$userId = (int)$_GET['id'];

// Lấy thông tin người dùng
$sql = "SELECT * FROM user WHERE id = $userId";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo '<div class="alert alert-danger">Không tìm thấy người dùng.</div>';
    require_once 'footer.php';
    exit;
}

$user = mysqli_fetch_assoc($result);

// Lấy danh sách đánh giá của người dùng
$reviews = [];
$sql = "SELECT r.*, rt.name as restaurant_name 
        FROM review r
        JOIN restaurant rt ON r.restaurant_id = rt.id
        WHERE r.user_id = $userId
        ORDER BY r.created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
}

// Đếm tổng số reviews
$total_reviews = count($reviews);
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="fas fa-user"></i> Chi tiết người dùng</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Thông tin người dùng -->
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-circle"></i> Thông tin cá nhân</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">ID:</th>
                        <td><?php echo $user['id']; ?></td>
                    </tr>
                    <tr>
                        <th>Họ tên:</th>
                        <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Vai trò:</th>
                        <td>
                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Bị khóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tham gia:</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php if ($user['updated_at']): ?>
                    <tr>
                        <th>Cập nhật lần cuối:</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <!-- Actions -->
                <?php if ($user['role'] === 'user'): ?>
                    <hr>
                    <div class="d-grid gap-2">
                        <?php if ($user['status'] === 'active'): ?>
                            <a href="users.php?action=lock&id=<?php echo $user['id']; ?>" 
                               class="btn btn-warning"
                               onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">
                                <i class="fas fa-lock"></i> Khóa tài khoản
                            </a>
                        <?php else: ?>
                            <a href="users.php?action=unlock&id=<?php echo $user['id']; ?>" 
                               class="btn btn-success"
                               onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?')">
                                <i class="fas fa-unlock"></i> Mở khóa tài khoản
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Lịch sử đánh giá -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-comment-dots"></i> Lịch sử đánh giá (<?php echo $total_reviews; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($reviews)): ?>
                    <p class="text-muted text-center py-4">Người dùng chưa có đánh giá nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nhà hàng</th>
                                    <th>Nội dung</th>
                                    <th>Sao</th>
                                    <th>Like/Dislike</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td>
                                            <a href="../restaurant.php?id=<?php echo $review['restaurant_id']; ?>" 
                                               target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($review['restaurant_name']); ?>
                                                <i class="fas fa-external-link-alt fa-xs"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            $content = htmlspecialchars($review['content']);
                                            echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                            ?>
                                            <?php if (!empty($review['images'])): ?>
                                                <br><small class="text-muted"><i class="fas fa-image"></i> Có ảnh</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php for ($i = 0; $i < $review['star']; $i++): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php endfor; ?>
                                            <br>
                                            <small class="text-muted"><?php echo $review['star']; ?>/5</small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-thumbs-up text-primary"></i> <?php echo $review['likes']; ?><br>
                                                <i class="fas fa-thumbs-down text-danger"></i> <?php echo $review['dislikes']; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = 'secondary';
                                            $status_text = 'Khác';
                                            if ($review['status'] === 'approved') {
                                                $badge_class = 'success';
                                                $status_text = 'Đã duyệt';
                                            } elseif ($review['status'] === 'pending') {
                                                $badge_class = 'warning';
                                                $status_text = 'Chờ duyệt';
                                            } elseif ($review['status'] === 'hidden') {
                                                $badge_class = 'danger';
                                                $status_text = 'Đã ẩn';
                                            }
                                            ?>
                                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                                            <br>
                                            <small class="text-muted"><?php echo date('H:i', strtotime($review['created_at'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

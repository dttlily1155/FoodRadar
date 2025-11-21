<?php
// Trang quản lý đánh giá
require_once 'header.php';

$success = '';
$error = '';

// Xử lý các action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action === 'approve') {
        $sql = "UPDATE review SET status = 'approved' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Duyệt đánh giá thành công.';
        }
    } elseif ($action === 'hide') {
        $sql = "UPDATE review SET status = 'hidden' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Ẩn đánh giá thành công.';
        }
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM review WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Xóa đánh giá thành công.';
        }
    }
}

// Lấy danh sách đánh giá
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where_clause = '';

if ($filter === 'pending') {
    $where_clause = "WHERE r.status = 'pending'";
} elseif ($filter === 'approved') {
    $where_clause = "WHERE r.status = 'approved'";
} elseif ($filter === 'hidden') {
    $where_clause = "WHERE r.status = 'hidden'";
}

$sql = "SELECT r.*, u.fullname, u.email, rt.name as restaurant_name 
        FROM review r
        JOIN user u ON r.user_id = u.id
        JOIN restaurant rt ON r.restaurant_id = rt.id
        $where_clause
        ORDER BY r.created_at DESC";
$result = mysqli_query($conn, $sql);
$reviews = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
}

// Đếm số lượng theo trạng thái
$counts = [
    'all' => 0,
    'pending' => 0,
    'approved' => 0,
    'hidden' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM review GROUP BY status";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['status']] = $row['count'];
        $counts['all'] += $row['count'];
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="fas fa-comment-dots"></i> Quản lý đánh giá</h2>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="reviews.php?filter=all">
            Tất cả <span class="badge bg-secondary"><?php echo $counts['all']; ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $filter === 'pending' ? 'active' : ''; ?>" href="reviews.php?filter=pending">
            Chờ duyệt <span class="badge bg-warning"><?php echo $counts['pending']; ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $filter === 'approved' ? 'active' : ''; ?>" href="reviews.php?filter=approved">
            Đã duyệt <span class="badge bg-success"><?php echo $counts['approved']; ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $filter === 'hidden' ? 'active' : ''; ?>" href="reviews.php?filter=hidden">
            Đã ẩn <span class="badge bg-danger"><?php echo $counts['hidden']; ?></span>
        </a>
    </li>
</ul>

<!-- Reviews Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Danh sách đánh giá (<?php echo count($reviews); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($reviews)): ?>
            <p class="text-muted">Không có đánh giá nào.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Nhà hàng</th>
                            <th>Nội dung</th>
                            <th>Sao</th>
                            <th>Like/Dislike</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($review['fullname']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($review['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($review['restaurant_name']); ?></td>
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
                                <td><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if ($review['status'] !== 'approved'): ?>
                                            <a href="reviews.php?action=approve&id=<?php echo $review['id']; ?>&filter=<?php echo $filter; ?>" 
                                               class="btn btn-sm btn-success" title="Duyệt">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($review['status'] !== 'hidden'): ?>
                                            <a href="reviews.php?action=hide&id=<?php echo $review['id']; ?>&filter=<?php echo $filter; ?>" 
                                               class="btn btn-sm btn-warning" title="Ẩn">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="reviews.php?action=delete&id=<?php echo $review['id']; ?>&filter=<?php echo $filter; ?>" 
                                           class="btn btn-sm btn-danger" title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>

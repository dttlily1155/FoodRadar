<?php
// Trang quản lý nhà hàng
require_once 'header.php';

// Xử lý các action
$success = '';
$error = '';

// Xóa nhà hàng
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM restaurant WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $success = 'Xóa nhà hàng thành công.';
    } else {
        $error = 'Có lỗi xảy ra khi xóa nhà hàng.';
    }
}

// Ẩn/hiện nhà hàng
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'hide') {
        $sql = "UPDATE restaurant SET status = 'hidden' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Ẩn nhà hàng thành công.';
        }
    } elseif ($_GET['action'] === 'show') {
        $sql = "UPDATE restaurant SET status = 'active' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Hiện nhà hàng thành công.';
        }
    }
}

// Lấy danh sách nhà hàng
$sql = "SELECT r.*, c.name as category_name,
        (SELECT COUNT(*) FROM review WHERE restaurant_id = r.id) as review_count,
        (SELECT AVG(star) FROM review WHERE restaurant_id = r.id) as avg_rating
        FROM restaurant r
        LEFT JOIN category c ON r.category_id = c.id
        ORDER BY r.created_at DESC";
$result = mysqli_query($conn, $sql);
$restaurants = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $restaurants[] = $row;
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="fas fa-store"></i> Quản lý nhà hàng</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="restaurant_form.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm nhà hàng mới
        </a>
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

<!-- Restaurants Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Danh sách nhà hàng (<?php echo count($restaurants); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($restaurants)): ?>
            <p class="text-muted">Chưa có nhà hàng nào.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên nhà hàng</th>
                            <th>Danh mục</th>
                            <th>Địa chỉ</th>
                            <th>Đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <tr>
                                <td><?php echo $restaurant['id']; ?></td>
                                <td>
                                    <?php
                                    $images = explode(',', $restaurant['images']);
                                    $firstImage = !empty($images[0]) ? $images[0] : 'default.png';
                                    ?>
                                    <img src="../assets/images/restaurants/<?php echo $firstImage; ?>" 
                                         alt="<?php echo htmlspecialchars($restaurant['name']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($restaurant['category_name']); ?></span></td>
                                <td><?php echo htmlspecialchars($restaurant['address']); ?></td>
                                <td>
                                    <strong><?php echo number_format($restaurant['avg_rating'] ?? 0, 1); ?></strong>
                                    <i class="fas fa-star text-warning"></i>
                                    <small class="text-muted">(<?php echo $restaurant['review_count']; ?>)</small>
                                </td>
                                <td>
                                    <?php if ($restaurant['status'] === 'active'): ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Đã ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="restaurant_form.php?id=<?php echo $restaurant['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($restaurant['status'] === 'active'): ?>
                                        <a href="restaurants.php?action=hide&id=<?php echo $restaurant['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Ẩn"
                                           onclick="return confirm('Bạn có chắc muốn ẩn nhà hàng này?')">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="restaurants.php?action=show&id=<?php echo $restaurant['id']; ?>" 
                                           class="btn btn-sm btn-success" title="Hiện"
                                           onclick="return confirm('Bạn có chắc muốn hiện nhà hàng này?')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="restaurants.php?action=delete&id=<?php echo $restaurant['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Xóa"
                                       onclick="return confirm('Bạn có chắc muốn xóa nhà hàng này? Tất cả đánh giá liên quan cũng sẽ bị xóa.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
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

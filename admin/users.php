<?php
// Trang quản lý người dùng
require_once 'header.php';

// Xử lý các action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action === 'lock') {
        $sql = "UPDATE user SET status = 'locked' WHERE id = $id AND role = 'user'";
        if (mysqli_query($conn, $sql)) {
            $success = 'Khóa tài khoản thành công.';
        }
    } elseif ($action === 'unlock') {
        $sql = "UPDATE user SET status = 'active' WHERE id = $id AND role = 'user'";
        if (mysqli_query($conn, $sql)) {
            $success = 'Mở khóa tài khoản thành công.';
        }
    }
}

// Xử lý tìm kiếm
$search = '';
$where_clause = "WHERE role = 'user'";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clause .= " AND (fullname LIKE '%$search%' OR email LIKE '%$search%')";
}

// Lấy danh sách người dùng
$sql = "SELECT * FROM user $where_clause ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="fas fa-users"></i> Quản lý người dùng</h2>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="users.php" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Tìm kiếm theo tên hoặc email..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Danh sách người dùng (<?php echo count($users); ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <p class="text-muted">Không tìm thấy người dùng nào.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Ngày tham gia</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Bị khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="user_detail.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </a>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <a href="users.php?action=lock&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-warning btn-sm"
                                           onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">
                                            <i class="fas fa-lock"></i> Khóa
                                        </a>
                                    <?php else: ?>
                                        <a href="users.php?action=unlock&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?')">
                                            <i class="fas fa-unlock"></i> Mở khóa
                                        </a>
                                    <?php endif; ?>
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

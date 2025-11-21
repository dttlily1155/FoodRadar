<?php
// Trang viết đánh giá

// Kết nối tới file header
require_once 'includes/header.php';

// Kết nối tới file hàm tiện ích
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo '<div class="alert alert-danger">Bạn cần đăng nhập để viết đánh giá.</div>';
    echo '<p class="text-center mt-3"><a href="login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) . '" class="btn btn-primary">Đăng nhập ngay</a></p>';
    require_once 'includes/footer.php';
    exit;
}

// Kiểm tra ID nhà hàng
if (!isset($_GET['restaurant_id']) || empty($_GET['restaurant_id'])) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin nhà hàng.</div>';
    require_once 'includes/footer.php';
    exit;
}

$restaurantId = (int)$_GET['restaurant_id'];
$restaurant = getRestaurantById($restaurantId);

// Kiểm tra nhà hàng có tồn tại không
if (!$restaurant) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin nhà hàng.</div>';
    require_once 'includes/footer.php';
    exit;
}

// Khởi tạo biến thông báo
$error = '';
$success = '';

// Xử lý form đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $content = trim($_POST['content']);
    $star = isset($_POST['star']) ? (int)$_POST['star'] : 0;
    
    // Kiểm tra dữ liệu
    if (empty($content)) {
        $error = 'Vui lòng nhập nội dung đánh giá.';
    } elseif ($star < 1 || $star > 5) {
        $error = 'Vui lòng chọn số sao đánh giá (1-5).';
    } else {
        // Tải ảnh lên nếu có
        $imagePaths = '';
        if (!empty($_FILES['images']['name'][0])) {
            $imagePaths = uploadImages($_FILES, 'images');
        }
        
        // Thêm đánh giá mới vào cơ sở dữ liệu
        $userId = $_SESSION['user_id'];
        $content = mysqli_real_escape_string($conn, $content);
        $insert_sql = "INSERT INTO review (restaurant_id, user_id, content, star, images, status) 
                      VALUES ('$restaurantId', '$userId', '$content', '$star', '$imagePaths', 'pending')";
        
        if (mysqli_query($conn, $insert_sql)) {
            $success = 'Đánh giá của bạn đã được gửi thành công và đang chờ admin duyệt.';
            
            // Chuyển hướng về trang chi tiết nhà hàng sau 2 giây
            header("refresh:2;url=restaurant.php?id=$restaurantId");
        } else {
            $error = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="form-container">
            <h2 class="form-title">Viết đánh giá</h2>
            <h5 class="text-center mb-4">
                <a href="restaurant.php?id=<?php echo $restaurantId; ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($restaurant['name']); ?>
                </a>
            </h5>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php else: ?>
            
            <form method="POST" action="write-review.php?restaurant_id=<?php echo $restaurantId; ?>" enctype="multipart/form-data">
                <div class="mb-4 text-center">
                    <label class="form-label d-block">Chọn số sao</label>
                    <div class="rating-input">
                        <input type="radio" id="star5" name="star" value="5" />
                        <label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star4" name="star" value="4" />
                        <label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star3" name="star" value="3" />
                        <label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star2" name="star" value="2" />
                        <label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star1" name="star" value="1" />
                        <label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                    </div>
                    <div class="mt-2">
                        <span id="rating-value">0</span>/5
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Nhận xét</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="review-images" class="form-label">Hình ảnh (không bắt buộc)</label>
                    <input type="file" class="form-control" id="review-images" name="images[]" accept="image/*" multiple>
                    <small class="form-text text-muted">Bạn có thể tải lên tối đa 5 ảnh (định dạng JPG, PNG)</small>
                </div>
                
                <div id="image-preview" class="mb-3 d-flex flex-wrap"></div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-50 mx-auto d-block">Gửi đánh giá</button>
                </div>
            </form>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
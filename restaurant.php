<?php
// Trang chi tiết nhà hàng

// Kết nối tới file header
require_once 'includes/header.php';

// Kết nối tới file hàm tiện ích
require_once 'includes/functions.php';

// Kiểm tra user có bị khóa không
if (isLoggedIn() && isUserLocked()) {
    session_destroy();
    echo '<div class="alert alert-danger">Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.</div>';
    require_once 'includes/footer.php';
    exit;
}

// Kiểm tra id nhà hàng có tồn tại không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin nhà hàng.</div>';
    require_once 'includes/footer.php';
    exit;
}

$restaurantId = (int)$_GET['id'];
$restaurant = getRestaurantById($restaurantId);

// Kiểm tra nhà hàng có tồn tại và đang active không
if (!$restaurant || $restaurant['status'] !== 'active') {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin nhà hàng.</div>';
    require_once 'includes/footer.php';
    exit;
}

// Lấy danh sách đánh giá của nhà hàng
$reviews = getReviewsByRestaurant($restaurantId);

// Lấy đánh giá trung bình và số lượng đánh giá
$averageRating = getAverageRating($restaurantId);
$reviewCount = getReviewCount($restaurantId);

// Xử lý thông tin hình ảnh
$images = !empty($restaurant['images']) ? explode(',', $restaurant['images']) : [];
$firstImage = !empty($images[0]) ? $images[0] : 'assets/images/default-restaurant.png';
?>

<!-- Thông tin nhà hàng -->
<div class="restaurant-detail mb-5">
    <div class="row">
        <div class="col-md-5">
            <div class="restaurant-main-image mb-3">
                <img src="assets/images/restaurants/<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="img-fluid rounded">
            </div>
            <?php if (count($images) > 1): ?>
                <div class="restaurant-gallery d-flex flex-wrap">
                    <?php for ($i = 1; $i < count($images) && $i < 4; $i++): ?>
                        <div class="gallery-item me-2 mb-2" style="width: 100px; height: 100px;">
                            <img src="assets/images/restaurants/<?php echo $images[$i]; ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="img-fluid rounded w-100 h-100" style="object-fit: cover;">
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h1 class="restaurant-title mb-3"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <p class="mb-3">
                <i class="fas fa-map-marker-alt"></i> Địa chỉ: <?php echo htmlspecialchars($restaurant['address']); ?>
            </p>
            <p class="mb-3">
                <i class="fas fa-utensils"></i> Danh mục: <?php echo htmlspecialchars($restaurant['category_name']); ?>
            </p>
            <?php if (!empty($restaurant['open_time'])): ?>
                <p class="mb-3">
                    <i class="far fa-clock"></i> Giờ mở cửa: <?php echo htmlspecialchars($restaurant['open_time']); ?>
                </p>
            <?php endif; ?>
            <div class="restaurant-rating mb-3">
                <div class="d-flex align-items-center">
                    <h2 class="me-2 mb-0"><?php echo number_format($averageRating, 1); ?></h2>
                    <?php echo displayStarRating($averageRating); ?>
                    <span class="ms-2">(<?php echo $reviewCount; ?> đánh giá)</span>
                </div>
            </div>
            <?php if (!empty($restaurant['description'])): ?>
                <div class="restaurant-description mt-3">
                    <h5>Mô tả: </h5>
                    <div class="description-content">
                        <?php echo $restaurant['description']; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Nút viết đánh giá -->
            <div class="mt-4">
                <?php if (isLoggedIn()): ?>
                    <a href="write-review.php?restaurant_id=<?php echo $restaurantId; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Viết đánh giá
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=restaurant.php?id=<?php echo $restaurantId; ?>" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập để viết đánh giá
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Phần đánh giá -->
<div class="restaurant-reviews mt-5">
    <h2 class="mb-4">Đánh giá (<?php echo $reviewCount; ?>)</h2>
    
    <?php if (empty($reviews)): ?>
        <div class="alert alert-info">Chưa có đánh giá nào cho nhà hàng này. Hãy là người đầu tiên đánh giá!</div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="review-card mb-4">
                <div class="review-header">
                    <div class="reviewer-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0"><?php echo htmlspecialchars($review['fullname']); ?></h5>
                        <div class="d-flex align-items-center">
                            <?php echo displayStarRating($review['star']); ?>
                            <small class="text-muted ms-2">
                                <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="review-content mt-3">
                    <p><?php echo nl2br(htmlspecialchars($review['content'])); ?></p>
                </div>
                <?php if (!empty($review['images'])): ?>
                    <div class="review-images">
                        <?php
                        $reviewImages = explode(',', $review['images']);
                        foreach ($reviewImages as $image):
                        ?>
                            <img src="<?php echo $image; ?>" alt="Review image" class="review-image">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Like/Dislike buttons -->
                <div class="mt-3 d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-primary like-btn" data-review-id="<?php echo $review['id']; ?>" data-action="like">
                        <i class="fas fa-thumbs-up"></i> 
                        <span class="like-count"><?php echo $review['likes']; ?></span>
                    </button>
                    <button class="btn btn-sm btn-outline-danger dislike-btn" data-review-id="<?php echo $review['id']; ?>" data-action="dislike">
                        <i class="fas fa-thumbs-down"></i> 
                        <span class="dislike-count"><?php echo $review['dislikes']; ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal hiển thị ảnh lớn -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="modal-img img-fluid" alt="Restaurant image">
            </div>
        </div>
    </div>
</div>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
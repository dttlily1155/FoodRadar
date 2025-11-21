<?php
// Trang chủ của website

// Kết nối tới file header
require_once 'includes/header.php';

// Kết nối tới file hàm tiện ích
require_once 'includes/functions.php';

// Xử lý tìm kiếm và lọc
$restaurants = [];
$filters = [];

// Lấy các tham số lọc
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['keyword'] = $_GET['search'];
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters['category_id'] = $_GET['category'];
}

if (isset($_GET['rating']) && !empty($_GET['rating'])) {
    $filters['min_rating'] = $_GET['rating'];
}

if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $filters['sort'] = $_GET['sort'];
}

// Lấy danh sách nhà hàng
if (!empty($filters)) {
    $restaurants = filterRestaurants($filters);
} else {
    $restaurants = getFeaturedRestaurants(8);
}
?>

<!-- Banner & Search section -->
<section class="mb-5">
    <div class="search-container">
        <h2 class="text-center mb-4">Tìm kiếm nhà hàng yêu thích</h2>
        <form action="index.php" method="GET" id="search-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" id="search-input" class="form-control" 
                           placeholder="Tên nhà hàng, địa chỉ..." 
                           value="<?php echo isset($filters['keyword']) ? htmlspecialchars($filters['keyword']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">-- Tất cả danh mục --</option>
                        <?php
                        $categories = getCategories();
                        foreach ($categories as $cat):
                        ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                <?php echo (isset($filters['category_id']) && $filters['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">-- Số sao --</option>
                        <option value="4" <?php echo (isset($filters['min_rating']) && $filters['min_rating'] == 4) ? 'selected' : ''; ?>>Từ 4 sao</option>
                        <option value="3" <?php echo (isset($filters['min_rating']) && $filters['min_rating'] == 3) ? 'selected' : ''; ?>>Từ 3 sao</option>
                        <option value="2" <?php echo (isset($filters['min_rating']) && $filters['min_rating'] == 2) ? 'selected' : ''; ?>>Từ 2 sao</option>
                        <option value="1" <?php echo (isset($filters['min_rating']) && $filters['min_rating'] == 1) ? 'selected' : ''; ?>>Từ 1 sao</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="">Sắp xếp</option>
                        <option value="rating_desc" <?php echo (isset($filters['sort']) && $filters['sort'] == 'rating_desc') ? 'selected' : ''; ?>>Đánh giá cao</option>
                        <option value="review_count" <?php echo (isset($filters['sort']) && $filters['sort'] == 'review_count') ? 'selected' : ''; ?>>Nhiều review</option>
                        <option value="rating_asc" <?php echo (isset($filters['sort']) && $filters['sort'] == 'rating_asc') ? 'selected' : ''; ?>>Đánh giá thấp</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn search-button w-100" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Main content -->
<section class="mt-5">
    <?php if (!empty($filters)): ?>
        <h2 class="mb-4">Kết quả tìm kiếm (<?php echo count($restaurants); ?> nhà hàng)</h2>
        <div class="mb-3">
            <a href="index.php" class="btn btn-sm btn-secondary">
                <i class="fas fa-times"></i> Xóa bộ lọc
            </a>
        </div>
    <?php else: ?>
        <h2 class="mb-4">Nhà hàng nổi bật</h2>
    <?php endif; ?>

    <?php if (empty($restaurants)): ?>
        <div class="alert alert-info">Không tìm thấy nhà hàng phù hợp. Vui lòng thử lại với từ khóa khác.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="restaurant-card">
                        <?php
                        // Hiển thị ảnh đầu tiên của nhà hàng
                        $images = explode(',', $restaurant['images']);
                        $firstImage = !empty($images[0]) ? $images[0] : 'assets/images/default-restaurant.png';
                        ?>
                        <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>">
                            <img src="assets/images/restaurants/<?php echo $firstImage; ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="restaurant-img">
                        </a>
                        <div class="restaurant-info">
                            <h5 class="restaurant-title">
                                <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($restaurant['name']); ?>
                                </a>
                            </h5>
                            <p class="restaurant-address">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($restaurant['address']); ?>
                            </p>
                            <div class="d-flex align-items-center">
                                <?php 
                                // Hiển thị đánh giá trung bình
                                $avgRating = isset($restaurant['average_rating']) ? $restaurant['average_rating'] : 0;
                                echo displayStarRating($avgRating);
                                ?>
                                <span class="rating-count">(<?php echo isset($restaurant['review_count']) ? $restaurant['review_count'] : 0; ?> đánh giá)</span>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-info"><?php echo htmlspecialchars($restaurant['category_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
// Kết nối tới file footer
require_once 'includes/footer.php';
?> 
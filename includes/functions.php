<?php
// File chứa các hàm tiện ích cho website

/**
 * Hàm lấy thông tin của nhà hàng theo ID
 * @param int $id ID của nhà hàng
 * @return array|null Thông tin nhà hàng hoặc null nếu không tồn tại
 */
function getRestaurantById($id) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    
    $sql = "SELECT r.*, c.name as category_name 
            FROM restaurant r
            LEFT JOIN category c ON r.category_id = c.id
            WHERE r.id = '$id'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Hàm lấy danh sách nhà hàng nổi bật (sắp xếp theo đánh giá)
 * @param int $limit Số lượng nhà hàng muốn lấy
 * @return array Danh sách nhà hàng
 */
function getFeaturedRestaurants($limit = 6) {
    global $conn;
    $limit = (int)$limit;
    
    $sql = "SELECT r.*, c.name as category_name, 
            (SELECT COUNT(*) FROM review WHERE restaurant_id = r.id AND status = 'approved') as review_count,
            (SELECT AVG(star) FROM review WHERE restaurant_id = r.id AND status = 'approved') as average_rating
            FROM restaurant r
            LEFT JOIN category c ON r.category_id = c.id
            WHERE r.status = 'active'
            GROUP BY r.id
            ORDER BY average_rating DESC, review_count DESC
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    $restaurants = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $restaurants[] = $row;
        }
    }
    
    return $restaurants;
}

/**
 * Hàm lấy danh sách nhà hàng theo danh mục
 * @param int $categoryId ID của danh mục
 * @param int $limit Số lượng nhà hàng muốn lấy
 * @return array Danh sách nhà hàng
 */
function getRestaurantsByCategory($categoryId, $limit = 10) {
    global $conn;
    $categoryId = mysqli_real_escape_string($conn, $categoryId);
    $limit = (int)$limit;
    
    $sql = "SELECT r.*, c.name as category_name, 
            (SELECT COUNT(*) FROM review WHERE restaurant_id = r.id AND status = 'approved') as review_count,
            (SELECT AVG(star) FROM review WHERE restaurant_id = r.id AND status = 'approved') as average_rating
            FROM restaurant r
            LEFT JOIN category c ON r.category_id = c.id
            WHERE r.category_id = '$categoryId' AND r.status = 'active'
            GROUP BY r.id
            ORDER BY average_rating DESC
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    $restaurants = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $restaurants[] = $row;
        }
    }
    
    return $restaurants;
}

/**
 * Hàm tìm kiếm nhà hàng
 * @param string $keyword Từ khóa tìm kiếm
 * @return array Danh sách nhà hàng phù hợp
 */
function searchRestaurants($keyword) {
    global $conn;
    $keyword = mysqli_real_escape_string($conn, $keyword);
    
    $sql = "SELECT r.*, c.name as category_name, 
            (SELECT COUNT(*) FROM review WHERE restaurant_id = r.id AND status = 'approved') as review_count,
            (SELECT AVG(star) FROM review WHERE restaurant_id = r.id AND status = 'approved') as average_rating
            FROM restaurant r
            LEFT JOIN category c ON r.category_id = c.id
            WHERE r.status = 'active' AND (r.name LIKE '%$keyword%' 
               OR r.address LIKE '%$keyword%'
               OR c.name LIKE '%$keyword%')
            GROUP BY r.id
            ORDER BY average_rating DESC";
    
    $result = mysqli_query($conn, $sql);
    $restaurants = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $restaurants[] = $row;
        }
    }
    
    return $restaurants;
}

/**
 * Hàm lấy các đánh giá của nhà hàng
 * @param int $restaurantId ID của nhà hàng
 * @return array Danh sách đánh giá
 */
function getReviewsByRestaurant($restaurantId) {
    global $conn;
    $restaurantId = mysqli_real_escape_string($conn, $restaurantId);
    
    $sql = "SELECT r.*, u.fullname
            FROM review r
            JOIN user u ON r.user_id = u.id
            WHERE r.restaurant_id = '$restaurantId' AND r.status = 'approved'
            ORDER BY r.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $reviews = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
    }
    
    return $reviews;
}

/**
 * Hàm lấy đánh giá trung bình của nhà hàng
 * @param int $restaurantId ID của nhà hàng
 * @return float Điểm đánh giá trung bình
 */
function getAverageRating($restaurantId) {
    global $conn;
    $restaurantId = mysqli_real_escape_string($conn, $restaurantId);
    
    $sql = "SELECT AVG(star) as average_rating FROM review WHERE restaurant_id = '$restaurantId' AND status = 'approved'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Kiểm tra xem average_rating có phải là NULL không
        if ($row['average_rating'] !== NULL) {
            return round((float)$row['average_rating'], 1);
        }
    }
    
    return 0;
}

/**
 * Hàm lấy số lượng đánh giá của nhà hàng
 * @param int $restaurantId ID của nhà hàng
 * @return int Số lượng đánh giá
 */
function getReviewCount($restaurantId) {
    global $conn;
    $restaurantId = mysqli_real_escape_string($conn, $restaurantId);
    
    $sql = "SELECT COUNT(*) as count FROM review WHERE restaurant_id = '$restaurantId' AND status = 'approved'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['count'];
    }
    
    return 0;
}

/**
 * Hàm lấy danh mục của nhà hàng
 * @return array Danh sách danh mục
 */
function getCategories() {
    global $conn;
    
    $sql = "SELECT * FROM category ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $categories = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Hàm hiển thị sao đánh giá
 * @param float $rating Điểm đánh giá
 * @return string HTML để hiển thị sao
 */
function displayStarRating($rating) {
    $output = '<div class="rating">';
    
    $fullStars = floor($rating);
    $halfStar = round($rating - $fullStars, 1) >= 0.5;
    
    // Hiển thị sao đầy
    for ($i = 1; $i <= $fullStars; $i++) {
        $output .= '<i class="fas fa-star"></i>';
    }
    
    // Hiển thị nửa sao nếu có
    if ($halfStar) {
        $output .= '<i class="fas fa-star-half-alt"></i>';
        $i++;
    }
    
    // Hiển thị sao rỗng
    for (; $i <= 5; $i++) {
        $output .= '<i class="far fa-star"></i>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Hàm tạo liên kết thân thiện với SEO
 * @param string $string Chuỗi cần chuyển đổi
 * @return string Chuỗi đã được chuyển đổi
 */
function createSlug($string) {
    $string = trim($string);
    $string = preg_replace('/[^a-zA-Z0-9ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễếệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+/u', '', $string);
    $string = preg_replace('/\s+/u', '-', $string);
    $string = preg_replace('/-+/u', '-', $string);
    
    $string = str_replace(array('à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ'), 'a', $string);
    $string = str_replace(array('è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ'), 'e', $string);
    $string = str_replace(array('ì','í','ị','ỉ','ĩ'), 'i', $string);
    $string = str_replace(array('ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ'), 'o', $string);
    $string = str_replace(array('ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ'), 'u', $string);
    $string = str_replace(array('ỳ','ý','ỵ','ỷ','ỹ'), 'y', $string);
    $string = str_replace(array('đ'), 'd', $string);
    
    return strtolower($string);
}

/**
 * Hàm tải ảnh lên và trả về đường dẫn
 * @param array $files Mảng $_FILES
 * @param string $fileInputName Tên input file
 * @return string Danh sách đường dẫn ảnh (phân cách bởi dấu phẩy)
 */
function uploadImages($files, $fileInputName) {
    $uploadDir = 'assets/images/reviews/';
    // Đảm bảo thư mục tồn tại
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            // Nếu không thể tạo thư mục, sử dụng thư mục mặc định
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }
    }
    
    $imagePaths = [];
    
    // Xử lý nhiều file
    if (is_array($files[$fileInputName]['name'])) {
        foreach ($files[$fileInputName]['name'] as $key => $name) {
            if ($files[$fileInputName]['error'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $files[$fileInputName]['tmp_name'][$key];
                
                // Lấy phần mở rộng của file
                $fileExt = pathinfo($name, PATHINFO_EXTENSION);
                // Tạo tên file mới với dấu chấm trước phần mở rộng
                $newName = uniqid() . '_' . createSlug(pathinfo($name, PATHINFO_FILENAME)) . '.' . $fileExt;
                $destination = $uploadDir . $newName;
                
                if (move_uploaded_file($tmpName, $destination)) {
                    $imagePaths[] = $destination;
                }
            }
        }
    } 
    // Xử lý một file
    else if ($files[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $files[$fileInputName]['tmp_name'];
        $name = $files[$fileInputName]['name'];
        
        // Lấy phần mở rộng của file
        $fileExt = pathinfo($name, PATHINFO_EXTENSION);
        // Tạo tên file mới với dấu chấm trước phần mở rộng
        $newName = uniqid() . '_' . createSlug(pathinfo($name, PATHINFO_FILENAME)) . '.' . $fileExt;
        $destination = $uploadDir . $newName;
        
        if (move_uploaded_file($tmpName, $destination)) {
            $imagePaths[] = $destination;
        }
    }
    
    return implode(',', $imagePaths);
}

/**
 * Kiểm tra người dùng đã đăng nhập chưa
 * @return bool True nếu đã đăng nhập, ngược lại là False
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Chuyển hướng tới URL
 * @param string $url URL đích
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Hàm hiển thị thông báo
 * @param string $message Nội dung thông báo
 * @param string $type Loại thông báo (success, danger, warning, info)
 * @return string HTML để hiển thị thông báo
 */
function showAlert($message, $type = 'info') {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Hàm lọc nhà hàng theo nhiều tiêu chí
 * @param array $filters Mảng chứa các tiêu chí lọc
 * @return array Danh sách nhà hàng phù hợp
 */
function filterRestaurants($filters = []) {
    global $conn;
    
    $where_conditions = ["r.status = 'active'"];
    
    // Lọc theo danh mục
    if (!empty($filters['category_id'])) {
        $category_id = mysqli_real_escape_string($conn, $filters['category_id']);
        $where_conditions[] = "r.category_id = '$category_id'";
    }
    
    // Lọc theo từ khóa
    if (!empty($filters['keyword'])) {
        $keyword = mysqli_real_escape_string($conn, $filters['keyword']);
        $where_conditions[] = "(r.name LIKE '%$keyword%' OR r.address LIKE '%$keyword%' OR c.name LIKE '%$keyword%')";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    $sql = "SELECT r.*, c.name as category_name, 
            (SELECT COUNT(*) FROM review WHERE restaurant_id = r.id AND status = 'approved') as review_count,
            (SELECT AVG(star) FROM review WHERE restaurant_id = r.id AND status = 'approved') as average_rating
            FROM restaurant r
            LEFT JOIN category c ON r.category_id = c.id
            WHERE $where_clause
            GROUP BY r.id";
    
    // Lọc theo số sao (sau khi query vì dùng subquery)
    if (isset($filters['min_rating'])) {
        if ($filters['min_rating'] == '0') {
            // Lọc nhà hàng chưa có đánh giá
            $sql .= " HAVING (average_rating IS NULL OR review_count = 0)";
        } else {
            // Lọc theo số sao tối thiểu
            $sql .= " HAVING average_rating >= " . (float)$filters['min_rating'];
        }
    }
    
    // Sắp xếp
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'rating_desc':
                $sql .= " ORDER BY average_rating DESC, review_count DESC";
                break;
            case 'rating_asc':
                $sql .= " ORDER BY average_rating ASC";
                break;
            case 'review_count':
                $sql .= " ORDER BY review_count DESC, average_rating DESC";
                break;
            default:
                $sql .= " ORDER BY r.created_at DESC";
        }
    } else {
        $sql .= " ORDER BY average_rating DESC, review_count DESC";
    }
    
    $result = mysqli_query($conn, $sql);
    $restaurants = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $restaurants[] = $row;
        }
    }
    
    return $restaurants;
}

/**
 * Kiểm tra user đã khóa chưa
 * @return bool True nếu bị khóa
 */
function isUserLocked() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    $sql = "SELECT status FROM user WHERE id = '$userId'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['status'] === 'locked';
    }
    
    return false;
}
?> 
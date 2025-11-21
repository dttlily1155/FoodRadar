<?php
// File AJAX xử lý like/dislike review
session_start();
require_once dirname(__FILE__) . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện hành động này.']);
    exit;
}

if (!isset($_POST['review_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin.']);
    exit;
}

$reviewId = (int)$_POST['review_id'];
$action = $_POST['action']; // like, dislike, remove_like, remove_dislike
$userId = $_SESSION['user_id'];

if (!in_array($action, ['like', 'dislike', 'remove_like', 'remove_dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
    exit;
}

// Kiểm tra review có tồn tại không
$sql = "SELECT likes, dislikes FROM review WHERE id = $reviewId";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Đánh giá không tồn tại.']);
    exit;
}

$review = mysqli_fetch_assoc($result);
$newLikes = $review['likes'];
$newDislikes = $review['dislikes'];

// Xử lý logic
switch ($action) {
    case 'like':
        $newLikes = max(0, $review['likes'] + 1);
        break;
    
    case 'dislike':
        $newDislikes = max(0, $review['dislikes'] + 1);
        break;
    
    case 'remove_like':
        $newLikes = max(0, $review['likes'] - 1);
        break;
    
    case 'remove_dislike':
        $newDislikes = max(0, $review['dislikes'] - 1);
        break;
}

// Cập nhật database
$sql = "UPDATE review SET likes = $newLikes, dislikes = $newDislikes WHERE id = $reviewId";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        'success' => true, 
        'likes' => $newLikes,
        'dislikes' => $newDislikes
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra.']);
}
?>

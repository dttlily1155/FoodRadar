<?php
// File xử lý upload ảnh cho CKEditor
session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

require_once '../config/database.php';

// Thư mục lưu ảnh
$uploadDir = '../assets/images/restaurants/';

// Đảm bảo thư mục tồn tại
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$funcNum = $_GET['CKEditorFuncNum'];
$message = '';
$url = '';

if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileSize = $file['size'];
    
    // Kiểm tra lỗi upload
    if ($fileError === UPLOAD_ERR_OK) {
        // Kiểm tra loại file
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Kiểm tra kích thước file (tối đa 5MB)
            if ($fileSize <= 5 * 1024 * 1024) {
                // Tạo tên file mới
                $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;
                
                // Di chuyển file
                if (move_uploaded_file($fileTmpName, $destination)) {
                    // Đường dẫn trả về cho CKEditor (relative path)
                    $url = '../assets/images/restaurants/' . $newFileName;
                    $message = 'Upload thành công!';
                } else {
                    $message = 'Lỗi khi di chuyển file!';
                }
            } else {
                $message = 'File quá lớn! Tối đa 5MB.';
            }
        } else {
            $message = 'Định dạng file không được phép! Chỉ chấp nhận: ' . implode(', ', $allowedExtensions);
        }
    } else {
        $message = 'Lỗi upload file: ' . $fileError;
    }
} else {
    $message = 'Không có file nào được upload!';
}

// Trả về kết quả cho CKEditor
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>

<?php
// Form thêm/sửa nhà hàng
require_once 'header.php';

$isEdit = false;
$restaurant = null;
$success = '';
$error = '';

// Kiểm tra nếu là sửa
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM restaurant WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $restaurant = mysqli_fetch_assoc($result);
    } else {
        header('Location: restaurants.php');
        exit;
    }
}

// Lấy danh mục
$categories = [];
$sql = "SELECT * FROM category ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category_id = (int)$_POST['category_id'];
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $open_time = mysqli_real_escape_string($conn, trim($_POST['open_time']));
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Xử lý upload ảnh
    $imageFiles = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = '../assets/images/restaurants/';
        foreach ($_FILES['images']['name'] as $key => $filename) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $newFilename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadDir . $newFilename)) {
                    $imageFiles[] = $newFilename;
                }
            }
        }
    }
    
    // Nếu có ảnh mới, dùng ảnh mới, nếu không giữ ảnh cũ
    if (!empty($imageFiles)) {
        $images = implode(',', $imageFiles);
    } else {
        $images = $isEdit ? $restaurant['images'] : '';
    }
    
    if (empty($name) || empty($address)) {
        $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    } else {
        if ($isEdit) {
            // Cập nhật
            $sql = "UPDATE restaurant SET 
                    name = '$name',
                    category_id = $category_id,
                    address = '$address',
                    description = '$description',
                    open_time = '$open_time',
                    images = '$images',
                    status = '$status'
                    WHERE id = " . $restaurant['id'];
        } else {
            // Thêm mới
            $sql = "INSERT INTO restaurant (name, category_id, address, description, open_time, images, status) 
                    VALUES ('$name', $category_id, '$address', '$description', '$open_time', '$images', '$status')";
        }
        
        if (mysqli_query($conn, $sql)) {
            $success = $isEdit ? 'Cập nhật nhà hàng thành công.' : 'Thêm nhà hàng mới thành công.';
            if (!$isEdit) {
                header('refresh:2;url=restaurants.php');
            } else {
                // Load lại dữ liệu sau khi cập nhật
                $sql = "SELECT * FROM restaurant WHERE id = " . $restaurant['id'];
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    $restaurant = mysqli_fetch_assoc($result);
                }
            }
        } else {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        }
    }
}
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="fas fa-store"></i> <?php echo $isEdit ? 'Sửa nhà hàng' : 'Thêm nhà hàng mới'; ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="restaurants.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
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

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên nhà hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo $restaurant ? htmlspecialchars($restaurant['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?php echo $restaurant ? htmlspecialchars($restaurant['address']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="8"><?php echo $restaurant ? htmlspecialchars($restaurant['description']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="0">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($restaurant && $restaurant['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="open_time" class="form-label">Giờ mở cửa</label>
                        <input type="text" class="form-control" id="open_time" name="open_time" 
                               placeholder="VD: 10h-22h"
                               value="<?php echo $restaurant ? htmlspecialchars($restaurant['open_time']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo ($restaurant && $restaurant['status'] === 'active') ? 'selected' : ''; ?>>Hiển thị</option>
                            <option value="hidden" <?php echo ($restaurant && $restaurant['status'] === 'hidden') ? 'selected' : ''; ?>>Ẩn</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <small class="text-muted">Có thể chọn nhiều ảnh</small>
                        <?php if ($restaurant && !empty($restaurant['images'])): ?>
                            <div class="mt-2">
                                <p class="mb-1"><strong>Ảnh hiện tại:</strong></p>
                                <?php
                                $images = explode(',', $restaurant['images']);
                                foreach ($images as $img):
                                ?>
                                    <img src="../assets/images/restaurants/<?php echo $img; ?>" 
                                         class="img-thumbnail me-1 mb-1" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Cập nhật' : 'Thêm mới'; ?>
                </button>
                <a href="restaurants.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description', {
        height: 300,
        filebrowserUploadUrl: 'upload_image.php',
        filebrowserUploadMethod: 'form',
        toolbar: [
            { name: 'document', items: [ 'Source' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', 'SelectAll' ] },
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'Blockquote' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
        ]
    });
</script>

<?php require_once 'footer.php'; ?>

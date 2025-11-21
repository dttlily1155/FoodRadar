## Cấu trúc dự án FoodRadar

```
FoodRadar/
├── admin/                      # Trang quản trị
│   ├── header.php              # Header admin
│   ├── footer.php              # Footer admin
│   ├── restaurants.php         # Quản lý nhà hàng
│   ├── restaurant_form.php     # Form thêm/sửa nhà hàng
│   ├── upload_image.php        # Upload ảnh CKEditor
│   ├── users.php               # Quản lý người dùng
│   ├── user_detail.php         # Chi tiết người dùng
│   ├── reviews.php             # Quản lý đánh giá
│   └── logout.php              # Đăng xuất admin
├── ajax/
│   └── like_review.php         # Xử lý like/dislike
├── assets/
│   ├── css/
│   │   ├── style.css           # CSS trang user
│   │   └── admin.css           # CSS trang admin
│   ├── js/
│   │   ├── main.js             # JS trang user
│   │   └── admin.js            # JS trang admin
│   └── images/
│       ├── restaurants/        # Ảnh nhà hàng
│       └── reviews/            # Ảnh đánh giá
├── config/
│   └── database.php            # Cấu hình database
├── includes/
│   ├── header.php              # Header user
│   ├── footer.php              # Footer user
│   └── functions.php           # Các hàm tiện ích
├── index.php                   # Trang chủ
├── login.php                   # Đăng nhập
├── register.php                # Đăng ký
├── logout.php                  # Đăng xuất
├── profile.php                 # Trang cá nhân
├── restaurant.php              # Chi tiết nhà hàng
├── write-review.php            # Viết đánh giá
└── food_radar.sql              # Database SQL
```

## Chức năng đã triển khai

### Phía Admin ✅
- **Đăng nhập**: Chung với user, tự động redirect theo role
- **Quản lý người dùng**:
  - Hiển thị danh sách người dùng
  - Tìm kiếm theo tên/email
  - Xem chi tiết người dùng (trang riêng)
  - Khóa/Mở khóa tài khoản
  
- **Quản lý nhà hàng** (CRUD đầy đủ):
  - Thêm/Sửa/Xóa nhà hàng
  - Upload nhiều ảnh
  - CKEditor cho mô tả (hỗ trợ upload ảnh)
  - Ẩn/Hiện nhà hàng
  - Tự động refresh dữ liệu sau khi cập nhật
  
- **Quản lý đánh giá**:
  - Hiển thị danh sách đánh giá
  - Lọc theo trạng thái (Chờ duyệt/Đã duyệt/Đã ẩn)
  - Duyệt/Ẩn/Xóa đánh giá

### Phía Người dùng ✅
- **Đăng ký/Đăng nhập**:
  - Mật khẩu được mã hóa (password_hash)
  - Kiểm tra trạng thái tài khoản (bị khóa không đăng nhập được)
  
- **Bộ lọc tìm kiếm nâng cao**:
  - Tìm theo từ khóa (tên, địa chỉ)
  - Lọc theo danh mục
  - Lọc theo số sao (từ 1, 2, 3, 4 sao)
  - Sắp xếp (Đánh giá cao, Nhiều review, Đánh giá thấp)
  
- **Chi tiết nhà hàng**:
  - Hiển thị đầy đủ thông tin
  - Mô tả hỗ trợ HTML/hình ảnh từ CKEditor
  - Xem danh sách đánh giá đã duyệt
  
- **Viết đánh giá**:
  - Chọn số sao (1-5)
  - Viết nội dung
  - Upload hình ảnh
  - **Trạng thái: Chờ admin duyệt**
  
- **Like/Dislike đánh giá**:
  - Sử dụng AJAX (không reload trang)
  - Lưu trạng thái localStorage
  - Hiển thị số lượng like/dislike

## Cơ sở dữ liệu

### Bảng `user`
- `id` (int, PK, AI)
- `fullname` (varchar)
- `email` (varchar, UNIQUE)
- `password` (varchar, hashed)
- `role` (enum: user, admin) - Mặc định: user
- `status` (enum: active, locked) - Mặc định: active
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Bảng `category`
- `id` (int, PK, AI)
- `name` (varchar)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Bảng `restaurant`
- `id` (int, PK, AI)
- `category_id` (int, FK)
- `name` (varchar)
- `address` (varchar)
- `description` (text) - Hỗ trợ HTML
- `open_time` (varchar)
- `images` (text) - CSV
- `status` (enum: active, hidden) - Mặc định: active
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Bảng `review`
- `id` (int, PK, AI)
- `restaurant_id` (int, FK)
- `user_id` (int, FK)
- `content` (text)
- `star` (int, 1-5)
- `images` (text) - CSV
- `likes` (int) - Mặc định: 0
- `dislikes` (int) - Mặc định: 0
- `status` (enum: pending, approved, hidden) - Mặc định: pending
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Công nghệ sử dụng

- **Backend**: PHP 8.x, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Framework CSS**: Bootstrap 5
- **Icons**: Font Awesome 6
- **Editor**: CKEditor 4 (Full version)
- **Server**: Laragon

## Tính năng nổi bật

✅ Hệ thống phân quyền (Admin/User)  
✅ Bảo mật: Password hash, SQL injection prevention  
✅ Upload nhiều ảnh (nhà hàng, đánh giá, CKEditor)  
✅ Rich text editor với upload ảnh  
✅ AJAX cho like/dislike  
✅ Responsive design  
✅ Tìm kiếm & lọc nâng cao  
✅ Hệ thống duyệt đánh giá  
✅ Quản lý trạng thái user (khóa/mở khóa)  

## Lưu ý

- Tất cả đánh giá mới cần **admin duyệt** trước khi hiển thị
- User bị khóa sẽ tự động logout
- Ảnh upload qua CKEditor lưu trong `assets/images/restaurants/`
- Ảnh đánh giá lưu trong `assets/images/reviews/`


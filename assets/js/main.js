$(document).ready(function() {
    // Thêm hiệu ứng fadeIn cho các card nhà hàng
    $('.restaurant-card').addClass('fadeIn');
    
    // Xử lý sự kiện đánh giá sao
    $('.rating-input label').click(function() {
        var value = $(this).prev('input').val();
        $('#rating-value').text(value);
    });
    
    // Xử lý tải ảnh lên (hiển thị xem trước)
    $('#review-images').change(function() {
        var previewContainer = $('#image-preview');
        previewContainer.empty();
        
        if (this.files) {
            for (var i = 0; i < this.files.length; i++) {
                if (i >= 5) break; // Giới hạn tối đa 5 ảnh
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = $('<img class="preview-img me-2 mb-2" width="100" height="100">');
                    img.attr('src', e.target.result);
                    previewContainer.append(img);
                }
                reader.readAsDataURL(this.files[i]);
            }
        }
    });
    
    // Xử lý hiệu ứng cuộn trang
    $(window).scroll(function() {
        var scrollDistance = $(window).scrollTop();
        
        // Hiển thị nút lên đầu trang khi cuộn xuống
        if (scrollDistance > 300) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });
    
    // Xử lý cuộn lên đầu trang khi click vào nút
    $('.scroll-to-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 'slow');
    });
    
    // Thêm hiệu ứng hoạt hình khi hiển thị review
    setTimeout(function() {
        $('.review-card').each(function(index) {
            var card = $(this);
            setTimeout(function() {
                card.addClass('slideUp');
            }, index * 100);
        });
    }, 300);
    
    // Xử lý modal hình ảnh
    $('.review-image').click(function() {
        var imgSrc = $(this).attr('src');
        $('#imageModal .modal-img').attr('src', imgSrc);
        $('#imageModal').modal('show');
    });
    
    // Xác thực form đăng ký
    $('#register-form').submit(function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm-password').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            $('#password-error').text('Mật khẩu xác nhận không khớp');
        }
    });
    
    // Xử lý like/dislike review
    $('.like-btn, .dislike-btn').on('click', function() {
        var btn = $(this);
        var reviewId = btn.data('review-id');
        var clickedAction = btn.data('action'); // 'like' hoặc 'dislike'
        
        // Kiểm tra trạng thái hiện tại từ localStorage
        var storageKey = 'review_vote_' + reviewId;
        var currentVote = localStorage.getItem(storageKey); // null, 'like', hoặc 'dislike'
        
        var action = '';
        var newVote = null;
        
        if (currentVote === clickedAction) {
            // Click vào cùng nút -> Bỏ vote
            action = 'remove_' + clickedAction;
            newVote = null;
        } else if (currentVote && currentVote !== clickedAction) {
            // Đang vote cái khác -> Chuyển vote (bỏ cái cũ, thêm cái mới)
            // Gửi 2 request: remove cái cũ và add cái mới
            $.ajax({
                url: 'ajax/like_review.php',
                method: 'POST',
                data: {
                    review_id: reviewId,
                    action: 'remove_' + currentVote
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Sau khi remove, thêm vote mới
                        $.ajax({
                            url: 'ajax/like_review.php',
                            method: 'POST',
                            data: {
                                review_id: reviewId,
                                action: clickedAction
                            },
                            dataType: 'json',
                            success: function(response2) {
                                if (response2.success) {
                                    updateVoteUI(btn, reviewId, clickedAction, response2.likes, response2.dislikes);
                                    localStorage.setItem(storageKey, clickedAction);
                                }
                            }
                        });
                    }
                }
            });
            return;
        } else {
            // Chưa vote -> Thêm vote mới
            action = clickedAction;
            newVote = clickedAction;
        }
        
        // Gửi request
        $.ajax({
            url: 'ajax/like_review.php',
            method: 'POST',
            data: {
                review_id: reviewId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateVoteUI(btn, reviewId, newVote, response.likes, response.dislikes);
                    
                    // Lưu trạng thái
                    if (newVote) {
                        localStorage.setItem(storageKey, newVote);
                    } else {
                        localStorage.removeItem(storageKey);
                    }
                } else {
                    alert(response.message || 'Có lỗi xảy ra.');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi thực hiện hành động.');
            }
        });
    });
    
    // Hàm cập nhật UI
    function updateVoteUI(btn, reviewId, currentVote, likes, dislikes) {
        var container = btn.closest('.gap-3');
        var likeBtn = container.find('.like-btn');
        var dislikeBtn = container.find('.dislike-btn');
        
        // Cập nhật số lượng
        container.find('.like-count').text(likes);
        container.find('.dislike-count').text(dislikes);
        
        // Reset trạng thái button
        likeBtn.removeClass('btn-primary active');
        dislikeBtn.removeClass('btn-danger active');
        
        // Highlight button đang được chọn
        if (currentVote === 'like') {
            likeBtn.addClass('btn-primary active');
        } else if (currentVote === 'dislike') {
            dislikeBtn.addClass('btn-danger active');
        }
    }
    
    // Khôi phục trạng thái vote khi load trang
    $('.like-btn, .dislike-btn').each(function() {
        var btn = $(this);
        var reviewId = btn.data('review-id');
        var storageKey = 'review_vote_' + reviewId;
        var currentVote = localStorage.getItem(storageKey);
        
        if (currentVote) {
            var container = btn.closest('.gap-3');
            if (currentVote === 'like') {
                container.find('.like-btn').addClass('btn-primary active');
            } else if (currentVote === 'dislike') {
                container.find('.dislike-btn').addClass('btn-danger active');
            }
        }
    });
}); 
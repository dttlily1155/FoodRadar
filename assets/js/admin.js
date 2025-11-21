$(document).ready(function() {
    // Toggle sidebar
    $('#sidebarCollapse').on('click', function() {
        $('#sidebar, #content').toggleClass('active');
    });
    
    // Confirm before delete actions
    $('.btn-danger[href*="delete"]').on('click', function(e) {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            e.preventDefault();
        }
    });
    
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Table row hover effect
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
    
    // Preview uploaded images
    $('input[type="file"]').on('change', function() {
        var files = this.files;
        var previewContainer = $(this).siblings('.image-preview');
        
        if (previewContainer.length === 0) {
            previewContainer = $('<div class="image-preview mt-2"></div>');
            $(this).after(previewContainer);
        }
        
        previewContainer.empty();
        
        if (files.length > 0) {
            for (var i = 0; i < files.length; i++) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = $('<img>').attr('src', e.target.result)
                                        .addClass('img-thumbnail me-1 mb-1')
                                        .css({
                                            'width': '80px',
                                            'height': '80px',
                                            'object-fit': 'cover'
                                        });
                    previewContainer.append(img);
                };
                reader.readAsDataURL(files[i]);
            }
        }
    });
});

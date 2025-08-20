/**
 * AJAX Login Handler
 */
jQuery(document).ready(function($) {
    
    // Handle login form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $('#login-btn');
        var $errorMsg = $('#error-message');
        var $successMsg = $('#success-message');
        var $messagesWrapper = $('.form-messages');
        
        // Get form data
        var formData = {
            action: 'honors_account_login',
            log: $('#user_login').val(),
            pwd: $('#user_pass').val(),
            nonce: define.nonce
        };
        
        // Show loading state
        $btn.prop('disabled', true).text('로그인 중...');
        $errorMsg.hide();
        $successMsg.hide();
        $messagesWrapper.hide();
        
        // Send AJAX request
        $.ajax({
            url: define.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Login successful
                    $messagesWrapper.show();
                    $successMsg.text('로그인 성공! 리다이렉트 중...').show();
                    
                    // Redirect after short delay
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url || '/management';
                    }, 1000);
                    
                } else {
                    // Login failed
                    $messagesWrapper.show();
                    $errorMsg.text(response.data.message || '로그인에 실패했습니다.').show();
                    $btn.prop('disabled', false).text('로그인');
                }
            },
            error: function(xhr, status, error) {
                // AJAX error
                $messagesWrapper.show();
                $errorMsg.text('서버 오류가 발생했습니다. 다시 시도해주세요.').show();
                $btn.prop('disabled', false).text('로그인');
                console.error('Login AJAX error:', error);
            }
        });
    });
    
    // Clear error message when user starts typing
    $('input').on('input', function() {
        var $errorMsg = $('#error-message');
        var $successMsg = $('#success-message');
        var $messagesWrapper = $('.form-messages');
        $errorMsg.hide();
        if (!$successMsg.is(':visible') && !$errorMsg.is(':visible')) {
            $messagesWrapper.hide();
        }
    });
    
    // Handle Enter key
    $('input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#login-form').submit();
        }
    });
});

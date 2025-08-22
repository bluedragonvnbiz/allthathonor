jQuery(document).ready(function($) {
    // Set active navigation
    $("nav.main-nav li:nth-child(3) a").addClass("active");
    
    // Form elements
    var $form = $("#inquiry-form");
    var $submitBtn = $("#submit-btn");
    var $messageDiv = $("#form-message");
    var $categoryMain = $("#category_main");
    var $categorySub = $("#category_sub");
    
    // Category data
    var categories = {
        '멤버십': {
            '이용권 사용방법': '이용권 사용방법',
            '멤버십 혜택': '멤버십 혜택',
            '멤버십 가입': '멤버십 가입',
            '멤버십 해지': '멤버십 해지',
            '기타': '기타'
        },
        '상품': {
            '상품 문의': '상품 문의',
            '상품 구매': '상품 구매',
            '상품 환불': '상품 환불',
            '기타': '기타'
        },
        '서비스': {
            '서비스 이용': '서비스 이용',
            '서비스 문의': '서비스 문의',
            '기타': '기타'
        },
        '기타': {
            '일반 문의': '일반 문의',
            '불만 사항': '불만 사항',
            '제안 사항': '제안 사항',
            '기타': '기타'
        }
    };
    
    // Update sub category when main category changes
    $categoryMain.on('change', function() {
        var mainCategory = $(this).val();
        $categorySub.empty().append('<option value="">서브 카테고리 선택</option>');
        
        if (mainCategory && categories[mainCategory]) {
            console.log('Loading sub categories for:', mainCategory);
            $.each(categories[mainCategory], function(key, value) {
                $categorySub.append('<option value="' + value + '">' + value + '</option>');
            });
        } else {
            console.log('No sub categories found for:', mainCategory);
        }
        
        checkFormValidity();
    });
    
    // Check form validity
    function checkFormValidity() {
        var isValid = true;
        
        // Check all required fields
        $form.find("input[required], select[required], textarea[required]").each(function() {
            var $field = $(this);
            var fieldValue = $field.val();
            
            if ($field.is(":checkbox")) {
                if (!$field.is(":checked")) {
                    isValid = false;
                    return false;
                }
            } else {
                // Check if fieldValue exists and is not empty after trimming
                if (!fieldValue || (typeof fieldValue === 'string' && !fieldValue.trim())) {
                    isValid = false;
                    return false;
                }
            }
        });
        
        // Update submit button state
        $submitBtn.prop("disabled", !isValid);
    }
    
    // Add event listeners for form validation
    $form.find("input[required], select[required], textarea[required]").on("input change", checkFormValidity);
    
    // Handle form submission
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button and show loading
        $submitBtn.prop('disabled', true).text('전송 중...');
        $messageDiv.html('').removeClass('alert alert-success alert-danger');
        
        // Collect form data
        var formData = {
            action: 'submit_inquiry',
            nonce: $('#inquiry_nonce').val(),
            corporate_name: $('input[name="corporate_name"]').val(),
            contact_person: $('input[name="contact_person"]').val(),
            contact_phone: $('input[name="contact_phone"]').val(),
            email: $('input[name="email"]').val(),
            category_main: $categoryMain.val(),
            category_sub: $categorySub.val(),
            inquiry_content: $('textarea[name="inquiry_content"]').val()
        };
        
        // Debug: log form data
        console.log('Form data being sent:', formData);
        
        // Send AJAX request
        $.ajax({
            url: define.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $messageDiv.html(
                        '<div class="alert alert-success">' +
                        '<strong>성공!</strong> ' + response.data.message +
                        '<br>문의번호: ' + response.data.inquiry_number +
                        '</div>'
                    );
                    
                    // Reset form
                    $form[0].reset();
                    $categorySub.empty().append('<option value="">서브 카테고리 선택</option>');
                    checkFormValidity();
                    
                } else {
                    // Show error message
                    var errorMessage = response.data.message;
                    if (response.data.errors && response.data.errors.length > 0) {
                        errorMessage += '<ul class="mb-0 mt-2">';
                        response.data.errors.forEach(function(error) {
                            errorMessage += '<li>' + error + '</li>';
                        });
                        errorMessage += '</ul>';
                    }
                    
                    $messageDiv.html(
                        '<div class="alert alert-danger">' +
                        '<strong>오류!</strong> ' + errorMessage +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', xhr.status, xhr.statusText);
                console.log('Response:', xhr.responseText);
                
                // Show error message
                var errorMsg = '서버 오류가 발생했습니다. 다시 시도해주세요.';
                if (xhr.status === 400) {
                    errorMsg = '잘못된 요청입니다. 입력 정보를 확인해주세요.';
                } else if (xhr.status === 403) {
                    errorMsg = '보안 검증에 실패했습니다. 페이지를 새로고침해주세요.';
                }
                
                $messageDiv.html(
                    '<div class="alert alert-danger">' +
                    '<strong>오류!</strong> ' + errorMsg +
                    '</div>'
                );
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false).text('send now');
                checkFormValidity();
            }
        });
    });
    
    // Email validation
    $('input[name="email"]').on('blur', function() {
        var email = $(this).val();
        if (email && !isValidEmail(email)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">올바른 이메일 형식을 입력해주세요.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Phone validation
    $('input[name="contact_phone"]').on('blur', function() {
        var phone = $(this).val();
        if (phone && !isValidPhone(phone)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">올바른 전화번호를 입력해주세요. (최소 7자리 이상)</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Email validation function
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Phone validation function - flexible international phone validation
    function isValidPhone(phone) {
        // Remove all non-digit characters except +, -, space, and parentheses
        var cleanPhone = phone.replace(/[^\d\-\+\s\(\)]/g, '');
        
        // Basic phone validation - should have at least 7 digits
        var digitsOnly = cleanPhone.replace(/[^\d]/g, '');
        
        // Check if it's a valid phone number (7-15 digits is typical for international numbers)
        return digitsOnly.length >= 7 && digitsOnly.length <= 15;
    }
    
    // Initial form validation check
    setTimeout(checkFormValidity, 100);
});

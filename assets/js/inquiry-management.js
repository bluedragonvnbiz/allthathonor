jQuery(document).ready(function($) {
    // Open media library
    function openMediaLibrary(fileInput) {
        const container = fileInput.closest('.upload-box');
        const fieldName = fileInput.dataset.fieldName || fileInput.name;
        const hiddenInput = container.querySelector(`input[name="${fieldName}"]`);
        // Get current selected image URL
        let currentImageUrl = fileInput.dataset.mediaUrl || (hiddenInput ? hiddenInput.value : '');
        
        const frame = wp.media({
            title: '이미지 선택',
            button: {
                text: '선택'
            },
            multiple: false
        });
        
        // If there's a current image, select it in the library
        if (currentImageUrl) {
            frame.on('open', function() {
                const selection = frame.state().get('selection');
                
                // Try to get attachment ID from URL
                $.ajax({
                    url: define.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_attachment_id_by_url',
                        url: currentImageUrl,
                        nonce: $('input[name="nonce"]').first().val()
                    },
                    success: function(response) {
                        if (response.success && response.data.attachment_id) {
                            const attachment = wp.media.attachment(response.data.attachment_id);
                            attachment.fetch().then(function() {
                                selection.add([attachment]);
                            });
                        }
                    }
                });
            });
        }
        
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            const fieldName = fileInput.dataset.fieldName;
            const hiddenInput = document.querySelector(`input[name="${fieldName}"]`);
            
            // Update file input display - find the correct elements
            const $fileInput = $(fileInput);
            const $fileName = $fileInput.closest('.upload-box').find('.file-name');
            
            if ($fileName.length) {
                $fileName.val(attachment.filename);
            }
            
            // Store URL
            fileInput.dataset.mediaUrl = attachment.url;
            if (hiddenInput) hiddenInput.value = attachment.url;
            
            // Trigger validation
            checkFormValidity();
        });
        
        frame.open();
    }

    function openQuillMediaLibrary(quill) {
        const frame = wp.media({
            title: '이미지 선택',
            button: { text: '삽입' },
            multiple: false,
            library: { type: 'image' }
        });
        
        frame.on('select', () => {
            const attachment = frame.state().get('selection').first().toJSON();
            const imageUrl = attachment.url;
            const imageTitle = attachment.title || attachment.filename;
            
            // Insert image into Quill
            const range = quill.getSelection();
            quill.insertEmbed(range.index, 'image', imageUrl);
            
            // Trigger text-change event to enable submit button
            setTimeout(() => {
                $('.btn-submit-answer').prop('disabled', false);
            }, 100);
        });
        
        frame.open();
    }

    $(document).on('click', '.select-file', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openMediaLibrary(this);
    });
        
    // Category data (same as inquiry-form.js)
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
    
    // Form elements
    var $categoryMain = $("#category_main");
    var $categorySub = $("#category_sub");

    // get category_sub from url
    var urlParams = new URLSearchParams(window.location.search);
    var categorySub = urlParams.get('category_sub');
    if (categorySub) {
        categorySub = decodeURIComponent(categorySub);
    }
    
    // Update sub category when main category changes
    $categoryMain.on('change', function() {
        var mainCategory = $(this).val();
        $categorySub.empty().append('<option value="">서브 카테고리 선택</option>');
        
        if (mainCategory && categories[mainCategory]) {
            $.each(categories[mainCategory], function(key, value) {
                var isSelected = (value === categorySub) ? 'selected' : '';
                $categorySub.append('<option value="' + value + '" ' + isSelected + '>' + value + '</option>');
            });
        }
    });

    // Trigger change event for main category to populate sub categories
    $categoryMain.trigger('change');
    
    // Reset search form
    window.resetSearch = function() {
        $('select[name="search_type"]').val('corporate_name');
        $('input[name="search_keyword"]').val('');
        $categoryMain.val('');
        $categorySub.empty().append('<option value="">서브 카테고리 선택</option>');
        $('input[name="status[]"]').prop('checked', false);
        $('#status_all').prop('checked', true);
        
        // Redirect to first page without search parameters
        window.location.href = window.location.pathname;
    };

    // Handle answer form submission (for inquiry view page)
    let currentFormData = null;
    let currentSubmitBtn = null;
    let currentOriginalText = null;
    
    $('#answer-form').on('submit', function(e) {
        e.preventDefault();
        
        // Store form data and button info
        currentFormData = new FormData(this);
        currentSubmitBtn = $('.btn-submit-answer');
        currentOriginalText = currentSubmitBtn.text();
        
        // Show confirmation modal
        $('#confirmSubmitModal').modal('show');
    });
    
    // Handle confirm button click
    $('#confirmSubmitBtn').on('click', function() {
        // Hide modal
        $('#confirmSubmitModal').modal('hide');
        
        // Disable button and show loading
        currentSubmitBtn.prop('disabled', true).text('전송 중...');
        
        $.ajax({
            url: define.ajax_url,
            type: 'POST',
            data: currentFormData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('답변이 성공적으로 저장되었습니다.');
                    location.reload();
                } else {
                    alert('오류가 발생했습니다: ' + (response.data?.message || '알 수 없는 오류'));
                }
            },
            error: function() {
                alert('네트워크 오류가 발생했습니다.');
            },
            complete: function() {
                currentSubmitBtn.prop('disabled', false).text(currentOriginalText);
            }
        });
    });

    // Enhanced Quill editor initialization for answer form
    function initAnswerEditor() {
        const textarea = document.getElementById('answer_content');
        if (!textarea) return;
        
        const container = $('<div>').css({
            height: '300px',
            marginBottom: '20px'
        });
        
        $(textarea).before(container).hide();
        
        const quill = new Quill(container[0], {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: '답변 내용을 입력해주세요.'
        });

        // Custom image handler
        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', () => {
            openQuillMediaLibrary(quill);
        });
        
        // Sync with textarea and handle submit button
        quill.on('text-change', function() {
            const content = quill.getText().trim();
            textarea.value = quill.root.innerHTML;
            
            // Enable/disable submit button based on content
            const submitBtn = $('.btn-submit-answer');
            if (content.length > 0) {
                submitBtn.prop('disabled', false);
            } else {
                submitBtn.prop('disabled', true);
            }
        });
        
        // Load existing content
        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
            quill.trigger('text-change');
        }
        
        textarea.quillInstance = quill;
    }

    // Initialize answer editor if on view page
    if ($('#answer-form').length > 0) {
        initAnswerEditor();
    }
});

/**
 * Simple Voucher Form Handler with jQuery
 */
jQuery(document).ready(function($) {
    
    // Required fields for validation
    const requiredFields = [
        'section_status',
        'section_image', 
        'content_name',
        'content_short_description',
        'content_detail_description'
    ];
    
    // Initialize Quill editors
    function initQuillEditors() {
        $('.rich-text-editor').each(function() {
            const editor = this;
            const container = $('<div>').css({
                height: '300px',
                marginBottom: '20px'
            });
            
            $(editor).before(container).hide();
            
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
                placeholder: '내용을 입력해주세요.'
            });
            
            // Sync with textarea
            quill.on('text-change', function() {
                editor.value = quill.root.innerHTML;
                checkFormValidity();
            });
            
            // Load existing content
            if (editor.value) {
                quill.root.innerHTML = editor.value;
            }
            
            editor.quillInstance = quill;
        });
    }
    
    // Check if form is valid
    function checkFormValidity() {
        let isValid = true;
        
        requiredFields.forEach(function(fieldName) {
            const $field = $(`[name="${fieldName}"]`);
            
            if (!$field.length) {
                isValid = false;
                return;
            }
            
            let value = '';
            
            if ($field.attr('type') === 'radio') {
                value = $(`[name="${fieldName}"]:checked`).val() || '';
            } else if ($field.hasClass('rich-text-editor')) {
                if ($field[0].quillInstance) {
                    value = $field[0].quillInstance.getText().trim();
                } else {
                    value = $field.val().trim();
                }
            } else if (fieldName === 'section_image') {
                const $hiddenInput = $(`input[name="${fieldName}"]`);
                const $fileInput = $(`input[data-field-name="${fieldName}"]`);
                
                if ($hiddenInput.length && $hiddenInput.val()) {
                    value = $hiddenInput.val();
                } else if ($fileInput.length && $fileInput[0].dataset.mediaUrl) {
                    value = $fileInput[0].dataset.mediaUrl;
                }
            } else {
                value = $field.val().trim();
            }
            
            if (!value) {
                isValid = false;
            }
        });
        
        // Update submit button
        $('.btn-submit-voucher').prop('disabled', !isValid);
    }
    
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
    
    // Collect form data
    function collectFormData() {
        const formData = new FormData();
        const processedFields = new Set();
        
        const voucherId = $('input[name="voucher_id"]').val();
        if (voucherId) {
            formData.append('voucher_id', voucherId);
            formData.append('action', 'update_voucher');
        } else {
            formData.append('action', 'create_voucher');
        }
        
        // Get nonce
        const nonceInput = $('input[name="nonce"]').first();
        if (nonceInput.length) {
            formData.append('nonce', nonceInput.val());
        }
        
        // Collect from all section forms
        $('.section-form').each(function() {
            const $form = $(this);
            
            // Regular fields
            $form.find('input, textarea, select').each(function() {
                const field = this;
                if (!field.name || field.name.includes('nonce') || field.name.includes('action') || 
                    $(field).hasClass('file-name') || $(field).hasClass('rich-text-editor')) {
                    return;
                }
                
                let serverFieldName = field.name;
                if (field.name.startsWith('section_')) {
                    serverFieldName = field.name.replace('section_', '');
                } else if (field.name.startsWith('content_')) {
                    serverFieldName = field.name.replace('content_', '');
                }
                
                // Map field names to match database columns
                const fieldMapping = {
                    'voucher_name': 'name',
                    'summary_description': 'short_description',
                    'detailed_description': 'detail_description'
                };
                
                if (fieldMapping[serverFieldName]) {
                    serverFieldName = fieldMapping[serverFieldName];
                }
                
                if (processedFields.has(serverFieldName)) return;
                
                if (field.type === 'radio' && field.checked) {
                    formData.append(serverFieldName, field.value);
                    processedFields.add(serverFieldName);
                } else if (field.type === 'checkbox') {
                    // Handle checkbox arrays - only append if checked
                    if (field.checked) {
                        formData.append(serverFieldName, field.value);
                    }
                    // Don't add to processedFields for checkboxes as they can have multiple values
                } else if (field.type !== 'radio') {
                    formData.append(serverFieldName, field.value);
                    processedFields.add(serverFieldName);
                }
            });
            
            // File inputs
            $form.find('input[type="file"].select-file').each(function() {
                const fieldName = this.dataset.fieldName;
                if (this.files.length > 0 || this.dataset.mediaUrl) {
                    let serverFieldName = fieldName;
                    if (fieldName.startsWith('section_')) {
                        serverFieldName = fieldName.replace('section_', '');
                    } else if (fieldName.startsWith('content_')) {
                        serverFieldName = fieldName.replace('content_', '');
                    }
                    
                    // Map field names to match database columns
                    const fieldMapping = {
                        'voucher_name': 'name',
                        'summary_description': 'short_description',
                        'detailed_description': 'detail_description'
                    };
                    
                    if (fieldMapping[serverFieldName]) {
                        serverFieldName = fieldMapping[serverFieldName];
                    }
                    
                    if (!processedFields.has(serverFieldName)) {
                        formData.append(serverFieldName, this.dataset.mediaUrl || this.files[0]);
                        processedFields.add(serverFieldName);
                    }
                }
            });
            
            // Rich text editors
            $form.find('.rich-text-editor').each(function() {
                let serverFieldName = this.name;
                if (this.name.startsWith('section_')) {
                    serverFieldName = this.name.replace('section_', '');
                } else if (this.name.startsWith('content_')) {
                    serverFieldName = this.name.replace('content_', '');
                }
                
                if (processedFields.has(serverFieldName)) return;
                
                if (this.quillInstance) {
                    formData.append(serverFieldName, this.quillInstance.root.innerHTML);
                } else {
                    formData.append(serverFieldName, this.value);
                }
                processedFields.add(serverFieldName);
            });
            
            // Handle unchecked checkboxes - send empty arrays for array fields
            const checkboxFields = ['category', 'type', 'status'];
            checkboxFields.forEach(function(fieldName) {
                if (!processedFields.has(fieldName)) {
                    // Check if any checkbox with this name exists
                    const $checkboxes = $form.find(`input[name="${fieldName}[]"]`);
                    if ($checkboxes.length > 0) {
                        // If no checkboxes are checked, send empty array
                        const checkedValues = $checkboxes.filter(':checked').map(function() {
                            return this.value;
                        }).get();
                        
                        if (checkedValues.length === 0) {
                            formData.append(fieldName, '[]');
                        }
                    }
                }
            });
        });
        
        return formData;
    }
    
    // Send AJAX request
    function sendAjaxRequest(formData) {
        return $.ajax({
            url: define.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        });
    }
    
    // Handle form submission
    function handleSubmit() {
        const $submitBtn = $('.btn-submit-voucher');
        const originalText = $submitBtn.text();
        
        $submitBtn.prop('disabled', true).text('저장 중...');
        
        const formData = collectFormData();
        
        sendAjaxRequest(formData)
            .done(function(response) {
                if (response.success) {
                    alert('Voucher saved successfully!');
                    
                    // Get return URL from hidden field or fallback to voucher list
                    const returnUrlField = $('input[name="return_url"]');
                    const returnUrl = returnUrlField.length ? returnUrlField.val() : '/admin/voucher/';
                    
                    window.location.href = returnUrl;
                } else {
                    alert('Error: ' + (response.data?.message || 'Unknown error'));
                }
            })
            .fail(function(xhr, status, error) {
                alert('Error: ' + error);
            })
            .always(function() {
                $submitBtn.prop('disabled', false).text(originalText);
            });
    }
    
    // Event handlers
    $(document).on('click', '.btn-submit-voucher', function(e) {
        e.preventDefault();
        handleSubmit();
    });
    
    $(document).on('click', '.select-file', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openMediaLibrary(this);
    });
    
    // Validation triggers
    $(document).on('input change', '.voucher-form input, .voucher-form textarea, .voucher-form select', function() {
        checkFormValidity();
    });
    
    // Initialize
    initQuillEditors();
    checkFormValidity();
    
    // Check again after a delay
    setTimeout(checkFormValidity, 500);
    setTimeout(checkFormValidity, 1000);
});

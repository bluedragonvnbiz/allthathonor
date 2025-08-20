/**
 * Product Form Handler
 * Handle product form submission and interactions
 */
class ProductFormHandler {
    constructor() {
        // Use jQuery ready with $ parameter
        jQuery(document).ready(($) => {
            this.init($);
        });
    }
    
    init($) {
        this.$ = $; // Store jQuery reference
        this.bindEvents();
        this.initRichTextEditors();
        this.loadProductData();
    }
    
    bindEvents() {
        this.$(document).on('submit', '.product-form', (e) => {
            e.preventDefault();
            this.handleProductFormSubmit(e.target);
        });
        
        this.$(document).on('click', '.select-file', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.openMediaLibrary(e.target);
        });
        
        // Add real-time validation for submit button
        this.setupSubmitButtonValidation();
    }

    initRichTextEditors() {
        const editors = document.querySelectorAll('.rich-text-editor');
        if (!editors.length) return;
        
        // Use Quill.js
        if (typeof Quill !== 'undefined') {
            editors.forEach(editor => {
                // Create Quill container
                const container = document.createElement('div');
                container.style.height = '300px';
                container.style.marginBottom = '20px';
                editor.parentNode.insertBefore(container, editor);
                editor.style.display = 'none';
                
                // Initialize Quill
                const quill = new Quill(container, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Write something...'
                });
                
                // Custom image handler
                const toolbar = quill.getModule('toolbar');
                toolbar.addHandler('image', () => {
                    this.openQuillMediaLibrary(quill);
                });
                
                // Sync with original textarea
                quill.on('text-change', () => {
                    editor.value = quill.root.innerHTML;
                });
                
                // Load existing content
                if (editor.value) {
                    quill.root.innerHTML = editor.value;
                }
                
                // Store Quill instance
                editor.quillInstance = quill;
                
                // Add text-change listener for validation
                quill.on('text-change', () => {
                    // Trigger form validation
                    this.$('.product-form').trigger('input');
                });
            });
        } else {
            // Fallback to styled textarea
            editors.forEach(editor => {
                Object.assign(editor.style, {
                    minHeight: '200px',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    fontSize: '14px',
                    padding: '10px',
                    border: '1px solid #ddd',
                    borderRadius: '4px'
                });
            });
        }
    }
    
    async openMediaLibrary(fileInput) {
        const container = fileInput.closest('.upload-box');
        const fileNameInput = container.querySelector('.file-name');
        const fieldName = fileInput.dataset.fieldName || fileInput.name;
        const hiddenInput = container.querySelector(`input[name="${fieldName}"]`);
        
        let currentImageUrl = fileInput.dataset.mediaUrl || (hiddenInput ? hiddenInput.value : '');
        let attachmentId = null;
        
        if (currentImageUrl && !fileInput.files.length) {
            attachmentId = await this.getAttachmentIdByUrl(currentImageUrl);
        }
        
        const frame = wp.media({
            title: '이미지 선택',
            button: { text: '선택' },
            multiple: false,
            library: { type: 'image' }
        });
        
        if (currentImageUrl) {
            frame.on('ready open', () => {
                this.selectAttachmentInLibrary(frame, currentImageUrl, attachmentId);
            });
        }
        
        frame.on('select', () => {
            const attachment = frame.state().get('selection').first().toJSON();
            if (fileNameInput) fileNameInput.value = attachment.filename || attachment.title;
            
            const dataTransfer = new DataTransfer();
            const file = new File([], attachment.filename || attachment.title, { 
                type: attachment.mime_type || 'image/jpeg' 
            });
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            fileInput.dataset.mediaUrl = attachment.url;
            if (hiddenInput) hiddenInput.value = attachment.url;
            
            // Trigger form validation after image selection
            this.$('.product-form').trigger('input');
        });
        
        frame.open();
    }
    
    // Open WordPress Media Library for TinyMCE
    openQuillMediaLibrary(quill) {
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
        });
        
        frame.open();
    }
    
    selectAttachmentInLibrary(frame, currentImageUrl, attachmentId) {
        const selection = frame.state().get('selection');
        selection.reset();
        
        if (attachmentId?.attachment_id) {
            const attachment = wp.media.attachment(attachmentId.attachment_id);
            attachment.fetch().then(() => selection.add(attachment));
        } else {
            const library = frame.state().get('library');
            library.each((attachment) => {
                const urls = [
                    attachment.get('url'),
                    attachment.get('sizes')?.full?.url,
                    attachment.get('sizes')?.medium?.url
                ];
                
                if (urls.includes(currentImageUrl)) {
                    selection.add(attachment);
                    return false;
                }
            });
        }
    }
    
    async getAttachmentIdByUrl(url) {
        try {
            const form = document.querySelector('.product-form');
            const nonce = form?.querySelector('input[name="nonce"]')?.value || '';
            
            const response = await fetch(define.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'get_attachment_id_by_url',
                    url: url,
                    nonce: nonce
                })
            });
            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Error getting attachment ID:', error);
            return null;
        }
    }
    
    handleProductFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        const action = form.dataset.action;
        
        submitBtn.disabled = true;
        submitBtn.textContent = '저장 중...';
        
        this.sendAjaxRequest(this.collectFormData(form, action))
            .then(response => {
                this.showSuccessMessage('저장되었습니다!');
                submitBtn.textContent = '저장됨';
                setTimeout(() => submitBtn.textContent = originalText, 2000);
                
                if (action === 'create') {
                    setTimeout(() => window.location.href = '/product/', 100);
                } else {
                    setTimeout(() => window.location.href = '/product/view/?id=' + response.data.product_id, 100);
                }
            })
            .catch(error => {
                this.showErrorMessage(error.message || '저장에 실패했습니다.');
                submitBtn.textContent = originalText;
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
    }
        
    setupSubmitButtonValidation() {
        const $form = this.$('.product-form');
        if (!$form.length) return;
        
        const $submitBtn = $form.find('button[type="submit"]');
        if (!$submitBtn.length) return;
        
        // Initially disable submit button
        $submitBtn.prop('disabled', true);
        
        // Define required fields (with product_ prefix)
        const requiredFields = [
            { name: 'product_product_name', label: '상품명' },
            { name: 'product_product_name_en', label: '상품명(영문)' },
            { name: 'product_summary_description', label: '요약 설명' },
            { name: 'product_detailed_description', label: '상세 설명' },
            { name: 'product_main_image', label: '메인 이미지' },
            { name: 'product_exposure_status', label: '노출상태' }
        ];
        
        // Function to check if all required fields are filled
        const checkFormValidity = () => {
            let isValid = true;
            
            requiredFields.forEach(field => {
                const $element = $form.find(`[name="${field.name}"]`);
                if (!$element.length) return;
                
                let value = '';
                
                if ($element.attr('type') === 'radio') {
                    const $checkedRadio = $form.find(`[name="${field.name}"]:checked`);
                    value = $checkedRadio.length ? $checkedRadio.val() : '';
                } else if ($element.hasClass('rich-text-editor')) {
                    // Check Quill.js content
                    if ($element[0].quillInstance) {
                        value = $element[0].quillInstance.getText().trim();
                    } else {
                        value = $element.val().trim();
                    }
                } else {
                    value = $element.val().trim();
                }
                
                if (!value) {
                    isValid = false;
                }
            });
            
            // Update submit button state
            if (isValid) {
                $submitBtn.prop('disabled', false);
            } else {
                $submitBtn.prop('disabled', true);
            }
        };
        
        // Add event listeners for all form fields
        $form.on('input change', checkFormValidity);
        
        // Special handling for Quill.js
        this.$('.rich-text-editor').each(function() {
            if (this.quillInstance) {
                this.quillInstance.on('text-change', checkFormValidity);
            }
        });
        
        // Check initial state
        setTimeout(checkFormValidity, 100);
        
        // Also check after a longer delay to ensure all elements are loaded
        setTimeout(checkFormValidity, 500);
    }

    collectFormData(form, action) {
        const formData = new FormData();
        
        formData.append('action', action === 'create' ? 'create_product' : 'update_product');
        formData.append('nonce', form.querySelector('input[name="nonce"]').value);
        
        // Collect form fields
        form.querySelectorAll('input, textarea, select').forEach(field => {
            if (!field.name || field.name.includes('nonce') || field.name.includes('action') || 
                field.classList.contains('file-name') || field.classList.contains('rich-text-editor')) return;
            
            // Convert field name from product_field_name to field_name for server
            let serverFieldName = field.name;
            if (field.name.startsWith('product_')) {
                serverFieldName = field.name.replace('product_', '');
            }
            
            if (field.type === 'radio' && field.checked) {
                formData.append(serverFieldName, field.value);
            } else if (field.type !== 'radio') {
                formData.append(serverFieldName, field.value);
            }
        });
        
        // Handle file inputs separately (they don't have name attribute anymore)
        form.querySelectorAll('input[type="file"].select-file').forEach(fileInput => {
            const fieldName = fileInput.dataset.fieldName;
            if (fileInput.files.length > 0) {
                let serverFieldName = fieldName;
                if (fieldName.startsWith('product_')) {
                    serverFieldName = fieldName.replace('product_', '');
                }
                formData.append(serverFieldName, fileInput.dataset.mediaUrl || fileInput.files[0]);
            }
        });
        
        // Add Quill.js content
        document.querySelectorAll('.rich-text-editor').forEach(editor => {
            if (editor.quillInstance) {
                let serverFieldName = editor.name;
                if (editor.name.startsWith('product_')) {
                    serverFieldName = editor.name.replace('product_', '');
                }
                formData.append(serverFieldName, editor.quillInstance.root.innerHTML);
            } else {
                let serverFieldName = editor.name;
                if (editor.name.startsWith('product_')) {
                    serverFieldName = editor.name.replace('product_', '');
                }
                formData.append(serverFieldName, editor.value);
            }
        });
        
        return formData;
    }

    async loadProductData(productId = null) {
        const form = document.querySelector('.product-form');
        if (!form || form.dataset.action !== 'update') return;

        const currentProductId = productId || form.dataset.productId;
        if (!currentProductId) return;

        try {
            const response = await fetch(define.ajax_url, {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'get_product',
                    nonce: form.querySelector('input[name="nonce"]').value,
                    product_id: currentProductId
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept-Charset': 'utf-8',
                },
            });
            const result = await response.json();

            if (result.success && result.data.product) {
                this.populateFormFields(form, result.data.product);
            }
        } catch (error) {
            console.error('Error loading product data:', error);
        }
    }
    
    populateFormFields(form, productData) {
        for (const [key, value] of Object.entries(productData)) {
            // Try both with and without product_ prefix
            let field = form.querySelector(`[name="${key}"]`);
            if (!field) {
                field = form.querySelector(`[name="product_${key}"]`);
            }
            if (!field) continue;
            
            if (field.type === 'radio') {
                const radioField = form.querySelector(`[name="${key}"][value="${value}"]`);
                if (radioField) radioField.checked = true;
            } else if (field.type === 'checkbox') {
                const values = Array.isArray(value) ? value : [value];
                values.forEach(val => {
                    const checkboxField = form.querySelector(`[name="${key}"][value="${val}"]`);
                    if (checkboxField) checkboxField.checked = true;
                });
            } else if (field.type === 'hidden' && field.id.endsWith('_display')) {
                const displayField = document.getElementById(`${key}_display`);
                if (displayField) {
                    displayField.value = value ? value.split('/').pop() : '';
                }
                field.value = value;
                field.dataset.mediaUrl = value;
            } else if (field.classList.contains('rich-text-editor')) {
                if (typeof tinymce !== 'undefined' && tinymce.get(field.id)) {
                    tinymce.get(field.id).setContent(value || '');
                } else {
                    field.value = value;
                }
            } else {
                field.value = value;
            }
        }
        
        // Trigger validation after populating fields
        setTimeout(() => {
            this.$('.product-form').trigger('input');
        }, 100);
    }
    
    async sendAjaxRequest(formData) {
        const response = await fetch(define.ajax_url, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'Accept-Charset': 'utf-8'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.data.message || 'Unknown error');
        }
        
        return data;
    }
    
    showSuccessMessage(message) {
        alert(message);
    }
    
    showErrorMessage(message) {
        alert(message);
    }
}

// Initialize product form handler
new ProductFormHandler();

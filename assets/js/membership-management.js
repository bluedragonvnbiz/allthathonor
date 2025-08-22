/**
 * Section Form Handler
 * Handle form submission and AJAX requests
 */
class MembershipManagementHandler {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        // Handle form submission
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('section-form')) {
                e.preventDefault();
                this.handleFormSubmit(e.target);
            }
        });
        
        // Handle file selection for image fields
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('select-file')) {
                this.handleFileSelection(e.target);
            }
        });
        
        // Handle WordPress Media Library selection (if available)
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('select-file') && typeof wp !== 'undefined' && wp.media) {
                e.preventDefault();
                e.stopPropagation();
                await this.openMediaLibrary(e.target);
            }
        });
        
        // Handle edit button click for view mode
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('btn-edit-section')) {
                e.preventDefault();
                await this.loadEditForm(e.target);
            }
        });
        
        // Handle membership benefits form submission
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-save-benefits')) {
                e.preventDefault();
                this.handleBenefitsSubmit(e.target);
            }
        });
        
        // Note: Category tab switching and checkbox handling are done in view.php
        // This file only handles form submission
    }
    
    /**
     * Open WordPress Media Library
     */
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
                // Add delay to ensure library is fully loaded
                setTimeout(() => {
                    this.selectAttachmentInLibrary(frame, currentImageUrl, attachmentId);
                }, 200);
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
        });
        
        frame.open();
    }
    
    /**
     * Select attachment in Media Library
     */
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
    
    /**
     * Get attachment ID by URL using AJAX
     */
    async getAttachmentIdByUrl(url) {
        try {
            const form = document.querySelector('.section-form');
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
    
    /**
     * Handle file selection for image fields (fallback)
     */
    handleFileSelection(fileInput) {
        const file = fileInput.files[0];
        const inputTarget = fileInput.dataset.inputTarget;
        const fileNameInput = document.querySelector(`input[name="${inputTarget}"].file-name`);
        
        if (file && fileNameInput) {
            fileNameInput.value = file.name;
        }
    }
    
    /**
     * Handle form submission
     */
    handleFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Disable submit button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = '저장 중...';
        
        // Collect form data
        const formData = this.collectFormData(form);
        
        // Send AJAX request
        this.sendAjaxRequest(formData)
            .then(response => {
                this.showSuccessMessage('저장되었습니다!');
                // Reload page after successful save
                setTimeout(() => {
                    window.location.reload();
                }, 200);
            })
            .catch(error => {
                this.showErrorMessage(error.message || '저장에 실패했습니다.');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }
    
    /**
     * Collect form data
     */
    collectFormData(form) {
        const formData = new FormData();
        
        // Get section_page from URL
        const urlParams = new URLSearchParams(window.location.search);
        
        // Add hidden fields
        formData.append('action', 'update_membership');
        formData.append('nonce', form.querySelector('input[name="nonce"]').value);
        
        // Collect all form fields
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            if (field.name && !field.name.includes('nonce') && !field.name.includes('action')) {
                if (field.type === 'checkbox') {
                    if (field.checked) {
                        formData.append(field.name, field.value);
                    }
                } else if (field.type === 'radio') {
                    if (field.checked) {
                        console.log('Debug - Radio field:', field.name, '=', field.value, 'checked:', field.checked);
                        formData.append(field.name, field.value);
                    } else {
                        console.log('Debug - Radio field:', field.name, '=', field.value, 'checked:', field.checked);
                    }
                } else if (field.type === 'file') {
                    // Handle file input
                    if (field.files.length > 0) {
                        formData.append(field.name, field.dataset.mediaUrl || field.files[0]);
                    }
                } else if (field.classList.contains('file-name')) {
                    // Skip file name display input
                    return;
                } else {
                    formData.append(field.name, field.value);
                }
            }
        });
        
        return formData;
    }
    
    /**
     * Send AJAX request
     */
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
            throw new Error('Network error');
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.data.message || 'Unknown error');
        }
        
        return result.data;
    }
    
    /**
     * Show success message
     */
    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }
    
    /**
     * Show error message
     */
    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }
    
    /**
     * Show message
     */
    showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.section-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `section-message alert alert-${type === 'success' ? 'success' : 'danger'} mt-3`;
        messageDiv.textContent = message;
        
        // Try to find the form to insert message
        const form = document.querySelector('.section-form') || document.querySelector('.card');
        
        if (form && form.parentNode) {
            form.parentNode.insertBefore(messageDiv, form.nextSibling);
        } else {
            // Fallback: insert at the top of the page
            document.body.insertBefore(messageDiv, document.body.firstChild);
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    /**
     * Load edit form via AJAX
     */
    async loadEditForm(button) {
        const card = button.closest('.card');
        
        // Show loading
        button.textContent = '로딩 중...';
        button.disabled = true;
        
        try {
            // Get nonce from the card (view mode)
            const nonceInput = card.querySelector('input[name="nonce"]');
            const nonce = nonceInput ? nonceInput.value : '';
            const idInput = card.querySelector('input[name="id"]');
            const id = idInput ? idInput.value : '';
            
            
            // Load edit form via AJAX
            const response = await fetch(define.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'get_membership_management_form',
                    id: id,
                    nonce: nonce
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Replace entire card with new form
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = result.data.html;
                const newForm = tempDiv.firstElementChild;
                
                // Replace the card with the new form
                card.parentNode.replaceChild(newForm, card);
                
                // No need to bind events - delegation handles it automatically
                
            } else {
                throw new Error(result.data.message || 'Failed to load edit form');
            }
            
        } catch (error) {
            console.error('Error loading edit form:', error);
            button.textContent = '수정';
            button.disabled = false;
            
            // Show error message in card
            this.showErrorMessageInCard(card, '편집 폼을 불러오는데 실패했습니다: ' + error.message);
        }
    }
    
    /**
     * Show error message in specific card
     */
    showErrorMessageInCard(card, message) {
        // Remove existing messages in this card
        const existingMessages = card.querySelectorAll('.section-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = 'section-message alert alert-danger mt-3';
        messageDiv.textContent = message;
        
        // Insert after card content
        const cardBody = card.querySelector('.card-body');
        if (cardBody) {
            cardBody.appendChild(messageDiv);
        } else {
            card.appendChild(messageDiv);
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    /**
     * Handle membership benefits form submission
     */
    handleBenefitsSubmit(button) {
        const form = button.closest('form');
        const originalText = button.textContent;
        
        // Disable button and show loading
        button.disabled = true;
        button.textContent = '저장 중...';
        
        // Collect form data
        const formData = this.collectBenefitsData(form);
        
        // Send AJAX request
        this.sendBenefitsAjaxRequest(formData)
            .then(response => {
                this.showSuccessMessage('혜택 정보가 저장되었습니다!');
                // Reload page after successful save
                setTimeout(() => {
                    window.location.reload();
                }, 200);
            })
            .catch(error => {
                this.showErrorMessage(error.message || '저장에 실패했습니다.');
                button.textContent = originalText;
                button.disabled = false;
            });
    }
    
    /**
     * Collect benefits form data
     */
    collectBenefitsData(form) {
        const formData = new FormData();
        
        // Add hidden fields
        formData.append('action', 'update_membership_benefits');
        formData.append('nonce', form.querySelector('input[name="nonce"]')?.value || '');
        formData.append('membership_id', form.querySelector('input[name="membership_id"]')?.value || '');
        
        // Collect voucher selections for each category
        const categories = ['travel_care', 'lifestyle', 'special_benefit', 'welcome_gift'];
        
        categories.forEach(category => {
            const voucherCheckboxes = form.querySelectorAll(`input[name="${category}_vouchers[]"]:checked`);
            
            // Collect selected vouchers for this category
            const selectedVouchers = [];
            voucherCheckboxes.forEach(checkbox => {
                const voucherId = checkbox.value;
                const category = checkbox.getAttribute('data-category');
                
                // Find the corresponding summary checkbox for this specific voucher in this category
                const summaryCheckbox = form.querySelector(`input[name="summary_benefits[]"][value="${voucherId}"][data-category="${category}"]`);
                const isSummary = summaryCheckbox ? summaryCheckbox.checked : false;
                
                // Debug: Log the values
                console.log(`Voucher ${voucherId} in ${category}:`, {
                    summaryCheckbox: summaryCheckbox,
                    checked: summaryCheckbox ? summaryCheckbox.checked : 'not found',
                    isSummary: isSummary
                });
                
                // Only add if this voucher is not already added for this category
                const existingVoucher = selectedVouchers.find(v => v.id === voucherId && v.category === category);
                if (!existingVoucher) {
                    selectedVouchers.push({
                        id: voucherId,
                        category: category,
                        is_summary: isSummary
                    });
                }
            });
            
            // Add voucher data to form
            if (selectedVouchers.length > 0) {
                formData.append(`${category}_vouchers`, JSON.stringify(selectedVouchers));
            }
            
            // Collect usage guide
            const usageGuide = form.querySelector(`textarea[name="${category}_usage_guide"]`);
            if (usageGuide && usageGuide.value.trim()) {
                formData.append(`${category}_usage_guide`, usageGuide.value.trim());
            }
        });
        
        return formData;
    }
    
    /**
     * Send benefits AJAX request
     */
    async sendBenefitsAjaxRequest(formData) {
        const response = await fetch(define.ajax_url, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'Accept-Charset': 'utf-8'
            }
        });
        
        if (!response.ok) {
            throw new Error('Network error');
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.data.message || 'Unknown error');
        }
        
        return result.data;
    }
    
    // Note: Category switching and checkbox handling methods removed
    // These are handled by view.php JavaScript
}

// Initialize when DOM is ready (only once)
if (!window.membershipManagementHandler) {
    document.addEventListener('DOMContentLoaded', () => {
        window.membershipManagementHandler = new MembershipManagementHandler();
    });
}

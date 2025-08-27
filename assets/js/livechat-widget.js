/**
 * LiveChat Widget with jQuery
 */
class LiveChatWidget {
    constructor() {
        this.$ = jQuery;
        this.config = window.liveChatConfig;
        this.sessionId = null;
        this.currentStage = 'intro';
        this.selectedMainCategory = null;
        this.selectedSubCategory = null;
        this.eventSource = null;
        this.lastMessageId = 0;
        this.$chatBox = this.$('.chat-box');
        this.sessionRestored = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupErrorHandling();
    }
    
    /**
     * Try to restore session from sessionStorage and database
     */
    async restoreSession() {
        const sessionId = sessionStorage.getItem('livechat_session_id');
        
        if (sessionId) {
            try {
                const response = await this.makeAjaxRequest('livechat_get_session', {
                    session_id: sessionId
                });
                
                if (response.success) {
                    const session = response.data.session;
                    const messages = response.data.messages;
                    
                    // Restore session data
                    this.sessionId = session.session_id;
                    this.selectedMainCategory = session.category_main;
                    this.selectedSubCategory = session.category_sub;
                    this.currentStage = this.mapStageFromDB(session.chat_stage);
                    
                    // Get last message ID
                    if (messages.length > 0) {
                        this.lastMessageId = Math.max(...messages.map(m => m.id));
                    }
                    
                    // Restore UI and messages
                    this.restoreUIState(response.data.subcategories || []);
                    this.restoreMessages(messages);
                    
                    return;
                }
            } catch (e) {
                console.error('Failed to restore session:', e);
                this.clearSession();
            }
        }
    }
    
    /**
     * Map database chat_stage to frontend currentStage
     */
    mapStageFromDB(dbStage) {
        switch (dbStage) {
            case 'category_main': return 'main_category';
            case 'category_sub': return 'sub_category';
            case 'chat_active': return 'chat_active';
            default: return 'intro';
        }
    }
    
    /**
     * Restore UI state based on current stage
     */
    restoreUIState(subcategories = []) {
        // Hide intro first
        this.$chatBox.find('.intro-box').addClass('d-none');
        this.$chatBox.find('.main-chat-box').removeClass('d-none');
        
        switch (this.currentStage) {
            case 'main_category':
                // Show main category selection interface
                this.$chatBox.find('#main-category-selection').removeClass('d-none');
                this.$chatBox.find('#sub-category-selection').addClass('d-none');
                this.$chatBox.find('#category-breadcrumb').addClass('d-none');
                this.updateCurrentTime();
                break;
                
            case 'sub_category':
                // Show subcategory selection interface with main category in breadcrumb
                this.$chatBox.find('#main-category-selection').addClass('d-none');
                this.$chatBox.find('#sub-category-selection').removeClass('d-none');
                
                const $breadcrumb = this.$chatBox.find('#category-breadcrumb').removeClass('d-none');
                $breadcrumb.find('.main-category').text(this.selectedMainCategory);
                $breadcrumb.find('.arrow-icon, .sub-category').addClass('d-none');
                
                // Populate subcategories if available
                if (subcategories.length > 0) {
                    const $subList = this.$chatBox.find('#sub-category-list').empty();
                    subcategories.forEach(subCategory => {
                        this.$('<button>', {
                            class: 'btn category-btn',
                            type: 'button',
                            text: subCategory,
                            click: () => this.selectSubCategory(subCategory)
                        }).appendTo($subList);
                    });
                }
                break;
                
            case 'chat_active':
                // Show chat interface
                this.$chatBox.find('#main-category-selection, #sub-category-selection').addClass('d-none');
                
                const $chatBreadcrumb = this.$chatBox.find('#category-breadcrumb').removeClass('d-none');
                $chatBreadcrumb.find('.main-category').text(this.selectedMainCategory);
                
                if (this.selectedSubCategory) {
                    // Show full breadcrumb with sub category
                    $chatBreadcrumb.find('.arrow-icon, .sub-category').removeClass('d-none');
                    $chatBreadcrumb.find('.sub-category').text(this.selectedSubCategory);
                } else {
                    // Hide sub category part
                    $chatBreadcrumb.find('.arrow-icon, .sub-category').addClass('d-none');
                }

                break;
                
            default:
                // Show intro stage
                this.$chatBox.find('.intro-box').removeClass('d-none');
                this.$chatBox.find('.main-chat-box').addClass('d-none');
                break;
        }
    }
    
    
    /**
     * Restore chat messages from database
     */
    restoreMessages(messages) {
        if (messages && messages.length > 0) {
            // Clear any existing messages
            this.$chatBox.find('.body .message-item').remove();
            
            // Display each message
            messages.forEach(message => {
                this.displayMessage(message);
            });
        }
    }
    
    /**
     * Save session_id to sessionStorage
     */
    saveSession() {
        if (this.sessionId) {
            sessionStorage.setItem('livechat_session_id', this.sessionId);
        } else {
            console.warn('Cannot save session: no sessionId');
        }
    }
    
    /**
     * Clear session state
     */
    clearSession() {
        sessionStorage.removeItem('livechat_session_id');
        this.sessionId = null;
        this.selectedMainCategory = null;
        this.selectedSubCategory = null;
        this.currentStage = 'intro';
        this.lastMessageId = 0;
    }
    
    bindEvents() {
        // Chat box open button - trigger restore or show interface
        this.$('.open-chat-btn').on('click', () => this.onChatBoxOpen());
        
        // Start chat button (문의하기)
        this.$chatBox.on('click', '.intro-content button', () => this.startChat());
        
        // Back button in main chat
        this.$chatBox.on('click', '.main-chat-box .header button', () => this.goBack());
        
        // Main category buttons
        this.$chatBox.on('click', '#main-category-selection .category-btn', (e) => {
            const category = this.$(e.target).data('category');
            this.selectMainCategory(category);
        });
        
        // Message form
        this.$chatBox.on('click', '.footer button[type="submit"]', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        this.$chatBox.on('keypress', '.footer input[type="text"]', (e) => {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                this.sendMessage();
            }
        });
    }
    
    setupErrorHandling() {
        this.$(window).on('error', (e) => console.error('LiveChat Error:', e));
    }
    
    /**
     * Handle chat box open - restore session and start SSE if needed
     */
    async onChatBoxOpen() {        
        // Only restore once per page load
        if (!this.sessionRestored) {
            await this.restoreSession();
            this.sessionRestored = true;
        }
        
        // If we're in chat_active stage after restore, start SSE
        if (this.currentStage === 'chat_active' && this.sessionId && !this.eventSource) {
            this.delayedStartSSE();
        }
    }
    
    /**
     * Stage 1: Start chat session
     */
    async startChat() {
        try {
            this.showLoading();
            
            const response = await this.makeAjaxRequest('livechat_start', {});
            
            if (response.success) {
                this.sessionId = response.data.session_id;
                this.showMainCategorySelection();
                this.saveSession(); // Save after starting session
                this.hideLoading();
            } else {
                throw new Error(response.data.message || 'Failed to start chat');
            }
        } catch (error) {
            this.showError('채팅을 시작할 수 없습니다. 다시 시도해주세요.');
            console.error('Start chat error:', error);
        }
    }
    
    /**
     * Stage 2: Select main category
     */
    async selectMainCategory(category) {
        try {
            this.showLoading();
            
            const response = await this.makeAjaxRequest('livechat_subcategories', {
                session_id: this.sessionId,
                main_category: category
            });
            
            if (response.success) {
                this.selectedMainCategory = category;
                this.saveSession(); // Save after selecting main category
                const subcategories = response.data.subcategories;
                
                // Nếu không có subcategories, bắt đầu chat luôn
                if (!subcategories || subcategories.length === 0) {
                    await this.beginChatWithoutSubcategory();
                } else {
                    this.showSubCategorySelection(subcategories);
                }
                this.hideLoading();
            } else {
                throw new Error(response.data.message || 'Failed to get subcategories');
            }
        } catch (error) {
            this.showError('카테고리를 불러올 수 없습니다. 다시 시도해주세요.');
            console.error('Select main category error:', error);
        }
    }
    
    /**
     * Begin chat without subcategory (khi main category không có sub)
     */
    async beginChatWithoutSubcategory() {
        try {
            const customerName = this.config.currentUser.name || '고객';
            const customerEmail = this.config.currentUser.email || '';
            
            const response = await this.makeAjaxRequest('livechat_begin', {
                session_id: this.sessionId,
                sub_category: '', // Không có sub category
                customer_name: customerName,
                customer_email: customerEmail
            });
            
            if (response.success) {
                this.selectedSubCategory = null; // Không có sub category
                this.saveSession(); // Save after beginning chat without sub
                this.showChatInterfaceWithoutSub();
                this.delayedStartSSE();
            } else {
                throw new Error(response.data.message || 'Failed to begin chat');
            }
        } catch (error) {
            this.showError('채팅을 시작할 수 없습니다. 다시 시도해주세요.');
            console.error('Begin chat without subcategory error:', error);
        }
    }
    
    /**
     * Stage 3: Select sub category and begin chat
     */
    async selectSubCategory(subCategory) {
        try {
            this.showLoading();
            
            const customerName = this.config.currentUser.name || '고객';
            const customerEmail = this.config.currentUser.email || '';
            
            const response = await this.makeAjaxRequest('livechat_begin', {
                session_id: this.sessionId,
                sub_category: subCategory,
                customer_name: customerName,
                customer_email: customerEmail
            });
            
            if (response.success) {
                this.selectedSubCategory = subCategory;
                this.saveSession(); // Save after selecting sub category
                this.showChatInterface();
                this.delayedStartSSE();
                this.hideLoading();
            } else {
                throw new Error(response.data.message || 'Failed to begin chat');
            }
        } catch (error) {
            this.showError('채팅을 시작할 수 없습니다. 다시 시도해주세요.');
            console.error('Select sub category error:', error);
        }
    }
    
    /**
     * Stage 4: Send message
     */
    async sendMessage() {
        const $messageInput = this.$chatBox.find('.footer input[type="text"]');
        const message = $messageInput.val().trim();
        
        if (!message || !this.sessionId) return;
        
        try {
            // Clear input and display message immediately
            $messageInput.val('');
            this.displayMessage({
                sender_type: 'customer',
                sender_name: this.config.currentUser.name || '나',
                message: message,
                created_at: new Date().toISOString()
            });
            
            const response = await this.makeAjaxRequest('livechat_send', {
                session_id: this.sessionId,
                message: message,
                sender_name: this.config.currentUser.name || '고객'
            });
            
            if (!response.success) {
                throw new Error(response.data.message || 'Failed to send message');
            } else {
                this.saveSession(); // Save after sending message
            }
        } catch (error) {
            this.showError('메시지를 보낼 수 없습니다. 다시 시도해주세요.');
            console.error('Send message error:', error);
        }
    }
    
    /**
     * Delay SSE connection until page is fully loaded
     */
    delayedStartSSE() {
        // Wait for page to be fully loaded
        if (document.readyState === 'complete') {
            // Page already loaded, start immediately
            setTimeout(() => this.startSSEConnection(), 100);
        } else {
            // Wait for load event
            window.addEventListener('load', () => {
                setTimeout(() => this.startSSEConnection(), 200);
            }, { once: true });
        }
    }
    
    /**
     * Start SSE connection for real-time messages
     */
    startSSEConnection() {
        // Always close existing connection first
        this.destroy();        
        const url = `${this.config.homeUrl}/chat/stream/?session_id=${this.sessionId}&last_message_id=${this.lastMessageId}`;
        this.eventSource = new EventSource(url);
        
        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                
                if (data.type === 'heartbeat') {
                    return; // Ignore heartbeat messages
                }
                
                if (data.type === 'closing') {
                    if (data.reason === 'timeout') {
                        this.destroy();
                        // Reconnect after 1 second
                        setTimeout(() => {
                            if (this.currentStage === 'chat_active') {
                                this.delayedStartSSE();
                            }
                        }, 1000);
                    } else {
                        // Other reasons - don't reconnect
                        this.destroy();
                    }
                    return;
                }
                
                if (data.error) {
                    console.error('SSE Error:', data.error);
                    return;
                }
                
                // Only process actual messages (must have message content)
                if (!data.message) {
                    return;
                }
                
                // Display new message if it's not from current user
                if (data.sender_type !== 'customer') {
                    this.displayMessage(data);
                }
                
                this.lastMessageId = Math.max(this.lastMessageId, data.id || 0);
                this.saveSession(); // Save after receiving new message

            } catch (error) {
                console.error('SSE Message Parse Error:', error);
            }
        };
        
        this.eventSource.onerror = (error) => {
            console.error('SSE Connection Error:', error);
            
            // Check if connection still exists before attempting reconnect
            if (this.eventSource && this.eventSource.readyState === EventSource.CLOSED) {
                this.destroy();
                return;
            }
            
            // Only reconnect if we're still in chat and connection exists
            if (this.eventSource && this.currentStage === 'chat_active') {
                console.log('Attempting to reconnect in 5 seconds...');
                setTimeout(() => {
                    if (this.currentStage === 'chat_active' && !this.eventSource) {
                        this.delayedStartSSE();
                    }
                }, 5000);
            }
        };
    }
    
    /**
     * UI Management Methods
     */
    showMainCategorySelection() {
        this.currentStage = 'main_category';
        
        // Hide intro, show main chat
        this.$chatBox.find('.intro-box').addClass('d-none');
        this.$chatBox.find('.main-chat-box').removeClass('d-none');
        
        // Show main category selection
        this.$chatBox.find('#main-category-selection').removeClass('d-none');
        this.$chatBox.find('#sub-category-selection').addClass('d-none');
        
        // Update time
        this.updateCurrentTime();
    }
    
    showSubCategorySelection(subcategories) {
        this.currentStage = 'sub_category';
        
        // Show only main category name (no icon, no sub category yet)
        const $breadcrumb = this.$chatBox.find('#category-breadcrumb');
        $breadcrumb.removeClass('d-none')
            .find('.main-category').text(this.selectedMainCategory);
        
        // Hide icon and sub category (chưa chọn sub)
        $breadcrumb.find('.arrow-icon, .sub-category').addClass('d-none');
        $breadcrumb.find('.sub-category').text('');
        
        // Hide main category selection, show sub category selection
        this.$chatBox.find('#main-category-selection').addClass('d-none');
        this.$chatBox.find('#sub-category-selection').removeClass('d-none');
        
        // Dynamically populate subcategories
        const $subList = this.$chatBox.find('#sub-category-list').empty();
        subcategories.forEach(subCategory => {
            this.$('<button>', {
                class: 'btn category-btn',
                type: 'button',
                text: subCategory,
                click: () => this.selectSubCategory(subCategory)
            }).appendTo($subList);
        });
    }
    
    showChatInterface() {
        this.currentStage = 'chat_active';
        
        // Hide category selections, show chat interface
        this.$chatBox.find('#main-category-selection, #sub-category-selection').addClass('d-none');
        
        // Show full breadcrumb (main -> sub) với icon
        const $breadcrumb = this.$chatBox.find('#category-breadcrumb').removeClass('d-none');
        $breadcrumb.find('.main-category').text(this.selectedMainCategory);
        $breadcrumb.find('.arrow-icon, .sub-category').removeClass('d-none');
        $breadcrumb.find('.sub-category').text(this.selectedSubCategory);
        
        // Clear any existing messages in body
        this.$chatBox.find('.body .item').remove();
    }
    
    showChatInterfaceWithoutSub() {
        this.currentStage = 'chat_active';
        
        // Hide category selections, show chat interface
        this.$chatBox.find('#main-category-selection, #sub-category-selection').addClass('d-none');
        
        // Show breadcrumb chỉ có main category (không có icon và sub)
        const $breadcrumb = this.$chatBox.find('#category-breadcrumb').removeClass('d-none');
        $breadcrumb.find('.main-category').text(this.selectedMainCategory);
        $breadcrumb.find('.arrow-icon, .sub-category').addClass('d-none');
        $breadcrumb.find('.sub-category').text('');
        
        // Clear any existing messages in body
        this.$chatBox.find('.body .item').remove();
    }
    
    displayMessage(messageData) {
        const $body = this.$chatBox.find('.body');
        
        // Determine CSS class based on sender type
        let itemClass = 'item';
        if (messageData.sender_type === 'customer') {
            itemClass += ' right'; // Customer messages have 'right' class
        }
        // Admin/system messages just have 'item' class
        
        const $messageDiv = this.$('<div>', {
            class: itemClass,
            html: `<p>${this.escapeHtml(messageData.message)}</p>`
        });
        
        $body.append($messageDiv);
        
        // Scroll to bottom
        $body.scrollTop($body[0].scrollHeight);
    }
    
    goBack() {
        if (this.currentStage === 'sub_category') {
            this.showMainCategorySelection();
        } else if (this.currentStage === 'main_category') {
            this.showIntro();
        }
    }
    
    showIntro() {
        this.currentStage = 'intro';
        
        this.$chatBox.find('.intro-box').removeClass('d-none');
        this.$chatBox.find('.main-chat-box').addClass('d-none');
        
        // Close SSE connection
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
    }
    
    showLoading() {
        // You can implement a loading spinner here
        console.log('Loading...');
    }
    
    hideLoading() {
        console.log('Loading complete');
    }
    
    showError(message) {
        alert(message); // Replace with better error display
    }
    
    updateCurrentTime() {
        const $timeElement = this.$chatBox.find('.main-chat-box .time');
        if ($timeElement.length) {
            const timeString = new Date().toLocaleTimeString('ko-KR', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            $timeElement.text(timeString);
        }
    }
    
    /**
     * Utility Methods
     */
    async makeAjaxRequest(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', this.config.nonce);
        
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });
        
        const response = await fetch(this.config.ajaxUrl, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Cleanup method - fast cleanup for page unload
     */
    destroy() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
    }
    
    /**
     * Resume SSE connection (when tab becomes visible)
     */
    resumeSSE() {
        if (this.currentStage === 'chat_active' && this.sessionId && !this.eventSource) {
            this.delayedStartSSE();
        }
    }
}

// Initialize widget when DOM is loaded
jQuery(document).ready(function($) {
    if (window.liveChatConfig) {
        window.liveChatWidget = new LiveChatWidget();
    }
    
    // Fast cleanup on page unload - don't block the unload
    $(window).on('beforeunload', () => {
        if (window.liveChatWidget) {
            window.liveChatWidget.destroy();
        }
    });
    
    // Also handle visibility change (tab switch, minimize)
    $(document).on('visibilitychange', () => {
        if (window.liveChatWidget) {
            if (document.hidden) {
                window.liveChatWidget.destroy();
            } else {
                window.liveChatWidget.resumeSSE();
            }
        }
    });
});
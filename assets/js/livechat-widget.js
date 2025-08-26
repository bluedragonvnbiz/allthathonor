/**
 * LiveChat Widget JavaScript
 * Handles all frontend chat functionality
 */

class LiveChatWidget {
    constructor() {
        this.config = window.liveChatConfig || {};
        this.sessionId = null;
        this.currentStage = 'welcome';
        this.eventSource = null;
        this.lastMessageId = 0;
        this.sessionData = {};
        this.sendingMessage = false;
        this.sseRetryCount = 0;
        this.maxSseRetries = 3;
        this.isCleaningUp = false;
        
        this.elements = {};
        this.messageContainer = null;
        
        this.init();
    }
    
    init() {
        console.time('LiveChat Init');
        console.log('LiveChat: Starting initialization');
        
        console.time('Cache Elements');
        this.cacheElements();
        console.timeEnd('Cache Elements');
        
        console.time('Bind Events');
        this.bindEvents();
        console.timeEnd('Bind Events');
        
        console.time('Setup Cleanup');
        this.setupPageUnloadCleanup();
        console.timeEnd('Setup Cleanup');
        
        console.time('Restore Session');
        this.restoreSession();
        console.timeEnd('Restore Session');
        
        this.showWidget();
        console.timeEnd('LiveChat Init');
    }
    
    cacheElements() {
        this.elements = {
            widget: document.getElementById('livechat-widget'),
            trigger: document.getElementById('chat-trigger'),
            window: document.getElementById('chat-window'),
            
            // Stages
            welcomeStage: document.getElementById('stage-welcome'),
            mainCategoryStage: document.getElementById('stage-main-category'),
            subCategoryStage: document.getElementById('stage-sub-category'),
            chatStage: document.getElementById('stage-chat'),
            loadingStage: document.getElementById('stage-loading'),
            
            // Buttons
            startChat: document.getElementById('start-chat'),
            closeButtons: document.querySelectorAll('.chat-close'),
            backToWelcome: document.getElementById('back-to-welcome'),
            backToMainCategory: document.getElementById('back-to-main-category'),
            backToCategories: document.getElementById('back-to-categories'),
            
            // Category containers
            mainCategories: document.getElementById('main-categories'),
            subCategories: document.getElementById('sub-categories'),
            selectedMainCategory: document.getElementById('selected-main-category'),
            chatMainCategory: document.getElementById('chat-main-category'),
            chatSubCategory: document.getElementById('chat-sub-category'),
            
            // Chat elements
            chatMessages: document.getElementById('chat-messages'),
            chatInput: document.getElementById('chat-input'),
            sendButton: document.getElementById('send-message'),
            typingIndicator: document.getElementById('typing-indicator')
        };
        
        this.messageContainer = this.elements.chatMessages;
    }
    
    bindEvents() {
        // Trigger button
        this.elements.trigger.addEventListener('click', () => this.openChat());
        
        // Close buttons
        // this.elements.closeButtons.forEach(btn => {
        //     btn.addEventListener('click', () => this.closeChat());
        // });
        
        // Stage navigation
        this.elements.startChat.addEventListener('click', () => this.startChatSession());
        this.elements.backToWelcome.addEventListener('click', () => this.goToStage('welcome'));
        this.elements.backToMainCategory.addEventListener('click', () => this.goToStage('main-category'));
        this.elements.backToCategories.addEventListener('click', () => this.goToStage('sub-category'));
        
        // Chat input
        this.elements.chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        this.elements.chatInput.addEventListener('input', () => this.autoResize());
        this.elements.sendButton.addEventListener('click', () => this.sendMessage());
        
        // Click outside to close
        // document.addEventListener('click', (e) => {
        //     if (!this.elements.widget.contains(e.target)) {
        //         this.closeChat();
        //     }
        // });
    }
    
    setupPageUnloadCleanup() {
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
        
        window.addEventListener('pagehide', () => {
            this.cleanup();
        });
        
        // Also cleanup on page visibility change (mobile browsers)
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.cleanup();
            }
        });
    }
    
    cleanup() {
        if (this.isCleaningUp) {
            console.log('Cleanup already in progress, skipping');
            return;
        }
        
        if (this.eventSource) {
            this.isCleaningUp = true;
            console.time('SSE Cleanup');
            console.log('Cleaning up SSE connection, readyState:', this.eventSource.readyState);
            
            try {
                // Force immediate close
                this.eventSource.close();
                console.log('SSE connection closed');
            } catch (error) {
                console.error('Error closing SSE:', error);
            }
            
            this.eventSource = null;
            console.timeEnd('SSE Cleanup');
            
            // Reset flag after a brief delay
            setTimeout(() => {
                this.isCleaningUp = false;
            }, 100);
        }
    }
    
    showWidget() {
        this.elements.widget.style.display = 'block';
    }
    
    openChat() {
        this.elements.window.style.display = 'block';
        this.elements.trigger.style.display = 'none';
        
        // Check if we have active session, if not go to welcome
        if (!this.sessionId || this.currentStage === 'welcome') {
            this.goToStage('welcome');
        } else {
            // Stay on current stage (session was already restored in init)
            this.goToStage(this.currentStage);
        }
    }
    
    closeChat() {
        this.elements.window.style.display = 'none';
        this.elements.trigger.style.display = 'flex';
        
        // Close EventSource if open
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        
        // Clear session storage when explicitly closing chat
        this.clearSession();
    }
    
    goToStage(stage) {
        // Hide all stages
        document.querySelectorAll('.chat-stage').forEach(el => {
            el.classList.remove('active');
        });
        
        // Show target stage
        const targetStage = document.getElementById(`stage-${stage}`);
        if (targetStage) {
            targetStage.classList.add('active');
            this.currentStage = stage;
        }
    }
    
    showLoading() {
        this.goToStage('loading');
    }
    
    async startChatSession() {
        try {
            this.showLoading();
            
            const response = await this.ajaxRequest('livechat_start_session', {
                customer_name: this.config.currentUser.name,
                customer_email: this.config.currentUser.email
            });
            
            if (response.success) {
                this.sessionId = response.data.session_id;
                this.loadMainCategories(response.data.main_categories);
                this.goToStage('main-category');
                this.saveSession();
            } else {
                this.showError('채팅 세션을 시작할 수 없습니다.');
                this.goToStage('welcome');
            }
            
        } catch (error) {
            console.error('Start chat error:', error);
            this.showError('연결 오류가 발생했습니다.');
            this.goToStage('welcome');
        }
    }
    
    loadMainCategories(categories) {
        this.elements.mainCategories.innerHTML = '';
        
        categories.forEach((category, index) => {
            const option = document.createElement('div');
            option.className = 'category-option';
            option.textContent = category;
            
            // Add emoji for first option
            if (index === 0) {
                option.innerHTML = `<span class="emoji">👉</span>${category}`;
            }
            
            option.addEventListener('click', () => this.selectMainCategory(category));
            this.elements.mainCategories.appendChild(option);
        });
    }
    
    async selectMainCategory(category) {
        try {
            this.showLoading();
            
            const response = await this.ajaxRequest('livechat_get_subcategories', {
                session_id: this.sessionId,
                main_category: category
            });
            
            if (response.success) {
                this.elements.selectedMainCategory.textContent = category;
                this.loadSubCategories(response.data.sub_categories);
                this.goToStage('sub-category');
                this.saveSession();
            } else {
                this.showError('카테고리를 불러올 수 없습니다.');
            }
            
        } catch (error) {
            console.error('Select main category error:', error);
            this.showError('오류가 발생했습니다.');
        }
    }
    
    loadSubCategories(categories) {
        this.elements.subCategories.innerHTML = '';
        
        categories.forEach((category, index) => {
            const option = document.createElement('div');
            option.className = 'category-option';
            option.textContent = category;
            
            // Add emoji for first option
            if (index === 0) {
                option.innerHTML = `<span class="emoji">👉</span>${category}`;
            }
            
            option.addEventListener('click', () => this.selectSubCategory(category));
            this.elements.subCategories.appendChild(option);
        });
    }
    
    async selectSubCategory(category) {
        try {
            this.showLoading();
            
            const response = await this.ajaxRequest('livechat_select_category', {
                session_id: this.sessionId,
                main_category: this.elements.selectedMainCategory.textContent,
                sub_category: category
            });
            
            if (response.success) {
                this.elements.chatMainCategory.textContent = response.data.main_category;
                this.elements.chatSubCategory.textContent = response.data.sub_category;
                
                // Load initial messages and start SSE after a delay
                await this.loadInitialMessages();
                setTimeout(() => this.startSSE(), 100);
                
                this.goToStage('chat_active');
                this.saveSession();
            } else {
                this.showError('채팅을 시작할 수 없습니다.');
            }
            
        } catch (error) {
            console.error('Select sub category error:', error);
            this.showError('오류가 발생했습니다.');
        }
    }
    
    async loadInitialMessages() {
        // Clear existing messages
        this.messageContainer.innerHTML = '';
        this.lastMessageId = 0;
        
        // Show typing indicator briefly to simulate loading
        this.showTypingIndicator();
        
        setTimeout(() => {
            this.hideTypingIndicator();
        }, 1000);
    }
    
    startSSE() {
        // Close existing connection
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        
        // Don't start SSE if not in chat stage
        if (this.currentStage !== 'chat_active' || !this.sessionId) {
            return;
        }
        
        const sseUrl = `${this.config.homeUrl}/chat/stream?session_id=${this.sessionId}&since_id=${this.lastMessageId}`;
        console.log('Starting SSE connection:', sseUrl);
        
        this.eventSource = new EventSource(sseUrl);
        
        this.eventSource.onopen = () => {
            console.log('SSE connection opened');
            this.sseRetryCount = 0; // Reset retry count on successful connection
        };
        
        this.eventSource.addEventListener('message', (event) => {
            try {
                const message = JSON.parse(event.data);
                this.addMessage(message);
                this.lastMessageId = Math.max(this.lastMessageId, message.id);
            } catch (error) {
                console.error('SSE message error:', error);
            }
        });
        
        // Handle connection events
        this.eventSource.addEventListener('connected', () => {
            console.log('SSE: Connection established');
        });
        
        // Handle heartbeat events (reduced frequency)
        this.eventSource.addEventListener('heartbeat', () => {
            console.log('SSE: Heartbeat received');
        });
        
        // Handle close events
        this.eventSource.addEventListener('close', () => {
            console.log('SSE: Server closed connection');
            this.eventSource.close();
            this.eventSource = null;
        });
        
        this.eventSource.onerror = (error) => {
            console.error('SSE connection error:', error);
            
            // Force immediate cleanup on error
            if (this.eventSource) {
                this.eventSource.close();
                this.eventSource = null;
            }
            
            // Only retry if we haven't exceeded max retries and still in chat
            if (this.sseRetryCount < this.maxSseRetries && this.currentStage === 'chat_active') {
                this.sseRetryCount++;
                console.log(`SSE retry attempt ${this.sseRetryCount}/${this.maxSseRetries}`);
                setTimeout(() => {
                    this.startSSE();
                }, 3000 * this.sseRetryCount); // Exponential backoff
            } else {
                console.log('SSE max retries reached or chat closed');
            }
        };
    }
    
    addMessage(message) {
        const messageEl = document.createElement('div');
        messageEl.className = `message ${message.sender_type}`;
        
        if (message.sender_type === 'system' || message.sender_type === 'admin') {
            messageEl.innerHTML = `
                <div class="avatar-small">
                    <img src="${this.config.themeUrl}/assets/images/icons/icon-admin-menu-live-chat.svg" alt="상담원">
                </div>
                <div class="message-content">
                    ${this.escapeHtml(message.message)}
                    <div class="message-time">${message.formatted_time || this.formatTime(message.created_at)}</div>
                </div>
            `;
        } else {
            messageEl.innerHTML = `
                <div class="message-content">
                    ${this.escapeHtml(message.message)}
                    <div class="message-time">${message.formatted_time || this.formatTime(message.created_at)}</div>
                </div>
            `;
        }
        
        this.messageContainer.appendChild(messageEl);
        this.scrollToBottom();
        
        // Update session timestamp when receiving messages
        this.updateSessionTimestamp();
    }
    
    async sendMessage() {
        // Prevent double sending
        if (this.sendingMessage) return;
        
        const message = this.elements.chatInput.value.trim();
        if (!message) return;
        
        // Set sending flag
        this.sendingMessage = true;
        
        // Disable send button
        this.elements.sendButton.disabled = true;
        
        // Add user message immediately
        this.addMessage({
            sender_type: 'user',
            message: message,
            created_at: new Date().toISOString()
        });
        
        // Clear input
        this.elements.chatInput.value = '';
        this.autoResize();
        
        try {
            const response = await this.ajaxRequest('livechat_send_message', {
                session_id: this.sessionId,
                message: message,
                sender_name: this.config.currentUser.name || '고객'
            });
            
            if (!response.success) {
                this.showError('메시지를 전송할 수 없습니다.');
            }
            
        } catch (error) {
            console.error('Send message error:', error);
            this.showError('메시지 전송 중 오류가 발생했습니다.');
        } finally {
            this.elements.sendButton.disabled = false;
            this.sendingMessage = false;
        }
    }
    
    showTypingIndicator() {
        this.elements.typingIndicator.style.display = 'flex';
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        this.elements.typingIndicator.style.display = 'none';
    }
    
    autoResize() {
        const textarea = this.elements.chatInput;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
    }
    
    scrollToBottom() {
        setTimeout(() => {
            this.messageContainer.scrollTop = this.messageContainer.scrollHeight;
        }, 100);
    }
    
    formatTime(dateString) {
        const date = new Date(dateString);
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
    
    showError(message) {
        // Simple error display - you can enhance this
        alert(message);
    }
    
    async ajaxRequest(action, data = {}) {
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
        
        return await response.json();
    }
    
    // ===================
    // SESSION PERSISTENCE
    // ===================
    
    /**
     * Save session data to localStorage
     */
    saveSession() {
        const sessionData = {
            sessionId: this.sessionId,
            currentStage: this.currentStage,
            lastMessageId: this.lastMessageId,
            mainCategory: this.elements.selectedMainCategory?.textContent || this.elements.chatMainCategory?.textContent,
            subCategory: this.elements.chatSubCategory?.textContent,
            timestamp: Date.now()
        };
        
        localStorage.setItem('livechat_session', JSON.stringify(sessionData));
        this.sessionData = sessionData;
    }
    
    /**
     * Restore session from localStorage
     */
    restoreSession() {
        console.log('LiveChat: Starting session restore');
        console.time('Session Restore Total');
        
        try {
            console.time('Get localStorage');
            const savedSession = localStorage.getItem('livechat_session');
            console.timeEnd('Get localStorage');
            
            if (!savedSession) {
                console.log('No saved session found');
                console.timeEnd('Session Restore Total');
                return;
            }
            
            console.time('Parse JSON');
            const sessionData = JSON.parse(savedSession);
            console.timeEnd('Parse JSON');
            
            console.time('Check Session Age');
            // Check if session is too old (expire after 2 hours)
            const twoHours = 2 * 60 * 60 * 1000;
            if (Date.now() - sessionData.timestamp > twoHours) {
                console.log('Session expired, clearing');
                this.clearSession();
                console.timeEnd('Check Session Age');
                console.timeEnd('Session Restore Total');
                return;
            }
            console.timeEnd('Check Session Age');
            
            // Validate required session data
            if (!sessionData.sessionId || !sessionData.currentStage) {
                console.log('Invalid session data, clearing');
                this.clearSession();
                console.timeEnd('Session Restore Total');
                return;
            }
            
            console.log('Valid session found, restoring:', sessionData);
            
            // Restore session state
            this.sessionId = sessionData.sessionId;
            this.currentStage = sessionData.currentStage;
            this.lastMessageId = sessionData.lastMessageId || 0;
            this.sessionData = sessionData;
            
            // Validate session with server
            console.time('Validate with Server');
            this.validateAndRestoreSession(sessionData);
            console.timeEnd('Validate with Server');
            
        } catch (error) {
            console.error('Failed to restore session:', error);
            this.clearSession();
            console.timeEnd('Session Restore Total');
        }
    }
    
    /**
     * Validate session with server and restore UI state
     */
    async validateAndRestoreSession(sessionData) {
        console.log('LiveChat: Starting session validation');
        console.time('Validate Session Total');
        
        try {
            // For chat stage, verify session is still active by attempting to get messages
            if (sessionData.currentStage === 'chat_active' && sessionData.sessionId) {
                
                console.time('Restore UI Elements');
                // Restore UI elements first
                if (sessionData.mainCategory) {
                    this.elements.chatMainCategory.textContent = sessionData.mainCategory;
                    this.elements.selectedMainCategory.textContent = sessionData.mainCategory;
                }
                if (sessionData.subCategory) {
                    this.elements.chatSubCategory.textContent = sessionData.subCategory;
                }
                console.timeEnd('Restore UI Elements');
                
                // Load existing messages
                console.time('Load Existing Messages');
                await this.loadExistingMessages();
                console.timeEnd('Load Existing Messages');
                
                // Start SSE stream after a delay
                console.log('Scheduling SSE start in 100ms');
                setTimeout(() => {
                    console.log('Starting SSE connection now');
                    this.startSSE();
                }, 100);
                
                // Go to chat stage
                this.goToStage('chat_active');
                
                console.log('Session restored: Chat active');
                console.timeEnd('Validate Session Total');
                
            } else if (sessionData.currentStage === 'main-category') {
                // Restore main category selection stage
                await this.loadMainCategoriesFromServer();
                this.goToStage('main-category');
                console.timeEnd('Validate Session Total');
                
            } else if (sessionData.currentStage === 'sub-category' && sessionData.mainCategory) {
                // Restore sub category selection stage
                this.elements.selectedMainCategory.textContent = sessionData.mainCategory;
                await this.loadSubCategoriesFromServer(sessionData.mainCategory);
                this.goToStage('sub-category');
                console.timeEnd('Validate Session Total');
                
            } else {
                // Invalid stage, clear session
                console.log('Invalid stage, clearing session');
                this.clearSession();
                console.timeEnd('Validate Session Total');
            }
            
        } catch (error) {
            console.error('Session validation failed:', error);
            this.clearSession();
            console.timeEnd('Validate Session Total');
        }
    }
    
    /**
     * Load existing messages for restored session
     */
    async loadExistingMessages() {
        console.log('LiveChat: Loading existing messages for session:', this.sessionId);
        console.time('Load Messages AJAX');
        
        try {
            // Use AJAX to get existing messages instead of SSE
            const response = await this.ajaxRequest('livechat_get_messages', {
                session_id: this.sessionId,
                since_id: 0,
                limit: 50
            });
            console.timeEnd('Load Messages AJAX');
            
            if (response.success && response.data.messages) {
                // Clear existing messages
                this.messageContainer.innerHTML = '';
                
                // Add messages to UI
                response.data.messages.forEach(message => {
                    this.addMessage(message);
                    this.lastMessageId = Math.max(this.lastMessageId, message.id);
                });
                
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Failed to load existing messages:', error);
        }
    }
    
    /**
     * Load main categories from server 
     */
    async loadMainCategoriesFromServer() {
        try {
            const response = await this.ajaxRequest('livechat_get_main_categories');
            if (response.success && response.data.categories) {
                this.loadMainCategories(response.data.categories);
            }
        } catch (error) {
            console.error('Failed to load main categories:', error);
        }
    }
    
    /**
     * Load sub categories from server
     */
    async loadSubCategoriesFromServer(mainCategory) {
        try {
            const response = await this.ajaxRequest('livechat_get_subcategories', {
                session_id: this.sessionId,
                main_category: mainCategory
            });
            
            if (response.success && response.data.sub_categories) {
                this.loadSubCategories(response.data.sub_categories);
            }
        } catch (error) {
            console.error('Failed to load sub categories:', error);
        }
    }
    
    /**
     * Clear session data
     */
    clearSession() {
        localStorage.removeItem('livechat_session');
        this.sessionId = null;
        this.currentStage = 'welcome';
        this.lastMessageId = 0;
        this.sessionData = {};
        
        // Reset UI to welcome stage
        this.goToStage('welcome');
        
        // Clear message container
        if (this.messageContainer) {
            this.messageContainer.innerHTML = '';
        }
        
        console.log('Session cleared');
    }
    
    /**
     * Update session timestamp to prevent expiration
     */
    updateSessionTimestamp() {
        if (this.sessionData.sessionId) {
            this.sessionData.timestamp = Date.now();
            localStorage.setItem('livechat_session', JSON.stringify(this.sessionData));
        }
    }
}

// Initialize widget when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Prevent multiple instances
    if (window.liveChatWidget) {
        console.log('LiveChatWidget already initialized');
        return;
    }
    
    if (window.liveChatConfig) {
        window.liveChatWidget = new LiveChatWidget();
        console.log('LiveChatWidget initialized');
    }
});


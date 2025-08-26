<!-- LiveChat Widget -->
<div id="livechat-widget" class="livechat-widget" style="display: none;">
    <!-- Chat Trigger Button (Floating) -->
    <div id="chat-trigger" class="chat-trigger">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22H20C20.55 22 21 21.55 21 21V12C21 6.48 16.52 2 12 2ZM12 18C11.45 18 11 17.55 11 17C11 16.45 11.45 16 12 16C12.55 16 13 16.45 13 17C13 17.55 12.55 18 12 18ZM13 14H11V6H13V14Z" fill="white"/>
            <circle cx="7" cy="7" r="2" fill="white"/>
            <circle cx="17" cy="7" r="2" fill="white"/>  
            <circle cx="12" cy="17" r="2" fill="white"/>
        </svg>
        <span class="chat-dots">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </div>

    <!-- Chat Window -->
    <div id="chat-window" class="chat-window" style="display: none;">
        
        <!-- Stage 1: Welcome Screen -->
        <div id="stage-welcome" class="chat-stage active">
            <div class="chat-header">
                <div class="chat-header-content">
                    <img src="<?= THEME_URL ?>/assets/images/logo.svg" alt="Honors Club" class="chat-logo">
                    <div class="chat-title">
                        <h3>HONORS CLUB</h3>
                        <p>ALL THAT HONORS CLUB</p>
                    </div>
                </div>
                <button id="chat-close" class="chat-close">×</button>
            </div>
            
            <div class="chat-body">
                <div class="welcome-content">
                    <div class="welcome-icon">💬</div>
                    <h4>실시간 채팅 상담 안내</h4>
                    <p>궁금하신 점이 있으시면 언제든지 문의해주세요!</p>
                    <p>담당자가 순차적으로 확인 후 신속히 답변드리겠습니다.</p>
                    
                    <div class="operating-hours">
                        <div class="hours-icon">⏰</div>
                        <strong>운영시간</strong>
                        <p>월-금 오전 10시 ~ 오후 6시 (주말,공휴일 제외)</p>
                    </div>
                    
                    <button id="start-chat" class="btn-start-chat">문의하기</button>
                </div>
            </div>
        </div>

        <!-- Stage 2: Main Category Selection -->
        <div id="stage-main-category" class="chat-stage">
            <div class="chat-header">
                <button id="back-to-welcome" class="chat-back">←</button>
                <div class="chat-header-content">
                    <img src="<?= THEME_URL ?>/assets/images/logo.svg" alt="Honors Club" class="chat-logo">
                    <div class="chat-title">
                        <h3>HONORS CLUB</h3>
                        <p>ALL THAT HONORS CLUB</p>
                    </div>
                </div>
                <button class="chat-close">×</button>
            </div>
            
            <div class="chat-body">
                <div class="category-content">
                    <div class="chat-time">오후 12:02</div>
                    <div class="category-question">
                        <div class="avatar-small">
                            <img src="<?= THEME_URL ?>/assets/images/icons/icon-admin-menu-live-chat.svg" alt="상담원">
                        </div>
                        <span>어떤 점이 궁금하세요?</span>
                    </div>
                    
                    <div class="category-options" id="main-categories">
                        <!-- Dynamic categories will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage 3: Sub Category Selection -->
        <div id="stage-sub-category" class="chat-stage">
            <div class="chat-header">
                <button id="back-to-main-category" class="chat-back">←</button>
                <div class="chat-header-content">
                    <img src="<?= THEME_URL ?>/assets/images/logo.svg" alt="Honors Club" class="chat-logo">
                    <div class="chat-title">
                        <h3>HONORS CLUB</h3>
                        <p>ALL THAT HONORS CLUB</p>
                    </div>
                </div>
                <button class="chat-close">×</button>
            </div>
            
            <div class="chat-body">
                <div class="category-content">
                    <div class="chat-time">오후 12:02</div>
                    
                    <!-- Selected main category -->
                    <div class="selected-category">
                        <span class="category-badge" id="selected-main-category"></span>
                        <div class="avatar-small">
                            <img src="<?= THEME_URL ?>/assets/images/icons/icon-admin-menu-live-chat.svg" alt="상담원">
                        </div>
                    </div>
                    
                    <div class="category-question">
                        <span>어떤 점이 궁금하세요?</span>
                    </div>
                    
                    <div class="category-options" id="sub-categories">
                        <!-- Dynamic sub categories will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage 4: Chat Interface -->
        <div id="stage-chat_active" class="chat-stage">
            <div class="chat-header">
                <button id="back-to-categories" class="chat-back">←</button>
                <div class="chat-header-content">
                    <img src="<?= THEME_URL ?>/assets/images/logo.svg" alt="Honors Club" class="chat-logo">
                    <div class="chat-title">
                        <h3>HONORS CLUB</h3>
                        <p>ALL THAT HONORS CLUB</p>
                    </div>
                </div>
                <button class="chat-close">×</button>
            </div>
            
            <div class="chat-body">
                <div class="chat-time">오후 12:02</div>
                
                <!-- Selected categories breadcrumb -->
                <div class="chat-breadcrumb">
                    <span class="category-badge" id="chat-main-category"></span>
                    <span class="breadcrumb-arrow">></span>
                    <span class="category-badge" id="chat-sub-category"></span>
                </div>
                
                <!-- Chat messages -->
                <div id="chat-messages" class="chat-messages">
                    <!-- Messages will be loaded here -->
                </div>
                
                <!-- Typing indicator -->
                <div id="typing-indicator" class="typing-indicator" style="display: none;">
                    <div class="avatar-small">
                        <img src="<?= THEME_URL ?>/assets/images/icons/icon-admin-menu-live-chat.svg" alt="상담원">
                    </div>
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="chat-footer">
                <div class="chat-input-wrapper">
                    <div class="input-container">
                        <textarea id="chat-input" placeholder="내용을 입력해주세요" rows="1"></textarea>
                        <div class="input-actions">
                            <button id="attach-file" class="btn-attach">📎</button>
                            <button id="send-message" class="btn-send">전송</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Screen -->
        <div id="stage-loading" class="chat-stage">
            <div class="chat-header">
                <div class="chat-header-content">
                    <img src="<?= THEME_URL ?>/assets/images/logo.svg" alt="Honors Club" class="chat-logo">
                    <div class="chat-title">
                        <h3>HONORS CLUB</h3>
                        <p>ALL THAT HONORS CLUB</p>
                    </div>
                </div>
            </div>
            
            <div class="chat-body">
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <p>연결 중...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Data -->
<script>
window.liveChatConfig = {
    ajaxUrl: '<?= admin_url('admin-ajax.php') ?>',
    homeUrl: '<?= home_url() ?>',
    nonce: '<?= wp_create_nonce('livechat_nonce') ?>',
    themeUrl: '<?= THEME_URL ?>',
    currentUser: <?= json_encode([
        'name' => wp_get_current_user()->display_name ?? '',
        'email' => wp_get_current_user()->user_email ?? ''
    ]) ?>,
    routes: {
        startSession: '/chat/start',
        getSubCategories: '/chat/subcategories', 
        startChat: '/chat/begin',
        sendMessage: '/chat/send',
        streamMessages: '/chat/stream',
        closeChat: '/chat/close'
    }
};
</script>
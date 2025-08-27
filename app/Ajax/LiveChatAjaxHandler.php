<?php
use App\Services\LiveChatService;
use Exception;

class LiveChatAjaxHandler {
    
    private $liveChatService;
    
    public function __construct() {
        $this->liveChatService = new LiveChatService();
        $this->init();
    }
    
    public function init() {
        // Register AJAX handlers for logged in and non-logged in users
        add_action('wp_ajax_livechat_start', [$this, 'start']);
        add_action('wp_ajax_nopriv_livechat_start', [$this, 'start']);
        
        add_action('wp_ajax_livechat_subcategories', [$this, 'getSubcategories']);
        add_action('wp_ajax_nopriv_livechat_subcategories', [$this, 'getSubcategories']);
        
        add_action('wp_ajax_livechat_begin', [$this, 'beginChat']);
        add_action('wp_ajax_nopriv_livechat_begin', [$this, 'beginChat']);
        
        add_action('wp_ajax_livechat_send', [$this, 'sendMessage']);
        add_action('wp_ajax_nopriv_livechat_send', [$this, 'sendMessage']);
        
        add_action('wp_ajax_livechat_get_session', [$this, 'getSession']);
        add_action('wp_ajax_nopriv_livechat_get_session', [$this, 'getSession']);
    }
    
    private function verifyNonce() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
            wp_die('Security check failed');
        }
    }
    
    /**
     * Stage 1: Start chat session
     * POST /chat/start → return session_id + main categories
     */
    public function start() {
        $this->verifyNonce();
        
        try {
            // Create new session
            $sessionId = $this->liveChatService->createSession();
            
            // Get main categories
            $categories = include get_template_directory() . '/config/livechat_categories.php';
            $mainCategories = array_keys($categories['main_categories']);
            
            wp_send_json_success([
                'session_id' => $sessionId,
                'main_categories' => $mainCategories,
                'message' => 'Chat session started'
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Failed to start chat session',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stage 2: Get subcategories for selected main category
     * POST /chat/subcategories → return sub categories
     */
    public function getSubcategories() {
        $this->verifyNonce();
        
        $sessionId = sanitize_text_field($_POST['session_id'] ?? '');
        $mainCategory = sanitize_text_field($_POST['main_category'] ?? '');
        
        if (empty($sessionId) || empty($mainCategory)) {
            wp_send_json_error(['message' => 'Missing required parameters']);
        }
        
        try {
            // Update session with selected main category
            $this->liveChatService->updateSession($sessionId, [
                'category_main' => $mainCategory,
                'chat_stage' => 'category_sub'
            ]);
            
            // Get subcategories
            $categories = include get_template_directory() . '/config/livechat_categories.php';
            $subCategories = $categories['main_categories'][$mainCategory] ?? [];
            
            wp_send_json_success([
                'subcategories' => $subCategories,
                'main_category' => $mainCategory
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Failed to get subcategories',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stage 3: Begin actual chat
     * POST /chat/begin → send welcome message + activate chat
     */
    public function beginChat() {
        $this->verifyNonce();
        
        $sessionId = sanitize_text_field($_POST['session_id'] ?? '');
        $subCategory = sanitize_text_field($_POST['sub_category'] ?? '');
        $customerName = sanitize_text_field($_POST['customer_name'] ?? '');
        $customerEmail = sanitize_email($_POST['customer_email'] ?? '');
        
        if (empty($sessionId)) {
            wp_send_json_error(['message' => 'Missing session_id parameter']);
        }
        
        try {
            // Update session with subcategory and customer info
            // subCategory có thể empty nếu main category không có sub
            $updateData = [
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'chat_stage' => 'chat_active',
                'status' => 'waiting'
            ];
            
            // Chỉ update category_sub nếu có giá trị
            if (!empty($subCategory)) {
                $updateData['category_sub'] = $subCategory;
            }
            
            $this->liveChatService->updateSession($sessionId, $updateData);
            
            // Send welcome message
            $welcomeMessage = "안녕하세요! {$customerName}님, 문의해주셔서 감사합니다. 담당자가 확인 후 신속히 답변드리겠습니다.";
            $this->liveChatService->saveMessage($sessionId, 'system', $welcomeMessage, 'welcome');
            
            wp_send_json_success([
                'message' => 'Chat activated successfully',
                'welcome_message' => $welcomeMessage
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Failed to begin chat',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stage 4: Send message
     * POST /chat/send → send message
     */
    public function sendMessage() {
        $this->verifyNonce();
        
        $sessionId = sanitize_text_field($_POST['session_id'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        $senderName = sanitize_text_field($_POST['sender_name'] ?? '');
        
        if (empty($sessionId) || empty($message)) {
            wp_send_json_error(['message' => 'Missing required parameters']);
        }
        
        try {
            // Save customer message
            $messageId = $this->liveChatService->saveMessage($sessionId, 'customer', $message, 'text', $senderName);
            
            wp_send_json_success([
                'message_id' => $messageId,
                'message' => 'Message sent successfully'
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get session info from database
     * POST /chat/get_session → return session info + messages
     */
    public function getSession() {
        $this->verifyNonce();
        
        $sessionId = sanitize_text_field($_POST['session_id'] ?? '');
        
        if (empty($sessionId)) {
            wp_send_json_error(['message' => 'Missing session_id parameter']);
        }
        
        try {
            // Get session data
            $session = $this->liveChatService->getSession($sessionId);
            
            if (!$session) {
                wp_send_json_error(['message' => 'Session not found']);
                return;
            }
            
            // Get messages for this session
            $messages = $this->liveChatService->getNewMessages($sessionId, 0); // Get all messages
            
            // Get categories if needed
            $categories = include get_template_directory() . '/config/livechat_categories.php';
            $subcategories = [];
            if (!empty($session->category_main)) {
                $subcategories = $categories['main_categories'][$session->category_main] ?? [];
            }
            
            wp_send_json_success([
                'session' => [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'chat_stage' => $session->chat_stage,
                    'category_main' => $session->category_main,
                    'category_sub' => $session->category_sub,
                    'customer_name' => $session->customer_name,
                    'customer_email' => $session->customer_email,
                    'status' => $session->status,
                    'created_at' => $session->created_at
                ],
                'messages' => array_map(function($message) {
                    return [
                        'id' => $message->id,
                        'sender_type' => $message->sender_type,
                        'sender_name' => $message->sender_name,
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'created_at' => $message->created_at
                    ];
                }, $messages),
                'subcategories' => $subcategories
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Failed to get session',
                'error' => $e->getMessage()
            ]);
        }
    }
}
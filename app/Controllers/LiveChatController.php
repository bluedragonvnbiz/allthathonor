<?php

use App\Database\LiveChatDatabase;

class LiveChatController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Start new chat session - returns session_id
     */
    public function startSession() {
        $sessionId = $this->generateSessionId();
        
        $sessionDbId = LiveChatDatabase::createSession($sessionId);
        
        if (!$sessionDbId) {
            wp_send_json_error('Failed to create chat session', 500);
            return;
        }
        
        wp_send_json_success([
            'session_id' => $sessionId,
            'stage' => 'category_main',
            'main_categories' => array_keys(LiveChatDatabase::getMainCategories())
        ]);
    }
    
    /**
     * Get sub categories for selected main category
     */
    public function getSubCategories() {
        $sessionId = $_POST['session_id'] ?? '';
        $mainCategory = $_POST['main_category'] ?? '';
        
        if (!$sessionId || !$mainCategory) {
            wp_send_json_error('Missing required parameters');
            return;
        }
        
        // Validate session exists
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session) {
            wp_send_json_error('Invalid session');
            return;
        }
        
        // Update session with main category
        LiveChatDatabase::setCategories($sessionId, $mainCategory);
        
        $subCategories = LiveChatDatabase::getSubCategories($mainCategory);
        
        wp_send_json_success([
            'session_id' => $sessionId,
            'main_category' => $mainCategory,
            'sub_categories' => $subCategories,
            'stage' => 'category_sub'
        ]);
    }
    
    /**
     * Complete category selection and start chat
     */
    public function startChat() {
        $sessionId = $_POST['session_id'] ?? '';
        $mainCategory = $_POST['main_category'] ?? '';
        $subCategory = $_POST['sub_category'] ?? '';
        
        if (!$sessionId || !$mainCategory || !$subCategory) {
            wp_send_json_error('Missing required parameters');
            return;
        }
        
        // Validate session exists
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session) {
            wp_send_json_error('Invalid session');
            return;
        }
        
        // Set sub category and activate chat
        LiveChatDatabase::setCategories($sessionId, $mainCategory, $subCategory);
        
        // Add welcome message
        LiveChatDatabase::addWelcomeMessage($sessionId, $mainCategory, $subCategory);
        
        wp_send_json_success([
            'session_id' => $sessionId,
            'main_category' => $mainCategory,
            'sub_category' => $subCategory,
            'stage' => 'chat_active',
            'message' => 'Chat session started successfully'
        ]);
    }
    
    /**
     * Send message to chat
     */
    public function sendMessage() {
        $sessionId = $_POST['session_id'] ?? '';
        $message = $_POST['message'] ?? '';
        $senderName = $_POST['sender_name'] ?? '';
        
        if (!$sessionId || !$message) {
            wp_send_json_error('Missing required parameters');
            return;
        }
        
        // Validate session exists and is active
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session || $session['chat_stage'] !== 'chat_active') {
            wp_send_json_error('Invalid or inactive session');
            return;
        }
        
        // Add message
        $messageId = LiveChatDatabase::addMessage(
            $sessionId, 
            'customer', 
            sanitize_textarea_field($message),
            'text',
            sanitize_text_field($senderName)
        );
        
        if (!$messageId) {
            wp_send_json_error('Failed to send message');
            return;
        }
        
        wp_send_json_success([
            'message_id' => $messageId,
            'session_id' => $sessionId,
            'message' => 'Message sent successfully'
        ]);
    }
    
    /**
     * SSE Stream endpoint for real-time messages
     */
    public function streamMessages() {
        $sessionId = $_GET['session_id'] ?? '';
        $sinceId = (int) ($_GET['since_id'] ?? 0);
        
        if (!$sessionId) {
            http_response_code(400);
            echo "Missing session_id parameter";
            exit;
        }
        
        // Validate session exists
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session) {
            http_response_code(404);
            echo "Session not found";
            exit;
        }
        
        // Only stream if chat is active
        if ($session['chat_stage'] !== 'chat_active') {
            http_response_code(400);
            echo "Chat not active";
            exit;
        }
        
        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');
        
        // Set memory and time limits
        ini_set('memory_limit', '32M');
        set_time_limit(0);
        ignore_user_abort(false);
        
        $lastActivity = time();
        $maxIdleTime = 300; // 5 minutes
        
        while (true) {
            // Check if connection is still alive
            if (connection_aborted()) {
                break;
            }
            
            // Check idle timeout
            if (time() - $lastActivity > $maxIdleTime) {
                echo "event: timeout\n";
                echo "data: Connection timeout\n\n";
                break;
            }
            
            // Get new messages
            $messages = LiveChatDatabase::getMessages($sessionId, $sinceId, 20);
            
            if (!empty($messages)) {
                $lastActivity = time();
                
                foreach ($messages as $message) {
                    echo "event: message\n";
                    echo "data: " . json_encode([
                        'id' => $message['id'],
                        'session_id' => $message['session_id'],
                        'sender_type' => $message['sender_type'],
                        'sender_name' => $message['sender_name'],
                        'message_type' => $message['message_type'],
                        'message' => $message['message'],
                        'created_at' => $message['created_at'],
                        'metadata' => $message['metadata'] ? json_decode($message['metadata'], true) : null
                    ]) . "\n\n";
                    
                    $sinceId = max($sinceId, (int) $message['id']);
                }
            }
            
            // Send heartbeat every 30 seconds
            if (time() % 30 === 0) {
                echo "event: heartbeat\n";
                echo "data: " . json_encode(['timestamp' => time()]) . "\n\n";
            }
            
            ob_flush();
            flush();
            
            // Sleep for 2 seconds before checking again
            sleep(2);
        }
    }
    
    /**
     * Get chat history for admin view
     */
    public function getChatHistory() {
        $sessionId = $_GET['session_id'] ?? '';
        
        if (!$sessionId) {
            wp_send_json_error('Missing session_id parameter');
            return;
        }
        
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session) {
            wp_send_json_error('Session not found');
            return;
        }
        
        $messages = LiveChatDatabase::getAllMessages($sessionId);
        
        wp_send_json_success([
            'session' => $session,
            'messages' => $messages
        ]);
    }
    
    /**
     * Close chat session
     */
    public function closeChat() {
        $sessionId = $_POST['session_id'] ?? '';
        
        if (!$sessionId) {
            wp_send_json_error('Missing session_id parameter');
            return;
        }
        
        $session = LiveChatDatabase::getSession($sessionId);
        if (!$session) {
            wp_send_json_error('Session not found');
            return;
        }
        
        LiveChatDatabase::updateChatStage($sessionId, 'closed');
        
        wp_send_json_success([
            'session_id' => $sessionId,
            'message' => 'Chat session closed'
        ]);
    }
    
    /**
     * Generate unique session ID
     */
    private function generateSessionId() {
        return 'chat_' . time() . '_' . wp_generate_password(12, false);
    }
}
<?php

namespace App\Services;

use App\Database\LiveChatDatabase;
use Exception;

class LiveChatService {
    
    public function __construct() {
        // Initialize service
    }
    
    // ===================
    // DATA ACCESS METHODS
    // ===================
    
    /**
     * Create new session in database
     */
    private function createSession($sessionId, $customerData = []) {
        global $wpdb;
        
        $table = LiveChatDatabase::getSessionTableName();
        
        $data = array_merge([
            'session_id' => $sessionId,
            'chat_stage' => 'category_main',
            'status' => 'waiting',
            'created_at' => current_time('mysql')
        ], $customerData);
        
        $result = $wpdb->insert($table, $data);
        
        if ($result === false) {
            error_log("LiveChat: Failed to create session - " . $wpdb->last_error);
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get session by session ID
     */
    public function getSession($sessionId) {
        global $wpdb;
        
        $table = LiveChatDatabase::getSessionTableName();
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE session_id = %s",
            $sessionId
        ), ARRAY_A);
    }
    
    /**
     * Update session data
     */
    private function updateSession($sessionId, $data) {
        global $wpdb;
        
        $table = LiveChatDatabase::getSessionTableName();
        
        $data['updated_at'] = current_time('mysql');
        
        return $wpdb->update(
            $table,
            $data,
            ['session_id' => $sessionId],
            null,
            ['%s']
        );
    }
    
    /**
     * Set categories for session
     */
    private function setCategories($sessionId, $mainCategory, $subCategory = null) {
        $data = [
            'category_main' => $mainCategory,
            'updated_at' => current_time('mysql')
        ];
        
        if ($subCategory) {
            $data['category_sub'] = $subCategory;
            $data['chat_stage'] = 'chat_active';
            $data['status'] = 'active';
        } else {
            $data['chat_stage'] = 'category_sub';
        }
        
        return $this->updateSession($sessionId, $data);
    }
    
    /**
     * Add message to database
     */
    private function addMessage($sessionId, $senderType, $message, $messageType = 'text', $senderName = null, $metadata = null) {
        global $wpdb;
        
        $table = LiveChatDatabase::getMessageTableName();
        
        $data = [
            'session_id' => $sessionId,
            'sender_type' => $senderType,
            'message_type' => $messageType,
            'sender_name' => $senderName,
            'message' => $message,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => current_time('mysql')
        ];
        
        $result = $wpdb->insert($table, $data);
        
        if ($result === false) {
            error_log("LiveChat: Failed to add message - " . $wpdb->last_error);
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Add welcome message
     */
    private function addWelcomeMessage($sessionId, $categoryMain, $categorySub) {
        $welcomeText = $this->getWelcomeMessage($categoryMain);
        
        $metadata = [
            'category_main' => $categoryMain,
            'category_sub' => $categorySub
        ];
        
        return $this->addMessage($sessionId, 'system', $welcomeText, 'welcome', '상담원', $metadata);
    }
    
    /**
     * Get messages from database
     */
    public function getMessagesFromDB($sessionId, $sinceId = 0, $limit = 50) {
        global $wpdb;
        
        $table = LiveChatDatabase::getMessageTableName();
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE session_id = %s AND id > %d 
             ORDER BY created_at ASC 
             LIMIT %d",
            $sessionId, $sinceId, $limit
        ), ARRAY_A);
    }
    
    /**
     * Get all messages for session
     */
    public function getAllMessagesFromDB($sessionId) {
        global $wpdb;
        
        $table = LiveChatDatabase::getMessageTableName();
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE session_id = %s 
             ORDER BY created_at ASC",
            $sessionId
        ), ARRAY_A);
    }
    
    /**
     * Get active sessions
     */
    public function getActiveSessions() {
        global $wpdb;
        
        $table = LiveChatDatabase::getSessionTableName();
        
        return $wpdb->get_results(
            "SELECT * FROM $table 
             WHERE status IN ('waiting', 'active') 
             ORDER BY created_at ASC",
            ARRAY_A
        );
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount($sessionId, $forSenderType) {
        global $wpdb;
        
        $table = LiveChatDatabase::getMessageTableName();
        
        // Count unread messages from opposite sender type
        $targetSenderType = $forSenderType === 'admin' ? 'customer' : 'admin';
        
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
             WHERE session_id = %s 
             AND sender_type = %s 
             AND is_read = 0",
            $sessionId, $targetSenderType
        ));
    }
    
    // ===================
    // CONFIG METHODS
    // ===================
    
    /**
     * Get chat categories from config
     */
    private function getChatConfig() {
        static $config = null;
        if ($config === null) {
            $config = include get_template_directory() . '/config/livechat_categories.php';
        }
        return $config;
    }
    
    /**
     * Get main categories
     */
    public function getMainCategories() {
        $config = $this->getChatConfig();
        return array_keys($config['main_categories']);
    }
    
    /**
     * Get sub categories for main category
     */
    public function getSubCategories($mainCategory) {
        $config = $this->getChatConfig();
        return $config['main_categories'][$mainCategory] ?? [];
    }
    
    /**
     * Get welcome message for category
     */
    private function getWelcomeMessage($mainCategory) {
        $config = $this->getChatConfig();
        
        // Map categories to welcome message keys
        $messageMap = [
            '직원 혜택' => 'staff_benefits',
            '일반인 가기금' => 'general_inquiry',
            '비회원' => 'non_member'
        ];
        
        $messageKey = $messageMap[$mainCategory] ?? 'default';
        return $config['welcome_messages'][$messageKey];
    }
    
    // ===================
    // BUSINESS LOGIC METHODS
    // ===================
    
    /**
     * Create new chat session with validation
     */
    public function createChatSession($customerData = []) {
        try {
            $sessionId = $this->generateSessionId();
            
            // Validate customer data if provided
            if (!empty($customerData)) {
                $customerData = $this->validateCustomerData($customerData);
            }
            
            $sessionDbId = $this->createSession($sessionId, $customerData);
            
            if (!$sessionDbId) {
                throw new Exception('Failed to create chat session in database');
            }
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'db_id' => $sessionDbId,
                'stage' => 'category_main'
            ];
            
        } catch (Exception $e) {
            error_log("LiveChatService: createChatSession failed - " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Handle category selection flow
     */
    public function handleCategorySelection($sessionId, $mainCategory, $subCategory = null) {
        try {
            // Validate session exists
            $session = $this->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session ID');
            }
            
            // Validate categories
            if (!$this->isValidMainCategory($mainCategory)) {
                throw new Exception('Invalid main category');
            }
            
            if ($subCategory && !$this->isValidSubCategory($mainCategory, $subCategory)) {
                throw new Exception('Invalid sub category');
            }
            
            // Update session with categories
            $result = $this->setCategories($sessionId, $mainCategory, $subCategory);
            
            if (!$result) {
                throw new Exception('Failed to update categories');
            }
            
            $response = [
                'success' => true,
                'session_id' => $sessionId,
                'main_category' => $mainCategory
            ];
            
            if ($subCategory) {
                // Both categories selected - start chat with welcome message
                $welcomeId = $this->addWelcomeMessage($sessionId, $mainCategory, $subCategory);
                
                $response['sub_category'] = $subCategory;
                $response['stage'] = 'chat_active';
                $response['welcome_message_id'] = $welcomeId;
                $response['message'] = 'Chat session activated with welcome message';
            } else {
                // Only main category selected - return sub categories
                $response['sub_categories'] = $this->getSubCategories($mainCategory);
                $response['stage'] = 'category_sub';
            }
            
            return $response;
            
        } catch (Exception $e) {
            error_log("LiveChatService: handleCategorySelection failed - " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send message with validation and processing
     */
    public function sendMessage($sessionId, $message, $senderType = 'customer', $senderName = null) {
        try {
            // Validate session is active
            $session = $this->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session ID');
            }
            
            if ($session['chat_stage'] !== 'chat_active') {
                throw new Exception('Chat session is not active');
            }
            
            // Validate and sanitize message
            $message = $this->sanitizeMessage($message);
            if (empty($message)) {
                throw new Exception('Message cannot be empty');
            }
            
            // Check message length
            if (strlen($message) > 1000) {
                throw new Exception('Message too long (max 1000 characters)');
            }
            
            // Add message to database
            $messageId = $this->addMessage(
                $sessionId, 
                $senderType, 
                $message, 
                'text', 
                $senderName
            );
            
            if (!$messageId) {
                throw new Exception('Failed to save message');
            }
            
            // Update session activity
            $this->updateSession($sessionId, [
                'updated_at' => current_time('mysql')
            ]);
            
            return [
                'success' => true,
                'message_id' => $messageId,
                'session_id' => $sessionId,
                'sender_type' => $senderType,
                'message' => $message,
                'timestamp' => current_time('mysql')
            ];
            
        } catch (Exception $e) {
            error_log("LiveChatService: sendMessage failed - " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get chat messages with pagination and filtering
     */
    public function getChatMessages($sessionId, $sinceId = 0, $limit = 50) {
        try {
            // Validate session
            $session = $this->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session ID');
            }
            
            $messages = $this->getMessagesFromDB($sessionId, $sinceId, $limit);
            
            // Format messages for frontend
            $formattedMessages = array_map(function($message) {
                return [
                    'id' => (int) $message['id'],
                    'session_id' => $message['session_id'],
                    'sender_type' => $message['sender_type'],
                    'sender_name' => $message['sender_name'],
                    'message_type' => $message['message_type'],
                    'message' => $message['message'],
                    'metadata' => $message['metadata'] ? json_decode($message['metadata'], true) : null,
                    'is_read' => (bool) $message['is_read'],
                    'created_at' => $message['created_at'],
                    'formatted_time' => $this->formatMessageTime($message['created_at'])
                ];
            }, $messages);
            
            return [
                'success' => true,
                'messages' => $formattedMessages,
                'count' => count($formattedMessages),
                'session_id' => $sessionId
            ];
            
        } catch (Exception $e) {
            error_log("LiveChatService: getChatMessages failed - " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Close chat session
     */
    public function closeChatSession($sessionId) {
        try {
            $session = $this->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session ID');
            }
            
            $data = [
                'chat_stage' => 'closed',
                'status' => 'closed',
                'closed_at' => current_time('mysql')
            ];
            
            $result = $this->updateSession($sessionId, $data);
            
            if (!$result) {
                throw new Exception('Failed to close session');
            }
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'message' => 'Chat session closed successfully'
            ];
            
        } catch (Exception $e) {
            error_log("LiveChatService: closeChatSession failed - " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get session statistics for admin
     */
    public function getSessionStats($sessionId) {
        try {
            $session = $this->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session ID');
            }
            
            $messages = $this->getAllMessagesFromDB($sessionId);
            
            $stats = [
                'session_id' => $sessionId,
                'status' => $session['status'],
                'chat_stage' => $session['chat_stage'],
                'category_main' => $session['category_main'],
                'category_sub' => $session['category_sub'],
                'total_messages' => count($messages),
                'customer_messages' => count(array_filter($messages, function($msg) {
                    return $msg['sender_type'] === 'customer';
                })),
                'admin_messages' => count(array_filter($messages, function($msg) {
                    return $msg['sender_type'] === 'admin';
                })),
                'created_at' => $session['created_at'],
                'updated_at' => $session['updated_at'],
                'duration' => $this->calculateSessionDuration($session)
            ];
            
            return [
                'success' => true,
                'stats' => $stats
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    // ===================
    // HELPER METHODS
    // ===================
    
    private function generateSessionId() {
        return 'chat_' . time() . '_' . wp_generate_password(12, false);
    }
    
    private function validateCustomerData($data) {
        $validated = [];
        
        if (isset($data['customer_name'])) {
            $validated['customer_name'] = sanitize_text_field($data['customer_name']);
        }
        
        if (isset($data['customer_email'])) {
            $email = sanitize_email($data['customer_email']);
            if (is_email($email)) {
                $validated['customer_email'] = $email;
            }
        }
        
        if (isset($data['customer_phone'])) {
            $validated['customer_phone'] = sanitize_text_field($data['customer_phone']);
        }
        
        return $validated;
    }
    
    private function isValidMainCategory($category) {
        $categories = $this->getMainCategories();
        return in_array($category, $categories);
    }
    
    private function isValidSubCategory($mainCategory, $subCategory) {
        $subCategories = $this->getSubCategories($mainCategory);
        return in_array($subCategory, $subCategories);
    }
    
    private function sanitizeMessage($message) {
        return sanitize_textarea_field(trim($message));
    }
    
    private function formatMessageTime($datetime) {
        $timestamp = strtotime($datetime);
        $now = current_time('timestamp');
        $diff = $now - $timestamp;
        
        if ($diff < 60) {
            return '방금 전';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . '분 전';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . '시간 전';
        } else {
            return date('Y-m-d H:i', $timestamp);
        }
    }
    
    private function calculateSessionDuration($session) {
        $start = strtotime($session['created_at']);
        $end = $session['closed_at'] ? strtotime($session['closed_at']) : current_time('timestamp');
        
        return $end - $start; // duration in seconds
    }
}
<?php

namespace App\Services;

use App\Database\LiveChatDatabase;

class LiveChatService {
    
    private $sessionTable;
    private $messageTable;
    
    public function __construct() {
        $this->sessionTable = LiveChatDatabase::getSessionTableName();
        $this->messageTable = LiveChatDatabase::getMessageTableName();
    }
    
    /**
     * Create new chat session
     */
    public function createSession() {
        global $wpdb;
        
        $sessionId = $this->generateSessionId();
        
        $result = $wpdb->insert(
            $this->sessionTable,
            [
                'session_id' => $sessionId,
                'client_connected' => 1,
                'chat_stage' => 'category_main',
                'status' => 'waiting',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );
        
        if ($result === false) {
            throw new \Exception('Failed to create session: ' . $wpdb->last_error);
        }
        
        return $sessionId;
    }
    
    /**
     * Update session data
     */
    public function updateSession($sessionId, $data) {
        global $wpdb;
        
        $data['updated_at'] = current_time('mysql');
        
        $result = $wpdb->update(
            $this->sessionTable,
            $data,
            ['session_id' => $sessionId],
            null,
            ['%s']
        );
        
        if ($result === false) {
            throw new \Exception('Failed to update session: ' . $wpdb->last_error);
        }
        
        return $result;
    }
    
    /**
     * Get session by ID
     */
    public function getSession($sessionId) {
        global $wpdb;
        
        $session = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->sessionTable} WHERE session_id = %s", $sessionId)
        );
        
        if (!$session) {
            throw new \Exception('Session not found');
        }
        
        return $session;
    }
    
    /**
     * Save message to database
     */
    public function saveMessage($sessionId, $senderType, $message, $messageType = 'text', $senderName = null) {
        global $wpdb;
        
        // Verify session exists
        $this->getSession($sessionId);
        
        $result = $wpdb->insert(
            $this->messageTable,
            [
                'session_id' => $sessionId,
                'sender_type' => $senderType,
                'message_type' => $messageType,
                'sender_name' => $senderName,
                'message' => $message,
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );
        
        if ($result === false) {
            throw new \Exception('Failed to save message: ' . $wpdb->last_error);
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get new messages since last message ID
     */
    public function getNewMessages($sessionId, $lastMessageId = 0) {
        global $wpdb;
        
        $messages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->messageTable} 
                WHERE session_id = %s AND id > %d 
                ORDER BY created_at ASC",
                $sessionId,
                $lastMessageId
            )
        );
        
        return $messages;
    }
    
    /**
     * Get all messages for a session
     */
    public function getMessages($sessionId) {
        global $wpdb;
        
        $messages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->messageTable} 
                WHERE session_id = %s 
                ORDER BY created_at ASC",
                $sessionId
            )
        );
        
        return $messages;
    }
    
    /**
     * Mark session as connected or disconnected for SSE detection
     */
    public function markSessionConnected($sessionId, $connected = 1) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->sessionTable,
            [
                'client_connected' => $connected,
                'updated_at' => current_time('mysql')
            ],
            ['session_id' => $sessionId],
            ['%d', '%s'],
            ['%s']
        );
        
        if ($result === false) {
            throw new \Exception('Failed to mark session disconnected: ' . $wpdb->last_error);
        }
        
        return $result;
    }
    
    /**
     * Check if client is still connected
     */
    public function isClientConnected($sessionId) {
        global $wpdb;
        
        $connected = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT client_connected FROM {$this->sessionTable} WHERE session_id = %s",
                $sessionId
            )
        );
        
        return (bool) $connected;
    }
    
    /**
     * Close chat session
     */
    public function closeSession($sessionId) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->sessionTable,
            [
                'status' => 'closed',
                'chat_stage' => 'closed',
                'closed_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            ['session_id' => $sessionId],
            ['%s', '%s', '%s', '%s'],
            ['%s']
        );
        
        if ($result === false) {
            throw new \Exception('Failed to close session: ' . $wpdb->last_error);
        }
        
        return $result;
    }
    
    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($sessionId, $senderType = null) {
        global $wpdb;
        
        $where = ['session_id' => $sessionId, 'is_read' => 0];
        $where_format = ['%s', '%d'];
        
        if ($senderType) {
            $where['sender_type'] = $senderType;
            $where_format[] = '%s';
        }
        
        $result = $wpdb->update(
            $this->messageTable,
            ['is_read' => 1],
            $where,
            ['%d'],
            $where_format
        );
        
        return $result;
    }
    
    /**
     * Generate unique session ID
     */
    private function generateSessionId() {
        return 'chat_' . uniqid() . '_' . wp_generate_password(8, false);
    }
    
    /**
     * Validate category exists in config
     */
    public function validateCategory($mainCategory, $subCategory = null) {
        $categories = include get_template_directory() . '/config/livechat_categories.php';
        
        if (!isset($categories['main_categories'][$mainCategory])) {
            throw new \Exception('Invalid main category');
        }
        
        if ($subCategory && !in_array($subCategory, $categories['main_categories'][$mainCategory])) {
            throw new \Exception('Invalid sub category');
        }
        
        return true;
    }
    
    /**
     * Get session statistics
     */
    public function getSessionStats($sessionId) {
        global $wpdb;
        
        $stats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                    COUNT(*) as total_messages,
                    SUM(CASE WHEN sender_type = 'customer' THEN 1 ELSE 0 END) as customer_messages,
                    SUM(CASE WHEN sender_type = 'admin' THEN 1 ELSE 0 END) as admin_messages,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages
                FROM {$this->messageTable} 
                WHERE session_id = %s",
                $sessionId
            )
        );
        
        return $stats;
    }
}
<?php

namespace App\Ajax;

use App\Services\LiveChatService;
use Exception;

class LiveChatAjaxHandler {
    
    private $liveChatService;
    
    public function __construct() {
        $this->liveChatService = new LiveChatService();
        $this->registerHandlers();
    }
    
    /**
     * Register AJAX handlers
     */
    public function registerHandlers() {
        // Public AJAX actions (available to logged in and non-logged in users)
        add_action('wp_ajax_livechat_start_session', [$this, 'startSession']);
        add_action('wp_ajax_nopriv_livechat_start_session', [$this, 'startSession']);
        
        add_action('wp_ajax_livechat_get_subcategories', [$this, 'getSubCategories']);
        add_action('wp_ajax_nopriv_livechat_get_subcategories', [$this, 'getSubCategories']);
        
        add_action('wp_ajax_livechat_select_category', [$this, 'selectCategory']);
        add_action('wp_ajax_nopriv_livechat_select_category', [$this, 'selectCategory']);
        
        add_action('wp_ajax_livechat_send_message', [$this, 'sendMessage']);
        add_action('wp_ajax_nopriv_livechat_send_message', [$this, 'sendMessage']);
        
        add_action('wp_ajax_livechat_close_session', [$this, 'closeSession']);
        add_action('wp_ajax_nopriv_livechat_close_session', [$this, 'closeSession']);
        
        // Admin only AJAX actions
        add_action('wp_ajax_livechat_admin_send_message', [$this, 'adminSendMessage']);
        add_action('wp_ajax_livechat_get_session_stats', [$this, 'getSessionStats']);
        add_action('wp_ajax_livechat_get_active_sessions', [$this, 'getActiveSessions']);
    }
    
    /**
     * Start new chat session
     */
    public function startSession() {
        try {
            // Verify nonce for security
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            // Get customer data if provided  
            $customerData = [];
            if (!empty($_POST['customer_name'])) {
                $customerData['customer_name'] = $_POST['customer_name'];
            }
            if (!empty($_POST['customer_email'])) {
                $customerData['customer_email'] = $_POST['customer_email'];
            }
            if (!empty($_POST['customer_phone'])) {
                $customerData['customer_phone'] = $_POST['customer_phone'];
            }
            
            $result = $this->liveChatService->createChatSession($customerData);
            
            if ($result['success']) {
                wp_send_json_success([
                    'session_id' => $result['session_id'],
                    'stage' => $result['stage'],
                    'main_categories' => $this->liveChatService->getMainCategories(),
                    'message' => 'Chat session created successfully'
                ]);
            } else {
                wp_send_json_error($result['error'], 500);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: startSession failed - " . $e->getMessage());
            wp_send_json_error('Failed to start chat session: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get sub categories for selected main category
     */
    public function getSubCategories() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            $mainCategory = $_POST['main_category'] ?? '';
            
            if (!$sessionId || !$mainCategory) {
                throw new Exception('Missing required parameters');
            }
            
            $result = $this->liveChatService->handleCategorySelection($sessionId, $mainCategory);
            
            if ($result['success']) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: getSubCategories failed - " . $e->getMessage());
            wp_send_json_error('Failed to get subcategories: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Select category and start chat if both main and sub are selected
     */
    public function selectCategory() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            $mainCategory = $_POST['main_category'] ?? '';
            $subCategory = $_POST['sub_category'] ?? '';
            
            if (!$sessionId || !$mainCategory || !$subCategory) {
                throw new Exception('Missing required parameters');
            }
            
            $result = $this->liveChatService->handleCategorySelection($sessionId, $mainCategory, $subCategory);
            
            if ($result['success']) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: selectCategory failed - " . $e->getMessage());
            wp_send_json_error('Failed to select category: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Send chat message
     */
    public function sendMessage() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            $message = $_POST['message'] ?? '';
            $senderName = $_POST['sender_name'] ?? '';
            
            if (!$sessionId || !$message) {
                throw new Exception('Missing required parameters');
            }
            
            $result = $this->liveChatService->sendMessage($sessionId, $message, 'customer', $senderName);
            
            if ($result['success']) {
                wp_send_json_success([
                    'message_id' => $result['message_id'],
                    'session_id' => $result['session_id'],
                    'timestamp' => $result['timestamp'],
                    'message' => 'Message sent successfully'
                ]);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: sendMessage failed - " . $e->getMessage());
            wp_send_json_error('Failed to send message: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Close chat session
     */
    public function closeSession() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            
            if (!$sessionId) {
                throw new Exception('Missing session ID');
            }
            
            $result = $this->liveChatService->closeChatSession($sessionId);
            
            if ($result['success']) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: closeSession failed - " . $e->getMessage());
            wp_send_json_error('Failed to close session: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Admin send message (requires admin privileges)
     */
    public function adminSendMessage() {
        try {
            // Check if user is admin
            if (!current_user_can('manage_options')) {
                throw new Exception('Unauthorized access');
            }
            
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_admin_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            $message = $_POST['message'] ?? '';
            $adminName = $_POST['admin_name'] ?? wp_get_current_user()->display_name;
            
            if (!$sessionId || !$message) {
                throw new Exception('Missing required parameters');
            }
            
            $result = $this->liveChatService->sendMessage($sessionId, $message, 'admin', $adminName);
            
            if ($result['success']) {
                wp_send_json_success([
                    'message_id' => $result['message_id'],
                    'session_id' => $result['session_id'],
                    'sender_name' => $adminName,
                    'timestamp' => $result['timestamp'],
                    'message' => 'Admin message sent successfully'
                ]);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: adminSendMessage failed - " . $e->getMessage());
            wp_send_json_error('Failed to send admin message: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get session statistics (admin only)
     */
    public function getSessionStats() {
        try {
            if (!current_user_can('manage_options')) {
                throw new Exception('Unauthorized access');
            }
            
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_admin_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            
            if (!$sessionId) {
                throw new Exception('Missing session ID');
            }
            
            $result = $this->liveChatService->getSessionStats($sessionId);
            
            if ($result['success']) {
                wp_send_json_success($result['stats']);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: getSessionStats failed - " . $e->getMessage());
            wp_send_json_error('Failed to get session stats: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get active chat sessions (admin only)
     */
    public function getActiveSessions() {
        try {
            if (!current_user_can('manage_options')) {
                throw new Exception('Unauthorized access');
            }
            
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_admin_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessions = $this->liveChatService->getActiveSessions();
            
            // Format sessions for admin interface
            $formattedSessions = array_map(function($session) {
                return [
                    'id' => $session['id'],
                    'session_id' => $session['session_id'],
                    'customer_name' => $session['customer_name'] ?? 'Anonymous',
                    'category_main' => $session['category_main'],
                    'category_sub' => $session['category_sub'],
                    'status' => $session['status'],
                    'chat_stage' => $session['chat_stage'],
                    'created_at' => $session['created_at'],
                    'updated_at' => $session['updated_at'],
                    'unread_count' => $this->liveChatService->getUnreadCount($session['session_id'], 'admin')
                ];
            }, $sessions);
            
            wp_send_json_success([
                'sessions' => $formattedSessions,
                'total' => count($formattedSessions)
            ]);
            
        } catch (Exception $e) {
            error_log("LiveChatAjax: getActiveSessions failed - " . $e->getMessage());
            wp_send_json_error('Failed to get active sessions: ' . $e->getMessage(), 500);
        }
    }
}
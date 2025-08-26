<?php

use App\Services\LiveChatService;

class LiveChatController extends BaseController {
    
    private $liveChatService;
    
    public function __construct() {
        parent::__construct();
        $this->liveChatService = new LiveChatService();
    }
    
    /**
     * Start chat session
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
            error_log("LiveChatController: startSession failed - " . $e->getMessage());
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
            error_log("LiveChatController: getSubCategories failed - " . $e->getMessage());
            wp_send_json_error('Failed to get subcategories: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Start chat after category selection
     */
    public function startChat() {
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
            error_log("LiveChatController: startChat failed - " . $e->getMessage());
            wp_send_json_error('Failed to start chat: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Send message
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
            error_log("LiveChatController: sendMessage failed - " . $e->getMessage());
            wp_send_json_error('Failed to send message: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Stream messages using Server-Sent Events (SSE)
     */
    public function streamMessages() {
        try {
            // Set execution time limit for SSE
            set_time_limit(60); // Max 60 seconds instead of unlimited
            ignore_user_abort(true); // Continue even if user aborts
            
            // Validate session
            $sessionId = $_GET['session_id'] ?? '';
            if (!$sessionId) {
                throw new Exception('Missing session ID');
            }
            
            $session = $this->liveChatService->getSession($sessionId);
            if (!$session) {
                throw new Exception('Invalid session: ' . $sessionId);
            }
            
            // Set SSE headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
            header('Access-Control-Allow-Headers: Cache-Control');
            
            // Disable output buffering for real-time streaming
            if (ob_get_level()) ob_end_clean();
            
            $lastMessageId = (int) ($_GET['since_id'] ?? 0);
            $iterations = 0;
            $maxIterations = 30; // Reduce to 30 seconds
            $heartbeatInterval = 10; // Send heartbeat every 10 iterations (10 seconds)
            
            // Send initial connection confirmation
            echo "event: connected\n";
            echo "data: ready\n\n";
            flush();
            
            // Stream messages
            while ($iterations < $maxIterations) {
                // Check if connection is still active
                if (connection_aborted()) {
                    error_log("SSE: Client disconnected, stopping stream");
                    break;
                }
                
                // Additional check with fastcgi_finish_request if available
                if (function_exists('fastcgi_finish_request')) {
                    if (connection_status() !== CONNECTION_NORMAL) {
                        error_log("SSE: Connection status abnormal, stopping stream");
                        break;
                    }
                }
                
                // Get new messages since last check
                $messages = $this->liveChatService->getMessagesFromDB($sessionId, $lastMessageId, 10);
                
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        $formattedMessage = [
                            'id' => (int) $message['id'],
                            'session_id' => $message['session_id'],
                            'sender_type' => $message['sender_type'],
                            'sender_name' => $message['sender_name'],
                            'message_type' => $message['message_type'],
                            'message' => $message['message'],
                            'metadata' => $message['metadata'] ? json_decode($message['metadata'], true) : null,
                            'is_read' => (bool) $message['is_read'],
                            'created_at' => $message['created_at']
                        ];
                        
                        echo "event: message\n";
                        echo "data: " . json_encode($formattedMessage) . "\n\n";
                        $lastMessageId = max($lastMessageId, (int) $message['id']);
                    }
                    
                    flush();
                    $iterations = 0; // Reset counter when we have activity
                } else {
                    // Only send heartbeat every 10 iterations (10 seconds)
                    if ($iterations % $heartbeatInterval === 0 && $iterations > 0) {
                        echo "event: heartbeat\n";
                        echo "data: alive\n\n";
                        flush();
                    }
                }
                
                // Sleep for 1 second before checking again
                sleep(1);
                $iterations++;
            }
            
            // Send close event before ending
            echo "event: close\n";
            echo "data: timeout\n\n";
            flush();
            
        } catch (Exception $e) {
            error_log("LiveChatController: streamMessages failed - " . $e->getMessage());
            echo "event: error\n";
            echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
            flush();
        }
        
        exit;
    }
    
    /**
     * Get chat history
     */
    public function getChatHistory() {
        try {
            if (!wp_verify_nonce($_POST['nonce'] ?? '', 'livechat_nonce')) {
                throw new Exception('Invalid security token');
            }
            
            $sessionId = $_POST['session_id'] ?? '';
            $sinceId = (int) ($_POST['since_id'] ?? 0);
            $limit = (int) ($_POST['limit'] ?? 50);
            
            if (!$sessionId) {
                throw new Exception('Missing session ID');
            }
            
            $result = $this->liveChatService->getChatMessages($sessionId, $sinceId, $limit);
            
            if ($result['success']) {
                wp_send_json_success([
                    'messages' => $result['messages'],
                    'count' => $result['count'],
                    'session_id' => $result['session_id']
                ]);
            } else {
                wp_send_json_error($result['error'], 400);
            }
            
        } catch (Exception $e) {
            error_log("LiveChatController: getChatHistory failed - " . $e->getMessage());
            wp_send_json_error('Failed to get chat history: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Close chat session
     */
    public function closeChat() {
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
            error_log("LiveChatController: closeChat failed - " . $e->getMessage());
            wp_send_json_error('Failed to close session: ' . $e->getMessage(), 500);
        }
    }
}
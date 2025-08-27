<?php
use App\Services\LiveChatService;
use BaseController;

class LiveChatController extends BaseController {
    
    private $liveChatService;
    
    public function __construct() {
        $this->liveChatService = new LiveChatService();
    }
    
    /**
     * SSE Stream endpoint for real-time messages
     * Route: chat/stream
     */
    public function streamMessages() {
        $sessionId = sanitize_text_field($_GET['session_id'] ?? '');
        
        if (empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing session_id parameter']);
            exit;
        }
        
        // Verify session exists
        try {
            $this->liveChatService->getSession($sessionId);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(['error' => 'Session not found']);
            exit;
        }
        
        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');
        header('X-Accel-Buffering: no'); // Disable nginx buffering
        
        // Disable output buffering and set no time limit
        if (ob_get_level()) ob_end_clean();
        @set_time_limit(0);
        @ini_set('max_execution_time', 0);
        
        $lastMessageId = intval($_GET['last_message_id'] ?? 0);
        
        // Send initial connection confirmation
        echo "data: " . json_encode([
            'type' => 'connected',
            'session_id' => $sessionId,
            'timestamp' => time()
        ]) . "\n\n";
        flush();
        
        $maxAttempts = 15;
        $attempt = 0;
        
        // Enable connection abort detection
        ignore_user_abort(false);
        
        while ($attempt < $maxAttempts && !connection_aborted()) {
            try {
                // Check if connection is still alive
                if (connection_aborted()) {
                    error_log("Client disconnected (abort:" . (connection_aborted() ? 'yes' : 'no') . ")");
                    break;
                }
                
                // Get new messages since last check
                $messages = $this->liveChatService->getNewMessages($sessionId, $lastMessageId);
                
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        // Check abort before each message send
                        if (connection_aborted()) {
                            error_log("Client disconnected");
                            break 2;
                        }
                        
                        $data = json_encode([
                            'id' => $message->id,
                            'sender_type' => $message->sender_type,
                            'sender_name' => $message->sender_name,
                            'message' => $message->message,
                            'message_type' => $message->message_type,
                            'created_at' => $message->created_at,
                            'timestamp' => time()
                        ]);
                        
                        echo "data: " . $data . "\n\n";
                        flush();
                        
                        // Check abort after flush for immediate detection
                        if (connection_aborted()) {
                            error_log("Client disconnected");
                            break 2;
                        }
                        
                        $lastMessageId = max($lastMessageId, $message->id);
                    }
                } else {
                    // Send heartbeat to keep connection alive (every 4 seconds)
                    echo "data: " . json_encode([
                        'type' => 'heartbeat',
                        'timestamp' => time(),
                        'last_message_id' => $lastMessageId
                    ]) . "\n\n";
                    flush();
                    
                    // Check abort after heartbeat flush
                    if (connection_aborted()) {
                        error_log("Client disconnected");
                        break;
                    }
                }

                $attempt++;

                sleep(1);
            } catch (\Exception $e) {
                echo "data: " . json_encode([
                    'type' => 'error',
                    'error' => $e->getMessage(),
                    'timestamp' => time()
                ]) . "\n\n";
                flush();
                break;
            }
        }
        
        // Send closing message
        echo "data: " . json_encode([
            'type' => 'closing',
            'reason' => 'timeout',
            'timestamp' => time()
        ]) . "\n\n";
        flush();
        
        exit;
    }
    
    /**
     * Placeholder methods for other routes (can redirect to AJAX or implement here)
     */
    public function startSession() {
        // Redirect to AJAX or implement here
        wp_redirect(admin_url('admin-ajax.php?action=livechat_start'));
        exit;
    }
    
    public function getSubCategories() {
        wp_redirect(admin_url('admin-ajax.php?action=livechat_subcategories'));
        exit;
    }
    
    public function startChat() {
        wp_redirect(admin_url('admin-ajax.php?action=livechat_begin'));
        exit;
    }
    
    public function sendMessage() {
        wp_redirect(admin_url('admin-ajax.php?action=livechat_send'));
        exit;
    }
    
    public function getChatHistory() {
        // Can implement later if needed
        echo json_encode(['message' => 'Chat history endpoint']);
        exit;
    }
    
    public function closeChat() {
        // Can implement later if needed
        echo json_encode(['message' => 'Close chat endpoint']);
        exit;
    }
}
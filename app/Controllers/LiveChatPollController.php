<?php
use App\Services\LiveChatService;

class LiveChatPollController {
    
    private $liveChatService;
    
    public function __construct() {
        $this->liveChatService = new LiveChatService();
    }
    
    /**
     * Polling endpoint - short-lived requests
     * Route: chat/poll
     */
    public function pollMessages() {
        $sessionId = sanitize_text_field($_GET['session_id'] ?? '');
        $lastMessageId = intval($_GET['last_message_id'] ?? 0);
        
        if (empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing session_id parameter']);
            exit;
        }
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $this->liveChatService->getSession($sessionId);
            $messages = $this->liveChatService->getNewMessages($sessionId, $lastMessageId);
            
            if (!empty($messages)) {
                $response = [
                    'success' => true,
                    'messages' => array_map(function($message) {
                        return [
                            'id' => $message->id,
                            'sender_type' => $message->sender_type,
                            'sender_name' => $message->sender_name,
                            'message' => $message->message,
                            'message_type' => $message->message_type,
                            'created_at' => $message->created_at
                        ];
                    }, $messages)
                ];
            } else {
                $response = [
                    'success' => true,
                    'messages' => []
                ];
            }
            
            echo json_encode($response);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        
        exit;
    }
}
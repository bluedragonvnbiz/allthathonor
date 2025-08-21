<?php

namespace App\Ajax;

class AttachmentAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
        add_action('wp_ajax_nopriv_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
    }
    
    /**
     * Get attachment ID by URL
     */
    public function getAttachmentIdByUrl() {
        // Verify nonce for security - check both product and section nonces
        $nonce_valid = wp_verify_nonce($_POST['nonce'], 'update_section_nonce') || 
                      wp_verify_nonce($_POST['nonce'], 'product_nonce');
        
        if (!$nonce_valid) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $url = sanitize_url($_POST['url']);
        
        if (!$url) {
            wp_send_json_error(['message' => 'URL is required']);
            return;
        }
        
        try {
            global $wpdb;
            
            // Get attachment ID by URL
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM $wpdb->posts WHERE guid = %s AND post_type = 'attachment'",
                $url
            ));
            
            if (!$attachment_id) {
                wp_send_json_error(['message' => 'Attachment not found']);
                return;
            }
            
            wp_send_json_success([
                'attachment_id' => (int) $attachment_id
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Initialize attachment AJAX handler
new AttachmentAjaxHandler();

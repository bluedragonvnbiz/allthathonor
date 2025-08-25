<?php

namespace App\Ajax;

class PolicyAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_update_policy_metadata', [$this, 'updatePolicyMetadata']);
        add_action('wp_ajax_delete_policy_file', [$this, 'deletePolicyFile']);
    }
    
    /**
     * Update policy metadata
     */
    public function updatePolicyMetadata() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'update_policy_metadata')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $attachmentId = intval($_POST['attachment_id']);
        $policyType = sanitize_text_field($_POST['policy_type']);
        $policyTypeKey = sanitize_text_field($_POST['policy_type_key']);
        
        if (!$attachmentId || !$policyType) {
            wp_send_json_error(['message' => 'Missing required data - ID: ' . $attachmentId . ', Type: ' . $policyType]);
            return;
        }
        
        // Check if attachment exists
        if (!get_post($attachmentId)) {
            wp_send_json_error(['message' => 'Attachment not found']);
            return;
        }
        
        // Use policy type key if provided, otherwise use the policy type name
        $metaValue = $policyTypeKey ?: $policyType;
        
        // If we have a policy type key, we need to handle default policy assignment
        if ($policyTypeKey) {
            // First, remove this policy type from ALL attachments to avoid duplicates
            $args = [
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'numberposts' => -1,
                'meta_query' => [
                    [
                        'key' => '_policy_type',
                        'value' => $metaValue,
                        'compare' => '='
                    ]
                ]
            ];
            $existing_attachments = get_posts($args);
            foreach ($existing_attachments as $existing_attachment) {
                delete_post_meta($existing_attachment->ID, '_policy_type', $metaValue);
            }
            
            // Then add it to the new attachment
            add_post_meta($attachmentId, '_policy_type', $metaValue);
        } else {
            // For custom policy types, use update_post_meta
            update_post_meta($attachmentId, '_policy_type', $metaValue);
        }
        
        // Also update the attachment title for better organization
        $updateResult = wp_update_post([
            'ID' => $attachmentId,
            'post_title' => $policyType
        ]);
        
        // Check if update was successful (wp_update_post returns post ID on success, 0 on failure)
        if ($updateResult !== 0) {
            wp_send_json_success(['message' => 'Policy metadata updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update post title']);
        }
    }
    
    /**
     * Delete policy file
     */
    public function deletePolicyFile() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'delete_policy_file')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $attachmentId = intval($_POST['attachment_id']);
        
        if (!$attachmentId) {
            wp_send_json_error(['message' => 'Missing attachment ID']);
            return;
        }
        
        // Check if attachment exists
        if (!get_post($attachmentId)) {
            wp_send_json_error(['message' => 'Attachment not found']);
            return;
        }
        
        // Delete the attachment (this will also delete the file)
        $result = wp_delete_attachment($attachmentId, true);
        
        if ($result) {
            wp_send_json_success(['message' => 'Policy file deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete policy file']);
        }
    }
}

// Initialize AJAX handler
new PolicyAjaxHandler();
<?php

namespace App\Ajax;

use App\Services\SectionService;

class SectionAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_update_section', [$this, 'updateSection']);
        add_action('wp_ajax_nopriv_update_section', [$this, 'updateSection']);
        add_action('wp_ajax_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
        add_action('wp_ajax_nopriv_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
        add_action('wp_ajax_get_edit_form', [$this, 'getEditForm']);
        add_action('wp_ajax_nopriv_get_edit_form', [$this, 'getEditForm']);
    }
    
    /**
     * Handle section update AJAX request
     */
    public function updateSection() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $fullSectionKey = sanitize_text_field($_POST['section']);
        $sectionPage = sanitize_text_field($_POST['section_page'] ?? 'main'); // Get section_page parameter
        $blockType = sanitize_text_field($_POST['block'] ?? ''); // Get block type from form
        
        // Parse section key and form type
        $parsed = $this->parseSectionKey($fullSectionKey);
        $sectionKey = $parsed['section'];
        $formType = !empty($blockType) ? $blockType : $parsed['form_type']; // Use block type if available
  
        // Get all POST data except action, nonce, section, section_page, block
        $data = [];
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['action', 'nonce', 'section', 'section_page', 'block'])) {
                // Debug: Log radio field data
                if (strpos($key, 'exposure_status') !== false) {
                    error_log("Debug - Radio field {$key}: " . print_r($value, true));
                }
                
                // Handle Unicode encoding issues
                if (is_string($value)) {
                    // First try to decode if it's JSON encoded
                    if (preg_match('/\\\\u[0-9a-fA-F]{4}/', $value)) {
                        $decoded = json_decode('"' . $value . '"');
                        if ($decoded !== null) {
                            $value = $decoded;
                        }
                    }
                    
                    // Ensure proper UTF-8 encoding
                    if (!mb_check_encoding($value, 'UTF-8')) {
                        $value = mb_convert_encoding($value, 'UTF-8', 'auto');
                    }
                }
                $data[$key] = $value;
            }
        }
        
        // Sanitize data
        $sanitizedData = $this->sanitizeSectionData($data);
        
        // Debug: Log the data before saving
        error_log('Section data before save: ' . json_encode($sanitizedData, JSON_UNESCAPED_UNICODE));
        
        try {
            $sectionService = new SectionService();
            $sectionService->updateSectionData($sectionKey, $sanitizedData, $formType, $sectionPage);
            
            wp_send_json_success([
                'message' => 'Section updated successfully',
                'section' => $sectionKey,
                'form_type' => $formType,
                'section_page' => $sectionPage
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
                'section' => $sectionKey,
                'form_type' => $formType,
                'section_page' => $sectionPage
            ]);
        }
    }
    
    /**
     * Parse section key to extract section name and form type
     * Example: "banner_section_info" -> ["section" => "banner", "form_type" => "section_info"]
     */
    private function parseSectionKey($fullSectionKey) {
        if (strpos($fullSectionKey, '_section_info') !== false) {
            return [
                'section' => str_replace('_section_info', '', $fullSectionKey),
                'form_type' => 'section_info'
            ];
        } elseif (strpos($fullSectionKey, '_content_info') !== false) {
            return [
                'section' => str_replace('_content_info', '', $fullSectionKey),
                'form_type' => 'content_info'
            ];
        }
        
        // Fallback for backward compatibility
        return [
            'section' => $fullSectionKey,
            'form_type' => 'unknown'
        ];
    }
    
    /**
     * Sanitize section data
     */
    private function sanitizeSectionData($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeSectionData($value);
            } else {
                // For Korean text, use minimal sanitization to preserve characters
                $sanitized[$key] = sanitize_text_field($value);
                
                // If it's Korean text, ensure proper encoding
                if (preg_match('/[\x{AC00}-\x{D7AF}]/u', $sanitized[$key])) {
                    $sanitized[$key] = mb_convert_encoding($sanitized[$key], 'UTF-8', 'auto');
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Get attachment ID by URL
     */
    public function getAttachmentIdByUrl() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $url = sanitize_url($_POST['url']);
        
        if (empty($url)) {
            wp_send_json_error(['message' => 'URL is required']);
            return;
        }
        
        global $wpdb;
        
        // Get attachment ID by URL - try multiple approaches
        $attachment_id = null;
        
        // Method 1: Try to get by guid
        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'",
            $url
        ));
        
        // Method 2: If not found, try to get by meta value
        if (!$attachment_id) {
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s",
                '%' . basename($url)
            ));
        }
        
        // Method 3: If still not found, try to get by filename in guid
        if (!$attachment_id) {
            $filename = basename($url);
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE guid LIKE %s AND post_type = 'attachment'",
                '%' . $filename
            ));
        }
        
        if ($attachment_id) {
            wp_send_json_success(['attachment_id' => (int)$attachment_id]);
        } else {
            wp_send_json_error(['message' => 'Attachment not found']);
        }
    }
    
    /**
     * Get edit form HTML via AJAX
     */
    public function getEditForm() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $sectionKey = sanitize_text_field($_POST['section']);
        $sectionPage = sanitize_text_field($_POST['section_page'] ?? 'main');
        $blockType = sanitize_text_field($_POST['block']); // 'section_info' hoặc 'content_info'
        
        if (empty($sectionKey) || empty($blockType)) {
            wp_send_json_error(['message' => 'Missing required parameters']);
            return;
        }
        
        try {
            // Load section config và data
            $sectionService = new SectionService();
            $sectionConfig = $sectionService->loadSectionConfig($sectionKey, $sectionPage);
            $sectionData = $sectionService->getSectionData($sectionKey, $sectionPage);
            
            // Render form block
            $renderer = new \App\Helpers\FieldRenderer();
            $formType = ($blockType === 'section_info') ? 'section' : 'content';
            $formHtml = $renderer->renderFormBlock($sectionConfig[$blockType], $sectionData, $formType, $sectionKey);
            
            wp_send_json_success([
                'html' => $formHtml,
                'section' => $sectionKey,
                'block' => $blockType,
                'section_page' => $sectionPage
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => 'Error loading form: ' . $e->getMessage(),
                'section' => $sectionKey,
                'block' => $blockType
            ]);
        }
    }
}

// Initialize AJAX handler
new SectionAjaxHandler();

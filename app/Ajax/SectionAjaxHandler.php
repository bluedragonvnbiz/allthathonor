<?php

namespace App\Ajax;

use App\Services\SectionService;

class SectionAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_update_section', [$this, 'updateSection']);
        add_action('wp_ajax_nopriv_update_section', [$this, 'updateSection']);
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
                // Handle Unicode encoding issues and fix quotes
                if (is_string($value)) {
                    // Remove unwanted slashes first (fix magic quotes issue)
                    $value = stripslashes($value);
                    
                    // Decode unicode sequences safely (preserves newlines and special chars)
                    if (preg_match('/\\\\u[0-9a-fA-F]{4}/', $value)) {
                        $value = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function($matches) {
                            $codepoint = hexdec($matches[1]);
                            return mb_chr($codepoint, 'UTF-8');
                        }, $value);
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
                // Check if this field should preserve newlines (textarea fields)
                $preserveNewlines = (
                    // Specific field names
                    in_array($key, [
                        'content_description', 
                        'section_description',
                        'description',
                        'content',
                        'notes',
                        'detail'
                    ]) ||
                    // Fields ending with _description
                    str_ends_with($key, '_description') ||
                    // Fields containing 'description'
                    strpos($key, 'description') !== false ||
                    // Fields ending with _content
                    str_ends_with($key, '_content') ||
                    // Fields ending with _notes
                    str_ends_with($key, '_notes')
                );
                
                if ($preserveNewlines) {
                    // Use sanitize_textarea_field for fields that need newlines
                    $sanitized[$key] = sanitize_textarea_field($value);
                } else {
                    // For other fields, use minimal sanitization to preserve characters
                    $sanitized[$key] = sanitize_text_field($value);
                }
                
                // If it's Korean text, ensure proper encoding
                if (preg_match('/[\x{AC00}-\x{D7AF}]/u', $sanitized[$key])) {
                    $sanitized[$key] = mb_convert_encoding($sanitized[$key], 'UTF-8', 'auto');
                }
            }
        }
        
        return $sanitized;
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
<?php

namespace App\Ajax;

use App\Services\SectionService;
use App\Services\MembershipService;

class MembershipAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_update_membership', [$this, 'updateMembership']);
        add_action('wp_ajax_nopriv_update_membership', [$this, 'updateMembership']);
        add_action('wp_ajax_update_membership_benefits', [$this, 'updateMembershipBenefits']);
        add_action('wp_ajax_nopriv_update_membership_benefits', [$this, 'updateMembershipBenefits']);
        add_action('wp_ajax_get_membership_management_form', [$this, 'getMembershipManagementForm']);
        add_action('wp_ajax_nopriv_get_membership_management_form', [$this, 'getMembershipManagementForm']);
    }
    
    /**
     * Handle membership update AJAX request
     */
    public function updateMembership() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $membershipId = sanitize_text_field($_POST['id'] ?? '');
        if (empty($membershipId)) {
            wp_send_json_error(['message' => 'Missing membership ID']);
            return;
        }
        
        try {
            $membershipService = new MembershipService();
            
            // Get current membership data first to preserve benefits
            $currentMembership = $membershipService->getMembership($membershipId);
            if (!$currentMembership) {
                wp_send_json_error(['message' => 'Membership not found']);
                return;
            }
            
            // Start with current data to preserve benefits
            $membershipData = $currentMembership;
            
            // Map section fields to membership fields
            $fieldMapping = [
                'section_membership_name' => 'membership_name',
                'section_top_phrase' => 'top_phrase',
                'section_summary_description' => 'summary_description',
                'section_sale_price' => 'sale_price',
                'section_image' => 'image',
                'section_status' => 'status',
                'section_notes' => 'notes'
            ];
            
            foreach ($fieldMapping as $postField => $membershipField) {
                if (isset($_POST[$postField])) {
                    $value = $_POST[$postField];
                    
                    // Special handling for different field types
                    if ($membershipField === 'sale_price') {
                        // Convert to float and format properly
                        $membershipData[$membershipField] = floatval($value);
                    } elseif ($membershipField === 'notes') {
                        // Allow HTML for notes field
                        $membershipData[$membershipField] = wp_kses_post($value);
                    } elseif ($membershipField === 'description') {
                        // Allow HTML for description field
                        $membershipData[$membershipField] = wp_kses_post($value);
                    } else {
                        // Regular text fields
                        $membershipData[$membershipField] = sanitize_text_field($value);
                    }
                }
            }
            
            // Handle voucher arrays if they exist
            $voucherCategories = [
                'section_travel_care_vouchers', 'section_lifestyle_vouchers', 
                'section_special_benefit_vouchers', 'section_welcome_gift_vouchers'
            ];
            
            foreach ($voucherCategories as $postField) {
                $membershipField = str_replace('section_', '', $postField);
                if (isset($_POST[$postField]) && is_array($_POST[$postField])) {
                    $membershipData[$membershipField] = $this->sanitizeVoucherArray($_POST[$postField]);
                }
            }
            
            // Handle usage guides if they exist
            $usageGuideFields = [
                'section_travel_care_usage_guide', 'section_lifestyle_usage_guide',
                'section_special_benefit_usage_guide', 'section_welcome_gift_usage_guide'
            ];
            
            foreach ($usageGuideFields as $postField) {
                $membershipField = str_replace('section_', '', $postField);
                if (isset($_POST[$postField])) {
                    $membershipData[$membershipField] = wp_kses_post($_POST[$postField]);
                }
            }
            
            // Update membership
            $result = $membershipService->updateMembership($membershipId, $membershipData);
            
            if ($result) {
                wp_send_json_success([
                    'message' => 'Membership updated successfully',
                    'membership_id' => $membershipId
                ]);
            } else {
                wp_send_json_error(['message' => 'Failed to update membership']);
            }
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => 'Error updating membership: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Sanitize voucher array data
     */
    private function sanitizeVoucherArray($voucherArray) {
        $sanitized = [];
        
        foreach ($voucherArray as $index => $voucherData) {
            if (is_array($voucherData)) {
                $sanitizedVoucher = [];
                
                // Sanitize voucher ID
                if (isset($voucherData['id'])) {
                    $sanitizedVoucher['id'] = intval($voucherData['id']);
                }
                
                // Sanitize is_summary flag
                if (isset($voucherData['is_summary'])) {
                    $sanitizedVoucher['is_summary'] = (bool) $voucherData['is_summary'];
                }
                
                // Only add if we have valid data
                if (!empty($sanitizedVoucher)) {
                    $sanitized[] = $sanitizedVoucher;
                }
            }
        }
        
        return $sanitized;
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
     * Get edit form HTML via AJAX
     */
    public function getMembershipManagementForm() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $id = sanitize_text_field($_POST['id']);
        if (empty($id)) {
            wp_send_json_error(['message' => 'Missing required parameters']);
            return;
        }
        
        try {
            $membershipService = new MembershipService();
            $card_info = $membershipService->getMembership($id);

            // Load membership field configuration
            $membershipFieldsConfig = require THEME_PATH . '/config/membership_fields.php';
            $renderer = new \App\Helpers\FieldRenderer();
            $formHtml = $renderer->renderSection($membershipFieldsConfig, sectionData: $card_info, sectionKey: 'voucher');
            
            wp_send_json_success([
                'html' => $formHtml,
                'id' => $id
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => 'Error loading form: ' . $e->getMessage(),
                'section' => $sectionKey,
                'block' => $blockType
            ]);
        }
    }
    
    /**
     * Handle membership benefits update AJAX request
     */
    public function updateMembershipBenefits() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $membershipId = sanitize_text_field($_POST['membership_id'] ?? '');
        if (empty($membershipId)) {
            wp_send_json_error(['message' => 'Missing membership ID']);
            return;
        }
        
        try {
            $membershipService = new MembershipService();
            
            // Get current membership data first
            $currentMembership = $membershipService->getMembership($membershipId);
            if (!$currentMembership) {
                wp_send_json_error(['message' => 'Membership not found']);
                return;
            }
            
            // Start with current data and update only benefits
            $benefitsData = $currentMembership;
            
            // Handle voucher categories
            $voucherCategories = [
                'travel_care', 'lifestyle', 'special_benefit', 'welcome_gift'
            ];
            
            foreach ($voucherCategories as $category) {
                $voucherField = $category . '_vouchers';
                $usageGuideField = $category . '_usage_guide';
                
                // Process voucher data (including empty arrays for clearing categories)
                if (isset($_POST[$voucherField])) {
                    $voucherData = json_decode(stripslashes($_POST[$voucherField]), true);
                    if (is_array($voucherData)) {
                        // Always set data, even if empty array (to clear category)
                        $benefitsData[$voucherField] = $this->sanitizeVoucherArray($voucherData);
                    } else {
                        // If JSON decode failed, set as empty array
                        $benefitsData[$voucherField] = [];
                    }
                }
                
                // Process usage guide
                if (isset($_POST[$usageGuideField])) {
                    $benefitsData[$usageGuideField] = wp_kses_post($_POST[$usageGuideField]);
                }
            }
            
            // Debug: Log the data being processed
            error_log('Membership Benefits Update - Membership ID: ' . $membershipId);
            error_log('Membership Benefits Update - Benefits Data: ' . print_r($benefitsData, true));
            
            // Update membership benefits
            $result = $membershipService->updateMembership($membershipId, $benefitsData);
            
            // Debug: Log the result
            error_log('Membership Benefits Update - Result: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                wp_send_json_success([
                    'message' => 'Membership benefits updated successfully',
                    'membership_id' => $membershipId,
                    'debug_data' => $benefitsData
                ]);
            } else {
                wp_send_json_error([
                    'message' => 'Failed to update membership benefits',
                    'debug_data' => $benefitsData
                ]);
            }
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => 'Error updating membership benefits: ' . $e->getMessage()
            ]);
        }
    }
}

// Initialize AJAX handler
new MembershipAjaxHandler();
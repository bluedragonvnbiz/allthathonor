<?php

namespace App\Ajax;

use App\Services\VoucherService;

class VoucherAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_create_voucher', [$this, 'createVoucher']);
        add_action('wp_ajax_nopriv_create_voucher', [$this, 'createVoucher']);
        add_action('wp_ajax_update_voucher', [$this, 'updateVoucher']);
        add_action('wp_ajax_nopriv_update_voucher', [$this, 'updateVoucher']);
        add_action('wp_ajax_delete_voucher', [$this, 'deleteVoucher']);
        add_action('wp_ajax_nopriv_delete_voucher', [$this, 'deleteVoucher']);
        add_action('wp_ajax_get_voucher', [$this, 'getVoucher']);
        add_action('wp_ajax_nopriv_get_voucher', [$this, 'getVoucher']);
    }
    
    /**
     * Handle voucher creation AJAX request
     */
    public function createVoucher() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        try {
            $voucherService = new VoucherService();
            
            // Sanitize data
            $data = $this->sanitizeVoucherData($_POST);
            
            // Create voucher
            $voucherId = $voucherService->createVoucher($data);
            
            if (!$voucherId) {
                wp_send_json_error(['message' => 'Failed to create voucher']);
                return;
            }
            
            wp_send_json_success([
                'message' => 'Voucher created successfully',
                'voucher_id' => $voucherId
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle voucher update AJAX request
     */
    public function updateVoucher() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $voucherId = (int) $_POST['voucher_id'];
        
        if (!$voucherId) {
            wp_send_json_error(['message' => 'Voucher ID is required']);
            return;
        }
        
        try {
            $voucherService = new VoucherService();
            
            // Sanitize data
            $data = $this->sanitizeVoucherData($_POST);
            
            // Update voucher
            $result = $voucherService->updateVoucher($voucherId, $data);
            
            if (!$result) {
                wp_send_json_error(['message' => 'Failed to update voucher']);
                return;
            }
            
            wp_send_json_success([
                'message' => 'Voucher updated successfully',
                'voucher_id' => $voucherId
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle voucher deletion AJAX request
     */
    public function deleteVoucher() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'voucher_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $voucherId = (int) $_POST['voucher_id'];
        
        if (!$voucherId) {
            wp_send_json_error(['message' => 'Voucher ID is required']);
            return;
        }
        
        try {
            $voucherService = new VoucherService();
            $voucherService->deleteVoucher($voucherId);
            
            wp_send_json_success([
                'message' => 'Voucher deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle voucher retrieval AJAX request
     */
    public function getVoucher() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'voucher_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $voucherId = (int) $_POST['voucher_id'];
        
        if (!$voucherId) {
            wp_send_json_error(['message' => 'Voucher ID is required']);
            return;
        }
        
        try {
            $voucherService = new VoucherService();
            $voucher = $voucherService->getVoucher($voucherId);
            
            if (!$voucher) {
                wp_send_json_error(['message' => 'Voucher not found']);
                return;
            }
            
            wp_send_json_success([
                'voucher' => $voucher
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    

    
    /**
     * Sanitize voucher data
     */
    private function sanitizeVoucherData($data) {
        $sanitized = [];
        
        // Handle type (multi-select)
        if (isset($data['type'])) {
            if (is_array($data['type'])) {
                $sanitized['type'] = array_map('sanitize_text_field', $data['type']);
            } else {
                $sanitized['type'] = sanitize_text_field($data['type']);
            }
        }
        
        // Handle status
        if (isset($data['status'])) {
            $sanitized['status'] = sanitize_text_field($data['status']);
        }
        
        // Handle grade (multi-select) - map to category
        if (isset($data['category'])) {
            if (is_array($data['category'])) {
                $sanitized['category'] = array_map('sanitize_text_field', $data['category']);
            } else {
                $sanitized['category'] = sanitize_text_field($data['category']);
            }
        }
        
        // Handle representative_image - map to image
        if (isset($data['image'])) {
            $sanitized['image'] = sanitize_url($data['image']);
        }
        
        // Handle voucher_name - map to name
        if (isset($data['name'])) {
            $sanitized['name'] = sanitize_text_field($data['name']);
        }
        
        // Handle summary_description - map to short_description
        if (isset($data['short_description'])) {
            $sanitized['short_description'] = sanitize_textarea_field(stripslashes($data['short_description']));
        }
        
        // Handle detailed_description - map to detail_description
        if (isset($data['detail_description'])) {
            $sanitized['detail_description'] = wp_kses_post(stripslashes($data['detail_description']));
        }
        
        return $sanitized;
    }
}

// Initialize voucher AJAX handler
new VoucherAjaxHandler();

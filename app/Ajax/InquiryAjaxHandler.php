<?php

namespace App\Ajax;

use App\Database\InquiryDatabase;
use App\Services\EmailService;

class InquiryAjaxHandler {
    
    public function __construct() {
        error_log('InquiryAjaxHandler constructor called');
        add_action('wp_ajax_submit_inquiry', [$this, 'submitInquiry']);
        add_action('wp_ajax_nopriv_submit_inquiry', [$this, 'submitInquiry']);
        add_action('wp_ajax_get_inquiry_categories', [$this, 'getInquiryCategories']);
        add_action('wp_ajax_nopriv_get_inquiry_categories', [$this, 'getInquiryCategories']);
        add_action('wp_ajax_submit_inquiry_answer', [$this, 'submitInquiryAnswer']);
        error_log('InquiryAjaxHandler actions registered');
    }
    
    /**
     * Handle inquiry form submission AJAX request
     */
    public function submitInquiry() {
        error_log('submitInquiry method called');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'inquiry_form_nonce')) {
            error_log('Nonce verification failed');
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        try {
            // Validate required fields
            $required_fields = [
                'corporate_name' => '법인명',
                'contact_person' => '담당자 성함', 
                'contact_phone' => '담당자 연락처',
                'email' => '담당자 이메일',
                'inquiry_content' => '문의 내용',
                'category_main' => '문의 유형 (메인)',
                'category_sub' => '문의 유형 (서브)'
            ];
            
            $errors = [];
            $data = [];
            
            foreach ($required_fields as $field => $label) {
                if (empty($_POST[$field])) {
                    $errors[] = $label . '은(는) 필수 입력 항목입니다.';
                } else {
                    $data[$field] = sanitize_text_field($_POST[$field]);
                }
            }
            
            // Validate email format
            if (!empty($data['email']) && !is_email($data['email'])) {
                $errors[] = '올바른 이메일 형식을 입력해주세요.';
            }
            
            // Validate phone format (flexible international phone validation)
            if (!empty($data['contact_phone'])) {
                $phone = $data['contact_phone'];
                
                // Remove all non-digit characters except +, -, space, and parentheses
                $cleanPhone = preg_replace('/[^\d\-\+\s\(\)]/', '', $phone);
                
                // Basic phone validation - should have at least 7 digits
                $digitsOnly = preg_replace('/[^\d]/', '', $cleanPhone);
                
                // Check if it's a valid phone number (7-15 digits is typical for international numbers)
                if (strlen($digitsOnly) < 7 || strlen($digitsOnly) > 15) {
                    $errors[] = '올바른 전화번호를 입력해주세요. (최소 7자리 이상)';
                }
            }
            
            // If there are errors, return them
            if (!empty($errors)) {
                wp_send_json_error([
                    'message' => '입력 정보를 확인해주세요.',
                    'errors' => $errors
                ]);
                return;
            }
            
            // Generate inquiry number
            $data['inquiry_number'] = $this->generateInquiryNumber();
            
            // Set default values
            $data['status'] = 'unanswered';
            $data['registration_date'] = current_time('mysql');
            
            // Sanitize content
            $data['inquiry_content'] = sanitize_textarea_field($_POST['inquiry_content']);
            
            // Insert into database
            global $wpdb;
            $table_name = InquiryDatabase::getTableName();
            
            $result = $wpdb->insert($table_name, $data);
            
            if ($result === false) {
                wp_send_json_error([
                    'message' => '문의 등록에 실패했습니다. 다시 시도해주세요.',
                    'error' => $wpdb->last_error
                ]);
                return;
            }
            
            // Send success response
            wp_send_json_success([
                'message' => '문의가 성공적으로 등록되었습니다.',
                'inquiry_number' => $data['inquiry_number']
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => '문의 등록 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get inquiry categories for dropdown
     */
    public function getInquiryCategories() {
        $categories = [
            '멤버십' => [
                '이용권 사용방법' => '이용권 사용방법',
                '멤버십 혜택' => '멤버십 혜택',
                '멤버십 가입' => '멤버십 가입',
                '멤버십 해지' => '멤버십 해지',
                '기타' => '기타'
            ],
            '상품' => [
                '상품 문의' => '상품 문의',
                '상품 구매' => '상품 구매',
                '상품 환불' => '상품 환불',
                '기타' => '기타'
            ],
            '서비스' => [
                '서비스 이용' => '서비스 이용',
                '서비스 문의' => '서비스 문의',
                '기타' => '기타'
            ],
            '기타' => [
                '일반 문의' => '일반 문의',
                '불만 사항' => '불만 사항',
                '제안 사항' => '제안 사항',
                '기타' => '기타'
            ]
        ];
        
        wp_send_json_success($categories);
    }
    
    /**
     * Generate unique inquiry number
     */
    private function generateInquiryNumber(): string {
        global $wpdb;
        $table_name = InquiryDatabase::getTableName();
        
        do {
            $number = 'QN' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE inquiry_number = %s",
                $number
            ));
        } while ($exists > 0);
        
        return $number;
    }

    /**
     * Handle inquiry answer submission AJAX request
     */
    public function submitInquiryAnswer() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['inquiry_answer_nonce'], 'submit_inquiry_answer')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        // Check permissions (only admin can submit answers)
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        try {
            // Validate required fields
            $inquiryId = intval($_POST['inquiry_id']);
            $answerContent = wp_kses_post($_POST['answer_content']);

            if (empty($inquiryId) || empty($answerContent)) {
                wp_send_json_error(['message' => 'Missing required fields']);
                return;
            }

            // Update inquiry with answer
            global $wpdb;
            $table_name = InquiryDatabase::getTableName();
            
            $result = $wpdb->update(
                $table_name,
                [
                    'answer_content' => $answerContent,
                    'status' => 'answered',
                    'answer_date' => current_time('mysql')
                ],
                ['id' => $inquiryId],
                ['%s', '%s', '%s'],
                ['%d']
            );

            if ($result === false) {
                wp_send_json_error([
                    'message' => '답변 저장에 실패했습니다.',
                    'error' => $wpdb->last_error
                ]);
                return;
            }

            // Get updated inquiry data for email
            $inquiryData = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $inquiryId
            ), ARRAY_A);

            // Send email notification if inquiry data exists
            if ($inquiryData) {
                try {
                    $emailService = new EmailService();
                    $emailSent = $emailService->sendInquiryAnswerNotification($inquiryData);
                    
                    if ($emailSent) {
                        error_log("Answer notification email sent successfully for inquiry ID: $inquiryId");
                    } else {
                        error_log("Failed to send answer notification email for inquiry ID: $inquiryId");
                    }
                } catch (\Exception $e) {
                    error_log("Email service error for inquiry ID $inquiryId: " . $e->getMessage());
                }
            }

            // Send success response
            wp_send_json_success([
                'message' => '답변이 성공적으로 저장되었으며 고객에게 알림 메일이 발송되었습니다.'
            ]);

        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => '답변 저장 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ]);
        }
    }
}

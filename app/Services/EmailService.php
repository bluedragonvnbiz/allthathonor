<?php

namespace App\Services;

/**
 * Email Service for sending SMTP emails
 * Handles SMTP configuration and email sending
 */
class EmailService {
    
    private $smtp_host = 'smtp.gmail.com';
    private $smtp_port = 587;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Get SMTP credentials from WordPress options or wp-config constants
        $this->smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : get_option('smtp_username');
        $this->smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : get_option('smtp_password');
        $this->from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : get_option('smtp_from_email', $this->smtp_username);
        $this->from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : get_option('smtp_from_name', 'All That Honors Club');
        
        // Configure WordPress to use SMTP
        add_action('phpmailer_init', [$this, 'configureSmtp']);
        add_filter('wp_mail_from', [$this, 'setFromEmail']);
        add_filter('wp_mail_from_name', [$this, 'setFromName']);
    }
    
    /**
     * Configure PHPMailer to use SMTP
     */
    public function configureSmtp($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $this->smtp_host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $this->smtp_port;
        $phpmailer->Username = $this->smtp_username;
        $phpmailer->Password = $this->smtp_password;
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->CharSet = 'UTF-8';
    }
    
    /**
     * Set from email address
     */
    public function setFromEmail($email) {
        return $this->from_email;
    }
    
    /**
     * Set from name
     */
    public function setFromName($name) {
        return $this->from_name;
    }
    
    /**
     * Send inquiry answer notification email
     */
    public function sendInquiryAnswerNotification($inquiryData) {
        $to = $inquiryData['email'];
        $subject = '[All That Honors Club] 문의 답변이 등록되었습니다 - ' . $inquiryData['inquiry_number'];
        
        $message = $this->getInquiryAnswerEmailTemplate($inquiryData);
        
        $headers = [
            'Content-Type: text/html; charset=UTF-8'
        ];
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        if (!$sent) {
            error_log('Failed to send inquiry answer notification email to: ' . $to);
            return false;
        }
        
        error_log('Inquiry answer notification email sent successfully to: ' . $to);
        return true;
    }
    
    /**
     * Get email template for inquiry answer notification
     */
    private function getInquiryAnswerEmailTemplate($inquiryData) {
        $template = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: "Malgun Gothic", Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #1C1C1C; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background-color: #f9f9f9; }
                .inquiry-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #1C1C1C; }
                .answer-box { background: #e8f4f8; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .btn { background-color: #1C1C1C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                h3 { color: #1C1C1C; margin-bottom: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>All That Honors Club</h2>
                    <p>문의 답변 알림</p>
                </div>
                
                <div class="content">
                    <p><strong>' . esc_html($inquiryData['contact_person']) . '</strong>님, 안녕하세요!</p>
                    <p>고객님의 문의에 대한 답변이 등록되었습니다.</p>
                    
                    <div class="inquiry-box">
                        <h3>📝 문의 내용</h3>
                        <p><strong>문의번호:</strong> ' . esc_html($inquiryData['inquiry_number']) . '</p>
                        <p><strong>문의유형:</strong> ' . esc_html($inquiryData['category_main']) . ' > ' . esc_html($inquiryData['category_sub']) . '</p>
                        <p><strong>문의일시:</strong> ' . date('Y년 m월 d일 H:i', strtotime($inquiryData['registration_date'])) . '</p>
                        <p><strong>문의내용:</strong></p>
                        <p>' . nl2br(esc_html($inquiryData['inquiry_content'])) . '</p>
                    </div>
                    
                    <div class="answer-box">
                        <h3>💬 답변 내용</h3>
                        <p><strong>답변일시:</strong> ' . date('Y년 m월 d일 H:i', strtotime($inquiryData['answer_date'])) . '</p>
                        <div>' . wp_kses_post($inquiryData['answer_content']) . '</div>
                    </div>
                    
                    <p>추가 문의사항이 있으시면 언제든지 연락주세요.</p>
                    <p>감사합니다.</p>
                </div>
                
                <div class="footer">
                    <p>All That Honors Club<br>
                    이 메일은 발신 전용입니다. 회신하지 마세요.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $template;
    }
    
    /**
     * Test email functionality
     */
    public function sendTestEmail($to, $subject = 'Test Email') {
        $message = '<h3>이메일 설정 테스트</h3><p>SMTP 설정이 정상적으로 작동하고 있습니다.</p>';
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        return wp_mail($to, $subject, $message, $headers);
    }
}
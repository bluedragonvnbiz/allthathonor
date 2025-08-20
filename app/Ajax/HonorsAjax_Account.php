<?php
/**
 * AJAX: Account domain (login, register, etc.)
 */
class HonorsAjax_Account {
    public function __construct() {
        add_action('wp_ajax_nopriv_honors_account_login', [$this, 'login']);
        add_action('wp_ajax_honors_account_login', [$this, 'login']);
    }

    /**
     * Handle AJAX login
     */
    public function login() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'honors_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
        }

        $username = isset($_POST['log']) ? sanitize_text_field($_POST['log']) : '';
        $password = isset($_POST['pwd']) ? (string) $_POST['pwd'] : '';

        if (empty($username) || empty($password)) {
            wp_send_json_error(['message' => '이메일과 비밀번호를 입력해주세요.']);
        }

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
            switch ($user->get_error_code()) {
                case 'invalid_username':
                    $error_message = '존재하지 않는 이메일입니다.';
                    break;
                case 'incorrect_password':
                    $error_message = '비밀번호가 올바르지 않습니다.';
                    break;
                case 'empty_username':
                    $error_message = '이메일을 입력해주세요.';
                    break;
                case 'empty_password':
                    $error_message = '비밀번호를 입력해주세요.';
                    break;
                default:
                    $error_message = '로그인에 실패했습니다. 다시 시도해주세요.';
            }
            wp_send_json_error(['message' => $error_message]);
        }

        // Login success
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        // Role-based redirect
        if (user_can($user, 'manage_options')) {
            $redirect_url = home_url('/management');
        } else {
            $redirect_url = apply_filters('honors_member_dashboard_url', home_url('/member'), $user);
        }

        wp_send_json_success([
            'message' => '로그인 성공!',
            'redirect_url' => $redirect_url,
            'user_id' => $user->ID,
            'user_name' => $user->display_name,
        ]);
    }
}



<?php
// Redirect already logged-in users away from the login page
if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'manage_options' ) ) {
		wp_safe_redirect( home_url( '/management' ) );
		exit;
	}
	$member_dashboard_url = apply_filters( 'honors_member_dashboard_url', home_url( '/member' ), $current_user );
	wp_safe_redirect( $member_dashboard_url );
	exit;
}
?>
<div class="login-page-wrapper">
    <div class="login-form-container">
        <div class="logo-section">
            <div class="logo">
                <img src="<?php echo THEME_URL; ?>/assets/images/logo-black.svg" alt="HONORS CLUB" class="logo-image">
            </div>
        </div>
        
        <form class="login-form" id="login-form" method="post">
            <div class="form-group">
                <label for="user_login">이메일</label>
                <div class="input-wrapper">
                    <i class="icon-envelope"></i>
                    <input type="text" name="log" id="user_login" placeholder="이메일을 입력해주세요." required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="user_pass">비밀번호</label>
                <div class="input-wrapper">
                    <i class="icon-lock"></i>
                    <input type="password" name="pwd" id="user_pass" placeholder="비밀번호를 입력해주세요." required>
                </div>
            </div>
            
            <!-- Error/Success Messages -->
            <div class="form-messages" style="display: none;">
                <div class="error-message" id="error-message" style="display: none;"></div>
                <div class="success-message" id="success-message" style="display: none;"></div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="login-btn" id="login-btn">로그인</button>
            </div>
        </form>
    </div>
</div> 
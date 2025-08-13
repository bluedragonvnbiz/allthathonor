<?php
add_action( 'login_header', 'bj_login_header' );
function bj_login_header() { ?>
<div class="bj-login-wrap">
    <div class="bj-login-col-left">
        <h3>Welcome to the <br/> Stylevook Manager</h3>
        <p>We are the best and biggest social network with 5 billion active users all around the world. Share you thoughts, write blog posts, show your favourite music via Stopify, earn badges and much more!</p>
    </div>
<?php
}

add_action( 'login_footer', 'bj_login_footer' );
function bj_login_footer() { 
    echo "</div>";//end wrap
    if(isset($_GET["ver"]) && $_GET['ver'] == "app"){ ?>
        <script type="text/javascript">
            document.getElementById("loginform").setAttribute("action", "javascript:void(0)");
        </script>
<?php
    }
}

add_filter('login_title', 'custom_login_title', 99);
function custom_login_title($origtitle) { 

    return "Login - ".get_bloginfo('name');

}

//add_action( 'login_form', 'bj_login_form', 1, 10 );
function bj_login_form() { ?>
    <div class="login-custom-box">
        <p><span>OR</span></p>
        <div class="list-button">
            <a href="#" class="google">Login with Google</a>
        </div>
    </div>    
<?php       
}

function custom_login_message() {
$message = '<h3>Login to your Account</h3>';
return $message;
}
add_filter('login_message', 'custom_login_message');

add_filter( 'login_headerurl', 'my_custom_login_url' );
function my_custom_login_url($url) {
    return get_site_url();
}


add_filter(  'gettext',  'register_text'  );
function register_text( $translating ) {
    $translated = str_ireplace(  'Username or Email Address',  'Email Address',  $translating );
    return $translated;
}

function vpsb_disable_lost_password() {
    if (isset( $_GET['action'] )){
        if ( in_array( $_GET['action'], array('lostpassword', 'retrievepassword') ) ) {
            wp_redirect( wp_login_url(), 301 );
            exit;
        }
    }
}
add_action( "login_init", "vpsb_disable_lost_password" );


add_action('wp_ajax_nopriv_app_login_form', 'app_login_form');
function app_login_form() {
    
    //if( !isset( $_POST['token']) or !wp_verify_nonce($_POST['token'],BJ_NONCE_KEY."login"  )) die(json_encode(array("error")));

    $email = isset($_POST["email"]) ? strip_tags($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? strip_tags($_POST["password"]) : "";
    $check = wp_authenticate_email_password("null",$email,$password);

    if(is_email($email) && email_exists( $email ) && !is_wp_error($check)){
        $id_new_user = get_user_by( 'email', $email )->ID;
        if(is_customer($id_new_user)){
            $status = get_user_meta($id_new_user,"user_status",true);
            if($status != "active" && $status != ""){
                echo json_encode(array("false",alert_notice("danger","Your account is locked,<br>Please login with another account to continue")));
            }else{
                wp_clear_auth_cookie();
                $user = wp_set_current_user($id_new_user);
                wp_set_auth_cookie($id_new_user,true,is_ssl());
                do_action('wp_login', $user->user_login, $user);

                echo json_encode(["success",$id_new_user."|||".bj_render_nonce($id_new_user)]) ; 
            }
             
        }else{
             echo json_encode(array("false",alert_notice("danger","Incorrect email or password"))); 
        } 
           
    }else{
        echo json_encode(array("false",alert_notice("danger","Incorrect email or password"))); 
    }
    
    die();
}

add_action('wp_ajax_nopriv_app_user_register', 'app_user_register');
function app_user_register() {
    $data = array();
    parse_str($_POST["data"], $data);
    //if( !isset( $data['token']) or !wp_verify_nonce($data['token'],BJ_NONCE_KEY."register"  )) die(json_encode(array("error")));
    $message = "";

    if(!is_email($data["email"]) or empty($data["email"])) {
        $message = alert_notice("warning","Invalid email");
    }

    if( empty($data["password"]) or empty($data["c_password"])  ){
        $message = alert_notice("warning","Error");
    }

    if(email_exists($data["email"]) ){
        $user = get_user_by( 'email', $data["email"] );
        $userId = $user->ID;
        if(is_customer($userId)){
            $message = alert_notice("warning","Email already exists");
        }else{
            $message = alert_notice("warning","Email already registered to StyleVook Bussiness, Please, use another");
        }
    }

    if( strlen($data["password"]) < 8 ){
        $message = alert_notice("warning","Password must be more than 7 characters");
    }

    if(strlen($data["password"]) > 60 or strlen($data["email"]) > 60 ){
        $message = alert_notice("warning","Error characters length");
    }

    if( $data["password"] != $data["c_password"] ){
        $message = alert_notice("warning","Confirm password is incorrect");
    }


    if($message == ""){
        $data_new_user = array(
          'user_login'           => $data["email"], 
          'user_pass'            => $data["password"], 
          'display_name'    => $data["email"],
          'user_email'  => $data["email"],
          'role'  => CUSTOMER,
        );
        $user_id = wp_insert_user( $data_new_user ); 
        if ( ! is_wp_error( $user_id ) ) {
            $status = "success";$message = '<div class="alert alert-success border-0 mb-2" role="alert">'.__("Sign up success.","stylevook").'<a href="#" data-bs-target="#login" data-bs-toggle="modal" class="alert-link ms-1 ml-1">'.__("Log in now.","stylevook").'</a></div>';
        }
    }else{
        $status = "false";
    }
    die(json_encode(array($status,$message)));
}

add_action('wp_ajax_app_shopmanager_register', 'app_shopmanager_register');
add_action('wp_ajax_nopriv_app_shopmanager_register', 'app_shopmanager_register');
function app_shopmanager_register() {
    $data = array();
    parse_str($_POST["data"], $data);   
    //if( !isset( $data['token']) or !wp_verify_nonce($data['token'],BJ_NONCE_KEY."shopregister"  )) die(json_encode(array("error")));
    $message = "";

    $lat = !empty($data["lat"]) ? strip_tags($data["lat"]) : "";
    $long = !empty($data["long"]) ? strip_tags($data["long"]) : "";
    if(empty($data["store_name"]) or strlen($data["store_name"]) > 250){
        $message = alert_notice("warning","Invalid store name");
        goto end_label;
    }

    if(empty($data["address"]) or strlen($data["address"]) > 250){
        $message = alert_notice("warning","Invalid address");
        goto end_label;
    }

    if(empty($data["owner"]) or strlen($data["owner"]) > 250){
        $message = alert_notice("warning","Invalid owner name");
        goto end_label;
    }

    if(empty($data["phone"]) or !is_numeric($data["phone"]) or !is_phone_number($data["phone"])){
        $message = alert_notice("warning","Invalid phone number");
        goto end_label;
    }

    if(empty($data["service"]) or !is_array($data["service"])){
        $message = alert_notice("warning","Please select service");
        goto end_label;
    }
    if(!is_email($data["email"]) or empty($data["email"])) {
        $message = alert_notice("warning","Invalid email");
        goto end_label;
    }

    if( empty($data["password"]) or empty($data["c_password"])  ){
        $message = alert_notice("warning","Error");
        goto end_label;
    }

    if(email_exists($data["email"]) ){
        $user = get_user_by( 'email', $data["email"] );
        $userId = $user->ID;
        if(is_shopmanager($userId)){
            $message = alert_notice("warning","Email already exists");
        }else{
            $message = alert_notice("warning","Email already registered to StyleVook, Please, use another");
        }
        
    }

    if( strlen($data["password"]) < 8 ){
        $message = alert_notice("warning","Password must be more than 7 characters");
        goto end_label;
    }

    if(strlen($data["password"]) > 60 or strlen($data["email"]) > 60 ){
        $message = alert_notice("warning","Error characters length");
        goto end_label;
    }

    if( $data["password"] != $data["c_password"] ){
        $message = alert_notice("warning","Confirm password is incorrect");
        goto end_label;
    }

    end_label:

    $status = "false";
    if($message == ""){
        $data_new_user = array(
          'user_login'           => $data["email"], 
          'user_pass'            => $data["password"], 
          'display_name'    => $data["email"],
          'user_email'  => $data["email"],
          'role'  => SHOPMANAGER,
        );
        $user_id = wp_insert_user( $data_new_user ); 
        if ( ! is_wp_error( $user_id ) ) {
            

            $code = bj_render_nonce($data["email"].rand(1,10));
            $result  = update_user_meta($user_id,"active_token",$code );
            if($result){
                $obj = new PV_controller;
                $result = $obj->Model("store","admin")->add_store_information(
                    array(
                        "category_id" => json_encode($data["service"]),
                        "user_id" => $user_id,
                        "store_name" => $data["store_name"],
                        "address" => $data["address"],
                        "phone" => $data["phone"],
                        "lat_" => $lat,
                        "long_" => $long,
                        "time_" => "08:00||21:00",
                        "date_created" => current_time("mysql"),
                    ),
                    array("%s","%d","%s","%s","%s","%s","%s","%s","%s")
                );
                if($result > 0){
                    $to = $data["email"];
                    $subject = 'Đăng ký tài khoản StyleVook Bussiness';
                    $body = file_get_contents(get_template_directory()."/template/email/store-register.html");
                    $body = str_replace(array('{{email}}', '{{code}}'), array($data["email"], $code), $body);
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                     
                    wp_mail( $to, $subject, $body, $headers );
                    $status = "success";
                }
            }          
            
        }
    }
    die(json_encode(array($status,$message)));
}

add_action('wp_ajax_app_shopmanager_login_form', 'app_shopmanager_login_form');
add_action('wp_ajax_nopriv_app_shopmanager_login_form', 'app_shopmanager_login_form');
function app_shopmanager_login_form() {
   
    $headers = getallheaders();
    $is_mobile = wp_is_mobile();
    //echo "<pre>";print_r($headers);echo "</pre>";
    if($is_mobile){
        
        // if( !isset( $_POST['token']) or !bj_verify_nonce($_POST['token'],BJ_NONCE_KEY.$headers["app_device_id"])) {
        //     die(json_encode(array("false",alert_notice("danger",__("Token hết hạn, hãy tắt app và mở lại","stylevook")))));
        // }
    }else{
        if( !isset( $_POST['token']) or  !wp_verify_nonce($_POST['token'],BJ_NONCE_KEY."shoplogin"  )) {
            die(json_encode(array("false",alert_notice("danger","Token hết hạn, vui lòng reload lại để tiếp tục"))));
        }
       
    }
    

    $email = isset($_POST["email"]) ? strip_tags($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? strip_tags($_POST["password"]) : "";
    $check = wp_authenticate_email_password("null",$email,$password);

    if(is_email($email) && email_exists( $email ) && !is_wp_error($check)){
        $id_new_user = get_user_by( 'email', $email )->ID;
        if(is_shopmanager($id_new_user)){
            $obj = new PV_controller;
            $store_detail = $obj->Model("store","admin")->get_store_detail_by_user_id($id_new_user);
            if($store_detail["parent_id"] != 0 && !$is_mobile ){
                 echo json_encode(array("false",alert_notice("warning",__("This account can't log in because it's not authorized.","stylevook")))); 
                 die();
            }
            if(!empty($store_detail) && !empty($store_detail["status"]) ){
                if(in_array($store_detail["status"],["active","verified"])){
                    wp_clear_auth_cookie();
                    $user = wp_set_current_user($id_new_user);
                    wp_set_auth_cookie($id_new_user,true,is_ssl());
                    do_action('wp_login', $user->user_login, $user);
                    echo json_encode(array("success",$id_new_user."|||".bj_render_nonce($id_new_user))) ;
                }elseif($store_detail["status"] == "pending"){
                    echo json_encode(array("false",alert_notice("warning",__("Please check your email to activate your account.","stylevook")))); 
                }else{
                    echo json_encode(array("false",alert_notice("warning",__("Account is locked, please contact support for help.","stylevook")))); 
                }
                
            }else{
                echo json_encode(array("false",alert_notice("warning",__("Please check your email to activate your account.","stylevook")))); 
            }     
        }else{
            echo json_encode(array("false",alert_notice("danger",__("Incorrect email or password","stylevook"))));
        }
        
    }else{
        echo json_encode(array("false",alert_notice("danger",__("Incorrect email or password","stylevook")))); 
    }
    
    die();
}

add_action('wp_ajax_app_logout', 'app_logout');
function app_logout() {
    $current_user_id = current_user_id;
    if( !isset( $_POST['token']) or !wp_verify_nonce($_POST['token'],BJ_NONCE_KEY."logout".$current_user_id )) die("error");
    if (isset($_COOKIE['IS_LOGIN_BY_SN'])) {
        unset($_COOKIE['IS_LOGIN_BY_SN']);
        setcookie('IS_LOGIN_BY_SN', '', time() - 3600, '/'); 
    }
    if (isset($_COOKIE['BJ_USER_ID'])) {
        unset($_COOKIE['BJ_USER_ID']);
        setcookie('BJ_USER_ID', '', time() - 3600, '/'); 
    }if (isset($_COOKIE['BJ_LOGIN_TOKEN'])) {
        unset($_COOKIE['BJ_LOGIN_TOKEN']);
        setcookie('BJ_LOGIN_TOKEN', '', time() - 3600, '/'); 
    }
    if (isset($_COOKIE['BJ_ADMIN_ID'])) {
        unset($_COOKIE['BJ_ADMIN_ID']);
        setcookie('BJ_ADMIN_ID', '', time() - 3600, '/'); 
    }
    if (isset($_COOKIE['BJ_ADMIN_TOKEN'])) {
        unset($_COOKIE['BJ_ADMIN_TOKEN']);
        setcookie('BJ_ADMIN_TOKEN', '', time() - 3600, '/'); 
    }
    global $wpdb;
    $table = $wpdb->prefix."bj_user_push_token";  
    $wpdb->update($table , ["token" => ""], ["user_id" => $current_user_id],["%s"],["%d"]);
    wp_logout();
    echo "success";

    die();
}




add_action('wp_ajax_nopriv_app_social_login', 'app_social_login');
//add_action('wp_ajax_app_social_login', 'app_social_login');
function app_social_login() {

    //if( !isset( $_POST['web_token']) or !wp_verify_nonce($_POST['web_token'],BJ_NONCE_KEY."login" )) die("Invalid Web Token");
    $type = isset($_POST["type"]) ? $_POST['type'] : "";
    $accessToken = $_POST['token'];
    if($type == "F"){
        $facebook_inc = $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/stylevook/inc/login/Facebook/autoload.php";
        require_once( $facebook_inc );
        
        $fb = new Facebook\Facebook([
          'app_id' => "245162634384012", 
          'app_secret' => "ae8a966d7bedf5cdec644f1693f3f751",
          'default_graph_version' => 'v3.2',
          ]);

        $helper = $fb->getRedirectLoginHelper();
        try {
        
        $response = $fb->get('/me?fields=id,name,email', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
        }
        
        if (! isset($accessToken)) {
        if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
        }
        exit;
        }
        // Logged in
        $me = $response->getGraphUser();

        if (isset($me['email']) && !is_null($me['email'])) {
            app_social_process_user_login($me['email'], $me['name']);
        }elseif((!isset($me['email']) or is_null($me['email'])) && isset($me["id"])){
            $email = $me["id"]."_facebook@stylevook.vn";
            app_social_process_user_login($email, $me['name']);
        }else{
            die(" Can't login because of missing parameters ");
        }

        ///////end F
    }elseif($type == "K") {


        $access_token   =  $accessToken;
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://kapi.kakao.com/v2/user/me");
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$access_token,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);
        $server_output =  json_decode($server_output, true);

        curl_close ($ch);

        $kakao_id           = isset($server_output['id']) ? $server_output['id'] : "";
        $kakao_nickname     = isset($server_output['properties']['nickname']) ? $server_output['properties']['nickname'] : "";
        $kakao_email        = isset($server_output['kakao_account']['email']) ? $server_output['kakao_account']['email'] : "";

        if( $kakao_email == "" or $kakao_nickname == "" ){
            die(" Can't login because of missing parameters ");
        }else{
            app_social_process_user_login($kakao_email, $kakao_nickname );
        }

        ///////////end K
    }elseif($type == "G") {
        $request_url = 'https://oauth2.googleapis.com/tokeninfo?id_token='.$accessToken;
        $response_access = file_get_contents($request_url);
        $response_access = json_decode( $response_access, true );
        $email =  isset($response_access["email"]) ? $response_access["email"] : "";
        $name =  isset($response_access["name"]) ? $response_access["name"] : "";
        
        if( $email == "" or $name == "" ){
            die(" Can't login because of missing parameters ");
        }else{
            app_social_process_user_login($email, $name );
        }

        
        ////////end G
    }elseif($type == "Z"){
        $profile_json = file_get_contents('https://graph.zalo.me/v2.0/me?access_token='.$accessToken.'&fields=id,name');
        if (!empty($profile_json)){
            $profile = json_decode( $profile_json, true );
            $email = $profile['id'].'-zalo@'.$_SERVER['HTTP_HOST'];
            if(isset($profile['id'])){
                $email = $profile['id'].'-zalo@'.$_SERVER['HTTP_HOST'];
                app_social_process_user_login($email, $profile['name']);
            }else{
                die("error");
            }
        }

        
        }elseif($type == "A"){
            if(!empty($_POST['information']) && is_email(explode(":",$_POST['information'])[1])){
                $email =  explode(":",$_POST['information'])[1];
                $name =  explode("@",$email)[0];
                app_social_process_user_login($email, $name);
            }else{
                _e('Email is required',"umm");
            }
            
        }
    
    die();
}

function app_social_process_user_login($email, $name){

    if(!is_email($email)) die("error");
    $check_user = email_exists( $email );
    $check_user_name = username_exists($email);

    if($check_user && $check_user > 0 ){
        // is exits

        $id_new_user = $check_user;
        if(!is_customer($id_new_user)) die(json_encode(["warning",alert_notice("warning",$email." ".__("already registered with StyleVook Bussiness","stylevook"))]));
    }else{

        if($check_user_name && $check_user_name > 0 ){
            $id_new_user = $check_user_name;
        }else{
            $args = array (
                'user_login'    =>   $email,
                'user_pass'  =>  md5($email),
                'user_email'    =>  $email,
                'role'  => CUSTOMER,
                'display_name'      =>  strip_tags($name),
            ) ;

            $id = wp_insert_user( $args ) ;

            if(isset($id) && !empty($id)) {
                $id_new_user = $id;
            }else{
                return false;
            }
        }
    }

    if ( is_wp_error($id_new_user) ){
        echo $id_new_user->get_error_message();
    }else{
        $status = get_user_meta($id_new_user,"user_status",true);
        if($status != "active" && $status != ""){
            die(json_encode(array("false",alert_notice("danger",__("Your account is locked,<br>Please login with another account to continue","stylevook"))))); 
        }else{
            wp_clear_auth_cookie();
            $user = wp_set_current_user($id_new_user);
            wp_set_auth_cookie($id_new_user,true,is_ssl());
            do_action('wp_login', $user->user_login, $user);
            echo json_encode(["success",$id_new_user."|||".bj_render_nonce($id_new_user)]) ;
        }
       
        die();
    }
}




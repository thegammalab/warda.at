<?php
if ( ! class_exists('TDF_Users') ){

  class TDF_Users {

    public function __construct() {
      add_action( 'wp_ajax_tdf_check_username',array( $this, 'check_username' ) );
      add_action( 'wp_ajax_nopriv_tdf_check_username',array( $this, 'check_username' ) );

      add_action( 'wp_ajax_tdf_check_email',array( $this, 'check_email' ) );
      add_action( 'wp_ajax_nopriv_tdf_check_email',array( $this, 'check_email' ) );

      add_action( 'wp_ajax_tdf_logout',array( $this, 'logout' ) );
      add_action( 'wp_ajax_nopriv_tdf_logout',array( $this, 'logout' ) );

      add_action( 'tdf_get_login_form', array( $this, 'get_login' ), 10, 2);
      add_filter( 'tdf_get_display_name', array($this, 'get_display_name'), 10, 1);

      add_filter( 'tdf_get_add_user_field', array( $this, 'get_add_user_field' ), 10,3);
      add_filter( 'tdf_get_update_user_field', array( $this, 'get_update_user_field' ), 10,4);

      add_action( 'admin_post_tdf_save_user', array( $this, 'save' ));
      add_action( 'admin_post_nopriv_tdf_save_user', array( $this, 'save' ));

      add_action( 'template_include',array( $this, 'pwd_reset' ) );

      add_action( 'wp_ajax_tdf_send_reset_pwd',array( $this, 'send_reset_pwd' ) );
      add_action( 'wp_ajax_nopriv_tdf_send_reset_pwd',array( $this, 'send_reset_pwd' ) );

      add_action( 'admin_post_tdf_update_user', array( $this, 'update' ));
    }

    function get_display_name($user_id=0){
      $user = new TDF_Users_Model;
      if(!$user_id){
        $user_id = get_current_user_id();
      }
      return $user->get_display_name($user_id);
    }

    function check_username(){
      $user = new TDF_Users_Model;
      if(isset($_GET["vals"])){
        echo $user->check_username($_GET["vals"]);
      }else{
        echo 0;
      }
      die();
    }

    function check_email(){
      $user = new TDF_Users_Model;
      if(isset($_GET["vals"])){
        echo $user->check_email($_GET["vals"]);
      }else{
        echo 0;
      }
      die();
    }

    function logout(){
      $user = new TDF_Users_Model;
      $user->logout();
      die();
    }

    function get_login($redirect_to, $args = array()) {
      if(isset($_GET["login"])){
        if ($_GET["login"] == "required") {
          ?>
          <div class="alert alert-danger">
            You have to be logged in to be able to see this page
          </div>
          <?php
        }
        if ($_GET['login'] == 'failed') {
          ?>
          <div class="alert alert-danger">
            Login failed: You have entered an incorrect Username or password, please try again.
          </div>
          <?php
        }
      }
      if (isset($_GET["confirm_email"])) {
        $mylink = $wpdb->get_row("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'confirmation_code' AND `meta_value`='" . $_GET["confirm_email"] . "'");
        if ($mylink->user_id) {
          update_user_meta($mylink->user_id, "confirmed_email", 1);
          ?>
          <div class="alert alert-success">
            You have successfully confirmed your email address.
          </div>
        <?php } else { ?>
          <div class="alert alert-danger">
            The confirmation code provided is invalid.
          </div>
          <?php
        }
      }
      if(isset($_GET["action"])){
        if ($_GET["action"] == "reset_success") {
          ?>
          <div class="alert alert-success">
            You have successfully reset your password, your new password has been emailed to you.
          </div>
          <?php
        }
      }

      $args = array(
        'echo' => true,
        'redirect' => $redirect_to,
        'form_id' => 'loginform',
        'label_username' => __('Email'),
        'label_password' => __('Password'),
        'label_remember' => __('Remember Me'),
        'label_log_in' => __('Log In'),
        'id_username' => 'login_user_login',
        'id_password' => 'login_user_pass',
        'id_remember' => 'login_rememberme',
        'id_submit' => 'login_wp-submit',
        'remember' => true,
        'value_username' => NULL,
        'value_remember' => false
      );

      foreach ($args as $f => $v) {
        $args1[$f] = $v;
      }
      if (isset($_GET["return_url"])) {
        $args["redirect"] = urldecode($_GET["return_url"]);
      }

      wp_login_form($args);

    }

    function pwd_reset($template){
      global $wpdb;

      if(is_page(PAGE_ID_FORGOT_PASS)){
        if(isset($_GET["action"]) && isset($_GET['key']) && isset($_GET['login']) ){
          if($_GET["action"]=="reset_pwd"){
            $reset_key = $_GET['key'];
            $user_login = $_GET['login'];
            $user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));
            if(isset($user_data)){
              $user_login = $user_data->user_login;
              $user_email = $user_data->user_email;
              if (!empty($reset_key) && !empty($user_data)) {

                $user = get_user_by("id", $cid);
                wp_set_current_user($user_data->ID, $user_data->user_login);
                wp_set_auth_cookie($user_data->ID);
                do_action('wp_login', $user_data->user_login);
                $redirect_to = PAGE_EDIT_ACCOUNT."?action=reset_pass";
                wp_safe_redirect($redirect_to);
                exit();
                // }
              } else {
                $redirect_to = PAGE_EDIT_ACCOUNT."?action=invalid_reset_key";
                wp_safe_redirect($redirect_to);
                exit();
              }
            }
          }
        }
      }

      return $template;

    }

    function send_reset_pwd(){
      global $wpdb;

      if (empty($_REQUEST['user_input'])) {
        echo "<div class='alert alert-danger'>Please enter your Username or E-mail address</div>";
        exit();
      }
      //We shall SQL escape the input
      $user_input = $wpdb->escape(trim($_REQUEST['user_input']));
      if (strpos($user_input, '@')) {
        $user_data = get_user_by_email($user_input);
        if (empty($user_data)) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
          echo "<div class='alert alert-danger'>Invalid E-mail address!</div>";
          exit();
        }
      } else {
        $user_data = get_userdatabylogin($user_input);
        if (empty($user_data)) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
          echo "<div class='alert alert-danger'>Invalid Username!</div>";
          exit();
        }
      }
      $user_login = $user_data->user_login;
      $user_email = $user_data->user_email;
      $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
      if (empty($key)) {
        //generate reset key
        $key = wp_generate_password(20, false);
        $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
      }

      $reset_link = PAGE_FORGOT_PASS."?action=reset_pwd&key=$key&login=" . rawurlencode($user_login);

      $email = new TDF_Email_Model;
      $email->send_email("forgot",$user_data->ID,array("username"=>$user_login,"reset_link"=>$reset_link));

      echo "<div class='alert alert-success'>We have sent you instructions on resetting your password.</div>";

      die();
    }

    public function get_add_user_field($name,$type="",$args=array()){
      $user = new TDF_Users_Model;
      $field = $user->get_add_field($name,$type,$args);
      return $field;
    }

    public function get_update_user_field($name,$user_id,$type="",$args=array()){
      $user = new TDF_Users_Model;
      $field = $user->get_update_field($name,$user_id,$type,$args);
      return $field;
    }


    public function save(){
      global $wpdb;
      $user = new TDF_Users_Model;
      $user_id = $user->save(0,$_POST);

      if(is_wp_error($user_id)){
        $error_msg = "";
        foreach ($user_id->errors as $f => $v) {
          $error_msg.=$v[0] . "<br>";
        }
        if(strpos($_POST["error_url"],"?")){
          $cnt = "&";
        }else{
          $cnt = "?";
        }
        header("Location:".$_POST["error_url"].$cnt."error_msg=".$error_msg."&".http_build_query($_POST));
      }else{
        if ($_POST["autologin"]) {
          $user = get_user_by("id", $user_id);
          wp_set_current_user($user_id, $user->user_login);
          wp_set_auth_cookie($user_id);
          do_action('wp_login', $user->user_login);
        }

        $email = new TDF_Email_Model;
        $email->send_email("register",$user_id,array("username"=>$user->user_login,"reset_link"=>$reset_link));
        header("Location:".$_POST["success_url"]);
      }
    }

    public function update(){
      global $wpdb;
      $user = new TDF_Users_Model;
      $user_id = $_POST["user_id"];
      $user_id = $user->save($user_id,$_POST);

      if(is_wp_error($user_id)){
        $error_msg = "";
        foreach ($user_id->errors as $f => $v) {
          $error_msg.=$v[0] . "<br>";
        }
        if(strpos($_POST["error_url"],"?")){
          $cnt="&";
        }else{
          $cnt = "?";
        }
        header("Location:".$_POST["error_url"].$cnt."error_msg=".$error_msg."&".http_build_query($_POST));
      }else{
        header("Location:".$_POST["success_url"]);
      }
    }
  }

  new TDF_Users;
}

<?php
if ( ! class_exists('TDF_Email_Model') ){

  class TDF_Email_Model {
    public function __construct() {
      add_action('acf/init', array( $this, 'register_emails_page' ));
    }

    function register_emails_page(){
      if( function_exists('acf_add_options_page') ) {
        acf_add_options_page('Email Settings');

        acf_add_local_field_group(array(
          'key' => 'group_1',
          'title' => 'Email Settings',
          'fields' => array (
            array (
              'key' => 'tdf_email_from_name',
              'label' => 'From Name',
              'name' => 'tdf_email_from_name',
              'type' => 'text',
            ),
            array (
              'key' => 'tdf_email_from_email',
              'label' => 'From Email',
              'name' => 'tdf_email_from_email',
              'type' => 'text',
            ),
            array (
              'key' => 'tdf_email_header',
              'label' => 'Email Header',
              'name' => 'tdf_email_header',
              'type' => 'wysiwyg',
            ),
            array (
              'key' => 'tdf_email_footer',
              'label' => 'Email Footer',
              'name' => 'tdf_email_footer',
              'type' => 'wysiwyg',
            )
          ),
          'location' => array (
            array (
              array (
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-email-settings',
              ),
            ),
          ),
        ));
      }
    }

    function register_emails($emails){
      foreach($emails as $email){
        if(isset($email[1]) && $email[1]){
          $args = $email[1];
        }

        $fields = array(
          array (
            'key' => 'tdf_email_subject_'.$email[0],
            'label' => 'Email Subject',
            'name' => 'tdf_email_subject_'.$email[0],
            'type' => 'text',
          ),
          array (
            'key' => 'tdf_email_body_'.$email[0],
            'label' => 'Email Body',
            'name' => 'tdf_email_body_'.$email[0],
            'type' => 'wysiwyg',
          ),
        );
        if(isset($args["variables"])){
          $vars = array();
          foreach($args["variables"] as $var){
            $vars[]="{".$var."}";
          }
          $instructions = array (
            'key' => 'instructions_'.$email[0],
            'message' => 'You can use the following variables: '.implode(", ",$vars),
            'type' => 'message',
          );
          array_unshift($fields, $instructions);
        }

        if(isset($args["has_admin"])){
          if(isset($args["admin_variables"])){
            $vars = array();
            foreach($args["admin_variables"] as $var){
              $vars[]="{".$var."}";
            }
            $fields[] = array (
              'key' => 'instructions_admin_'.$email[0],
              'message' => 'You can use the following variables: '.implode(", ",$vars),
              'type' => 'message',
            );
          }
          $fields[] = array (
            'key' => 'tdf_email_subject_admin_'.$email[0],
            'label' => 'Admin Email Subject',
            'name' => 'tdf_email_subject_admin_'.$email[0],
            'type' => 'text',
          );
          $fields[] = array (
            'key' => 'tdf_email_body_admin_'.$email[0],
            'label' => 'Admin Email Body',
            'name' => 'tdf_email_body_admin_'.$email[0],
            'type' => 'wysiwyg',
          );
        }

        if(isset($args["custom_function"])){
          update_option('tdf_custom_function_'.$email[0],$args["custom_function"]);
        }else{
          delete_option('tdf_custom_function_'.$email[0]);
        }

        if(isset($args["custom_admin_function"])){
          update_option('tdf_custom_admin_function_'.$email[0],$args["custom_admin_function"]);
        }else{
          delete_option('tdf_custom_admin_function_'.$email[0]);
        }

        acf_add_local_field_group(array(
          'key' => 'group_'.$email[0],
          'title' => apply_filters("tdf_generate_name",$email[0])." Emails",
          'fields' => $fields,
          'location' => array (
            array (
              array (
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-email-settings',
              ),
            ),
          ),
        ));
      }
    }

    function register_email($email){

    }

    function send_email($type,$user_id,$args){
      global $wpdb;

      if (!$user_id) {
        $user_id = get_current_user_id();
      }
      $user_info = get_userdata($user_id);
      $to = $user_info->user_email;
      $admin_to = get_option("admin_email");

      $body = stripslashes(get_option("options_tdf_email_body_".$type));
      $subject = get_option("options_tdf_email_subject_".$type);

      if($body && $subject){
        foreach($args as $f=>$v){
          $body = str_replace("{".$f."}",$v, $body);
          $subject = str_replace("{".$f."}",$v, $subject);
        }
        if($custom_function = get_option('tdf_custom_function_'.$type)){
          $body = apply_filters("tdf_email_".$custom_function,$body,$args);
          $subject = apply_filters("tdf_email_".$custom_function,$subject,$args);
        }

        $message = '<table rules="all" cellpadding="10">';
        $message .= "<tr><td>" . $body . "</td></tr>";
        $message .= "</table></body></html>";

        $this->process_email($to,$subject,$message);
      }

      $body_admin = stripslashes(get_option("options_tdf_email_body_admin_".$type));
      $subject_admin = get_option("options_tdf_email_subject_admin_".$type);

      if($body_admin && $subject_admin){
        foreach($args as $f=>$v){
          $body_admin = str_replace("{".$f."}",$v, $body_admin);
          $subject_admin = str_replace("{".$f."}",$v, $subject_admin);
        }
        if($custom_admin_function = get_option('tdf_custom_admin_function_'.$type)){
          $body_admin = apply_filters("tdf_email_".$custom_admin_function,$body_admin,$args);
          $subject_admin = apply_filters("tdf_email_".$custom_admin_function,$subject_admin,$args);
        }

        $message_admin = '<table rules="all" cellpadding="10">';
        $message_admin .= "<tr><td>" . $body_admin . "</td></tr>";
        $message_admin .= "</table></body></html>";

        $this->process_email($admin_to,$subject_admin,$message_admin);
      }
    }

    function process_email($to, $subject, $body){
      $headers = "From: " . get_option("options_tdf_email_from_name") . " <" . get_option("options_tdf_email_from_email") . ">\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=utf-8\r\n";
      // $headers .= "Content-Transfer-Encoding: quoted-printable";
      $msg = stripslashes(get_option("options_tdf_email_header"));
      $msg .= $body;
      $msg .= stripslashes(get_option("options_tdf_email_footer"));
      if (wp_mail($to, $subject, $msg, $headers)) {
        return true;
      } else {
        return false;
      }
    }
  }

  new TDF_Email_Model;
}

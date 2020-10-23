<?php
if ( ! class_exists('TDF_Users_Model') ){

  class TDF_Users_Model {

    public function __construct() {
      add_action('authenticate', array($this,'login_redirect'));
      add_action('wp_login_failed', array($this,'login_failed')); // hook failed login
    }

    function logout(){
      wp_logout();
    }

    function login_failed($user) {
      $referrer = $_SERVER['HTTP_REFERER'];
      $referrer = str_replace("?login=failed", "", $referrer);
      $referrer = str_replace("?login=required", "", $referrer);

      if($referrer){
        if (!strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && $user != null) {
          if (!strpos($referrer, '?')) {
            wp_redirect($referrer . '?login=failed');
          } else {
            wp_redirect($referrer . '&login=failed');
          }
          exit;
        }
      }
    }

    function login_redirect($user) {
      if(isset($_SERVER['HTTP_REFERER'])){
        $referrer = $_SERVER['HTTP_REFERER'];
        $error = false;
        if(isset($_POST['log']) && isset($_POST['pwd'])){
          if ($_POST['log'] == '' || $_POST['pwd'] == '') {
            $error = true;
          }
        }

        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && $error) {
          if (!strpos($referrer, '?login=failed')) {
            wp_redirect($referrer . '?login=failed');
          } else {
            wp_redirect($referrer);
          }
          exit;
        }
      }
    }

    function logmein(){
      $creds = array();
      $user_data = wp_signon();
      wp_set_current_user($user_data->ID, $user_data->user_login);
      wp_set_auth_cookie($user_data->ID);
      do_action('wp_login', $user_data->user_login);

      return true;
    }

    function check_username($value){
      if (get_user_by("login", $value)) {
        return true;
      }else{
        return false;
      }
    }

    function check_email($value){
      if (get_user_by("email", $value)) {
        return true;
      }else{
        return false;
      }
    }

    function get_display_name($user_id,$post_id=0){
      global $wpdb;

      if (get_user_meta($user_id, "first_name", true)) {
        return ucfirst(get_user_meta($user_id, "first_name", true)) . " " . ucfirst(get_user_meta($user_id, "last_name", true));
      } else {
        $user = get_user_by("id",$user_id);
        return $user->display_name;
      }
    }


    /* FIELDS */


    function get_add_field($name, $type = "", $args = "") {
      ob_start();

      if(isset($args["value"])){
        $value=$args["value"];
      }elseif(isset($_GET["user_meta_".$name])){
        $value=$_GET["user_meta_".$name];
      }elseif(isset($_GET["user_core_".$name])){
        $value=$_GET["user_core_".$name];
      }else{
        $value="";
      }

      if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('tdf_custom_user_' . $field);
        $info = json_decode($info);
        if (!$type) {
          $type = $info->type;
        }

        $vals = $info->values;
        if ($type == "textarea") {
          echo '<textarea name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="user_meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="checkbox" name="user_meta_' . $field . '[]" value="' . $f . '" class="' . $args["classes"] . '" /></span>' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="user_meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            echo '<li><span><input type="radio" name="user_meta_' . $field . '" value="' . $f . '" class="' . $args["classes"] . '" /></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            echo '<option value="' . $f . '">' . $v . "</option>";
          }
          echo "</select>";
        } elseif ($type == "file") {
          echo '<input type="file" name="user_file_' . $field . '" id="user_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        } else {
          echo '<input type="text" name="user_meta_' . $field . '" placeholder="' . $args["placeholder"] . '"  id="user_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
      if ($name == "password") {
        echo '<input type="password" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" value="' . $value . '" />';
      }
      if ($name == "password_confirm") {
        echo '<input type="password" name="user_core_' . $name . '2" id="user_core_' . $name . '2" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" value="' . $value . '" />';
      }
      if ($name == "login" || $name == "display_name" || $name == "nickname") {
        echo '<input type="text" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" value="' . $value . '" />';
      }
      if ($name == "first_name" || $name == "last_name") {
        echo '<input type="text" name="user_meta_' . $name . '" id="user_meta_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" value="' . $value . '" />';
      }
      if ($name == "description") {
        echo '<textarea name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '">' . $value . '</textarea>';
      }
      if ($name == "email") {
        echo '<input type="email" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" value="' . $value . '" />';
      }
      if ($name == "avatar") {
        echo '<input type="file" name="user_core_avatar" id="user_core_avatar" class="' . $args["classes"] . '" />';
      }
      if ($name == "image_gallery") {
        //echo '<div class="upload_div"><label>'.$args["label"].'</label><input type="file" name="post_core_gallery" id="post_core_gallery" multiple /></div>';
        $sel = '[data_file="+file.name+"]';
        ?>
        <script>
        $(document).ready(function () {
          Dropzone.options.myAwesomeDropzoneUser = {
            url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image_user",
            previewsContainer: ".dropzone-previews_user",
            uploadMultiple: true,
            parallelUploads: 1,
            maxFiles: 100,
            addRemoveLinks: true,
            init: function () {
              this.on("success", function (file, response) {
                $("#attachment_inputs").append(response);
              });
              this.on("removedfile", function (file, response) {
                var rem = $('#attachment_inputs input[data_file="' + file.name + '"]').attr("value");
                $('input[data_file="' + file.name + '"]').remove();
                $.ajax({
                  url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                  context: document.body
                })
              });
            }
          }
        })
        </script>
        <div class="dropzone-previews_user dropzone" id="my-awesome-dropzone-user" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs" class="hidden"></div>
        <?php
      }

      $cont = ob_get_contents();
      ob_end_clean();

      return $cont;
    }

    function get_update_field($name, $pid, $type = "", $args = "") {
      ob_start();

      $user = get_user_by("id", $pid);
      $user_vals = array();
      foreach ($user->data as $f => $v) {
        $user_vals[$f] = $v;
      }
      if (substr($name, 0, 5) == "meta_") {
        $field = substr($name, 5);
        $info = get_option('tdf_custom_user_' . $field);
        if(($info)){
          $info = json_decode($info);
          if (!$type) {
            $type = $info->type;
          }
        }
        if(isset($info->values)){
          $vals = $info->values;
        }
        $value = get_user_meta($pid, $field, true);
        if (count($value1 = explode("|", $value)) > 1) {
          $value = explode("|", $value);
        }
        if ($type == "textarea") {
          echo '<textarea name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
        } elseif ($type == "checkbox") {
          echo '<ul id="user_meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><span><input type="checkbox" name="user_meta_' . $field . '[]" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/></span>' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "radio") {
          echo '<ul id="user_meta_' . $field . '">';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'checked="checked"';
              } else {
                $selected = "";
              }
            }
            echo '<li><span><input type="radio" name="user_meta_' . $field . '" value="' . $f . '" ' . $selected . ' class="' . $args["classes"] . '"/></span> ' . $v . "</li>";
          }
          echo "</ul>";
        } elseif ($type == "select") {
          echo '<select name="user_meta_' . $field . '" id="user_meta_' . $field . '" class="' . $args["classes"] . '">';
          echo '<option value="0"> -- select -- </option>';
          foreach ($vals as $f => $v) {
            if (is_array($value)) {
              if (in_array($f, $value)) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            } else {
              if ($f == $value) {
                $selected = 'selected="selected"';
              } else {
                $selected = "";
              }
            }
            echo '<option value="' . $f . '" ' . $selected . '>' . $v . "</option>";
          }
          echo "</select>";
        } elseif ($type == "file") {
          echo '<input type="file" name="user_file_' . $field . '" id="user_file_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
          echo "<div>Current file: ";
          echo '<a href="' . wp_get_attachment_url($value) . '" target="_blank">' . get_the_title($value) . "</a>";
          echo "</div>";
        } else {
          echo '<input type="text" name="user_meta_' . $field . '" id="user_meta_' . $field . '" value="' . $value . '" class="' . $args["classes"] . '" />';
        }
      }
      if ($name == "password") {
        echo '<input type="password" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" />';
      }
      if ($name == "password_confirm") {
        echo '<input type="password" name="user_core_' . $name . '2" id="user_core_' . $name . '2" class="' . $args["classes"] . '" />';
      }
      if ($name == "login" || $name == "display_name" || $name == "nickname") {
        if ($name == "login") {
          $name1 = "user_login";
        } else {
          $name1 = $name;
        }
        echo '<input type="text" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" value="' . $user_vals[$name1] . '" />';
      }
      if ($name == "first_name" || $name == "last_name") {
        $value = get_user_meta($pid, $name, true);
        echo '<input type="text" name="user_meta_' . $name . '" id="user_meta_' . $name . '" value="' . $value . '" class="' . $args["classes"] . '" placeholder="' . $args["placeholder"] . '" />';
      }
      if ($name == "description") {
        $value = get_user_meta($pid, "description", true);
        echo '<textarea name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '">' . $value . '</textarea>';
      }
      if ($name == "email") {
        echo '<input type="email" name="user_core_' . $name . '" id="user_core_' . $name . '" class="' . $args["classes"] . '" value="' . $user_vals["user_email"] . '" />';
      }
      if ($name == "avatar") {
        $av = get_user_meta($pid, "avatar_id", true);
        echo '<div style="float:left; width:20%;" class="img_max_width">';
        if (wp_get_attachment_image($av)) {
          echo wp_get_attachment_image($av);
        } else {
          echo "<img alt='image_alt' src='" . get_bloginfo("template_url") . "/assets/images/defaults/no_user.png' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }
        echo '</div><div style="float:right; width:75%;">';
        echo '<input type="file" name="user_core_avatar" id="user_core_avatar" />';
        echo '</div>';
      }
      if ($name == "image_gallery") {
        $images2 = array();
        if (get_user_meta($pid, "avatar_id", true)) {
          $images2[] = get_user_meta($pid, "avatar_id", true);
        }
        $images_list = get_user_meta($pid, "image_gallery", true);
        foreach ($images_list as $im) {
          $images2[] = $im;
        }
        if ($images2) {
          $gal = '<ul class="gallery_list row" style="list-style:none; padding:0; margin:0; margin-bottom:20px; margin-left:-10px;">';
          foreach ($images2 as $image) {
            $gal .= '<li class="col-lg-2 no-padding img_max_width" style="margin-bottom:10px;">' . wp_get_attachment_image($image, "thumbnail", false) . '<div style="position:absolute; bottom:0; background:#FFF; width:100%; padding:3px 5px; opacity:0.95;"><a href="' . get_bloginfo("url") . '/edit-profile/edit-image/?img_id=' . $image . '" class="pull-left" style="display:block; background:#FFF; padding:3px;">edit</a><a href="' . get_bloginfo("url") . '/?ajax_action=delete_img_user&img_id=' . $image . '" class="del_img_link pull-right" style="display:block; background:#FFF; padding:3px; text-align:right;">delete</a></div></li>';
          }
          $gal .= "</ul>";
          ?>
          <script>$(document).ready(function () {
            $('.del_img_link').click(function () {
              var lnk = $(this).attr('href');
              var txt;
              var r = confirm("Are you sure you want to delete?");
              if (r == true) {
                $.ajax({url: lnk});
                $(this).parent().remove();
              } else {
              }
              return false;
            })
          });</script>
          <?php
        }
        $sel = '[data_file="+file.name+"]';
        echo $gal;
        ?>
        <script>
        $(document).ready(function () {
          Dropzone.options.myAwesomeDropzoneUser = {
            url: "<?php echo get_bloginfo("url"); ?>/?ajax_action=upload_image_user",
            previewsContainer: ".dropzone-previews_user",
            uploadMultiple: true,
            parallelUploads: 1,
            maxFiles: 100,
            addRemoveLinks: true,
            init: function () {
              this.on("success", function (file, response) {
                $("#attachment_inputs").append(response);
              });
              this.on("removedfile", function (file, response) {
                var rem = $('#attachment_inputs input[data_file="' + file.name + '"]').attr("value");
                $('input[data_file="' + file.name + '"]').remove();
                $.ajax({
                  url: '<?php bloginfo("url"); ?>/?ajax_action=delete_img&img_id=' + rem,
                  context: document.body
                })
              });
            }
          }
        })
        </script>
        <div class="dropzone-previews_user dropzone" id="my-awesome-dropzone-user" style="clear:both; overflow:auto; margin-bottom:20px;"></div>
        <div id="attachment_inputs" class="hidden"></div>
        <?php
      }

      $cont = ob_get_contents();
      ob_end_clean();

      return $cont;
    }

    function register_field($name, $label, $type = "", $values = "") {
      if (!$type) {
        $type = "text";
      }
      if (!is_array($values)) {
        $values = explode("|", $values);
      }
      foreach ($values as $f => $v) {
        if (is_int($f)) {
          $vals[$f] = $v;
        } else {
          $vals[$f] = $v;
        }
      }
      $options = array(
        "label" => $label,
        "type" => $type,
        "values" => $vals
      );
      update_option('tdf_custom_user_' . $name, json_encode($options));
    }


    /* PROCESS SAVE */


    function save($user_id, $args){
      global $error_msg;

      if ($user_id = $_POST["user_core_id"]) {
        $fields = array("user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        $data["ID"] = $user_id;
        foreach ($fields as $f => $v) {
          if (isset($_POST[$v])) {
            $data[$f] = $_POST[$v];
          }
        }
        if ($_POST["user_core_password"]) {
          if ($user_id == get_current_user_id()) {
            $update_cookie = 1;
          }
          $data["user_pass"] = $_POST["user_core_password"];
        }
        $user_id = (wp_update_user($data));
      } else {
        $fields = array("user_pass" => "user_core_password", "user_login" => "user_core_login", "user_nicename" => "user_core_nicename", "user_email" => "user_core_email", "display_name" => "user_core_display_name", "nickname" => "user_core_nickname", "first_name" => "user_core_first_name", "last_name" => "user_core_last_name", "description" => "user_core_description", "user_registered" => "user_core_registered", "role" => "user_core_role");
        $data = array();
        foreach ($fields as $f => $v) {
          if (isset($_POST[$v])) {
            $data[$f] = $_POST[$v];
          }
        }

        if (!$data["user_login"] && $_REQUEST["user_core_email"]) {
          $data["user_login"] = $_REQUEST["user_core_email"];
        }
        if (!$data["user_email"] && $_REQUEST["user_core_login"]) {
          $data["user_email"] = $_REQUEST["user_core_login"];
        }
        //
        $user_id = wp_insert_user($data);

      }

      if ($user_id && !is_wp_error($user_id)) {


        if ($file = basename($_FILES["user_core_avatar"]["name"])) {
          $uploadedfile = $_FILES['user_core_avatar'];
          $upload_overrides = array('test_form' => false);
          $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
          //print_r($movefile);
          if ($movefile) {
            $wp_filetype = $movefile['type'];
            $filename = $movefile['file'];
            $wp_upload_dir = wp_upload_dir();
            $attachment = array(
              'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
              'post_mime_type' => $wp_filetype,
              'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
              'post_content' => '',
              'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $filename);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            update_user_meta($user_id, "avatar_id", $attach_id);
          }
        }
        foreach ($_FILES as $f => $v) {
          if (substr($f, 0, 10) == "user_file_") {
            $field = substr($f, 10);
            $uploadedfile = $_FILES[$f];
            if ($uploadedfile['name']) {
              $upload_overrides = array('test_form' => false);
              $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
              if ($movefile) {
                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                  'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                  'post_mime_type' => $wp_filetype,
                  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                  'post_content' => '',
                  'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
              }
            }
            update_user_meta($user_id, $field, $attach_id);
          }
        }
        if ($_POST["user_attach"]) {
          $attach_list = $_POST["user_attach"];
          $attach_list = array_unique($attach_list);
          $vals = get_user_meta($user_id, "image_gallery", true);
          foreach ($attach_list as $aid) {
            $vals[] = $aid;
          }
          update_user_meta($user_id, "image_gallery", $vals);
        }
        foreach ($_POST as $f => $v) {
          if (substr($f, 0, 10) == "user_meta_") {
            $field = substr($f, 10);
            if (is_array($v)) {
              delete_user_meta($user_id, $field);
              foreach ($v as $val) {
                add_user_meta($user_id, $field, $val);
              }
            } else {
              update_user_meta($user_id, $field, $v);
            }
          }
        }
      }

      return $user_id;
    }
  }
  new TDF_Users_Model;
}

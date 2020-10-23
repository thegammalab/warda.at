<?php
if ( ! class_exists('TDF_Media_Model') ){

  class TDF_Media_Model {

    function upload_images($files){
      if (is_admin()) {
        require_once('includes/file.php' );
        require_once('includes/image.php' );
      } else {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
      }

      $response = array();
      $wp_upload_dir = wp_upload_dir();
      $files = $_FILES["file"];
      for ($i = 0; $i < count($files['name']); $i++) {
        $upload_overrides = array('test_form' => false);
        $wp_filetype = $files['type'][$i];
        $fname = $files['name'][$i];
        $filename = sanitize_file_name(rand(100, 999) . "_" . $files['name'][$i]);
        $movefile = move_uploaded_file($files['tmp_name'][$i], $wp_upload_dir['path'] . '/' . basename($filename));
        if ($movefile) {
          $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $wp_filetype,
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
          );
          $attach_id = wp_insert_attachment($attachment, $wp_upload_dir['path'] . '/' . basename($filename));
          $attach_data = wp_generate_attachment_metadata($attach_id, $wp_upload_dir['path'] . '/' . basename($filename));
          wp_update_attachment_metadata($attach_id, $attach_data);
        }

        $media_info = get_post($attach_id);
        $src = wp_get_attachment_image_src($attach_id, "thumbnail");
        $response[]=array(
          "id" =>$attach_id,
          "src" => $src[0],
          "file"=>$fname,
          "title"=>$media_info->post_title,
          "description"=>$media_info->post_content,
        );
      }

      return $response;
    }

    function delete_images($img_id){
      wp_delete_attachment($img_id);
    }

    function save_image_content($img_id,$title,$description){
      $my_post = array(
        'ID'           => $img_id,
        'post_title'   => $title,
        'post_content' => $description,
      );
      wp_update_post( $my_post );
    }
  }
}

<?php
if ( ! class_exists('TDF_Media') ){

  class TDF_Media {

    public function __construct() {
      add_action( 'wp_ajax_tdf_save_image_content',array( $this, 'save_image_content' ) );
      add_action( 'wp_ajax_nopriv_tdf_save_image_content',array( $this, 'save_image_content' ) );

      add_action( 'wp_ajax_tdf_upload_image',array( $this, 'upload_posts_image' ) );
      add_action( 'wp_ajax_nopriv_tdf_upload_image',array( $this, 'upload_posts_image' ) );
      add_action( 'wp_ajax_upload_image_user',array( $this, 'upload_users_image' ) );
      add_action( 'wp_ajax_nopriv_upload_image_user',array( $this, 'upload_users_image' ) );

      add_action( 'wp_ajax_tdf_delete_image',array( $this, 'delete_image' ) );
      add_action( 'wp_ajax_nopriv_tdf_delete_image',array( $this, 'delete_image' ) );
      add_action( 'wp_ajax_tdf_delete_user_image',array( $this, 'delete_user_image' ) );
      add_action( 'wp_ajax_nopriv_tdf_delete_user_image',array( $this, 'delete_user_image' ) );

      add_action( 'wp_ajax_tdf_save_image_order',array( $this, 'save_image_order' ) );
      add_action( 'wp_ajax_nopriv_tdf_save_image_order',array( $this, 'save_image_order' ) );
    }

    function save_image_content(){
      $media = new TDF_Media_Model;
      $files = $media->save_image_content($_REQUEST["img_id"],$_REQUEST["img_title"],$_REQUEST["img_description"]);
    }

    function upload_posts_image(){
      $media = new TDF_Media_Model;

      $files = $media->upload_images($_FILES["file"]);
      foreach($files as $file){
        include(get_stylesheet_directory().'/views/media/item.php');
      }
      die();
    }

    function upload_users_image(){
      $media = new TDF_Media_Model;

      $files = $media->upload_images($_FILES["file"]);
      foreach($files as $file){
        include(get_stylesheet_directory().'/views/media/item.php');
      }
      die();
    }

    function delete_image(){
      $media = new TDF_Media_Model;
      if ($media->delete_image($_REQUEST['img_id'])) {
        echo 1;
      } else {
        echo 0;
      }
      die();
    }

    function delete_user_image(){
      $media = new TDF_Media_Model;

      $args = array();
      $user_id = get_current_user_id();
      $field_id = $_REQUEST['img_id'];
      $new_gal = array();
      $gal = get_user_meta($user_id, "image_gallery", true);
      foreach ($gal as $v) {
        if ($v != $field_id) {
          $new_gal[] = $v;
        }
      }
      $gal = update_user_meta($user_id, "image_gallery", $new_gal);
      if ($media->delete_image($_REQUEST['img_id'])) {
        echo 1;
      } else {
        echo 0;
      }
      die();
    }

    function save_image_order(){
      global $wpdb;
      $images = explode("|",$_REQUEST["images"]);
      $order=0;

      foreach($images as $im){
        $wpdb->query("UPDATE `".$wpdb->posts."` SET `menu_order`='".$order."' WHERE `ID`='".$im."'");
        $order++;
      }
    }
  }

  new TDF_Media;
}

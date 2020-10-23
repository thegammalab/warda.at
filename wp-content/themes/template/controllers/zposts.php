<?php
if ( ! class_exists('TDF_Posts') ){

  class TDF_Posts {

    public function __construct() {
      add_filter( 'tdf_get_posts', array( $this, 'get' ), 10, 4);
      add_filter( 'tdf_get_single', array( $this, 'get_single' ), 10, 2);

      add_filter( 'warda_events_date_time', array( $this, 'events_date_time' ), 10, 2);

      

      add_filter( 'tdf_get_add_post_field', array( $this, 'get_add_post_field' ), 10,3);
      add_filter( 'tdf_get_update_post_field', array( $this, 'get_update_post_field' ), 10,4);

      add_action( 'admin_post_tdf_save_post', array( $this, 'save' ));
      add_action( 'admin_post_tdf_update_post', array( $this, 'update' ));

      add_action('wp_ajax_get_ajax_post_list', array($this, 'get_ajax_post_list'));
      add_action('wp_ajax_nopriv_get_ajax_post_list', array($this, 'get_ajax_post_list'));
    }

    function events_date_time($post_id,$include_date=0){
      $start_date = get_post_meta($post_id,"start_date",true);
      $end_date = get_post_meta($post_id,"end_date",true);

      if(!$start_date){
        return "";
      }

      $start_time = date("G:i",strtotime(get_post_meta($post_id,"start_time",true)));
      $end_time = date("G:i",strtotime(get_post_meta($post_id,"end_time",true)));
      if($start_date==$end_date || !$end_date){
        if($include_date){
          $time = strftime("%e. %B",strtotime($start_date))." ab ".$start_time;
        }else{
          $time = "von ".$start_time;
        }
        if($end_time){
          $time.=" bis ".$end_time;
        }
      }else{
        $time = "von ".strftime("%e. %B",strtotime($start_date))." ".$start_time;
        if($end_date){
          $time.=" bis ".strftime("%e. %B",strtotime($end_date))." ".$end_time;
        }
      }
      return $time;
    }

    function get_ajax_post_list()
    {
      $args = json_decode(stripslashes($_REQUEST["args"]),TRUE);
      $post_type = $_REQUEST["post_type"];
      $results = $this->get($post_type, $_REQUEST["per_page"], $_REQUEST["page"], $args);
      if($post_type=="post"){
        include(locate_template("/views/posts/post/content-ajax-list.php")); 
      }
      die();
    }

    public function get($post_type,$per_page=10,$page_no=0,$args=array()){
      $posts = new TDF_Posts_Model;
      $args["post_type"] = $post_type;
      if(!isset($args["post_template"])){
        if($post_type=="product"){
          $args["post_template"] = ((get_stylesheet_directory().'/views/woocommerce/product-item.php'));
        }else{
          $args["post_template"] = ((get_stylesheet_directory().'/views/posts/' . $post_type . "/content-item.php"));
        }
      }
      if(!isset($args["search"])){
        $args["search"] = $posts->generate_search_from_get();
      }
      if(!isset($args["no_results_html"])){
        $args["no_results_html"] = '<div class="col-sm-12"><h3 class="no_results" style="text-align:center;">Sorry, no results</h3></div>';
      }

      $args["page"] = $page_no;
      $args["per_page"] = $per_page;

      $results = $posts->get($args);
      return $results;
    }

    public function get_single($post_id,$args=array()){
      $posts = new TDF_Posts_Model;
      $item = $posts->get_the_post($post_id);
      return $item;
    }

    public function get_add_post_field($name,$type="",$args=array()){
      $posts = new TDF_Posts_Model;
      $field = $posts->get_add_field($name,$type,$args);
      return $field;
    }

    public function get_update_post_field($name,$post_id,$type="",$args=array()){
      $posts = new TDF_Posts_Model;
      $field = $posts->get_update_field($name,$post_id,$type,$args);
      return $field;
    }


    public function save(){
      global $wpdb;
      $post = new TDF_Posts_Model;
      $post_id = $post->save($_POST["post_type"],0,$_POST);

      if(is_wp_error($post_id)){
        $error_msg = "";
        foreach ($post_id->errors as $f => $v) {
          $error_msg.=$v[0] . "<br>";
        }
        if(strpos($_POST["error_url"],"?")){
          $cnt="&";
        }else{
          $cnt = "?";
        }
        header("Location:".$_POST["error_url"].$cnt."error_msg=".$error_msg."&".http_build_query($_POST));
      }else{
        if(isset($_POST["post_attach"][0])){
          set_post_thumbnail($post_id, $_POST["post_attach"][0]);
        }
        header("Location:".$_POST["success_url"]);
      }
    }

    public function update(){

      global $wpdb;
      $post = new TDF_Posts_Model;
      $post_id = $_POST["post_id"];
      $post_id = $post->save($_POST["post_type"],$post_id,$_POST);
      if(is_wp_error($post_id)){
        $error_msg = "";
        foreach ($post_id->errors as $f => $v) {
          $error_msg.=$v[0] . "<br>";
        }
        if(strpos($_POST["error_url"],"?")){
          $cnt="&";
        }else{
          $cnt = "?";
        }
        header("Location:".$_POST["error_url"].$cnt."error_msg=".$error_msg."&".http_build_query($_POST));
      }else{
        if(isset($_POST["post_attach"][0])){
          set_post_thumbnail($post_id, $_POST["post_attach"][0]);
        }
        header("Location:".$_POST["success_url"]);
      }
    }
  }

  new TDF_Posts;
}

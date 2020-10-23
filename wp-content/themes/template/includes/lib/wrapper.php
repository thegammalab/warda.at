<?php
/**
* Theme wrapper
*
* @link http://TDF.io/an-introduction-to-the-TDF-theme-wrapper/
* @link http://scribu.net/wordpress/theme-wrappers.html
*/

class TDF_Wrapping {
  // Stores the full path to the main template file
  public static $main_template;

  // basename of template file
  public $base_slug;

  // array of templates
  public $templates;

  // Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
  static $base;

  public function __construct($template = 'default') {
    $template = $this->get_slug();
    if(file_exists(get_stylesheet_directory()."/views/wrapper/".$template."-wrapper.php")){
      $file_path = "/views/wrapper/".$template."-wrapper.php";
    }else{
      $template = "default";
      $file_path = "/views/wrapper/".$template."-wrapper.php";
    }

    $this->base_slug = basename($template);
    $this->templates = array($file_path);

    add_action('tdf_get_head', array($this, 'get_head'));
    add_action('tdf_get_header', array($this, 'get_header'));
    add_action('tdf_get_footer', array($this, 'get_footer'));
    add_action('tdf_get_template_path', array($this, 'get_template_path'));
    add_action('tdf_get_sidebar_path', array($this, 'get_sidebar_path'));
  }

  public function __toString() {
    $this->templates = apply_filters('TDF/' . $this->base_slug, $this->templates);
    return locate_template($this->templates);
  }

  static function wrap($main) {
    // Check for other filters returning null
    if (!is_string($main)) {
      return $main;
    }

    self::$main_template = $main;
    self::$base = basename(self::$main_template, '.php');

    if (self::$base === 'index') {
      self::$base = false;
    }

    return new TDF_Wrapping();
  }

  function get_template_path() {
    include(self::$main_template);
  }

  function get_head(){
    wp_head();
    echo '<title>'.wp_title('',FALSE).'</title>';
    if(file_exists(get_stylesheet_directory()."/views/head/".$this->get_slug()."-head.php")){
      get_template_part('views/head/'.$this->get_slug(),'head');
    }else{
      get_template_part('views/head/default','head');
    }
  }

  function get_header(){
    if(file_exists(get_stylesheet_directory()."/views/header/".$this->get_slug()."-header.php")){
      get_template_part('views/header/'.$this->get_slug(),'header');
    }else{
      get_template_part('views/header/default','header');
    }
  }

  function get_footer(){
    if(file_exists(get_stylesheet_directory()."/views/footer/".$this->get_slug()."-footer.php")){
      get_template_part('views/footer/'.$this->get_slug(),'footer');
    }else{
      get_template_part('views/footer/default','footer');
    }
    wp_footer();
  }

  function get_slug(){
    if(is_front_page()){
      return "frontpage";
    }elseif(is_page()){
      if($template = trim(basename(get_page_template_slug()))){
        $template = str_replace(".php","",$template);
        $template = trim($template);
        return $template;
      }else{
        return "page";
      }
    }elseif(is_category()){
      $elem = (get_queried_object());
      $cat = get_query_var('cat');
      return "category";
    }elseif(is_tag()){
      $elem = (get_queried_object());
      $cat = get_query_var('tag');
      return "tag";
    }elseif(is_tax()){
      $elem = (get_queried_object());
      $tax = get_query_var('taxonomy');
      return "taxonomy-".$tax;
    }elseif(is_archive()){
      $elem = (get_queried_object());
      $post_type = get_query_var('post_type');
      return "archive-".$post_type;
    }elseif(is_singular()){
      $elem = (get_queried_object());
      $post_type = $elem->post_type;
      return "single-".$post_type;
    }else{
      return "default";
    }
  }
}

add_filter('template_include', array('TDF_Wrapping', 'wrap'), 99);

<?php

/**
 * Utility functions
 */
function is_element_empty($element)
{
  $element = trim($element);
  return !empty($element);
}

// Tell WordPress to use searchform.php from the templates/ directory
function TDF_get_search_form()
{
  $form = '';
  locate_template('/templates/searchform.php', true, false);
  return $form;
}
add_filter('get_search_form', 'TDF_get_search_form');

/**
 * Add page slug to body_class() classes if it doesn't exist
 */
function TDF_body_class($classes)
{
  // Add post/page slug
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }
  return $classes;
}
add_filter('body_class', 'TDF_body_class');


add_action('wp_print_scripts', 'TDF_de_script', 100);

function TDF_de_script()
{
  wp_dequeue_script('bp-legacy-js');
  wp_deregister_script('bp-legacy-js');
}


if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
function my_jquery_enqueue()
{
  wp_deregister_script('jquery');
  wp_register_script('jquery', "https://code.jquery.com/jquery-1.12.4.js", false, null);
  wp_enqueue_script('jquery');
}



add_theme_support('category-thumbnails');
add_theme_support('post-thumbnails', array('page'));
add_theme_support('excerpt', array('page'));


add_action('init', 'TDF_featured_sizes');

function TDF_featured_sizes()
{
  if (isset($_GET["logout"])) {
    wp_logout();
    header("Location:" . get_bloginfo("url"));
    die();
  }

  add_image_size('square_crop', 300, 300, true);
  add_image_size('thumbnail2x', 300, 300, true);

  add_image_size('tiny_crop', 300, 200, true);
  add_image_size('smaller_crop', 450, 300, true);
  add_image_size('small_crop', 600, 400, true);
  add_image_size('medium_crop', 750, 500, true);
  add_image_size('large_crop', 1200, 800, true);

  add_image_size('wide_crop', 1200, 400, true);
}


add_action('template_include', 'TDF_redirects');
function TDF_redirects($template)
{
  if (!is_admin()) {
    include(locate_template('/config/redirects.php'));
  }
  wp_reset_query();

  return $template;
}

add_action('init', 'TDF_page_links');
function TDF_page_links()
{
  global $wpdb;
  define("PAGE_ID_FORGOT_PASS", 0);

  $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->postmeta . "` WHERE `meta_key`='_wp_page_template'");
  foreach ($results as $item) {
    $link = $item->meta_value;
    if ($link != "default") {
      $link_pieces = explode("/", $link);
      if (isset($link_pieces[3])) {
        $link_pieces[3] = str_replace("template-", "", $link_pieces[3]);
        $link_pieces[3] = str_replace(".php", "", $link_pieces[3]);
        $link_pieces[3] = str_replace("-", "_", $link_pieces[3]);

        define("PAGE_ID_" . strtoupper($link_pieces[3]), $item->post_id);
        define("PAGE_" . strtoupper($link_pieces[3]), get_permalink($item->post_id));
      }
    }
  }
}



add_filter('theme_templates', 'TDF_page_templates', 20, 4);

function TDF_page_templates($post_templates, $theme, $post, $post_type)
{
  //print_r($page_templates);
  if (is_admin()) {
    if ($post_type == "page") {
      $path    = get_stylesheet_directory() . '/views/pages/';
      $files = scandir($path);
      foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
          $fh = file_get_contents(get_stylesheet_directory() . "/views/pages/" . $file);
          $start = strpos($fh, "Template Name:");
          if ($start) {
            $start += 15;
            $nd = strpos($fh, "\n", $start);
            $str = trim(substr($fh, $start, $nd - $start));
          }
          if (isset($str)) {
            $page_templates["/views/pages/" . $file] = $str;
          } else {
            //$page_templates["/views/pages/".$file]= $file;
          }
        }
      }
      return $page_templates;
    }
  }
}


function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if (count($words) > $word_limit)
    array_pop($words);
  return implode(' ', $words);
}

function custom_excerpt_length($length)
{
  return 20;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);

function add_mce_markup($initArray)
{
  $ext = '*[*]';
  $initArray['extended_valid_elements'] = $ext;
  return $initArray;
}

add_filter('tdf_generate_name', 'tdf_generate_name');

function tdf_generate_name($name)
{
  $name = str_replace("_", " ", $name);
  $name = ucfirst($name);

  return $name;
}


add_filter('tiny_mce_before_init', 'add_mce_markup');

if (function_exists('acf_add_options_page')) {
  acf_add_options_page('Theme Settings');
}
function my_acf_init()
{
  acf_update_setting('google_api_key', 'AIzaSyBX4TXQxinwtAPodspFoS6w-y8_0rjhgOk');
}

add_action('acf/init', 'my_acf_init');

add_filter('show_admin_bar', '__return_false');

add_filter('style_loader_src',  'sdt_remove_ver_css_js', 9999, 2);
add_filter('script_loader_src', 'sdt_remove_ver_css_js', 9999, 2);

function sdt_remove_ver_css_js($src, $handle)
{
  $handles_with_version = ['style']; // <-- Adjust to your needs!

  if (strpos($src, 'ver=') && !in_array($handle, $handles_with_version, true))
    $src = remove_query_arg('ver', $src);

  return $src;
}

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// include custom jQuery
function shapeSpace_include_custom_jquery()
{

  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, false);
}
add_action('wp_enqueue_scripts', 'shapeSpace_include_custom_jquery', 11);

add_action('wp_enqueue_scripts', 'de_script', 999);

function de_script()
{
  if (!is_admin()) {
    wp_dequeue_style('bootstrap');

    wp_dequeue_script('bootstrap');
    wp_deregister_script('bootstrap');
    //
    wp_dequeue_script('jquery-migrate');
    wp_deregister_script('jquery-migrate');

    wp_dequeue_script('utils');
    wp_deregister_script('utils');

    wp_dequeue_script('bp-legacy-js');
    wp_deregister_script('bp-legacy-js');

    wp_dequeue_script('wp-embed');
    wp_deregister_script('wp-embed');

    wp_dequeue_script('word-count');
    wp_deregister_script('word-count');

    wp_dequeue_script('thickbox');
    wp_deregister_script('thickbox');


    wp_dequeue_script('shortcode');
    wp_deregister_script('shortcode');

    wp_dequeue_script('photon');
    wp_deregister_script('photon');

    wp_deregister_style('wp-color-picker');
    wp_dequeue_style('wp-color-picker');

    wp_deregister_style('thickbox');
    wp_dequeue_style('thickbox');

    wp_deregister_style('bootstrap');
    wp_dequeue_style('bootstrap');

    wp_deregister_style('material-wp_dynamic');
    wp_dequeue_style('material-wp_dynamic');

    wp_deregister_style('jetpack-widget-social-icons-styles');
    wp_dequeue_style('jetpack-widget-social-icons-styles');

    wp_deregister_style('roots_css');
    wp_dequeue_style('roots_css');

    wp_deregister_style('jetpack_css');
    wp_dequeue_style('jetpack_css');

    wp_deregister_style('jetpack');
    wp_dequeue_style('jetpack');
  }
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
// Remove Jetpack CSS
add_filter('jetpack_implode_frontend_css', '__return_false');

add_action('admin_head', 'tdf_admin_css');
add_action('login_enqueue_scripts', 'tdf_admin_css');

function tdf_admin_css()
{
  echo '<style>
    body:not(.wp-customizer):not(.vc_editor) #wpbody-content{ width: calc(100% - 380px) !important;}
    body.login h1 a{background-size: auto auto !important;}
    .wp-core-ui .button-primary {color: #000 !important;text-shadow: none !important;}
    #wpbd-edit-menu{display:none !important;}
    .ac-right{margin-right: -450px !important; margin-top: -40px !important;}
    .ac-admin{margin-right: 350px; max-width:100%;}
    #screen-meta-links .show-settings{background: none !important; text-shadow: 1px 1px 10px rgba(0,0,0,0.1);}
  </style>';
}

function cc_mime_types($mimes)
{
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

<?php
/**
 Plugin Name: Product Carousel Slider for Elementor
 Description: Product Carousel Slider for Elementor Lets you display your WooCommerce Products as Carousel Slider. You can now show your Products using this plugin easily to your users as a Carousel Slider
 Author: Plugin Devs
 Author URI: https://plugin-devs.com/
 Version: 1.0.0
 License: GPLv2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: wpce
*/

 // Exit if accessed directly.
 if ( ! defined( 'ABSPATH' ) ) { exit; }

 /**
  * Main class for News Ticker
  */
class WPCE_SLIDER
 {
 	
 	private static $instance;

	public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new WPCE_SLIDER();
            self::$instance->init();
        }
        return self::$instance;
    }

    //Empty Construct
 	function __construct(){}
 	
 	//initialize Plugin
 	public function init(){
 		$this->defined_constants();
 		$this->include_files();
		add_action( 'elementor/init', array( $this, 'wb_create_category') ); // Add a custom category for panel widgets
 	}

 	//Defined all constants for the plugin
 	public function defined_constants(){
 		define( 'WPCE_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WPCE_URL', plugin_dir_url( __FILE__ ) ) ;
		define( 'WPCE_VERSION', '1.0.0' ) ; //Plugin Version
		define( 'WPCE_MIN_ELEMENTOR_VERSION', '2.0.0' ) ; //MINIMUM ELEMENTOR Plugin Version
		define( 'WPCE_MIN_PHP_VERSION', '5.4' ) ; //MINIMUM PHP Plugin Version
		define( 'WPCE_PRO_LINK', 'https://plugin-devs.com/product/woocommerce-product-carousel-elementor/' ) ; //Pro Link
 	}

 	//Include all files
 	public function include_files(){

 		require_once( WPCE_PATH . 'functions.php' );
 		require_once( WPCE_PATH . 'admin/woo-product-slider-utils.php' );
 		if( is_admin() ){
 			require_once( WPCE_PATH . 'admin/admin-pages.php' );	
 			require_once( WPCE_PATH . 'class-plugin-deactivate-feedback.php' );	
 			require_once( WPCE_PATH . 'class-plugin-review.php' );	
 			require_once( WPCE_PATH . 'support-page/class-support-page.php' );	
 		}
 		//require_once( WPCE_PATH . 'admin/notices/support.php' );
 	}

 	//Elementor new category register method
 	public function wb_create_category() {
	   \Elementor\Plugin::$instance->elements_manager->add_category( 
		   	'web-builder-element',
		   	[
		   		'title' => esc_html( 'Web Builders Element', 'news-ticker-for-elementor' ),
		   		'icon' => 'fa fa-plug', //default icon
		   	],
		   	2 // position
	   );
	}

 	// prevent the instance from being cloned
    private function __clone(){}

    // prevent from being unserialized
    private function __wakeup(){}
 }

function wpce_slider_register_function(){
	$WPCE_SLIDER = WPCE_SLIDER::getInstance();
	
	if( is_admin() ){
		$wpce_feedback = new WPCE_Usage_Feedback(
			__FILE__,
			'webbuilders03@gmail.com',
			false,
			true
		);
	}
}
add_action('plugins_loaded', 'wpce_slider_register_function');

add_action('wp_footer', 'wpce_display_custom_css');
function wpce_display_custom_css(){
	$custom_css = get_option( 'wpce_custom_css' );
	$css ='';
	if ( ! empty( $custom_css ) ) {
		$css .= '<style type="text/css">';
		$css .= '/* Custom CSS */' . "\n";
		$css .= $custom_css . "\n";
		$css .= '</style>';
	}
	echo $css;
}


/**
 * Submenu filter function. Tested with Wordpress 4.1.1
 * Sort and order submenu positions to match your custom order.
 *
 */
function wpce_order_submenu( $menu_ord ) {

  global $submenu;

  // Enable the next line to see a specific menu and it's order positions
  //echo '<pre>'; print_r( $submenu['wpce-slider'] ); echo '</pre>'; exit();

  $arr = array();

  $arr[] = $submenu['wpce-slider'][1];
  $arr[] = $submenu['wpce-slider'][2];
  $arr[] = $submenu['wpce-slider'][5];
  $arr[] = $submenu['wpce-slider'][4];

  $submenu['wpce-slider'] = $arr;

  return $menu_ord;

}

// add the filter to wordpress
add_filter( 'custom_menu_order', 'wpce_order_submenu' );


/**
 * Setup Plugin Activation Time
 *
 * @since 1.0.1
 *
 */
register_activation_hook(__FILE__,  'wpce_setup_plugin_activation_time' );
add_action('upgrader_process_complete', 'wpce_setup_plugin_activation_time');
add_action('init', 'wpce_setup_plugin_activation_time');
function wpce_setup_plugin_activation_time(){
	$installation_time = get_option('wpce_installed_time');
	if( !$installation_time ){
		update_option('wpce_installed_time', current_time('timestamp'));
	}
}
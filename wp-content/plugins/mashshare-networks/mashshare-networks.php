<?php
/**
 * Plugin Name: Mashshare - Social Networks Add-On
 * Plugin URI: https://www.mashshare.net
 * Description: This Mashshare Add-On brings you 23 additional social networks and services.
 * Author: René Hermenau
 * Author URI: https://www.mashshare.net
 * Version: 2.4.5
 * Text Domain: mashnet
 * Domain Path: languages
 
 * MashshareNetworks Share Buttons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * MashshareNetworks Share Buttons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MashshareNetworks Share Buttons. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package MASHNET
 * @category Add-On
 * @author René Hermenau
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Global constant Version Numer
 * Must be defined out of main class MashshareNetworks to allow activation hook access to this constant 
 * Moreover easier for doing updates to have the version number on top of this file
 */
if ( ! defined( 'MASHNET_VERSION' ) ) {
        define( 'MASHNET_VERSION', '2.4.5' );
}

if ( ! class_exists( 'MashshareNetworks' ) ) :

/**
 * Main mashnet Class
 *
 * @since 1.0.0
 */
class MashshareNetworks {
	/** Singleton *************************************************************/

	/**
	 * @var MashshareNetworks The one and only MashshareNetworks
	 * @since 2.0.0
	 */
	private static $instance;
        
        /**
	 * MASHNET HTML Element Helper Object
	 *
	 * @var object
	 * @since 2.0.0
	 */
	//public $html;
	
	
	/**
	 * Main Instance
	 *
	 * Insures that only one instance of this Add-On exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 2.0.0
	 * @static
	 * @staticvar array $instance
	 * @uses mashshareNetworks::setup_constants() Setup the constants needed
	 * @uses mashshareNetworks::includes() Include the required files
	 * @uses mashshareNetworks::load_textdomain() load the language files
	 * @see MASHNET()
	 * @return The one true Add-On
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MashshareNetworks ) ) {
			self::$instance = new MashshareNetworks;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
                        self::$instance->hooks();
		}
		return self::$instance;
        }

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'MASHNET' ), '2.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 2.0.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'MASHNET' ), '2.0.0' );
	}
        
        /**
	 * Constructor Function
	 *
	 * @since 2.0
	 * @access protected
	 */
	public function __construct() {
		//self::$instance = $this;
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {
		global $wpdb, $mashnet_options; 

		// Plugin Folder Path
		if ( ! defined( 'MASHNET_PLUGIN_DIR' ) ) {
			define( 'MASHNET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'MASHNET_PLUGIN_URL' ) ) {
			define( 'MASHNET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'MASHNET_PLUGIN_FILE' ) ) {
			define( 'MASHNET_PLUGIN_FILE', __FILE__ );
		}
                
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 2.0.0
	 * @return void
	 */
	private function includes() {
            require_once MASHNET_PLUGIN_DIR . 'includes/scripts.php';
            require_once MASHNET_PLUGIN_DIR . 'includes/template-functions.php';
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                        require_once MASHNET_PLUGIN_DIR . 'includes/admin/welcome.php';
                        require_once MASHNET_PLUGIN_DIR . 'includes/admin/plugins.php';
                        require_once MASHNET_PLUGIN_DIR . 'includes/admin/settings.php';
		}
                require_once MASHNET_PLUGIN_DIR . 'includes/install.php';
	}
        
        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       2.0.0
         * @return      void
         *
         * @todo        The hooks listed in this section are a guideline, and
         *              may or may not be relevant to your particular extension.
         *              Please remove any unnecessary lines, and refer to the
         *              WordPress codex and MASHSB  documentation for additional
         *              information on the included hooks.
         *
         *              This method should be used to add any filters or actions
         *              that are necessary to the core of your extension only.
         *              Hooks that are relevant to meta boxes, widgets and
         *              the like can be placed in their respective files.
         *
         *              IMPORTANT! If you are releasing your extension as a
         *              commercial extension in the MASHSB store, DO NOT remove
         *              the license check!
         */
        private function hooks() {
             /* Instantiate class MASHNET_licence 
             * Create 
             * @since 2.0.0
             * @return apply_filter mashsb_settings_licenses and create licence key input field in core mashsbs
             */
            if (class_exists('MASHSB_License')) {
                $mashsb_sl_license = new MASHSB_License(__FILE__, 'Mashshare Social Networks Add-On', MASHNET_VERSION, 'Rene Hermenau', 'edd_sl_license_key'); 
            }
        }

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
		$mashnet_lang_dir = dirname( plugin_basename( MASHNET_PLUGIN_FILE ) ) . '/languages/';
		$mashnet_lang_dir = apply_filters( 'mashnet_languages_directory', $mashnet_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'mashnet' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'mashnet', $locale );

		// Setup paths to current locale file
		$mofile_local  = $mashnet_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/mashnet/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/MASHNET folder
			load_textdomain( 'mashnet', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/mashshare/languages/ folder
			load_textdomain( 'mashshare-networks', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'mashnet', false, $mashnet_lang_dir );
		}
                
	}
        
        
        /* Activation function fires when the plugin is activated.  
         * Checks first if multisite is enabled
         * @since 2.1.1
         * 
         */

        public static function activation($networkwide) {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                if ($networkwide) {
                    $old_blog = $wpdb->blogid;
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        MashshareNetworks::mashnet_during_activation();
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            MashshareNetworks::mashnet_during_activation();
        }
        
        /**
	 * This function is fired from the activation method.
	 *
	 * @since 2.1.1
	 * @access public
	 *
	 * @return void
	 */
	 public static function mashnet_during_activation() {
           global $wpdb;
         
            // Add Upgraded From Option
            $current_version = get_option('mashnet_version');
            if ($current_version) {
                update_option('mashnet_version_upgraded_from', $current_version);
            }

            // Add the current version
            update_option('mashnet_version', MASHNET_VERSION);
            // Add the transient to redirect
            set_transient('_mashnet_activation_redirect', true, 30);

            /* Upgrade routine
             * Get current networks, count them and add the additional ones dependant on version number
             */
            
            $add_networks_core = array(
                'Facebook',
                'Twitter',
                'Subscribe'
            );
            
            $add_networks_v_1 = array(
                'Google',
                'Whatsapp',
                'Pinterest',
                'Digg',
                'Linkedin',
                'Reddit',
                'Stumbleupon',
                'Vk',
                'Print',
                'Delicious',
                'Buffer',
                'Weibo',
                'Pocket',
                'Xing',
                'Tumblr'
            );
            
            $add_networks_v_2 = array(
                'Mail'
            );
            
            $add_networks_v_3 = array(
                'Meneame',
                'Odnoklassniki',
                'Managewp'
            );
            
            $add_networks_v_4 = array(
                'Mailru',    
                'Line'
            );
            
            $add_networks_v_5 = array (
                'yummly'
            );
            
            $add_networks_v_6 = array (
                'frype',
                'skype'
            );
            
            $add_networks_v_7 = array (
                'Telegram'
            );
            
            $add_networks_v_8 = array (
                'Flipboard',
                'Hackernews'
            );
           
            
            
            // First time activation of mashshare networks
            $current_networks = get_option('mashsb_networks');
            //This runs when something went wrong and mashsb_networks is total emtpy. Should not happen but who knows*/
            if ( empty($current_networks) ) {
                $new_networks = array_merge($add_networks_core, $add_networks_v_1, $add_networks_v_2, $add_networks_v_3, $add_networks_v_4, $add_networks_v_5, $add_networks_v_6, $add_networks_v_7, $add_networks_v_8);
                update_option('mashsb_networks', $new_networks);
                return;
            }

            /* First time activation of mashshare networks when only Facebook, Twitter and Subscribe exist */
            if (count($current_networks) === 3) {
                $new_networks = array_merge($current_networks, $add_networks_v_1, $add_networks_v_2, $add_networks_v_3, $add_networks_v_4, $add_networks_v_5, $add_networks_v_6, $add_networks_v_7, $add_networks_v_8);
                update_option('mashsb_networks', $new_networks);  
                return;
            }
           
            
            /* UPDATE: We use an version earlier than 2.0.4 of mashshare networks
             * @since 2.0.4
             */
            if ( version_compare( $current_version, '2.0.4', '<=' ) and version_compare( $current_version, '2.0.0', '>=' )  ) {
                $new_networks = array_merge($current_networks, $add_networks_v_2);
                update_option('mashsb_networks', $new_networks);    
            }
            
            /* UPDATE: We use an version earlier than 2.0.8 of mashshare networks
             * @since 2.0.8
             */
            if ( version_compare( $current_version, '2.0.8', '<' ) and version_compare( $current_version, '2.0.4', '>=' )  ) {
                $new_networks = array_merge($current_networks, $add_networks_v_3, $add_networks_v_4);
                update_option('mashsb_networks', $new_networks);   
            }
            
             /* UPDATE: We use an version earlier than 2.1.8 of mashshare networks
             * @since 2.1.8
             */
            if ( version_compare( $current_version, '2.1.8', '<' ) and version_compare( $current_version, '2.0.8', '>=' )  ) {
                $new_networks = array_merge($current_networks, $add_networks_v_4);
                update_option('mashsb_networks', $new_networks);   
            }
            
            /* UPDATE: We use an version earlier than 2.1.9 of mashshare networks
             * @since 2.1.9
             */
            if ( version_compare( $current_version, '2.1.9', '<' ) and version_compare( $current_version, '2.1.8', '>=' )  ) {
                $new_networks = array_merge($current_networks, $add_networks_v_5);
                update_option('mashsb_networks', $new_networks);   
            }
            
            /* UPDATE: We use an version earlier than 2.2.6 but newer or same than 2.1.9 of mashshare networks
             * @since 2.2.6
             */
            if ( version_compare( $current_version, '2.2.6', '<' ) and version_compare( $current_version, '2.1.9', '>=' )  ) {
                $new_networks = array_merge($current_networks, $add_networks_v_6);
                update_option('mashsb_networks', $new_networks);   
            }
            
            /* UPDATE: We use an version earlier than 2.3.5 but newer or same than 2.2.6 of mashshare networks
             * @since 2.3.5
             */
            //if ( version_compare( $current_version, '2.3.6', '<' ) and version_compare( $current_version, '2.2.6', '>=' )  ) {
            if( !isset( $current_networks['Telegram'] ) ) {
                $new_networks_v_7 = array_merge($current_networks, $add_networks_v_7);
                update_option('mashsb_networks', $new_networks_v_7);   
            }
            
            if( !isset( $current_networks['Flipboard'] ) && !isset( $current_networks['Hackernews'] ) ) {
                $new_networks_v_8 = array_merge($current_networks, $add_networks_v_8);
                update_option('mashsb_networks', $new_networks_v_8);   
            }
            
            // Remove duplicate entries because of broken update in 2.3.7
            $dirty_networks = get_option('mashsb_networks');
            $cleaned_networks = array_unique($dirty_networks);
            update_option('mashsb_networks', $cleaned_networks);  
            
            /* 
             * Fix inconsistent pinterest setting
             * 
             * UPDATE: We use an version earlier than 2.3.1
             * @since 2.3.1
             */
            if( version_compare( $current_version, '2.3.1', '<' ) ) {
                $settings = get_option( 'mashsb_settings' );
                if( isset( $settings['mashnet_pinterest_selection'] ) && $settings['mashnet_pinterest_selection'] === '1' ) {
                    $settings['mashnet_pinterest_selection'] = '0';
                } else {
                    $settings['mashnet_pinterest_selection'] = '1';
                }
                update_option( 'mashsb_settings', $settings );
            }
        }        
}




/**
 * The main function responsible for returning the one true Add-On
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $MASHNET = MASHNET(); ?>
 *
 * @since 2.0.0
 * @return object The one true MashshareNetworks Instance
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where this extension is activated but MASHSB is not
 *              present.
 */

function MASHNET() {
    if( ! class_exists( 'Mashshare' ) ) {
        if( ! class_exists( 'MASHSB_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new MASHSB_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
        return MashshareNetworks::instance();
    } else {
        return MashshareNetworks::instance();
    }
}

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class hence, needs to be called outside and the
 * function also needs to be static.
 */
register_activation_hook( __FILE__, array( 'MashshareNetworks', 'activation' ) );

// Get MASHNET Running after other plugins loaded
add_action( 'plugins_loaded', 'MASHNET' );
//MASHNET();

endif; // End if class_exists check
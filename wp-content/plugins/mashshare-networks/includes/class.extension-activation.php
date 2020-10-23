<?php
/**
 * Activation handler checks if MASHSB is running before activating the Add-On
 *
 * @package     MASHNET\ActivationHandler
 * @since       2.0.0
 * 
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * MASHSB Extension Activation Handler Class
 *
 * @since       2.0.0
 */
class MASHSB_Extension_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_mashsb;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        $this->plugin_name = str_replace( 'Mashshare - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );

        // Is MASHSB installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'Mashshare Share Buttons' ) {
                $this->has_mashsb = true;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        //Deactivate this plugin - disabled! Because update process would kill the Add-On
        //deactivate_plugins( $this->plugin_path . '/' . $this->plugin_file );
        //unset( $_GET['activate'] );

        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_mashsb_notice' ) );
    }


    /**
     * Display notice if MASHSB isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_mashsb_notice() {
        if( $this->has_mashsb ) {
            echo '<div class="error"><p>' . $this->plugin_name . __( ' requires Mashshare! Please activate it to continue!', 'mashnet' ) . '</p></div>';
        } else {
            echo '<div class="error"><p>' . $this->plugin_name . __( ' requires Mashshare! Please install it to continue!', 'mashnet' ) . '</p></div>';
        }
    }
}
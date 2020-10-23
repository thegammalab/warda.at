<?php
/**
 * Uninstall Mashshare Networks
 *
 * @package     MASHSB
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load MASHSB file
include_once( 'mashshare-networks.php' );

global $wpdb, $mashsb_options;

if (class_exists('Mashshare')) {
    if( mashsb_get_option( 'uninstall_on_delete' ) ) {
            /** Delete the additional Add-On Options 
             * todo: get array and delete all additional networks. Keep the original ones
             */
            //delete_option( 'mashsb_networks');
    }
}
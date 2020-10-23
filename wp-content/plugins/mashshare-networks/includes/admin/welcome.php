<?php
/**
 * Weclome Page Class
 *
 * @package     MASHNET
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * MASHNET_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class MASHNET_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Sends user to the Settings page on first activation of MASHNET as well as each
	 * time MASHNET is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0.1
	 * @global $mashnet_options Array of all the MASHNET Options
	 * @return void
	 */
	public function welcome() {
		global $mashnet_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_mashnet_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_mashnet_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$upgrade = get_option( 'mashnet_version_upgraded_from' );

		if (class_exists( 'Mashshare' )) { // First time install
			wp_safe_redirect( admin_url( 'admin.php?page=mashsb-settings&tab=networks' ) ); exit;
		} else { // Update
			/*nothing here*/
		}
	}
}
new MASHNET_Welcome();

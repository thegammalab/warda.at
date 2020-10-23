<?php
/**
 * Install Function
 *
 * @package     MASHNET
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 * @deprecated since version 2.0.8
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * mashnet_after_install hook.
 *
 * @since 2.0
 * @return void
 */
function mashnet_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	$activation_pages = get_transient( '_mashnet_activation_pages' );

	// Exit if not in admin or the transient doesn't exist
	if ( false === $activation_pages ) {
		return;
	}

	// Delete the transient
	delete_transient( '_mashnet_activation_pages' );

	do_action( 'mashnet_after_install', $activation_pages );
}
add_action( 'admin_init', 'mashnet_after_install' );
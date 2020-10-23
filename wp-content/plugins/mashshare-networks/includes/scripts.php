<?php

/**
 * Scripts
 *
 * @package     MASHNET
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */
// Exit if accessed directly
if ( !defined('ABSPATH') )
    exit;

/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 2.0.0
 * @global $mashnet_options
 * @global $post
 * @return void
 */
function mashnet_load_scripts($hook) {
    global $mashsb_options, $post;
    if ( function_exists('mashsbGetActiveStatus') ) {
        if ( !apply_filters('mashsb_load_scripts', mashsbGetActiveStatus(), $hook) ) {
            mashdebug()->info("mashsb_load_script not active");
            return;
        }
    }

    $js_dir = MASHNET_PLUGIN_URL . 'assets/js/';
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    isset($mashsb_options['load_scripts_footer']) ? $in_footer = true : $in_footer = false;

    $pinterest_select = isset($mashsb_options['mashnet_pinterest_selection']) ? '1' : '0';

    wp_enqueue_script('mashnet', $js_dir . 'mashnet' . $suffix . '.js', array('jquery'), MASHNET_VERSION, $in_footer);
    wp_localize_script('mashnet', 'mashnet', array(
        'body' => !empty($mashsb_options['mashnet_bodytext']) ? $mashsb_options['mashnet_bodytext'] : '',
        'subject' => !empty($mashsb_options['mashnet_subjecttext']) ? $mashsb_options['mashnet_subjecttext'] : '',
        'pinterest_select' => $pinterest_select,
    ));
}

add_action('wp_enqueue_scripts', 'mashnet_load_scripts');

/**
 * Register Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 2.0.0
 * @global $mashnet_options
 * @param string $hook Page hook
 * @return void
 */
function mashnet_register_styles($hook) {
    global $mashnet_options;
    if ( function_exists('mashsbGetActiveStatus') ) {
        if ( !apply_filters('mashsb_load_scripts', mashsbGetActiveStatus(), $hook) ) {
            mashdebug()->info("mashsb_load_script not active");
            return;
        }
    }

    if ( isset($mashnet_options['disable_styles']) ) {
        return;
    }

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
    $file = 'mashnet' . $suffix . '.css';

    $url = MASHNET_PLUGIN_URL . 'assets/css/' . $file;
    wp_enqueue_style('mashnet-styles', $url, array(), MASHNET_VERSION);
}

add_action('wp_enqueue_scripts', 'mashnet_register_styles');


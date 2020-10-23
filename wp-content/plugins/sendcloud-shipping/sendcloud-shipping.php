<?php
/**
 * Plugin Name: SendCloud | Smart Shipping Service
 * Plugin URI: http://sendcloud.sc
 * Description: SendCloud plugin.
 * Version: 1.1.2
 * Author: SendCloud B.V.
 * Author URI: http://sendcloud.sc
 * Requires at least: 4.5.0
 * Tested up to: 5.3
 *
 * Text Domain: sendcloud-shipping
 * Domain Path: /languages/
 *
 * @package sendcloud-shipping
 * @category Core
 * @author SendCloud B.V.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'SENDCLOUDSHIPPING_SERVICE_POINT_SCRIPT', 'sendcloudshipping_service_point_script' );
define( 'SENDCLOUDSHIPPING_SERVICE_POINT_CARRIERS', 'sendcloudshipping_service_point_carriers' );
define( 'SENDCLOUDSHIPPING_SM_SERVICE_POINT', 'service_point_shipping_method' );
define( 'SENDCLOUDSHIPPING_SERVICE_POINT_SELECTED', 'sendcloudshipping_service_point_selected' );
define( 'SENDCLOUDSHIPPING_SERVICE_POINT_EXTRA', 'sendcloudshipping_service_point_extra' );
define( 'SENDCLOUDSHIPPING_SERVICE_POINT_META', 'sendcloudshipping_service_point_meta' );
define( 'SENDCLOUDSHIPPING_VERSION', '1.1.2' );

register_activation_hook(__FILE__, 'sendcloudshipping_activate');

add_action('init', 'sendcloudshipping_init');
add_action('plugins_loaded', 'sendcloudshipping_bootstrap');

function sendcloudshipping_activate() {
    if (!class_exists('WooCommerce')) {
        return;
    }
}

function sendcloudshipping_init() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    load_plugin_textdomain('sendcloud-shipping', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function sendcloudshipping_add_service_point_shipping_method( $methods ) {
    $script = get_option(SENDCLOUDSHIPPING_SERVICE_POINT_SCRIPT);
    if ( !empty($script) ) {
        if ( version_compare( WC()->version, '2.6', ">=" ) ) {
            $methods[SENDCLOUDSHIPPING_SM_SERVICE_POINT] = 'SendCloudShipping_Service_Point_Shipping_Method';
        } else {
            $methods[] = 'SendCloudShipping_Service_Point_Shipping_Method';
        }
    }
    return $methods;
}

function sendcloudshipping_service_point_shipping_method_init(){
    require_once 'includes/shipping/class-service-point-shipping-method.php';
}

function sendcloudshipping_load_api() {
    include_once 'includes/api/class-sc-api-service-point.php';
}

function sendcloudshipping_add_api( $apis ) {
    $apis[] = 'SendCloudShipping_API_ServicePoint';
    return $apis;
}

function sendcloudshipping_bootstrap() {
    if (!class_exists('WooCommerce')) {
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        deactivate_plugins(plugin_basename(__FILE__));
        add_action('admin_notices', 'sendcloudshipping_deactivate_notice');
    } else {
        include_once 'includes/checkout.php';
        include_once 'includes/email.php';

        add_action('woocommerce_api_loaded', 'sendcloudshipping_load_api');
        add_filter('woocommerce_api_classes', 'sendcloudshipping_add_api');
        add_action('woocommerce_shipping_init', 'sendcloudshipping_service_point_shipping_method_init');
        add_filter('woocommerce_shipping_methods', 'sendcloudshipping_add_service_point_shipping_method');
        add_filter('woocommerce_get_settings_pages', 'sendcloudshipping_settings_page');
    }
}

function sendcloudshipping_deactivate_notice() {
    $message = __(
        'SendCloud | Smart Shipping Service plugin deactivated itself because WooCommerce is not available.',
        'sendcloud-shipping');
    printf('<div class="error"><p>%s</p></div>', $message);
}


function sendcloudshipping_settings_page($settings) {
    $settings[] = include('includes/class-sc-settings-sendcloud.php');
    return $settings;
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists('SendCloudShipping_API_ServicePoint') ):

class SendCloudShipping_API_ServicePoint extends WC_API_Resource {
    protected $base = '/sendcloudshipping';

    public function register_routes( $routes ) {

        # POST|DELETE /service_point
        $routes[ $this->base . '/service_point' ] = array(
            array( array( $this, 'enable_service_point' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
            array( array( $this, 'disable_service_point' ), WC_API_Server::DELETABLE ),
        );

        $routes[ $this->base . '/version' ] = array(
            array( array( $this, 'check_version' ), WC_API_SERVER::READABLE ),
        );

        return $routes;
    }

    public function enable_service_point($data) {
        if ( ! isset( $data['script'] ) ) {
            return new WP_Error( 'sendcloudshipping_api_missing_script_data', __( 'No data specified to enable the plugin', 'sendcloud-shipping' ), 400 );
        }

        update_option(SENDCLOUDSHIPPING_SERVICE_POINT_SCRIPT, $data['script']);
        update_option(SENDCLOUDSHIPPING_SERVICE_POINT_CARRIERS, $data['carriers']);
        return array( 'message' => __( 'Plugin enabled', 'sendcloud-shipping' ));
    }

    public function disable_service_point() {
        delete_option(SENDCLOUDSHIPPING_SERVICE_POINT_SCRIPT);
        return array( 'message' => __( 'Plugin disabled', 'sendcloud-shipping' ) );
    }

    public function check_version() {
        global $wp_version;
        $version = array(
            'wordpress' => $wp_version,
            'woocommerce' => WC()->version,
            'sendcloud' => SENDCLOUDSHIPPING_VERSION,
        );
        return $version;
    }

}
endif;

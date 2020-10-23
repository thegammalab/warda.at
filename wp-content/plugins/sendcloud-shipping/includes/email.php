<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


add_action( 'woocommerce_email_after_order_table', 'sendcloudshipping_email_after_order_table', 15, 2 );

function sendcloudshipping_email_after_order_table($order, $sent_to_admin) {
    if ( version_compare( WC()->version, '3.0', ">=" ) ) {
        $order_id = $order->get_id();
    } else {
        $order_id = $order->id;
    }
    $service_point = get_post_meta( $order_id, SENDCLOUDSHIPPING_SERVICE_POINT_META );
    if ($service_point) {
        $address = join('<br>', explode('|', $service_point[0]['extra']));
        echo '<h3>' . __('Service Point Address', 'sendcloud-shipping') . '</h3>';
        echo '<p>' . $address . '</p>';
    }
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'woocommerce_checkout_after_order_review', 'sendcloudshipping_add_service_point_to_checkout' );
add_action( 'woocommerce_after_shipping_rate', 'sendcloudshipping_add_extra_shiping_method' );
add_action( 'woocommerce_checkout_process', 'sendcloudshipping_checkout_process' );
add_action( 'woocommerce_checkout_update_order_meta', 'sendcloudshipping_checkout_update_order_meta' );
add_action( 'woocommerce_thankyou', 'sendcloudshipping_order_details_table', 11 );
add_action( 'woocommerce_view_order', 'sendcloudshipping_order_details_table', 11 );
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'sendcloudshipping_admin_order_data_after_shipping_address', 11 );

function sendcloudshipping_cart_max_dimensions() {
    $dimensions = array();
    $cart = WC()->cart;

    foreach ( $cart->get_cart() as $cart_item_key => $values ) {
        $product = $values['data'];
        if ( $product->has_dimensions() )
        {
            $dimensions[] = array(
                $product->get_length(),
                $product->get_width(),
                $product->get_height()
            );
        }
    }
    return $dimensions;
}

function sendcloudshipping_add_service_point_to_checkout() {
    $script = get_option(SENDCLOUDSHIPPING_SERVICE_POINT_SCRIPT);

    if( empty($script) ) {
        return;
    }

    $parts = explode("_", get_locale());
    $language = $parts[0];

    // Double encode dimensions because we want to pass the string as one GET param
    $cart_dimensions = base64_encode(json_encode(sendcloudshipping_cart_max_dimensions()));
    $cart_dimensions_unit = json_encode(get_option('woocommerce_dimension_unit'));

    $select_spp_label = __('Select Service Point', 'sendcloud-shipping');

    echo <<<EOT
<script type="text/javascript">
var SENDCLOUDSHIPPING_LANGUAGE = "$language";
var SENDCLOUDSHIPPING_SELECT_SPP_LABEL = "$select_spp_label";
var SENDCLOUDSHIPPING_DIMENSIONS = "$cart_dimensions";
var SENDCLOUDSHIPPING_DIMENSIONS_UNIT = $cart_dimensions_unit;
</script>
<script type="text/javascript" data-cfasync="false" src="$script"></script>
EOT;
}

function sendcloudshipping_add_extra_shiping_method($method) {
    if ($method->method_id == SENDCLOUDSHIPPING_SM_SERVICE_POINT) {
        $method_options = get_option( 'sendcloudshipping_service_point_shipping_method_'. $method->instance_id .'_settings' );
        $field_id = $method->id . ':carrier_select';
        $carrier_select = isset($method_options["carrier_select"]) ? $method_options["carrier_select"] : '';
        echo '<input type="hidden" id="'. esc_attr($field_id) . '" value="'.esc_attr($carrier_select).'">';
    }
}

function sendcloudshipping_checkout_process() {
    $servicePointSM = '';

    if (isset($_POST['shipping_method'][0])) {
        $parts = explode(':', $_POST['shipping_method'][0]);
        $servicePointSM = $parts[0];
    }

    $servicePointSelected = isset($_POST[SENDCLOUDSHIPPING_SERVICE_POINT_SELECTED]) && !empty($_POST[SENDCLOUDSHIPPING_SERVICE_POINT_SELECTED]);
    if ( $servicePointSM == SENDCLOUDSHIPPING_SM_SERVICE_POINT && !$servicePointSelected ) {
        wc_add_notice( __( 'Please choose a service point.', 'sendcloud-shipping' ), 'error' );
    }
}

function sendcloudshipping_checkout_update_order_meta( $order_id ) {
    if ( isset($_POST[SENDCLOUDSHIPPING_SERVICE_POINT_SELECTED]) ) {
        $service_point = array(
            'id' => $_POST[SENDCLOUDSHIPPING_SERVICE_POINT_SELECTED],
            'extra' => $_POST[SENDCLOUDSHIPPING_SERVICE_POINT_EXTRA],
        );
        update_post_meta( $order_id, SENDCLOUDSHIPPING_SERVICE_POINT_META, $service_point);
    }
}

function sendcloudshipping_admin_order_data_after_shipping_address( $order ) {
    if ( version_compare( WC()->version, '3.0', ">=" ) ) {
        $order_id = $order->get_id();
    } else {
        $order_id = $order->id;
    }

    $service_point = get_post_meta($order_id, SENDCLOUDSHIPPING_SERVICE_POINT_META);
    if ( $service_point ) {
        $address = join('<br>', explode('|', $service_point[0]['extra']));
        $service_point_address = __('Service Point Address', 'sendcloud-shipping');
        $help_tip_label = __('Non editable', 'sendcloud-shipping');
        $help_tip_content = wc_help_tip( __( "You can't change the selected Service Point", 'sendcloud-shipping') );
        echo <<<EOT

   <div class="address">
   <h3>$service_point_address</h3>
      $address
      <br>
      <span class="description">$help_tip_content $help_tip_label</span>
   </div>
EOT;
    }
}

function sendcloudshipping_order_details_table( $order_id ) {
    $service_point = get_post_meta($order_id, SENDCLOUDSHIPPING_SERVICE_POINT_META);
    if ( $service_point ) {
        $address = join('<br>', explode('|', $service_point[0]['extra']));
        $service_point_address = __('Service Point Address', 'sendcloud-shipping');
        echo <<<EOT
<div class="col2-set addresses">
  <div class="col1">
    <header class="title">
      <h3>$service_point_address</h3>
    </header>
    <address>
      $address
    </address>
   </div>
</div>
EOT;
    }
}

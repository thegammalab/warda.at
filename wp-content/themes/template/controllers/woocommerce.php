<?php
if ( ! class_exists('TDF_Woocommerce') ){

  class TDF_Woocommerce {

    public function __construct() {
      add_filter( 'tdf_woocommerce_item_buy_button', array( $this, 'buy_item_button' ), 10, 3);
      add_filter( 'tdf_woocommerce_single_buy_button', array( $this, 'buy_single_button' ), 10, 3);
      add_filter( 'tdf_woocommerce_item_price', array( $this, 'get_price_html' ), 10, 2);
      add_filter('woocommerce_form_field_args',array( $this, 'add_form_fields'),10,3);
    }
    function add_form_fields( $args, $key, $value = null ) {

    	// Start field type switch case
    	switch ( $args['type'] ) {
    		case "select" :  /* Targets all select input type elements, except the country and state select input types */
    			$args['class'][] = 'form-group'; // Add a class to the field's html element wrapper - woocommerce input types (fields) are often wrapped within a <p></p> tag
    			$args['input_class'] = array('form-control woo-tdf-field', 'input-lg'); // Add a class to the form input itself
    			//$args['custom_attributes']['data-plugin'] = 'select2';
    			$args['label_class'] = array('control-label');
    			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  ); // Add custom data attributes to the form input itself
    		break;
    		case 'country' : /* By default WooCommerce will populate a select with the country names - $args defined for this specific input type targets only the country select element */
    			$args['class'][] = 'form-group single-country';
    			$args['label_class'] = array('control-label');
    		break;
    		case "state" : /* By default WooCommerce will populate a select with state names - $args defined for this specific input type targets only the country select element */
    			$args['class'][] = 'form-group'; // Add class to the field's html element wrapper
    			$args['input_class'] = array('form-control woo-tdf-field', 'input-lg'); // add class to the form input itself
    			//$args['custom_attributes']['data-plugin'] = 'select2';
    			$args['label_class'] = array('control-label');
    			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  );
    		break;
    		case "password" :
    		case "text" :
    		case "email" :
    		case "tel" :
    		case "number" :
    			$args['class'][] = 'form-group';
    			//$args['input_class'][] = 'form-control woo-tdf-field input-lg'; // will return an array of classes, the same as bellow
    			$args['input_class'] = array('form-control woo-tdf-field', 'input-lg');
    			$args['label_class'] = array('control-label');
    		break;
    		case 'textarea' :
    			$args['input_class'] = array('form-control woo-tdf-field', 'input-lg');
    			$args['label_class'] = array('control-label');
    		break;
    		case 'checkbox' :
    		break;
    		case 'radio' :
    		break;
    		default :
    			$args['class'][] = 'form-group';
    			$args['input_class'] = array('form-control woo-tdf-field', 'input-lg');
    			$args['label_class'] = array('control-label');
    		break;
    	}
    	return $args;
    }


    function buy_item_button($prod_id,$text,$args=array()){
      if(isset($args["classes"])){
        $classes = $args["classes"];
      }else{
        $classes = "";
      }
      return '<a href="' . get_bloginfo("url") . '/?add-to-cart=' . $prod_id . '" data-quantity="1" class="add_to_cart_button ajax_add_to_cart" data-product_id="' . $prod_id . '" data-product_sku="" aria-label="' . get_the_title($prod_id) . '" rel="nofollow"><button class="'.$classes.'">'.$text.'</button></a>';
    }

    function buy_single_button($prod_id,$text,$args=array()){
      if(isset($args["classes"])){
        $classes = $args["classes"];
      }else{
        $classes = "";
      }
      return '<a href="' . get_the_permalink($prod_id) . '/?add-to-cart=' . $prod_id . '" aria-label="' . get_the_title($prod_id) . '" rel="nofollow"><button class="'.$classes.'">'.$text.'</button></a>';
    }

    function get_price_html($prod_id,$args=array()){
      $prices = get_post_meta($prod_id,"_price",false);
      $the_price = (string) number_format($prices[0],2);
      $the_price_pieces = explode(".",$the_price);
      $output ="";
      if(count($prices)>1){
        $output .= '<span>From:</span>';
      }
      $output .= ''.get_woocommerce_currency_symbol().$the_price_pieces[0].'<sup style="font-size: 60%;">.'.$the_price_pieces[1].'</sup>';

      return $output;
    }
  }

  new TDF_Woocommerce;
}

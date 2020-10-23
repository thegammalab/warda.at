<?php

/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
	return;
}
?>
<div class="row">
	<div class="col-3 col-md-2">
		<div class="cart_image">
			<?php echo get_the_post_thumbnail($product->get_ID(), "thumbnail"); ?>
		</div>
	</div>
	<div class="col-9 col-md-10">
		<div class="row align-items-center align-items-md-start">
			<div class="col-8 col-md-8">
				<?php
				$is_visible        = $product && $product->is_visible();
				$product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);
				?>
				<h5><?php echo apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible); ?></h5>
				<?php
				do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);
				wc_display_item_meta($item);
				do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
				?>
				<div class="product_price">
					<?php echo $order->get_formatted_line_subtotal($item); ?>
				</div>
			</div>
			<div class="col-4 col-md-4 number_of_products">
				<div class="text-right quantity">
					<input type="text" class="qty" value="<?= $item->get_quantity(); ?>" disabled />
				</div>
			</div>

		</div>
	</div>
</div>
<hr />
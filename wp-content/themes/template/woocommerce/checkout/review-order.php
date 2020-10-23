<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class=" woocommerce-checkout-review-order-table cart_total_box mb-0">
	<table class="shop_table mb-0 cart_totals_table">

		<tbody>

			<tr class="cart-subtotal">
				<th><?php _e('Subtotal', 'woocommerce'); ?></th>
				<td><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
				<th><?php wc_cart_totals_coupon_label($coupon); ?></th>
				<td><?php wc_cart_totals_coupon_html($coupon); ?></td>
			</tr>
			<?php endforeach; ?>

			<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) { ?>

			<?php do_action('woocommerce_review_order_before_shipping'); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action('woocommerce_review_order_after_shipping'); ?>

			<?php } else { ?>
			<tr class="shipping">
				<th><?php _e('Shipping', 'woocommerce'); ?></th>
				<td data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>"><label>Pickup at school</label>
					<small class="d-block" style="text-transform: none; font-size: 12px; line-height: 1.2;">You will be able to pick up your items at the reception</small>

				</td>
			</tr>
			<?php } ?>

			<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee">
				<th><?php echo esc_html($fee->name); ?></th>
				<td><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
			<?php endforeach; ?>

			<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
			<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
			<tr class="tax-rate tax-rate-<?php echo sanitize_title($code); ?>">
				<th><?php echo esc_html($tax->label); ?></th>
				<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
			</tr>
			<?php endforeach; ?>
			<?php else : ?>
			<tr class="tax-total">
				<th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
				<td><?php wc_cart_totals_taxes_total_html(); ?></td>
			</tr>
			<?php endif; ?>
			<?php endif; ?>

			<?php do_action('woocommerce_review_order_before_order_total'); ?>

			<tr class="order-total">
				<th><?php _e('Total', 'woocommerce'); ?></th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action('woocommerce_review_order_after_order_total'); ?>

		</tbody>
	</table>
</div>
<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	if ( ! get_current_user_id() ) {
		if ( $cart_item['product_id'] == 3284 ) {
			$has_sub = 1;
		} else {
			wp_redirect( get_bloginfo( 'url' ) );
		}
	}
}

if ( $has_sub ) {
	?>
	<div class="header_titles mb-6">
		<h2>Subscribe to</h2>
		<h1>Limited Edition</h1>
	</div>
	<?php // do_action('woocommerce_before_checkout_form', $checkout); ?>
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>
			<h6 class="text-center mb-6"><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h6>
		<?php else : ?>
			<h6 class="text-center mb-6"><?php esc_html_e( 'Billing information', 'woocommerce' ); ?></h6>
		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<div id="customer_details">
			<div><?php do_action( 'woocommerce_checkout_billing' ); ?></div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<div class="mt-7">
			<h6 class="text-center mb-6"><?php esc_html_e( 'About Your Subscription', 'woocommerce' ); ?></h6>
		</div>

		<div class="color_box">
			<div class="row">
			<div class="col-md-6">
				<h6 class="color1 text-uppercase">1 yr subscription to limited edition</h6>
				<div class="subscr_price">
					<h2>81.90</h2>
					<h6>/year<br>renewed yearly</h6>
				</div>
			</div>
			<div class="col-md-6">
				<?php
				if ( ! is_ajax() ) {
					do_action( 'woocommerce_review_order_before_payment' );
				}
				?>
				<div id="payment" class="woocommerce-checkout-payment">
					<?php if ( WC()->cart->needs_payment() ) : ?>
						<ul class="wc_payment_methods payment_methods methods">
							<?php
							if ( ! empty( $available_gateways ) ) {
								foreach ( $available_gateways as $gateway ) {
									wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
								}
							} else {
								echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')) . '</li>'; // @codingStandardsIgnoreLine
							}
							?>
						</ul>
					<?php endif; ?>
				</div>
				<?php
				if ( ! is_ajax() ) {
					do_action( 'woocommerce_review_order_after_payment' );
				}
				?>
			</div>
			<div class="col-12 pt-5 text-center">

					<div class="form-row place-order">
						<noscript>
							<?php
							/* translators: $1 and $2 opening and closing emphasis tags respectively */
							printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
							?>
							<br /><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
						</noscript>

					</div>
						<?php wc_get_template( 'checkout/terms.php' ); ?>

						<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
						<div class="pt-3 w-100">
							<button type="submit" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-primary go_checkout mb-0 py-4 px-6 ">
								<i class="fas fa-shopping-cart"></i> <?php esc_html_e( 'Place your Order', 'woocommerce' ); ?>
							</button>
						</div>
						<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

						<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
			</div>
			</div>
		</div>

	</form>
<?php } else { ?>
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<div class="row">
			<div class="col-lg-6">
				<h2 id="order_review_heading" class="text-left"><?php esc_html_e( 'Billing Information', 'woocommerce' ); ?></h2>

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div id="customer_details">
						<div>
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>
			</div>
			<div class="col-lg-6 pl-lg-8">

				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<h2 id="order_review_heading" class="text-left"><?php esc_html_e( 'Cart Totals', 'woocommerce' ); ?></h2>
				<div class="color_box p-2 mb-5">
					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="cart_total_box">
							<button type="button" id="toggle_coupon">Enter Coupon </button>
							<div class="coupon">
								<div class="d-flex align-items-end">
									<div class=" flex-grow-1">
										<input type="text" class="input-text form-control" id="fake_coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
									</div>
									<button type="button" class="btn-primary" id="fake_coupon_code_apply" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>

								</div>
								<?php do_action( 'woocommerce_cart_coupon' ); ?>
							</div>
						</div>
						<script>
					jQuery(document).ready(function(){
						jQuery("#toggle_coupon").click(function(){
							jQuery(".coupon").slideToggle();
						});
					});
				</script>
						<script>
							jQuery(document).ready(function() {
								jQuery("#fake_coupon_code_apply").click(function() {
									jQuery("#coupon_code").val(jQuery("#fake_coupon_code").val());
									jQuery(".checkout_coupon").submit();
								});
							});
						</script>
					<?php } ?>
				</div>

				<div class="color_box p-2 mb-5">
					<div class=" woocommerce-checkout-review-order-table cart_total_box mb-0">
	<table class="shop_table mb-0 cart_totals_table">

		<tbody>

			<tr class="cart-subtotal">
				<th><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
				<td><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
			<?php endforeach; ?>

			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) { ?>

				<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

				<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

			<?php } else { ?>
			<tr class="shipping">
				<th><?php _e( 'Shipping', 'woocommerce' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><label>Pickup at school</label>
					<small class="d-block" style="text-transform: none; font-size: 12px; line-height: 1.2;">You will be able to pick up your items at the reception</small>

				</td>
			</tr>
			<?php } ?>

			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
			<?php endforeach; ?>

			<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
				<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
					<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
			<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
				<th><?php echo esc_html( $tax->label ); ?></th>
				<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
			</tr>
			<?php endforeach; ?>
			<?php else : ?>
			<tr class="tax-total">
				<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
				<td><?php wc_cart_totals_taxes_total_html(); ?></td>
			</tr>
			<?php endif; ?>
			<?php endif; ?>

			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<tr class="order-total">
				<th><?php _e( 'Total', 'woocommerce' ); ?></th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

		</tbody>
	</table>
</div>
				</div>

				<div class="color_box py-3 px-2 mb-5">
					<?php
					if ( ! is_ajax() ) {
						do_action( 'woocommerce_review_order_before_payment' );
					}
					?>
					<div id="payment" class="woocommerce-checkout-payment">
						<?php if ( WC()->cart->needs_payment() ) : ?>
							<ul class="wc_payment_methods payment_methods methods">
								<?php
								if ( ! empty( $available_gateways ) ) {
									foreach ( $available_gateways as $gateway ) {
										wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
									}
								} else {
									echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')) . '</li>'; // @codingStandardsIgnoreLine
								}
								?>
							</ul>
						<?php endif; ?>
					</div>
					<?php
					if ( ! is_ajax() ) {
						do_action( 'woocommerce_review_order_after_payment' );
					}
					?>
				</div>
				<?php wc_get_template( 'checkout/terms.php' ); ?>

						<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
						<div class="pt-3 w-100">
							<button type="submit" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn-primary go_checkout mb-0 py-4 w-100 ">
								<i class="fas fa-shopping-cart"></i> <?php esc_html_e( 'Place your Order', 'woocommerce' ); ?>
							</button>
						</div>
						<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

						<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</div>



	</form>
<?php } ?>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

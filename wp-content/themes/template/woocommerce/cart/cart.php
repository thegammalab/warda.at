<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>
<?php wc_print_notices(); ?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<div class="row">
		<div class="col-lg-7 pr-lg-7">
			<h2 class="text-left mb-5">Review Cart</h2>
			<?php do_action( 'woocommerce_before_cart_table' ); ?>
			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<div class="cart_prod_details mb-5  woocommerce-cart-form__contents">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<div class="row align-items-center">
							<div class="col-4 col-md-3">
								<div class="cart_image">
									<?php
									echo get_the_post_thumbnail( $product_id, 'thumbnail' );
									?>
								</div>
							</div>
							<div class="col-8 col-md-9">
								<div class="row align-items-center">
									<div class="col-md-7">
										<h5 class="mb-0 cart_title">
											<?php
											if ( ! $product_permalink ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
											} else {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
											}

											?>
										</h5>
										<div class="product_price">
											<?php
											echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
											if ( $cart_item['quantity'] > 1 ) {
												echo '<small>(' . apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) . ' x ' . $cart_item['quantity'] . ')</small>';
											}
											?>
										</div>
									</div>
									<div class="col-9 col-md-4 cart_qty">
										<div class="d-flex number_of_products align-item-center justify-content-between">
											<a href="#"><i class="fas fa-minus"></i></a>
												<?php
												if ( $_product->is_sold_individually() ) {
													$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
												} else {
													$product_quantity = woocommerce_quantity_input(
														array(
															'input_name'   => "cart[{$cart_item_key}][qty]",
															'input_value'  => $cart_item['quantity'],
															'max_value'    => $_product->get_max_purchase_quantity(),
															'min_value'    => '0',
															'product_name' => $_product->get_name(),
														),
														$_product,
														false
													);
												}

												echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
												?>
											<a href="#"><i class="fas fa-plus"></i></a>
										</div>

									</div>

									<!--<div class="col-3 col-md-1 d-flex justify-content-end">-->
										<?php
										// @codingStandardsIgnoreLine
										// echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
										// '<a href="%s" class="remove_item" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa fa-times p-2 pr-0"></i></a>',
										// esc_url(wc_get_cart_remove_url($cart_item_key)),
										// __('Remove this item', 'woocommerce'),
										// esc_attr($product_id),
										// esc_attr($_product->get_sku())
										// ), $cart_item_key);
										?>
									<!-- </div> -->
								</div>
								<?php

											do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

											// Meta data.
											echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

											// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}
								?>
							</div>
						</div>
						<hr />
						<?php
					}
				}
				?>
				<?php do_action( 'woocommerce_after_cart_contents' ); ?>

				<div class="d-flex justify-content-between align-items-center">
					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					<a href="#" class="btn-secondary line_button_1">continue shopping</a>
					<button type="submit" class="btn-primary line_button_2" name="update_cart" id="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
				</div>
				<script>
					jQuery(document).ready(function() {
						setInterval(function() {
							jQuery("#update_cart").prop("disabled", false);
						}, 500);
					});
				</script>
				<?php do_action( 'woocommerce_after_cart_table' ); ?>

			</div>



		</div>
		<div class="col-lg-5 ">
			<div class="cart-collaterals">
				<?php
				/**
				 * Cart collaterals hook.
				 *
				 * @hooked woocommerce_cross_sell_display
				 * @hooked woocommerce_cart_totals - 10
				 */
				do_action( 'woocommerce_cart_collaterals' );
				?>
			</div>

		</div>
	</div>
</form>


<?php do_action( 'woocommerce_after_cart' ); ?>

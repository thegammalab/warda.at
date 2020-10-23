<?php
global $product;
wp_reset_query();
$item = apply_filters( 'tdf_get_single', get_the_ID() );

$main_cat = $item['meta__yoast_wpseo_primary_product_cat'];
foreach ( $item['tax_array_product_cat'] as $f => $term ) {
	if ( $main_cat ) {
		if ( $main_cat == $term->term_id ) {
			$main_cat_term = $term;
		}
	} else {
		if ( $f == 0 ) {
			$main_cat_term = $term;
		}
	}
}

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
// do_action('woocommerce_before_single_product');

?>
<section >
  <?php
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		echo '<div class="breadcrumbs">' . yoast_breadcrumb( '', '', false ) . '</div>';}
	?>
</section>
<div class="container py-6">
  <div class="product_modal">
	<div class="row">
			<div class="col-md-12 mb-7">
			  <div class="product_image">
				<?php
				  /**
				   * Hook: woocommerce_before_single_product_summary.
				   *
				   * @hooked woocommerce_show_product_sale_flash - 10
				   * @hooked woocommerce_show_product_images - 20
				   */
				  do_action( 'woocommerce_before_single_product_summary' );
				?>
			  </div>
			</div>
			<div class="col-md-9">
			  <div class="product_details">
				<h2><?php echo $item['post_title']; ?></h2>
				<?php echo apply_filters( 'the_content', $item['post_content'] ); ?>


				<!-- <p><b>Keeps for 5days at 10C</b></p>
				<ul class="mt-5">
				  <li class="active"><a href="#">250g</a></li>
				  <li><a href="#">500g</a></li>
				  <li><a href="#">1000g</a></li>
				</ul> -->

			  </div>
			</div>
			<div class="col-md-3">
			  <h3><?php echo $product->get_price_html(); ?></h3>
			  <?php do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' ); ?>
			</div>
	</div>
  </div>
</div>
<div class="container">
  <div class="row">
	<div class="col-md-12">
	<h3 class="text-center">DON'T BE SHT PUT SOME MORE</h3>
	  <?php echo do_shortcode( '[related_products limit="4"]' ); ?>
	</div>
  </div>
</div>

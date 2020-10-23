<?php
$cat = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
if ( $cat ) {
	$subcats = get_terms( get_query_var( 'taxonomy' ), 'parent=' . $cat->term_id );
}

if ( $subcats ) {
	?>
  <div class="<?php echo $the_class; ?>">
	<section class="header_article">
		<div class="container">
			<h1 class="pb-2"><?php echo $cat->name; ?></h1>
		</div>
	</section>
	<section class="content_mag">
	  <div class="container">
		<div class="row">
		  <?php
			foreach ( $subcats as $cat ) {
				include locate_template( 'views/woocommerce/cat-item.php' );
			}
			?>
		</div>
	  </div>
	</section>
  </div>
<?php } else { ?>
  <div class="<?php echo $the_class; ?>">
	<section class="header_article">
		<div class="container">
			<h1><?php echo $cat->name; ?></h1>
			<div class="card_box header_format">
			  <div class="article_image">
				<?php echo wp_get_attachment_image( get_term_meta( $cat->term_id, 'header_image', true ), 'large_crop' ); ?>
			  </div>
			</div>
		</div>
	</section>
	<section class="content_mag">
	  <div class="container">
		<div class="row">
		  <?php
			$product_results = apply_filters( 'tdf_get_posts', 'product', 999, 0, array( 'search' => array( 'tax_' . get_query_var( 'taxonomy' ) => array( $cat->term_id ) ) ) );
			echo $product_results['output'];
			?>
		</div>

	  </div>
	</section>
  </div>

<?php } ?>

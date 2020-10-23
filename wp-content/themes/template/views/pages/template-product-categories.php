<?php
/**
 * Template Name: Product Categories
 */
?>
<section class="header_article">
  <div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<div class="gray_section" style="margin-top: 50px;margin-bottom: 50px;">
	<div class="container">
				<div class="product_cats">
			  <div class="row">
					<?php
					$args       = array(
						'taxonomy'   => 'product_cat',
						'orderby'    => 'name',
						'show_count' => 0,
					);
					$categories = get_categories( $args );
					foreach ( $categories as $cat ) {
						?>
						<div class="col-md-6 mb-3">
							<div class="card_box header_format">
								<div class="article_image img_contain">
									<a href="<?php echo get_term_link( $cat ); ?>"><?php echo wp_get_attachment_image( get_term_meta( $cat->term_id, 'thumbnail_id', true ), 'large_crop' ); ?></a>
								</div>
								<div class="cat_title">
									<a href="<?php echo get_term_link( $cat ); ?>"><h6><?php echo $cat->name; ?></h6></a>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
	</div>
</div>

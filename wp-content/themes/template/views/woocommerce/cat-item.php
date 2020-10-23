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

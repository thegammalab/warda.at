<section class="header_article">
	<div class="container">

		<h1><?php the_title(); ?></h1>
		<div class="card_box header_format">
			<div class="article_image">
				<?php the_post_thumbnail(); ?>
			</div>
		</div>
	</div>
</section>
<div class="gray_section" style="margin-top: 50px;margin-bottom: 50px;">
	<div class="container">
			<?php the_content();?>
	</div>
</div>

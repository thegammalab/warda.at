<section > 
  <?php if ( function_exists('yoast_breadcrumb') ) {echo '<div class="breadcrumbs">'.yoast_breadcrumb("","",false).'</div>';}  ?>
</section>

<section class="py-6">
  <div class="container">
    <?php the_content(); ?>
  </div>
</section>

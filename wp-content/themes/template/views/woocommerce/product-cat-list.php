<?php
echo "aaaaaaaaaaaaa";
$cat = get_term_by('slug',get_query_var('term'),get_query_var('taxonomy'));
print_r($cat);
$bg_img = get_term_meta($cat->term_id,"background_image",true);
echo "///".$bg_img."///";
?>
<section class="browse_products">
  <div class="container">
    <div class="mb-6">
      <?php  ?>
      <div class="mb-6 text-center">
        <h1>Browse <?php echo $cat->name; ?></h1>
        <?php echo apply_filters("the_content",$cat->description); ?>
      </div>
    </div>
    <?php
    $topics = get_terms("topics");
    foreach($topics as $topic){
      $results = apply_filters( 'tdf_get_posts',"product",10,0,array("search"=>array("tax_product_cat"=>$cat->term_id, "tax_topics"=>$topic->term_id)));
      if(count($results["items"])){
        //do_action( 'woocommerce_before_shop_loop' );
        // ^ uncomment this line to show top count and sort

        woocommerce_product_loop_start();
        ?>
        <div class="col-md-6">
          <div class="product_category">
            <h3 class="fs225"><?php echo $topic->name; ?></h3>
            <?php echo $topic->description; ?>
          </div>
        </div>

        <?php
        echo $results["output"];
        woocommerce_product_loop_end();
        do_action( 'woocommerce_after_shop_loop' );
      }
    }
    ?>
  </div>
</section>

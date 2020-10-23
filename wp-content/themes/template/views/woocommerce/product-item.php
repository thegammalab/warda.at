<?php
if(!isset($item) && get_the_ID()){
$item = apply_filters("tdf_get_single",get_the_ID());
} 
$_pf = new WC_Product_Factory();  
$_product = $_pf->get_product($item["post_id"]);
?>


<div class="col-lg-3 col-sm-6">
    <div class="card_box square_format">
        <div class="article_image img_contain">
            <a href="<?php echo $item["post_permalink"]; ?>"><?php echo $item["featured_img_square_crop"]; ?></a>
        </div>
        <div class="product_item_bottom">
          <a href="<?php echo $item["post_permalink"]; ?>"><h6><?php echo $item["post_title"]; ?></h6></a>
          <div class="d-flex justify-content-between align-items-center">
            <div class="product_price"><?=$_product->get_price_html();?></div>
            <?php echo apply_filters( 'tdf_woocommerce_item_buy_button',$item["post_id"],"+",array("classes"=>"product_add_to_cart")); ?>
          </div>
        </div> 
    </div>
</div>


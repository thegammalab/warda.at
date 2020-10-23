<div class="<?=$the_class; ?>">
    <section class="header_article">
        <div class="container">
            <h1><?php the_title(); ?></h1>
            <div class="card_box header_format">
              <div class="article_image">
                <?php echo ($item["featured_img_large_crop"]); ?>
              </div>
            </div>
        </div>
    </section>
    <section class="content_mag">
      <div class="container">
        <h3 class="shop_title">Produkte</h3>
        <div class="row mb-7">
          <?php
          $product_results = apply_filters('tdf_get_posts',"product",999,0,array());
          echo $product_results["output"];
          ?>
        </div>
        <h3 class="shop_title">Kategorien</h3>
        <div class="row">
          <?php 
          $categories = get_terms("product_cat","hide_empty=0&parent=0");
          foreach( $categories as $cat){ 
            include(locate_template("views/woocommerce/cat-item.php"));
          } 
          ?>
        </div>
      </div>
    </section>
</div>
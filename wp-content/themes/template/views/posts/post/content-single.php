<?php
if(is_array($item["post_title"])){
  $item["post_title"] = $item["post_title"][0];
}
if(is_array($item["post_id"])){
  $item["post_id"] = $item["post_id"][0];
}
?>
<div class="article">
  <section class="header_article">
    <div class="container">
      <ul class="article_categories">
        <?php 
        $cats = array();
        foreach ($item["tax_array_category"] as $j => $cat) { 
          $cats[]=$cat->term_id;
          if($cat->parent){
            $level0 = $cat->parent;
          }else{
            $level0 = $cat->term_id;
          }
          ?>
          <li><a href="<?= get_term_link($cat); ?>" class="<?=get_term_meta($level0,"theme_color",true); ?>"><?=$cat->name;?></a></li>
        <?php } ?>
      </ul>
      <h1><?php the_title(); ?></h1>
      <div class="card_box header_format">
        <div class="article_image">
          <?= $item["featured_img_large_crop"]; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="more_articles">
    <div class="container">
      <div class="article_area">
        <div class="share_links"><?php echo do_shortcode('[mashshare]'); ?></div>
        <div class="author mb-4">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
        <div class="row double_space">
          <div class="col-lg-8 right_border_col">
            <?php the_content(); ?>
          </div>
          <div class="col-lg-4">
            <div class="sticky_sidebar">
              <?php dynamic_sidebar("article_sidebar"); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>


  <section class="content_mag py-6">
    <div class="container">
      <h2 class="text-center mb-5">DAS KÖNNTE SIE AUCH INTERESSIEREN</h2>
      <div class="row">
        <?php 
        $args = array("search" => array("tax_category"=>$cats, "exclude"=>array($item["post_id"])), "order"=>"rand");
        $results = apply_filters('tdf_get_posts', "post", 6, 0, $args);

        foreach($results["items"] as $item){
          $exclude_ids[]=$item["post_id"];
          echo '<div class="col-md-4">';
          include(locate_template("/views/posts/post/content-item.php")); 
          echo '</div>';
        }
        ?>          
      </div>
    </div>
  </section>
</div>

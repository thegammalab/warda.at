<?php

if(isset($item["tax_array_category"][0])){
  $main_cat = $item["tax_array_category"][0];
}else{
  $main_cat = false;
}

if($main_cat->parent){
    $level0 = $main_cat->parent;
}else{
    $level0 = $main_cat->term_id;
}

if(is_array($item["post_title"])){
  $item["post_title"] = $item["post_title"][0];
}
if(is_array($item["post_id"])){
  $item["post_id"] = $item["post_id"][0];
}
?>

<div class="card_box tall_format large_format">
    <div class="article_image">
        <?php if(!is_wp_error($main_cat) && isset($main_cat) && $main_cat){ ?>
                <a href="<?= get_term_link($main_cat); ?>" class="category_name <?=get_term_meta($level0,"theme_color",true); ?>"><?= $main_cat->name; ?></a>
              <?php } ?>
        <a href="<?= $item["post_permalink"]; ?>"><?= $item["featured_img_medium_crop"]; ?></a>
    </div>
    <div class="article_content">
        <div class="author">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
        <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
        <p><?= $item["post_excerpt"]; ?></p>
    </div>
</div>
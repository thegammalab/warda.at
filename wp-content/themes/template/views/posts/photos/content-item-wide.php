<?php
$gallery=get_post_meta($item["post_id"],"gallery",true);
// if(is_array($item["post_id"])){
//     $item["post_id"] = $item["post_id"][0];
// }
?>
<div class="photos_wide">
    <div class="row">
        <div class="col-md-6">
            <a href="<?= $item["post_permalink"]; ?>" class="photos_img"><?= $item["featured_img_smaller_crop"] ?></a>
        </div>
        <div class="col-md-6">
            <div class="photos_info">
                <div class="d-flex align-items-center mb-3">
                    <a href="<?=$item["post_permalink"]; ?>" class="category_name purple mr-2"><?=$item["meta_gallery_images_count"]; ?> fotos</a>
                    <?php if($item["author_display_name"]){ ?>
                        <div class="author_name">von <a href="<?=$item["author_link"];?>" style="color:#FFF;"><?=$item["author_display_name"];?></a></div>
                    <?php } ?>
                </div>
                <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
                <?=apply_filters("warda_get_photo_thumbnails",$post_id); ?>
                    
            </div>
        </div>
    </div>
</div>
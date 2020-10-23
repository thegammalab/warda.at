<div class="position-relative">
    <a href="<?= $item["post_permalink"]; ?>"><div class="video_img_box"><?php if($item["meta__thumbnail_id"]){ ?><img src="<?= wp_get_attachment_url($item["meta__thumbnail_id"]); ?>" /><?php } ?></div></a>
    <div class="photo_date_place position-absolute">
        <p><?php if($item["meta_photographer"]){echo '<a href="'.get_author_posts_url($item["meta_photographer"]).'">'.apply_filters("tdf_get_display_name",$item["meta_photographer"]).'</a>';}else{echo '<a href="'.get_author_posts_url($item["author_id"]).'">'.apply_filters("tdf_get_display_name",$item["author_id"]).'</a>';}?></p>
        <a href="<?=$item["post_permalink"];?>"><h5><?=$item["post_title"];?></h5></a>
    </div>
    <div class="position-absolute photo_number">
        <h2 class="text-center"><?=$item["meta_gallery_images_count"]; ?></h2>
        <p>Fotos</p>
    </div>
</div>
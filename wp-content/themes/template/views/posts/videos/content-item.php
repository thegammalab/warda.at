<?php
$ev_id = $item["meta_new_event_id"];
?>
    <div class="video_box position-relative">
<a href="<?= $item["post_permalink"]; ?>"><div class="video_img_box"><?php if($item["meta__thumbnail_id"]){ ?><img src="<?= wp_get_attachment_url($item["meta__thumbnail_id"]); ?>" /><?php } ?></div></a>
        <a href="<?= $item["post_permalink"]; ?>" class="play_btn position-absolute d-flex m-auto"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/playbtn.png" alt=""></a>
    </div>
    <div class="photo_date_place text-center">
      <?php if($item["meta_the_date"]){ ?>
        <p class="mx-auto"><?=strftime("%e. %B %Y",$item["meta_the_date"]);?></p>
      <?php } ?>
      <div class="mx-auto">
        <a href="<?= $item["post_permalink"]; ?>"><h5 class="fuchsia_text"><?= $item["post_title"]; ?></h5></a>
      </div>
    </div>

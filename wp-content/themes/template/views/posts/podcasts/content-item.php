<div class="podcast_box">
    <div class="row">
        <div class="col-md-12">
            <div class="author">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
            <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
            <?php echo do_shortcode($item["meta_podcast_shortcode"]); ?>
        </div>
        <?php 
        if($item["meta_related_article"]){ 
            $art_id = $item["meta_related_article"];
            $art = apply_filters("tdf_get_single",$art_id);
            ?>
            <div class="col-md-3">
                <div class="mb-3"><a href="<?= $art["post_permalink"]; ?>"><?= get_the_post_thumbnail($art["post_id"], "smaller_crop"); ?></a></div>
                <div class="author">von <b><a href="<?=$art["author_link"];?>"><?=$art["author_display_name"];?> </a></b>am <?=date("d.m.Y",$art["post_date"]);?></div>
                <a href="<?= $art["post_permalink"]; ?>"><h6 class="podcast_title"><?= $art["post_title"]; ?></h6></a>
            </div>
        <?php } ?>
    </div>
</div>
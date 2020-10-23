<?php apply_filters( 'warda_get_event_thumbnail', $item["post_id"]); ?>
<div class="event_box">
    <div class="event_image">
        <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "smaller_crop"); ?></a>
    </div>
    <div class="event_details">
        <div class="tag_category_names">
            <?=$item["tax_links_string_t"]; ?>
        </div>
        <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
        <?php if($the_time = apply_filters("warda_events_date_time",$item["post_id"],1)){ ?>
            <div class="event_time"><?= $the_time; ?></div>
        <?php  } ?>
        <a href="<?= $item["post_permalink"]; ?>" class="btn-primary d-block">Eventdetails</a>
    </div>
</div>
<?php apply_filters( 'warda_get_event_thumbnail', $item["post_id"]); ?>

<div class="sponsored_event events_wide">
        <h5>Sponsored Event</h5>
        <div class="event_box wide_format ">
            <div class="event_image">
                <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "smaller_crop"); ?></a>
            </div>
            <div class="event_details pt-0">
                <div class="tag_category_names">
                    <?=$item["tax_links_string_t"]; ?>
                </div>
                <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <?php if($the_time = apply_filters("warda_events_date_time",$item["post_id"],1)){ ?>
                        <div class="event_time mb-0"><?= $the_time; ?></div>
                    <?php  } ?>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="<?= $item["post_permalink"]; ?>" class="btn-primary buy_ticket "> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="">Gewinnspiel</a>
                </div>
            </div>
        </div>
</div>
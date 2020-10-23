<div class="ticket_box position-relative <?=$the_class;?>">
    <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "smaller_crop"); ?></a>
    <!-- <div class="position-absolute event_date">
        <h2 class="text-center"><?=date("j",strtotime($item["meta_start_date"]));?><br><?=strftime("%h",strtotime($item["meta_start_date"]));?></h2>
    </div> -->
    <div class="ticket_details">
        <div class="tag_category_names red_bg"><?=$item["tax_links_string_t"]; ?></div>
        <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>

        <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
            <div class="event_place"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/pin_icon.svg" alt=""> <?=get_term_meta($venue_id,"address",true);?></div>
        <?php  } ?>
        
        <div class="ticket_bottom">
            <div class="divider"></div>
            <a href="<?= $item["post_permalink"]; ?>" class="btn-primary d-block buy_ticket"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-red.svg" alt=""><?=$item["meta_button_label"];?></a>
        </div>
    </div>
</div>
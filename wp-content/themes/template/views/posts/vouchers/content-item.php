<div class="ticket_box position-relative">
    <div class="w-100">
        <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "smaller_crop"); ?></a>
    </div>
    <div class="ticket_details">
        <div class="tag_category_names indigo_bg"><?=$item["tax_links_string_voucher_cat"]; ?></div>
        <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
        <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
            <div class="event_place"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/pin_icon.svg" alt=""> <?=get_term_meta($venue_id,"address",true);?></div>
        <?php  } ?>
        <div class="ticket_bottom">
            <div class="divider"></div>
            <a href="<?= $item["post_permalink"]; ?>" class="btn-primary d-block buy_ticket">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/white_voucher_icon.svg" alt=""> Gutschein einlösen
            </a>
        </div>
    </div>
</div>

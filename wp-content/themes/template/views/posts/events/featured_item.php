<?php apply_filters( 'warda_get_event_thumbnail', $item["post_id"]); ?>

<section class="featured_event">
  <div class="container">
    <div class="section_title mb-4">
      <h5 class="green_text">HIER KANNST DU FEIERN</h5>
      <h2 class="mb-0 ">Featured Event</h2>
    </div>
    <div class="d-flex">
      <div class="event_image">
        <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "smaller_crop"); ?></a>
      </div>
      <div class="event_details">

        <h2><?=strftime("%e	%h",strtotime($item["meta_start_date"]));?></h2>
          <div class="tag_category_names">
  <?=$item["tax_links_string_t"]; ?>
          </div>
          <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
          <?php if($the_time = apply_filters("warda_events_date_time",$item["post_id"],0)){ ?>
  <div class="event_time"><i class="fas fa-clock"></i><?= $the_time; ?></div>
          <?php  } ?>
          <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
  <div class="event_place"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/pin_icon.svg" alt=""><?=get_term_meta($venue_id,"address",true);?></div>
          <?php  } ?>
          <a href="<?=$item["post_permalink"];?>" class="btn-primary d-block">View event</a>
      </div>
    </div>
  </div>
</section>


<?php
$ev_id = $item["meta_new_event_id"];
?>
<div class="video_single ">
  <div class="container">
    <div class="row">
      <div class="col-md-9  pt-5">
        <div class="mb-4 full_img">
          <?=apply_filters("the_content",$item["meta_video_link"]); ?>
        </div>
        <div class="row">
          <?php if($item["meta_the_date"]){ ?>
            <div class="col-md-4">
              <div class="date_bar border-0 p-0 mb-3">
                <span class="fuchsia_text"><?=utf8_encode(strftime("%A",$item["meta_the_date"]));?></span>
                <h3><?=utf8_encode(strftime("%e. %B %Y",$item["meta_the_date"]));?></h3>
              </div>
            </div>
            <div class="col-xl-1 d-none d-xl-block"></div>
          <?php } ?>
          <div class="col-md-8 col-xl-7">
            <a href="#" class="category_name fuchsia">VIDEO</a><p class="author_name">By <?=apply_filters("tdf_get_display_name",$item["meta_photographer"]);?></p>
            <h2 class="page_title"><?=$item["post_title"];?></h2>
            <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
                <div class="event_address">
                <p class="text-uppercase"><b><?=$item["tax_array_venue"][0]->name;?></b></p>
                <p class="text-uppercase"><?=get_term_meta($venue_id,"address",true);?></p>
                </div>
            <?php  } ?>
            <div class="sharethis">
              <p class="text-uppercase">teilen</p>
              <div class="share_links_small"><?php echo do_shortcode('[mashshare]'); ?></div>
            </div>

          </div>
        </div>
        <div class="mb-8 pt-5 ">
              <?php the_content(); ?>
        </div>
      </div>
      <div class="col-md-3 pt-5 grey_sidebar">
        <div class=" white_tickets_bg sidebar sticky_sidebar">
            <?php dynamic_sidebar("videos_single_sidebar"); ?>
        </div>
      </div>
    </div>
  </div>
  <hr class="mt-0">
  <section class="related_videos pt-5 pb-6">
    <div class="container ">
      <h3 class="text-uppercase mb-4">FOTOS AUS DIESER LOCATION</h3>
      
      <div class="events_photos p-0" style="background:none;">
        <div class="row">
        <?php 
        $args = array("search" => array("tax_venue"=>$venue_id, "exclude"=>array($item["post_id"])), "order"=>"rand");
        $results = apply_filters('tdf_get_posts', "videos", 6, 0, $args);

        foreach($results["items"] as $item){
          $exclude_ids[]=$item["post_id"];
          echo '<div class="col-md-4 mb-6 text-center">';
          include(locate_template("/views/posts/videos/content-item.php")); 
          echo '</div>';
        }
        ?>  
        </div>
    </div>

    </div>
  </section>
</div>
  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>

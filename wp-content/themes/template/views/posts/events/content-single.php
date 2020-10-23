<div class=" event_single white_bg">
  <div class="container">
    <div class="row">

      <div class="col-md-9 pt-6 pr-md-6">
        <div class="mb-4 full_img">
            <?= get_the_post_thumbnail($item["post_id"], "full"); ?>

        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="date_bar border-0 p-0 mb-3">
              <span><?=utf8_encode(strftime("%A",strtotime($item["meta_start_date"])));?></span>
              <h3><?=utf8_encode(strftime("%e. %B %Y",strtotime($item["meta_start_date"])));?></h3>
            </div>
            <ul class="left_details">
              <li> <b class="pr-2">Einlass:</b><?=$item["meta_admission_time"];?></li>
              <li> <b class="pr-2">Beginn:</b><?=$item["meta_start_time"];?></li>
            </ul>
            <?php if($item["meta_ticket_pricing"]){ ?>
              <hr>
              <ul class="left_details">
                <?php for($i=0;$i<$item["meta_ticket_pricing"];$i++){ ?>
                    <li> <b class="pr-2"><?=$item["meta_ticket_pricing_".$i."_ticket"]; ?>:</b><?=$item["meta_ticket_pricing_".$i."_price"]; ?></li>
                <?php } ?>
              </ul>
            <?php } ?>
            <hr>
            <?php if($item["meta_organizer_name"]){ ?>
              <p><b>Folge <span class="text-uppercase"><?=$item["meta_organizer_name"];?></span>:</b></p>
            <?php } ?>
            <ul class="left_details d-flex">
              <?php if($item["meta_organizer_facebook"]){ ?>
                <li><a href="<?=$item["meta_organizer_facebook"];?>" class="social_circles green_blue" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/facebook.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_instagram"]){ ?>
                <li><a href="<?=$item["meta_organizer_instagram"];?>" class="social_circles green_blue" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/instagram.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_website"]){ ?>
                <li><a href="<?=$item["meta_organizer_website"];?>" class="social_circles green_blue" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/website.svg" alt=""></a></li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-xl-1 d-none d-xl-block"></div>
          <div class="col-md-8 col-xl-7">
            <div class="tag_category_names">
              <?=$item["tax_links_string_events_cat"]; ?> <?=$item["tax_links_string_genre"]; ?>
            </div>
            <h2 class="page_title"><?=$item["post_title"];?></h2>
            <div class="d-flex align-items-center justify-content-between">
              <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
                <div class="event_place mb-3">
                  <h6><?=$item["tax_array_venue"][0]->name;?></h6>
                  <p class="text-uppercase"><?php $add = get_term_meta($venue_id,"address",true); echo $add; ?></p>
                </div>
                <?php if($add){ ?>
                  <div class="">
                    <a href="https://www.google.com/maps/search/<?=$item["tax_array_venue"][0]->name.",".$add;?>" target="_blank" class="map_btn"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/bullseye.svg" alt="">Map</a>
                  </div>
                <?php } ?>
              <?php  } ?>
              

            </div>
            <div class="share_links_small"><?php echo do_shortcode('[mashshare]'); ?></div>
            <hr>
            <div class="d-flex justify-content-between">
              <div class="">
                <?php if(isset($item["meta_ticket_info"])){ echo $item["meta_ticket_info"];} ?>
              </div>
              <div class="">
                <?php for($i=0;$i<$item["meta_ticket_links"];$i++){ ?>
                  <a href="<?=$item["meta_ticket_links_".$i."_affiliate_url"]; ?>" target="_blank" class="green_blue d-block btn-primary mb-2"><?=$item["meta_ticket_links_".$i."_site_name"]; ?></a>
                <?php } ?>
              </div>
            </div>

          </div>
        </div>
        <div class="bordered_section d-none">
          <?php
          global $wpdb;
          $results = $wpdb->get_col("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='sweepstake_event_id' AND `meta_value`='".$item["post_id"]."'");
          foreach($results as $sweepstake_id){
          ?>
            <div class="row">
              <div class="col-md-4 pl-md-0">
                <h2 class="green_text text-uppercase mb-0">Gewinne</h2>
                <h3 class="text-uppercase mb-0"><?php echo get_post_meta($sweepstake_id,"sweepstake_offer",true); ?></h3>
                <p class="mb-0">Exklusives Gewinnspiel für unsere User!</p>
              </div>
              <div class="col-xl-1 d-none d-xl-block"></div>
              <div class="col-md-8 col-xl-7">
                <div class="d-flex ">
                  <div class="single_page_buttons">
                    <a href="<?php echo get_the_permalink($sweepstake_id); ?>" class="btn-primary buy_ticket d-flex align-items-center  justify-content-center mb-2"> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="" width="18" height="18" class="mr-1">Gewinnspiel</a>
                    <a href="#" class="green_blue d-block btn-primary">register</a>
                    <div class="ticket_disclaimer text-center mt-0">Ich hab gar k1 account</div>
                  </div>
                  <div>
                    <div class="ticket_disclaimer mt-0 ml-4">Expires on <?php echo date("m d Y",strtotime(get_post_meta($sweepstake_id,"sweepstake_expiration",true)))." ".get_post_meta($sweepstake_id,"sweepstake_expiration_time",true); ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
        <?php if(get_the_content()){ ?>
          <div class="">
            <hr class="my-6" />
            <div class=" mb-8">
              <?php the_content(); ?>
            </div>
          </div>
        <?php }else{ ?>
          <div class="pt-8"></div>
        <?php } ?>
      </div>
      <div class="col-md-3 pt-5 grey_sidebar">
        <div class="sticky_sidebar white_tickets_bg">
              <?php dynamic_sidebar("events_single_sidebar"); ?>
            </div>
      </div>
    </div>
  </div>
  <?php             
  $exclude_ids = array($item["post_id"]);
  $feat_events = apply_filters('tdf_get_posts', "events", 3, 0, array("search" => array("exclude"=>$exclude_ids,"meta_the_date_more"=>time())));
  if(count($feat_events["items"])){
  ?>
    <section class="related_events">
      <div class="container ">
        <h3 class="text-uppercase mb-4">Ähnliche Veranstaltungen</h3>
        <div class="row">
              <?php
              foreach($feat_events["items"] as $item){
                  $exclude_ids[] = $item["post_id"];
                  echo '<div class="col-md-9 mb-4">';
                  include(locate_template("/views/posts/events/wide_item.php")); 
                  echo '</div>';
              } 
              ?>
        </div>
        <div class="m-auto d-none">
          <a href="" class="load_more_btn">Mehr Laden</a>
        </div>
      </div>
    </section>
  <?php } ?>
</div>
<?php include(locate_template("views/pieces/newsletter_black.php")); ?>            

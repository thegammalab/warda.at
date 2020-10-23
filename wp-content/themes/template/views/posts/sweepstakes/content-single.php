
<div class=" ticket_single white_bg ">
  <div class="container">
    <div class="row">

      <div class="col-md-9 white_bg pt-5 pr-md-5">
        <div class="mb-4 full_img">
            <?= get_the_post_thumbnail($item["post_id"], "full"); ?>
        </div>
        <div class="row">
          <!-- <div class="col-md-4">
            <div class="date_bar border-0 p-0 mb-3">
              <span><?=strftime("%A",strtotime($item["meta_start_date"]));?></span>
              <h3><?=strftime("%e. %B %Y",strtotime($item["meta_start_date"]));?></h3>
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
                <li><a href="<?=$item["meta_organizer_facebook"];?>" class="social_circles red" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/facebook.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_instagram"]){ ?>
                <li><a href="<?=$item["meta_organizer_instagram"];?>" class="social_circles red" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/instagram.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_website"]){ ?>
                <li><a href="<?=$item["meta_organizer_website"];?>" class="social_circles red" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/website.svg" alt=""></a></li>
              <?php } ?>
            </ul>
          </div>
         <div class="col-xl-1 d-none d-xl-block"></div> -->
          <div class="col-md-12 col-xl-12">
            <div class="tag_category_names">
              <?=$item["tax_links_string_t"]; ?>
            </div>
            <h2 class="text-uppercase page_title"><?=$item["post_title"];?></h2>
            <div class="d-flex align-items-center justify-content-between">
              <?php if(isset($item["tax_array_venue"][0])){ $venue_id = $item["tax_array_venue"][0]->term_id; ?>
                <div class="event_place mb-3">
                  <h6><?=$item["tax_array_venue"][0]->name;?></h6>
                  <p class="text-uppercase"><?php $add = get_term_meta($venue_id,"address",true); echo $add; ?></p>
                </div>
                <?php if($add){ ?>
                  <div class="">
                    <a href="https://www.google.com/maps/search/<?=$item["tax_array_venue"][0]->name.",".$add;?>" target="_blank" class="map_btn"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/bullseye_red.svg" alt="">Map</a>
                  </div>
                <?php } ?>
              <?php  } ?>
            </div>
            <div class="share_links_small"><?php echo do_shortcode('[mashshare]'); ?></div>
            <hr>
            <div class="">
              <div class="d-md-flex ">
                <div class="single_page_buttons">
                      <?php if(!get_current_user_id()){ ?>
                        <a href="#" class="btn-primary buy_ticket d-flex align-items-center justify-content-center mb-2" data-toggle="modal" data-target="#login_popup"> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-red.svg" alt="" width="18" height="18" class="mr-1">Teilnehmen</a>
                      <?php }else{ ?>
                        <div class="alert alert-success" id="join_ok" style="display:none;">Thank you for joining</div>
                        <div class="alert alert-danger" id="join_exists" style="display:none;">You have already joined</div>
                        <a href="#" class="btn-primary buy_ticket d-flex align-items-center justify-content-center mb-2 add_to_sweepstake" data-sweepstakes_id="<?=$item["post_id"];?>"> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-red.svg" alt="" width="18" height="18" class="mr-1">Teilnehmen</a>
                      <?php } ?>
                </div>
                <div class='pt-2'>
                  <div class="ticket_disclaimer mt-0 ml-md-4">Teilnahmeschluss: <?=utf8_encode(strftime("%e. %B %Y",strtotime($item["meta_sweepstake_expiration"])));?> </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="content_area py-6 px-xl-8 mt-6 border-top">
          <?php the_content(); ?>
        </div>

      </div>
      <div class="col-md-3 pt-5 grey_sidebar ">
        <div class=" white_tickets_bg sidebar sticky_sidebar">
            <?php dynamic_sidebar("sweepstakes_list_sidebar"); ?>
        </div>
      </div>
    </div>
  </div>

<script>
  jQuery(document).ready(function(){
    jQuery(".add_to_sweepstake").click(function(e){
      e.preventDefault();
      
      jQuery.ajax({
        url: "<?=admin_url("admin-ajax.php");?>?action=warda_join_sweepstakes&sweepstakes_id=<?=$item["post_id"];?>",
        context: document.body
      }).done(function(data) {
        if(data=="added"){
          jQuery("#join_ok").slideDown();
          setTimeout(function(){jQuery("#join_ok").slideUp()},3000);
        }else{
          jQuery("#join_exists").slideDown();
          setTimeout(function(){jQuery("#join_exists").slideUp()},3000);
        }
      });

      return false;
    });
  });
</script>


  <section class="more_sweepstakes related_events">
    <div class="container ">
        <?php
            $feat_events = apply_filters('tdf_get_posts', "sweepstakes", 3, 0, array("search" => array("exclude"=>array($item["post_id"]))));
            if(count($feat_events["items"])){
              echo '<h3 class="text-uppercase mb-4">MEHR GEWINNSPIELE</h3>';
              echo '<div class="row white_tickets_bg" id="results_div">';
              foreach($feat_events["items"] as $item){
                include(locate_template("/views/posts/sweepstakes/ajax_load_item.php")); 
              }
              echo '</div>';
            }
            ?>
        
        <div class="m-auto">
          <a href="#" id="load_more_button" class="load_more_btn">Mehr Laden</a>
        </div>
          <form action="" class="filters">
            <input type="hidden" name="post_type" value="sweepstakes" />
            <input type="hidden" name="args[search][exclude][]" value="<?=$item["post_id"];?>" />
            <input type="hidden" name="per_page" value="3" />
            <input type="hidden" name="page" id="page_no" value="1" />           
            <?php include(locate_template("views/pieces/filters/scripts.php")); ?>
          </form>
      
    </div>
  </section>
</div>
  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>



<div class=" voucher_single white_bg ">
  <?php include("templates/header.php"); ?>
  <div class="container">
    <div class="row">

      <div class="col-lg-9 white_bg pt-5">
        <div class="mb-4">
            <?= get_the_post_thumbnail($item["post_id"], "full"); ?>

        </div>
        <div class="row">
          <div class="col-md-4">
   
            <?php if($item["meta_opening_times"]){ ?>
              <ul class="left_details">
                <li> 
                  <div class="row">
                    <div class="col-5 pr-0" style="font-size: 14px;"><b style="font-weight:600;">Einlösbar:</b></div>
                    <div class="col-7" style="font-size: 14px; line-height: 1.5;"><?=$item["meta_opening_times"];?></div>
                  </div>
                </li>
              </ul>
              <hr>
            <?php } ?>
            <?php if($item["meta_price_guide"]){ ?>
              <ul class="left_details">
                <li> 
                  <div class="row">
                    <div class="col-5 pr-0" style="font-size: 14px;"><b style="font-weight:600;">Details:</b></div>
                    <div class="col-7" style="font-size: 14px; line-height: 1.5;"><?=$item["meta_price_guide"];?></div>
                  </div>
                </li>
              </ul>
              <hr>
            <?php } ?>
            <?php if($item["meta_organizer_name"]){ ?>
              <p><b>Folge <span class="text-uppercase"><?=$item["meta_organizer_name"];?></span>:</b></p>
            <?php } ?>
            <ul class="left_details d-flex">
              <?php if($item["meta_organizer_facebook"]){ ?>
                <li><a href="<?=$item["meta_organizer_facebook"];?>" class="social_circles indigo" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/facebook.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_instagram"]){ ?>
                <li><a href="<?=$item["meta_organizer_instagram"];?>" class="social_circles indigo" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/instagram.svg" alt=""></a></li>
              <?php } ?>
              <?php if($item["meta_organizer_website"]){ ?>
                <li><a href="<?=$item["meta_organizer_website"];?>" class="social_circles indigo" target="_blank"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/website.svg" alt=""></a></li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-xl-1 d-none d-xl-block"></div>
          <div class="col-md-8 col-xl-7">
            <div class="tag_category_names">
              <?=$item["tax_links_string_voucher_cat"]; ?>
            </div>
            <h2 class="page_title"><?=$item["post_title"];?></h2>
            <div class="d-flex align-items-center justify-content-between">
              <?php if(isset($item["tax_array_venue"][0])){ 
                $venue_id = $item["tax_array_venue"][0]->term_id; 
                $add = get_term_meta($venue_id,"address",true);
                ?>
                <div class="event_place mb-3">
                  <h6><?=$item["tax_array_venue"][0]->name;?></h6>
                  <p class="text-uppercase"><?=$add;?></p>
                </div>
                <?php if($add){ ?>
                  <div class="">
                    <a href="https://www.google.com/maps/place/<?=urlencode($add." Wien Austria"); ?>" target="_blank" class="map_btn"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/bullseye_blue.svg" alt="">Map</a>
                  </div>
                <?php } ?>
              <?php  } ?>
              

            </div>
           
              <div class="">
                <div class="share_links_small"><?php echo do_shortcode('[mashshare]'); ?></div>
              </div>
              <hr>
              <div class="d-flex justify-space-between">
                <div class="w-100">
                  <?php if($item["meta_voucher_link"]){ ?>
                    <div class="text-center">
                      <?php if(!get_current_user_id() && 1==0){ ?>
                        <a href="#" class="voucher_red btn-primary mb-2" data-toggle="modal" data-target="#login_popup">Gutschein einlösen</a>
                        <?php include(locate_template("views/pieces/login_modal.php"));?>
                      <?php }else{ ?>
                        <a href="<?=$item["meta_voucher_link"];?>" target="_blank" class="voucher_red btn-primary mb-2">Gutschein einlösen</a>
                      <?php } ?>
                      <div class="row pt-2">
                        <div class="col-6">
                          <h6 class="text-uppercase text-center">läuft noch <span style="color:#d41f39;"><?=round((strtotime($item["meta_valid_until"])-time())/(24*3600));?> Tage</span></h6>
                        </div>
                        <div class="col-6">
                          <h6 class="text-uppercase text-center">Verfügbarkeit <span style="color:#d41f39;"><?=$item["meta_total_vouchers"];?>x</span> </h6>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>

              </div>
            </div>
          </div>
          <div class="content_area py-6 mt-6 px-xl-8 mb-8 border-top">
            <?php the_content(); ?>
          </div>
        </div>
        <div class="d-none d-lg-block col-lg-3 pt-5 grey_sidebar sidebar">
          <div class=" white_tickets_bg sidebar sticky_sidebar">
            <?php dynamic_sidebar("vouchers_single_sidebar"); ?>
          </div>
        </div>
      </div>
    </div>
    <section class="more_vouchers related_events">
      <div class="container ">
        
            <?php
            $feat_events = apply_filters('tdf_get_posts', "vouchers", 4, 0, array("search" => array("exclude"=>array($item["post_id"]))));
            if(count($feat_events["items"])){
              echo '<h3 class="text-uppercase mb-4">Mehr Gutscheine</h3>';
              echo '<div class="row white_tickets_bg" id="results_div">';
              foreach($feat_events["items"] as $item){
                include(locate_template("/views/posts/vouchers/ajax_load_item.php")); 
              }
              echo '</div>';
            }
            ?>
        
        <div class="m-auto">
          <a href="#" id="load_more_button" class="load_more_btn">Mehr Laden</a>
        </div>
          <form action="" class="filters">
            <input type="hidden" name="post_type" value="vouchers" />
            <input type="hidden" name="args[search][exclude][]" value="<?=$item["post_id"];?>" />
            <input type="hidden" name="per_page" value="4" />
            <input type="hidden" name="page" id="page_no" value="1" />           
            <?php include(locate_template("views/pieces/filters/scripts.php")); ?>
          </form>

      </div>
    </section>

  </div>
  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>

<?php
$orig_gal_id = $item["meta_orig_gal_id"];
$item["post_id"] = get_the_ID();
?>
<div class="photo_single gray_3rd">
  <div class="container">
    <div class="row">
      <div class="col-lg-9 content_area pt-5">
          <div class="mb-4 full_img">
            <?= $item["featured_img_full"]; ?>
        </div>
        <div class="row">
          <?php if($item["meta_the_date"]){ ?>
            <div class="col-md-4">
              <div class="date_bar border-0 p-0 mb-3">
                <span><?=utf8_encode(strftime("%A",($item["meta_the_date"])));?></span>
                <h3><?=utf8_encode(strftime("%e. %B %Y",($item["meta_the_date"])));?></h3>
              </div>
            </div>
            <div class="col-xl-1 d-none d-xl-block"></div>
          <?php } ?>
          <div class="col-md-8 col-xl-7">
            <a href="#" class="category_name purple"><?=$item["meta_gallery_images_count"]; ?> fotos </a><p class="author_name">von <a href="<?=$item["author_link"];?>" style="color:#FFF;"><?=$item["author_display_name"];?></a></p>
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
        <hr>
        <div class="mb-8">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
          <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

          <script>
            jQuery(document).ready(function(){
              jQuery(window).scroll(function(){
                var scroll = $(window).scrollTop();
                var bot = jQuery(".photo_gallery").offset().top+jQuery(".photo_gallery").outerHeight();
                if(jQuery(".photo_gallery img.disabled").length){
                  if(jQuery(".photo_gallery img.disabled").offset().top < (scroll+$(window).height()-100)){
                    jQuery("#loading_gallery:hidden").slideDown();
                    for(i=0;i<5;i++){
                      var th = jQuery(".photo_gallery img.disabled:first");
                      th.hide();
                      th.prop("src",th.attr("attr-src")).removeClass("disabled").delay(300).slideDown(300,function(){
                        jQuery(this);
                      });
                    }
                    <?php if(!get_current_user_id()){ ?>
                      if(jQuery(".photo_gallery img:not(.disabled)").length==20){
                        jQuery("#login_gallery").delay(200).slideDown();
                        jQuery("#loading_gallery:visible").delay(200).slideUp();
                        jQuery(".login_link").click();
                      }
                    <?php } ?>
                    jQuery("#loading_gallery:visible").delay(200).slideUp();
                  }
                }
              });
            });
          </script>
          
          <?php 
          $gallery = unserialize($item["meta_gallery_images"]); 
          if(!get_current_user_id()){
            $gallery = array_slice($gallery,0,20);
          }
          ?>
          <ul class="photo_gallery">
            <?php foreach($gallery as $i=>$gallery_item){ ?>
              <li><a href="<?=$gallery_item["image"];?>" data-fancybox="gallery"><img <?php if($i<5){ ?> src="<?=$gallery_item["thumb"];?>" <?php }else{ ?> src="" attr-src="<?=$gallery_item["thumb"];?>" class="disabled" <?php } ?> /></a></li>
            <?php } ?>
          </ul>
          <div id="loading_gallery" style="display:none;">LOADING</div>
          <div id="login_gallery" class="text-center" style="display:none;">
            <a href="#" class="voucher_red btn-primary mb-2 mt-3 mx-0" data-toggle="modal" data-target="#login_popup">Log dich ein um die gesamte Fotogallerie zu sichten</a>
            <?php include(locate_template("views/pieces/login_modal.php"));?>
          </div>

        </div>
      </div>
      <div class="col-lg-3 pt-5 grey_sidebar">
        <div class="photos_single_sidebar sidebar sticky_sidebar white_tickets_bg">
            <?php dynamic_sidebar("photos_single_sidebar"); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="photo_single">
  <hr class="mt-0">
  <section class="related_videos pt-5 pb-6">
    <div class="container ">
      <h3 class="text-uppercase mb-4">FOTOS AUS DIESER LOCATION</h3>
      <div class="events_photos p-0" style="background:none;">
        <div class="row">
          <?php 
          $args = array("search" => array("tax_venue"=>$venue_id, "exclude"=>array($item["post_id"])), "order"=>"rand");
          $results = apply_filters('tdf_get_posts', "photos", 6, 0, $args);

          foreach($results["items"] as $item){
            $exclude_ids[]=$item["post_id"];
            echo '<div class="col-md-4 mb-4">';
            include(locate_template("/views/posts/photos/content-item.php")); 
            echo '</div>';
          }
          ?>  
        </div>
      </div>
    </div>
  </section>
</div>
<?php include(locate_template("views/pieces/newsletter_black.php")); ?>            


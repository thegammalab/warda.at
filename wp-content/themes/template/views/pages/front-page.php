<?php
$feat_results = apply_filters('tdf_get_posts', "post", 5, 0, array("search" => array("meta__is_ns_featured_post"=>"yes")));
$exclude_ids = array();
?>
<div class="homepage">
  <section class="featured_article">
    <div class="container-fluid p-0 m-0">
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <?php foreach($feat_results["items"] as $i=>$item){ ?>
            <li data-target="#carouselExampleIndicators" data-slide-to="<?=$i;?>" class="<?php if($i==0){echo "active";} ?>"></li>
          <?php } ?>
        </ol>
        <div class="carousel-inner">
          <?php 
          foreach($feat_results["items"] as $i=>$item){ 
            // print_r($item);
            $exclude_ids[]=$item["post_id"]; 
            if(isset($item["tax_array_category"][0])){
              $main_cat = $item["tax_array_category"][0];
            }else{
              $main_cat = false;
            }
            if($main_cat->parent){
                $level0 = $main_cat->parent;
            }else{
                $level0 = $main_cat->term_id;
            }
            ?>
            <div class="carousel-item <?php if($i==0){echo "active";} ?>">
              <div class="row m-0">
                <div class="col-md-7 p-0">
                  <div class="header_featured_img">
                    <?php if(!is_wp_error($main_cat) && isset($main_cat)){ ?>
                      <a href="<?= get_term_link($main_cat); ?>" class="category_name <?php if($color = get_term_meta($level0,"theme_color",true)){echo $color;}else{echo 'yellow';} ?>"><?= $main_cat->name; ?></a>
                    <?php } ?>
                    <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "medium_crop"); ?></a>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="header_featured_body">
                    <a href="<?= $item["post_permalink"]; ?>"><h1><?= $item["post_title"]; ?></h1></a>
                    <p><?= $item["post_excerpt"]; ?></p>
                    <div class="author">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
      
    </div>
  </section>
  <section class="top_articles">
    <div class="container">
      <div class="row">
        <?php 
        $args = array("search" => array("exclude"=>$exclude_ids),"order"=>"date");
        $results = apply_filters('tdf_get_posts', "post", 3, 0, $args);

        foreach($results["items"] as $item){
          $exclude_ids[]=$item["post_id"];
          echo '<div class="col-md-4">';
          include(locate_template("/views/posts/post/content-item.php")); 
          echo '</div>';
        }
        ?>
      </div>
    </div>
  </section>
  <hr>
  <section class="more_articles" style="background:none;">
    <div class="container">
      <div class="row double_space">
        <div class="col-md-8 right_border_col">
          <?php 
          $args = array("search" => array("exclude"=>$exclude_ids),"order"=>"date");
          $results = apply_filters('tdf_get_posts', "post", 6, 0, $args);

          foreach($results["items"] as $item){
            $exclude_ids[]=$item["post_id"];
            include(locate_template("/views/posts/post/content-item-wide.php")); 
          }
          ?>
        </div>
        <div class="col-md-4">
          <div class="frontpage_sidebar sidebar sticky_sidebar white_tickets_bg">
            <?php dynamic_sidebar("frontpage_sidebar"); ?>
          </div>
          <!-- <div class="mb-4">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/podcast.jpg" alt="">
          </div>
          <div class="mb-4">
            <div class="d-flex justify-content-between">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/fb_box.jpg" alt="">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/ig_box.jpg" alt="">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/yt_box.jpg" alt="">
            </div>
          </div>
          <div class="mb-4">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/tiktok.jpg" alt="">
          </div>
          <div class="ticket_box position-relative">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/ticket1.jpg" alt="">
            <div class="position-absolute event_date">
              <h2 class="text-center">13 <br> mar</h2>
            </div>
            <div class="ticket_details">
              <a href="#" class="category_name red">Clubbing #hiphop</a>
              <h3>6 YEARS OF PSYPERIENCE
                MADNESS ON 4 FLOORS!
              </h3>
              <div class="event_place"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/pin_icon.svg" alt=""> PRATERSAUNA <br>
                Waldsteingartenstraße 135, 1020 Wien
              </div>
              <div class="divider">

              </div>
              <a href="#" class="btn-primary d-block buy_ticket">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket.svg" alt="">1X2 TICKETS
              </a>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </section>
  
  <?php include(locate_template("views/pieces/newsletter_yellow.php")); ?>            

  <section class="homepage_events_photos">
    <div class="container">
      <div class="row align-items-center mb-4">
        <div class="col-md-8">
          <div class="section_title">
            <h5 class="white">Wir waren unterwegs</h5>
            <h2 class="mb-0 white">Aktuelle Fotos</h2>
          </div>

        </div>

        <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
          <a href="<?=get_post_type_archive_link("photos");?>" class="btn-primary purple">Alle Fotos</a>
        </div>
      </div>
      <div class="row half_space">
        
          <?php 
            $args = array("search" => array());
            $results = apply_filters('tdf_get_posts', "photos", 5, $page, $args);
            foreach($results["items"] as $i=>$item){
              if($i==1){
                echo '<div class="col-md-8 mb-3 double_size">';
              }else{
                echo '<div class="col-md-4 mb-3">';
              }
              include(locate_template("/views/posts/photos/content-item.php")); 
              echo '</div>';
            }
          ?>
        </div>
      </div>
    </section>
    <section class="lifestyle_mag">
      <div class="container">
        <div class="row align-items-center mb-4">
          <div class="col-md-8">
            <div class="section_title">
              <h5 class="yellow_text">Magazin</h5>
              <h2 class="mb-0">Lifestyle</h2>
            </div>

          </div>

          <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
            <a href="<?=get_term_link(7);?>" class="btn-primary yellow">Lifestyle Beiträge</a>
          </div>
        </div>
        <div class="row">
          <?php 
            $args = array("search" => array("tax_category" => 7, "exclude"=>$exclude_ids));
            $results = apply_filters('tdf_get_posts', "post", 5, $page, $args);
            foreach($results["items"] as $i=>$item){
              if($i%10==0 || $i%10==9){
                if($results["items"][$i]){
echo '<div class="col-md-6">';
include(locate_template("/views/posts/post/content-item-big.php")); 
echo '</div>';
                }
              }else{
                if($i%10==1 || $i%10==5){
echo '<div class="col-md-6"><div class="row">';
                }
                
                if($results["items"][$i]){
echo '<div class="col-md-6">';
include(locate_template("/views/posts/post/content-item-small.php")); 
echo '</div>';
                }

                if($i%10==4 || $i%10==8){
echo '</div></div>';
                }
              }
            }
            if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
            echo '</div></div>';
            }
            ?>
        </div>
      </section>

      <?php
      $feat_events = apply_filters('tdf_get_posts', "events", 1, 0, array("search" => array("meta__is_ns_featured_post"=>"yes", "exclude"=>$exclude_ids)));
      foreach($feat_events["items"] as $item){
        $exclude_ids[] = $item["post_id"];
          include(locate_template("/views/posts/events/featured_item.php")); 
      }
      ?>

      <section class="party_mag">
        <div class="container">
          <div class="row align-items-center mb-4">
            <div class="col-md-8">
              <div class="section_title">
                <h5 class="pink_text">Magazin</h5>
                <h2 class="mb-0">Nightlife</h2>
              </div>

            </div>

            <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
              <a href="<?=get_term_link(949);?>" class="btn-primary pink">Nightlife Beiträge</a>
            </div>
          </div>
          <div class="row">
            <?php 
              $args = array("search" => array("tax_category" => 949, "exclude"=>$exclude_ids));
              $results = apply_filters('tdf_get_posts', "post", 5, $page, $args);
              foreach($results["items"] as $i=>$item){
                if($i%10==4 || $i%10==9){
if($results["items"][$i]){
  echo '<div class="col-md-6">';
  include(locate_template("/views/posts/post/content-item-big.php")); 
  echo '</div>';
}
                }else{
if($i%10==0 || $i%10==5){
  echo '<div class="col-md-6"><div class="row">';
}

if($results["items"][$i]){
  echo '<div class="col-md-6">';
  include(locate_template("/views/posts/post/content-item-small.php")); 
  echo '</div>';
}

if($i%10==3 || $i%10==8){
  echo '</div></div>';
}
                }
              }
              if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
              echo '</div></div>';
              }
              ?>
          </div>
        </section>


        <!-- Hidden by request (trello task) -->
        <!-- <section class="video_section">
          <div class="container">
            <div class="row align-items-center mb-4">
              <div class="col-md-8">
                <div class="section_title">
                  <h5 class="fuchsia_text">Warda TV</h5>
                  <h2 class="mb-0">Aktuelle Videos</h2>
                </div>
              </div>

              <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
                <a href="<?=get_bloginfo("url")."/videos/";?>" class="btn-primary fuchsia">Video Beiträge</a>
              </div>
            </div>
            <div class="row half_space">
                <?php
                  $feat_events = apply_filters('tdf_get_posts', "videos", 6, 0, array("search" => array("exclude"=>$exclude_ids1)));
                  foreach($feat_events["items"] as $item){
                    $exclude_ids[] = $item["post_id"];
                    ?>
                    <div class="col-md-4">
                      <div class="mb-5">
                        <?php include(locate_template("/views/posts/videos/content-item.php")); ?>
                      </div>
                    </div>
                <?php }?>
            </div>
          </div>
        </section> -->



        <section class="food_mag">
          <div class="container">
            <div class="row align-items-center mb-4">
              <div class="col-md-8">
                <div class="section_title">
<h5 class="light_green_text">Magazin</h5>
<h2 class="mb-0">Essen & Trinken</h2>
                </div>

              </div>

              <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
                <a href="<?=get_term_link(948);?>" class="btn-primary light_green">Kulinarik Beiträge</a>
              </div>
            </div>
            <div class="row">
          <?php 
            $args = array("search" => array("tax_category" => 948, "exclude"=>$exclude_ids));
            $results = apply_filters('tdf_get_posts', "post", 5, $page, $args);
            foreach($results["items"] as $i=>$item){
              if($i%10==0 || $i%10==9){
                if($results["items"][$i]){
echo '<div class="col-md-6">';
include(locate_template("/views/posts/post/content-item-big.php")); 
echo '</div>';
                }
              }else{
                if($i%10==1 || $i%10==5){
echo '<div class="col-md-6"><div class="row">';
                }
                
                if($results["items"][$i]){
echo '<div class="col-md-6">';
include(locate_template("/views/posts/post/content-item-small.php")); 
echo '</div>';
                }

                if($i%10==4 || $i%10==8){
echo '</div></div>';
                }
              }
            }
            if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
            echo '</div></div>';
            }
            ?>
        </div>
  </section>


          <section class="home_voucher_section">
            <div class="container">
              <div class="row align-items-center mb-4">
                <div class="col-md-8">
<div class="section_title">
  <h5 class="voucher_text">Entdecke unsere Deals</h5>
  <h2 class="mb-0">Gutscheine</h2>
</div>
                </div>
                <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
<a href="<?=get_bloginfo("url")."/gutscheine/";?>" class="btn-primary voucher_red">Alle Gutscheine</a>
                </div>
              </div>
              <div class="row white_tickets_bg">
                <?php
                $feat_events = apply_filters('tdf_get_posts', "vouchers", 3, 0, array("search" => array("exclude"=>$exclude_ids)));
                foreach($feat_events["items"] as $item){
$exclude_ids[] = $item["post_id"];
?>
<div class="col-md-4">
  <?php include(locate_template("/views/posts/vouchers/content-item.php")); ?>
</div>
                <?php }?>
              </div>
            </div>
          </section>

          
  <section class="more_events">
    <div class="container">
      <div class="row align-items-center mb-4">
        <div class="col-md-8">
          <div class="section_title">
            <h5 class="green_text">HIER KANNST DU FEIERN</h5>
            <h2 class="mb-0">Aktuelle Events</h2>
          </div>
        </div>
        <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
          <a href="<?=get_bloginfo("url")."/events/";?>" class="btn-primary green_blue">Alle Events</a>
        </div>
      </div>
      <div class="row">
        <?php
        $feat_events = apply_filters('tdf_get_posts', "events", 3, 0, array("search" => array("exclude"=>$exclude_ids)));
        foreach($feat_events["items"] as $item){
        $exclude_ids[] = $item["post_id"];
        ?>
          <div class="col-md-4 mb-4 mb-md-0">
            <?php include(locate_template("/views/posts/events/content-item.php")); ?>
          </div>
        <?php }?>
      </div>
    </div>
  </section>

  <section class="gaming_mag">
    <div class="container">
      <div class="row align-items-center mb-4">
        <div class="col-md-8">
          <div class="section_title">
            <h5 class="blue_text">Magazin</h5>
            <h2 class="mb-0">Gaming</h2>
          </div>
        </div>
        <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
          <a href="<?=get_term_link(969);?>" class="btn-primary blue">Gaming Beiträge</a>
        </div>
      </div>
      <div class="row">
            <?php 
              $args = array("search" => array("tax_category" => 969, "exclude"=>$exclude_ids));
              $results = apply_filters('tdf_get_posts', "post", 5, $page, $args);
              foreach($results["items"] as $i=>$item){
                if($i%10==4 || $i%10==9){
                  if($results["items"][$i]){
                    echo '<div class="col-md-6">';
                    include(locate_template("/views/posts/post/content-item-big.php")); 
                    echo '</div>';
                  }
                }else{
                  if($i%10==0 || $i%10==5){
                    echo '<div class="col-md-6"><div class="row">';
                  }

                  if($results["items"][$i]){
                    echo '<div class="col-md-6">';
                    include(locate_template("/views/posts/post/content-item-small.php")); 
                    echo '</div>';
                  }

                  if($i%10==3 || $i%10==8){
                    echo '</div></div>';
                  }
                }
              }
              if($i%5!=4 && $i%10!=8 && $i%10!=0 && $i%10!=9){
              echo '</div></div>';
              }
              ?>
    </div>
  </section>


  <section class="tickets">
    <div class="container">
      <div class="row align-items-center mb-4">
        <div class="col-md-8">
          <div class="section_title">
            <h5 class="pink_text">HIER KANNST DU FEIERN</h5>
            <h2 class="mb-0">Gewinnspiele</h2>
          </div>
        </div>
        <div class="col-md-4 d-flex justify-content-md-end mt-2 mt-md-0">
          <a href="<?=get_bloginfo("url")."/gewinnspiel/";?>" class="btn-primary red">Alle Gewinnspiele</a>
        </div>
      </div>
      <div class="row white_tickets_bg">
        <?php
        $feat_events = apply_filters('tdf_get_posts', "sweepstakes", 3, 0, array("search" => array("exclude"=>$exclude_ids)));
        foreach($feat_events["items"] as $item){
          $exclude_ids[] = $item["post_id"];
          ?>
          <div class="col-md-4">
            <?php include(locate_template("/views/posts/sweepstakes/content-item.php")); ?>
          </div>
        <?php }?>
      </div>
    </div>
  </section>

  <?php include(locate_template("views/pieces/instagram.php")); ?>

</div>

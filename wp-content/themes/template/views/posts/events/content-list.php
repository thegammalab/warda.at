<?php
global $wp;
$featured_ids = array();
$page = get_query_var("paged");

if (!$page) {
  $page = 1;
}
?>
<div class="events_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 col-xl-3 pt-5">
        <div class="left_sidebar sticky_sidebar">
          <form action="" class="filters">
            <input type="hidden" name="post_type" value="events" />
            <input type="hidden" name="per_page" value="10" />
            <input type="hidden" id="page_no" name="page" value="1" />

            <div class="search_bar d-block d-md-none">
              <div class="form-group align-middle mb-0">
                <div class="input-group ">
                  <input class="form-control text-uppercase search_key" name="args[search][key]" id="search_input_left" type="text" placeholder="Suche nach Events, Locations, Künstlern und Genres">
                  <div class="input-group-append">
                    <button type="button" id="search_but_left" class="input-group-text call-to-action-btn btn-primary">
                      <span class="d-none d-lg-block">Suchen</span>
                      <span class="d-block d-lg-none"><i class="fa fa-search"></i></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="sweepstakes_filters">
              <h5 class="">filter</h5>
              <?php

              $category_slug = "date";
              $category_name = "Datum";
              include(locate_template("views/pieces/filters/filter_date_future.php"));

              $category_slug = "genre";
              $category_name = "Genre";
              include(locate_template("views/pieces/filters/filter_checkbox.php"));

              // $category_slug = "venue";
              // $category_name = "Venue";
              // include(locate_template("views/pieces/filters/filter_checkbox.php"));

              $post_filter = "_events";
              include(locate_template("views/pieces/filters/events_scripts.php"));
              ?>
            </div>
          </form>
        </div>


      </div>
      <div class="col-md-8 col-xl-6 white_bg">
        <div class="mb-5">
          <div class="search_bar d-none d-md-block">
            <div class="form-group align-middle mb-0">
              <div class="input-group ">
                <input class="form-control text-uppercase search_key" id="search_input_right" type="text" placeholder="Suche nach Events, Locations, Künstlern und Genres">
                <div class="input-group-append">
                  <button type="button" class="input-group-text btn-primary voucher_yellow" id="search_but_right"><span class="d-none d-lg-block">Suchen</span>
                    <span class="d-block d-lg-none"><i class="fa fa-search"></i></span></button>
                </div>
              </div>
            </div>
          </div>
          <div id="selected_filter_list">
            <ul class="filter_list">
              <!-- <li><a href="#">Nightlife </a><a href="#"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-yellow.svg" alt=""></a></li>
              <li><a href="#">Innerhalb 100.00 km</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-yellow.svg" alt=""></a></li> -->
            </ul>
          </div>
        </div>
        
        <div class="events_photos p-0" id="results_div" style="background:none;">
            <div class="row">
                <?php 
                $date = "";
                $results = apply_filters('tdf_get_posts', "events", get_option("posts_per_page"), $page, array("order"=>"meta_asc_the_date","search"=>array("meta_the_date_more"=>time())));
                if(count($results["items"])){
                  foreach($results["items"] as $item){
                      if($item["meta_the_date"]!=$date){
                          $date = $item["meta_the_date"];
                          echo '<div class="col-12 mt-4"><div class="date_bar"><span>'.strftime("%A",($item["meta_the_date"])).'</span><h3>'.strftime("%e. %B %Y",($item["meta_the_date"])).'</h3></div></div>';
                      }
                      echo '<div class="col-12 mb-4">';
                    if(get_post_meta($item["post_id"],"_is_ns_featured_post",true)){
                      include(locate_template("/views/posts/events/wide_item-featured.php")); 
                    }else{
                      include(locate_template("/views/posts/events/wide_item.php")); 
                    }
                      echo '</div>';
                  }
                }else{
                  echo '<div class="col-12 text-center py-5"><h3>Sorry, keine Treffer</h3></div>';
                }
                ?>
            </div>
        </div>
        
         <div class="m-auto pb-5">
              <a href="" class="load_more_btn" id="load_more_button" data-total="<?=$results["total_posts"];?>" data-page="<?=$page;?>" data-perpage="<?=get_option("posts_per_page");?>" <?php if(($page*get_option("posts_per_page"))>$results["total_posts"]){ ?>style="display:none"<?php } ?>>Mehr Laden</a>
        </div>
      </div>
      <div class="d-none d-xl-block col-xl-3 pt-5 sidebar">
        <div class="sticky_sidebar white_tickets_bg">
              <?php dynamic_sidebar("event_list_sidebar"); ?>
            </div>
      </div>
    </div>
  </div>
</div>
<?php include(locate_template("views/pieces/newsletter_black.php")); ?>            

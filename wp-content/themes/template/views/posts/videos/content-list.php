<div class="videos_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 col-xl-3 pt-5 bg_black">
         <div class="left_sidebar">
          <form action="" class="filters">
            <input type="hidden" name="post_type" value="videos" />
            <input type="hidden" name="per_page" value="999" />
            <input type="hidden" name="page" value="1" />

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

              $category_slug = "events_cat";
              $category_name = "Kategorie";
              include(locate_template("views/pieces/filters/filter_checkbox.php"));



              include(locate_template("views/pieces/filters/scripts.php"));
              ?>
            </div>
          </form>
        </div>

      </div>
      <div class="col-md-8 col-xl-6 grey_bg">
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
        <div class="events_photos p-0 mb-4" style="background:none;">
            <div class="row" id="results_div">
                <?php 
                $date = "";
                $results = apply_filters('tdf_get_posts', "videos", 20, 0, array("order"=>"meta_the_date_desc"));
                if(count($results["items"])){
                  foreach($results["items"] as $item){
                      echo '<div class="col-md-6 mb-6">';
                      include(locate_template("/views/posts/videos/content-item.php")); 
                      echo '</div>';
                  }
                }else{
                  echo '<div class="col-12 text-center py-5"><h3>Sorry, keine Treffer</h3></div>';
                }
                ?>
            </div>
        </div>
        <div class="m-auto d-none1">
          <a href="" class="load_more_btn">Mehr Laden</a>
        </div>
      </div>
      <div class="d-none d-xl-block col-xl-3 pt-5 sidebar">
        <div class=" white_tickets_bg sidebar sticky_sidebar">
            <?php dynamic_sidebar("videos_list_sidebar"); ?>
        </div>
      </div>
    </div>
  </div>
</div>
  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>

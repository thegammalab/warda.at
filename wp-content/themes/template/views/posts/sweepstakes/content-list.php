
<div class="tickets_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 col-xl-2 pt-5 the_sidebar">
         <div class="left_sidebar">
          <form action="" class="filters">
            <input type="hidden" name="post_type" value="sweepstakes" />
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
              $category_slug = "sweepstakes_cat";
              $category_name = "Kategorie";
              include(locate_template("views/pieces/filters/filter_icons.php"));

              include(locate_template("views/pieces/filters/scripts.php"));
              ?>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-9 col-xl-10 white_bg pr-xl-4">
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

        <div class="row mb-8"  id="results_div">
            <?php
            $feat_events = apply_filters('tdf_get_posts', "sweepstakes", 99, 0, array("search" => array("exclude"=>$exclude_ids)));
            if(count($feat_events["items"])){
              foreach($feat_events["items"] as $item){
                $exclude_ids[] = $item["post_id"];
                include(locate_template("/views/posts/sweepstakes/ajax_load_item.php")); 
              }
            }else{
              echo '<div class="col-12 text-center py-5"><h3>Sorry, keine Treffer</h3></div>';
            }
            ?>
        </div>
      </div>
      <div class="d-none d-xl-block col-xl-1 white_bg pl-0 pr-0"></div>
    </div>
  </div>
</div>
  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>


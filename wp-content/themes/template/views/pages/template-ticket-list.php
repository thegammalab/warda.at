<?php
/*
Template Name: ticket list
*/


?>

<div class="tickets_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 col-xl-3 pt-5">
         <div class="left_sidebar">
          <form action="" class="search_bar d-block d-md-none">
            <div class="form-group align-middle mb-0">
              <div class="input-group ">
                <input class="form-control text-uppercase" type="text" placeholder="Suche nach Events, Locations, Künstlern und Genres">
                <div class="input-group-append">
                  <button type="submit" class="input-group-text call-to-action-btn btn-primary">
                    <span class="d-none d-lg-block">Suchen</span>
                    <span class="d-block d-lg-none"><i class="fa fa-search"></i></span>
                  </button>
                </div>
              </div>
            </div>
          </form>
          <div class="d-block d-md-none">
             <ul class="filter_icons d-flex">
                <li class="active"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon1_yellow.png" alt="">Essen</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon2_yellow.png" alt="">Freizeit</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon3_yellow.png" alt="">Nightlife</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon4_yellow.png" alt="">Fashion</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon5_yellow.png" alt="">Wohnen</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon6_yellow.png" alt="">Reisen</li>
              </ul>
          </div>
          <div class="d-none d-md-block">
            <h5 class="">filter</h5>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Kategorie </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter_icons">
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon1_yellow.png" alt="">Essen</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon2_yellow.png" alt="">Freizeit</li>
                <li class="active"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon3_yellow.png" alt="">Nightlife</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon4_yellow.png" alt="">Fashion</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon5_yellow.png" alt="">Wohnen</li>
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon6_yellow.png" alt="">Reisen</li>
              </ul>
            </div>

          </div>
        </div>
      </div>
      <div class="col-md-9 col-xl-8 white_bg pr-xl-0">
        <div class="mb-5">
          <form action="" class="search_bar d-none d-md-block">
            <div class="form-group align-middle mb-0">
              <div class="input-group ">
                <input class="form-control text-uppercase" type="text" placeholder="Suche nach Events, Locations, Künstlern und Genres">
                <div class="input-group-append">
                  <button type="submit" class="input-group-text btn-primary voucher_yellow"><span class="d-none d-lg-block">Suchen</span>
                    <span class="d-block d-lg-none"><i class="fa fa-search"></i></span></button>
                </div>
              </div>
            </div>
          </form>
          <ul class="filter_list">
            <li><a href="#">Nightlife </a><a href="#"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-yellow.svg" alt=""></a></li>
            <li><a href="#">Innerhalb 100.00 km</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-yellow.svg" alt=""></a></li>

          </ul>
        </div>

        <div class="row mb-8">
          <?php for($i=0;$i<6;$i++){ ?>
            <div class="col-sm-6 col-lg-4">
              <div class="ticket_box position-relative">
                <a href="<?=get_bloginfo("url"); ?>/ticket/details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/ticket1.jpg" class="w-100"></a>
                <div class="position-absolute event_date">
                  <h2 class="text-center">13 <br> mar</h2>
                </div>
                <div class="ticket_details">
                  <a href="#" class="category_name red">Clubbing #hiphop</a>
                  <a href="<?=get_bloginfo("url"); ?>/ticket/details/"><h3>6 YEARS OF PSYPERIENCE
                    MADNESS ON 4 FLOORS!
                  </h3></a>
                  <div class="event_place"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/pin_icon.svg" alt=""> PRATERSAUNA <br>
                    Waldsteingartenstraße 135, 1020 Wien
                  </div>
                  <div class="divider">

                  </div>
                  <a href="<?=get_bloginfo("url"); ?>/ticket/details/" class="btn-primary d-block buy_ticket">
                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket.svg" alt="">1X2 TICKETS
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="d-none d-xl-block col-xl-1 white_bg pl-0 pr-0"></div>
    </div>
  </div>
  <section class="newsletter_signup black_signup">
    <div class="container">
      <div class="mb-3 text-center">
        <h2 class="mb-3 pr-3 text-uppercase color-black">Ride with us!</h2>
        <p>Immer gut unterwegs mit unserem WARDA CREWSLETTER!</p>
      </div>
      <form action="">

        <div class="form-group mr-3">
          <label for="" class="sr-only">Your Email Address</label>
          <input type="text" class="form-control" id="inputPassword2" placeholder="Password">
        </div>
        <button type="submit" class=" btn-primary">Subscribe</button>
      </form>
    </div>
  </section>
</div>

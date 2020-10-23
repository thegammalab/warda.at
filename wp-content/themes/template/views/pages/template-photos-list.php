<?php
/*
Template Name: photos list
*/


?>

<div class="photos_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 col-xl-3 pt-5 bg_black">
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
            <div class="filters_list">
              <div><select class="form-control"><option>Datum</option></select></div>
              <div><select class="form-control"><option>Location</option></select></div>
            </div>
          </div>
          <div class="d-none d-md-block">
            <h5 class="">filter</h5>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Datum </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
                <li>Pick a Date</li>
                <li><input type="checkbox" aria-label="Checkbox for following text input">Beliebiges Datum</li>
                <li><input type="checkbox" aria-label="Checkbox for following text input">Tomorrow</li>
                <li class="purple_text"><i class="fas fa-chevron-down"></i>Weitere Termine</li>
              </ul>
            </div>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Location </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
                <li><input type="checkbox" aria-label="Checkbox for following text input">Beliebiges Datum</li>
                <li><input type="checkbox" aria-label="Checkbox for following text input">Tomorrow</li>
                <li class="purple_text"><i class="fas fa-chevron-down"></i>Weitere Termine</li>
              </ul>
            </div>
          </div>
        </div>

      </div>
      <div class="col-md-8 col-xl-6 grey_bg">
        <div class="mb-5">
          <form action="" class="search_bar d-none d-md-block">
            <div class="form-group align-middle mb-0">
              <div class="input-group ">
                <input class="form-control text-uppercase" type="text" placeholder="Suche nach Events, Locations, Künstlern und Genres">
                <div class="input-group-append">
                  <button type="submit" class="input-group-text btn-primary">
                    <span class="d-none d-lg-block">Suchen</span>
                    <span class="d-block d-lg-none"><i class="fa fa-search"></i></span>  

                  </button>
                </div>
              </div>
            </div>
          </form>
          <ul class="filter_list">
            <li><a href="#">Heute </a><a href="#"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-purple.svg" alt=""></a></li>
            <li><a href="#">Rock</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon-purple.svg" alt=""></a></li>

          </ul>
        </div>
         <?php for($j=0;$j<3;$j++){ ?>
        <div class="date_bar mt-5">
          <span>ALLE FOTOS VOM</span>
          <h3>MONTAG, 9. MÄRZ 2020</h3>
        </div>

        <div class="row">
          <?php for($i=0;$i<4;$i++){ ?>
          <div class="col-md-6 mb-3">
            <div class="position-relative">
              <a href="<?=get_bloginfo("url"); ?>/fotos/details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/photo1.jpg" class="w-100" alt=""></a>
              <a href="<?=get_bloginfo("url"); ?>/fotos/details/"><div class="photo_date_place position-absolute">
                <p>22. Feb / Tim Konig</p>
                <h5>BEAT IT @ Flex, Wien</h5>
              </div></a>
              <div class="position-absolute photo_number">
                <h2 class="text-center">59</h2>
                <p>Fotos</p>
              </div>

            </div>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
        <div class="m-auto">
          <a href="" class="load_more_btn">load more</a>

        </div>
      </div>
      <div class="d-none d-xl-block col-xl-3 pt-5 sidebar">


        <div class="ticket_box position-relative">
          <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/ticket1.jpg" alt="">
          <div class="position-absolute event_date">
            <h2 class="text-center">13 <br> mar</h2>
          </div>
          <div class="ticket_details">
            <a href="#" class="category_name yellow">Clubbing #hiphop</a>
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
            <div class="ticket_disclaimer">Teilnahmeschluss: 19.03.2020 <br>19:00 Uhr</div>
          </div>
        </div>
        <div class="product_box">
          <div class="product_image">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/product.jpg" alt="">

          </div>
          <div class="product_details">
            <h5>NEOH CROSSBAR 9 X 30G | CHOCOLATE | RASPBERRY | COCONUT</h5>
            <p>€15,00 EUR </p>
          </div>
        </div>
      </div>
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

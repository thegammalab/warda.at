<?php
/*
Template Name: photo single
*/


?>


<div class="photo_single ">
  <div class="container">
    <div class="row">

      <div class="col-md-9  pt-5">
        <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/big_video.jpg" alt="" class="w-100 mb-4">

        <div class="row">
          <div class="col-md-4">
            <div class="date_bar border-0 p-0 mb-3">
              <span>Friday</span>
              <h3>14. February 2020</h3>
            </div>

          </div>
          <div class="col-xl-1 d-none d-xl-block"></div>
          <div class="col-md-8 col-xl-7">
            <a href="#" class="category_name purple">73 photos </a><p class="author_name">By MAXIMILIAN RÖDER</p>
            <h2 class="page_title">BEST OF FUNKY MONKEY</h2>
            <div class="event_address">
              <p class="text-uppercase"><b>Funky Monkey</b></p>
              <p class="text-uppercase">TERNGASSE 11, WIEN 1010</p>
            </div>
            <div class="sharethis">
              <p class="text-uppercase">teilen</p>
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/sharethis.png" alt="">
            </div>

          </div>
        </div>
        <hr>
        <div class="mb-8">
          <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/gallery.png" class="w-100" alt="">

        </div>
      </div>
      <div class="col-md-3 pt-5 grey_sidebar">
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
            <div class="ticket_disclaimer text-center">Teilnahmeschluss: 19.03.2020 <br>19:00 Uhr</div>

          </div>

        </div>
      </div>
    </div>
  </div>
  <hr class="mt-0">
  <section class="related_videos pt-5 pb-6">
    <div class="container ">
      <h3 class="text-uppercase mb-4">FOTOS AUS DIESER LOCATION</h3>
      <div class="row">
        <div class="col-md-4 mb-3">
          <div class="position-relative">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/photo1.jpg" alt="">
            <div class="photo_date_place position-absolute">
              <p>22. Feb / Tim Konig</p>
              <h5>BEAT IT @ Flex, Wien</h5>
            </div>
            <div class="position-absolute photo_number">
              <h2 class="text-center">59</h2>
              <p>Fotos</p>
            </div>

          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="position-relative">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/photo1.jpg" alt="">
            <div class="photo_date_place position-absolute">
              <p>22. Feb / Tim Konig</p>
              <h5>BEAT IT @ Flex, Wien</h5>
            </div>
            <div class="position-absolute photo_number">
              <h2 class="text-center">59</h2>
              <p>Fotos</p>
            </div>

          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="position-relative">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/photo1.jpg" alt="">
            <div class="photo_date_place position-absolute">
              <p>22. Feb / Tim Konig</p>
              <h5>BEAT IT @ Flex, Wien</h5>
            </div>
            <div class="position-absolute photo_number">
              <h2 class="text-center">59</h2>
              <p>Fotos</p>
            </div>

          </div>
        </div>
      </div>


    </div>
  </section>
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

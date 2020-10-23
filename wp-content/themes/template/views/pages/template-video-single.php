<?php
/*
Template Name: video single
*/


?>

<div class="video_single">
  <div class="container">
    <div class="row">

      <div class="col-md-9  pt-5">
        <div class="video_box position-relative">
          <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/big_video.jpg" alt="">
          <a href="#" class="play_btn position-absolute d-flex m-auto">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/playbtn.png" alt="">
          </a>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="date_bar border-0 p-0 mb-3">
              <span>Friday</span>
              <h3>14. February 2020</h3>
            </div>

          </div>
          <div class="col-xl-1 d-none d-xl-block"></div>
          <div class="col-md-8 col-xl-7">
            <a href="#" class="category_name fuchsia">Clubbing </a><p class="author_name">by by Jonas Grasberger</p>
            <h2 class="page_title">WAYFORM - AFTERMOVIE</h2>

            <div class="sharethis">
              <p class="text-uppercase">teilen</p>
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/sharethis.png" alt="">
            </div>

          </div>
        </div>
          <div class="content_area mb-8 mt-4">
            <p>Bei Mach Turbo Comedy Club erwartet euch das Beste was die junge, aufstrebende Stand Up Comedy Szene Österreichs zu bieten hat. Das Gilette unter den Stand Up Comedy Formaten, denn hier wird rasiert. Laut, provokant und verdammt ehrlich werden Stereotypen zerlegt und Schwierigkeiten mit der deutschen Sprache geklärt. Die kulturelle Vielfalt und die Einzigartigkeit der einzelnen Comedians öffnet Herzen und sorgt für unzählige Lacher.</p>

          </div>



      </div>
      <div class="col-md-3 pt-5 sidebar grey_sidebar">
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
      <h3 class="text-uppercase mb-4">EMPFOHLENE VIDEOS</h3>
      <div class="row">
        <div class="col-md-4">
          <div class="mb-5">
            <div class="video_box position-relative">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/video1.jpg" alt="">
              <a href="#" class="play_btn position-absolute d-flex m-auto">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/playbtn.png" alt="">
              </a>
            </div>
            <div class="photo_date_place text-center">
              <p class="">Mo, 24. Februar 2020</p>
              <div>
                <h5 class="fuchsia_text">Red bull Carneval 2020</h5>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-5">
            <div class="video_box position-relative">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/video1.jpg" alt="" >
              <a href="#" class="play_btn position-absolute d-flex m-auto">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/playbtn.png" alt="" >
              </a>
            </div>
            <div class="photo_date_place text-center">
              <p class="">Mo, 24. Februar 2020</p>
              <div>
                <h5 class="fuchsia_text">Red bull Carneval 2020</h5>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-5">
            <div class="video_box position-relative">
              <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/video1.jpg" alt="">
              <a href="#" class="play_btn position-absolute d-flex m-auto">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/playbtn.png" alt="">
              </a>
            </div>
            <div class="photo_date_place text-center">
              <p class="">Mo, 24. Februar 2020</p>
              <div>
                <h5 class="fuchsia_text">Red bull Carneval 2020</h5>
              </div>
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

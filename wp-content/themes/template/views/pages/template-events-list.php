<?php
/*
Template Name: Events list
*/


?>

<div class="events_page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4 col-xl-3 pt-5">
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
              <div><select class="form-control"><option>Category</option></select></div>
              <div><select class="form-control"><option>Genres</option></select></div>
              <div><select class="form-control"><option>Filtern nach</option></select></div>
              <div><select class="form-control"><option>Price</option></select></div>
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
                <li class="green_text"><i class="fas fa-chevron-down"></i>Weitere Termine</li>
              </ul>
            </div>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Kategorie </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
                <li><input type="checkbox" aria-label="Checkbox for following text input">Beliebiges Datum</li>
                <li><input type="checkbox" aria-label="Checkbox for following text input">Tomorrow</li>
                <li class="green_text"><i class="fas fa-chevron-down"></i>Weitere Termine</li>
              </ul>
            </div>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Genres </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
                <li><input type="checkbox" aria-label="Checkbox for following text input">Beliebiges Datum</li>
                <li><input type="checkbox" aria-label="Checkbox for following text input">Tomorrow</li>
                <li class="green_text"><i class="fas fa-chevron-down"></i>Weitere Termine</li>
              </ul>
            </div>
            <div class="filter_box">
              <div class="filter_title">
                <h5>Filtern nach </h5>
                <i class="fas fa-chevron-down"></i>
              </div>
              <ul class="filter1">
                <li><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/voucher_green.svg" alt="">Vouchers</li>
                <li class="active"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="" width="18" height="18">Sweepstakes</li>
              </ul>
            </div>
          </div>
        </div>


      </div>
      <div class="col-md-8 col-xl-6 white_bg">
        <div class="mb-5">
          <form action="" class="search_bar d-none d-md-block">
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
          <ul class="filter_list">
            <li><a href="#">Heute </a><a href="#"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon.svg" alt=""></a></li>
            <li><a href="#">Rock</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon.svg" alt=""></a></li>
            <li><a href="#">Sweepstakes</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon.svg" alt=""></a></li>
            <li><a href="#">bis 45€</a><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/close-icon.svg" alt=""></a></li>
          </ul>
        </div>
        <div class="date_bar">
          <span>Samstag</span>
          <h3>28. MÄRZ 2020</h3>
        </div>
        <div class="event_box wide_format">
          <div class="event_image">
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/event1.jpg" alt=""></a>
          </div>
          <div class="event_details pt-0">
            <a href="#" class="category_name ">Clubbing </a>
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><h3>ABGESAGT: MIXWOCH <br> @ VIE I PEE - FEBRUAR</h3></a>
            <div class="d-flex align-items-end justify-content-between mb-3">
              <div class="event_time mb-0">DONNERSTAG, 12. MÄRZ 2020
                ab 22:00
              </div>
              <div class="ticket_price">VVK € 21.00</div>
            </div>

            <div class="d-flex justify-content-end">
              <a href="<?=get_bloginfo("url"); ?>/events/event-details/" class="btn-primary buy_ticket "> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="">Gewinnspiel</a>

            </div>
          </div>
        </div>
        <div class="sponsored_event">
          <h5>Sponsored Event</h5>
          <div class="event_box wide_format ">
            <div class="event_image">
              <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/event1.jpg" alt=""></a>
            </div>
            <div class="event_details pt-0">
              <a href="#" class="category_name ">Clubbing </a>
              <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><h3>ABGESAGT: MIXWOCH <br> @ VIE I PEE - FEBRUAR</h3></a>
              <div class="d-flex align-items-end justify-content-between mb-3">
                <div class="event_time mb-0">DONNERSTAG, 12. MÄRZ 2020
                  ab 22:00
                </div>
                <div class="ticket_price">VVK € 21.00</div>
              </div>

              <div class="d-flex justify-content-end">
                <a href="<?=get_bloginfo("url"); ?>/events/event-details/" class="btn-primary buy_ticket "> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="">Gewinnspiel</a>

              </div>
            </div>
          </div>
        </div>
        <div class="date_bar">
          <span>Samstag</span>
          <h3>28. MÄRZ 2020</h3>
        </div>
        <div class="event_box wide_format">
          <div class="event_image">
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/event1.jpg" alt=""></a>
          </div>
          <div class="event_details pt-0">
            <a href="#" class="category_name ">Clubbing </a>
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><h3>ABGESAGT: MIXWOCH <br> @ VIE I PEE - FEBRUAR</h3></a>
            <div class="d-flex align-items-end justify-content-between mb-3">
              <div class="event_time mb-0">DONNERSTAG, 12. MÄRZ 2020
                ab 22:00
              </div>
              <div class="ticket_price">VVK € 21.00</div>
            </div>

            <div class="d-flex justify-content-end">
              <a href="<?=get_bloginfo("url"); ?>/events/event-details/" class="btn-primary buy_ticket "> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="">Gewinnspiel</a>

            </div>
          </div>
        </div>
        <div class="date_bar">
          <span>Samstag</span>
          <h3>28. MÄRZ 2020</h3>
        </div>
        <div class="event_box wide_format">
          <div class="event_image">
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/event1.jpg" alt=""></a>
          </div>
          <div class="event_details pt-0">
            <a href="#" class="category_name ">Clubbing </a>
            <a href="<?=get_bloginfo("url"); ?>/events/event-details/"><h3>ABGESAGT: MIXWOCH <br> @ VIE I PEE - FEBRUAR</h3></a>
            <div class="d-flex align-items-end justify-content-between mb-3">
              <div class="event_time mb-0">DONNERSTAG, 12. MÄRZ 2020
                ab 22:00
              </div>
              <div class="ticket_price">VVK € 21.00</div>
            </div>

            <div class="d-flex justify-content-end">
              <a href="<?=get_bloginfo("url"); ?>/events/event-details/" class="btn-primary buy_ticket "> <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket-green.svg" alt="">Gewinnspiel</a>

            </div>
          </div>
        </div>
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
            <div class="ticket_disclaimer text-center">Teilnahmeschluss: 19.03.2020 <br>19:00 Uhr</div>

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

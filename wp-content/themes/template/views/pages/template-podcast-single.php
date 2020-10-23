<?php
/*
Template Name: Podcast single
*/


?>

<div class="article podcast_single">
    <section class="header_article">
        <div class="container">
            <ul class="article_categories">
                <li><a href="#">Podcast</a></li>
            </ul>
            <h1>WARDA #12 Frisches Geld...</h1>
            <div class="card_box header_format">
                <div class="article_image">
                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/podcast_single_image.jpg" alt="">

                </div>
            </div>
        </div>
    </section>
</div>



<section class="more_articles">
    <div class="container">
        <div class="article_area">
            <div class="share_links">
                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/share.png" class="w-100" />
            </div>

            <div class="author mb-4">von <b><a href="#"> FABIAN PETSCHNIG </a></b>am 25.02.2020</div>
            <div class="row double_space">
                <div class="col-md-8 right_border_col">
                    <h6>Gestern ging die erste Folge von Frisches Geld auf Spotify online. Zwei richtig krasse Kontaktmänner plaudern aus dem Nähkästchen. Was euch genau erwartet, lest ihr hier:</h6>
                    <p>
                        Das Warda Superbrain und Chief of Everything Eugen Prosquill und einer der erfolgreichsten österreichischen DJs – Arash Rabbani a.k.a. DJ Mosaken – haben sich zusammengetan und beglücken euch ab sofort alle 14 Tage mit wilden Anekdoten, verrückten Geistesblitzen, deepen Gedankenexperimenten und vielem mehr – seid gespannt auf 45 Minuten puren Hörgenuss.
                    </p>

                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/podcast_player.jpg" class="w-100 mb-4" />


                    <h6>FIRST IMPRESSIONS - SUPERBOWL & SAFARI</h6>
                    <p>Bei Frisches Geld geht’s laut den beiden Protagonisten hauptsächlich um Musik, Essen, Reisen und – welch Überraschung – frisches Geld und Profitipps rundherum - auch wenn die beiden betonen, dass das nicht Prio 1 ist. In der ersten Folge bekommt man unter anderem Einblicke in Eugens ganz private und ziemlich abgefahrene Superbowl Erlebnisse. DJ Mosaken erzählt von seiner Tansania Reise – kurz gesagt, man lernt die beiden ein Stückchen besser und außerhalb ihrer Social-Media-Darstellung kennen. Auch die sweete Geschichte über den Anfang ihrer
                    </p>
                    <p>
                        Bromance wird preisgegeben. Neben dem ganzen Spaß und dem obligatorischen Leeren der Gin Tonic Gläser werden aber auch ernstere Themen nicht ausgespart – eine gute Mischung eben.
                    </p>
                    <h6>WHAT'S NEXT?</h6>
                    <p>Wer wissen möchte, was Eugen bei einem Englischkurs mit sieben Ü-60er Damen macht und was DJ Mosaken mit Töpfern am Hut hat, hört am besten selbst einmal hinein.
                        Filmempfehlungen und Kommentare zur Oscar-Verleihung kommen ebenfalls nicht zu kurz.
                    </p>
                    <p>Wir sind schon auf die zweite Folge Frisches Geld gespannt und hoffen, ihr habt genauso viel Spaß beim Zuhören wie wir. Hier könnt ihr übrigens den Podcast finden. </p>
                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/podcast_player.jpg" class="w-100" />

                </div>

                <div class="col-md-4">
                    <div class="mb-4">
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
                                <div class="event_place"><i class="fas fa-map-marker-alt"></i> PRATERSAUNA <br>
                                    Waldsteingartenstraße 135, 1020 Wien
                                </div>
                                <div class="divider">

                                </div>
                                <a href="#" class="btn-primary d-block buy_ticket">
                                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/icon-ticket.svg" alt="">1X2 TICKETS
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php for($i=0;$i<2;$i++){ ?>
                        <div class="mb-4">
                            <div class="card_box tall_format">
                                <div class="article_image">
                                    <a href="#" class="category_name blue">BERICHT</a>
                                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/article_image.jpg" alt="">
                                </div>
                                <div class="article_content">
                                    <div class="author">von <b><a href="#"> FABIAN PETSCHNIG </b></a>am 25.02.2020</div>
                                    <h3>DIGITAL DETOX - MIT
                                        DIGITALER ENTSCHLACKUNG
                                        GEGEN DIGITALE DEMENZ?
                                    </h3>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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

<section class="content_mag py-6">
    <div class="container">
        <h2 class="text-center mb-5">DAS KÖNNTE SIE AUCH INTERESSIEREN</h2>
        <div class="row">
            <?php for($i=0;$i<6;$i++){ ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card_box tall_format">
                        <div class="article_image">
                            <a href="#" class="category_name blue">BERICHT</a>
                            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/article_image.jpg" alt="">
                        </div>
                        <div class="article_content">
                            <div class="author">von <b><a href="#"> FABIAN PETSCHNIG </b></a>am 25.02.2020</div>
                            <h3>DIGITAL DETOX - MIT
                                DIGITALER ENTSCHLACKUNG
                                GEGEN DIGITALE DEMENZ?
                            </h3>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
</div>

<?php
/*
Template Name: Article
*/

echo do_shortcode('[s3browse bucket=assets.warda.at]');

die();
?>
<div class="article">
    <section class="header_article">
        <div class="container">
            <ul class="article_categories">
                <li><a href="#">Vienna Originals</a></li>
                <li><a href="#">Party</a></li>
            </ul>
            <h1>DAS HORST - TOTGEGLAUBTE LEBEN LÄNGER...</h1>
            <div class="card_box header_format">
                <div class="article_image">
                    <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/header_article.jpg" alt="">

                </div>
            </div>
        </div>

    </section>
</div>


<section class="more_articles">
    <div class="container">
        <div class="article_area">
            <div class="share_links"><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/share.png" class="w-100" /></div>

            <div class="author mb-4">von <b><a href="#"> FABIAN PETSCHNIG </a></b>am 25.02.2020</div>
            <div class="row double_space">
                <div class="col-md-8 right_border_col">

                    <p>Langsam aber sicher kehren wir auch die letzten Staubkörner des Jahres 2019 weg und können mit offenen Augen und Ohren ins neue Jahr schreiten. Was ihr erwarten dürft, haben wir für euch in einer kurzen Vorschau festgehalten.</p>
                    <p>Wie jedes Jahr kämpfen wir bereits im Jänner mit unseren Vorsätzen weniger zu feiern, mehr gesundes Zeug zu futtern und nicht mehr nur für das gute Gewissen eine Mitgliedschaft im
                        Fitnesscenter abzuschließen. Aber – ein ganz großes ABER – gerade kurz vor Jahreswechsel hatten ein paar neue Clubs eröffnet, es gab letztes Jahr ein Festivaldebüt und wir wissen jetzt schon, dass es kein leichtes wird, dass ihr im Jahr 2020 eure Füßchen stillhält. Was euch in nächster und auch ferner Zukunft dieses Jahr erwartet, erfahrt ihr jetzt:</p>
                        <p><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/im1.png" class="w-100" /></p>
                        <h5>WIEN – DIE KLEINE GROSSSTADT SCHÖPFT ENDLICH MEHR VON SEINEM POTENTIAL AU</h5>
                        <p>Ein wenig wehmütig blicke ich auf die letzten Stunden des Club Horst, der bald seine Pforten schließt. Nach der Eröffnung durfte ich damals davon berichten und nun – circa 2 Jahre
                            später - muss ich zusehen, wie diese Location auf sein Ende zugeht. Mit einem Knall und vielen kleinen Stolpersteinen hatte dieser Weg begonnen und mit einem phänomenalen
                            Abschiedswochenende danken euch die Clubbesitzer noch ein letztes Mal für eure Treue. Als kleines Schmankerl kommen sie am vorletzten Wochenende sogar noch mit einer Sex Positive Party an.</p>
                            <p>Ein Vögelchen hat mir gezwitschert, dass es ein massives Abrisswochenende mit 7 Bühnen gibt. Als Zusatz noch die alte Tanzschule über dem Horst und einer Afterhour. Sie lassen es also noch einmal so richtig krachen.</p>
                            <h5>ELEGANT UND AUSGELASSEN</h5>
                            <p>Geht es im Sechser zu. Ein weiteres Opening, über das wir uns freuen durften. Es ist nicht nur aufgrund seiner optischen Gestaltung ein wunderbares Ziel für Abende mit Freunden, sondern auch die Drinks und die Crew bescheren euch das absolute Feeling eines gelungenen und gemütlichen Abends. Wenn euch das ein bisschen zu ruhig ist, dürft ihr ganz unten auch ein bisschen die Sau rauslassen - aber alles mit einer Prise Stil und Eleganz. </p>
                            <p><img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/im2.png" class="w-100" /></p>
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

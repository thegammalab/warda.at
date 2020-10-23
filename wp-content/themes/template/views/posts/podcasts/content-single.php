
<div class="article podcast_single">
    <section class="header_article">
        <div class="container">
            <ul class="article_categories">
                <li><a href="#">Podcast</a></li>
            </ul>
            <h1><?php the_title(); ?></h1>
            <div class="card_box header_format">
                <div class="article_image">
                    <?= $item["featured_img_large_crop"]; ?>
                </div>
            </div>
        </div>
    </section>
</div>



<section class="more_articles">
    <div class="container">
        <div class="article_area">
            <div class="share_links"><?php echo do_shortcode('[mashshare]'); ?></div>
            <div class="author mb-4">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
            <div class="row double_space">
                <div class="col-md-8 right_border_col">
                    <?php the_content(); ?>
                </div>

                <div class="col-md-4">
                    <?php dynamic_sidebar("podcasts_single_sidebar"); ?>
                </div>
            </div>
        </div>
    </div>
</section>


  <?php include(locate_template("views/pieces/newsletter_black.php")); ?>


<section class="content_mag py-6 d-none">
    <div class="container">
        <h2 class="text-center mb-5">DAS KÖNNTE SIE AUCH INTERESSIEREN</h2>
        <div class="row">
            <?php for($i=0;$i<0;$i++){ ?>
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

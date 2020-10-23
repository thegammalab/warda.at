<?php
/*
Template Name: Podcast
*/


?>
<div class="article podcast">
    <section class="header_article">
        <div class="container">

            <?php
            $exclude_ids = array();
            $feat_events = apply_filters('tdf_get_posts', "podcasts", 1, 0, array("search" => array("exclude"=>$exclude_ids,"meta__is_ns_featured_post"=>"yes")));
            foreach($feat_events["items"] as $item){
                $exclude_ids[] = $item["post_id"];
                ?>
                <div class="card_box header_format">
                    <div class="article_image">
                        <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "full"); ?></a>
                    </div>
                </div>
                <div class="latest_podcast">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="author">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
                            <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
                            <?php echo do_shortcode($item["meta_podcast_shortcode"]); ?>
                        </div>
                        <?php 
                        if($item["meta_related_article"]){ 
                            $art_id = $item["meta_related_article"];
                            $art = apply_filters("tdf_get_single",$art_id);
                            ?>
                            <div class="col-md-3">
                                <div class="mb-3"><a href="<?= $art["post_permalink"]; ?>"><?= get_the_post_thumbnail($art["post_id"], "smaller_crop"); ?></a></div>
                                <div class="author">von <b><a href="<?=$art["author_link"];?>"><?=$art["author_display_name"];?> </a></b>am <?=date("d.m.Y",$art["post_date"]);?></div>
                                <a href="<?= $art["post_permalink"]; ?>"><h6 class="podcast_title"><?= $art["post_title"]; ?></h6></a>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>
        </div>
    </section>
</div>

<section class="podcast_list">
    <div class="container">
        <div class="row mb-8">
            <div class="col-md-8">
                <div class="podcast_info">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="about_image mb-4 text-center">
                                <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/profile_image.jpg" alt="">

                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <h6 class="mb-2">Founder & CEO von Warda Network</h6>
                                <h4>Eugen Prosquill</h4>
                                <p class="mb-4">
                                    Frisches Geld - der Name ist Programm. DJ Mosaken und Eugen Prosquill - zwei Normbrecher und Superbrains, Genießer und Allerweltmenschen. Auf dem Menü stehen wilde Anekdoten, deepe Gedankenexperimente oder brisante Gastbeiträge und, wie der Name schon leicht anmerkt, Profitips rund um Benjamins aka. Para alias Hak oder auch: Geld. Frisches, wohlduftendes Geld.
                                </p>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            <div class="col-md-4">
                <div class="podcast_subscribe_box">
                    <p>
                        Podcast Abo <br>
                        Abonniere das Frische Geld Podcast
                    </p>
                    <a href="https://open.spotify.com/show/38NCvbi1Mjn7UcxODUNm0z?" target="_blank" class="load_more_btn mb-3">spotify</a>
                    <a href="https://podcasts.apple.com/at/podcast/frisches-geld/id1500284629" target="_blank" class="load_more_btn">itunes</a>
                </div>
            </div>
        </div>

        <div id="podcast_list">
            <?php
            $feat_events = apply_filters('tdf_get_posts', "podcasts", 5, 0, array("search" => array()));
            foreach($feat_events["items"] as $item){
                $exclude_ids[] = $item["post_id"];
                ?>
                <?php include(locate_template("/views/posts/podcasts/content-item.php")); ?>
            <?php } ?>
        </div>
        <input id="page_no" type="hidden" value="1" />
        <div class="m-auto pb-6">
            <a href="#" id="load_more_button" class="load_more_btn">Mehr Laden</a>
        </div>
    </div>


</section>

<script>
    jQuery(document).ready(function(){
        function load_more_podcasts(){
            var new_page_no = parseInt(jQuery("#page_no").val())+1;
            jQuery("#page_no").val(new_page_no);
            jQuery(".podcast_box").addClass("loaded");

            jQuery.ajax({
                url: "<?= admin_url("admin-ajax.php"); ?>?action=warda_load_podcasts&page="+new_page_no+"&per_page=5",
                method: "GET",
            }).done(function(data) {
                jQuery("#podcast_list").append(data);
                jQuery(".podcast_box").each(function(){
                    if(!jQuery(this).is(".loaded")){
                        jQuery(this).hide().slideDown();
                    }
                })
                jQuery("#load_more_button").removeClass("disabled");
                jQuery([document.documentElement, document.body]).animate({
                    scrollTop: jQuery(".podcast_box:last").offset().top
                }, 500);
                if(jQuery(".podcast_box").length%5!=0){
                    jQuery("#load_more_button").slideUp();
                }
            });
        }

        jQuery("#load_more_button").unbind("click").click(function(e){
            e.preventDefault();
            if(!jQuery("#load_more_button").is(".disabled")){
                jQuery("#load_more_button").addClass("disabled");
                load_more_podcasts();
            }
            return false;
        })
    })
</script>
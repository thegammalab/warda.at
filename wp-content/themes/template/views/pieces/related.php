<?php
$results = apply_filters('tdf_get_posts', "services", 12, 0, array("order" => "rand"));

?>
<section class="mortgage_section">
    <div class="container">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php for ($i = 0; $i < count($results["items"]); $i += 4) { ?>
                <div class="carousel-item <?php if ($i == 0) {
                                                    echo 'active';
                                                } ?>">
                    <div class="row">
                        <?php for ($j = 0; $j < 4; $j++) {
                                if ($results["items"][$i + $j]) { ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="mortgage_home_box">
                                <div class="review_title">
                                    <?= $results["items"][$i + $j]["post_title"]; ?>
                                </div>
                                <div class="mb-1">
                                    <a href="<?= $results["items"][$i + $j]["post_permalink"]; ?>"><?= $results["items"][$i + $j]["featured_img_medium"]; ?></a>
                                </div>
                                <div class="mb-2">
                                    <?= $results["items"][$i + $j]["post_excerpt"]; ?>
                                </div>
                                <a href="<?= $results["items"][$i + $j]["post_permalink"]; ?>">Read full review</a>
                            </div>
                        </div>

                        <?php }
                            } ?>
                    </div>
                </div>
                <?php } ?>

            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</section>
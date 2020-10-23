<?php
$url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
?>
<div class="top-image" style="background-image: url(<?php echo $url; ?>);">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 pull-left" >
                <div class="page-title section-title">
                    <h1>Oops! </h1>
                </div>
            </div> 
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-xs-12" style="margin-bottom: 30px;">
            <div class="alert alert-warning">
                I’m not sure what happened, but this page is missing. 
            </div>


        </div>
    </div>
</div>







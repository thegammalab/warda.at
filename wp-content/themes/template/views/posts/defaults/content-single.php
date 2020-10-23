<?php
the_post();
?>
<div id="content_section">
    <div class="container">
        <h1><?php the_title(); ?></h1>
    </div>
</div>
<div class="gray_section">
    <div class="container">
        <?php the_content(); ?>
    </div>
</div>
<div class="white_section">
    <div class="container">
        <div id="comment_div">
            <?php comments_template('comments.php'); ?>
        </div>
    </div>
</div>

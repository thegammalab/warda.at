<?php if (!have_posts()) : ?>
    <div class="alert alert-warning">
        <?php _e('Sorry, no results were found.', 'tdf'); ?>
    </div>
    <?php get_search_form(); ?>
<?php endif; ?>

<div id="content_section">
    <div class="container">
        <h1>Our Blog</h1>
        <?php echo do_shortcode('[smartblock id=102]'); ?>
    </div>
</div>
<div id="blog_section">
    <div class="container">
        <ul id="blog_list">
            <?php while (have_posts()) : the_post(); ?>
                <?php
                get_template_part('templates/content-post');
                ?>
            <?php endwhile; ?>
        </ul>
        <hr />
        <div class="pagination_links">
            <<?php echo paginate_links($args); ?>
        </div>
    </div>
</div>

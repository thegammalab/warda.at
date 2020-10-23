<li>
    <div class="blog_img">
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
    </div>
    <div class="blog_title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </div>
    <div class="blog_meta">
        <ul class="blog_meta_items">
            <li class="blog_meta_date"><?php the_date(); ?></li>
            <li class="blog_meta_comments">No Comments</li>
        </ul>
    </div>
</li>

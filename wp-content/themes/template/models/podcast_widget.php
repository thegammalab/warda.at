<?php
// Creating the widget 
class Warda_Podcast extends WP_Widget {
    
    function __construct() {
        parent::__construct('warda_podcast',  __('Warda Podcast', 'warda_podcast_domain'), array( 'description' => __( 'Warda Podcast Widget', 'warda_podcast_domain' ), ) );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $feat_events = apply_filters('tdf_get_posts', "podcasts", 1, 0, array("search" => array("exclude"=>$exclude_ids,"meta__is_ns_featured_post"=>"yes")));
        foreach($feat_events["items"] as $item){
            ?>
                <div class="card_box header_format">
                    <div class="article_image">
                        <a href="<?= $item["post_permalink"]; ?>"><?= get_the_post_thumbnail($item["post_id"], "full"); ?></a>
                    </div>
                </div>
                <div class="latest_podcast p-0">
                    <div class="pt-2 px-3">
                        <div class="author">von <b><a href="<?=$item["author_link"];?>"><?=$item["author_display_name"];?> </a></b>am <?=date("d.m.Y",$item["post_date"]);?></div>
                        <a href="<?= $item["post_permalink"]; ?>"><h3><?= $item["post_title"]; ?></h3></a>
                    </div>
                    <div class="px-1">
                        <?php echo do_shortcode($item["meta_podcast_shortcode"]); ?>
                    </div>
                </div>
        <?php } 
        echo $args['after_widget'];
    }
            
    // Widget Backend 
    public function form( $instance ) {
       
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {

        return $instance;
    }
 
} 


<?php
// Creating the widget 
class Warda_tiktok extends WP_Widget {
    
    function __construct() {
        parent::__construct('warda_tiktok',  __('Warda TikTok', 'warda_tiktok_domain'), array( 'description' => __( 'Warda TikTok Widget', 'warda_tiktok_domain' ), ) );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        ?>
        <div id="tiktok_widget">
            <img src="<?php echo get_bloginfo("template_directory"); ?>/assets/images/tiktok_logo.png" alt="">
            <h3>Warda</h3>
            <h5>@wardakommewieder</h5>
            <ul>
                <li><span><?=get_option("options_tt_following_count");?></span>Folge ich</li>
                <li><span><?=get_option("options_tt_followers_count");?></span>Follower</li>
                <li><span><?=get_option("options_tt_likes_count");?></span>Likes</li>
                <li><span><?=get_option("options_tt_videos_count");?></span>Videos</li>
            </ul>
            <a href="<?=get_option("options_tt_link");?>" target="_blank"><button class="btn btn-primary">Folge uns auf TikTok</button></a>
        </div>
        <?php 
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


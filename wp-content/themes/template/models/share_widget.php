<?php
// Creating the widget 
class Warda_share extends WP_Widget {
    
    function __construct() {
        parent::__construct('warda_share',  __('Warda Share', 'warda_share_domain'), array( 'description' => __( 'Warda Share Widget', 'warda_share_domain' ), ) );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        ?>
        <div class="row quarter_space">
            <div class="col-4">
                <a href="<?=get_option("options_fb_link");?>" target="_blank" class="share_box facebook_box">
                    <div class="share_top">
                        <div class="share_icon"><i class="fab fa-facebook-f"></i></div>
                        <div class="share_text">Like uns auf Facebook</div>
                        <div class="share_plus"><i class="fas fa-plus"></i></div>
                    </div>
                    <div class="share_count"><?=get_option("options_fb_count");?> likes</div>
                </a>
            </div>
            <div class="col-4">
                <a href="<?=get_option("options_ig_link");?>" target="_blank" class="share_box instagram_box">
                    <div class="share_top">
                        <div class="share_icon"><i class="fab fa-instagram"></i></div>
                        <div class="share_text">Folge uns auf Instagram</div>
                        <div class="share_plus"><i class="fas fa-plus"></i></div>
                    </div>
                    <div class="share_count"><?=get_option("options_ig_count");?> follower</div>
                </a>
            </div>
            <div class="col-4">
                <a href="<?=get_option("options_yt_link");?>" target="_blank" class="share_box youtube_box">
                    <div class="share_top">
                        <div class="share_icon"><i class="fab fa-youtube"></i></div>
                        <div class="share_text">Abonniere uns auf Youtube</div>
                        <div class="share_plus"><i class="fas fa-plus"></i></div>
                    </div>
                    <div class="share_count"><?=get_option("options_yt_count");?> abbonenten</div>
                </a>
            </div>
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

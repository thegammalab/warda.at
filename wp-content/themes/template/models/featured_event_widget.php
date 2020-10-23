<?php
// Creating the widget 
class Warda_featevent extends WP_Widget {
    
    function __construct() {
        parent::__construct('warda_featevent',  __('Warda Feat. Event', 'warda_featevent_domain'), array( 'description' => __( 'Warda  Feat. Event Widget', 'warda_featevent_domain' ), ) );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $feat_events = apply_filters('tdf_get_posts', "sweepstakes", 1, 0, array("search" => array("exclude"=>$exclude_ids)));
        foreach($feat_events["items"] as $item){
            echo '<div class="">';
            include(locate_template("/views/posts/sweepstakes/content-item.php")); 
            echo '</div>';
        }
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


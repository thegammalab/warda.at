<?php
// Creating the widget 
class Warda_relatedart extends WP_Widget {
    
    function __construct() {
        parent::__construct('warda_relatedart',  __('Warda Related Articles', 'warda_relatedart_domain'), array( 'description' => __( 'Warda Related Articles Widget', 'warda_relatedart_domain' ), ) );
    }
    
    // Creating widget front-end
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        global $post;
        $pid = ($post->ID);
        $cat = wp_get_post_terms($pid,"category");
        $terms = array();
        foreach($cat as $c){
            $terms[]=$c->term_id;
        }
        $feat_events = apply_filters('tdf_get_posts', "post", 2, 0, array("search" => array("exclude"=>array($pid),"tax_category"=>$terms)));
        foreach($feat_events["items"] as $item){
            include(locate_template("/views/posts/post/content-item.php")); 
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


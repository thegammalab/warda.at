<?php
function wpb_load_widget() {
    register_widget( 'warda_podcast' );
    register_widget( 'warda_share' );
    register_widget( 'warda_tiktok' );
    register_widget( 'warda_featevent' );
    register_widget( 'warda_relatedart' );

}
add_action( 'widgets_init', 'wpb_load_widget' );

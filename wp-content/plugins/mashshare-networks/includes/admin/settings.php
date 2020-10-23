<?php

/**
 * Registers the options in mashnet Extensions tab
 * *
 * @access      private
 * @since       1.0
 * @param 	$settings array the existing plugin settings
 * @return      array
*/

function mashnet_extension_settings( $settings ) {

	$ext_settings = array(
		array(
			'id' => 'mashnet_header',
			'name' => '<strong>' . __( 'Social Networks Settings', 'mashnet' ) . '</strong>',
			'desc' => '',
			'type' => 'header',
			'size' => 'regular'
		),
		array(
			'id' => 'mashnet_subjecttext',
			'name' => __( 'Mail Subject', 'mashnet' ),
			'desc' => __( '', 'mashnet' ),
			'type' => 'text',
                        'size' => 'large',
                        'std' => 'Check out this site'
		),
                array(
			'id' => 'mashnet_bodytext',
			'name' => __( 'Mail Body', 'mashpv' ),
			'desc' => __( '', 'mashnet' ),
			'type' => 'text',
                        'size' => 'large',
                        'std' => 'Check out this article: '
		),
                array(
                        'id' => 'mashnet_pinterest_selection',
                        'name' => __('Pinterest Gallery', 'mashnet'),
                        'desc' => 'Pinterest Share Button opens a Image Selection Gallery which allows you to select a specific image. Default opens the pinterest or featured image',
                        'type' => 'checkbox'
                ),
	);

	return array_merge( $settings, $ext_settings );

}
add_filter('mashsb_settings_extension', 'mashnet_extension_settings');


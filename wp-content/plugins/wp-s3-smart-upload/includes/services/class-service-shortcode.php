<?php
/**
 * Created by PhpStorm.
 * User: gaupoit
 * Date: 8/28/19
 * Time: 11:24
 */

/**
 *
 * Class PPW_Shortcode
 */
class SSU_Shortcode {
	/**
	 * Instance of SSU_Shortcode class.
	 *
	 * @var SSU_Shortcode
	 */
	protected static $instance = null;

	/**
	 * Short code attributes.
	 *
	 * @var array
	 */
	private $attributes;

	/**
	 * @var SSU_S3_Service
	 */
	private $aws_service;


	/**
	 * Register the short code ppwp_content_protector with WordPress
	 * and include the asserts for it.
	 */
	public function __construct() {
//		add_shortcode( 'ssu_s3', array( $this, 'render_shortcode_ssu_s3' ) );
	}

	/**
	 * Get short code instance
	 *
	 * @return SSU_Shortcode
	 */
	public static function get_instance() {
		new SSU_Shortcode();
	}

	/**
	 * Render signed url for shortcode.
	 *
	 * @param array  $attrs   list of attributes including password.
	 * @param string $content the content inside short code.
	 *
	 * @return string
	 */
	public function render_shortcode_ssu_s3( $attrs, $content = null ) {
		$url  = '';
		$time = '';
		extract(
			shortcode_atts(
				array(
					'url'  => '',
					'time' => '+2 minutes',
				),
				$attrs
			)
		);
		$option            = ssu_get_s3_options();
		$this->aws_service = new SSU_S3_Service( $option );

		if ( ! $url ) {
			return $content;
		}

		$signed_url = $this->aws_service->create_read_presigned_url( $url, $time );

		return ! empty( $signed_url ) ? $signed_url : $content;
	}

}

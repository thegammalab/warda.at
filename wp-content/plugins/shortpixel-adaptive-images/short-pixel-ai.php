<?php
	/*
	 * Plugin Name: ShortPixel Adaptive Images
	 * Plugin URI: https://shortpixel.com/
	 * Description: Display properly sized, smart cropped and optimized images on your website. Images are processed on the fly and served from our CDN.
	 * Version: 2.0.9
	 * Author: ShortPixel
	 * GitHub Plugin URI: https://github.com/short-pixel-optimizer/shortpixel-adaptive-images
	 * Author URI: https://shortpixel.com
	 * Text Domain: shortpixel-adaptive-images
	 */

	!defined( 'ABSPATH' ) and exit;

	if ( !class_exists( 'ShortPixelAI' ) ) {
		define( 'SHORTPIXEL_AI_VERSION', '2.0.9' );
		define( 'SHORTPIXEL_AI_PLUGIN_FILE', __FILE__ );
		define( 'SHORTPIXEL_AI_PLUGIN_DIR', __DIR__ );
		define( 'SHORTPIXEL_AI_WP_PLUGINS_DIR', dirname( __DIR__ ) );

		// Controllers
		require_once __DIR__ . '/includes/controllers/short-pixel-ai.class.php';
		require_once __DIR__ . '/includes/controllers/logger.class.php';
		require_once __DIR__ . '/includes/controllers/css-parser.class.php';
		require_once __DIR__ . '/includes/controllers/regex-parser.class.php';
		require_once __DIR__ . '/includes/controllers/json-parser.class.php';
		require_once __DIR__ . '/includes/controllers/js-parser.class.php';
		require_once __DIR__ . '/includes/controllers/simple-dom-parser.class.php';
		require_once __DIR__ . '/includes/controllers/feedback.class.php';
		require_once __DIR__ . '/includes/controllers/options.class.php';
		require_once __DIR__ . '/includes/controllers/notice.class.php';
		require_once __DIR__ . '/includes/controllers/page.class.php';
		require_once __DIR__ . '/includes/controllers/help.class.php';

		// Actions
		require_once __DIR__ . '/includes/actions/feedback.actions.class.php';
		require_once __DIR__ . '/includes/actions/page.actions.class.php';
		require_once __DIR__ . '/includes/actions/notice.actions.class.php';
		require_once __DIR__ . '/includes/actions/help.actions.class.php';

		// Constants
		require_once __DIR__ . '/includes/constants/page.constants.class.php';
		require_once __DIR__ . '/includes/constants/notice.constants.class.php';

		// Models
		require_once __DIR__ . '/includes/models/options.option.class.php';
		require_once __DIR__ . '/includes/models/options.category.class.php';
		require_once __DIR__ . '/includes/models/options.collection.class.php';

		// Helpers
		require_once __DIR__ . '/includes/helpers/converter.php';
		require_once __DIR__ . '/includes/helpers/url-tools.class.php';

		// Http
		require_once __DIR__ . '/includes/http/request.php';

		if ( isset( $_GET[ 'SHORTPIXEL_AI_DEBUG' ] ) && $_GET[ 'SHORTPIXEL_AI_DEBUG' ] === 'delete' ) {
			ShortPixelAILogger::instance()->clearLog();
			unset( $_GET[ 'SHORTPIXEL_AI_DEBUG' ] );
		}

		if ( !defined( 'SHORTPIXEL_AI_DEBUG' ) ) {
			define( 'SHORTPIXEL_AI_DEBUG', isset( $_GET[ 'SHORTPIXEL_AI_DEBUG' ] ) ? $_GET[ 'SHORTPIXEL_AI_DEBUG' ] : false );
		}

		if ( is_numeric( SHORTPIXEL_AI_DEBUG ) && ( SHORTPIXEL_AI_DEBUG & 2 ) ) {
			ini_set( 'display_errors', 1 );
			ini_set( 'display_startup_errors', 1 );
			error_reporting( E_ALL );

			$old_error_handler = set_error_handler( [ 'ShortPixelAILogger', 'errorHandler' ] );
		}

		register_activation_hook( __FILE__, [ 'ShortPixelAI', 'activate' ] );
		register_deactivation_hook( __FILE__, [ 'ShortPixelAI', 'deactivate' ] );

		//init the singleton
		ShortPixelAI::_();
	}

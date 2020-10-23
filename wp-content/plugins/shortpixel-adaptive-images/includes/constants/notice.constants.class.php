<?php

	namespace ShortPixel\AI\Notice;

	class Constants {
		private static $instance;

		public $autoptimize;
		public $avadalazy;
		public $divitoolbox;
		public $elementorexternal;
		public $beta;
		public $on_boarding;
		public $lazy;
		public $wp_rocket_defer_js;
		public $wp_rocket_lazy;
		public $wprocketcss;
		public $key;
		public $credits;
		public $twicelossy;
		public $missing_jquery;
		public $swift_performance;
		public $imagify;
		public $spio_webp;
		public $litespeed_js_combine;
		public $wpo_merge_css;

		/**
		 * Single ton implementation
		 *
		 * @param \ShortPixelAI|null $controller
		 *
		 * @return \ShortPixel\AI\Notice\Constants
		 */
		public static function _( $controller = null ) {
			return self::$instance instanceof self ? self::$instance : new self( $controller );
		}

		/**
		 * Constants constructor.
		 *
		 * @param \ShortPixelAI|null $controller
		 */
		private function __construct( $controller ) {
			if ( !isset( self::$instance ) || !self::$instance instanceof self ) {
				self::$instance = $this;
			}

			$integrations = $controller->getActiveIntegrations();

			$this->autoptimize = [
				'title' => __( 'Autoptimize option conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'The option "<strong>Optimize images on the fly and serve them from a CDN.</strong>" is active in Autoptimize. Please <span>deactivate it</span> to let ShortPixel Adaptive Images serve the images properly optimized and scaled.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->avadalazy = [
				'title' => __( 'Avada option conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'The option "Enable Lazy Loading" is active in your Avada theme options, under the Performance section. Please <span>deactivate it</span> to let ShortPixel Adaptive Images serve the images properly optimized and scaled.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->ginger = [
				'title' => __( 'Ginger option conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'The option "<strong>Cookie Confirmation Type</strong>" is set to Opt-in in Ginger - EU Cookie Law and this conflicts with ShortPixel. Please <span>set it differently</span> to let ShortPixel Adaptive Images serve the images properly optimized and scaled.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->divitoolbox = [
				'title' => __( 'Divi Toolbox option conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'The option "Custom Post Meta" is active in your Divi Toolbox options, under the Blog section. Please either update the plugin to version > 1.4.2 or <span>deactivate the option</span> to let ShortPixel Adaptive Images serve the images.',
						'shortpixel-adaptive-images' ),
				],
			];

			/* Obsolete
			$this->elementorexternal = [
				'title' => __( 'Elementor option conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'The option "<strong>CSS Print Method</strong>" is set on External File in your Elementor options. Please either activate the "Replace in CSS files" in the Advanced tab of <span>ShortPixel Adaptive Images options</span>', 'shortpixel-adaptive-images' ) . ' 😰',
					__( 'or <span>change Elementor\'s option</span> to Internal Embedding in order to let ShortPixel Adaptive Images also optimize background images.', 'shortpixel-adaptive-images' ),
				],
			];
			*/

			$this->beta = [
				'title' => __( 'ShortPixel Adaptive Images is in BETA', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'Currently the plugin is in the Beta phase. While we have tested it a lot, we can\'t possibly test it with all the themes out there. On Javascript-intensive themes, layout issues could occur or some images might not be replaced.',
						'shortpixel-adaptive-images' ),
					__( 'If you notice any problems, just deactivate the plugin and the site will return to the previous state. Please kindly <span>let us know</span> and we\'ll be more than happy to work them out.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->on_boarding = [
				'title' => __( 'ShortPixel Adaptive Images new feature', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'Thank you for updating to our new 2.0 version!', 'shortpixel-adaptive-images' ),
					__( 'Please let us introduce our <span>On-Boarding Wizard</span> which has been developed to help you decide exactly which advanced options are really necessary for your website.', 'shortpixel-adaptive-images' ),
				],
			];

			$this->lazy = [
				'title' => __( 'ShortPixel Adaptive Images conflicts with other lazy-loading settings', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( '<strong>ShortPixel Adaptive Images</strong> has detected that your theme or another plugin is providing lazy-loading functionality to your website.',
						'shortpixel-adaptive-images' ),
					__( '<strong>ShortPixel Adaptive Images</strong> is also using a lazy-loading method as means to provide its service, so please deactivate the other lazy-loading setting.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->wp_rocket_defer_js = [
				'title' => __( 'ShortPixel Adaptive Images conflicts with defer of all JavaScript files', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( '<strong>ShortPixel Adaptive Images</strong> has found that conflicting option <span>Load JavaScript deferred</span> in the WP Rocket has been enabled without safe mode.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->wp_rocket_lazy = [
				'title' => __( 'ShortPixel Adaptive Images conflicts with other lazy-loading settings', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( '<strong>ShortPixel Adaptive Images</strong> is also using a lazy-loading method as means to provide its service, so please deactivate the other lazy-loading setting. <span>Open the WP Rocket Settings</span> to turn off the Lazy Load option.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->wprocketcss = [
				'title' => __( 'ShortPixel Adaptive Images conflicts with other CSS settings', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'You have enabled the "Replace in CSS files" option in ShortPixel. Please either <span>Open the WP Rocket Settings</span> to turn off the "Minify CSS files" option of WP Rocket or <span>update your WP Rocket plugin</span> to at least version 3.4.3.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->key = [
				'title' => __( 'ShortPixel account', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'You already have a ShortPixel account for this website. Do you want to use ShortPixel Adaptive Images with this account?', 'shortpixel-adaptive-images' ),
				],
			];

			$this->credits = [
				'title' => __( 'ShortPixel credits', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'Your ShortPixel Adaptive Images quota has been exceeded.', 'shortpixel-adaptive-images' ),
				],
			];

			$this->twicelossy = [
				'title' => __( 'ShortPixel optimization alert', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'ShortPixel Adaptive Images and ShortPixel Image Optimizer are both set to do Lossy optimization which could result in a too aggressive optimization of your images, please set one of them on Lossless.', 'shortpixel-adaptive-images' ),
				],
			];

			$this->missing_jquery = [
				'title' => __( 'ShortPixel Adaptive Images has found that jQuery is missing', 'shortpixel-adaptive-images' ),
				'body'  => [
					sprintf( __( 'Looks like your theme is missing the <a href="%s" target="_blank">jQuery</a> library %s and the plugin needs it in order to properly run. You can find out more details about the plugin requirements <a href="%s" target="_blank">here</a>.',
						'shortpixel-adaptive-images' ), 'https://jquery.com', '😰', 'https://help.shortpixel.com/article/220-i-installed-shortpixel-adaptive-images-but-it-doesnt-seem-to-work' ),
					__( 'Please press <span>Re-Check</span> button if <b>jQuery</b> has been restored in your theme.', 'shortpixel-adaptive-images' ),
				],
			];

			$this->swift_performance = [
				'title' => 'Swift Performance ' . ( empty( $integrations[ 'swift-performance' ][ 'plugin' ] ) ? '' : ucfirst( $integrations[ 'swift-performance' ][ 'plugin' ] ) . ' ' ) . __( 'options conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'There is a known compatibility issue between ShortPixel Adaptive Images and older Swift Performance plugin versions which makes some background images to never get displayed.', 'shortpixel-adaptive-images' ),
					__( 'Please update to the latest plugin version, or deactivate either "<b>Merge Styles</b>" or "<b>Normalize Static Resources</b>" options from the Swift Performance <a href="tools.php?page=swift-performance&subpage=settings" target="_blank">plugin settings</a>.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->imagify = [
				'title' => __( 'Imagify options conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					__( 'There is a known compatibility issue with <i>Imagify\'s WebP delivery</i> that will make images not display on the site.', 'shortpixel-adaptive-images' ),
					__( 'Please deactivate <b>"Display images in webp format on the site"</b> from the Imagify <a href="options-general.php?page=imagify" target="_blank">plugin settings</a>. <b>ShortPixel</b> will handle the delivery of WebP images to supporting browsers.',
						'shortpixel-adaptive-images' ),
				],
			];

			$this->spio_webp = [
				'title' => __( 'ShortPixel optimization alert', 'shortpixel-adaptive-images' ),
				'body'  => [
					sprintf( __( 'Please deactivate the <span>ShortPixel Image Optimizer\'s</span> <a href="%s" target="_blank">Deliver WebP using PICTURE tag</a> option when the ShortPixel Adaptive Images plugin is active.', 'shortpixel-adaptive-images' ),
						admin_url( 'options-general.php?page=wp-shortpixel-settings&part=adv-settings' ) ),
				],
			];

			$this->litespeed_js_combine = [
				'title' => __( 'LiteSpeed Cache options conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					sprintf( __( 'Please deactivate the <span>LiteSpeed Cache\'s</span> <a href="%s" target="_blank">JS Combine</a> option when the ShortPixel Adaptive Images plugin is active.', 'shortpixel-adaptive-images' ),
						admin_url( 'admin.php?page=litespeed-page_optm#settings_js' ) ),
				],
			];

			$plugin_folder = plugin_basename( SHORTPIXEL_AI_PLUGIN_DIR );

			$conflicting_files = [
				'/' . $plugin_folder . '/assets/css/admin.css',
				'/' . $plugin_folder . '/assets/css/admin.min.css',
				'/' . $plugin_folder . '/assets/css/style-bar.css',
				'/' . $plugin_folder . '/assets/css/style-bar.min.css',
			];

			$this->wpo_merge_css = [
				'title' => __( 'WP Optimize CSS options conflict', 'shortpixel-adaptive-images' ),
				'body'  => [
					sprintf( __( 'In some circumstances, the <span>WP Optimize\'s</span> <a href="%s" target="_blank">Enable merging of CSS files</a> option breaks the ShortPixel Adaptive Images plugin CSS. Please check your website and if you find CSS issues, please deactivate this option.',
						'shortpixel-adaptive-images' ),
						admin_url( 'admin.php?page=wpo_minify&tab=wp_optimize_css' ) ),
					sprintf( __( 'Also you could add the following ShortPixel Adaptive Images plugin CSS files present below to <a href="%s" target="_blank">Default exclusions</a> or <a href="%s" target="_blank">CSS exclusions</a>.', 'shortpixel-adaptive-images' ),
						admin_url( 'admin.php?page=wpo_minify&tab=wp_optimize_advanced' ),
						admin_url( 'admin.php?page=wpo_minify&tab=wp_optimize_css' ) ),
					'<pre>' . implode( PHP_EOL, $conflicting_files ) . '</pre>',
				],
			];
		}
	}
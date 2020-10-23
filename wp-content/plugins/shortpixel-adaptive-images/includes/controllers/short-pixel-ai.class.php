<?php
use ShortPixel\AI\Page;
use ShortPixel\AI\Help;
use ShortPixel\AI\Notice;
use ShortPixel\AI\Options;
use ShortPixel\AI\Request;
use ShortPixel\AI\Feedback;

class ShortPixelAI {
    const DEFAULT_API_AI = 'https://cdn.shortpixel.ai';
    const DEFAULT_API_AI_PATH = '/spai';
    const SP_API = 'https://api.shortpixel.com/';
    const SEP = '+'; //can be + or ,
    const LOG_NAME = 'shortpixel-ai.log';
	const ACCOUNT_CHECK_SCHEDULE = array( 'name' => 'spai_account_check_event', 'recurrence' => 'twicedaily', );
    public static $SHOW_STOPPERS = array('ao', 'avadalazy', 'ginger');
    public static $excludedAjaxActions = array(
        //Add Media popup     Image to editor              Woo product variations
        'query-attachments', 'send-attachment-to-editor', 'woocommerce_load_variations',
        //avia layout builder AJAX calls
        'avia_ajax_text_to_interface', 'avia_ajax_text_to_preview',
        //My Listing theme
        'mylisting_upload_file',
        //Oxygen stuff
        'ct_get_components_tree', 'ct_exec_code'
    );

	public $options;
	public $settings;
    public $cssCacheVer;

    public $lazyNoticeThrown = false;
    public $affectedTags = array();

    /**
     * @var $instance
     */
    private $file;
    private static $instance;
    private $doingAjax = false;

    private $conflict = false;
    private $spaiJSDequeued = false;

    private $integrations = false;
    private $logger = false;
    private $parser = false;
    private $cssParser = false;

    /**
     * @return ShortPixelRegexParser
     */
    public function getRegexParser() {
        return $this->parser;
    }

    /**
     * @return bool|ShortPixelCssParser
     */
    public function getCssParser()
    {
        return $this->cssParser;
    }

    public function doingAjax() {
        return $this->doingAjax;
    }

	public static function isAjax() {
		return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Method walks recursively thru the folders and returns the paths to the files using passed pattern
	 *
	 * @param string $directory Dir path
	 * @param string $pattern   Regex
	 *
	 * @return array
	 */
	public static function recursiveGlob( $directory, $pattern ) {
		$recursive_dir_iterator = new RecursiveDirectoryIterator( $directory );
		$recursive_iterator     = new RecursiveIteratorIterator( $recursive_dir_iterator );
		$file_iterator          = new RegexIterator( $recursive_iterator, $pattern, RegexIterator::GET_MATCH );

		$paths = [];
		foreach ( $file_iterator as $file_list ) {
			// $file_list == [ 0 => 'path to file' ... ] that's why array_merge
			$paths = array_merge( $paths, $file_list );
		}

		return $paths;
	}

	/**
	 * Method checks is user logged in and has capability
	 *
	 * @param string            $capability WP user capabilities
	 * @param \WP_User|int|null $user       if null would be used current user ID
	 *
	 * @return bool
	 */
	public static function userCan( $capability, $user = null ) {
		$user = $user instanceof WP_User ? $user : ( is_int( $user ) && $user > 0 ? get_user_by( 'id', $user ) : wp_get_current_user() );

		if ( !$user instanceof WP_User ) {
			return false;
		}

		return $user->exists() && user_can( $user, $capability );
	}

    /**
     * Make sure only one instance is running.
     */
	public static function _() {
		if ( !isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

    private function __construct() {
        load_plugin_textdomain('shortpixel-adaptive-images', false, plugin_basename(dirname(SHORTPIXEL_AI_PLUGIN_FILE)) . '/lang');

        $this->logger = ShortPixelAILogger::instance();
        $this->options = Options::_();

        //$parser = new ShortPixelRegexParser($this);
        //$parser = new ShortPixelDomParser($this);
        $this->cssParser = new ShortPixelCssParser($this);
        //$this->parser = new ShortPixelSimpleDomParser($this);
        $this->parser = new ShortPixelRegexParser($this);

        //The recorded affected tags are from pieces of content that are loaded after the page, for example AJAX content. The first time the image will be blank but at second load OK
        $this->affectedTags = $this->getRecordedAffectedTags();

	    Page::_( $this );
	    Notice::_( $this );
	    Notice\Constants::_( $this );
	    Feedback::_( $this );
	    Help::_();

        $this->doingAjax = ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		$this->setup_globals();
		$this->include_libs();
        $this->setup_hooks();
    }

    public function init_ob() {
        if ($this->isWelcome()) {
            SHORTPIXEL_AI_DEBUG && $this->logger->log('WILL PARSE ' . $_SERVER['REQUEST_URI'] . ' CALLED BY ' . @$_SERVER['HTTP_REFERER']);
            //remove srcset and sizes param
			add_filter( 'wp_calculate_image_srcset', array( $this, 'replace_image_srcset' ), 10, 5 );

            // action to change urls in the Elementor's autogenerated css
	        if ( !$this->settings->areas->parse_css_files && get_option( 'elementor_css_print_method', false ) === 'external' ) {
				add_action( 'elementor/element/parse_css', array( $this, 'parse_elementor_css' ), 10, 2 );
			}

	        $integrations = $this->getActiveIntegrations( true );

	        if ( isset( $integrations[ 'nextgen' ] ) && !!$integrations[ 'nextgen' ] ) {
		        add_filter( 'ngg_pro_lightbox_images_queue', [ $this, 'parseNextGenEntities' ] );
	        }

	        if ( $this->settings->areas->parse_css_files && $integrations[ 'wp-rocket' ][ 'minify-css' ] && $integrations[ 'wp-rocket' ][ 'css-filter' ] ) {
		        $this->logger->log('SETUP: WP ROCKET CSS FILTER');
		        // if WP Rocket is active and the css option is on and the version is >=3.4 we can use its cache to store the changed CSS
		        add_filter( 'rocket_css_content', [ $this, 'parse_cached_css' ], 10, 3 );
	        }

	        if ( $this->settings->areas->parse_css_files && $integrations[ 'wp-fastest-cache' ] ) {
		        $this->logger->log( 'SETUP: WP FASTEST CACHE CSS FILTER' );

		        add_filter( 'wpfc_css_content', [ $this, 'parse_cached_css' ], 10, 3 );
	        }

	        if ( $this->settings->areas->parse_css_files && $integrations[ 'w3-total-cache' ] ) {
		        $this->logger->log( 'SETUP: W3 TOTAL CACHE CSS FILTER' );

		        add_filter( 'w3tc_minify_css_content', [ $this, 'parse_cached_css' ], 10, 3 );
	        }

            if ( isset( $integrations[ 'slider-revolution' ] ) && !!$integrations[ 'slider-revolution' ] ) {
                $this->settings->exclusions->excluded_paths .= PHP_EOL . "path:/revslider/public/assets/assets/transparent.png";
            }

	        if (
		        $this->settings->areas->parse_css_files && !empty( $integrations[ 'swift-performance' ] ) && !empty( $integrations[ 'swift-performance' ][ 'merge_styles' ] )
		        && isset( $integrations[ 'swift-performance' ][ 'plugin' ] ) && $integrations[ 'swift-performance' ][ 'plugin' ] === 'pro'
	        ) {
		        add_filter( 'swift_performance_critical_css_content', function( $critical_css ) {
			        $this->logger->log( 'SETUP: SWIFT PERFORMANCE (CRITICAL) CSS FILTER' );

			        // try to replace the background images with our CDN
			        $critical_css = $this->parse_cached_css( $critical_css, null, null );

			        return $critical_css;
		        }, 10, 1 );

		        add_filter( 'swift_performance_css_content', function( $css_content, $key ) {
			        $this->logger->log( 'SETUP: SWIFT PERFORMANCE (REGULAR) CSS FILTER' );

			        // try to replace the background images with our CDN
			        $css_content = $this->parse_cached_css( $css_content, null, null );

			        return $css_content;
		        }, 10, 2 );
	        }

			// add a hook to the Rocket's init 'wp' filter
			add_filter( 'wp', array( $this, 'disableRocketLazy' ), 1 );

            ob_start(array($this, 'maybe_replace_images_src'));
        } elseif(defined('SHORTPIXEL_AI_CLEANUP')) {
            $this->logger->log("CLEANUP " . $_SERVER['REQUEST_URI']);
            ob_start(array($this, 'maybe_cleanup'));
        } else {
            $this->logger->log("WON'T PARSE " . $_SERVER['REQUEST_URI']);
        }
    }

	/**
	 * Method adds filter do_rocket_lazyload to disable the WP Rocket's lazy loading
	 * @since 1.8.1
	 */
	public function disableRocketLazy() {
		add_filter( 'do_rocket_lazyload', '__return_false', 1 );
	}

	/**
	 * Method parses NextGen Gallery Entities to replace image URLs with placeholders
	 *
	 * @param array $entities
	 *
	 * @return array
	 */
	public function parseNextGenEntities( $entities ) {
		$return        = [];
		$api_url       = $this->get_api_url( false ) . '/';
		$short_api_url = $this->get_api_url( false, false, 'svg' ) . '/';

		if ( !empty( $entities ) && is_array( $entities ) ) {
			foreach ( $entities as $entity ) {
                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'image' ]);
                $entity[ 'image' ]                      = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'image' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'full_image' ]);
                $entity[ 'full_image' ]                 = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'full_image' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'thumb' ]);
                $entity[ 'thumb' ]                      = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'thumb' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'srcsets' ][ 'hdpi' ]);
                $entity[ 'srcsets' ][ 'hdpi' ]          = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'srcsets' ][ 'hdpi' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'srcsets' ][ 'original' ]);
                $entity[ 'srcsets' ][ 'original' ]      = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'srcsets' ][ 'original' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'full_srcsets' ][ 'hdpi' ]);
                $entity[ 'full_srcsets' ][ 'hdpi' ]     = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'full_srcsets' ][ 'hdpi' ]);

                $sizes = ShortPixelUrlTools::get_image_size($entity[ 'full_srcsets' ][ 'original' ]);
                $entity[ 'full_srcsets' ][ 'original' ] = ShortPixelUrlTools::generate_placeholder_svg(isset($sizes[0]) ? $sizes[0]: false, isset($sizes[1]) ? $sizes[1]: false,
                                                          $entity[ 'full_srcsets' ][ 'original' ]);

				$return[] = $entity;
			}
		}

		$this->logger->log( 'NEXTGEN ENTITIES: ' . var_export( $return, true ) );

		return $return;
	}

	/**
	 * Method regenerates Elementor's CSS files for posts
	 */
	public function regenerateElementorsCSS() {
		if ( class_exists( 'Elementor\Core\Files\Manager' ) ) {
			$elementor_files_manager = new Elementor\Core\Files\Manager();

			if ( method_exists( $elementor_files_manager, 'clear_cache' ) ) {
				$elementor_files_manager->clear_cache();
			}
		}
	}

	/**
	 * Method integrates SPAI with Elementor's CSS Print method
	 *
	 * @param \Elementor\Core\DynamicTags\Dynamic_CSS $post_css
	 * @param \Elementor\Element_Base                 $element
	 */
	public function parse_elementor_css( $post_css, $element ) {
		try {
			$reflection = new \ReflectionClass( $element );
			$class_name = $reflection->getName();

		}
		catch ( \ReflectionException $exception ) {
			$class_name = get_class( $element );
		}

		/**
		 * Temporary fix until Elementor Pro have fix
		 */
		$contains_bug = [
			'Elementor\Widget_Image',
			'Elementor\Widget_Heading',
			'ElementorPro\Modules\GlobalWidget\Widgets\Global_Widget',
			'Elementor\Widget_Spacer',
			'Elementor\Element_Column',
			'Elementor\Element_Section',
		];

		if ( in_array( $class_name, $contains_bug ) ) {
			return;
		}

		$element_raw      = $element->get_raw_data();
		$element_selector = $element->get_unique_selector();

		$post_stylesheet = $post_css->get_stylesheet();

		$api_url     = $this->get_api_url( false );
		$api_url_svg = $this->get_api_url( false, false, 'svg' );

		if ( !empty( $element_raw[ 'settings' ][ '_background_image' ] ) || !empty( $element_raw[ 'settings' ][ 'background_image' ] ) ) {
			// getting current rules
			$current_rules = $post_stylesheet->get_rules( null, $element->get_unique_selector() );

			// if rules weren't found it means nothing to change there
			if ( !empty( $current_rules ) ) {
				// passing through devices (Elementor supports responsive options)
				foreach ( $current_rules as $device => $rule ) {
					// exploding hash to prepare right query for Elementor
					$exploded_device = explode( '_', $device );

					// if device 'all' - null, otherwise generate the query
					// 0 - max or min (end point), 1 - targeted device width or string with name of device
					$query = $device === 'all' ? null : [ $exploded_device[ 0 ] => $exploded_device[ 1 ] ];

					// rule contains selector and styles
					foreach ( $rule as $selector => $styles ) {
						// does the general selector has a sought element's selector? & do styles have a background-image?
						if ( strpos( $selector, $element_selector ) && array_key_exists( 'background-image', $styles ) ) {
							// taking targeted device or width for $element
							$background_target = $device === 'all' ? '' : '_' . $exploded_device[ 1 ];

							// determine should be the underscore before targeted background image
							$underscore = empty( $element_raw[ 'settings' ][ '_background_image' . $background_target ] ) ? '' : '_';

							if ( isset( $element_raw[ 'settings' ][ $underscore . 'background_image' . $background_target ][ 'url' ] ) ) {
								$background_url = $element_raw[ 'settings' ][ $underscore . 'background_image' . $background_target ][ 'url' ];

								// preparing the url to right (full) format
								$background_url = ShortPixelUrlTools::absoluteUrl( $background_url );

								// does passed url contain ".svg" at the end of the string? if so it's a SVG
								$is_svg = ShortPixelUrlTools::is( $background_url, 'svg' );

								// if current image is SVG and the "Serve SVGs through CDN" is disabled we'll let it lie as is
								if ( $is_svg && !$this->settings->areas->serve_svg ) {
									continue;
								}

								// set the right API URL depending on the image's extension
								$current_api_url = ( $is_svg ? $api_url_svg : $api_url );

								// if so replacing the url with API url
								$styles[ 'background-image' ] = 'url("' . $current_api_url . '/' . $background_url . '")';

								// adding the rules to the post's stylesheet
								$post_stylesheet->add_rules( $selector, $styles, $query );
							}
						}
					}
				}
			}
		}
	}

	private function include_libs() {
		// libs to be included
	}

	private function setup_globals() {
		$this->file        = SHORTPIXEL_AI_PLUGIN_FILE;
		$this->basename    = plugin_basename( $this->file );
		$this->plugin_dir  = plugin_dir_path( $this->file );
		$this->plugin_url  = plugin_dir_url( $this->file );
		$gravatar          = 'regex:/\/\/([^\/]*\.|)gravatar.com\//';

		if ( is_null( $this->options->get( 'api_url', [ 'settings', 'behaviour' ], null ) ) ) {
			$this->options->settings_behaviour_apiUrl        = ShortPixelAI::DEFAULT_API_AI . self::DEFAULT_API_AI_PATH;
			$this->options->settings_behaviour_replaceMethod = 'src';
			$this->options->settings_behaviour_fadein        = true;
			// moved to self::migrate_options()
			// $this->options->settings_compression_level     = 'lossy';
			$this->options->settings_compression_webp         = true;
			$this->options->settings_compression_pngToWebp    = true;
			$this->options->settings_compression_jpgToWebp    = true;
			$this->options->settings_compression_gifToWebp    = true;
			$this->options->settings_areas_serveSvg           = true;
			$this->options->settings_exclusions_excludedPaths = $gravatar;
		}

	    if ( is_null( $this->options->get( 'backgrounds_lazy', [ 'settings', 'areas' ], null ) ) ) {
		    $this->options->settings_areas_backgroundsLazy  = false;
		    $this->options->settings_compression_removeExif = true;
	    }

        if ( is_null( $this->options->get( 'backgrounds_lazy_style', [ 'settings', 'areas' ], null ) ) ) {
            //copy the tag option (formerly ambiguously described as for STYLE blocks)
            $this->options->settings_areas_backgroundsLazyStyle  = $this->options->settings_areas_backgroundsLazy;
        }

	    if ( is_null( $this->options->get( 'excluded_paths', [ 'settings', 'exclusions' ], null ) ) ) {
		    $this->options->settings_exclusions_excludedPaths = $gravatar;
	    }

	    if ( is_null( $this->options->get( 'eager_selectors', [ 'settings', 'exclusions' ], null ) ) && !empty( $this->options->get( 'noresize_selectors', [ 'settings', 'exclusions' ], null ) ) ) {
		    // for backwards compatibility, the eager should take the values from noresize because noresize was also eager.
		    $this->options->settings_exclusions_eagerSelectors = $this->options->settings_exclusions_noresizeSelectors;
	    }

	    if ( !is_bool( $this->options->get( 'enqueued', [ 'tests', 'front_end' ], null ) ) ) {
		    $this->options->tests_frontEnd_enqueued = true;
	    }

	    if ( $this->options->get( 'parse_css_files', [ 'settings', 'areas' ], false ) ) {
		    $this->cssCacheVer = $this->options->get( 'css_ver', [ 'flags', 'all' ], 0 );
	    }

	    $this->settings = $this->options->settings;

//        $this->settings = array(
//            'api_url' => get_option('spai_settings_api_url'),
//            'compress_level' => get_option('spai_settings_compress_level'),
//            'remove_exif' => get_option('spai_settings_remove_exif', 1),
//            'excluded_selectors' => get_option('spai_settings_excluded_selectors', ''),
//            'eager_selectors' => get_option('spai_settings_eager_selectors', ''),
//            'noresize_selectors' => get_option('spai_settings_noresize_selectors', ''),
//            'excluded_paths' => get_option('spai_settings_excluded_paths'),
//            'type' => get_option('spai_settings_type', 1),
//            'crop' => get_option('spai_settings_crop', 0),
//            'fadein' => get_option('spai_settings_fadein', 1),
//            'webp' => get_option('spai_settings_webp', 1),
//            'native_lazy' => get_option('spai_settings_native_lazy'),
//			'hover_handling' => !!get_option( 'spai_settings_hover_handling' ),
//            'backgrounds_lazy' => get_option('spai_settings_backgrounds_lazy'),
//            'backgrounds_max_width' => get_option('spai_settings_backgrounds_max_width'),
//            'parse_js' => get_option('spai_settings_parse_js'),
//            'parse_js_lazy' => get_option('spai_settings_parse_js_lazy'),
//            'parse_json' => get_option('spai_settings_parse_json'),
//            'parse_json_lazy' => get_option('spai_settings_parse_json_lazy'),
//            'parse_css_files' => get_option('spai_settings_parse_css_files'),
//            'parse_css_files_changing_ward' => get_option('spai_settings_parse_css_files_changing_ward'),
//            'css_domains' => get_option('spai_settings_css_domains'),
//            'missing_jquery' => get_option('spai_settings_missing_jquery'),
//            'front_tests' => get_option('spai_settings_front_tests'),
//            //TODO REMOVE OBSOLETE 'ext_meta' => get_option('spai_settings_ext_meta')
//        );

//        if(SHORTPIXEL_AI_DEBUG) {
//            foreach($this->settings as $key => $value) {
//                if(isset($_GET[$key])) {
//                    $this->settings[$key] = $_GET[$key];
//                }
//            }
//        }
    }

    private function setup_hooks() {
	    // has event not already been scheduled?
		if ( !wp_next_scheduled( self::ACCOUNT_CHECK_SCHEDULE[ 'name' ] ) ) {
			wp_schedule_event( time(), self::ACCOUNT_CHECK_SCHEDULE[ 'recurrence' ], self::ACCOUNT_CHECK_SCHEDULE[ 'name' ] );
		}

		// account check event's handler
		add_action( self::ACCOUNT_CHECK_SCHEDULE[ 'name' ], array( $this, 'account_check_handler' ) );

	    /**
		 * When current theme has been changed/deactivated this hook fired
	     * @since WP 1.5.2
	     */
	    if ( has_action( 'switch_theme' ) ) {
		    add_action( 'switch_theme', [ $this, 'enqueue_front_tests' ] );
	    }

	    /**
		 * When new theme has been activated this hook fired
	     * @since WP 3.3
	     */
		if ( has_action( 'after_switch_theme' ) ) {
			add_action( 'after_switch_theme', [ $this, 'enqueue_front_tests' ] );
		}
		/**
		 * When theme has been initialized this hook fired
		 * @since WP 3.0
		 */
		else if ( has_action( 'after_setup_theme' ) ) {
			add_action( 'after_setup_theme', [ $this, 'enqueue_front_tests' ] );
		}
		else {
			$this->enqueue_front_tests();
		}

	    add_action( 'admin_bar_menu', [ $this, 'toolbar_styles' ], 999 );

	    /**
	     * Filter deactivates WordPress's images lazy-loading
		 * @since WP 5.5
	     */
	    add_filter( 'wp_lazy_loading_enabled', '__return_false', 1 );

        //if(!(is_admin() && !wp_doing_ajax() /* && function_exists("is_user_logged_in") && is_user_logged_in() */)) {
        if (!is_admin() || $this->doingAjax) {
            //FRONT-END
            if (!in_array($this->is_conflict(), self::$SHOW_STOPPERS)) {
                //setup to replace URLs only if not admin.
	            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_script' ] );
	            add_action( 'init', [ $this, 'init_ob' ], 1 );
	            // USING ob_ instead of the filters below.
                //add_filter( 'the_content', array( $this, 'maybe_replace_images_src',));
                //add_filter( 'post_thumbnail_html', array( $this, 'maybe_replace_images_src',));
                //add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'maybe_replace_images_src',));

                //Disable the Cloudflare Rocket Loader for ai.min.js
                add_filter( 'script_loader_tag', array(&$this, 'disable_rocket_loader'), 10, 3 );
            }

	        // Deactivating when jQuery is missing
	        add_action( 'wp_ajax_nopriv_shortpixel_deactivate_ai', [ $this, 'deactivate_ai_handler' ] );
	        add_action( 'wp_ajax_nopriv_shortpixel_activate_ai', [ $this, 'activate_ai_handler' ] );

	        //EXCEPT our AJAX actions which are front but also from admin :)
            if (is_admin()) {
            	// TODO: should be removed because it's been deprecated since 2.0
                // add_action('wp_ajax_shortpixel_ai_dismiss_notice', array(&$this, 'dismiss_admin_notice'));
                //add_action('wp_ajax_shortpixel_ai_clear_css_cache', array(&$this, 'clear_css_cache'));
	            add_action( 'wp_ajax_shortpixel_ai_add_selector_to_list', [ $this, 'add_selector_to_list' ] );
	            add_action( 'wp_ajax_shortpixel_ai_remove_selector_from_list', [ $this, 'remove_selector_from_list' ] );
	            add_action( 'wp_ajax_shortpixel_deactivate_ai', [ $this, 'deactivate_ai_handler' ] );
	            add_action( 'wp_ajax_shortpixel_activate_ai', [ $this, 'activate_ai_handler' ] );
            }
        } else {
            //BACK-END
	        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
	        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_script' ] );

	        add_action( 'in_plugin_update_message-' . $this->basename, [ $this, 'in_plugin_update_message' ], 10, 2 );

	        add_filter( 'plugin_action_links_' . $this->basename, [ $this, 'generate_plugin_links' ] ); //for plugin settings page
        }
    }

	public function in_plugin_update_message( $data, $response ) {
		$version_parts = explode( '.', $response->new_version );

		$notice = '';

		if ( version_compare( PHP_VERSION, $response->requires_php, '<' ) ) {
			$notice .= '<span>' . sprintf( __( '<strong>Heads up! We do not recommend to update!</strong> ShortPixel Adaptive Images version <strong>%s</strong> is not compatible with your PHP version.', 'shortpixel-adaptive-images' ), $response->new_version ) . '</span>';
			$notice .= '<span>' . sprintf( __( 'The new ShortPixel Adaptive Images version requires at least PHP <strong>%s</strong> and your PHP version is <strong>%s</strong>', 'shortpixel-adaptive-images' ), $response->requires_php, PHP_VERSION ) . '</span>';

			echo wp_kses_post( $notice );

			return;
		}

		// Major
		if ( $version_parts[ 1 ] == '0' && ( isset( $version_parts[ 2 ] ) ? $version_parts[ 2 ] == '0' : true ) && version_compare( $version_parts[ 0 ] . '.' . $version_parts[ 1 ], SHORTPIXEL_AI_VERSION, '>' ) ) {
			$notice .= '<span>' . sprintf( __( '<strong>Heads up!</strong> %s is a %s update.', 'shortpixel-adaptive-images' ), $response->new_version, __( 'major', 'shortpixel-adaptive-images' ) ) . '</span>';
		}
		// Minor update message
		else if ( $version_parts[ 1 ] != '0' && ( isset( $version_parts[ 2 ] ) ? $version_parts[ 2 ] == '0' : true ) && version_compare( $version_parts[ 0 ] . '.' . $version_parts[ 1 ], SHORTPIXEL_AI_VERSION, '>' ) ) {
			$notice .= '<span>' . sprintf( __( '<strong>Heads up!</strong> %s is a %s update.', 'shortpixel-adaptive-images' ), $response->new_version, __( 'minor', 'shortpixel-adaptive-images' ) ) . '</span>';
		}

		$notice .= $this->get_update_notice( $response );

		echo wp_kses_post( $notice );
	}

	/**
	 * Get the upgrade notice from WordPress.org.
	 *
	 * @param object $response WordPress response
	 *
	 * @return string
	 */
	private function get_update_notice( $response ) {
		$transient_name = 'spai_update_notice_' . $response->new_version;
		$update_notice  = get_transient( $transient_name );

		if ( $update_notice === false ) {
			$readme_response = Request::get( 'https://plugins.svn.wordpress.org/shortpixel-adaptive-images/trunk/readme.txt' );

			if ( !empty( $readme_response ) ) {
				$update_notice = $this->parse_update_notice( $readme_response, $response );
				set_transient( $transient_name, $update_notice, DAY_IN_SECONDS );
			}
		}

		return $update_notice;
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param string $content  ShortPixel AI readme file content
	 * @param object $response WordPress response
	 *
	 * @return string
	 */
	private function parse_update_notice( $content, $response ) {
		$version_parts     = explode( '.', $response->new_version );
		$check_for_notices = [
			$version_parts[ 0 ] . '.' . $version_parts[ 1 ] . '.' . $version_parts[ 2 ] . '.' . $version_parts[ 3 ], // build
			$version_parts[ 0 ] . '.' . $version_parts[ 1 ] . '.' . $version_parts[ 2 ], // patch (micro)
			$version_parts[ 0 ] . '.' . $version_parts[ 1 ] . '.0', // minor
			$version_parts[ 0 ] . '.' . $version_parts[ 1 ], // minor
			$version_parts[ 0 ] . '.0.0', // major
			$version_parts[ 0 ] . '.0', // major
		];

		$update_notice = '';

		foreach ( $check_for_notices as $id => $check_version ) {
			if ( version_compare( SHORTPIXEL_AI_VERSION, $check_version, '>' ) ) {
				continue;
			}

			$result = $this->parse_readme_content( $content, $check_version, $response );

			if ( !empty( $result ) ) {
				$update_notice .= $result;
				break;
			}
		}

		return wp_kses_post( $update_notice );
	}

	/**
	 * Parses readme file's content to find notice related to passed version
	 *
	 * @param string $content Readme file content
	 * @param string $version Checked version
	 * @param object $response WordPress response
	 *
	 * @return string
	 */
	private function parse_readme_content( $content, $version, $response ) {
		$notice_regexp = '/==\s*Upgrade Notice\s*==.*=\s*(' . preg_quote( $version ) . ')\s*=(.*)(=\s*' . preg_quote( $version . ':END' ) . '\s*=|$)/Uis';

		$notice = '';
		$matches = null;
		if ( preg_match( $notice_regexp, $content, $matches ) ) {
			$notices = (array) preg_split( '/[\r\n]+/', trim( $matches[ 2 ] ) );

			if ( version_compare( trim( $matches[ 1 ] ), $version, '=' ) ) {
				foreach ( $notices as $index => $line ) {
					$notice .= '<span>';
					$notice .= $this->replace_readme_constants( $this->markdown2html( $line ), $response );
					$notice .= '</span>';
				}
			}
		}

		return $notice;
	}

	private function replace_readme_constants( $content, $response ) {
		$constants    = [ '{{ NEW VERSION }}', '{{ CURRENT VERSION }}', '{{ PHP VERSION }}', '{{ REQUIRED PHP VERSION }}' ];
		$replacements = [ $response->new_version, SHORTPIXEL_AI_VERSION, PHP_VERSION, $response->requires_php ];

		return str_replace( $constants, $replacements, $content );
	}

	public function markdown2html( $content ) {
		$patterns = [
			'/\*\*(.+)\*\*/U', // bold
			'/__(.+)__/U', // italic
			'/\[([^\]]*)\]\(([^\)]*)\)/U', // link
		];

		$replacements = [
			'<strong>${1}</strong>',
			'<em>${1}</em>',
			'<a href="${2}" target="_blank">${1}</a>',
		];

		$prepared_content = preg_replace( $patterns, $replacements, $content );

		return isset( $prepared_content ) ? $prepared_content : $content;
	}

	public function account_check_handler() {
		$domain_response   = $this->get_domain_status( true );
		$domain_status     = (int) $domain_response->Status === 2 || (int) $domain_response->Status === 0;
		$dismissed_notices = Notice::getDismissed();

		if ( $domain_status && isset( $dismissed_notices->credits ) ) {
			Notice::deleteDismissing( 'credits' );
		}
	}

	public function enqueue_front_tests() {
		return !!$this->options->set( true, 'enqueued', [ 'tests', 'front_end' ] );
	}

	public function deactivate_ai_handler() {
		$success = !!$this->options->set( false, 'enqueued', [ 'tests', 'front_end' ] ) && !!$this->options->set( true, 'missing_jquery', [ 'tests', 'front_end' ] );

		if ( self::isAjax() ) {
			wp_send_json( [
				'success' => $success,
				'front'   => [
					'reload' => true,
				],
			] );
		}

		return $success;
	}

	public function activate_ai_handler() {
		$success = !!$this->options->set( false, 'enqueued', [ 'tests', 'front_end' ] ) && !!$this->options->set( false, 'missing_jquery', [ 'tests', 'front_end' ] );

		if ( self::isAjax() ) {
			wp_send_json( [ 'success' => $success ], 200 );
		}

		return $success;
	}

	/**
	 * Method returns queried WP dependencies
	 *
	 * @param string $type
	 *
	 * @return \_WP_Dependency[]
	 */
	public function get_queried_dependencies( $type = 'scripts' ) {
		switch ( $type ) {
			case 'styles' :
				global $wp_styles;
				$dependencies = $wp_styles;
				break;

			case 'scripts' :
				global $wp_scripts;
				$dependencies = $wp_scripts;
				break;

			default :
				global $wp_scripts;
				$dependencies = $wp_scripts;
		}

		$return = [];

		foreach ( $dependencies->queue as $handle ) {
			$return[] = $dependencies->registered[ $handle ];
		}

		return $return;
	}

	/**
	 * Method returns user's logged in token
	 *
	 * @return string
	 */
	public function get_user_token() {
		if ( function_exists( 'wp_get_session_token' ) ) {
			return wp_get_session_token();
		}

		$cookie = wp_parse_auth_cookie( '', 'logged_in' );

		return !empty( $cookie[ 'token' ] ) ? $cookie[ 'token' ] : '';
	}

    public function toolbar_sniper_bar($wp_admin_bar) {
		//                                                TODO: missing_jquery check should be removed after sniper will migrate to VanillaJS
	    if ( !self::userCan( 'manage_options' ) || !!$this->options->tests_frontEnd_missingJquery || !!$this->options->tests_frontEnd_enqueued ) return;

        $args = array(
            'id'    => 'shortpixel_ai_sniper',
            'title' => '<div id="shortpixel_ai_sniper" title="' . __('Click here and then use the mouse to select an image to check, clear the CDN cache for it, or exclude','shortpixel-adaptive-images') . '" class="shortpixel_ai_sniper spai-smp-trigger ab-icon">
                       <div id="spai-smps">
                            <div id="spai-smp-multiple" class="spai-smp">
                                <p id="spai-smp-multiple-title">' . __('Please choose an image from the following list.','shortpixel-adaptive-images') . '</p>
                                <div id="spai-smp-multiple-list"></div>
                            </div>
                            <div id="spai-smp-single-template" class="spai-smp">
                                <div class="spai-smp-single-item-container">
                                    <div class="spai-smp-single-item-container-image-container">
                                        <img src="#" class="spai-smp-single-item-container-image" alt="">
                                    </div>
                                    <span class="spai-smp-single-item-container-basename"></span>
                                </div>
                                <div class="spai-smp-single-menu">
                                    <p class="spai-smp-single-title"></p>
                                    <div class="spai-smp-single-options"><p class="spai-smp-single-details"></p></div>
                                    <div class="spai-smp-buttons"></div>
                                </div>
                            </div>
                            <div id="spai-smp-message" class="spai-smp">
                                <p class="spai-smp-single-title">' . __( 'Couldn\'t find an image there...', 'shortpixel-adaptive-images' ) . '</p>
                                <p class="spai-smp-message-body">
                                </p>
                                <div class="spai-smp-buttons" class="spai-smp">
                                    <button class="spai-smp-options-button-retry">' . __( 'Retry', 'shortpixel-adaptive-images' ) . '</button>
                                    <button class="spai-smp-options-button-cancel">' . __( 'Close', 'shortpixel-adaptive-images' ) . '</button>
                                </div>
                            </div>
                       </div>
					   <div id="spai-snip-loader" class="spai-snip-loader">
					        <script>window.addEventListener("DOMContentLoaded", function(event) {spai_settings.eager_selectors.push("img.spai-snip-loader-img");})</script>
							<img src="' . plugins_url( 'assets/img/Spinner-1s-200px.gif', SHORTPIXEL_AI_PLUGIN_FILE ) . '" alt="" class="spai-snip-loader-img" />
							<p class="spai-snip-loader-text">' . __('Requesting...','shortpixel-adaptive-images') . '</p>
                       </div>
					   <div id="spai-snip-response" class="spai-snip-loader">
							<p class="spai-snip-loader-text"><span></span></p>
							<button id="spai-snip-refresh-page" onclick="window.location.reload(true)">' . __( 'Refresh', 'shortpixel-adaptive-images' ) . '</button>
                       </div>
                       '
                .'</div>',
            'href'  => '#',
            'meta'  => array('class' => 'shortpixel-ai-sniper')
        );
        $wp_admin_bar->add_node( $args );
    }

	public function toolbar_sniper_scripts() {
		if ( !self::userCan( 'manage_options' ) ) return;

		$min = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );

		$styles                             = [];
		$styles[ 'style-bar' ][ 'file' ]    = 'assets/css/style-bar' . $min . '.css';
		$styles[ 'style-bar' ][ 'url' ]     = $this->plugin_url . $styles[ 'style-bar' ][ 'file' ];
		$styles[ 'style-bar' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . $styles[ 'style-bar' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

		wp_enqueue_style( 'spai-bar-style', $styles[ 'style-bar' ][ 'url' ], [], $styles[ 'style-bar' ][ 'version' ] );

		wp_register_script( 'spai-sniper', self::DEFAULT_API_AI . '/assets/js/snip' . $min . '.js', [ 'jquery' ], SHORTPIXEL_AI_VERSION, true );
		wp_localize_script( 'spai-sniper', 'sniperLocalization', [
			'sizes'    => (object) [
				'gb'    => __( 'GB', 'shortpixel-adaptive-images' ),
				'mb'    => __( 'MB', 'shortpixel-adaptive-images' ),
				'kb'    => __( 'KB', 'shortpixel-adaptive-images' ),
				'byte'  => __( 'byte', 'shortpixel-adaptive-images' ),
				'bytes' => __( 'bytes', 'shortpixel-adaptive-images' ),
			],
			'messages' => (object) [
				'static'  => (object) [
					'cdn'                  => __( 'CDN', 'shortpixel-adaptive-images' ),
					'origin'               => __( 'ORIGIN', 'shortpixel-adaptive-images' ),
					'yes'                  => __( 'Yes', 'shortpixel-adaptive-images' ),
					'back'                 => __( 'Back', 'shortpixel-adaptive-images' ),
					'show'                 => __( 'Show', 'shortpixel-adaptive-images' ),
					'retry'                => __( 'Retry', 'shortpixel-adaptive-images' ),
					'cancel'               => __( 'Cancel', 'shortpixel-adaptive-images' ),
					'path'                 => __( 'Path', 'shortpixel-adaptive-images' ),
					'selector'             => __( 'Selector', 'shortpixel-adaptive-images' ),
					'imageExcluded'        => __( 'Image is excluded.', 'shortpixel-adaptive-images' ),
					'removeExcludingRule'  => __( 'Remove the excluding rule', 'shortpixel-adaptive-images' ),
					'clickToInspect'       => sprintf( __( 'Please click on the image that you want to inspect. <a href="%s" target="_blank">More details</a>', 'shortpixel-adaptive-images' ), 'https://help.shortpixel.com/article/338-how-to-use-the-image-checker-tool' ),
					'whyImageNotIncluded'  => __( 'Why isn\'t this image included?', 'shortpixel-adaptive-images' ),
					'hasBeenSelected'      => __( 'has been selected', 'shortpixel-adaptive-images' ),
					'imageOptimized'       => __( 'Image optimized', 'shortpixel-adaptive-images' ),
					'excludeLikeThis'      => __( 'Exclude images like this one from optimization.', 'shortpixel-adaptive-images' ),
					'excludeUrl'           => __( 'Exclude this image URL.', 'shortpixel-adaptive-images' ),
					'removeNoResizeRule'   => __( 'Remove the no resize rule.', 'shortpixel-adaptive-images' ),
					'dontResizeLikeThis'   => __( 'Do not resize images like this one.', 'shortpixel-adaptive-images' ),
					'removeLazyRule'       => __( 'Remove the lazy-load rule.', 'shortpixel-adaptive-images' ),
					'dontLazyLikeThis'     => __( 'Do not lazy-load images like this one.', 'shortpixel-adaptive-images' ),
					'refreshOnCdn'         => __( 'Refresh on CDN.', 'shortpixel-adaptive-images' ),
					'wantToExcludeUrl'     => __( 'Are you sure you want to exclude this image URL from optimization?', 'shortpixel-adaptive-images' ),
					'createNeededSelector' => sprintf( __( 'Use the controls below to create the CSS selector needed. Try to keep it as simple as possible. <a href="%s" target="_blank">How do I use this?</a>', 'shortpixel-adaptive-images' ),
						'https://help.shortpixel.com/article/338-how-to-use-the-image-checker-tool' ),
					'errorOccurred'        => __( 'An error occurred, please contact support.', 'shortpixel-adaptive-images' ),
					'resizing'             => __( 'resizing', 'shortpixel-adaptive-images' ),
					'optimizing'           => __( 'optimizing', 'shortpixel-adaptive-images' ),
					'lazyLoading'          => __( 'lazy-loading', 'shortpixel-adaptive-images' ),
					'dontResize'           => __( 'Don\'t resize', 'shortpixel-adaptive-images' ),
					'excluded'             => __( 'Excluded', 'shortpixel-adaptive-images' ),
					'dontLazyLoad'         => __( 'Don\'t lazy-load', 'shortpixel-adaptive-images' ),
					'oneImage'             => __( 'One image', 'shortpixel-adaptive-images' ),
					'invalidParameters'    => __( 'Invalid parameters have been passed to the function. Please try again.', 'shortpixel-adaptive-images' ),
					'refreshing'    => __( 'Refreshing...', 'shortpixel-adaptive-images' ),
					'checking'    => __( 'Checking...', 'shortpixel-adaptive-images' ),
					'cdnCacheCleared'    => __( 'Image CDN cache was cleared and image was refreshed. If you do not see the expected change, please clear the browser cache and then refresh the page.', 'shortpixel-adaptive-images' ),
				],
				'dynamic' => (object) [
					'sizeReducedFromTo'         => __( 'Size reduced from %s to %s', 'shortpixel-adaptive-images' ),
					'scaledFrom'                => __( 'and scaled from %spx to %spx.', 'shortpixel-adaptive-images' ),
					'reallyWantToStop'          => __( 'Really want to stop %s these images?', 'shortpixel-adaptive-images' ),
					'confirmClickedForSelector' => __( 'Confirm has been clicked for selector %s. Data action was %s', 'shortpixel-adaptive-images' ),
					'scrollToSeeAllImages' => __( 'Scroll to see all <b>%s</b> images', 'shortpixel-adaptive-images' ),
					'ruleWillBeAddedToList' => sprintf( __( 'matched by this selector on this page. The rule will be added to the <b>%s selectors</b> list in <a href="%s" target="_blank">ShortPixel AI Settings</a> and applied to <b>all the pages of your website</b>.', 'shortpixel-adaptive-images' ), '%s', admin_url( 'options-general.php?page=shortpixel-ai-settings#top#exclusions' ) ),
					'alreadyHaveSelectors' => __( 'You already have %s selectors active. Please keep the number of exclusion selectors low for site performance.', 'shortpixel-adaptive-images' ),
				],
			],
		] );

		wp_enqueue_script( 'spai-sniper', self::DEFAULT_API_AI . '/assets/js/snip' . $min . '.js', [ 'jquery' ], SHORTPIXEL_AI_VERSION, true );
	}

	public function generate_plugin_links($links)
    {
	    $in = '<a href="options-general.php?page=' . Page::NAMES[ 'settings' ] . '">' . __( 'Settings' ) . '</a>';
        array_unshift($links, $in);
        return $links;
    }

    function disable_rocket_loader( $tag, $handle, $src ) {
        if ( 'spai-scripts' === $handle ) {
            //$tag = str_replace( 'src=', 'data-cfasync="false" src=', $tag );
            $tag = str_replace( '<script', '<script data-cfasync="false"', $tag );
        }
        return $tag;
    }

    function parse_cached_css($content, $source, $target) {
        $this->cssParser->cssFilePath = trailingslashit(dirname($target));
        $ret = $this->cssParser->replace_inline_style_backgrounds($content);
        $this->cssParser->cssFilePath = false;
        SHORTPIXEL_AI_DEBUG && $this->logger->log("PARSE WP-ROCKET || W3TC || Swift || WPFC CSS return " . strlen($ret)
            . ((SHORTPIXEL_AI_DEBUG & 4) ? "\n\nSOURCE: $source\n\nTARGET: $target\n\nCONTENT: $content\n\nCONTENT PARSED: $ret" : ''));
        return $ret;
    }

	public function enqueue_script() {
		$scripts = [];
		$min     = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );

		if ( !!$this->options->get( 'enqueued', [ 'tests', 'front_end' ] ) ) {
			$scripts[ 'tests' ][ 'file' ]    = 'assets/js/ai.tests' . $min . '.js';
			$scripts[ 'tests' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . $scripts[ 'tests' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			wp_register_script( 'spai-tests', $this->plugin_url . $scripts[ 'tests' ][ 'file' ], [], $scripts[ 'tests' ][ 'version' ], true );
			wp_enqueue_script( 'spai-tests' );
		}

		if ( !$this->isWelcome() ) {
			return;
		}

		if ( $this->integrations === false ) {
			$this->integrations = $this->getActiveIntegrations();
		}

		if ( $this->settings->behaviour->fadein ) {
		    wp_register_style( 'spai-fadein', false );
		    wp_enqueue_style( 'spai-fadein' );
		    //Exclude the .zoomImg's as it conflicts with rules of WooCommerce.
		    wp_add_inline_style( 'spai-fadein',
                'img[data-spai]{'
                    . 'opacity: 0;'
                . '} '
                . 'div.woocommerce-product-gallery img[data-spai]{' //exclusions
                    . 'opacity: 1;'
                . '} '
                    . 'img[data-spai-eager],'
                    . 'img[data-spai-upd] {'
                    . 'transition: opacity .5s linear .2s;'
                    . '-webkit-transition: opacity .5s linear .2s;'
                    . '-moz-transition: opacity .5s linear .2s;'
                    . '-o-transition: opacity .5s linear .2s;'
                    . ' opacity: 1;'
                . '}');
        }

		$front_worker = Options::_()->get( 'front_worker', [ 'pages', 'on_boarding' ], Options\Option::_() );
		$front_worker = $front_worker instanceof Options\Option ? $front_worker : Options\Option::_();

		$current_user_login = wp_get_current_user()->ID;

		if ( !empty( $current_user_login ) ) {
		    $this->toolbar_sniper_scripts();

			$front_worker->{$current_user_login} = isset( $front_worker->{$current_user_login} ) && $front_worker->{$current_user_login} instanceof Options\Option ? $front_worker->{$current_user_login} : Options\Option::_();

			if (
				self::userCan( 'manage_options' )
				&& !!$front_worker->{$current_user_login}->enabled && $front_worker->{$current_user_login}->token === $this->get_user_token()
				&& isset( $_COOKIE[ 'shortpixel-ai-front-worker' ] ) && !!$_COOKIE[ 'shortpixel-ai-front-worker' ]
			) {
				$scripts[ 'js_cookie' ][ 'file' ]    = 'assets/js/libs/js.cookie' . $min . '.js';
				$scripts[ 'js_cookie' ][ 'version' ] = '3.0.0-rc.0';

				$scripts[ 'front_worker' ][ 'file' ]    = 'assets/js/front.worker' . $min . '.js';
				$scripts[ 'front_worker' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . $scripts[ 'front_worker' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

				add_filter( 'body_class', function( $classes ) {
					$classes[] = 'spai-fw-sidebar-hidden';

					return $classes;
				} );

				//TODO Yurii move this to a view
				add_action( 'wp_head', function() {
					?>
					<div class="spai-popup__wrap">
						<div class="spai-popup__inner">
							<div class="spai-popup__body">
								<div class="spai-popup__title"><?php _e( 'Hang on, <strong>ShortPixel Adaptive Images</strong> is checking your page…', 'shortpixel-adaptive-images' ); ?></div>
								<div class="roller-wrap">
									<div class="roller">
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="spai-fw-maximize-button clearfix">
						<svg class="icon">
							<use xlink:href="<?= $this->plugin_url; ?>assets/img/hide-show-sprite.svg#show"></use>
						</svg>
					</div>
					<aside class="spai-fw-sidebar">
						<div class="spai-fw-sidebar__header clearfix">
							<h6 class="title">ShortPixel AI</h6>
							<div class="buttons-wrap">
								<button class="dark_blue_link back"><?php _e( 'Done', 'shortpixel-adaptive-images' ); ?></button>
							</div>
						</div>
						<div class="spai-fw-sidebar__body">
							<div class="spai__recommended-options-wrap">
								<p class="has-recommendations hidden"><?= __( 'Based on this page’s contents, we recommend to activate the following options:', 'shortpixel-adaptive-images' ); ?></p>
								<p class="no-recommendations hidden"><?= __( 'The images on this page are properly optimized.', 'shortpixel-adaptive-images' ); ?></p>

								<div class="option clearfix hidden" data-option="lazy-load-backgrounds">
									<input id="lazy-load-backgrounds" type="checkbox" name="lazy-load-backgrounds" class="tgl" value="1" <?= checked( true, $this->settings->areas->backgrounds_lazy ); ?>>
									<label for="lazy-load-backgrounds" class="tgl-btn">
										<span></span><?= __( 'Lazy-load the backgrounds', 'shortpixel-adaptive-images' ); ?>
									</label>
								</div>
								<div class="option clearfix hidden" data-option="parse-css">
									<input id="parse-css" type="checkbox" name="parse-css" class="tgl" value="1" <?= checked( true, $this->settings->areas->parse_css_files ); ?>>
									<label for="parse-css" class="tgl-btn">
										<span></span><?= __( 'Replace in CSS files', 'shortpixel-adaptive-images' ); ?>
									</label>
								</div>
								<div class="option clearfix hidden" data-option="parse-js">
									<input id="parse-js" type="checkbox" name="parse-js" class="tgl" value="1" <?= checked( true, $this->settings->areas->parse_js ); ?>>
									<label for="parse-js" class="tgl-btn">
										<span></span><?= __( 'Replace in the JS blocks', 'shortpixel-adaptive-images' ); ?>
									</label>
								</div>
								<div class="option clearfix hidden" data-option="parse-json">
									<input id="parse-json" type="checkbox" name="parse-json" class="tgl" value="1" <?= checked( true, $this->settings->areas->parse_json ); ?>>
									<label for="parse-json" class="tgl-btn">
										<span></span><?= __( 'Replace in JSON data', 'shortpixel-adaptive-images' ); ?>
									</label>
								</div>
								<div class="option clearfix hidden" data-option="hover-handling">
									<input id="hover-handling" type="checkbox" name="hover-handling" class="tgl" value="1" <?= checked( true, $this->settings->behaviour->hover_handling ); ?>>
									<label for="hover-handling" class="tgl-btn">
										<span></span><?= __( 'Images hover handling', 'shortpixel-adaptive-images' ); ?>
									</label>
								</div>
								<div class="revert-confirmation hidden">
									<?= __( 'Based on our checks, your website needs this option.', 'shortpixel-adaptive-images' ); ?>
									<span><?= __( 'Are you sure you want to revert it?', 'shortpixel-adaptive-images' ); ?></span>
									<div class="buttons-wrap">
										<button class="blue_link cancel">Cancel</button>
										<button class="bordered_link revert">Revert</button>
									</div>
								</div>
								<p class="no-options hidden"><span><?= __( 'Good job!', 'shortpixel-adaptive-images' ); ?></span>
									<?= __( 'All recommended options for this page are successfully enabled. You can now navigate to another page of the site in order to run the check on that page too. Please press the <span>Done</span> button above when you\'re ready, to go back to the onboarding setup.',
										'shortpixel-adaptive-images' ); ?></p>
								<p class="cache-message hidden"><?= __( 'Please properly clear all levels of cache and open the website in a private window to check the results. <a href="https://www.howtogeek.com/269265/how-to-enable-private-browsing-on-any-web-browser/" target="_blank">How do I do this?</a>',
										'shortpixel-adaptive-images' ); ?></p>
							</div>
							<div class="buttons-wrap hidden" data-mission="general">
								<button class="dark_blue_link reload"><?php _e( 'OK, Reload the page', 'shortpixel-adaptive-images' ); ?></button>
							</div>
						</div>
						<div class="spai-fw-sidebar__footer">
							<div class="collapse-button clearfix">
								<svg class="icon icon--hide">
									<use xlink:href="<?= $this->plugin_url; ?>assets/img/hide-show-sprite.svg#hide"></use>
								</svg>
								<div class="label"><?= __( 'Hide Controls', 'shortpixel-adaptive-images' ); ?></div>
							</div>
						</div>
					</aside>
					<?php
				} );

				wp_deregister_script( 'js-cookie' );
				wp_register_script( 'js-cookie', $this->plugin_url . $scripts[ 'js_cookie' ][ 'file' ], [], $scripts[ 'js_cookie' ][ 'version' ], true );
				wp_register_script( 'spai-front-worker', $this->plugin_url . $scripts[ 'front_worker' ][ 'file' ], [ 'spai-scripts', 'js-cookie' ], $scripts[ 'front_worker' ][ 'version' ], true );

				wp_localize_script( 'spai-front-worker', 'SPAIFrontConstants', [
					'apiUrl'     => $this->settings->behaviour->api_url,
					'folderUrls' => [
						'plugins'  => str_replace( [ WP_CONTENT_URL, '/' ], '', WP_PLUGIN_URL ) . '/',
						'content'  => str_replace( [ site_url(), '/' ], '', WP_CONTENT_URL ) . '/',
						'includes' => WPINC . '/',
					],
				] );

				wp_enqueue_script( 'js-cookie' );
				wp_enqueue_script( 'spai-front-worker' );
			}
		}

		$scripts[ 'main' ][ 'file' ]    = 'assets/js/ai' . $min . '.js';
		$scripts[ 'main' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . $scripts[ 'main' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

		$noresize_selectors = $this->splitSelectors( $this->settings->exclusions->noresize_selectors, ',' );
		$eager_selectors = $this->splitSelectors( $this->settings->exclusions->eager_selectors, ',' );
		if($this->integrations['modula']) {
		    //This is for the creative gallery, because it sets the images positions outside of the view and they are never replaced
		    $noresize_selectors[] = '.modula-creative-gallery img.pic';
		    $eager_selectors[] = '.modula-creative-gallery img.pic';
        }

		wp_register_script( 'spai-scripts', $this->plugin_url . $scripts[ 'main' ][ 'file' ], [ 'jquery' ], $scripts[ 'main' ][ 'version' ], true );
		wp_localize_script( 'spai-scripts', 'spai_settings', [
			'api_url'               => $this->get_api_url(),
			'api_short_url'         => $this->get_api_url( false, false, 'svg' ),
			'method'                => $this->settings->behaviour->replace_method,
			'crop'                  => !!$this->settings->behaviour->crop,
			'native_lazy'           => !!$this->settings->areas->native_lazy,
			'serve_svg'             => !!$this->settings->areas->serve_svg,
			'hover_handling'        => !!$this->settings->behaviour->hover_handling,
			'debug'                 => SHORTPIXEL_AI_DEBUG,
			'site_url'              => home_url(),
			'plugin_url'            => plugins_url( '/shortpixel-adaptive-images' ),
			'version'               => SHORTPIXEL_AI_VERSION,
			'excluded_selectors'    => $this->splitSelectors( $this->settings->exclusions->excluded_selectors, ',' ),
			'eager_selectors'       => $eager_selectors,
			'noresize_selectors'    => $noresize_selectors,
			//the excluded_paths can contain URLs so we base64 encode them in order to pass our own JS parser :)
			'excluded_paths'        => array_map( 'base64_encode', $this->splitSelectors( $this->settings->exclusions->excluded_paths, "\n" ) ),
			'parse_css_files'       => !!$this->settings->areas->parse_css_files,
			'backgrounds_max_width' => (int) is_int( $this->settings->areas->backgrounds_max_width ) && $this->settings->areas->backgrounds_max_width >= 0 ? $this->settings->areas->backgrounds_max_width : 1920,
			'sep'                   => self::SEP, //separator
			'webp'                  => !!$this->settings->compression->webp,
			'extensions_to_webp'    => [
				'png' => !!$this->settings->compression->png_to_webp,
				'jpg' => !!$this->settings->compression->jpg_to_webp,
				'gif' => !!$this->settings->compression->gif_to_webp,
			],
			'sniper'                => $this->plugin_url . 'img/target.cur',
			'affected_tags'         => '{{SPAI-AFFECTED-TAGS}}',
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
            //LET THIS BE LAST - SWIFT Performance BUG.
            'active_integrations'   => $this->integrations,
		] );

		wp_enqueue_script( 'spai-scripts' );
		//wp_enqueue_script( 'spai-bg', self::DEFAULT_API_AI . '/assets/js/calculate-bg-size' . $min . '.js', [ 'jquery' ], SHORTPIXEL_AI_VERSION, true );
	}

	public function splitSelectors($selectors, $delimiter) {
	    if($delimiter !== "\n") {
	        $selectors = str_replace("\n", $delimiter, $selectors);
        }
        $selArray = strlen($selectors) ? explode($delimiter, $selectors) : array();
        return array_map('trim', $selArray);
    }

    public function toolbar_styles($wp_admin_bar) {
	    if ( !self::userCan( 'manage_options' ) ) {
		    return;
	    }

        if (!is_admin() && $this->isWelcome()) {
            $this->toolbar_sniper_bar($wp_admin_bar);
        }

        // Registering the styles
	    wp_register_style( 'spai-admin-styles', $this->plugin_url . 'assets/css/admin.css', [], hash_file( 'crc32', $this->plugin_dir . 'assets/css/admin.css' ) );

	    // Enqueueing the styles
	    wp_enqueue_style( 'spai-admin-styles' );
    }

	public function enqueue_admin_styles() {
		// Registering the styles
		$min = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );

		$styles = [];

		//TODO only load CSS when needed

		$styles[ 'admin' ][ 'file' ]    = 'assets/css/admin' . $min . '.css';
		$styles[ 'admin' ][ 'url' ]     = $this->plugin_url . $styles[ 'admin' ][ 'file' ];
		$styles[ 'admin' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . $styles[ 'admin' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

		$styles[ 'tippy' ][ 'file' ] = 'assets/css/libs/tippy.min.css';
		$styles[ 'tippy' ][ 'url' ]  = $this->plugin_url . $styles[ 'tippy' ][ 'file' ];

		$styles[ 'tippy-scale' ][ 'file' ] = 'assets/css/libs/scale.min.css';
		$styles[ 'tippy-scale' ][ 'url' ]  = $this->plugin_url . $styles[ 'tippy-scale' ][ 'file' ];

		$styles[ 'tippy-shift-away' ][ 'file' ] = 'assets/css/libs/shift-away.min.css';
		$styles[ 'tippy-shift-away' ][ 'url' ]  = $this->plugin_url . $styles[ 'tippy-shift-away' ][ 'file' ];

		$styles[ 'tippy-backdrop' ][ 'file' ] = 'assets/css/libs/backdrop.min.css';
		$styles[ 'tippy-backdrop' ][ 'url' ]  = $this->plugin_url . $styles[ 'tippy-backdrop' ][ 'file' ];

		$styles[ 'tippy-svg-arrow' ][ 'file' ] = 'assets/css/libs/svg-arrow.min.css';
		$styles[ 'tippy-svg-arrow' ][ 'url' ]  = $this->plugin_url . $styles[ 'tippy-svg-arrow' ][ 'file' ];

		wp_register_style( 'spai-admin-styles', $styles[ 'admin' ][ 'url' ], [], $styles[ 'admin' ][ 'version' ], 'all' );
		wp_register_style( 'tippy-css', $styles[ 'tippy' ][ 'url' ], [], null );
		wp_register_style( 'tippy-animations-scale', $styles[ 'tippy-scale' ][ 'url' ], [], null );
		wp_register_style( 'tippy-animations-shift-away', $styles[ 'tippy-shift-away' ][ 'url' ], [], null );
		wp_register_style( 'tippy-backdrop', $styles[ 'tippy-backdrop' ][ 'url' ], [], null );
		wp_register_style( 'tippy-svg-arrow', $styles[ 'tippy-svg-arrow' ][ 'url' ], [], null );

		// Enqueueing the styles
		wp_enqueue_style( 'spai-admin-styles' );
		wp_enqueue_style( 'tippy-css' );
		wp_enqueue_style( 'tippy-animations-scale' );
		wp_enqueue_style( 'tippy-animations-shift-away' );
		wp_enqueue_style( 'tippy-backdrop' );
		wp_enqueue_style( 'tippy-svg-arrow' );
	}

	public function enqueue_admin_script() {
		$min     = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );
        $screen = get_current_screen();
        //die(var_dump($screen->id));
        if($screen->id === 'settings_page_shortpixel-ai-settings') {
            wp_deregister_script( 'popper-js' );
            wp_register_script( 'popper-js', $this->plugin_url . 'assets/js/libs/popper' . $min . '.js', [], '2.4.4', true );
            wp_enqueue_script( 'popper-js' );

            wp_deregister_script( 'tippy-js' );
            wp_register_script( 'tippy-js', $this->plugin_url . 'assets/js/libs/tippy-bundle.umd' . $min . '.js', [], '6.2.6', true );
            wp_enqueue_script( 'tippy-js' );
        }
        elseif ($screen->id === 'admin_page_shortpixel-ai-on-boarding') {
            wp_deregister_script( 'js-cookie' );
            wp_register_script( 'js-cookie', $this->plugin_url . 'assets/js/libs/js.cookie' . $min . '.js', [], '3.0.0-rc.0', true );
            wp_enqueue_script( 'js-cookie' );
        }

		$adminJsVersion = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->plugin_dir . 'assets/js/admin' . $min . '.js' ) : SHORTPIXEL_AI_VERSION;
		wp_register_script( 'spai-admin-scripts', $this->plugin_url . 'assets/js/admin' . $min . '.js', [ 'jquery' ], $adminJsVersion, true );
		wp_enqueue_script( 'spai-admin-scripts' );
	}

	/**
	 * Method increases current css cache version to refresh files on the cdn
	 */
	public static function clear_css_cache() {
		return !!Options::_()->set( Options::_()->get( 'css_ver', [ 'flags', 'all' ], 0 ) + 1, 'css_ver', [ 'flags', 'all' ] );
	}

    public function add_selector_to_list() {
        $result = array('status' => 'error', 'message' => __( 'An error occurred, please contact support.', 'shortpixel-adaptive-images' ));
        $which = $_POST['which_list'];
        if(is_admin()) {
            if(empty($_POST['selector']) || !is_string($_POST['selector'])) {
                $result['message'] = __('Invalid selector has been provided.', 'shortpixel-adaptive-images' );
            }
            else if(empty($which) || !is_string($which) || !in_array($which, array('noresize_selectors', 'excluded_selectors', 'excluded_paths', 'eager_selectors'))) {
                $result['message'] = __('Invalid list has been provided.', 'shortpixel-adaptive-images' );
            }
            else {
                $selector =  preg_replace('/\s+/', ' ', trim($_POST['selector']));
                $wp_option_name = 'settings_exclusions_' . \ShortPixel\AI\Converter::snakeToCamelCase($which);
                $selectors_now = $this->options->$wp_option_name;
                $result['status'] = 'ok';
                if($which === 'excluded_paths') {
                    $name = 'URL';
                    $delimiter = "\n";
                    $selector = explode('/http', $selector);
                    if(count($selector) > 1) {
                        array_shift($selector);
                        $selector = 'http' . implode('/http', $selector);
                    } else {
                        $selector = explode('///', $selector);
                        if(count($selector) > 1) {
                            array_shift($selector);
                            $selector = 'http' . implode('///', $selector);
                        }
                    }
                }
                else {
                    $name = 'Selector';
                    $delimiter = ',';
                }
                $list = $this->splitSelectors($selectors_now, $delimiter);
                if(in_array($selector, $list)) {
                    $result['message'] = __( 'Selector is already present in the list. Please refresh.', 'shortpixel-adaptive-images' );
                }
                else {
                    $list[] = $selector;
                    $this->options->$wp_option_name = implode($delimiter, $list);
                    if($this->options->$wp_option_name) {
                        $listName = ($which == 'eager_selectors' ? '"Don\'t lazy load" selectors' : ucwords(str_replace('_', ' ', $which)));
                        $result['message'] = sprintf( __( 'The %s has been added to the %s list.', 'shortpixel-adaptive-images' ), $name, $listName );
                        $result['message'] = $this->clear_cache($result['message']);
                    }
                    else {
                        $result['status'] = 'error';
                        $result['message'] = __( 'An error occurred, please contact support.', 'shortpixel-adaptive-images' );
                    }
                }
                $result['list'] = $this->splitSelectors($this->options->$wp_option_name, $delimiter);
            }
        }
        else {
            $result['message'] = __( 'Please log in as admin.', 'shortpixel-adaptive-images' );
        }
        echo json_encode($result);
        wp_die();
    }

    public function remove_selector_from_list() {
        $result = array('status' => 'error', 'message' => __( 'An error occurred, please contact support.', 'shortpixel-adaptive-images' ));
        $which = $_POST['which_list'];
        if(is_admin()) {
            if(empty($_POST['selector']) || !is_string($_POST['selector'])) {
                $result['message'] = __('Invalid list has been provided.', 'shortpixel-adaptive-images' );
            }
            else if(empty($which) || !is_string($which) || !in_array($which, array('noresize_selectors', 'excluded_selectors', 'excluded_paths', 'eager_selectors'))) {
                $result['message'] = __('Invalid list has been provided.', 'shortpixel-adaptive-images' );
            }
            else {
                $selector = $_POST['selector'];
                $delimiter = $which == 'excluded_paths' ? "\n" : ',';
                $wp_option_name = 'settings_exclusions_' . \ShortPixel\AI\Converter::snakeToCamelCase($which);
                $selectors_now = $this->options->$wp_option_name;
                $list = $this->splitSelectors($selectors_now, $delimiter);
                $result['status'] = 'ok';
                if($which === 'excluded_paths' && in_array(str_replace('\\\\', '\\', $selector), $list)) {
                    $selector = str_replace('\\\\', '\\', $selector);
                }
                if(!in_array($selector, $list)) {
                    $result['message'] = __( 'The Selector does not exist in the list.', 'shortpixel-adaptive-images' );
                }
                else {
	                $list_new    = [];
	                $has_removed = false;

	                foreach ( $list as $list_element ) {
		                if ( $list_element !== $selector ) {
			                $list_new[] = $list_element;
		                }
		                else {
			                $has_removed = true;
		                }
	                }
	                $this->options->$wp_option_name = implode( $delimiter, $list_new );

	                if ( $has_removed ) {
		                $result[ 'message' ] = __( 'The Selector has been removed from the list.', 'shortpixel-adaptive-images' );
		                $result[ 'message' ] = $this->clear_cache( $result[ 'message' ] );
	                }
	                else {
		                $result[ 'status' ]  = 'error';
		                $result[ 'message' ] = __( 'An error occurred, please contact support.', 'shortpixel-adaptive-images' );
	                }
                }

                $result['list'] = $this->splitSelectors($this->options->$wp_option_name, $delimiter);
            }
        }
        else {
            $result['message'] = __( 'Please log in as admin.', 'shortpixel-adaptive-images' );
        }

        echo json_encode($result);
        wp_die();
    }

    function clear_cache($message) {
        $result = $message;
        $cache_cleared = false;
        wp_cache_flush();
        try {
            comet_cache::clear();
            $cache_cleared = true;
        } catch(Throwable $t) {} catch(Exception $e) {}
        if(function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
            $cache_cleared = true;
        }
        if(function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
            $cache_cleared = true;
        }
        if($cache_cleared) {
            $result .= ' ' . __( 'Please press OK to refresh the page.', 'shortpixel-adaptive-images' );
        }
        else {
            $result .= "\n" . __( 'Please clear all page cache levels from the WP admin and CDN if you have one, then press REFRESH to reload the page.', 'shortpixel-adaptive-images' );
        }
        return $result;
    }

	/**
	 * Method retrieves parent attachment using provided url it searches in the $prefix_postmeta table
	 *
	 * @param $image_url
	 *
	 * @return false|\WP_Post[]
	 */
	public static function get_parent_attachments( $image_url ) {
		$image_url = ShortPixelUrlTools::retrieve_name( $image_url );

		if ( empty( $image_url ) ) {
			return false;
		}

		global $wpdb;

		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM " . $wpdb->get_blog_prefix() . "postmeta WHERE meta_value LIKE %s;", '%' . $image_url . '%' ) );

		if ( !is_array( $post_ids ) || count( $post_ids ) === 0 ) {
			return false;
		}

		$posts = get_posts( [
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'include'     => $post_ids,
		] );

		if ( !is_array( $posts ) ) {
			return false;
		}

		return $posts;
	}

	/**
	 * Method returns the development stage of the plugin
	 *
	 * @return string
	 */
	public static function get_stage() {
		preg_match( '/(?:[0-9.,\-\t ]+)([a-zA-Z-_]*)(?<=[a-zA-Z])(?=\d*)/s', SHORTPIXEL_AI_VERSION, $matches );

		return !empty( $matches[ 1 ] ) ? strtolower( $matches[ 1 ] ) : 'release';
	}

	/**
	 * Method returns the result of testing is the plugin is in the beta stage
	 *
	 * @return bool
	 */
	public static function is_beta() {
		return stripos( SHORTPIXEL_AI_VERSION, 'beta' ) !== false;
	}

	/**
	 * Method migrates to the new Options implementation used in 2.x
	 */
	public static function migrate_options() {
		if ( get_option( 'spai_settings_compress_level', false ) !== false ) {
			$options = Options::_();

			// compression level of new "Options" needed to check has the 2.x.x been installed before
			$compression_level = $options->settings_compression_level;

			// if new compression method is empty means that it's a fresh installation of the 2.x.x version
			if ( empty( $compression_level ) ) {
				// Compression
				$compression_level = get_option( 'spai_settings_compress_level', false );
				$replace_method    = get_option( 'spai_settings_type' );

				$options->settings_compression_level      = $compression_level === false ? 'lossy' : ( $compression_level == 1 ? 'lossy' : ( $compression_level == 2 ? 'glossy' : 'lossless' ) );
				$options->settings_compression_webp       = !!get_option( 'spai_settings_webp' );
				$options->settings_compression_removeExif = !!get_option( 'spai_settings_remove_exif' );

				// Behaviour
				$options->settings_behaviour_fadein        = !!get_option( 'spai_settings_fadein' );
				$options->settings_behaviour_crop          = !!get_option( 'spai_settings_crop' );
				$options->settings_behaviour_replaceMethod = $replace_method == 1 ? 'src' : ( $replace_method == 3 ? 'both' : 'srcset' );
				$options->settings_behaviour_apiUrl        = get_option( 'spai_settings_api_url' );
				$options->settings_behaviour_hoverHandling = !!get_option( 'spai_settings_hover_handling' );

				// Areas
				$options->settings_areas_nativeLazy          = !!get_option( 'spai_settings_native_lazy' );
				$options->settings_areas_backgroundsLazy     = !!get_option( 'spai_settings_backgrounds_lazy' );
                $options->settings_areas_backgroundsLazyStyle= !!get_option( 'spai_settings_backgrounds_lazy' );
				$options->settings_areas_backgroundsMaxWidth = (int) get_option( 'spai_settings_backgrounds_max_width' );
				$options->settings_areas_parseCssFiles       = !!get_option( 'spai_settings_parse_css_files' );
				$options->settings_areas_cssDomains          = get_option( 'spai_settings_css_domains' );
				$options->settings_areas_parseJs             = !!get_option( 'spai_settings_parse_js' );
				$options->settings_areas_parseJsLazy         = !!get_option( 'spai_settings_parse_js_lazy' );
				$options->settings_areas_parseJson           = !!get_option( 'spai_settings_parse_json' );
				$options->settings_areas_parseJsonLazy       = !!get_option( 'spai_settings_parse_json_lazy' );

				// Exclusions
				$options->settings_exclusions_excludedPaths     = get_option( 'spai_settings_excluded_paths' );
				$options->settings_exclusions_excludedSelectors = get_option( 'spai_settings_excluded_selectors' );
				$options->settings_exclusions_noresizeSelectors = get_option( 'spai_settings_noresize_selectors' );
				$options->settings_exclusions_eagerSelectors    = get_option( 'spai_settings_eager_selectors' );

				// Flags
				$options->flags_all_account = get_option( 'spai_settings_account' );
				$options->flags_all_cssVer  = get_option( 'spai_settings_css_ver', 1 );

				// Notices
				$options->notices_dismissed = get_option( 'spai_settings_dismissed_notices', Options\Option::_() );
			}

			// not first install because of migrate
			$options->flags_all_firstInstall = false;

			// Deleting old options
			delete_option( 'spai_settings_compress_level' );
			delete_option( 'spai_settings_webp' );
			delete_option( 'spai_settings_remove_exif' );
			delete_option( 'spai_settings_fadein' );
			delete_option( 'spai_settings_crop' );
			delete_option( 'spai_settings_type' );
			delete_option( 'spai_settings_api_url' );
			delete_option( 'spai_settings_hover_handling' );
			delete_option( 'spai_settings_native_lazy' );
			delete_option( 'spai_settings_backgrounds_lazy' );
			delete_option( 'spai_settings_backgrounds_max_width' );
			delete_option( 'spai_settings_parse_css_files' );
			delete_option( 'spai_settings_css_domains' );
			delete_option( 'spai_settings_parse_js' );
			delete_option( 'spai_settings_parse_js_lazy' );
			delete_option( 'spai_settings_parse_json' );
			delete_option( 'spai_settings_parse_json_lazy' );
			delete_option( 'spai_settings_excluded_paths' );
			delete_option( 'spai_settings_excluded_selectors' );
			delete_option( 'spai_settings_noresize_selectors' );
			delete_option( 'spai_settings_eager_selectors' );
			delete_option( 'spai_settings_parse_css_files_changing_ward' );
			delete_option( 'spai_settings_missing_jquery' );
			delete_option( 'spai_settings_tab' );
			delete_option( 'spai_settings_account' );
			delete_option( 'spai_settings_css_ver' );
			delete_option( 'spai_settings_ext_meta' );
			delete_option( 'spai_settings_dismissed_notices' );
		}
		else {
			// Setting the flag that plugin has been installed for very first time
			if ( is_null( Options::_()->flags_all_firstInstall ) ) { // due to using magic get method we can't use isset() here because isset works only with varibales and properties
				Options::_()->flags_all_firstInstall = true;

				// Set the compression level to default
				Options::_()->settings_compression_level = 'lossy';
			}
		}
	}

	/**
	 * Method to be able to migrate back to the 1.x plugin version
	 */
	public static function revert_options() {
		$options = Options::_();

		$compression_level = $options->settings_compression_level;

		if ( isset( $compression_level ) ) {
			$replace_method = $options->settings_behaviour_replaceMethod;

			update_option( 'spai_settings_compress_level', $compression_level === 'lossy' ? '1' : ( $compression_level === 'glossy' ? '2' : '0' ) );
			update_option( 'spai_settings_webp', !!$options->settings_compression_webp );
			update_option( 'spai_settings_remove_exif', !!$options->settings_compression_removeExif );

			//Behaviour
			update_option( 'spai_settings_fadein', !!$options->settings_behaviour_fadein );
			update_option( 'spai_settings_crop', !!$options->settings_behaviour_crop );
			update_option( 'spai_settings_type', $replace_method === 'src' ? '1' : ( $replace_method === 'both' ? '3' : '0' ) );
			update_option( 'spai_settings_api_url', $options->settings_behaviour_apiUrl );
			update_option( 'spai_settings_hover_handling', !!$options->settings_behaviour_hoverHandling );
			//Areas
			update_option( 'spai_settings_native_lazy', !!$options->settings_areas_nativeLazy );
			update_option( 'spai_settings_backgrounds_lazy', !!$options->settings_areas_backgroundsLazy );
			update_option( 'spai_settings_backgrounds_max_width', $options->settings_areas_backgroundsMaxWidth );
			update_option( 'spai_settings_parse_css_files', !!$options->settings_areas_parseCssFiles );
			update_option( 'spai_settings_css_domains', $options->settings_areas_cssDomains );
			update_option( 'spai_settings_parse_js', !!$options->settings_areas_parseJs );
			update_option( 'spai_settings_parse_js_lazy', !!$options->settings_areas_parseJsLazy );
			update_option( 'spai_settings_parse_json', !!$options->settings_areas_parseJson );
			update_option( 'spai_settings_parse_json_lazy', !!$options->settings_areas_parseJsonLazy );
			//Exclusions
			update_option( 'spai_settings_excluded_paths', $options->settings_exclusions_excludedPaths );
			update_option( 'spai_settings_excluded_selectors', $options->settings_exclusions_excludedSelectors );
			update_option( 'spai_settings_noresize_selectors', $options->settings_exclusions_noresizeSelectors );
			update_option( 'spai_settings_eager_selectors', $options->settings_exclusions_eagerSelectors );

			update_option( 'spai_settings_account', $options->flags_all_account );
			update_option( 'spai_settings_css_ver', $options->get( 'css_ver', [ 'flags', 'all' ], 1 ) );

			$dismissed = $options->get( 'dismissed', 'notices' );
			$dismissed = $dismissed instanceof Options\Option ? (array) $dismissed : [];

			update_option( 'spai_settings_dismissed_notices', (array) $dismissed );

			// Deleting the options
			$options->delete( 'settings' );
		}
	}

	public static function activate() {
		// deleting already scheduled event
		wp_clear_scheduled_hook( self::ACCOUNT_CHECK_SCHEDULE[ 'name' ] );

		// adding event again
		wp_schedule_event( time(), self::ACCOUNT_CHECK_SCHEDULE[ 'recurrence' ], self::ACCOUNT_CHECK_SCHEDULE[ 'name' ] );

		self::migrate_options();

		// adding or updating option to run Front-end SPAI tests
		Options::_()->tests_frontEnd_enqueued = true;

		// getting the On-Boarding page options
		$on_boarding_options = Options::_()->get( 'redirect', [ 'pages', 'on_boarding' ], Options\Option::_() );
		$on_boarding_options = $on_boarding_options instanceof Options\Option ? $on_boarding_options : Options\Option::_();

		// set a flag to do a meet redirect (then it will be checked if on boarding has been passed)
		$on_boarding_options->allowed = true;

		Options::_()->pages_onBoarding_redirect = $on_boarding_options;
	}

    public static function deactivate() {
		// deleting already scheduled event
		wp_clear_scheduled_hook( self::ACCOUNT_CHECK_SCHEDULE[ 'name' ] );

		// adding or updating option to run Front-end SPAI tests
		Options::_()->tests_frontEnd_enqueued = true;
    }

	public function display_notice( $type, $data = false, $iconSuffix = '' ) {
		require_once( $this->plugin_dir . 'includes/notices.php' );
		new ShortPixelAINotice( $type, $data, $iconSuffix );
	}


    public function is_conflict() {
	    if ( in_array( $this->conflict, self::$SHOW_STOPPERS ) ) { // the elementorexternal doesn't deactivate the plugin
		    return $this->conflict;
	    }

        $this->conflict = 'none';

	    if ( !function_exists( 'is_plugin_active' ) || is_plugin_active( 'autoptimize/autoptimize.php' ) ) {
		    $autoptimizeImgopt = get_option( 'autoptimize_imgopt_settings', false ); //this is set by Autoptimize version >= 2.5.0

		    if ( $autoptimizeImgopt ) {
			    $this->conflict = ( isset( $autoptimizeImgopt[ 'autoptimize_imgopt_checkbox_field_1' ] ) && $autoptimizeImgopt[ 'autoptimize_imgopt_checkbox_field_1' ] == '1' ? 'ao' : 'none' );
		    }
		    else {
			    $autoptimizeExtra = get_option( 'autoptimize_extra_settings', false ); //this is set by Autoptimize version <= 2.4.4
			    $this->conflict   = ( isset( $autoptimizeExtra[ 'autoptimize_extra_checkbox_field_5' ] ) && $autoptimizeExtra[ 'autoptimize_extra_checkbox_field_5' ] ) ? 'ao' : 'none';
		    }
	    }

        if (function_exists('is_plugin_active') && is_plugin_active('divi-toolbox/divi-toolbox.php')) {
	        $path = SHORTPIXEL_AI_WP_PLUGINS_DIR . '/divi-toolbox/divi-toolbox.php';
            $pluginInfo = get_plugin_data($path);
            if(is_array($pluginInfo) && version_compare($pluginInfo['Version'], '1.4.2') < 0) {//older versions than 1.4.2 produce the conflict
                $diviToolboxOptions = unserialize(get_option('dtb_toolbox', 'a:0:{}'));
                if(is_array($diviToolboxOptions) && isset($diviToolboxOptions['dtb_post_meta'])) {
                    $this->conflict = 'divitoolbox';
                    return $this->conflict;
                }
            }
        }
        if (function_exists('is_plugin_active') && is_plugin_active('lazy-load-optimizer/lazy-load-optimizer.php')) {
            $this->conflict = 'llopt';
            return $this->conflict;
        }
        if (function_exists('is_plugin_active') && is_plugin_active('ginger/ginger-eu-cookie-law.php')) {
            $ginger = get_option('ginger_general', array());
            if(isset($ginger['ginger_opt']) && $ginger['ginger_opt'] === 'in') {
                $this->conflict = 'ginger';
                return $this->conflict;
            }
        }

        $theme = wp_get_theme();
        if (strpos($theme->Name, 'Avada') !== false) {
            $avadaOptions = get_option('fusion_options', array());
            if (isset($avadaOptions['lazy_load']) && $avadaOptions['lazy_load'] == '1') {
                $this->conflict = 'avadalazy';
            }
        }

	    if ( !function_exists( 'is_plugin_active' ) || is_plugin_active( 'elementor/elementor.php' ) || is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
		    $elementorCSS = get_option( 'elementor_css_print_method', false );

		    if ( $elementorCSS == 'external' ) {
			    if ( !isset( $this->settings->areas->parse_css_files ) ) {
				    $this->options->settings_areas_parseCssFiles = true;
			    }
			    else if ( $this->settings->areas->parse_css_files === false ) { //the option is explicitely unset by user
				    $this->conflict = 'elementorexternal';

				    return $this->conflict;
			    }
		    }
	    }

        return $this->conflict;
    }

	/**
	 * Method fills the empty spaces with zero data in API response
	 *
	 * @param array|object $place
	 * @param mixed $empty_data
	 */
	private function fill_cdn_usage( &$place, $empty_data ) {
		if ( empty( $place ) || ( !is_array( $place ) && !is_object( $place ) ) ) {
			return;
		}

		$is_object = false;

		if ( is_object( $place ) ) {
			$is_object = true;
			$place     = (array) $place;
		}

		// flag to double check the array has it more skipped dates?
		$has_skipped = false;

		foreach ( $place as $date => $value ) {
			$current_time       = time();
			$next_day_time      = strtotime( '+1 day', strtotime( $date ) );
			$next_day_formatted = date( 'Y-m-d', $next_day_time );

			// if next day is a real next day - stop it
			if ( $current_time < $next_day_time ) {
				break;
			}

			if ( empty( $place[ $next_day_formatted ] ) ) {
				$has_skipped                  = true;
				$place[ $next_day_formatted ] = $empty_data;
			}
		}

		if ( $has_skipped ) {
			$this->fill_cdn_usage( $place, $empty_data );
		}

		// sorting by keys
		ksort( $place );

		$place = $is_object ? (object) $place : $place;
	}

	/**
	 * Method retrieves information about domain from CDN's API
	 *
	 * @param null|string $domain Targeted domain (current by default)
	 * @param null|string $key    API key which domain connected
	 *
	 * @return false|object
	 */
	public function get_cdn_domain_usage( $domain = null, $key = null ) {
		if ( empty( $key ) ) {
			$key = $this->options->settings_general_apiKey;
		}

		if ( empty( $key ) ) {
			$key = get_option( 'wp-short-pixel-apiKey', false );
		}

		if ( !is_string( $key ) || empty( $key ) ) {
			return false;
		}

		if ( !is_string( $domain ) || empty( $domain ) ) {
			$domain = $this->get_site_domain();
		}

		$api_url  = self::DEFAULT_API_AI . '/read-domain-cdn-usage/' . $domain . '/' . $key;
		$response = Request::get( $api_url );

		if ( $response === '' ) {
			return false;
		}

		foreach ( $response as $key => $value ) {
			if ( $key === date( 'Y-m-d' ) || ( isset( $value->Traf ) && $value->Traf == 0 ) ) {
				return false;
			}
		}

		$return = (object) [];

		if ( !empty( $response->Email ) ) {
			$return->email = $this->truncate_email( $response->Email );
		}

		$return->quota   = (object) [];
		$return->cdn     = (object) [];
		$return->credits = (object) [];

		$return->quota->monthly = (object) [];
		$return->quota->oneTime = (object) [];

		$return->cdn->total     = $response->CDNQuota;
		$return->cdn->used      = 0;
		$return->cdn->available = null;
		// it'll be calculated later
		$return->cdn->usedPercent      = null;
		$return->cdn->availablePercent = null;
		$return->cdn->detailed         = [];
		$return->cdn->chart            = (object) [
			'labels' => [],
			'data'   => (object) [
				'b'  => [],
				'kb' => [],
				'mb' => [],
				'gb' => [],
			],
		];

		// Monthly quota
		$return->quota->monthly->total            = (int) $response->APIQuota;
		$return->quota->monthly->used             = (int) $response->PaidAPICalls;
		$return->quota->monthly->used             = $return->quota->monthly->used > $return->quota->monthly->total ? $return->quota->monthly->total : $return->quota->monthly->used;
		$return->quota->monthly->available        = $return->quota->monthly->total - $return->quota->monthly->used;
		$return->quota->monthly->usedPercent      = $return->quota->monthly->total > 0 ? round( $return->quota->monthly->used / ( $return->quota->monthly->total / 100 ), 2 ) : 0.0;
		$return->quota->monthly->availablePercent = 100 - $return->quota->monthly->usedPercent;

		// One-time quota
		$return->quota->oneTime->total            = (int) $response->APIQuotaOneTime;
		$return->quota->oneTime->used             = (int) $response->PaidAPICallsOneTime;
		$return->quota->oneTime->used             = $return->quota->oneTime->used > $return->quota->oneTime->total ? $return->quota->oneTime->total : $return->quota->oneTime->used;
		$return->quota->oneTime->available        = $return->quota->oneTime->total - $return->quota->oneTime->used;
		$return->quota->oneTime->usedPercent      = $return->quota->oneTime->total > 0 ? round( $return->quota->oneTime->used / ( $return->quota->oneTime->total / 100 ), 2 ) : 0.0;
		$return->quota->oneTime->availablePercent = 100 - $return->quota->oneTime->usedPercent;

		$return->credits->detailed  = [];
		$return->credits->chart     = (object) [
			'labels' => [],
			'data'   => (object) [
				'paid'      => [],
				'free'      => [],
				'total'     => [],
				'original'  => (object) [
					'b'  => [],
					'kb' => [],
					'mb' => [],
					'gb' => [],
				],
				'optimized' => (object) [
					'b'  => [],
					'kb' => [],
					'mb' => [],
					'gb' => [],
				],
			],
		];
		$return->credits->original  = 0;
		$return->credits->optimized = 0;

		/* CDN Usage */
		if ( !empty( $response->UsedCDN ) ) {
			// filling empty spaces with zero data
			$this->fill_cdn_usage( $response->UsedCDN, (object) [
				'Traf' => 0,
			] );

			// creating our own object
			foreach ( $response->UsedCDN as $date => $value ) {
				$traffic = isset( $value->Traf ) ? $value->Traf : 0;

				$return->cdn->chart->labels[]   = date( 'M d', strtotime( $date ) );
				$return->cdn->chart->data->b[]  = $traffic;
				$return->cdn->chart->data->kb[] = round( $traffic / 1024, 2 );
				$return->cdn->chart->data->mb[] = round( $traffic / 1024 / 1024, 2 );
				$return->cdn->chart->data->gb[] = round( $traffic / 1024 / 1024 / 1024, 2 );

				$return->cdn->detailed[] = (object) [
					'date'    => $date,
					'traffic' => $traffic,
				];

				$return->cdn->used += $traffic;
			}
		}

		$return->cdn->available        = $return->cdn->total - $return->cdn->used;
		$return->cdn->usedPercent      = $return->cdn->total > 0 ? round( $return->cdn->used * 100 / $return->cdn->total, 2 ): 0;
		$return->cdn->availablePercent = 100 - $return->cdn->usedPercent;

		// Credits Usage
		if ( !empty( $response->UsedCredits ) ) {
			// filling empty spaces with zero data
			$this->fill_cdn_usage( $response->UsedCredits, (object) [
				'Orig' => 0,
				'Opt'  => 0,
				'Paid' => 0,
				'Free' => 0,
			] );

			// creating our own object
			foreach ( $response->UsedCredits as $date => $value ) {
				$original  = isset( $value->Orig ) ? $value->Orig : 0;
				$optimized = isset( $value->Opt ) ? $value->Opt : 0;
				$free      = isset( $value->Free ) ? $value->Free : 0;
				$paid      = isset( $value->Paid ) ? $value->Paid : 0;

				$return->credits->chart->labels[]      = $date;
				$return->credits->chart->data->paid[]  = $paid;
				$return->credits->chart->data->free[]  = $free;
				$return->credits->chart->data->total[] = $paid + $free;

				// Original size
				$return->credits->chart->data->original->b[]  = $original;
				$return->credits->chart->data->original->kb[] = round( $original / 1024, 2 );
				$return->credits->chart->data->original->mb[] = round( $original / 1024 / 1024, 2 );
				$return->credits->chart->data->original->gb[] = round( $original / 1024 / 1024 / 1024, 2 );

				// Optimized size
				$return->credits->chart->data->optimized->b[]  = $optimized;
				$return->credits->chart->data->optimized->kb[] = round( $optimized / 1024, 2 );
				$return->credits->chart->data->optimized->mb[] = round( $optimized / 1024 / 1024, 2 );
				$return->credits->chart->data->optimized->gb[] = round( $optimized / 1024 / 1024 / 1024, 2 );

				$return->credits->detailed[] = (object) [
					'date'      => $date,
					'paid'      => $paid,
					'free'      => $free,
					'total'     => $paid + $free,
					'original'  => $original,
					'optimized' => $optimized,
				];

				$return->credits->original  += $original;
				$return->credits->optimized += $optimized;

				$original_percent = $original / 100;

				$return->credits->optimizedPercent = $original_percent === 0 ? 0 : round( ( $original - $optimized ) / $original_percent, 2 );
			}
		}

		return $return;
	}

	/**
	 * Method returns current blog's domain
	 * @return false|string
	 */
	public function get_site_domain() {
		return function_exists( 'parse_url' ) ? parse_url( get_home_url(), PHP_URL_HOST ) : false;
	}

	/**
	 * Method truncates the email to hide customer's full email
	 *
	 * @param string $email
	 *
	 * @return false|string
	 */
	public function truncate_email( $email ) {
		$email = explode( '@', $email );

		if ( count( $email ) === 2 ) {
			$email[ 0 ] = substr( $email[ 0 ], 0, max( 3, strlen( $email[ 0 ] ) / 2 ) ) . "...";

			if ( strlen( $email[ 1 ] ) > 3 ) {
				return implode( '@', $email );
			}
		}

		return false;
	}

	/**
	 * Method gets the ShortPixel account using SPIO's API key
	 *
	 * @return array|mixed|object
	 */
    public function get_shortpixel_account()
    {
        $email = array();
        $resp = new stdClass();
        if (($spKey = get_option('wp-short-pixel-apiKey', false))) {
            $resp = $this->get_domain_status();
            if ($resp->Status == -3) {
                return $resp;
            }
            if ($resp->HasAccount) {
                return (object)array('status' => 3, 'Message' => 'already associated', 'key' => '', 'email' => $resp->Email);
            }
            //the domain is not associated, check with SP API the user info for the key found locally
            $responseSP = wp_safe_remote_get(self::SP_API . '/v2/user-info.php?key=' . $spKey, array('timeout' => 120, 'httpversion' => '1.1'));
            if (is_object($responseSP) && get_class($responseSP) == 'WP_Error') {
                return (object)array('status' => -3, 'Message' => 'connection error', 'key' => '', 'email' => '');
            }
            if (isset($responseSP['response'])) {
                $respSP = json_decode($responseSP['body']);
                $email = explode('@', $respSP->email);
                if (/* $resp->HasAccount && */
                    count($email) == 2) {
                    $email[0] = substr($email[0], 0, max(3, strlen($email[0]) / 2)) . "...";
                }
            }
        }
        return (object)array_merge((array)$resp, array('key' => $spKey, 'email' => implode('@', $email)));
    }

	/**
	 * Method tries to associate current domain using SPIO's API key
	 *
	 * @return bool
	 */
	public function use_shortpixel_account() {
		$spio_api_key = get_option( 'wp-short-pixel-apiKey', false );

		if ( $spio_api_key ) {
			$response = $this->associate_domain( null, $spio_api_key );

			if ( $response->success ) {
				$this->options->flags_all_account       = true;
				$this->options->settings_general_apiKey = $spio_api_key;

				return true;
			}
		}

		return false;
	}

	public function associate_domain( $domain = null, $key = null ) {
		if ( empty( $key ) ) {
			$key = $this->options->settings_general_apiKey;
		}

		if ( empty( $key ) ) {
			$key = get_option( 'wp-short-pixel-apiKey', false );
		}

		if ( !is_string( $key ) || empty( $key ) ) {
			return false;
		}

		if ( !is_string( $domain ) || empty( $domain ) ) {
			$domain = $this->get_site_domain();
		}

		$api_url = self::DEFAULT_API_AI . '/add-domain/' . $domain . '/' . $key;
		$request = wp_safe_remote_get( $api_url, [ 'timeout' => 120, 'httpversion' => '1.1' ] );

		if ( $request instanceof WP_Error ) {
			return false;
		}

		if ( empty( $request[ 'response' ] ) || $request[ 'response' ][ 'code' ] !== 200 ) {
			return false;
		}

		$response = json_decode( $request[ 'body' ] );

		return (object) [
			'success' => $response->Status === 1 || $response->Status === 2,
			'status'  => $response->Status,
			'message' => $response->Message,
		];
	}

	public function get_domain_status( $refresh = false, $domain = null ) {
		if ( !is_string( $domain ) || empty( $domain ) ) {
			$domain = $this->get_site_domain();
		}

		if ( !$refresh && ( $domain_status = get_transient( 'spai_domain_status' ) ) ) {
			$domain_status->cached = 'yes';

			return $domain_status;
		}

		//possible statuses: 2 OK (credits available, this is also for not associated domains) 0 - credits near limit,
		// -1 credits depleted, CDN active, -2 credits depleted, CDN inactive, -3 connection error
		$api_url  = self::DEFAULT_API_AI . '/read-domain/' . $domain;
		$response = wp_safe_remote_get( $api_url, [ 'timeout' => 120, 'httpversion' => '1.1' ] );

		if ( $response instanceof WP_Error || !isset( $response[ 'response' ] ) ) {
			return (object) [ 'Status' => -3, 'Message' => 'connection error: ' . $response->get_error_message() ];
		}

		$domain_status = json_decode( wp_remote_retrieve_body( $response ) );
		set_transient( 'spai_domain_status', $domain_status, 600 );

		return $domain_status;
	}

    public function get_extension( $url ) {
		preg_match( '/.+(\.(\w+))\?*.*/su', $url, $matches );

		return empty( $matches[ 2 ] ) ? false : $matches[ 2 ];
	}

	public function get_api_url( $width = '%WIDTH%', $height = '%HEIGHT%', $type = false ) {
        $args = array();

		if ( $type !== 'svg' ) {
			//ATTENTION, w_ should ALWAYS be the first parameter if present! (see fancyboxUpdateWidth in JS)
			if ( $width !== false ) {
				$args[] = array( 'w' => $width );
				//$args[] = array('h' => $height);
			}
			$args[] = array( 'q' => ( $this->settings->compression->level ) );
			/*
			$args[] = ['g' => 'face'];
			$args[] = ['r' => 'max']
			$args[] = ['f' => 'auto']
			$args[] = ['x' => $this->settings['remove_exif'];*/
			//most proably not a good idea because of the page cache:
			/*if($this->browser_can_webp()) {
				$args[] = array('to' => 'webp');
			}*/
			if ( !$this->settings->compression->remove_exif ) {
				$args[] = array( 'ex' => '1' );
			}
		}

		$args[] = array( 'ret' => 'img' );// img returns the original if not found, wait will wait for a quick resize

		$api_url = $this->settings->behaviour->api_url;

        if ( !$api_url ) {
			$api_url = self::DEFAULT_API_AI . self::DEFAULT_API_AI_PATH;
		}

        $api_url = trailingslashit($api_url);

        /*
        Make args to be in desired format
         */
        foreach ($args as $arg) {
            foreach ($arg as $k => $v) {
                $api_url .= $k . '_' . $v . self::SEP;
            }
        }
        $api_url = rtrim($api_url, self::SEP);
        //$api_url = trailingslashit( $api_url );
        return $api_url;
    }

    protected function browser_can_webp() {
        $userAgent = explode(' ', $_SERVER['HTTP_USER_AGENT']);
        foreach($userAgent as $uaPart) {
            $uaPart = explode('/', $uaPart);
            if(count($uaPart) >= 2) {
                $ver = explode('.', $uaPart[1]);
                switch($uaPart[0]) {
                    case 'Chrome':
                        return intval($ver[0]) >= 32;
                    case 'Firefox':
                        return intval($ver[0]) >= 65;
                }
            }
        }
        return false;
    }

    public function maybe_replace_images_src($content)
    {

        if (!$this->doingAjax && !wp_script_is('spai-scripts')) {
            //the script was dequeued
            $this->logger->log("SPAI JS DEQUEUED ... and it's not AJAX");
            $this->spaiJSDequeued = true;
        }
        /*if(strpos($_SERVER['REQUEST_URI'],'action=alm_query_posts') > 0) {
            $this->logger->log("CONTENT: " . substr($content, 0, 200));
        //}*/
        if ((function_exists('is_amp_endpoint') && is_amp_endpoint())) {
            $this->logger->log("IS AMP ENDPOINT");
            return $content;
        }

        $contentObj = json_decode($content);
        $isJson = !($jsonErr = json_last_error() === JSON_ERROR_SYNTAX) && ($contentObj !== null);
        if ($isJson) {
            $this->logger->log("JSON CONTENT: " . $content);
            if ($this->settings->areas->parse_json) {
                $jsonParser = new ShortPixelJsonParser($this);
                $content = json_encode($jsonParser->parse($contentObj));
                $this->recordAffectedTags($this->affectedTags);
            }
            else {
                $changed = false;
                //if not parsing json, still replace inside first level html properties.
                if(is_object($contentObj) || is_array($contentObj)) { //primitive types as 'lala' or 10 can also be JSON, can't iterate over these.
                    foreach($contentObj as $key => $value) {
                        if(is_string($value) && preg_match('/^([\s↵]*(<!--[^>]+-->)*)*<\w*(\s[^>]*|)>/s', $value)) {
                            $contentObj->$key = $this->parser->parse($value);
                            $changed = true;
                        }
                    }
                }
                if($changed) {
                    //$this->logger->log(' AJAX - recording affected tags ', $this->affectedTags);
                    $this->recordAffectedTags($this->affectedTags);
                    $content = json_encode($contentObj);
                } else {
                    $this->logger->log("MISSING HTML");
                }
            }
        } elseif($this->spaiJSDequeued) {
            //TODO in cazul asta vom inlocui direct cu URL-urile finale ca AO
        } elseif(preg_match('/^(\s*<!--[^->!]+\-->)*\s*<\s*(!\s*doctype|\s*[a-z0-9]+)(\s+[^\>]+|)\/?\s?>/i', $content)) { //check if really HTML
            $content = $this->parser->parse($content);
        }

        return $content;
    }

    /*    public function replace_images_no_quotes ($matches) {
            if (strpos($matches[0], 'src=data:image/svg;u=') || count($matches) < 2){
                //avoid duplicated replaces due to filters interference
                return $matches[0];
            }
            return $this->_replace_images('src', $matches[0], $matches[1]);
        }*/

    public function maybe_cleanup($content)
    {
        $this->logger->log('CLEANUP: ' . preg_quote($this->settings->behaviour->api_url, '/'));
        return preg_replace_callback('/' . preg_quote($this->settings->behaviour->api_url, '/') . '.*?\/(https?):\/\//is',
            array($this, 'replace_api_url'), $content);
    }
    public function replace_api_url($matches) {
        return $matches[1] . '://';
    }

    /**
     * Method is unused anywhere
     * Method which locates below seems has been a replacement of this
     *
     * @param $matches
     *
     * @return mixed
     */
    public function replace_images_data_large_image($matches)
    {
        // if (count($matches) < 3 || strpos($matches[0], 'data-large_image=' . $matches[1] . 'data:image/svg+xml;u=')) { // old
        if (count($matches) < 3 || strpos($matches[0], 'data-large_image=' . $matches[1] . 'data:image/svg+xml;base64')) {
            //avoid duplicated replaces due to filters interference
            return $matches[0];
        }
        //$matches[1] will be either " or '
        return $this->_replace_images('data-large_image', $matches[0], $matches[2], $matches[1]);
    }


    public function replace_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if((function_exists('is_amp_endpoint') && is_amp_endpoint())) {
            return $sources;
        }
        $aspect = false;
        $this->logger->log("******** REPLACE IMAGE SRCSET: ", $sources);
        //return $sources;
        if($this->urlIsExcluded($image_src)) return $sources;
        if($this->settings->behaviour->replace_method === 'src') return $sources; //not returning array() because the srcset is integrated and removed in full document parse;
        $pseudoSources = array();
        foreach ($sources as $key => $data) {
            // if(strpos($data['url'], 'data:image/svg+xml;u=') === false) { // old implementation
            if(ShortPixelUrlTools::url_from_placeholder_svg($data['url']) !== false) {
                if($this->urlIsExcluded($data['url'])) {
                    //if any of the items are excluded, don't replace
                    return $sources;
                }
                if($aspect === false) {
                    $sizes = ShortPixelUrlTools::get_image_size($image_src);
                    $aspect = $sizes[1] / $sizes[0];
                    $height = $sizes[1] > 1 ? $sizes[1] : 100;
                } else {
                    $height = round($key * $aspect);
                }
                $pseudoSources[$key] = array(
                    'url' => ShortPixelUrlTools::generate_placeholder_svg($key, $height, $data['url']),//$this->absoluteUrl($data['url'])),
                    'descriptor' => $data['descriptor'],
                    'value' => $data['value']);
            } else {
                $pseudoSources[$key] = $data;
            }
        }
        $this->logger->log("******** WITH: ", $pseudoSources);
        return $pseudoSources;
    }

    public function getActiveIntegrations( $refresh = false ) {
	    if ( $this->integrations && !$refresh ) {
		    return $this->integrations;
	    }

        $activePlugins = (array) apply_filters('active_plugins', get_option( 'active_plugins', array()));
        if ( is_multisite() ) {
            $activePlugins = array_merge($activePlugins, array_keys(get_site_option( 'active_sitewide_plugins')));
        }
        //test WPRocket
        if (in_array('wp-rocket/wp-rocket.php', $activePlugins)) {
//            $path = dirname(dirname(__DIR__)) . '/wp-rocket/wp-rocket.php';
			$path = WP_CONTENT_DIR . '/plugins/wp-rocket/wp-rocket.php'; // Yurii: Right WP path
            $pluginVersion = $this->readPluginVersion($path);
            $wpRocketSettings = get_option('wp_rocket_settings', array());

            $hasCssFilter = (version_compare($pluginVersion, '3.4.3') >= 0);
			$hasLazyFilter = version_compare( $pluginVersion, '2.5', '>=' ); // has the WP Rocket hook for a lazy loading disabling?

			$rocket = array(
				'lazyload'   => !$hasLazyFilter && ( isset( $wpRocketSettings[ 'lazyload' ] ) && $wpRocketSettings[ 'lazyload' ] == 1 ),
				'css-filter' => $hasCssFilter,
				'minify-css' => isset( $wpRocketSettings[ 'minify_css' ] ) && $wpRocketSettings[ 'minify_css' ] == 1,
				'defer-all-js' => !!$wpRocketSettings[ 'defer_all_js' ] && !$wpRocketSettings[ 'defer_all_js_safe' ],
			);

        } else {
            $rocket = array('lazyload' => false, 'css-filter' => false, 'minify-css' => false, 'defer-all-js' => false);
        }

		// Swift Performance
		$swift_performance = array(
			'lite' => array(
				'active' => in_array( 'swift-performance-lite/performance.php', $activePlugins ),
				'path'   => WP_CONTENT_DIR . '/plugins/swift-performance-lite/performance.php',
			),
			'pro'  => array(
				'active' => in_array( 'swift-performance/performance.php', $activePlugins ),
				'path'   => WP_CONTENT_DIR . '/plugins/swift-performance/performance.php',
			),
		);

		foreach ( $swift_performance as $plugin => $info ) {
			if ( !$info[ 'active' ] ) {
				continue;
			}

			$swift_performance[ $plugin ][ 'version' ] = defined( 'SWIFT_PERFORMANCE_VER' ) ? SWIFT_PERFORMANCE_VER : $this->readPluginVersion( $info[ 'path' ] );
			$swift_performance[ $plugin ][ 'has_bug' ] = version_compare( $swift_performance[ $plugin ][ 'version' ], $plugin === 'lite' ? '2.1.2' : '2.1.6.2', $plugin === 'lite' ? '<=' : '<' );

			switch ( $plugin ) {
				case 'lite' :
					if ( class_exists( 'Swift_Performance_Lite' ) ) {
						$swift_performance[ $plugin ][ 'has_conflict' ] = !!\Swift_Performance_Lite::get_option( 'normalize-static-resources' )
							&& !!\Swift_Performance_Lite::get_option( 'merge-styles' );

						$swift_performance[ $plugin ][ 'merge_styles' ] = !!\Swift_Performance_Lite::get_option( 'merge-styles' );
					}
					break;

				case 'pro' :
					if ( class_exists( 'Swift_Performance' ) ) {
						$swift_performance[ $plugin ][ 'has_conflict' ] = !!\Swift_Performance::get_option( 'normalize-static-resources' )
							&& !!\Swift_Performance::get_option( 'merge-styles' );

						$swift_performance[ $plugin ][ 'merge_styles' ] = !!\Swift_Performance::get_option( 'merge-styles' );
					}
					break;
			}

			$swift_performance = $swift_performance[ $plugin ];

			$swift_performance[ 'plugin' ] = $plugin;
		}

		// Imagify
		$imagify = [
			'active' => in_array( 'imagify/imagify.php', $activePlugins ),
		];

		if ( function_exists( 'get_imagify_option' ) ) {
			$imagify[ 'has_conflict' ] = !!get_imagify_option( 'display_webp' );
		}

		// WP Fastest Cache
	    $wp_fastest_cache = false;

	    if ( in_array( 'wp-fastest-cache/wpFastestCache.php', $activePlugins ) ) {
		    $path    = WP_PLUGIN_DIR . '/wp-fastest-cache/wpFastestCache.php';
		    $version = $this->readPluginVersion( $path );

		    if ( version_compare( $version, '0.9.0.7', '>=' ) ) {
			    if ( class_exists( 'WpFastestCacheCreateCache' ) ) {
				    $wpfc = new WpFastestCacheCreateCache;

				    if ( property_exists( $wpfc, 'options' ) && is_object( $wpfc->options ) ) {
					    if (
						    ( property_exists( $wpfc->options, 'wpFastestCacheStatus' ) && !!$wpfc->options->wpFastestCacheStatus )
						    && ( ( property_exists( $wpfc->options, 'wpFastestCacheMinifyCss' ) && !!$wpfc->options->wpFastestCacheMinifyCss )
						         || ( property_exists( $wpfc->options, 'wpFastestCacheCombineCss' ) && !!$wpfc->options->wpFastestCacheCombineCss ) )
					    ) {
						    $wp_fastest_cache = true;
					    }
					    else {
						    SHORTPIXEL_AI_DEBUG && $this->logger->log( "WPFC: ", $wpfc );
						    $wp_fastest_cache = false;
					    }
				    }
			    }
			    // condition for pre-init or admin pages because class WpFastestCacheCreateCache exists after "admin_init" and "init" hooks
			    else {
				    $wp_fastest_cache = true;
			    }
		    }
	    }

	    // W3 Total Cache
	    $w3_total_cache = false;

	    if ( in_array( 'w3-total-cache/w3-total-cache.php', $activePlugins ) ) {
		    $path    = WP_PLUGIN_DIR . '/w3-total-cache/w3-total-cache.php';
		    $version = $this->readPluginVersion( $path );

		    if ( version_compare( $version, '0.14.0', '>=' ) ) {
			    $w3_total_cache = true;
		    }
	    }

	    // WP Optimize
	    $wp_optimize = false;

	    if ( in_array( 'wp-optimize/wp-optimize.php', $activePlugins ) ) {
		    if ( function_exists( 'wp_optimize_minify_config' ) ) {
			    $config = wp_optimize_minify_config();

			    if ( method_exists( $config, 'get' ) ) {
				    $config = $config->get();

				    if ( is_array( $config ) ) {
					    $has_ignore_list = false;
					    $has_exclusion   = true;

					    if ( isset( $config[ 'ignore_list' ] ) && is_string( $config[ 'ignore_list' ] ) ) {
						    $has_ignore_list = true;

						    $plugin_folder = plugin_basename( SHORTPIXEL_AI_PLUGIN_DIR );

						    $conflicting_files = [
							    '/' . $plugin_folder . '/assets/css/admin.css',
							    '/' . $plugin_folder . '/assets/css/admin.min.css',
								'/' . $plugin_folder . '/assets/css/style-bar.css',
							    '/' . $plugin_folder . '/assets/css/style-bar.min.css',
						    ];

						    foreach ( $conflicting_files as $file ) {
							    if ( strpos( $config[ 'ignore_list' ], $file ) === false ) {
								    $has_exclusion = false;
								    break;
							    }
						    }
					    }

					    if (
						    isset( $config[ 'enable_css' ] ) && isset( $config[ 'enable_merging_of_css' ] )
						    && ( !!$config[ 'enable_css' ] && !!$config[ 'enable_merging_of_css' ] )
						    && ( $has_ignore_list && !$has_exclusion )
					    ) {
						    $wp_optimize = true;
					    }
				    }
			    }
		    }
	    }

        $this->integrations = array(
            'nextgen' => in_array('nextgen-gallery/nggallery.php', $activePlugins),
            'modula' => in_array('modula-best-grid-gallery/Modula.php', $activePlugins),
            'elementor' => in_array('elementor/elementor.php', $activePlugins),
            'elementor-addons' => in_array('essential-addons-for-elementor/essential_adons_elementor.php', $activePlugins) || in_array('essential-addons-for-elementor-lite/essential_adons_elementor.php', $activePlugins),
            'viba-portfolio' => in_array('viba-portfolio/viba-portfolio.php', $activePlugins),
            'envira' => in_array('envira-gallery/envira-gallery.php', $activePlugins) || in_array('envira-gallery-lite/envira-gallery-lite.php', $activePlugins),
            'everest' => in_array('everest-gallery/everest-gallery.php', $activePlugins) || in_array('everest-gallery-lite/everest-gallery-lite.php', $activePlugins),
            'wp-bakery' => in_array('js_composer/js_composer.php', $activePlugins), //WP Bakery (testimonials)
            'woocommerce' => in_array('woocommerce/woocommerce.php' , $activePlugins),
            'foo' => in_array('foogallery/foogallery.php', $activePlugins),
            'oxygen' => in_array( 'oxygen/functions.php', $activePlugins),
            'slider-revolution' => in_array('revslider/revslider.php', $activePlugins),
            'smart-slider' => in_array('smart-slider-3/smart-slider-3.php', $activePlugins) || in_array('nextend-smart-slider3-pro/nextend-smart-slider3-pro.php', $activePlugins),
            'wp-grid-builder' => in_array('wp-grid-builder/wp-grid-builder.php', $activePlugins),
            'the-grid' => in_array('the-grid/the-grid.php', $activePlugins),
            'social-pug' => in_array('social-pug/index.php', $activePlugins), //Mediavine Grow
            'content-views' => in_array( 'content-views-query-and-display-post-page/content-views.php', $activePlugins),
            'wp-rocket' => $rocket,
            'swift-performance' => isset( $swift_performance[ 'active' ] ) ? $swift_performance : false,
			'imagify' => isset( $imagify[ 'active' ] ) ? $imagify : false,
			'wp-fastest-cache' => $wp_fastest_cache,
			'w3-total-cache' => $w3_total_cache,
			'wp-optimize' => $wp_optimize,
        );
        //test theme. 'Jupiter' 'CROWD 2'
        $theme = wp_get_theme();
        $this->integrations['theme'] = $theme->Name;
        if(SHORTPIXEL_AI_DEBUG) {
            //integration forced from the request parameters
            foreach($this->integrations as $key => $val) {
                if(isset($_REQUEST['spai_force_' . $key])) {
                    $this->integrations[$key] = true;
                }
            }
        }
        return $this->integrations;
    }

	public function readPluginVersion( $path ) {
		$ver = '0.0';

		if ( file_exists( $path ) ) {
			$fp = fopen( $path, 'r' );
			for ( $i = 0; $i < 100 && !feof( $fp ); $i++ ) {
				$line = trim( fgets( $fp ) );
				if ( strpos( $line, 'Version:' ) !== false ) {
					$version = explode( 'Version:', $line );
					$ver     = trim( end( $version ) );
					break;
				}
			}
			fclose( $fp );
		}

		return $ver;
	}

    public function getTagRules() {
        if ($this->integrations === false) {
	        $this->integrations = $this->getActiveIntegrations( true );
        }

        $regexItems = array(
            //IF YOU CHANGE ORDER AGAIN, CHANGE ALSO IN if(...nextgen) BELOW
            array('img|div', 'data-src'), //CHANGED ORDER for images which have both src and data-src - TODO better solution

            array('img|amp-img', 'src', false, false, ($this->settings->behaviour->replace_method === 'src' ? 'srcset' : false), false, false, false /*$this->settings['ext_meta'] == '1'*/),
            //fifth param instructs to integrate that attribute into the second, for method 1 (src) we integrate of srcset
            //\-> The given fifth attribute should have the exact structure of srcset.
            // eighth param - extMeta (default false) TODO REMOVE OBSOLETE
            array('img', 'data-large-image'),
            array('a', 'href', 'media-gallery-link'), //this one seems generally related to sliders, see HS 972394549
            array('link', 'href', false, 'rel', false, 'icon', true), //sixth param together with fourth filters by attribute value, seventh param isEager
            array('input', 'src', false, 'type', false, 'image'), // only this case so far: HS#39219
            array('video', 'poster')
        );
        if ($this->integrations['oxygen']) {
            $regexItems[] = array('a', 'href', 'oxy-gallery-item', false, false, false, true);
            array_unshift($regexItems, array('img', 'data-original-src', false, false, false, false, true));
        }
        if ($this->integrations['nextgen']) {
            $regexItems[0] = array('img|div|a', 'data-src');
            $regexItems[] = array('a', 'data-thumbnail');
        }
        if ($this->integrations['modula']) {
            $regexItems[] = array('a', 'href', false, 'data-lightbox'); //fourth param filters by attribute
            $regexItems[] = array('a', 'href', false, 'data-fancybox');
        }
        if ($this->integrations['elementor']) {
            $regexItems[] = array('a', 'href', false, 'data-elementor-open-lightbox'); //fourth param filters by attribute
            $regexItems[] = array('a', 'href', 'viba-portfolio-media-link'); //third param filters by class
        }
        if ($this->integrations['elementor-addons']) {
            $regexItems[] = array('a', 'href', 'eael-magnific-link'); //fourth param filters by attribute
        }
        if ($this->integrations['viba-portfolio'] && !$this->integrations['elementor']) {
            $regexItems[] = array('a', 'href', 'viba-portfolio-media-link'); //third param filters by class
        }
        if ($this->integrations['slider-revolution']) {
            $regexItems[] = array('img', 'data-lazyload', false, false, false, false, true);
        }
        if ($this->integrations['envira']) {
            $regexItems[] = array('img', 'data-envira-src');
            $regexItems[] = array('img', 'data-safe-src');
            $regexItems[] = array('a', 'href', 'envira-gallery-link'); //third param filters by class
        }
        if ($this->integrations['everest']) {
            $regexItems[] = array('a', 'href', false, 'data-lightbox-type'); //fourth param filters by attribute
        }
        if ($this->integrations['wp-bakery']) {
            $regexItems[] = array('span', 'data-element-bg', 'dima-testimonial-image');  //third param filters by class
        }
        if ($this->integrations['foo']) {
            $regexItems[] = array('img', 'data-src-fg', 'fg-image');
            $regexItems[] = array('a', 'href', 'fg-thumb', 'data-attachment-id');
        }
        if ($this->integrations['smart-slider']) {
            $regexItems[] = array('div', 'data-desktop', 'n2-ss-slide-background-image');  //third param filters by class
        }
        if ($this->integrations['wp-grid-builder']) {
            $regexItems[] = array('div', 'data-wpgb-src');
        }
        if ($this->integrations['content-views']) {
            $regexItems[] = array('img', 'data-cvpsrc');
        }
        if ($this->integrations['the-grid']) {
            $regexItems[] = array('a', 'data-tolb-src');
        }

	    if ( $this->settings->areas->parse_css_files && !( $this->integrations[ 'wp-rocket' ][ 'minify-css' ] && $this->integrations[ 'wp-rocket' ][ 'css-filter' ] ) && !$this->integrations[ 'wp-fastest-cache' ] && !$this->integrations[ 'w3-total-cache' ] ) {
            $this->logger->log("CSS FILES TO CDN");
            $regexItems[] = array('link', 'href', false, 'rel', false, 'stylesheet', true);
            $regexItems[] = array('link', 'href', false, 'rel', false, 'preload', true);
        }

        $this->logger->log("TAG RULES: ", $regexItems);
        return $regexItems;
    }

    public function getTagRulesMap() {
        $rules = $this->getTagRules();
        $tree = array();
        foreach($rules as $rule) {
            $tags = explode("|", $rule[0]);
            foreach($tags as $tag) {
                if(!isset($tree[$tag])) {
                    $tree[$tag] = array();
                }
                $ruleNode = array('attr' => $rule[1]);
                $ruleNode['classFilter'] = isset($rule[2]) ? $rule[2] : false;
                $ruleNode['attrFilter'] = isset($rule[3]) ? $rule[3] : false;
                $ruleNode['attrValFilter'] = isset($rule[5]) ? $rule[5] : false;
                $ruleNode['mergeAttr'] = isset($rule[4]) ? $rule[4] : false;
                $ruleNode['lazy'] = !isset($rule[6]) || ! $rule[6]? true : false;
                $ruleNode['extMeta'] = !isset($rule[7]) || ! $rule[7]? true : false;
                $tree[$tag][] = (object)$ruleNode;
            }
        }
        //add also the rule for background image
        $tree['*'] = array((object)array('attr' => 'style', 'lazy' => false, 'customReplacer' => array(&$this->cssParser, 'replace_in_tag_style_backgrounds')));
        return $tree;
    }

	public function getExceptionsMap() {
		return (object) [
			'excluded_selectors' => $this->splitSelectors( $this->settings->exclusions->excluded_selectors, ',' ),
			'eager_selectors'    => $this->splitSelectors( $this->settings->exclusions->eager_selectors, ',' ),
			'noresize_selectors' => $this->splitSelectors( $this->settings->exclusions->noresize_selectors, ',' ),
			'excluded_paths'     => $this->splitSelectors( $this->settings->exclusions->excluded_paths, "\n" ),
		];
	}

	public function tagIs( $type, $text ) {
		//could be excluded_selectors or noresize_selectors
		if (
			isset( $this->settings->exclusions->{$type . '_selectors'} ) && strlen( $this->settings->exclusions->{$type . '_selectors'} )
			&& ( strpos( $text, 'class=' ) !== false || strpos( $text, 'id=' ) !== false )
		) {
			foreach ( explode( ',', $this->settings->exclusions->{$type . '_selectors'} ) as $selector ) {
				$selector = trim( $selector );
				$parts    = explode( '.', $selector );
				if ( count( $parts ) == 2 && ( $parts[ 0 ] == '' || strpos( $text, $parts[ 0 ] ) === 1 ) ) {
					if ( preg_match( '/\sclass=[\'"]([-_a-zA-Z0-9\s]*[\s]+' . $parts[ 1 ] . '|' . $parts[ 1 ] . ')[\'"\s]/i', $text ) ) {
						return true;
					}
					else if ( preg_match( '/\sclass=' . $parts[ 1 ] . '[>\s]/i', $text ) ) {
						return true;
					}
				}
				else {
					$parts = explode( '#', $selector );
					if ( count( $parts ) == 2 && ( $parts[ 0 ] == '' || strpos( $text, $parts[ 0 ] ) === 1 ) ) {
						if ( preg_match( '/\sid=[\'"]' . $parts[ 1 ] . '[\'"\s]/i', $text ) ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	public function urlIsApi( $url ) {
		$parsed    = parse_url( $url );
		$parsedApi = parse_url( $this->settings->behaviour->api_url );

		return isset( $parsed[ 'host' ] ) && $parsed[ 'host' ] === $parsedApi[ 'host' ];
	}

    public function urlIsExcluded($url) {
        //exclude generated images like JetPack's admin bar hours stats
        if(strpos($url, '?page=')) {
            $admin = parse_url(admin_url());
            if(strpos($url, $admin['path'])) {
                return true;
            }
        }
        //$this->logger->log("IS EXCLUDED? $url");
        if( isset($this->settings->exclusions->excluded_paths) && strlen($this->settings->exclusions->excluded_paths)) {
            $urlParsed = parse_url($url);
            foreach (explode("\n", $this->settings->exclusions->excluded_paths) as $rule) {

                $rule = explode(':', $rule);
                if(count($rule) >= 2) {
                    $type = array_shift($rule);
                    $value = implode(':', $rule);
                    $value = trim($value); //remove whitespaces and especially the \r which gets added on Windows (most probably)

                    switch($type) {
                        case 'regex':
                            if(@preg_match($value, $url)) {
                                $this->logger->log("EXCLUDED by $type : $value");
                                return true;
                            }
                            break;
                        case 'path':
                        case 'http': //being so kind to accept urls as they are. :)
                        case 'https':
                            if(!isset($urlParsed['host'])) {
                                $valueParsed = parse_url($value);
                                $value = isset($valueParsed['path']) ? $valueParsed['path'] : false;
                            }
                            if(strpos($url, $value) !== false) {
                                $this->logger->log("EXCLUDED by $type : $value");
                                return true;
                            }
                            if(isset($urlParsed['path'])) {
                                preg_match("/(-[0-9]+x[0-9]+)\.([a-zA-Z0-9]+)$/", $urlParsed['path'], $matches);
                                //$this->logger->log("MATCHED THUMBNAIL for $url: ", $matches);
                                if(isset($matches[1]) && isset($matches[2])) {
                                    //try again without the resolution part, in order to exclude all thumbnails if main image is excluded
                                    $urlMain = str_replace($matches[1] . '.' . $matches[2], '.' . $matches[2], $url);
                                    //$this->logger->log("WILL REPLACE : {$matches[1]}.{$matches[2]} with .{$matches[2]} results: ", $urlMain);
                                    if($urlMain !== $url) {
                                        return $this->urlIsExcluded($urlMain);
                                    }
                                }
                            }
                            break;
                    }
                }
            }
        }
        //$this->logger->log("NOT EXCLUDED");
        return false;
    }

    /**
     * @return bool true if SPAI is welcome ( not welcome for example if it's an AMP page, CLI, is admin page or PageSpeed is off )
     */
	protected function isWelcome() {
		if ( isset( $_SERVER[ 'HTTP_REFERER' ] ) ) {
			$admin    = parse_url( admin_url() );
			$referrer = parse_url( $_SERVER[ 'HTTP_REFERER' ] );
			//don't act on pages being customized (wp-admin/customize.php)
			if ( isset( $referrer[ 'path' ] ) && ( $referrer[ 'path' ] === $admin[ 'path' ] . 'customize.php' || $referrer[ 'path' ] === $admin[ 'path' ] . 'post.php' ) ) {
				return false;
			}
			else if ( $this->doingAjax && $admin[ 'host' ] == $referrer[ 'host' ] && strpos( $referrer[ 'path' ], $admin[ 'path' ] ) === 0 ) {
				return false;
			}
		}
		$referrerPath = ( isset( $referrer[ 'path' ] ) ? $referrer[ 'path' ] : '' );

		return !( is_feed()
		          || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		          || ( defined( 'DOING_CRON' ) && DOING_CRON )
		          || ( defined( 'WP_CLI' ) && WP_CLI )
		          || ( isset( $_GET[ 'PageSpeed' ] ) && $_GET[ 'PageSpeed' ] == 'off' ) || strpos( $referrerPath, 'PageSpeed=off' )
		          || !!$this->options->get( 'missing_jquery', [ 'tests', 'front_end' ], false )
		          || isset( $_GET[ 'fl_builder' ] ) || strpos( $referrerPath, '/?fl_builder' ) // shh.... Beaver Builder is editing :)
		          || ( isset( $_GET[ 'tve' ] ) && $_GET[ 'tve' ] == 'true' ) // Thrive Architect editor (thrive-visual-editor/thrive-visual-editor.php)
		          || ( isset( $_GET[ 'ct_builder' ] ) && $_GET[ 'ct_builder' ] == 'true' ) // Oxygen Builder
		          || ( isset( $_GET[ 'oxygen_iframe' ] ) && $_GET[ 'oxygen_iframe' ] == 'true' ) // Oxygen Builder
		          || ( isset( $_REQUEST[ 'action' ] ) && in_array( $_REQUEST[ 'action' ], self::$excludedAjaxActions ) )
		          || ( is_admin() && function_exists( "is_user_logged_in" ) && is_user_logged_in()
		               && !$this->doingAjax ) );
	}

    public function recordAffectedTags($tags) {
        $affectedTags = get_option('spai_settings_lazy_ajax_tags', array());
        foreach($tags as $tag => $val) {
            if(!isset($affectedTags[$tag]) || ($affectedTags[$tag] !== $val)) {
                update_option('spai_settings_lazy_ajax_tags', $this->mergeTags($tags, $affectedTags));
                return;
            }
        }
    }

    public function getRecordedAffectedTags() {
        return get_option('spai_settings_lazy_ajax_tags', array());
    }

    public function getAllAffectedTags() {
        return $this->mergeTags($this->affectedTags, get_option('spai_settings_lazy_ajax_tags', array()));
    }

    protected function mergeTags($tags, $moreTags) {
        foreach($tags as $key => $val) {
            $moreTags[$key] |= $val;
        }
        return $moreTags;
    }
}
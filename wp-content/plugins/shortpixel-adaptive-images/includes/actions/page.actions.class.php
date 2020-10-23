<?php

	namespace ShortPixel\AI\Page;

	use ShortPixelAI;
	use ShortPixel\AI\Page;
	use ShortPixel\AI\Notice;
	use ShortPixel\AI\Options;
	use ShortPixel\AI\Converter;

	class Actions {
		/**
		 * Method handles the pages's actions
		 * Works via AJAX
		 */
		public static function handle() {
			$page = $_POST[ 'page' ];
			$data = $_POST[ 'data' ];

			$response = [ 'success' => false ];

			if ( !empty( $page ) && is_string( $page ) ) {
				$page = Converter::toTitleCase( $page );

				$response = call_user_func( [ 'self', 'handle' . $page ], isset( $data ) ? $data : null );
			}

			wp_send_json( $response );
		}

		private static function handleSettings( $data ) {
			// action which should be handled
			$action = isset( $data[ 'action' ] ) ? $data[ 'action' ] : null;

			$success = false;

			$response = [
				'success' => $success,
			];

			if ( $action === 'save' ) {
				$options         = json_decode( stripslashes( $data[ 'options' ] ) );
				$current_options = Options::_()->settings;

				if ( is_object( $options ) || is_array( $options ) ) {
					foreach ( $options as $category_name => $category ) {
						if ( is_object( $category ) || is_array( $category ) ) {
							$success = true;

							foreach ( $category as $option => $value ) {
								if ( $category_name === 'areas' && $option === 'parse_css_files' && $value === true && $current_options->areas->parse_css_files === false ) {
									ShortPixelAI::clear_css_cache();
								}

								Options::_()->set( $value, $option, [ 'settings', $category_name ] );
							}
						}
					}
				}

				$response[ 'success' ] = $success;
				$response[ 'notice' ]  = Notice::get( null, [
					'notice'  => [
						'type'        => $success ? 'success' : 'error',
						'dismissible' => true,
					],
					'message' => [
						'body' => [
							$success
								? __( 'The options have been successfully saved.', 'shortpixel-adaptive-images' )
								: __( 'Something went wrong...', 'shortpixel-adaptive-images' ) . ' 😓 ' . __( 'Options have not been saved.',
									'shortpixel-adaptive-images' ),
						],
					],
				] );
			}
			else if ( $action === 'save key' ) {
				if ( !empty( $data[ 'api_key' ] ) ) {
					if ( ShortPixelAI::_()->get_cdn_domain_usage( null, $data[ 'api_key' ] ) !== false ) {
						$response[ 'success' ] = !!Options::_()->set( $data[ 'api_key' ], 'api_key', [ 'settings', 'general' ] );
						$response[ 'reload' ]  = true;
					}
					else {
						$response[ 'success' ] = false;
						$response[ 'notice' ]  = Notice::get( null, [
							'notice'  => [
								'type'        => 'error',
								'dismissible' => true,
							],
							'message' => [
								'body' => [
									__( 'The domain is not associated to this API key.', 'shortpixel-adaptive-images' ) . ' 😓',
								],
							],
						] );
					}
				}
			}
			else if ( $action === 'remove key' ) {
				$response[ 'success' ] = !!Options::_()->delete( 'api_key', [ 'settings', 'general' ] );
				$response[ 'reload' ]  = true;
			}
			else if ( $action === 'clear css cache' ) {
				$success = !!ShortPixelAI::clear_css_cache();

				$response[ 'success' ] = $success;
				$response[ 'notice' ]  = Notice::get( null, [
					'notice'  => [
						'type'        => $success ? 'success' : 'error',
						'dismissible' => true,
					],
					'message' => [
						'body' => [
							$success
								? __( 'The CSS cache has been cleared.', 'shortpixel-adaptive-images' )
								: __( 'Something went wrong...', 'shortpixel-adaptive-images' ) . ' 😓 ' . __( 'Cache has not been cleared.',
									'shortpixel-adaptive-images' ),
						],
					],
				] );
			}

			return $response;
		}

		private static function handleFrontWorker( $data ) {
			// action which should be handled
			$action = isset( $data[ 'action' ] ) ? $data[ 'action' ] : null;

			// option to be handled
			$option = isset( $data[ 'option' ] ) ? $data[ 'option' ] : null;

			$response = [
				'success' => false,
			];

			switch ( $option ) {
				case 'lazy-load-backgrounds':
					Options::_()->settings_areas_backgroundsLazy = $data[ 'value' ] == 'true';
                    Options::_()->settings_areas_backgroundsLazyStyle = $data[ 'value' ] == 'true';
					$response[ 'success' ]                       = true;
					break;
				case 'parse-css':
					Options::_()->settings_areas_parseCssFiles = $data[ 'value' ] == 'true';
					if ( $data[ 'value' ] == 'true' ) {
						ShortPixelAI::clear_css_cache();
					}

					$response[ 'success' ] = true;
					break;
				case 'parse-js':
					Options::_()->settings_areas_parseJs = $data[ 'value' ] == 'true';
					$response[ 'success' ]               = true;
					break;
				case 'parse-json':
					Options::_()->settings_areas_parseJson = $data[ 'value' ] == 'true';
					$response[ 'success' ]                 = true;
					break;
				case 'hover-handling':
					Options::_()->settings_behaviour_hoverHandling = $data[ 'value' ] == 'true';
					$response[ 'success' ]                         = true;
					break;
			}

			if ( $action === 'done' ) {
				$front_worker = Options::_()->get( 'front_worker', [ 'pages', 'on_boarding' ], Options\Option::_() );
				$front_worker = $front_worker instanceof Options\Option ? $front_worker : Options\Option::_();

				$current_user_login = wp_get_current_user()->user_login;

				if ( !empty( $front_worker->{$current_user_login} ) ) {
					unset( $front_worker->{$current_user_login} );
				}

				$response[ 'success' ]  = !!Options::_()->set( $front_worker, 'front_worker', [ 'pages', 'on_boarding' ] );
				$response[ 'cookie' ]   = 'shortpixel-ai-front-worker';
				$response[ 'redirect' ] = [
					'allowed' => true,
					'url'     => admin_url( 'admin.php?page=' . Page::NAMES[ 'on-boarding' ] ),
				];
			}

			return $response;
		}

		private static function handleOnBoarding( $data ) {
			// action which should be handled
			$action = isset( $data[ 'action' ] ) ? $data[ 'action' ] : null;

			$response = [
				'success' => false,
			];

			$steps     = Constants::_( ShortPixelAI::_() )->onBoarding;
			$steps_qty = count( isset( $steps[ 'messages' ] ) ? $steps[ 'messages' ] : [] );
			$last_step = $steps_qty > 0 ? $steps_qty - 1 : 0;

			if ( $action === 'run front worker' ) {
				$current_user_login = wp_get_current_user()->ID;

				if ( empty( $current_user_login ) ) {
					return $response = [
						'success' => false,
						'notice'  => Notice::get( null, [
							'notice'  => [
								'type'        => 'error',
								'dismissible' => true,
							],
							'message' => [
								'body' => [
									__( 'It looks like you have logged out. Please <span>log in</span> and try again.', 'shortpixel-adaptive-images' ),
								],
							],
						] ),
					];
				}

				$front_worker = Options::_()->get( 'front_worker', [ 'pages', 'on_boarding' ], Options\Option::_() );
				$front_worker = $front_worker instanceof Options\Option ? $front_worker : Options\Option::_();

				$front_worker->{$current_user_login} = $front_worker->{$current_user_login} instanceof Options\Option ? $front_worker->{$current_user_login} : Options\Option::_();

				$front_worker->{$current_user_login}->enabled = true;
				$front_worker->{$current_user_login}->token   = ShortPixelAI::_()->get_user_token();

				return $response = [
					'success'  => !!Options::_()->set( $front_worker, 'front_worker', [ 'pages', 'on_boarding' ] ),
					'redirect' => [
						'allowed' => true,
						'url'     => home_url(),
					],
					'cookie'   => 'shortpixel-ai-front-worker',
				];
			}
			else if ( $action === 'go to settings' ) {
				return $response = [
					'success'  => !!Options::_()->set( $last_step, 'step', [ 'pages', 'on_boarding' ] ) && !!Options::_()->set( true, 'has_been_passed', [ 'pages', 'on_boarding' ] ),
					'redirect' => [
						'allowed' => true,
						'url'     => admin_url( 'options-general.php?page=' . Page::NAMES[ 'settings' ] ),
					],
				];
			}
			else if ( $action === 'save key' ) {
				if ( !empty( $data[ 'api_key' ] ) ) {
					if ( ShortPixelAI::_()->get_cdn_domain_usage( null, $data[ 'api_key' ] ) !== false ) {
						$response[ 'success' ] = !!Options::_()->set( $data[ 'api_key' ], 'api_key', [ 'settings', 'general' ] );
						$response[ 'notice' ]  = Notice::get( null, [
							'notice'  => [
								'type'        => 'success',
								'dismissible' => true,
							],
							'message' => [
								'body' => [
									__( 'API key has been successfully saved.', 'shortpixel-adaptive-images' ) . ' 😅',
								],
							],
						] );
						$response[ 'reload' ]  = true;
					}
					else {
						$response[ 'success' ] = false;
						$response[ 'notice' ]  = Notice::get( null, [
							'notice'  => [
								'type'        => 'error',
								'dismissible' => true,
							],
							'message' => [
								'body' => [
									sprintf( __( '<strong>%s</strong> is not associated to this API key.', 'shortpixel-adaptive-images' ), ShortPixelAI::_()->get_site_domain() ) . ' 😓',
								],
							],
						] );
					}

					return $response;
				}
			}
			else if ( $action === 'use same account' ) {
				$success = ShortPixelAI::_()->use_shortpixel_account();

				$response[ 'success' ] = $success;
				$response[ 'reload' ]  = $success;
				$response[ 'notice' ]  = $success
					? Notice::get( null, [
						'notice'  => [
							'type'        => 'success',
							'dismissible' => true,
						],
						'message' => [
							'body' => [
								sprintf( __( 'API key has been successfully saved. <strong>ShortPixel Adaptive Images</strong> will use the same account for <strong>%s</strong>.', 'shortpixel-adaptive-images' ), ShortPixelAI::_()->get_site_domain() ) . ' 😅',
							],
						],
					] )
					: Notice::get( null, [
						'notice'  => [
							'type'        => 'error',
							'dismissible' => true,
						],
						'message' => [
							'body' => [
								__( 'Something went wrong... API key has not been added.', 'shortpixel-adaptive-images' ) . ' 😓',
							],
						],
					] );

				return $response;
			}

			if ( function_exists( 'get_option' ) && function_exists( 'update_option' ) ) {
				$next_step = (int) $data[ 'step' ] + 1;

				$limited_next_step = $next_step >= $steps_qty ? $steps_qty - 1 : $next_step;

				// updating the current step
				Options::_()->pages_onBoarding_step = $limited_next_step;

				$response[ 'success' ] = true;

				if ( $next_step >= $steps_qty ) {
					// set flag that on-boarding has been passed
					Options::_()->pages_onBoarding_hasBeenPassed = true;

					$response[ 'redirect' ] = [
						'allowed' => true,
						'url'     => admin_url( 'options-general.php?page=' . Page::NAMES[ 'settings' ] ),
					];
				}
				else {
					$response[ 'message' ] = empty( $steps[ 'messages' ][ $limited_next_step ] ) ? false : $steps[ 'messages' ][ $limited_next_step ];
				}
			}

			return $response;
		}
	}
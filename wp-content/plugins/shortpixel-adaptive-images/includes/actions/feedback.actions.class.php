<?php

	namespace ShortPixel\AI\Feedback;

	use ShortPixelAI;
	use ShortPixel\AI\Options;
	use ShortPixel\AI\Feedback;
	use ShortPixel\AI\Converter;

	class Actions {
		/**
		 * Method handles the feedback's actions
		 * Works via AJAX
		 */
		public static function handle() {
			if ( ShortPixelAI::isAjax() ) {
				$data   = $_POST[ 'data' ];
				$action = $data[ 'action' ];

				if ( !empty( $action ) && is_string( $action ) ) {
					$action = ( Converter::toTitleCase( $action ) );

					// unset action
					unset( $data[ 'action' ] );

					$response = call_user_func( [ 'self', 'handle' . $action ], $data );
				}

				if ( !isset( $response ) ) {
					$response = [ 'success' => false ];
				}

				wp_send_json( $response );
			}
		}

		/**
		 * Would be called by self::handle() method
		 *
		 * @param array $data
		 *
		 * @return array
		 */
		private static function handleDeactivation( $data ) {
			$response = [ 'success' => false ];

			if ( isset( $data[ 'feedback' ] ) ) {
				$feedback    = $data[ 'feedback' ];
				$suggestions = isset( $data[ 'suggestions' ] ) ? $data[ 'suggestions' ] : null;
				$anonymous   = isset( $data[ 'anonymous' ] ) ? !!$data[ 'anonymous' ] : false;

				$response[ 'feedback' ] = Feedback::send( $feedback, $suggestions, $anonymous );
			}

			if ( isset( $data[ 'settings' ] ) ) {
				switch ( $data[ 'settings' ] ) {
					case 'remove' :
						$response[ 'success' ] = !!Options::_()->clearCollection();

						break;
					case 'revert' :
						ShortPixelAI::revert_options();
						$response[ 'success' ] = true;

						break;
					default:
						$response[ 'success' ] = true;

						break;
				}

				deactivate_plugins( ShortPixelAI::_()->basename );

				$response[ 'reload' ] = [
					'allowed' => true,
				];
			}

			return $response;
		}
	}
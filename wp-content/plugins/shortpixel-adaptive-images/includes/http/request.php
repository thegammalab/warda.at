<?php

	namespace ShortPixel\AI;

	class Request {
		private static $arguments = [ 'timeout' => 120, 'httpversion' => '1.1' ];

		public static function get( $url, $arguments = null ) {
			$arguments = is_array( $arguments ) && !empty( $arguments ) ? $arguments : self::$arguments;
			$arguments = array_merge( $arguments, [ 'method' => 'GET' ] );

			return self::request( $url, $arguments );
		}

		public static function post( $url, $arguments = null ) {
			$arguments = is_array( $arguments ) && !empty( $arguments ) ? $arguments : self::$arguments;
			$arguments = array_merge( $arguments, [ 'method' => 'POST' ] );

			return self::request( $url, $arguments );
		}

		private static function request( $url, $arguments ) {
			$arguments = is_array( $arguments ) && !empty( $arguments ) ? array_merge( self::$arguments, $arguments ) : self::$arguments;

			$response = wp_safe_remote_request( $url, $arguments );

			if ( is_wp_error( $response ) ) {
				return '';
			}

			if ( !isset( $response[ 'response' ] ) || !is_array( $response[ 'response' ] ) ) {
				return '';
			}

			if ( !isset( $response[ 'body' ] ) ) {
				return '';
			}

			return json_decode( $response[ 'body' ] );
		}
	}
<?php

namespace BHS\Client;

/**
 * General client for BHS Storehouse API.
 *
 * @since 1.0.0
 */
class APIClient {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->api_base = App::get_api_base();
	}

	/**
	 * Fetch by an identifier.
	 *
	 * Assumes the BHS URL base.
	 *
	 * @since 1.0.0
	 *
	 * @param string $identifier
	 * @return array|WP_Error Error object on failure.
	 */
	public function fetch_by_identifier( $identifier ) {
		$url = $this->api_base . 'record/' . urlencode( $identifier );
		$result = wp_remote_get( $url );
		$status = wp_remote_retrieve_response_code( $result );

		if ( 200 != $status ) {
			return new \WP_Error( 'bhsc_no_remote_record_found', __( 'No remote record found by that identifier', 'bhs-client' ), $identifier );
		}

		$body = json_decode( wp_remote_retrieve_body( $result ) );
		return (array) $body;
	}
}

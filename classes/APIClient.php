<?php

namespace BHS\Client;

class APIClient {
	public function __construct() {
		$this->api_base = App::get_api_base();
	}

	/**
	 * Fetch by an identifier.
	 *
	 * Assumes the BHS URL base.
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

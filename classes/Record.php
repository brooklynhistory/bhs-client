<?php

namespace BHS\Client;

class Record {
	protected $identifier;
	protected $record_data;

	/**
	 * Constructor.
	 *
	 * @param string $optional Identifier.
	 */
	public function __construct( $identifier = '' ) {
		if ( $identifier ) {
			$this->identifier = $identifier;
		}
	}

	/**
	 * Whether the record could be located.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exists() {
		$record_data = $this->get_record_data();

		if ( is_wp_error( $record_data ) ) {
			return false;
		}

		return true;
	}

	public function get_record_data( $fields = 'all' ) {
		// Look for cached version.
		$transient_key = $this->get_transient_key( $this->identifier );
		$data = get_transient( $transient_key );
		if ( ! $data ) {

			// If not found, fetch.
			$client = new APIClient();
			$data = $client->fetch_by_identifier( $this->identifier );

			// Errors should not be cached.
			if ( is_wp_error( $data ) ) {
				return $data;
			}

			set_transient( $transient_key, $data, DAY_IN_SECONDS );
			_b( 'Uncached!' );
		} else {
			_b( 'Cached!' );
		}

		if ( 'all' !== $fields ) {
			$_data = array();
			$_fields = explode( ',', $fields );
			foreach ( $_fields as $field ) {
				$_data[ $field ] = isset( $data[ $field ] ) ? $data[ $field ] : '';
			}
			$data = $_data;
		}

		return $data;
	}

	/**
	 * Get the transient key for a given identifier.
	 *
	 * The transient key is built from an incrementor. md5() is used to ensure
	 * no spaces in the key, as well as to ensure we're under the character length.
	 *
	 * @param string $identifier
	 * @return string
	 */
	protected function get_transient_key( $identifier ) {
		$incrementor = $this->get_transient_incrementor();
		$hashed = md5( $identifier );
		$key = sprintf( 'bhs_record_%s_%s', $hashed, $incrementor );

		return $key;
	}

	/**
	 * Get the currently saved incrementor for transient keys.
	 *
	 * Will create one if it doesn't exist.
	 *
	 * Transient keys are valid for up to 24 hours.
	 *
	 * @return string
	 */
	protected function get_transient_incrementor() {
		$incrementor = get_transient( 'bhs_record_incrementor' );

		if ( ! $incrementor ) {
			$incrementor = microtime();
			set_transient( 'bhs_record_incrementor', $incrementor, DAY_IN_SECONDS );
		}

		return $incrementor;
	}
}

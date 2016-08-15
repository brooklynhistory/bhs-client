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

	public function get_record_data() {
		// Already fetched on this pageload.
		if ( null !== $this->record_data ) {
			return $this->record_data;
		}

		// Look for cached version.

		// If not found, fetch.
		$client = new APIClient();
		$item = $client->fetch_by_identifier( $this->identifier );

		if ( is_wp_error( $item ) ) {
			$this->record_data = $item;
			return $item;
		}

		// @todo Cache
		$this->record_data = $item;
		return $this->record_data;
	}
}

<?php

namespace BHS\Client;

/**
 * Get a BHS record.
 *
 * This class is the main tool that should be used to access BHS records.
 * It contains two caching layers (one per-pageload, and one stored in the
 * transient cache). It also allows `foreach()` looping, so it can be used
 * easily in the context of a PHP WP template file:
 *
 *   $record = new \BHS\Client\Record( $identifier );
 *   foreach ( $record as $field ) {
 *       echo "Field Name: " . $field->get_the_label() . "<br />";
 *       echo "Field Value: " . $field->get_the_value() . "<br />";
 *   }
 */
class Record implements \IteratorAggregate {
	protected $identifier;
	protected $field_data;

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

	/**
	 * Get record data.
	 *
	 * @param string $fields Comma-separated list of field names, or 'all' to get
	 *                       all fields. Fields will be returned in the order in
	 *                       which they're specified.
	 * @return array Raw record data.
	 */
	public function get_record_data( $fields = 'all' ) {
		$data = null;

		// Look for cached version.
		if ( null !== $this->field_data ) {
			$data = $this->field_data;
		}

		if ( ! $data ) {
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
			}
		}

		$this->field_data = $data;

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
	 * Get a RecordField object for a given field.
	 *
	 * @param string $field_name
	 * @return \BHS\Client\RecordField|null Null on failure.
	 */
	public function get_field( $field_name ) {
		$this->populate();

		if ( ! isset( $this->field_data[ $field_name ] ) ) {
			return null;
		}

		$field_value = $this->field_data[ $field_name ];

		return new RecordField( $field_name, $field_value );
	}

	/**
	 * Populate the object.
	 *
	 * Used to fill the aggregator on-demand.
	 */
	protected function populate() {
		if ( null === $this->field_data && ! empty( $this->identifier ) ) {
			$this->get_record_data();
		}
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

	/**
	 * Get the iterator to be used by IteratorAggregate.
	 *
	 * The data array is an array of RecordField objects corresponding to the
	 * requested record.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		$data = $this->get_record_data();
		$items = array();
		foreach ( $data as $k => $v ) {
			$items[] = new RecordField( $k, $v );
		}
		$i = new \ArrayIterator( $items );
		return $i;
	}
}

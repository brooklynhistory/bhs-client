<?php

namespace BHS\Client;

/**
 * Record field class.
 *
 * @since 1.0.0
 */
class RecordField {
	protected $key;
	protected $value;
	protected $label;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function __construct( $key, $value ) {
		$this->key = $key;
		$this->value = $value;
		$this->label = $this->get_field_label( $key );
	}

	/**
	 * Getter.
	 *
	 * Provides magic access to 'key', 'value', and 'label'.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		$v = '';

		switch ( $key ) {
			case 'key' :
			case 'value' :
			case 'label' :
				if ( isset( $this->{$key} ) ) {
					$v = $this->{$key};
				}

			break;

			default :
			break;
		}

		return $v;
	}

	/**
	 * Get an echo-ready label for the current field.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_the_label() {
		return esc_html( $this->label );
	}

	/**
	 * Get an echo-ready version of the value.
	 *
	 * Arrays will be collapsed according to `$separator`.
	 *
	 * @param string $separator String to be used when collapsing arrays. Default '<br />'.
	 * @return string
	 */
	public function get_the_value( $separator = '<br />' ) {
		$items = array();
		foreach ( (array) $this->value as $v ) {
			if ( ! is_scalar( $v ) ) {
				continue;
			}
			$items[] = esc_html( $v );
		}

		return implode( $separator, $items );
	}

	/**
	 * Get the label belonging to a field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @return string
	 */
	protected function get_field_label( $key ) {
		$map = $this->get_field_map();
		if ( ! isset( $map[ $key ] ) ) {
			return '';
		}

		$f = $map[ $key ];
		return $f['label'];
	}

	/**
	 * Get a field mapping.
	 *
	 * Used to associate keys with labels.
	 *
	 * @return array
	 */
	protected function get_field_map() {
		return array(
			'contributor' => array( 'label' => __( 'Contributor', 'bhs-client' ) ),
			'coverage' => array( 'label' => __( 'Coverage', 'bhs-client' ) ),
			'coverage_GIS' => array( 'label' => __( 'GIS Boundaries', 'bhs-client' ) ),
			'creator' => array( 'label' => __( 'Creator', 'bhs-client' ) ),
			'date' => array( 'label' => __( 'Date', 'bhs-client' ) ),
			'description' => array( 'label' => __( 'Description', 'bhs-client' ) ),
			'format' => array( 'label' => __( 'Format', 'bhs-client' ) ),
			'format_scale' => array( 'label' => __( 'Format - Scale', 'bhs-client' ) ),
			'format_size' => array( 'label' => __( 'Format - Size', 'bhs-client' ) ),
			'identifier' => array( 'label' => __( 'Identifier', 'bhs-client' ) ),
			'language' => array( 'label' => __( 'Language', 'bhs-client' ) ),
			'publisher' => array( 'label' => __( 'Publisher', 'bhs-client' ) ),
			'rights' => array( 'label' => __( 'Rights', 'bhs-client' ) ),
			'source' => array( 'label' => __( 'Source', 'bhs-client' ) ),
			'subject' => array( 'label' => __( 'Subject', 'bhs-client' ) ),
			'title' => array( 'label' => __( 'Title', 'bhs-client' ) ),
			'type' => array( 'label' => __( 'Type', 'bhs-client' ) ),
		);
	}
}

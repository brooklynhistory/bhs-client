<?php

namespace BHS\Client;

class RecordField {
	protected $key;
	protected $value;
	protected $label;

	public function __construct( $key, $value ) {
		$this->key = $key;
		$this->value = $value;
		$this->label = $this->get_field_label( $key );
	}

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

	public function get_the_label() {
		return esc_html( $this->label );
	}

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

	protected function get_field_label( $key ) {
		$map = $this->get_field_map();
		if ( ! isset( $map[ $key ] ) ) {
			return '';
		}

		$f = $map[ $key ];
		return $f['label'];
	}

	protected function get_field_map() {
		return array(
			'contributor' => array( 'label' => __( 'Contributor', 'bhs-client' ) ),
			'coverage' => array( 'label' => __( 'Coverage', 'bhs-client' ) ),
			'creator' => array( 'label' => __( 'Creator', 'bhs-client' ) ),
			'date' => array( 'label' => __( 'Date', 'bhs-client' ) ),
			'description' => array( 'label' => __( 'Description', 'bhs-client' ) ),
			'format' => array( 'label' => __( 'Format', 'bhs-client' ) ),
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

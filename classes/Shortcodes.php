<?php

namespace BHS\Client;

class Shortcodes {
	public function set_up_hooks() {
		add_shortcode( 'bhs_record', array( $this, 'bhs_record_shortcode' ) );
	}

	public function bhs_record_shortcode( $atts ) {
		$markup = '';

		if ( ! isset( $atts['identifier'] ) ) {
			if ( get_the_ID() && current_user_can( 'edit_post', get_the_ID() ) ) {
				return '<p>' . __( 'The <code>bhs_record</code> shortcode requires an <code>identifier</code> attribute.', 'bhs-client' ) . '</p>';
			} else {
				return $markup;
			}
		}

		$identifier = $atts['identifier'];
		$r = array_merge( array(
			'hide_empty' => true,
			'fields' => 'all',
		), $atts );

		$record = new Record( $identifier );
		$hide_empty = (bool) $r['hide_empty'];

		if ( ! $record->exists() ) {
			if ( get_the_ID() && current_user_can( 'edit_post', get_the_ID() ) ) {
				return '<p>' . sprintf( __( 'No record found using the <code>identifier</code> "%s".', 'bhs-client' ), esc_html( $atts['identifier'] ) ) . '</p>';
			} else {
				return $markup;
			}
		}

		$data = $record->get_record_data( $r['fields'] );

		// Should maybe move rendering to Record object.
		$markup .= '<ul class="bhs-record-data">';
		foreach ( $data as $key => $value ) {
			if ( $hide_empty && empty( $value ) ) {
				continue;
			}

			$values = array();
			foreach ( (array) $value as $single_value ) {
				// skip multi-d arrays for now - should be excluded in most cases.
				if ( ! is_array( $single_value  ) ) {
					$values[] = esc_html( $single_value );
				}
			}

			$value_html = implode( '<br />', $values );

			$markup .= sprintf(
				'<li class="bhs-field-%s"><div class="bhs-field-name">%s</div><div class="bhs-field-value">%s</div></li>',
				sanitize_title( $key ),
				esc_html( ucwords( $key ) ),
				$value_html
			);
		}
		$markup .= '</ul>';

		return $markup;
	}
}

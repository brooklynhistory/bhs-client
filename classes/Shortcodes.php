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

		$record = new Record( $atts['identifier'] );

		if ( ! $record->exists() ) {
			if ( get_the_ID() && current_user_can( 'edit_post', get_the_ID() ) ) {
				return '<p>' . sprintf( __( 'No record found using the <code>identifier</code> "%s".', 'bhs-client' ), esc_html( $atts['identifier'] ) ) . '</p>';
			} else {
				return $markup;
			}
		}

		$data = $record->get_record_data();

		// Should maybe move rendering to Record object.
		foreach ( $data as $key => $value ) {
			$label = sprintf(
				'<strong>%s</strong>',
				esc_html( ucwords( $key ) )
			);

			$values = array();
			foreach ( $value as $single_value ) {
				// skip multi-d arrays for now - should be excluded in most cases.
				if ( ! is_array( $single_value  ) ) {
					$values[] = esc_html( $single_value );
				}
			}

			$value_html = implode( '<br />', $values );

			$markup .= $label . '<br />' . $value_html . '<br /><br />';
		}

		return $markup;

		// record identifier
		// fields
	}
}

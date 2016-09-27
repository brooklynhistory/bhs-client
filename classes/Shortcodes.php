<?php

namespace BHS\Client;

/**
 * Shortcode setup.
 *
 * @since 1.0.0
 */
class Shortcodes {
	/**
	 * Hook into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function set_up_hooks() {
		add_shortcode( 'bhs_record', array( $this, 'bhs_record_shortcode' ) );
	}

	/**
	 * Handler for 'bhs_record' shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 */
	public function bhs_record_shortcode( $atts ) {
		wp_enqueue_style( 'bhs-client', plugins_url() . '/bhs-client/assets/css/client.css' );

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

		// Special case - hardcoded for now.
		$skip = array( 'relation' );

		$element_labels = App::get_element_labels();

		// Should maybe move rendering to Record object.
		$markup .= '<ul class="bhs-record-data">';
		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $skip, true ) ) {
				continue;
			}

			$values = array();
			if ( ! empty( $value ) ) {
				foreach ( (array) $value as $single_value ) {
					// skip multi-d arrays for now - should be excluded in most cases.
					if ( ! is_array( $single_value  ) ) {
						$values[] = esc_html( $single_value );
					}
				}
			}

			if ( $hide_empty && empty( $values ) ) {
				continue;
			}

			$value_html = implode( '<br />', $values );

			$label = isset( $element_labels[ $key ] ) ? $element_labels[ $key ] : ucwords( $key );

			$markup .= sprintf(
				'<li class="bhs-field-%s"><div class="bhs-field-name">%s</div><div class="bhs-field-value">%s</div></li>',
				sanitize_title( $key ),
				esc_html( $label ),
				$value_html
			);
		}
		$markup .= '</ul>';

		return $markup;
	}
}

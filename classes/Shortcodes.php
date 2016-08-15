<?php

namespace BHS\Client;

class Shortcodes {
	public function set_up_hooks() {
		add_shortcode( 'bhs_record', array( $this, 'bhs_record_shortcode' ) );
	}

	public function bhs_record_shortcode( $atts ) {

	}
}

<?php

namespace BHS\Client;

class App {
	public static function init() {
		$shortcodes = new Shortcodes();
		$shortcodes->set_up_hooks();

		add_action( 'save_post', array( __CLASS__, 'cache_increment' ) );
	}

	/**
	 * Bump cache incrementor.
	 *
	 * Fired every time a post is saved.
	 *
	 * @since 1.0.0
	 */
	public static function cache_increment() {
		delete_transient( 'bhs_record_incrementor' );
	}
}

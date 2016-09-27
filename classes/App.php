<?php

namespace BHS\Client;

/**
 * Main application class.
 *
 * @since 1.0.0
 */
class App {
	/**
	 * Initialize application state.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$shortcodes = new Shortcodes();
		$shortcodes->set_up_hooks();

		$admin = new Admin();
		$admin->set_up_hooks();

		add_action( 'save_post', array( __CLASS__, 'cache_increment' ) );

		add_action( 'wp_head', array( __CLASS__, 'print_settings_for_js' ), 5 );
		add_action( 'admin_head', array( __CLASS__, 'print_settings_for_js' ), 5 );
	}

	/**
	 * Get the API base URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_api_base() {
		$default = 'http://assets.brooklynhistory.org/wp-json/bhs/v1/';

		if ( defined( 'BHS_API_BASE' ) ) {
			$base = BHS_API_BASE;
		} else {
			$base = get_option( 'bhs_api_base', $default );
		}

		return trailingslashit( $base );
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

	/**
	 * Print BHS_Client_Settings JS object, so that scripts have access to settings.
	 *
	 * @since 1.0.0
	 */
	public static function print_settings_for_js() {
		static $printed = false;

		if ( $printed ) {
			return;
		}

		$settings = array(
			'api_base' => self::get_api_base()
		);

		$to_print = array();
		foreach ( $settings as $key => $value ) {
			$to_print[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}

		$script = 'var BHS_Client_Settings = ' . wp_json_encode( $to_print ) . ';';

		echo '<script type="text/javascript">' . $script . '</script>';

		$printed = true;
	}

	public static function get_element_labels() {
		return array(
			'contributor' => __( 'Contributor', 'bhs-client' ),
			'coverage' => __( 'Coverage', 'bhs-client' ),
			'creator' => __( 'Creator', 'bhs-client' ),
			'creator_alpha' => __( 'Creator (Alphabetical)', 'bhs-client' ),
			'date' => __( 'Date', 'bhs-client' ),
			'description' => __( 'Description', 'bhs-client' ),
			'format' => __( 'Format', 'bhs-client' ),
			'identifier' => __( 'Identifier', 'bhs-client' ),
			'language' => __( 'Language', 'bhs-client' ),
			'publisher' => __( 'Publisher', 'bhs-client' ),
			'relation_findingaid' => __( 'Finding Aid', 'bhs-client' ),
			'relation_ohms' => __( 'OHMS File', 'bhs-client' ),
			'relation_image' => __( 'Image', 'bhs-client' ),
			'relation_attachment' => __( 'Attachment', 'bhs-client' ),
			'rights' => __( 'Rights', 'bhs-client' ),
			'rights_request' => __( 'Rights Request', 'bhs-client' ),
			'source' => __( 'Source', 'bhs-client' ),
			'subject_people' => __( 'People', 'bhs-client' ),
			'subject_subject' => __( 'Subjects', 'bhs-client' ),
			'subject_places' => __( 'Places', 'bhs-client' ),
			'title_collection' => __( 'Collection', 'bhs-client' ),
			'title_title' => __( 'Title', 'bhs-client' ),
			'title_accession' => __( 'Accession', 'bhs-client' ),
			'type' => __( 'Type', 'bhs-client' ),
		);
	}
}

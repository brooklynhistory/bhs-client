<?php

namespace BHS\Client;

/**
 * Admin integration.
 *
 * @since 1.0.0
 */
class Admin {
	/**
	 * Hook into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function set_up_hooks() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register admin menus.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu() {
		add_options_page(
			__( 'BHS Client', 'bhs-client' ),
			__( 'BHS Client', 'bhs-client' ),
			'manage_options',
			'bhs-client-settings',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @since 1.0.0
	 */
	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'BHS Client Settings', 'bhs-client' ); ?></h1>

			<form method="post" action="options.php">
			<?php
				settings_fields( 'bhs_client' );
				do_settings_sections( 'bhs-client-settings' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	public function register_settings() {
		register_setting(
			'bhs_client',
			'bhs_api_base',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'bhs_client_settings',
			__( 'API Settings', 'bhs-client' ),
			array( $this, 'section_header' ),
			'bhs-client-settings'
		);

		add_settings_field(
			'bhs_api_base', // ID
			__( 'API Base URL' ), // Title
			array( $this, 'api_base_callback' ), // Callback
			'bhs-client-settings', // Page
			'bhs_client_settings' // Section
		);
	}

	public function sanitize( $settings ) {
		return $settings;
	}

	public function section_header() {}

	public function api_base_callback() {
		$base = App::get_api_base();

		$disabled = $disabled_message = '';
		if ( defined( 'BHS_API_BASE' ) ) {
			$disabled = 'disabled="disabled"';
			$disabled_message = sprintf( esc_html__( 'You have defined %s in wp-config.php, so it cannot be set on this page.', 'bhs-client' ), '<code>BHS_API_BASE</code>' );
		}

		printf(
			'<input %s type="text" size="100" id="bhs-api-base" name="bhs_api_base" value="%s" />',
			$disabled,
			esc_attr( $base )
		);

		printf(
			'<p class="description">%s</p>',
			__( 'The API Base is used by the plugin to generate URLs of remote records. If your Storehouse is located at <code>https://example.com/</code>, then your API Base will look something like <code>https://example.com/wp-json/bhs/v1/</code>.' )
		);

		if ( $disabled_message ) {
			printf(
				'<p class="description"><strong>%s</strong></p>',
				$disabled_message
			);
		}
	}
}

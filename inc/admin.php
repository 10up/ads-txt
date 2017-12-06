<?php

namespace AdsTxt;

/**
 * Add admin menu page
 * @return void
 */
function admin_menu() {
	add_options_page( __( 'Ads.txt', 'adstxt' ), __( 'Ads.txt', 'adstxt' ), 'manage_options', 'adstxt-settings', __NAMESPACE__ . '\settings_screen' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );

/**
 * Output the settings screen
 * @return void
 */
function settings_screen() {
	$setting = get_option( 'adstxt' );
?>

<div class="wrap">
	<h2><?php _e( 'Ads.txt', 'adstxt' ); ?></h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'adstxt' ); ?>

		<p class="description"><?php _e( 'COPY: Ads.txt is a root-level file, etc.', 'adstxt' ); ?></p>

		<textarea class="widefat" rows="25" name="adstxt"><?php echo esc_textarea( $setting ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
		</p>
	</form>
</div>

<?php
}

/**
 * Register setting
 * @return void
 */
function admin_init() {
	$args = array(
		'type' => 'string',
		'description' => 'Contents of ads.txt',
		'sanitize_callback' =>  __NAMESPACE__ . '\sanitize_setting'
	);

	register_setting( 'adstxt', 'adstxt', $args );
}
add_action( 'admin_init', __NAMESPACE__ . '\admin_init' );

/**
 * Sanitize setting for saving
 * @param  array $setting Posted settings
 * @return array          Sanitized settings
 */
function sanitize_setting( $setting ) {
	// Different browsers use different line endings
	$lines = preg_split( '/\r\n|\r|\n/', $setting );
	$sanitized = $errors = array();

	foreach ( $lines as $i => $line ) {
		$line_number = $i + 1;

		if ( empty( $line ) ) {
			$sanitized[] = '';
		} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment
			$sanitized[] = sanitize_textarea_field( $line );
		} elseif( 1 < strpos( $line, '=' ) ) { // This is a variable declaration
			// The spec currently supports CONTACT and SUBDOMAIN
			if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'warning',
					'message' => __( 'Unrecognized variable', 'adstxt' ),
				);

				// Because the spec can change we don't comment out invalid-looking lines
				$sanitized[] = sanitize_textarea_field( $line );
			} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains
				// Discard any comments
				$subdomain = explode( '#', $line );
				$subdomain = $subdomain[0];

				$subdomain = explode( '=', $subdomain );
				array_shift( $subdomain );

				// If there's anything other than one piece left something's not right
				if ( 1 !== count( $subdomain ) ) {
					$errors[] = array(
						'line' => $line_number,
						'type' => 'warning',
						'message' => __( 'Invalid subdomain', 'adstxt' ),
					);

					// Comment this out
					$sanitized[] = '# ' . sanitize_textarea_field( $line );
				} else {
					$subdomain = $subdomain[0];
					// YOU ARE HERE - YOU WANT TO CHECK ON THE FORMATION OF THIS SUBDOMAIN
					// if not good, add a warning - probably fine to leave it there, just a crawler problem
				}
			} else {
				$sanitized[] = sanitize_textarea_field( $line );
			}

			unset( $subdomain );
		} else { // Data records: the most common
			// Discard any comments
			$record = explode( '#', $line );
			$record = $record[0];

			// Relatively strict matching: domain, pub ID (alphanumeric with dashes), account type (current spec is RESELLER or DIRECT), and TAG-ID (alphanumeric, seems to be a hash)
			if ( preg_match( '/([^\s,]*), ?([A-Z0-9-]*), ?([A-Z]*),? ?([A-Z0-9-]*)? ?$/i', $record, $matches ) ) {
				if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $matches[3] ) ) {
					$errors[] = array(
						'line' => $line_number,
						'type' => 'error',
						'message' => __( 'Third field should be RESELLER or DIRECT', 'adstxt' ),
					);
				}

				// TODO: CHECK IF FIELD 1 IS A DOMAIN

				$sanitized[] = sanitize_textarea_field( $line );
			} else {
				// Not a comment, variable declaration, or data record; therefore, invalid.
				// Comment it out for safety.
				// WARNING: this could get cached by crawlers and take time to clear. This is just the PHP fallback.
				$sanitized[] = '# ' . sanitize_textarea_field( $line );

				$errors[] = array(
					'line' => $line_number,
					'type' => 'error',
					'message' => __( 'Invalid record; commented out', 'adstxt' ),
				);
			}

			unset( $record );
		}
	}

	if( ! empty( $errors ) ) {
		// Settings errors are wrapped in strong tags wrapped in a paragraph so our formatting options are limited
		$error_message = __( 'Your Ads.txt file was saved but contains the following problems:', 'adstxt' ) . '<br><br>';

		foreach ( $errors as $error ) {
			$error_message .= '<span class="' . $error['type'] . '">';

			/* translators: Error message output. 1: Line number, 2: Error message */
			$error_message .= sprintf(
				__( 'Line %1$s: %2$s', 'adstxt' ),
				$error['line'],
				$error['message']
			);

			$error_message .= '</span><br>';
		}

		$error_message .= '</ul>';

		add_settings_error(
			'adstxt',
			'adstxt_errors',
			$error_message,
			'error'
		);
	}

	$sanitized = implode( PHP_EOL, $sanitized );
	return $sanitized;
}

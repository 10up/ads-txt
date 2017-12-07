<?php

namespace Adstxt;

function admin_post() {
	current_user_can( 'customize' ) || die;
	check_admin_referer( 'adstxt_save' );
	$_post = stripslashes_deep( $_POST );

	// Different browsers use different line endings
	$lines = preg_split( '/\r\n|\r|\n/', $_post['adstxt'] );
	$sanitized = $errors = array();

// TODO: ARRAY_MAP THIS THING
	foreach ( $lines as $i => $line ) {
		$line_number = $i + 1;
		$result = validate_line( $line, $line_number );

		$sanitized[] = $result['sanitized'];
		if ( ! empty( $result['errors'] ) ) {
			$errors += $result['errors'];
		}
	}

	if( ! empty( $errors ) ) {
// maybe store errors in post meta to display on the settings screen?
// note to self: would want to delete this meta at the top of the save routine
	}

	$sanitized = implode( PHP_EOL, $sanitized );

// here is where you'd want to update the post with $sanitized as post_content - anything need doing for revisions?

	wp_redirect( $_POST['_wp_http_referer'] . '&updated=true' );
	exit;
}
add_action( 'admin_post_adstxt-save', __NAMESPACE__ . '\admin_post' );

function validate_line( $line, $line_number ) {
	$errors = array();

	if ( empty( $line ) ) {
		$sanitized = '';
	} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment
		$sanitized = sanitize_textarea_field( $line );
	} elseif( 1 < strpos( $line, '=' ) ) { // This is a variable declaration
		// The spec currently supports CONTACT and SUBDOMAIN
		if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
			$errors[] = array(
				'line' => $line_number,
				'type' => 'warning',
				'message' => __( 'Unrecognized variable', 'adstxt' ),
			);

			// Because the spec can change we don't comment out invalid-looking lines
			$sanitized = sanitize_textarea_field( $line );
		} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains
			// Discard any comments
			// @TODO: regex group this instead
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
				$sanitized = '# ' . sanitize_textarea_field( $line );
			} else {
				$subdomain = $subdomain[0];
				// YOU ARE HERE - YOU WANT TO CHECK ON THE FORMATION OF THIS SUBDOMAIN
				// if not good, add a warning - probably fine to leave it there, just a crawler problem
			}
		} else {
			$sanitized = sanitize_textarea_field( $line );
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

			$sanitized = sanitize_textarea_field( $line );
		} else {
			// Not a comment, variable declaration, or data record; therefore, invalid.
			// Comment it out for safety.
			// WARNING: this could get cached by crawlers and take time to clear. This is just the PHP fallback.
			$sanitized = '# ' . sanitize_textarea_field( $line );

			$errors[] = array(
				'line' => $line_number,
				'type' => 'error',
				'message' => __( 'Invalid record; commented out', 'adstxt' ),
			);
		}

		unset( $record );
	}

	return array(
		'sanitized' => $sanitized,
		'errors' => $errors
	);
}

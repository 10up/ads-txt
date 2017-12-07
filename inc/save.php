<?php

namespace Adstxt;

function admin_post() {
	current_user_can( 'customize' ) || die;
	check_admin_referer( 'adstxt_save' );
	$_post = stripslashes_deep( $_POST );

	$post_id = $_post['post_id'];

	// Different browsers use different line endings
	$lines = preg_split( '/\r\n|\r|\n/', $_post['adstxt'] );
	$sanitized = $errors = array();

// TODO: ARRAY_MAP THIS THING
	foreach ( $lines as $i => $line ) {
		$line_number = $i + 1;
		$result = validate_line( $line, $line_number );

		$sanitized[] = $result['sanitized'];
		if ( ! empty( $result['errors'] ) ) {
			$errors = array_merge( $errors, $result['errors'] );
		}
	}

	$sanitized = implode( PHP_EOL, $sanitized );

	$postarr = array(
		'ID' => $post_id,
		'post_title' => 'Ads.txt',
		'post_content' => $sanitized,
		'post_type' => 'adstxt',
		'post_status' => 'publish',
		'meta_input' => array(
			'adstxt_errors' => $errors,
		),
	);

	$post_id = wp_insert_post( $postarr );
	update_option( 'adstxt_post', $post_id );

	wp_redirect( $_POST['_wp_http_referer'] . '&updated=true' );
	exit;
}
add_action( 'admin_post_adstxt-save', __NAMESPACE__ . '\admin_post' );

function validate_line( $line, $line_number ) {
	$errors = array();

	if ( empty( $line ) ) {
		$sanitized = '';
	} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment
		$sanitized = sanitize_text_field( $line );
	} elseif( 1 < strpos( $line, '=' ) ) { // This is a variable declaration
		// The spec currently supports CONTACT and SUBDOMAIN
		if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
			$errors[] = array(
				'line' => $line_number,
				'type' => 'warning',
				'message' => __( 'Unrecognized variable', 'adstxt' ),
			);

			// Because the spec can change we don't comment out invalid-looking lines
			$sanitized = sanitize_text_field( $line );
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
				$sanitized = '# ' . sanitize_text_field( $line );
			} else {
				$subdomain = $subdomain[0];
				// YOU ARE HERE - YOU WANT TO CHECK ON THE FORMATION OF THIS SUBDOMAIN
				// if not good, add a warning - probably fine to leave it there, just a crawler problem
			}
		} else {
			$sanitized = sanitize_text_field( $line );
		}

		unset( $subdomain );
	} else { // Data records: the most common
		// Disregard any comments
		$record = explode( '#', $line );
		$record = $record[0];

		// Record format: example.exchange.com,pub-id123456789,RESELLER|DIRECT,tagidhash123(optional)
		$fields = explode( ',', $record );

		if ( 3 <= count( $fields ) ) {
			$exchange = trim( $fields[0] );
			$pub_id = trim( $fields[1] );
			$account_type = trim( $fields[2] );

			// TODO: CHECK IF FIELD 1 IS A DOMAIN

			if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $account_type ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'error',
					'message' => __( 'Third field should be RESELLER or DIRECT', 'adstxt' ),
				);
			}

			if ( isset( $fields[3] ) ) {
				$tag_id = trim( $fields[3] );

				// TAG-IDs appear to be 16 character hashes
				// TAG-IDs are meant to be checked against their DB - perhaps good for a service or the future
				if ( ! preg_match( '/^[a-f0-9]{16}$/', $tag_id ) ) {
					$errors[] = array(
						'line' => $line_number,
						'type' => 'warning',
						'message' => __( 'TAG-ID appears invalid', 'adstxt' ),
					);
				}
			}

			$sanitized = sanitize_text_field( $line );
		} else {
			// Not a comment, variable declaration, or data record; therefore, invalid.
			// Comment it out for safety.
			// WARNING: this could get cached by crawlers and take time to clear. This is just the PHP fallback.
			$sanitized = '# ' . sanitize_text_field( $line );

			$errors[] = array(
				'line' => $line_number,
				'type' => 'error',
				'message' => __( 'Invalid record; commented out', 'adstxt' ),
			);
		}

		unset( $record, $fields );
	}

	return array(
		'sanitized' => $sanitized,
		'errors' => $errors
	);
}

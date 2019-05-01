<?php

namespace Adstxt;

/**
 * Process and save the ads.txt data.
 *
 * Handles both AJAX and POST saves via `admin-ajax.php` and `admin-post.php` respectively.
 * AJAX calls output JSON; POST calls redirect back to the Ads.txt edit screen.
 *
 * @return void
 */
function save() {
	current_user_can( 'customize' ) || die;
	check_admin_referer( 'adstxt_save' );
	$_post      = stripslashes_deep( $_POST );
	$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

	$post_id = $_post['post_id'];
	$ays     = isset( $_post['adstxt_ays'] ) ? $_post['adstxt_ays'] : null;

	// Different browsers use different line endings.
	$lines     = preg_split( '/\r\n|\r|\n/', $_post['adstxt'] );
	$sanitized = array();
	$errors    = array();
	$response  = array();

	foreach ( $lines as $i => $line ) {
		$line_number = $i + 1;
		$result      = validate_line( $line, $line_number );

		$sanitized[] = $result['sanitized'];
		if ( ! empty( $result['errors'] ) ) {
			$errors = array_merge( $errors, $result['errors'] );
		}
	}

	$sanitized = implode( PHP_EOL, $sanitized );

	$postarr = array(
		'ID'           => $post_id,
		'post_title'   => 'Ads.txt',
		'post_content' => $sanitized,
		'post_type'    => 'adstxt',
		'post_status'  => 'publish',
		'meta_input'   => array(
			'adstxt_errors' => $errors,
		),
	);

	if ( ! $doing_ajax || empty( $errors ) || 'y' === $ays ) {
		$post_id = wp_insert_post( $postarr );

		if ( $post_id ) {
			update_option( 'adstxt_post', $post_id );
			$response['saved'] = true;
		}
	}

	if ( $doing_ajax ) {
		$response['sanitized'] = $sanitized;

		if ( ! empty( $errors ) ) {
			$response['errors'] = $errors;
		}

		echo wp_json_encode( $response );
		die();
	}

	wp_safe_redirect( esc_url_raw( $_post['_wp_http_referer'] ) . '&updated=true' );
	exit;
}
add_action( 'admin_post_adstxt-save', __NAMESPACE__ . '\save' );
add_action( 'wp_ajax_adstxt-save', __NAMESPACE__ . '\save' );

/**
 * Validate a single line.
 *
 * @param string $line        The line to validate.
 * @param string $line_number The line number being evaluated.
 *
 * @return array {
 *     @type string $sanitized Sanitized version of the original line.
 *     @type array  $errors    Array of errors associated with the line.
 * }
 */
function validate_line( $line, $line_number ) {
	$domain_regex = '/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/i';
	$errors       = array();

	if ( empty( $line ) ) {
		$sanitized = '';
	} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment.
		$sanitized = wp_strip_all_tags( $line );
	} elseif ( 1 < strpos( $line, '=' ) ) { // This is a variable declaration.
		// The spec currently supports CONTACT and SUBDOMAIN.
		if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
			$errors[] = array(
				'line' => $line_number,
				'type' => 'invalid_variable',
			);
		} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains.
			// Disregard any comments.
			$subdomain = explode( '#', $line );
			$subdomain = $subdomain[0];

			$subdomain = explode( '=', $subdomain );
			array_shift( $subdomain );

			// If there's anything other than one piece left something's not right.
			if ( 1 !== count( $subdomain ) || ! preg_match( $domain_regex, $subdomain[0] ) ) {
				$subdomain = implode( '', $subdomain );
				$errors[]  = array(
					'line'  => $line_number,
					'type'  => 'invalid_subdomain',
					'value' => $subdomain,
				);
			}
		}

		$sanitized = wp_strip_all_tags( $line );

		unset( $subdomain );
	} else { // Data records: the most common.
		// Disregard any comments.
		$record = explode( '#', $line );
		$record = $record[0];

		// Record format: example.exchange.com,pub-id123456789,RESELLER|DIRECT,tagidhash123(optional).
		$fields = explode( ',', $record );

		if ( 3 <= count( $fields ) ) {
			$exchange     = trim( $fields[0] );
			$pub_id       = trim( $fields[1] );
			$account_type = trim( $fields[2] );

			if ( ! preg_match( $domain_regex, $exchange ) ) {
				$errors[] = array(
					'line'  => $line_number,
					'type'  => 'invalid_exchange',
					'value' => $exchange,
				);
			}

			if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $account_type ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'invalid_account_type',
				);
			}

			if ( isset( $fields[3] ) ) {
				$tag_id = trim( $fields[3] );

				// TAG-IDs appear to be 16 character hashes.
				// TAG-IDs are meant to be checked against their DB - perhaps good for a service or the future.
				if ( ! empty( $tag_id ) && ! preg_match( '/^[a-f0-9]{16}$/', $tag_id ) ) {
					$errors[] = array(
						'line'  => $line_number,
						'type'  => 'invalid_tagid',
						'value' => $fields[3],
					);
				}
			}

			$sanitized = wp_strip_all_tags( $line );
		} else {
			// Not a comment, variable declaration, or data record; therefore, invalid.
			// Early on we commented the line out for safety but it's kind of a weird thing to do with a JS AYS.
			$sanitized = wp_strip_all_tags( $line );

			$errors[] = array(
				'line' => $line_number,
				'type' => 'invalid_record',
			);
		}

		unset( $record, $fields );
	}

	return array(
		'sanitized' => $sanitized,
		'errors'    => $errors,
	);
}

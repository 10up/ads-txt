<?php
/**
 * Save functionality for Ads.txt.
 *
 * @package Ads_Txt_Manager
 */

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
	current_user_can( ADS_TXT_MANAGE_CAPABILITY ) || die;
	check_admin_referer( 'adstxt_save' );
	$_post      = stripslashes_deep( $_POST );
	$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

	$post_id = (int) $_post['post_id'];
	$ays     = isset( $_post['adstxt_ays'] ) ? $_post['adstxt_ays'] : null;

	// Different browsers use different line endings.
	$lines                        = preg_split( '/\r\n|\r|\n/', $_post['adstxt'] );
	$sanitized                    = array();
	$errors                       = array();
	$warnings                     = array();
	$response                     = array();
	$has_only_placeholder_records = null;

	foreach ( $lines as $i => $line ) {
		$line_number = $i + 1;
		$result      = validate_line( $line, $line_number, $has_only_placeholder_records );

		$sanitized[] = $result['sanitized'];
		if ( ! empty( $result['errors'] ) ) {
			$errors = array_merge( $errors, $result['errors'] );
		}

		if ( ! empty( $result['warnings'] ) ) {
			$warnings = array_merge( $warnings, $result['warnings'] );
		}

		if ( ! empty( $result['is_placeholder_record'] ) ) {
			if ( is_null( $has_only_placeholder_records ) ) {
				$has_only_placeholder_records = true;
			}
		}

		list( 'errors' => $errors_data, 'warnings' => $warnings_data, 'is_placeholder_record' => $is_placeholder, 'is_empty_record' => $is_empty_line, 'is_comment' => $is_comment ) = $result;

		// Check if the line is valid, then set $has_only_placeholder_records to false.
		if ( empty( $is_placeholder ) && empty( $errors_data ) && empty( $warnings_data ) && ( ! $is_comment && ! $is_empty_line ) ) {
			$has_only_placeholder_records = false;
		}
	}

	// If $has_only_placeholder_records is false, remove no_authorized_seller warning.
	if ( false === $has_only_placeholder_records ) {
		$key = array_search( 'no_authorized_seller', array_column( $warnings, 'type' ) );
		if ( false !== $key ) {
			unset( $warnings[ $key ] );
		}
	}

	$sanitized = implode( PHP_EOL, $sanitized );
	$postarr   = array(
		'ID'           => $post_id,
		'post_title'   => 'Ads.txt',
		'post_content' => $sanitized,
		'post_type'    => 'adstxt',
		'post_status'  => 'publish',
		'meta_input'   => array(
			'adstxt_errors'   => $errors,
			'adstxt_warnings' => $warnings,
		),
	);

	if ( 'app-adstxt' === $_post['adstxt_type'] ) {
		$postarr['post_title'] = 'App-ads.txt';
		$postarr['post_type']  = 'app-adstxt';
	}

	if ( ! $doing_ajax || empty( $errors ) || 'y' === $ays ) {
		$post_id = wp_insert_post( $postarr );

		if ( $post_id ) {
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
add_action( 'admin_post_app-adstxt-save', __NAMESPACE__ . '\save' );
add_action( 'wp_ajax_adstxt-save', __NAMESPACE__ . '\save' );
add_action( 'wp_ajax_app-adstxt-save', __NAMESPACE__ . '\save' );

/**
 * Validate a single line.
 *
 * @param string $line                         The line to validate.
 * @param string $line_number                  The line number being evaluated.
 * @param string $has_only_placeholder_records Flag for presence of placeholder record.
 *
 * @return array {
 *     @type string $sanitized Sanitized version of the original line.
 *     @type array  $errors    Array of errors associated with the line.
 * }
 */
function validate_line( $line, $line_number, $has_only_placeholder_records = null ) {
	static $record_lines   = 0;
	$is_placeholder_record = false;
	$is_empty_record       = false;
	$is_comment            = false;

	// Only to count for records, not comments/variables.
	$domain_regex = '/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/i';
	$errors       = array();
	$warnings     = array();

	if ( empty( $line ) ) {
		$sanitized       = '';
		$is_empty_record = true;
	} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment.
		$sanitized  = wp_strip_all_tags( $line );
		$is_comment = true;
	} elseif ( 1 < strpos( $line, '=' ) ) { // This is a variable declaration.
		// The spec currently supports CONTACT, INVENTORYPARTNERDOMAIN, SUBDOMAIN, OWNERDOMAIN and MANAGERDOMAIN.
		if ( ! preg_match( '/^(CONTACT|SUBDOMAIN|INVENTORYPARTNERDOMAIN|OWNERDOMAIN|MANAGERDOMAIN)=/i', $line ) ) {
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
			++$record_lines;
			$exchange              = trim( $fields[0] );
			$pub_id                = trim( $fields[1] );
			$account_type          = trim( $fields[2] );
			$tag_id                = ! empty( $fields[3] ) ? trim( $fields[3] ) : null;
			$is_placeholder_record = is_placeholder_record( $exchange, $pub_id, $account_type, $tag_id );

			// If the file contains placeholder record and no placeholder was already present, set variable.
			if ( $is_placeholder_record && is_null( $has_only_placeholder_records ) ) {
				$warnings[] = array(
					'type'    => 'no_authorized_seller',
					'message' => __( 'Your ads.txt indicates no authorized advertising sellers.', 'ads-txt' ),
				);
			}

			// Process further only if the current record is not placeholder record.
			if ( ! $is_placeholder_record ) {
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
		'sanitized'             => $sanitized,
		'errors'                => $errors,
		'warnings'              => $warnings,
		'is_placeholder_record' => $is_placeholder_record,
		'is_empty_record'       => $is_empty_record,
		'is_comment'            => $is_comment,
	);
}

/**
 * Delete `adstxt_errors` meta when restoring a revision.
 *
 * @param int $post_id Post ID, not revision ID.
 *
 * @return void
 */
function clear_error_meta( $post_id ) {
	delete_post_meta( $post_id, 'adstxt_errors' );
}
add_action( 'wp_restore_post_revision', __NAMESPACE__ . '\clear_error_meta', 10, 1 );

/**
 * Checks if the given record is placeholder record.
 * Placeholder indicates that no advertising system is authorized to buy and sell ads on the website.
 *
 * @see https://iabtechlab.com/wp-content/uploads/2021/03/ads.txt-1.0.3.pdf
 *
 * @param string      $exchange        Domain name of the advertising system.
 * @param string      $pub_id          Publisher’s Account ID.
 * @param string      $account_type    Type of Account/Relationship.
 * @param string|null $tag_id          Certification Authority ID.
 *
 * @return bool
 */
function is_placeholder_record( $exchange, $pub_id, $account_type, $tag_id = null ) {
	$result = true;

	// Check the exchange for placeholder.
	if ( 'placeholder.example.com' !== $exchange ) {
		$result = false;
	}

	// Check the publisher ID for placeholder.
	if ( 'placeholder' !== $pub_id ) {
		$result = false;
	}

	// Check the account type for placeholder.
	if ( 'DIRECT' !== $account_type ) {
		$result = false;
	}

	// Check the tag ID for placeholder.
	if ( ! empty( $tag_id ) && 'placeholder' !== $tag_id ) {
		$result = false;
	}

	return $result;
}

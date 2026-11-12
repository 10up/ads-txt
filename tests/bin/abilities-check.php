<?php
/**
 * Manual test for the Ads.txt Abilities API integration.
 *
 * Run with: wp eval-file wp-content/plugins/ads-txt/tests/bin/abilities-check.php
 * The plugin must be active on the site.
 *
 * Creates temporary users, saves test content, then restores the original
 * ads.txt and app-ads.txt options, the administrator role, and deletes the users.
 */

$results = array();
$check   = function ( $name, $pass, $detail = '' ) use ( &$results ) {
	$results[] = array( $name, $pass ? 'PASS' : 'FAIL', $detail );
};

$names = array( 'ads-txt/get-content', 'ads-txt/validate-content', 'ads-txt/update-content' );
$abils = array();
foreach ( $names as $name ) {
	$abils[ $name ] = wp_get_ability( $name );
	$check( "Registered: {$name}", (bool) $abils[ $name ] );
}
$check( 'Category registered', (bool) wp_get_ability_category( 'ads-txt' ) );

$users    = array();
$original = array(
	ADS_TXT_MANAGER_POST_OPTION     => get_option( ADS_TXT_MANAGER_POST_OPTION, null ),
	APP_ADS_TXT_MANAGER_POST_OPTION => get_option( APP_ADS_TXT_MANAGER_POST_OPTION, null ),
);
$created  = array();
$added_cap = false;
$admin_role = null;

try {
	$suffix = wp_generate_password( 4, false );
	foreach ( array( 'subscriber', 'editor', 'administrator' ) as $role ) {
		$users[ $role ] = wp_insert_user(
			array(
				'user_login' => "adstxt_test_{$role}_{$suffix}",
				'user_pass'  => wp_generate_password( 24 ),
				'user_email' => "adstxt_{$role}_{$suffix}@example.test",
				'role'       => $role,
			)
		);
	}
	// The capability is added on admin_init, so make sure the role has it. Remembered so it can be undone.
	$admin_role = get_role( 'administrator' );
	$added_cap  = $admin_role && ! $admin_role->has_cap( ADS_TXT_MANAGE_CAPABILITY );
	if ( $added_cap ) {
		$admin_role->add_cap( ADS_TXT_MANAGE_CAPABILITY );
	}

	$run = function ( $uid, $name, $input ) use ( $abils ) {
		wp_set_current_user( $uid );
		return $abils[ $name ]->execute( $input );
	};
	$denied = function ( $res ) {
		return is_wp_error( $res ) || false === $res;
	};

	$valid   = "# test\nexample.com, pub-123, DIRECT, f08c47fec0942fa0";
	$invalid = "example.com, pub-123, WRONG\nnot a record";

	// Permissions.
	foreach ( array( 'subscriber', 'editor' ) as $role ) {
		foreach ( $names as $name ) {
			$input = 'ads-txt/get-content' === $name ? array() : array( 'content' => $valid );
			$check( "{$role} denied: {$name}", $denied( $run( $users[ $role ], $name, $input ) ) );
		}
	}
	$check( 'Logged-out denied', $denied( $run( 0, 'ads-txt/get-content', array() ) ) );

	// Validate.
	$admin = $users['administrator'];
	$res   = $run( $admin, 'ads-txt/validate-content', array( 'content' => $valid ) );
	$check( 'Valid content passes', is_array( $res ) && $res['valid'] && array() === $res['errors'], wp_json_encode( $res ) );
	$res = $run( $admin, 'ads-txt/validate-content', array( 'content' => $invalid ) );
	$check( 'Invalid content reports errors', is_array( $res ) && ! $res['valid'] && count( $res['errors'] ) >= 2, wp_json_encode( $res ) );

	// Bad input.
	$check( 'Missing content rejected', is_wp_error( $run( $admin, 'ads-txt/update-content', array() ) ) );
	$check( 'Unknown property rejected', is_wp_error( $run( $admin, 'ads-txt/update-content', array( 'content' => $valid, 'bogus' => 1 ) ) ) );
	$check( 'Bad type rejected', is_wp_error( $run( $admin, 'ads-txt/get-content', array( 'type' => 'nope' ) ) ) );

	// Update with errors is refused without force.
	$before = $run( $admin, 'ads-txt/get-content', array() );
	$res    = $run( $admin, 'ads-txt/update-content', array( 'content' => $invalid ) );
	$check( 'Errors block save without force', is_array( $res ) && false === $res['saved'] && ! empty( $res['errors'] ), wp_json_encode( $res ) );
	$after = $run( $admin, 'ads-txt/get-content', array() );
	$check( 'Blocked save changed nothing', $before['content'] === $after['content'] );

	// Update and read back, both files.
	foreach ( array( 'adstxt', 'app-adstxt' ) as $type ) {
		$res = $run( $admin, 'ads-txt/update-content', array( 'type' => $type, 'content' => $valid ) );
		$check( "Save {$type}", is_array( $res ) && true === $res['saved'], is_wp_error( $res ) ? $res->get_error_message() : '' );
		$res = $run( $admin, 'ads-txt/get-content', array( 'type' => $type ) );
		$check( "Read back {$type}", is_array( $res ) && $res['content'] === $valid, wp_json_encode( $res ) );
	}
	$ads_id = (int) get_option( ADS_TXT_MANAGER_POST_OPTION );
	$app_id = (int) get_option( APP_ADS_TXT_MANAGER_POST_OPTION );
	$check( 'Files use separate posts', $ads_id && $app_id && $ads_id !== $app_id );
	$check( 'Public ads.txt content matches', get_post_field( 'post_content', $ads_id ) === $valid );

	// Force save.
	$res = $run( $admin, 'ads-txt/update-content', array( 'content' => $invalid, 'force' => true ) );
	$check( 'Force saves content with errors', is_array( $res ) && true === $res['saved'] && ! empty( $res['errors'] ) );
	$res = $run( $admin, 'ads-txt/get-content', array() );
	$check( 'Stored errors are returned', ! empty( $res['errors'] ), wp_json_encode( $res ) );
	$check( 'Second save reuses the same post', (int) get_option( ADS_TXT_MANAGER_POST_OPTION ) === $ads_id );

	// Hostile values.
	$before = $run( $admin, 'ads-txt/get-content', array() );
	$res    = $run( $admin, 'ads-txt/update-content', array( 'content' => 'junk', 'force' => 'false' ) );
	$check( 'force "false" does not force a save', is_array( $res ) && false === $res['saved'], wp_json_encode( $res ) );
	$check( 'Oversized content is rejected', is_wp_error( $run( $admin, 'ads-txt/update-content', array( 'content' => str_repeat( 'a', 500001 ) ) ) ) );
	$res = $run( $admin, 'ads-txt/validate-content', array( 'content' => "\u{FEFF}# a\x1b[31m\u{202E}b" ) );
	$check( 'Control and direction characters are removed', is_array( $res ) && '# a[31mb' === $res['sanitized'], wp_json_encode( $res ) );
	$max = str_repeat( "example.com, pub-1, DIRECT\n", (int) ( 500000 / 27 ) );
	$res = $run( $admin, 'ads-txt/validate-content', array( 'content' => $max ) );
	$check( 'Largest allowed file validates', is_array( $res ) && $res['valid'], sprintf( 'peak memory %.0f MB', memory_get_peak_usage() / MB_IN_BYTES ) );
	$after = $run( $admin, 'ads-txt/get-content', array() );
	$check( 'Hostile values changed nothing', $before['content'] === $after['content'] );

	// REST.
	wp_set_current_user( 0 );
	$req = new WP_REST_Request( 'POST', '/wp-abilities/v1/abilities/ads-txt/update-content/run' );
	$req->set_header( 'content-type', 'application/json' );
	$req->set_body( wp_json_encode( array( 'input' => array( 'content' => $valid ) ) ) );
	$check( 'REST logged-out is 401/403', in_array( rest_do_request( $req )->get_status(), array( 401, 403 ), true ) );
	wp_set_current_user( $admin );
	$req = new WP_REST_Request( 'GET', '/wp-abilities/v1/abilities/ads-txt/get-content/run' );
	$check( 'REST admin read is 200', 200 === rest_do_request( $req )->get_status() );

	// A large file must validate over POST. The ability is not a GET, so the URL length does not limit it.
	$big = str_repeat( "example.com, pub-123456789, DIRECT, f08c47fec0942fa0\n", 1000 );
	$req = new WP_REST_Request( 'POST', '/wp-abilities/v1/abilities/ads-txt/validate-content/run' );
	$req->set_header( 'content-type', 'application/json' );
	$req->set_body( wp_json_encode( array( 'input' => array( 'content' => $big ) ) ) );
	$response = rest_do_request( $req );
	$data     = $response->get_data();
	$check( 'Large file validates over POST', 200 === $response->get_status() && ! empty( $data['valid'] ), 'status ' . $response->get_status() . ', size ' . strlen( $big ) );
	$req = new WP_REST_Request( 'GET', '/wp-abilities/v1/abilities/ads-txt/validate-content/run' );
	$check( 'Validate is not available over GET', 200 !== rest_do_request( $req )->get_status() );
} finally {
	// Undo the capability only if this script added it.
	if ( $added_cap && $admin_role ) {
		$admin_role->remove_cap( ADS_TXT_MANAGE_CAPABILITY );
	}
	// Remove the posts the test made and restore the original options.
	foreach ( array_unique( array_filter( array( (int) get_option( ADS_TXT_MANAGER_POST_OPTION ), (int) get_option( APP_ADS_TXT_MANAGER_POST_OPTION ) ) ) ) as $id ) {
		if ( $id !== (int) $original[ ADS_TXT_MANAGER_POST_OPTION ] && $id !== (int) $original[ APP_ADS_TXT_MANAGER_POST_OPTION ] ) {
			wp_delete_post( $id, true );
		}
	}
	foreach ( $original as $option => $value ) {
		null === $value ? delete_option( $option ) : update_option( $option, $value );
	}
	foreach ( $users as $uid ) {
		wp_delete_user( $uid );
	}
}

$failed = 0;
foreach ( $results as $row ) {
	WP_CLI::log( sprintf( '%s  %s %s', $row[1], $row[0], $row[2] && 'FAIL' === $row[1] ? "-- {$row[2]}" : '' ) );
	$failed += 'FAIL' === $row[1] ? 1 : 0;
}
$failed ? WP_CLI::error( "{$failed} check(s) failed." ) : WP_CLI::success( count( $results ) . ' checks passed.' );

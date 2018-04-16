<?php

// need to check if bootstrap need to be ignore
if ( ! defined( 'WP_TEST_IGNORE_BOOTSTRAP' ) ) {
	$_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $_tests_dir ) {
		$_tests_dir = dirname( __DIR__ ) . '/tests/phpunit';
	}
	require_once $_tests_dir . '/includes/functions.php';
}
function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../ads-txt.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

if ( ! defined( 'WP_TEST_IGNORE_BOOTSTRAP' ) ) {
	require $_tests_dir . '/includes/bootstrap.php';
	// Disable the deprecated warnings (problem with WP3.7.1 and php 5.5)
	PHPUnit_Framework_Error_Deprecated::$enabled = false;
}


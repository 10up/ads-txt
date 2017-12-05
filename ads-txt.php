<?php
/**
 * Plugin Name: Ads.txt
 * Description: Manage your ads.txt file from within WordPress.
 * Version:     0.1
 * Author:      Helen Hou-Sandi, 10up
 * Author URI:  http://10up.com
 * License:     GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include_once( __DIR__ . '/inc/admin.php' );

/**
 * Display the contents of /ads.txt when requested.
 * @return void
 */
function tenup_display_ads_txt() {
	$request = $_SERVER['REQUEST_URI'];
	if ( '/ads.txt' === $request ) {
		header( 'Content-Type: text/plain' );
		echo 'this is ads.txt!';
		die();
	}
}
add_action( 'init', 'tenup_display_ads_txt' );



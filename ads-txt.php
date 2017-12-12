<?php
/**
 * Plugin Name: Ads.txt
 * Description: Manage your ads.txt file from within WordPress.
 * Version:     0.1
 * Author:      Helen Hou-Sandi, 10up
 * Author URI:  http://10up.com
 * License:     GPLv2 or later
 * Text Domain: adstxt
 * Domain Path: /lang/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/inc/post-type.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/save.php';

/**
 * Display the contents of /ads.txt when requested.
 * @return void
 */
function tenup_display_ads_txt() {
	$request = $_SERVER['REQUEST_URI'];
	if ( '/ads.txt' === $request ) {
		$post_id = get_option( 'adstxt_post' );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
			header( 'Content-Type: text/plain' );
			echo $post->post_content;
			die();
		}
	}
}
add_action( 'init', 'tenup_display_ads_txt' );

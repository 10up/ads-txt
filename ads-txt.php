<?php
/**
 * Plugin Name: Ads.txt Manager
 * Description: Create, manage, and validate your Ads.txt from within WordPress, just like any other content asset. Requires PHP 5.3+ and WordPress 4.9+.
 * Version:     1.1
 * Author:      10up
 * Author URI:  http://10up.com
 * License:     GPLv2 or later
 * Text Domain: ads-txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/inc/post-type.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/save.php';

/**
 * Display the contents of /ads.txt when requested.
 *
 * @return void
 */
function tenup_display_ads_txt() {
	$request = esc_url_raw( $_SERVER['REQUEST_URI'] );
	if ( '/ads.txt' === $request ) {
		$post_id = get_option( 'adstxt_post' );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
			header( 'Content-Type: text/plain' );
			echo esc_html( $post->post_content );
			die();
		}
	}
}
add_action( 'init', 'tenup_display_ads_txt' );

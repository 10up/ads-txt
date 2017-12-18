<?php
/**
 * Plugin Name: Ads.txt
 * Description: Manage your ads.txt file from within WordPress.
 * Version:     1.0
 * Author:      Helen Hou-Sandi, 10up
 * Author URI:  http://10up.com
 * License:     GPLv2 or later
 * Text Domain: ads-txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Require PHP version 5.3+.
 *
 * @return void
 */
function tenup_adstxt_php_version() {
	if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
		wp_die(
			__( 'Ads.txt Manager requires PHP 5.3+.', 'ads-txt' ),
			__( 'Error Activating', 'ads-txt' )
		);
	}
}
register_activation_hook( __FILE__, 'tenup_adstxt_php_version' );

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

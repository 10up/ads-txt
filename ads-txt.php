<?php
/**
 * Plugin Name: Ads.txt Manager
 * Description: Create, manage, and validate your Ads.txt from within WordPress, just like any other content asset. Requires PHP 5.3+ and WordPress 4.9+.
 * Version:     1.1
 * Author:      10up
 * Author URI:  http://10up.com
 * License:     GPLv2 or later
 * Text Domain: ads-txt
 *
 * @package Ads_Txt_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ADS_TXT_MANAGER_VERSION', '1.1.0' );
define( 'ADS_TXT_MANAGE_CAPABILITY', 'edit_ads_txt' );

require_once __DIR__ . '/inc/post-type.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/save.php';

/**
 * Display the contents of /ads.txt when requested.
 *
 * @return void
 */
function tenup_display_ads_txt() {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;
	if ( '/ads.txt' === $request ) {
		$post_id = get_option( 'adstxt_post' );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
			header( 'Content-Type: text/plain' );
			$adstxt = $post->post_content;

			/**
			 * Filter the ads.txt content.
			 *
			 * @since 1.2.0
			 *
			 * @param type  $adstxt The existing ads.txt content.
			 */
			echo esc_html( apply_filters( 'ads_txt_content', $adstxt ) );
			die();
		}
	}
}
add_action( 'init', 'tenup_display_ads_txt' );

/**
 * Add custom capabilities.
 *
 * @return void
 */
function add_adstxt_capabilities() {
	$role = get_role( 'administrator' );
	if ( ! has_cap( ADS_TXT_MANAGE_CAPABILITY ) ) {
		$role->add_cap( ADS_TXT_MANAGE_CAPABILITY );
	}
}
add_action( 'admin_init', __FILE__, 'add_adstxt_capabilities' );

/**
 * Remove custom capabilities when deactivating the plugin.
 *
 * @return void
 */
function remove_adstxt_capabilities() {
	$role = get_role( 'administrator' );
	$role->remove_cap( ADS_TXT_MANAGE_CAPABILITY );
}
register_deactivation_hook( __FILE__, 'remove_adstxt_capabilities' );

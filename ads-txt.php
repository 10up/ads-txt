<?php
/**
 * Plugin Name:       Ads.txt Manager
 * Description:       Create, manage, and validate your Ads.txt from within WordPress, just like any other content asset. Requires PHP 7.4+ and WordPress 5.7+.
 * Version:           1.4.1
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPLv2 or later
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Text Domain:       ads-txt
 *
 * @package Ads_Txt_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ADS_TXT_MANAGER_VERSION', '1.4.1' );
define( 'ADS_TXT_MANAGE_CAPABILITY', 'edit_ads_txt' );
define( 'ADS_TXT_MANAGER_POST_OPTION', 'adstxt_post' );
define( 'APP_ADS_TXT_MANAGER_POST_OPTION', 'app_adstxt_post' );

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
		$post_id = get_option( ADS_TXT_MANAGER_POST_OPTION );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post instanceof WP_Post ) {
				return;
			}

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
	} elseif ( '/app-ads.txt' === $request ) {
		$post_id = get_option( APP_ADS_TXT_MANAGER_POST_OPTION );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post instanceof WP_Post ) {
				return;
			}

			header( 'Content-Type: text/plain' );
			$adstxt = $post->post_content;

			/**
			 * Filter the app-ads.txt content.
			 *
			 * @since 1.3.0
			 *
			 * @param type  $app_adstxt The existing ads.txt content.
			 */
			echo esc_html( apply_filters( 'app_ads_txt_content', $adstxt ) );
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
	if ( ! $role->has_cap( ADS_TXT_MANAGE_CAPABILITY ) ) {
		$role->add_cap( ADS_TXT_MANAGE_CAPABILITY );
	}
}
add_action( 'admin_init', 'add_adstxt_capabilities' );
register_activation_hook( __FILE__, 'add_adstxt_capabilities' );

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

/**
 * Add a query var to detect when ads.txt has been saved.
 *
 * @param array $qvars Array of query vars.
 *
 * @return array Array of query vars.
 */
function tenup_ads_txt_add_query_vars( $qvars ) {
	$qvars[] = 'ads_txt_saved';
	return $qvars;
}
add_filter( 'query_vars', 'tenup_ads_txt_add_query_vars' );

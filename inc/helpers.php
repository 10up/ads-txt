<?php
/**
 * Helper functions for Ads.txt.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt;

/**
 * Display the contents of /ads.txt when requested.
 *
 * @return void
 */
function display_ads_txt() {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;
	if ( '/ads.txt' === $request || '/ads.txt?' === substr( $request, 0, 9 ) ) {
		$post_id = get_option( ADS_TXT_MANAGER_POST_OPTION );

		// Set custom header for ads-txt
		header( 'X-Ads-Txt-Generator: https://wordpress.org/plugins/ads-txt/' );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post instanceof \WP_Post ) {
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
	} elseif ( '/app-ads.txt' === $request || '/app-ads.txt?' === substr( $request, 0, 13 ) ) {
		$post_id = get_option( APP_ADS_TXT_MANAGER_POST_OPTION );

		// Set custom header for ads-txt
		header( 'X-Ads-Txt-Generator: https://wordpress.org/plugins/ads-txt/' );

		// Will fall through if no option found, likely to a 404.
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post instanceof \WP_Post ) {
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
add_action( 'init', __NAMESPACE__ . '\display_ads_txt' );

/**
 * Add custom capabilities.
 *
 * @return void
 */
function add_capabilities() {
	$role = get_role( 'administrator' );

	// Bail early if the administrator role doesn't exist.
	if ( null === $role ) {
		return;
	}

	if ( ! $role->has_cap( ADS_TXT_MANAGE_CAPABILITY ) ) {
		$role->add_cap( ADS_TXT_MANAGE_CAPABILITY );
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\add_capabilities' );
register_activation_hook( __FILE__, __NAMESPACE__ . '\add_capabilities' );

/**
 * Remove custom capabilities when deactivating the plugin.
 *
 * @return void
 */
function remove_capabilities() {
	$role = get_role( 'administrator' );

	// Bail early if the administrator role doesn't exist.
	if ( null === $role ) {
		return;
	}

	$role->remove_cap( ADS_TXT_MANAGE_CAPABILITY );
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\remove_capabilities' );

/**
 * Add a query var to detect when ads.txt has been saved.
 *
 * @param array $qvars Array of query vars.
 *
 * @return array Array of query vars.
 */
function add_query_vars( $qvars ) {
	$qvars[] = 'ads_txt_saved';
	return $qvars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\add_query_vars' );

<?php
/**
 * Plugin Name: Ads.txt E2E helpers
 * Description: Test fixture for the E2E suite. Loaded only in the wp-env test environment.
 *
 * @package Ads_Txt_Manager
 */

/**
 * Appends a marker to the served file when the request has `ads-txt-e2e-filter`.
 *
 * Only acts on that query argument, so every other spec sees unfiltered content.
 *
 * @param string $content File content about to be served.
 * @return string Content, with the marker when requested.
 */
function ads_txt_e2e_append_marker( $content ) {
	if ( ! isset( $_GET['ads-txt-e2e-filter'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $content;
	}

	return $content . "\n# filtered by " . current_filter();
}
add_filter( 'ads_txt_content', 'ads_txt_e2e_append_marker' );
add_filter( 'app_ads_txt_content', 'ads_txt_e2e_append_marker' );

/**
 * Prints a valid save nonce for the logged-in user.
 *
 * Lets a spec send a save request that only the capability check can reject.
 *
 * @return void
 */
function ads_txt_e2e_save_nonce() {
	echo esc_html( wp_create_nonce( 'adstxt_save' ) );
	wp_die();
}
add_action( 'wp_ajax_ads_txt_e2e_save_nonce', 'ads_txt_e2e_save_nonce' );

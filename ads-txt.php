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
define( 'ADS_TXT_MANAGER_POST_OPTION', 'adstxt_post' );

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

function tenup_ads_txt_add_query_vars( $qvars ) {
	$qvars[] = 'ads_txt_saved';
	return $qvars;
}
add_filter( 'query_vars', 'tenup_ads_txt_add_query_vars' );

function sample_admin_notice__success() {
	if ( ! isset( $_GET['ads_txt_saved'] ) ) {
		return;
	}
	?>
	<div class="notice notice-success adstxt-notice adstxt-saved">
		<p><?php echo esc_html__( 'Ads.txt saved', 'ads-txt' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'sample_admin_notice__success' );
<?php
/**
 * Plugin Name:       Ads.txt Manager
 * Plugin URI:        https://github.com/10up/ads-txt
 * Description:       Create, manage, and validate your Ads.txt from within WordPress, just like any other content asset. Requires PHP 7.4+ and WordPress 5.7+.
 * Version:           1.4.6
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       ads-txt
 *
 * @package Ads_Txt_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ADS_TXT_MANAGER_VERSION', '1.4.6' );
define( 'ADS_TXT_MANAGE_CAPABILITY', 'edit_ads_txt' );
define( 'ADS_TXT_MANAGER_POST_OPTION', 'adstxt_post' );
define( 'APP_ADS_TXT_MANAGER_POST_OPTION', 'app_adstxt_post' );

/**
 * Get the minimum version of PHP required by this plugin.
 *
 * @return string Minimum version required.
 */
function adstxt_minimum_php_requirement() {
	return '7.4';
}

/**
 * Whether PHP installation meets the minimum requirements
 *
 * @return bool True if meets minimum requirements, false otherwise.
 */
function adstxt_site_meets_php_requirements() {
	return version_compare( phpversion(), adstxt_minimum_php_requirement(), '>=' );
}

// Ensuring our PHP version requirement is met first before loading plugin.
if ( ! adstxt_site_meets_php_requirements() ) {
	add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						/* translators: %s: Minimum required PHP version */
						esc_html__( 'Ads.txt requires PHP version %s or later. Please upgrade PHP or disable the plugin.', 'ads-txt' ),
						esc_html( adstxt_minimum_php_requirement() )
					);
					?>
				</p>
			</div>
			<?php
		}
	);
	return;
}

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/post-type.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/save.php';

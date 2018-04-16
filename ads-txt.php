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

namespace Adstxt;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
define( 'ADSTXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/inc/class-admin.php';
require_once __DIR__ . '/inc/class-plugin.php';

new Plugin();
new Admin();

<?php

namespace AdsTxt;

/**
 * Add admin menu page
 * @return void
 */
function admin_menu() {
	add_options_page( 'Ads.txt', 'Ads.txt', 'manage_options', 'adstxt-settings', __NAMESPACE__ . '\settings_screen' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );

function settings_screen() {
?>

<div class="wrap">
	<h2>Ads.txt</h2>
</div>

<?php
}

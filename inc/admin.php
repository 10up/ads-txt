<?php

namespace AdsTxt;

/**
 * Add admin menu page
 * @return void
 */
function admin_menu() {
	add_options_page( __( 'Ads.txt', 'adstxt' ), __( 'Ads.txt', 'adstxt' ), 'manage_options', 'adstxt-settings', __NAMESPACE__ . '\settings_screen' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );

/**
 * Output the settings screen
 * @return void
 */
function settings_screen() {
	$setting = get_option( 'adstxt' );
?>

<div class="wrap">
	<h2><?php _e( 'Ads.txt', 'adstxt' ); ?></h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'adstxt' ); ?>
		<?php settings_errors(); ?>

		<p class="description"><?php _e( 'COPY: Ads.txt is a root-level file, etc.', 'adstxt' ); ?></p>

		<textarea class="widefat"><?php echo esc_textarea( $setting ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
		</p>
	</form>
</div>

<?php
}

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

		<p class="description"><?php _e( 'COPY: Ads.txt is a root-level file, etc.', 'adstxt' ); ?></p>

		<textarea class="widefat" name="adstxt"><?php echo esc_textarea( $setting ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
		</p>
	</form>
</div>

<?php
}

/**
 * Register setting
 * @return void
 */
function admin_init() {
	$args = array(
		'type' => 'string',
		'description' => 'Contents of ads.txt',
		'sanitize_callback' =>  __NAMESPACE__ . '\sanitize_setting'
	);

	register_setting( 'adstxt', 'adstxt', $args );
}
add_action( 'admin_init', __NAMESPACE__ . '\admin_init' );

/**
 * Sanitize setting for saving
 * @param  array $setting Posted settings
 * @return array          Sanitized settings
 */
function sanitize_setting( $setting ) {
	$sanitized = sanitize_textarea_field( $setting );
	return $sanitized;
}

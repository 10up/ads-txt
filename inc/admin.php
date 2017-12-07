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
// This needs to change to retrieving the latest published post
// Also need to display errors based on meta key
// It's okay if they display again if they leave and come back, I think
	$setting = get_option( 'adstxt' );
?>

<div class="wrap">
	<h2><?php _e( 'Ads.txt', 'adstxt' ); ?></h2>

	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="action" value="adstxt-save" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<p class="description"><?php _e( 'COPY: Ads.txt is a root-level file, etc.', 'adstxt' ); ?></p>

		<textarea class="widefat" rows="25" name="adstxt"><?php echo esc_textarea( $setting ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
		</p>
	</form>
</div>

<?php
}

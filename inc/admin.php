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
	$post_id = get_option( 'adstxt_post' );

	if ( $post_id ) {
		$post = get_post( $post_id );
	}

	$content = isset( $post->post_content ) ? $post->post_content : '';

// Also need to display errors based on meta key
// It's okay if they display again if they leave and come back, I think
?>

<div class="wrap">
	<h2><?php _e( 'Ads.txt', 'adstxt' ); ?></h2>

	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
		<input type="hidden" name="action" value="adstxt-save" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<p class="description"><?php _e( 'COPY: Ads.txt is a root-level file, etc.', 'adstxt' ); ?></p>

		<textarea class="widefat code" rows="25" name="adstxt"><?php echo esc_textarea( $content ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
		</p>
	</form>
</div>

<?php
}

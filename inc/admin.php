<?php

namespace AdsTxt;

function admin_enqueue_scripts( $hook ) {
	if ( 'settings_page_adstxt-settings' !== $hook ) {
		return;
	}

	wp_enqueue_script( 'adstxt', plugins_url( '/js/admin.js', dirname( __FILE__ ) ), array( 'jquery', 'wp-backbone' ), false, true );

	$strings = array(
		'saved'         => __( 'Ads.txt saved', 'adstxt' ),
		'error_intro'   => __( 'Your Ads.txt contains the following issues:', 'adstxt' ),
		'unknown_error' => __( 'Unknown error.', 'adstxt' ),
		'ays'           => __( 'Update anyway, even though it may adversely affect your ads?', 'adstxt' ),
	);

	wp_localize_script( 'adstxt', 'adstxt', $strings );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts' );

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

	$errors = get_post_meta( $post->ID, 'adstxt_errors', true );

// Also need to display errors based on meta key
// It's okay if they display again if they leave and come back, I think
?>

<div class="wrap">
<?php if ( ! empty( $errors ) ) : ?>
	<div class="notice notice-error adstxt-errors">
		<p><strong><?php _e( 'Your Ads.txt contains the following issues:', 'adstxt' ); ?></strong></p>
		<ul>
			<?php foreach( $errors as $error ) {
				echo '<li class="' . $error['type'] . '">' . format_error( $error ) . '</li>';
			} ?>
		</ul>
	</div>
<?php endif; ?>

	<h2><?php _e( 'Ads.txt', 'adstxt' ); ?></h2>

	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
		<input type="hidden" name="action" value="adstxt-save" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<label class="screen-reader-text" for="adstxt_content"><?php _e( 'Ads.txt content', 'adstxt' ); ?></label>
		<textarea class="widefat code" rows="25" name="adstxt" id="adstxt_content"><?php echo esc_textarea( $content ); ?></textarea>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
			<span class="spinner" style="float:none;vertical-align:top"></span>
		</p>
	</form>
</div>

<?php
}

/**
 * Take an error array and turn it into a message.
 * @param  array $error Array of error message components
 * @return string       Formatted error message
 */
function format_error( $error ) {
	/* translators: Error message output. 1: Line number, 2: Error message */
	$message = sprintf(
		__( 'Line %1$s: %2$s', 'adstxt' ),
		$error['line'],
		$error['message']
	);

	return $message;
}

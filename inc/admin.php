<?php

namespace AdsTxt;

/**
 * Enqueue any necessary scripts.
 *
 * @param  string $hook Hook name for the current screen.
 *
 * @return void
 */
function admin_enqueue_scripts( $hook ) {
	if ( 'settings_page_adstxt-settings' !== $hook ) {
		return;
	}

	wp_enqueue_script( 'adstxt', plugins_url( '/js/admin.js', dirname( __FILE__ ) ), array( 'jquery', 'wp-backbone', 'wp-codemirror' ), false, true );
	wp_enqueue_style( 'code-editor' );

	$strings = array(
		'saved'         => __( 'Ads.txt saved', 'ads-txt' ),
		'error_intro'   => __( 'Your Ads.txt contains the following issues:', 'ads-txt' ),
		'unknown_error' => __( 'Unknown error.', 'ads-txt' ),
	);

	wp_localize_script( 'adstxt', 'adstxt', $strings );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts' );

/**
 * Add admin menu page.
 *
 * @return void
 */
function admin_menu() {
	add_options_page( __( 'Ads.txt', 'ads-txt' ), __( 'Ads.txt', 'ads-txt' ), 'manage_options', 'adstxt-settings', __NAMESPACE__ . '\settings_screen' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );

/**
 * Output the settings screen.
 *
 * @return void
 */
function settings_screen() {
	$post_id = get_option( 'adstxt_post' );
	$post    = false;
	$content = false;

	if ( $post_id ) {
		$post = get_post( $post_id );
		$content = isset( $post->post_content ) ? $post->post_content : '';
		$errors = get_post_meta( $post->ID, 'adstxt_errors', true );
	}
?>
<div class="wrap">
<?php if ( ! empty( $errors ) ) : ?>
	<div class="notice notice-error adstxt-notice">
		<p><strong><?php echo esc_html( __( 'Your Ads.txt contains the following issues:', 'ads-txt' ) ); ?></strong></p>
		<ul>
			<?php
			foreach ( $errors as $error ) {
				echo '<li class="' . esc_attr( $error['type'] ) . '">' . esc_html( format_error( $error ) ) . '</li>';
			}
			?>
		</ul>
	</div>
<?php endif; ?>

	<h2><?php echo esc_html( __( 'Manage Ads.txt', 'ads-txt' ) ); ?></h2>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="post_id" value="<?php echo ( $post ? esc_attr( $post->ID ) : '' ); ?>" />
		<input type="hidden" name="action" value="adstxt-save" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<label class="screen-reader-text" for="adstxt_content"><?php echo esc_html( __( 'Ads.txt content', 'ads-txt' ) ); ?></label>
		<textarea class="widefat code" rows="25" name="adstxt" id="adstxt_content"><?php echo esc_textarea( $content ); ?></textarea>

		<div id="adstxt-notification-area"></div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( 'Save Changes' ); ?>">
			<span class="spinner" style="float:none;vertical-align:top"></span>
		</p>

	</form>

	<script type="text/template" id="tmpl-adstext-notice">
		<div class="notice notice-{{ data.class }} adstxt-notice">
			<p><strong>{{ data.message }}</strong></p>
			<# if ( data.errors ) { #>
			<ul class="adstxt-errors-items">
			<# _.each( data.errors, function( error ) { #>
				<li>{{ error }}.</li>
			<# } ); #>
			</ul>
			<# } #>
		</div>
		<# if ( data.errors ) { #>
		<p class="adstxt-ays">
			<input id="adstxt-ays-checkbox" name="adstxt_ays" type="checkbox" value="y" />
			<label for="adstxt-ays-checkbox">
				<?php _e( 'Update anyway, even though it may adversely affect your ads?', 'ads-txt' ); ?>
			</label>
		</p>
		<# } #>
	</script>
</div>

<?php
}

/**
 * Take an error array and turn it into a message.
 *
 * @param  array $error {
 *     Array of error message components.
 *
 *     @type string $type    Type of error. Typically 'warning' or 'error'.
 *     @type int    $line    Line number of the error.
 *     @type string $message Error message.
 * }
 *
 * @return string       Formatted error message.
 */
function format_error( $error ) {
	/* translators: Error message output. 1: Line number, 2: Error message */
	$message = sprintf(
		__( 'Line %1$s: %2$s', 'ads-txt' ),
		$error['line'],
		$error['message']
	);

	return $message;
}

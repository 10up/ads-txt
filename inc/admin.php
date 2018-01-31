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

	wp_enqueue_script( 'adstxt', esc_url( plugins_url( '/js/admin.js', dirname( __FILE__ ) ) ), array( 'jquery', 'wp-backbone', 'wp-codemirror' ), false, true );
	wp_enqueue_style( 'code-editor' );

	$strings = array(
		'saved_message' => esc_html__( 'Ads.txt saved', 'ads-txt' ),
		'error_message' => esc_html__( 'Your Ads.txt contains the following issues:', 'ads-txt' ),
		'unknown_error' => esc_html__( 'An unknown error occurred.', 'ads-txt' ),
	);

	wp_localize_script( 'adstxt', 'adstxt', $strings );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts' );

/**
 * Output some CSS directly in the head of the document.
 *
 * Should there ever be more than ~25 lines of CSS, this should become a separate file.
 *
 * @return void
 */
function admin_head_css() {
?>
<style>
.CodeMirror {
	width: 100%;
	min-height: 60vh;
	height: calc( 100vh - 295px );
	border: 1px solid #ddd;
	box-sizing: border-box;
}
</style>
<?php
}
add_action( 'admin_head-settings_page_adstxt-settings', __NAMESPACE__ . '\admin_head_css' );

/**
 * Add admin menu page.
 *
 * @return void
 */
function admin_menu() {
	add_options_page( esc_html__( 'Ads.txt', 'ads-txt' ), esc_html__( 'Ads.txt', 'ads-txt' ), 'manage_options', 'adstxt-settings', __NAMESPACE__ . '\settings_screen' );
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
	$errors  = [];

	if ( $post_id ) {
		$post = get_post( $post_id );
	}

	if ( is_a( $post, 'WP_Post' ) ) {
		$content = $post->post_content;
		$errors  = get_post_meta( $post->ID, 'adstxt_errors', true );
	}
?>
<div class="wrap">
<?php if ( ! empty( $errors ) ) : ?>
	<div class="notice notice-error adstxt-notice">
		<p><strong><?php echo esc_html__( 'Your Ads.txt contains the following issues:', 'ads-txt' ); ?></strong></p>
		<ul>
			<?php
			foreach ( $errors as $error ) {
				echo '<li>';

				// Errors were originally stored as an array
				// This old style only needs to be accounted for here at runtime display
				if ( isset( $error['message'] ) ) {
					$message = sprintf(
						/* translators: Error message output. 1: Line number, 2: Error message */
						__( 'Line %1$s: %2$s', 'ads-txt' ),
						$error['line'],
						$error['message']
					);

					echo esc_html( $message );
				} else {
					/*
					 * Important: This is escaped piece-wise inside `format_error()`,
					 * as we cannot do absolute-end late escaping as normally recommended.
					 * This is because the placeholders in the translations can contain HTML,
					 * namely escaped data values wrapped in code tags.
					 * We don't have good JS translation tools yet and it's better to avoid duplication,
					 * so we use a single PHP function for both the JS template and in PHP.
					 */
					echo format_error( $error ); // WPCS: XSS ok.
				}

				echo  '</li>';
			}
			?>
		</ul>
	</div>
<?php endif; ?>

	<h2><?php echo esc_html__( 'Manage Ads.txt', 'ads-txt' ); ?></h2>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="post_id" value="<?php echo ( is_a( $post, 'WP_Post' ) ? esc_attr( $post->ID ) : '' ); ?>" />
		<input type="hidden" name="action" value="adstxt-save" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<label class="screen-reader-text" for="adstxt_content"><?php echo esc_html__( 'Ads.txt content', 'ads-txt' ); ?></label>
		<textarea class="widefat code" rows="25" name="adstxt" id="adstxt_content"><?php echo esc_textarea( $content ); ?></textarea>

		<div id="adstxt-notification-area"></div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( 'Save Changes' ); ?>">
			<span class="spinner" style="float:none;vertical-align:top"></span>
		</p>

	</form>

	<script type="text/template" id="tmpl-adstext-notice">
		<# if ( ! _.isUndefined( data.saved ) ) { #>
		<div class="notice notice-success adstxt-notice adstxt-saved">
			<p>{{ data.saved.saved_message }}</p>
		</div>
		<# } #>

		<# if ( ! _.isUndefined( data.errors ) ) { #>
		<div class="notice notice-error adstxt-notice adstxt-errors">
			<p><strong>{{ data.errors.error_message }}</strong></p>
			<# if ( ! _.isUndefined( data.errors.errors ) ) { #>
			<ul class="adstxt-errors-items">
			<# _.each( data.errors.errors, function( error ) { #>
				<?php foreach ( array_keys( get_error_messages() ) as $error_type ) : ?>
				<# if ( "<?php echo esc_html( $error_type ); ?>" === error.type ) { #>
					<li>
						<?php
						/*
						 * Important: This is escaped piece-wise inside `format_error()`,
						 * as we cannot do absolute-end late escaping as normally recommended.
						 * This is because the placeholders in the translations can contain HTML,
						 * namely escaped data values wrapped in code tags.
						 * We don't have good JS translation tools yet and it's better to avoid duplication,
						 * so we have to get them already-translated from PHP.
						 */
						echo format_error( array( // WPCS: XSS ok.
							'line'  => '{{error.line}}',
							'type'  => $error_type,
							'value' => '{{error.value}}',
						) );
						?>
					</li>
				<# } #>
				<?php endforeach; ?>
			<# } ); #>
			</ul>
			<# } #>
		</div>

		<# if ( _.isUndefined( data.saved ) && ! _.isUndefined( data.errors.errors ) ) { #>
		<p class="adstxt-ays">
			<input id="adstxt-ays-checkbox" name="adstxt_ays" type="checkbox" value="y" />
			<label for="adstxt-ays-checkbox">
				<?php esc_html_e( 'Update anyway, even though it may adversely affect your ads?', 'ads-txt' ); ?>
			</label>
		</p>
		<# } #>

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
 *     @type int    $line    Line number of the error.
 *     @type string $type    Type of error.
 *     @type string $value   Optional. Value in question.
 * }
 *
 * @return string       Formatted error message.
 */
function format_error( $error ) {
	$messages = get_error_messages();

	if ( ! isset( $messages[ $error['type'] ] ) ) {
		return __( 'Unknown error', 'adstxt' );
	}

	if ( ! isset( $error['value'] ) ) {
		$error['value'] = '';
	}

	$message = sprintf( esc_html( $messages[ $error['type'] ] ), '<code>' . esc_html( $error['value'] ) . '</code>' );

	$message = sprintf(
		/* translators: Error message output. 1: Line number, 2: Error message */
		__( 'Line %1$s: %2$s', 'ads-txt' ),
		esc_html( $error['line'] ),
		$message // This is escaped piece-wise above and may contain HTML (code tags) at this point
	);

	return $message;
}

/**
 * Get all non-generic error messages, translated and with placeholders intact.
 *
 * @return array Associative array of error messages.
 */
function get_error_messages() {
	$messages = array(
		'invalid_variable'     => __( 'Unrecognized variable' ),
		'invalid_record'       => __( 'Invalid record' ),
		'invalid_account_type' => __( 'Third field should be RESELLER or DIRECT' ),
		/* translators: %s: Subdomain */
		'invalid_subdomain'    => __( '%s does not appear to be a valid subdomain' ),
		/* translators: %s: Exchange domain */
		'invalid_exchange'     => __( '%s does not appear to be a valid exchange domain' ),
		/* translators: %s: Alphanumeric TAG-ID */
		'invalid_tagid'        => __( '%s does not appear to be a valid TAG-ID' ),
	);

	return $messages;
}

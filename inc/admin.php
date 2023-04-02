<?php
/**
 * Admin functionality for Ads.txt.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt;

/**
 * Enqueue any necessary scripts.
 *
 * @param  string $hook Hook name for the current screen.
 *
 * @return void
 */
function admin_enqueue_scripts( $hook ) {
	if ( ! preg_match( '/adstxt-settings$/', $hook ) ) {
		return;
	}

	wp_enqueue_script(
		'adstxt',
		esc_url( plugins_url( '/js/admin.js', dirname( __FILE__ ) ) ),
		array( 'jquery', 'wp-backbone', 'wp-codemirror' ),
		ADS_TXT_MANAGER_VERSION,
		true
	);
	wp_enqueue_style( 'code-editor' );
	wp_enqueue_style(
		'adstxt',
		esc_url( plugins_url( '/css/admin.css', dirname( __FILE__ ) ) ),
		array(),
		ADS_TXT_MANAGER_VERSION
	);

	$strings = array(
		'error_message' => esc_html__( 'Your Ads.txt contains the following issues:', 'ads-txt' ),
		'unknown_error' => esc_html__( 'An unknown error occurred.', 'ads-txt' ),
	);

	if ( 'settings_page_app-adstxt-settings' === $hook ) {
		$strings['error_message'] = esc_html__( 'Your app-ads.txt contains the following issues:', 'ads-txt' );
	}

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
add_action( 'admin_head-settings_page_app-adstxt-settings', __NAMESPACE__ . '\admin_head_css' );

/**
 * Appends a query argument to the edit url to make sure it is redirected to
 * the ads.txt screen.
 *
 * @since 1.2.0
 *
 * @param string $url Edit url.
 * @return string Edit url.
 */
function ads_txt_adjust_revisions_return_to_editor_link( $url ) {
	global $pagenow, $post;

	if ( 'revision.php' !== $pagenow || ! isset( $_REQUEST['adstxt'] ) ) { // @codingStandardsIgnoreLine Nonce not required.
		return $url;
	}

	$type = 'adstxt';

	if ( 'app-adstxt' === $post->post_type ) {
		$type = 'app-adstxt';
	}

	return admin_url( 'options-general.php?page=' . $type . '-settings' );
}
add_filter( 'get_edit_post_link', __NAMESPACE__ . '\ads_txt_adjust_revisions_return_to_editor_link' );

/**
 * Modifies revisions data to preserve adstxt argument used in determining
 * where to redirect user returning to editor.
 *
 * @since 1.9.0
 *
 * @param array $revisions_data The bootstrapped data for the revisions screen.
 * @return array Modified bootstrapped data for the revisions screen.
 */
function adstxt_revisions_restore( $revisions_data ) {
	if ( isset( $_REQUEST['adstxt'] ) ) { // @codingStandardsIgnoreLine Nonce not required.
		$revisions_data['restoreUrl'] = add_query_arg(
			'adstxt',
			1,
			$revisions_data['restoreUrl']
		);
	}

	return $revisions_data;
}
add_filter( 'wp_prepare_revision_for_js', __NAMESPACE__ . '\adstxt_revisions_restore' );

/**
 * Hide the revisions title with CSS, since WordPress always shows the title
 * field even if unchanged, and the title is not relevant for ads.txt.
 */
function admin_header_revisions_styles() {
	$current_screen = get_current_screen();

	if ( ! $current_screen || 'revision' !== $current_screen->id ) {
		return;
	}

	if ( ! isset( $_REQUEST['adstxt'] ) ) { // @codingStandardsIgnoreLine Nonce not required.
		return;
	}

	?>
	<style>
		.revisions-diff .diff h3 {
			display: none;
		}
		.revisions-diff .diff table.diff:first-of-type {
			display: none;
		}
	</style>
	<?php

}
add_action( 'admin_head', __NAMESPACE__ . '\admin_header_revisions_styles' );

/**
 * Add admin menu page.
 *
 * @return void
 */
function admin_menu() {
	add_options_page(
		esc_html__( 'Ads.txt', 'ads-txt' ),
		esc_html__( 'Ads.txt', 'ads-txt' ),
		ADS_TXT_MANAGE_CAPABILITY,
		'adstxt-settings',
		__NAMESPACE__ . '\adstxt_settings_screen'
	);

	add_options_page(
		esc_html__( 'App-ads.txt', 'ads-txt' ),
		esc_html__( 'App-ads.txt', 'ads-txt' ),
		ADS_TXT_MANAGE_CAPABILITY,
		'app-adstxt-settings',
		__NAMESPACE__ . '\app_adstxt_settings_screen'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\admin_menu' );

/**
 * Set up settings screen for ads.txt.
 *
 * @return void
 */
function adstxt_settings_screen() {
	$post_id = get_option( ADS_TXT_MANAGER_POST_OPTION );

	$strings = array(
		'existing'      => __( 'Existing Ads.txt file found', 'ads-txt' ),
		'precedence'    => __( 'An ads.txt file on the server will take precedence over any content entered here. You will need to rename or remove the existing ads.txt file before you will be able to see any changes you make on this screen.', 'ads-txt' ),
		'errors'        => __( 'Your Ads.txt contains the following issues:', 'ads-txt' ),
		'screen_title'  => __( 'Manage Ads.txt', 'ads-txt' ),
		'content_label' => __( 'Ads.txt content', 'ads-txt' ),
	);

	$args = array(
		'post_type'  => 'adstxt',
		'post_title' => 'Ads.txt',
		'option'     => ADS_TXT_MANAGER_POST_OPTION,
		'action'     => 'adstxt-save',
	);

	settings_screen( $post_id, $strings, $args );
}

/**
 * Set up settings screen for app-ads.txt.
 *
 * @return void
 */
function app_adstxt_settings_screen() {
	$post_id = get_option( APP_ADS_TXT_MANAGER_POST_OPTION );

	$strings = array(
		'existing'      => __( 'Existing App-ads.txt file found', 'ads-txt' ),
		'precedence'    => __( 'An app-ads.txt file on the server will take precedence over any content entered here. You will need to rename or remove the existing app-ads.txt file before you will be able to see any changes you make on this screen.', 'ads-txt' ),
		'errors'        => __( 'Your app-ads.txt contains the following issues:', 'ads-txt' ),
		'screen_title'  => __( 'Manage App-ads.txt', 'ads-txt' ),
		'content_label' => __( 'App-ads.txt content', 'ads-txt' ),
	);

	$args = array(
		'post_type'  => 'app-adstxt',
		'post_title' => 'App-ads.txt',
		'option'     => APP_ADS_TXT_MANAGER_POST_OPTION,
		'action'     => 'app-adstxt-save',
	);

	settings_screen( $post_id, $strings, $args );
}

/**
 * Output the settings screen for both files.
 *
 * @param int   $post_id Post ID associated with the file.
 * @param array $strings Translated strings that mention the specific file name.
 * @param array $args    Array of other necessary information to appropriately name items.
 *
 * @return void
 */
function settings_screen( $post_id, $strings, $args ) {
	$post             = false;
	$content          = false;
	$errors           = array();
	$revision_count   = 0;
	$last_revision_id = false;

	if ( $post_id ) {
		$post = get_post( $post_id );
	}

	if ( is_a( $post, 'WP_Post' ) ) {
		$content          = $post->post_content;
		$revisions        = wp_get_post_revisions( $post->ID );
		$revision_count   = count( $revisions );
		$last_revision    = array_shift( $revisions );
		$last_revision_id = $last_revision ? $last_revision->ID : false;
		$errors           = get_post_meta( $post->ID, 'adstxt_errors', true );
		$revisions_link   = $last_revision_id ? admin_url( 'revision.php?adstxt=1&revision=' . $last_revision_id ) : false;

	} else {

		// Create an initial post so the second save creates a comparable revision.
		$postarr = array(
			'post_title'   => $args['post_title'],
			'post_content' => '',
			'post_type'    => $args['post_type'],
			'post_status'  => 'publish',
		);

		$post_id = wp_insert_post( $postarr );
		if ( $post_id ) {
			update_option( $args['option'], $post_id );
		}
	}
	?>
<div class="wrap">
	<?php if ( ! empty( $errors ) ) : ?>
	<div class="notice notice-error adstxt-notice">
		<p><strong><?php echo esc_html( $strings['errors'] ); ?></strong></p>
		<ul>
			<?php
			foreach ( $errors as $error ) {
				echo '<li>';

				// Errors were originally stored as an array.
				// This old style only needs to be accounted for here at runtime display.
				if ( isset( $error['message'] ) ) {
					$message = sprintf(
						/* translators: Error message output. 1: Line number, 2: Error message */
						__( 'Line %1$s: %2$s', 'ads-txt' ),
						$error['line'],
						$error['message']
					);

					echo esc_html( $message );
				} else {
					display_formatted_error( $error ); // WPCS: XSS ok.
				}

				echo '</li>';
			}
			?>
		</ul>
	</div>
	<?php endif; ?>

	<h2><?php echo esc_html( $strings['screen_title'] ); ?></h2>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="adstxt-settings-form">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ) ? esc_attr( $post_id ) : ''; ?>" />
		<input type="hidden" name="adstxt_type" value="<?php echo esc_attr( $args['post_type'] ); ?>" />
		<input type="hidden" name="action" value="<?php echo esc_attr( $args['action'] ); ?>" />
		<?php wp_nonce_field( 'adstxt_save' ); ?>

		<label class="screen-reader-text" for="adstxt_content"><?php echo esc_html( $strings['content_label'] ); ?></label>
		<textarea class="widefat code" rows="25" name="adstxt" id="adstxt_content"><?php echo esc_textarea( $content ); ?></textarea>
		<?php
		if ( $revision_count > 1 ) {
			?>
			<div class="misc-pub-section misc-pub-revisions">
			<?php
				echo wp_kses_post(
					sprintf(
						/* translators: Post revisions heading. 1: The number of available revisions */
						__( 'Revisions: <span class="adstxt-revision-count">%s</span>', 'ads-txt' ),
						number_format_i18n( $revision_count )
					)
				);
			?>
				<a class="hide-if-no-js" href="<?php echo esc_url( $revisions_link ); ?>">
					<span aria-hidden="true">
						<?php echo esc_html( __( 'Browse', 'ads-txt' ) ); ?>
					</span> <span class="screen-reader-text">
						<?php echo esc_html( __( 'Browse revisions', 'ads-txt' ) ); ?>
					</span>
				</a>
		</div>
			<?php
		}
		?>
		<div id="adstxt-notification-area"></div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( 'Save Changes' ); ?>">
			<span class="spinner" style="float:none;vertical-align:top"></span>
		</p>

	</form>

	<script type="text/template" id="tmpl-adstext-notice">
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
						display_formatted_error(
							array(
								'line'  => '{{error.line}}',
								'type'  => $error_type,
								'value' => '{{error.value}}',
							)
						);
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
 * Take an error array and output it as a message.
 *
 * @param  array $error {
 *     Array of error message components.
 *
 *     @type int    $line    Line number of the error.
 *     @type string $type    Type of error.
 *     @type string $value   Optional. Value in question.
 * }
 *
 * @return string|void
 */
function display_formatted_error( $error ) {
	$messages = get_error_messages();

	if ( ! isset( $messages[ $error['type'] ] ) ) {
		return __( 'Unknown error', 'adstxt' );
	}

	if ( ! isset( $error['value'] ) ) {
		$error['value'] = '';
	}

	$message = sprintf( esc_html( $messages[ $error['type'] ] ), '<code>' . esc_html( $error['value'] ) . '</code>' );

	printf(
		/* translators: Error message output. 1: Line number, 2: Error message */
		esc_html__( 'Line %1$s: %2$s', 'ads-txt' ),
		esc_html( $error['line'] ),
		wp_kses_post( $message )
	);
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

/**
 * Maybe display admin notices on the Ads.txt settings page.
 *
 * @return void
 */
function admin_notices() {
	if ( 'settings_page_adstxt-settings' === get_current_screen()->base ) {
		$saved = __( 'Ads.txt saved', 'ads-txt' );
	} elseif ( 'settings_page_app-adstxt-settings' === get_current_screen()->base ) {
		$saved = __( 'App-ads.txt saved', 'ads-txt' );
	} else {
		return;
	}

	if ( isset( $_GET['ads_txt_saved'] ) ) : // @codingStandardsIgnoreLine Nonce not required.
		?>
	<div class="notice notice-success adstxt-notice adstxt-saved">
		<p><?php echo esc_html( $saved ); ?></p>
	</div>
		<?php
	elseif ( isset( $_GET['revision'] ) ) : // @codingStandardsIgnoreLine Nonce not required.
		?>
	<div class="notice notice-success adstxt-notice adstxt-saved">
		<p><?php echo esc_html__( 'Revision restored', 'ads-txt' ); ?></p>
	</div>
		<?php
	endif;
}
add_action( 'admin_notices', __NAMESPACE__ . '\admin_notices' );

<?php

/**
 * class Admin
 * This will handle admin related actions
 */

namespace Adstxt;

class Admin {

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_head-settings_page_adstxt-settings', [ $this, 'admin_head_css' ] );
		add_action( 'admin_post_adstxt-save', [ $this, 'save' ] );
		add_action( 'wp_ajax_adstxt-save', [ $this, 'save' ] );
		add_action( 'init', [ $this, 'register' ] );

	}

	/**
	 * Process and save the ads.txt data.
	 *
	 * Handles both AJAX and POST saves via `admin-ajax.php` and `admin-post.php` respectively.
	 * AJAX calls output JSON; POST calls redirect back to the Ads.txt edit screen.
	 *
	 * @return void
	 */
	public function save() {
		current_user_can( 'customize' ) || die;
		check_admin_referer( 'adstxt_save' );
		$_post      = stripslashes_deep( $_POST );
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		$post_id = $_post['post_id'];
		$ays     = isset( $_post['adstxt_ays'] ) ? $_post['adstxt_ays'] : null;

		// Different browsers use different line endings.
		$lines     = preg_split( '/\r\n|\r|\n/', $_post['adstxt'] );
		$sanitized = array();
		$errors    = array();
		$response  = array();

		foreach ( $lines as $i => $line ) {
			$line_number = $i + 1;
			$result      = $this->validate_line( $line, $line_number );

			$sanitized[] = $result['sanitized'];
			if ( ! empty( $result['errors'] ) ) {
				$errors = array_merge( $errors, $result['errors'] );
			}
		}

		$sanitized = implode( PHP_EOL, $sanitized );

		$postarr = array(
			'ID'           => $post_id,
			'post_title'   => 'Ads.txt',
			'post_content' => $sanitized,
			'post_type'    => 'adstxt',
			'post_status'  => 'publish',
			'meta_input'   => array(
				'adstxt_errors' => $errors,
			),
		);

		if ( ! $doing_ajax || empty( $errors ) || 'y' === $ays ) {
			$post_id = wp_insert_post( $postarr );

			if ( $post_id ) {
				update_option( 'adstxt_post', $post_id );
				$response['saved'] = true;
			}
		}

		if ( $doing_ajax ) {
			$response['sanitized'] = $sanitized;

			if ( ! empty( $errors ) ) {
				$response['errors'] = $errors;
			}

			echo wp_json_encode( $response );
			die();
		}

		wp_safe_redirect( esc_url_raw( $_post['_wp_http_referer'] ) . '&updated=true' );
		exit;
	}

	/**
	 * Output some CSS directly in the head of the document.
	 *
	 * Should there ever be more than ~25 lines of CSS, this should become a separate file.
	 *
	 * @return void
	 */
	public function admin_head_css() {
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

	/**
	 * Enqueue any necessary scripts.
	 *
	 * @param  string $hook Hook name for the current screen.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
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

	/**
	 * Add admin menu page.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_options_page( esc_html__( 'Ads.txt', 'ads-txt' ), esc_html__( 'Ads.txt', 'ads-txt' ), 'manage_options', 'adstxt-settings', array( $this, 'settings_screen' ) );
	}

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
		include( ADSTXT_PLUGIN_DIR . 'templates/admin.tpl.php' );

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
	public function format_error( $error ) {
		$messages = $this->get_error_messages();

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


	/**
	 * Validate a single line.
	 *
	 * @param string $line        The line to validate.
	 * @param string $line_number The line number being evaluated.
	 *
	 * @return array {
	 *     @type string $sanitized Sanitized version of the original line.
	 *     @type array  $errors    Array of errors associated with the line.
	 * }
	 */
	function validate_line( $line, $line_number ) {
		$domain_regex = '/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/i';
		$errors       = array();

		if ( empty( $line ) ) {
			$sanitized = '';
		} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment.
			$sanitized = wp_strip_all_tags( $line );
		} elseif ( 1 < strpos( $line, '=' ) ) { // This is a variable declaration.
			// The spec currently supports CONTACT and SUBDOMAIN.
			if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'invalid_variable',
				);
			} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains.
				// Disregard any comments.
				$subdomain = explode( '#', $line );
				$subdomain = $subdomain[0];

				$subdomain = explode( '=', $subdomain );
				array_shift( $subdomain );

				// If there's anything other than one piece left something's not right.
				if ( 1 !== count( $subdomain ) || ! preg_match( $domain_regex, $subdomain[0] ) ) {
					$subdomain = implode( '', $subdomain );
					$errors[]  = array(
						'line'  => $line_number,
						'type'  => 'invalid_subdomain',
						'value' => $subdomain,
					);
				}
			}

			$sanitized = wp_strip_all_tags( $line );

			unset( $subdomain );
		} else { // Data records: the most common.
			// Disregard any comments.
			$record = explode( '#', $line );
			$record = $record[0];

			// Record format: example.exchange.com,pub-id123456789,RESELLER|DIRECT,tagidhash123(optional).
			$fields = explode( ',', $record );

			if ( 3 <= count( $fields ) ) {
				$exchange     = trim( $fields[0] );
				$pub_id       = trim( $fields[1] );
				$account_type = trim( $fields[2] );

				if ( ! preg_match( $domain_regex, $exchange ) ) {
					$errors[] = array(
						'line'  => $line_number,
						'type'  => 'invalid_exchange',
						'value' => $exchange,
					);
				}

				if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $account_type ) ) {
					$errors[] = array(
						'line' => $line_number,
						'type' => 'invalid_account_type',
					);
				}

				if ( isset( $fields[3] ) ) {
					$tag_id = trim( $fields[3] );

					// TAG-IDs appear to be 16 character hashes.
					// TAG-IDs are meant to be checked against their DB - perhaps good for a service or the future.
					if ( ! empty( $tag_id ) && ! preg_match( '/^[a-f0-9]{16}$/', $tag_id ) ) {
						$errors[] = array(
							'line'  => $line_number,
							'type'  => 'invalid_tagid',
							'value' => $fields[3],
						);
					}
				}

				$sanitized = wp_strip_all_tags( $line );
			} else {
				// Not a comment, variable declaration, or data record; therefore, invalid.
				// Early on we commented the line out for safety but it's kind of a weird thing to do with a JS AYS.
				$sanitized = wp_strip_all_tags( $line );

				$errors[] = array(
					'line' => $line_number,
					'type' => 'invalid_record',
				);
			}

			unset( $record, $fields );
		}

		return array(
			'sanitized' => $sanitized,
			'errors'    => $errors,
		);
	}


	/**
	 * Register the `adstxt` custom post type.
	 *
	 * @return void
	 */
	public function register() {
		register_post_type(
			'adstxt', array(
				'labels'           => array(
					'name'          => esc_html_x( 'Ads.txt', 'post type general name', 'ads-txt' ),
					'singular_name' => esc_html_x( 'Ads.txt', 'post type singular name', 'ads-txt' ),
				),
				'public'           => false,
				'hierarchical'     => false,
				'rewrite'          => false,
				'query_var'        => false,
				'delete_with_user' => false,
				'supports'         => array( 'revisions' ),
				'map_meta_cap'     => true,
				'capabilities'     => array(
					'create_posts'           => 'customize',
					'delete_others_posts'    => 'customize',
					'delete_post'            => 'customize',
					'delete_posts'           => 'customize',
					'delete_private_posts'   => 'customize',
					'delete_published_posts' => 'customize',
					'edit_others_posts'      => 'customize',
					'edit_post'              => 'customize',
					'edit_posts'             => 'customize',
					'edit_private_posts'     => 'customize',
					'edit_published_posts'   => 'customize',
					'publish_posts'          => 'customize',
					'read'                   => 'read',
					'read_post'              => 'customize',
					'read_private_posts'     => 'customize',
				),
			)
		);
	}

}

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
						echo $this->format_error( $error ); // WPCS: XSS ok.
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
									<?php foreach ( array_keys($this->get_error_messages() ) as $error_type ) : ?>
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
											echo $this->format_error( array( // WPCS: XSS ok.
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
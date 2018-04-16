<?php

/**
 * class Plugin
 * This will handle rendering the ads.txt contents
 */

namespace Adstxt;

class Plugin {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'tenup_display_ads_txt' ] );

	}

	/**
	 * Display the contents of /ads.txt when requested.
	 *
	 * @return void
	 */
	public function tenup_display_ads_txt() {
		$request = esc_url_raw( $_SERVER['REQUEST_URI'] );
		if ( '/ads.txt' === $request ) {
			$post_id = get_option( 'adstxt_post' );

			// Will fall through if no option found, likely to a 404.
			if ( ! empty( $post_id ) ) {
				$post = get_post( $post_id );
				header( 'Content-Type: text/plain' );
				echo esc_html( $post->post_content );
				die();
			}
		}
	}

}

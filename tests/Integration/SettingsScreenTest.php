<?php
/**
 * Tests what loading a settings screen does to the stored posts.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Tests\Integration;

use WP_UnitTestCase;

/**
 * Each settings screen owns one post of its type and permanently deletes any others.
 */
class SettingsScreenTest extends WP_UnitTestCase {

	/**
	 * Provides each settings screen with the post type and option it manages.
	 *
	 * @return array[]
	 */
	public function data_screens(): array {
		return array(
			'ads.txt'     => array( 'AdsTxt\adstxt_settings_screen', 'adstxt', 'app-adstxt', ADS_TXT_MANAGER_POST_OPTION ),
			'app-ads.txt' => array( 'AdsTxt\app_adstxt_settings_screen', 'app-adstxt', 'adstxt', APP_ADS_TXT_MANAGER_POST_OPTION ),
		);
	}

	/**
	 * Renders a settings screen and discards its markup.
	 *
	 * @param callable $screen Screen callback.
	 * @return void
	 */
	private function render( callable $screen ): void {
		ob_start();
		$screen();
		ob_end_clean();
	}

	/**
	 * The first visit creates a published post and stores its ID as the managed post.
	 *
	 * @dataProvider data_screens
	 *
	 * @param callable $screen     Screen callback.
	 * @param string   $post_type  Post type the screen manages.
	 * @param string   $other_type The other file's post type.
	 * @param string   $option     Option holding the managed post ID.
	 * @return void
	 */
	public function test_first_visit_creates_the_managed_post( callable $screen, string $post_type, string $other_type, string $option ): void {
		$this->assertFalse( get_option( $option ) );

		$this->render( $screen );

		$post = get_post( (int) get_option( $option ) );
		$this->assertInstanceOf( \WP_Post::class, $post, 'The option must point at the created post.' );
		$this->assertSame( $post_type, $post->post_type );
		$this->assertSame( 'publish', $post->post_status );
	}

	/**
	 * A later visit keeps the managed post, force-deletes other posts of the same type
	 * and leaves every other post type alone.
	 *
	 * @dataProvider data_screens
	 *
	 * @param callable $screen     Screen callback.
	 * @param string   $post_type  Post type the screen manages.
	 * @param string   $other_type The other file's post type.
	 * @param string   $option     Option holding the managed post ID.
	 * @return void
	 */
	public function test_visit_deletes_only_orphans_of_its_own_type( callable $screen, string $post_type, string $other_type, string $option ): void {
		$managed      = self::factory()->post->create( array( 'post_type' => $post_type ) );
		$orphan       = self::factory()->post->create( array( 'post_type' => $post_type ) );
		$other_file   = self::factory()->post->create( array( 'post_type' => $other_type ) );
		$regular_post = self::factory()->post->create();
		update_option( $option, $managed );

		$this->render( $screen );

		$this->assertInstanceOf( \WP_Post::class, get_post( $managed ), 'The managed post must be kept.' );
		$this->assertNull( get_post( $orphan ), 'Other posts of the same type must be deleted.' );
		$this->assertInstanceOf( \WP_Post::class, get_post( $other_file ), 'The other file must not be touched.' );
		$this->assertInstanceOf( \WP_Post::class, get_post( $regular_post ), 'Other post types must not be touched.' );
		$this->assertSame( $managed, (int) get_option( $option ) );
	}
}

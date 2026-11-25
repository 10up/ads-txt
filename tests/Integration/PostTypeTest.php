<?php
/**
 * Tests the post types that store ads.txt and app-ads.txt content.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Tests\Integration;

use WP_UnitTestCase;

/**
 * The storage post types never expose their content on the front end.
 */
class PostTypeTest extends WP_UnitTestCase {

	/**
	 * Provides both storage post types.
	 *
	 * @return array[]
	 */
	public function data_post_types(): array {
		return array(
			'ads.txt'     => array( 'adstxt' ),
			'app-ads.txt' => array( 'app-adstxt' ),
		);
	}

	/**
	 * The post type is registered but can't be viewed or queried publicly.
	 *
	 * @dataProvider data_post_types
	 *
	 * @param string $post_type Post type name.
	 * @return void
	 */
	public function test_post_type_is_not_publicly_viewable( string $post_type ): void {
		$this->assertTrue( post_type_exists( $post_type ) );
		$this->assertFalse( is_post_type_viewable( $post_type ) );
		$this->assertNotContains( $post_type, get_post_types( array( 'publicly_queryable' => true ) ) );
	}
}

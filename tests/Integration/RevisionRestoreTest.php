<?php
/**
 * Tests restoring a previous version of a file.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Tests\Integration;

use WP_UnitTestCase;

/**
 * Restoring a revision drops the stored errors of the version it replaces.
 */
class RevisionRestoreTest extends WP_UnitTestCase {

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
	 * After a restore, the settings screen no longer reports the replaced version's errors.
	 *
	 * @dataProvider data_post_types
	 *
	 * @param string $post_type Post type name.
	 * @return void
	 */
	public function test_restoring_a_revision_clears_stored_errors( string $post_type ): void {
		$post_id = self::factory()->post->create( array( 'post_type' => $post_type ) );
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => 'example.com, pub-1, DIRECT',
			)
		);
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => 'invalid line',
				'meta_input'   => array(
					'adstxt_errors' => array(
						array(
							'line' => 1,
							'type' => 'invalid_record',
						),
					),
				),
			)
		);

		$valid_revisions = wp_list_filter( wp_get_post_revisions( $post_id ), array( 'post_content' => 'example.com, pub-1, DIRECT' ) );
		$this->assertNotEmpty( $valid_revisions, 'The valid version must have a revision to restore.' );
		wp_restore_post_revision( reset( $valid_revisions ) );

		$this->assertSame( 'example.com, pub-1, DIRECT', get_post( $post_id )->post_content );
		$this->assertSame( '', get_post_meta( $post_id, 'adstxt_errors', true ) );
	}
}

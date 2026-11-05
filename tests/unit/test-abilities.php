<?php
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Abilities API integration and the shared validation logic.
 */
class AbilitiesTests extends TestCase {
	/**
	 * Set up WP Mock.
	 *
	 * @return void
	 */
	public function setUp() : void {
		\WP_Mock::setUp();
		\WP_Mock::passthruFunction( 'wp_strip_all_tags' );
	}

	/**
	 * Tear down WP Mock.
	 *
	 * @return void
	 */
	public function tearDown() : void {
		\WP_Mock::tearDown();
	}

	public function test_validate_content_accepts_valid_file() {
		$content = "# comment\nexample.com, pub-123, DIRECT, f08c47fec0942fa0";
		$result  = AdsTxt\validate_content( $content );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array(), $result['warnings'] );
		$this->assertSame( str_replace( "\n", PHP_EOL, $content ), $result['sanitized'] );
	}

	public function test_validate_content_reports_errors_with_line_numbers() {
		$result = AdsTxt\validate_content( "example.com, pub-123, WRONG\nnot a record" );

		$this->assertSame(
			array(
				array(
					'line' => 1,
					'type' => 'invalid_account_type',
				),
				array(
					'line' => 2,
					'type' => 'invalid_record',
				),
			),
			$result['errors']
		);
	}

	public function test_validate_content_handles_any_line_ending() {
		$result = AdsTxt\validate_content( "# a\r# b\r\n# c" );

		$this->assertSame( implode( PHP_EOL, array( '# a', '# b', '# c' ) ), $result['sanitized'] );
	}

	public function test_permission_check_uses_manage_capability() {
		\WP_Mock::userFunction( 'current_user_can' )
			->with( ADS_TXT_MANAGE_CAPABILITY )
			->twice()
			->andReturn( true, false );

		$this->assertTrue( AdsTxt\Abilities\permission_check() );
		$this->assertFalse( AdsTxt\Abilities\permission_check() );
	}

	public function test_get_type_defaults_to_adstxt() {
		$this->assertSame( 'adstxt', AdsTxt\Abilities\get_type( null ) );
		$this->assertSame( 'adstxt', AdsTxt\Abilities\get_type( array( 'type' => 'other' ) ) );
		$this->assertSame( 'app-adstxt', AdsTxt\Abilities\get_type( array( 'type' => 'app-adstxt' ) ) );
	}

	public function test_execute_validate_returns_valid_flag() {
		$good = AdsTxt\Abilities\execute_validate( array( 'content' => 'CONTACT=me@example.com' ) );
		$bad  = AdsTxt\Abilities\execute_validate( array( 'content' => 'junk' ) );

		$this->assertTrue( $good['valid'] );
		$this->assertFalse( $bad['valid'] );
		$this->assertSame( 'invalid_record', $bad['errors'][0]['type'] );
	}

	public function test_execute_update_does_not_save_errors_without_force() {
		// Nothing may be written, so any call to wp_insert_post fails the test.
		\WP_Mock::userFunction( 'wp_insert_post' )->never();

		$result = AdsTxt\Abilities\execute_update( array( 'content' => 'junk' ) );

		$this->assertFalse( $result['saved'] );
		$this->assertNotEmpty( $result['errors'] );
	}

	/**
	 * Mock a stored post for a file type.
	 *
	 * @param string $option    Option name that holds the post ID.
	 * @param int    $post_id   Stored post ID. Zero means none.
	 * @param string $post_type Post type of the stored post.
	 */
	private function mock_stored_post( $option, $post_id, $post_type ) {
		\WP_Mock::userFunction( 'get_option' )->with( $option )->andReturn( $post_id );

		if ( $post_id ) {
			\WP_Mock::userFunction( 'get_post' )
				->with( $post_id )
				->andReturn( (object) array( 'post_type' => $post_type ) );
		}
	}

	public function test_get_stored_post_id_requires_matching_post_type() {
		$this->mock_stored_post( ADS_TXT_MANAGER_POST_OPTION, 12, 'app-adstxt' );

		$this->assertSame( 0, AdsTxt\Abilities\get_stored_post_id( 'adstxt' ) );
	}

	public function test_get_stored_post_id_returns_zero_when_nothing_saved() {
		$this->mock_stored_post( APP_ADS_TXT_MANAGER_POST_OPTION, 0, 'app-adstxt' );

		$this->assertSame( 0, AdsTxt\Abilities\get_stored_post_id( 'app-adstxt' ) );
	}

	public function test_execute_get_returns_empty_result_when_nothing_saved() {
		$this->mock_stored_post( ADS_TXT_MANAGER_POST_OPTION, 0, 'adstxt' );

		$this->assertSame(
			array(
				'type'     => 'adstxt',
				'content'  => '',
				'errors'   => array(),
				'warnings' => array(),
			),
			AdsTxt\Abilities\execute_get( array() )
		);
	}

	public function test_execute_get_returns_saved_content_and_meta() {
		$this->mock_stored_post( APP_ADS_TXT_MANAGER_POST_OPTION, 7, 'app-adstxt' );
		\WP_Mock::userFunction( 'get_post_field' )->with( 'post_content', 7, 'raw' )->andReturn( 'CONTACT=me@example.com' );
		\WP_Mock::userFunction( 'get_post_meta' )->with( 7, 'adstxt_errors', true )->andReturn( array( 3 => array( 'line' => 1, 'type' => 'invalid_record' ) ) );
		\WP_Mock::userFunction( 'get_post_meta' )->with( 7, 'adstxt_warnings', true )->andReturn( '' );

		$result = AdsTxt\Abilities\execute_get( array( 'type' => 'app-adstxt' ) );

		$this->assertSame( 'CONTACT=me@example.com', $result['content'] );
		$this->assertSame( array( array( 'line' => 1, 'type' => 'invalid_record' ) ), $result['errors'] );
		$this->assertSame( array(), $result['warnings'] );
	}

	public function test_execute_update_creates_post_and_stores_option_on_first_save() {
		$this->mock_stored_post( ADS_TXT_MANAGER_POST_OPTION, 0, 'adstxt' );
		\WP_Mock::userFunction( 'is_wp_error' )->andReturn( false );
		\WP_Mock::userFunction( 'wp_insert_post' )
			->once()
			->with(
				\Mockery::on(
					function ( $postarr ) {
						return 0 === $postarr['ID']
							&& 'adstxt' === $postarr['post_type']
							&& 'publish' === $postarr['post_status']
							&& 'CONTACT=me@example.com' === $postarr['post_content'];
					}
				),
				true
			)
			->andReturn( 55 );
		\WP_Mock::userFunction( 'update_option' )->once()->with( ADS_TXT_MANAGER_POST_OPTION, 55 );

		$result = AdsTxt\Abilities\execute_update( array( 'content' => 'CONTACT=me@example.com' ) );

		$this->assertTrue( $result['saved'] );
		$this->assertSame( array(), $result['errors'] );
	}

	public function test_execute_update_reuses_existing_post_and_keeps_option() {
		$this->mock_stored_post( APP_ADS_TXT_MANAGER_POST_OPTION, 9, 'app-adstxt' );
		\WP_Mock::userFunction( 'is_wp_error' )->andReturn( false );
		\WP_Mock::userFunction( 'wp_insert_post' )
			->once()
			->with(
				\Mockery::on(
					function ( $postarr ) {
						return 9 === $postarr['ID'] && 'app-adstxt' === $postarr['post_type'] && 'App-ads.txt' === $postarr['post_title'];
					}
				),
				true
			)
			->andReturn( 9 );
		\WP_Mock::userFunction( 'update_option' )->never();

		$result = AdsTxt\Abilities\execute_update(
			array(
				'type'    => 'app-adstxt',
				'content' => 'CONTACT=me@example.com',
			)
		);

		$this->assertTrue( $result['saved'] );
	}

	public function test_execute_update_saves_content_with_errors_when_forced() {
		$this->mock_stored_post( ADS_TXT_MANAGER_POST_OPTION, 4, 'adstxt' );
		\WP_Mock::userFunction( 'is_wp_error' )->andReturn( false );
		\WP_Mock::userFunction( 'wp_insert_post' )->once()->andReturn( 4 );

		$result = AdsTxt\Abilities\execute_update(
			array(
				'content' => 'junk',
				'force'   => true,
			)
		);

		$this->assertTrue( $result['saved'] );
		$this->assertSame( 'invalid_record', $result['errors'][0]['type'] );
	}

	public function test_execute_update_returns_error_when_save_fails() {
		$this->mock_stored_post( ADS_TXT_MANAGER_POST_OPTION, 4, 'adstxt' );
		\WP_Mock::userFunction( 'is_wp_error' )->andReturn( true );
		\WP_Mock::userFunction( 'wp_insert_post' )->once()->andReturn( 0 );
		\WP_Mock::userFunction( 'update_option' )->never();

		$result = AdsTxt\Abilities\execute_update( array( 'content' => 'CONTACT=me@example.com' ) );

		$this->assertInstanceOf( 'WP_Error', $result );
		$this->assertSame( 'save_failed', $result->code );
	}
}

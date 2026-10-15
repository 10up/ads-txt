<?php
use PHPUnit\Framework\TestCase;

/**
 * The AddressTests class tests the functions associated with an address associated with an invoice.
 */
class AdminTests extends TestCase {
	/**
	 * Set up our mocked WP functions. Rather than setting up a database we can mock the returns of core WordPress functions.
	 *
	 * @return void
	 */
	public function setUp() : void {
		\WP_Mock::setUp();
	}
	/**
	 * Tear down WP Mock.
	 *
	 * @return void
	 */
	public function tearDown() : void {
		\WP_Mock::tearDown();
	}

	/** @test Query var is set
	 */
	public function test_query_vars() {
		$vars = AdsTxt\add_query_vars( array( 'existing', 'vars' ) );

		$this->assertContains( 'ads_txt_saved', $vars );
	}

	/**
	 * Test that users without permission do not receive a Settings link.
	 */
	public function test_plugin_action_links_unauthorized() {
		\WP_Mock::userFunction( 'current_user_can' )
			->with( ADS_TXT_MANAGE_CAPABILITY )
			->andReturn( false );

		$existing_links = array(
			'deactivate' => '<a href="#">Deactivate</a>',
		);

		$links = AdsTxt\adstxt_plugin_action_links( $existing_links );

		$this->assertSame( $existing_links, $links );
	}

}

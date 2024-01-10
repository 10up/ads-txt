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
		$vars = AdsTxt\tenup_ads_txt_add_query_vars( array( 'existing', 'vars' ) );

		$this->assertContains( 'ads_txt_saved', $vars );
	}

}

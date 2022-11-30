<?php
use PHPUnit\Framework\TestCase;

/**
 * The AddressTests class tests the functions associated with an address associated with an invoice.
 */
class PostTypeTests extends TestCase {
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
	public function test_register() {

		$registered = array();

		\WP_Mock::userFunction( 'register_post_type' )->times( 2 )->andReturnUsing(
			function( $post_type, $args ) use ( &$registered ) {
				$registered[] = $post_type;
				return !in_array( $post_type, $registered ) && in_array( $post_type, array( 'adstxt', 'app-adstxt' ) );
			}
		);

		AdsTxt\register();
		$this->assertTrue(true);
	}
}

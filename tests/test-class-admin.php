<?php
/**
 * @group ads-txt
 *
 * Unit tests for class Adstxt\Admin
 *
 * @requires PHP 5.6
 * @coversDefaultClass Adstxt\Admin
 */

use AdsTxt\Admin;

class Test_Class_Admin extends WP_UnitTestCase {

	function setUp() {

		// to speeed up unit test, we bypass files scanning on upload folder
		self::$ignore_files = true;

		wp_set_current_user( self::factory()->user->create( [
			'role' => 'administrator',
		] ) );

		set_current_screen( 1 );
		$this->_instance = new Admin();
		parent::setUp();

	}

	/**
	 * @covers ::register()
	 */
	public function test_register() {

		$this->assertEquals( 10, has_action( 'init', array( $this->_instance, 'register' ) ) );
		$this->assertTrue( post_type_exists( 'adstxt' ) );

	}

	/**
	 * @covers ::admin_menu()
	 */
	public function test_admin_menu() {
		global $menu;
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->_instance, 'admin_menu' ) ) );
		$this->_instance->admin_menu();
		$this->assertNotEmpty( menu_page_url( 'adstxt-settings', false ) );

	}

	/**
	 * @covers ::admin_enqueue_scripts()
	 */
	public function test_admin_enqueue_scripts() {
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->_instance, 'admin_enqueue_scripts' ) ) );
	}
}
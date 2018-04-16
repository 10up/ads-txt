<?php
/**
 * @group ads-txt
 *
 * Unit tests for class Adstxt\Plugin
 *
 * @requires PHP 5.6
 * @coversDefaultClass Adstxt\Plugin
 */

use AdsTxt\Plugin;

class Test_Class_Plugin extends WP_UnitTestCase {

	function setUp() {
		self::$ignore_files = true;
		$this->_instance = new Plugin();
		parent::setUp();
	}

	/**
	 * @covers ::register()
	 */
	public function test_tenup_display_ads_txt() {
		$this->assertEquals( 10, has_action( 'init', array( $this->_instance, 'tenup_display_ads_txt' ) ) );
		$this->assertTrue( post_type_exists( 'adstxt' ) );
	}

}
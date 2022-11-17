<?php
use PHPUnit\Framework\TestCase;

/**
 * The AddressTests class tests the functions associated with an address associated with an invoice.
 */
class SaveTests extends TestCase {
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

	/** @test Validate line
	 * @dataProvider data_provider_for_validate_line
	 */
	public function test_validate_line( $line, $line_number, $expected ) {
		\WP_Mock::passthruFunction( 'wp_strip_all_tags' );

		$result = AdsTxt\validate_line( $line, $line_number );

		$this->assertSame( $expected, $result );
	}

	public function data_provider_for_validate_line() {
		return array(
			'Validate empty string'               => array(
				'line'        => '',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => '',
					'errors'    => array(),
				),
			),
			'Validate comment'                    => array(
				'line'        => '# comment',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => '# comment',
					'errors'    => array(),
				),
			),
			'Validate CONTACT var'                => array(
				'line'        => 'CONTACT=contact',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'CONTACT=contact',
					'errors'    => array(),
				),
			),
			'Validate SUBDOMAIN var'              => array(
				'line'        => 'SUBDOMAIN=subdomain.com',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'SUBDOMAIN=subdomain.com',
					'errors'    => array(),
				),
			),
			'Validate INVENTORYPARTNERDOMAIN var' => array(
				'line'        => 'INVENTORYPARTNERDOMAIN=subdomain.com',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'INVENTORYPARTNERDOMAIN=subdomain.com',
					'errors'    => array(),
				),
			),
			'Invalid var'                         => array(
				'line'        => 'RANDOMVAR=subdomain.com',
				'line_number' => 3,
				'expected'    => array(
					'sanitized' => 'RANDOMVAR=subdomain.com',
					'errors'    => array(
						array(
							'line' => 3,
							'type' => 'invalid_variable',
						),
					),
				),
			),
			'Invalid SUBDOMAIN var'               => array(
				'line'        => 'SUBDOMAIN=subdomain',
				'line_number' => 42,
				'expected'    => array(
					'sanitized' => 'SUBDOMAIN=subdomain',
					'errors'    => array(
						array(
							'line'  => 42,
							'type'  => 'invalid_subdomain',
							'value' => 'subdomain',
						),
					),
				),
			),
			'Validate reseller record'            => array(
				'line'        => 'example.exchange.com,pub-id123456789,RESELLER,abcdef0123456789',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'example.exchange.com,pub-id123456789,RESELLER,abcdef0123456789',
					'errors'    => array(),
				),
			),
			'Validate direct record'              => array(
				'line'        => 'example.exchange.com,pub-id123456789,DIRECT,abcdef0123456789',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'example.exchange.com,pub-id123456789,DIRECT,abcdef0123456789',
					'errors'    => array(),
				),
			),
			'Validate commented record'           => array(
				'line'        => 'example.exchange.com,pub-id123456789,RESELLER,abcdef0123456789 # comment',
				'line_number' => 1,
				'expected'    => array(
					'sanitized' => 'example.exchange.com,pub-id123456789,RESELLER,abcdef0123456789 # comment',
					'errors'    => array(),
				),
			),
			'Invalid exchange'                    => array(
				'line'        => 'wrongexchange,pub-id123456789,RESELLER,abcdef0123456789 # comment',
				'line_number' => 7,
				'expected'    => array(
					'sanitized' => 'wrongexchange,pub-id123456789,RESELLER,abcdef0123456789 # comment',
					'errors'    => array(
						array(
							'line'  => 7,
							'type'  => 'invalid_exchange',
							'value' => 'wrongexchange',
						),
					),
				),
			),
			'Invalid account type'                => array(
				'line'        => 'example.exchange.com,pub-id123456789,WRONGACCTYPE,abcdef0123456789 # comment',
				'line_number' => 97,
				'expected'    => array(
					'sanitized' => 'example.exchange.com,pub-id123456789,WRONGACCTYPE,abcdef0123456789 # comment',
					'errors'    => array(
						array(
							'line' => 97,
							'type' => 'invalid_account_type',
						),
					),
				),
			),
			'Invalid record'                      => array(
				'line'        => 'Invalid record',
				'line_number' => 132,
				'expected'    => array(
					'sanitized' => 'Invalid record',
					'errors'    => array(
						array(
							'line' => 132,
							'type' => 'invalid_record',
						),
					),
				),
			),
			'Multiple errors'                     => array(
				'line'        => 'wrongexchange,pub-id123456789,MISTAKE,abcdef0123456789 # comment',
				'line_number' => 118,
				'expected'    => array(
					'sanitized' => 'wrongexchange,pub-id123456789,MISTAKE,abcdef0123456789 # comment',
					'errors'    => array(
						array(
							'line'  => 118,
							'type'  => 'invalid_exchange',
							'value' => 'wrongexchange',
						),
						array(
							'line' => 118,
							'type' => 'invalid_account_type',
						),
					),
				),
			),
		);
	}
}

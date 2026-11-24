<?php
/**
 * Tests the validation errors shown on the settings screens.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Tests\Integration;

use WP_UnitTestCase;

/**
 * Error values come straight from the saved file, so they are escaped when shown.
 */
class DisplayErrorTest extends WP_UnitTestCase {

	/**
	 * The offending value is printed as text inside a code element, never as markup.
	 *
	 * @return void
	 */
	public function test_error_value_is_escaped(): void {
		ob_start();
		\AdsTxt\display_formatted_error(
			array(
				'line'  => 3,
				'type'  => 'invalid_exchange',
				'value' => '<script>alert(1)</script>',
			)
		);
		$output = ob_get_clean();

		$this->assertStringNotContainsString( '<script>', $output, 'The stored value must not be rendered as HTML.' );
		$this->assertStringContainsString( '<code>&lt;script&gt;alert(1)&lt;/script&gt;</code>', $output );
	}
}

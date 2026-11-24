<?php
/**
 * Tests the Settings links added to the plugin's row on the Plugins screen.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Tests\Integration;

use WP_UnitTestCase;

/**
 * Plugin action links are only offered to users who can manage ads.txt.
 */
class PluginActionLinksTest extends WP_UnitTestCase {

	/**
	 * Grants the plugin's capability to administrators, as `admin_init` does on a real site.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();
		\AdsTxt\add_capabilities();
	}

	/**
	 * Runs the plugin's row through the same filter the Plugins list table applies.
	 *
	 * @return array Action links keyed by name.
	 */
	private function get_action_links(): array {
		$existing = array( 'deactivate' => '<a href="#">Deactivate</a>' );

		return apply_filters( 'plugin_action_links_' . plugin_basename( ADS_TXT_MANAGER_PLUGIN_FILE ), $existing );
	}

	/**
	 * A user without the capability sees only the links core added.
	 *
	 * @return void
	 */
	public function test_user_without_capability_gets_no_settings_links(): void {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$this->assertSame( array( 'deactivate' ), array_keys( $this->get_action_links() ) );
	}

	/**
	 * An administrator gets links to both settings screens ahead of core's links.
	 *
	 * @return void
	 */
	public function test_administrator_gets_links_to_both_settings_screens(): void {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$links = $this->get_action_links();

		$this->assertSame( array( 'ads_txt_settings', 'app_ads_txt_settings', 'deactivate' ), array_keys( $links ) );
		$this->assertStringContainsString( 'options-general.php?page=adstxt-settings', $links['ads_txt_settings'] );
		$this->assertStringContainsString( 'options-general.php?page=app-adstxt-settings', $links['app_ads_txt_settings'] );
	}
}

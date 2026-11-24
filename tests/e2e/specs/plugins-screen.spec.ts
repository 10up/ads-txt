/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Plugins screen', () => {
	test( 'can deactivate and reactivate the plugin', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage( 'plugins.php' );
		const row = page.locator( 'tr[data-plugin="ads-txt/ads-txt.php"]' );

		await row.getByRole( 'link', { name: 'Deactivate Ads.txt Manager' } ).click();
		await expect( page.getByText( 'Plugin deactivated.' ) ).toBeVisible();

		await row.getByRole( 'link', { name: 'Activate Ads.txt Manager' } ).click();
		await expect( page.getByText( 'Plugin activated.' ) ).toBeVisible();
		await expect(
			row.getByRole( 'link', { name: 'Deactivate Ads.txt Manager' } )
		).toBeVisible();
	} );

	for ( const { name, slug } of [
		{ name: 'Ads.txt Settings', slug: 'adstxt-settings' },
		{ name: 'App-ads.txt Settings', slug: 'app-adstxt-settings' },
	] ) {
		test( `links to ${ name }`, async ( { admin, page } ) => {
			await admin.visitAdminPage( 'plugins.php' );

			await page
				.locator( 'tr[data-plugin="ads-txt/ads-txt.php"]' )
				.getByRole( 'link', { name, exact: true } )
				.click();

			await expect( page ).toHaveURL(
				new RegExp( `options-general\\.php\\?page=${ slug }$` )
			);
		} );
	}
} );

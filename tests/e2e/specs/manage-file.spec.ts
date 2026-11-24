/**
 * External dependencies
 */
import type { Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const files = [
	{
		label: 'Ads.txt',
		path: '/ads.txt',
		page: 'options-general.php?page=adstxt-settings',
		errorNotice: 'Your Ads.txt contains the following issues',
	},
	{
		label: 'App-ads.txt',
		path: '/app-ads.txt',
		page: 'options-general.php?page=app-adstxt-settings',
		errorNotice: 'Your app-ads.txt contains the following issues',
	},
];

const invalidRecord = 'test incorrect record';
const validRecord = 'example.com, pub-00000000000, DIRECT, f08c47fec0942fa0';
const otherValidRecord = 'example.org, pub-11111111111, RESELLER';

/**
 * Replaces the CodeMirror editor's content by typing, as a user would.
 *
 * @param page    Playwright page on a settings screen.
 * @param content New file content.
 */
async function typeContent( page: Page, content: string ) {
	await page.locator( '.adstxt-settings-form .CodeMirror' ).click();
	await page.keyboard.press( 'ControlOrMeta+A' );
	await page.keyboard.type( content );
}

for ( const file of files ) {
	test.describe( `Manage ${ file.label }`, () => {
		test.beforeEach( async ( { admin } ) => {
			await admin.visitAdminPage( file.page );
		} );

		/**
		 * Saves valid content and waits for the reload that follows a save.
		 *
		 * @param page    Playwright page on the settings screen.
		 * @param content Valid file content.
		 */
		async function save( page: Page, content: string ) {
			await typeContent( page, content );
			await page.getByRole( 'button', { name: 'Save Changes' } ).click();
			await expect(
				page.getByText( `${ file.label } saved`, { exact: true } )
			).toBeVisible();
		}

		test( 'shows the settings screen', async ( { page } ) => {
			await expect(
				page.getByRole( 'heading', { name: `Manage ${ file.label }` } )
			).toBeVisible();
		} );

		test( 'saves an invalid record only after confirming', async ( {
			page,
		} ) => {
			const submit = page.getByRole( 'button', { name: 'Save Changes' } );
			const errors = page.locator( '.adstxt-notice-save-error' );

			await typeContent( page, invalidRecord );
			await submit.click();

			await expect( errors ).toContainText( file.errorNotice );
			await expect( submit ).toBeDisabled();

			await page
				.getByLabel(
					'Update anyway, even though it may adversely affect your ads?'
				)
				.check();
			await submit.click();

			await expect(
				page.getByText( `${ file.label } saved`, { exact: true } )
			).toBeVisible();
			await expect( errors ).toContainText( file.errorNotice );
		} );

		test( 'serves a saved valid record', async ( { page, request } ) => {
			await save( page, validRecord );
			await expect( page.locator( '.adstxt-notice-save-error' ) ).toHaveCount(
				0
			);

			for ( const url of [
				file.path,
				`${ file.path }?cache-busting=1`,
				`${ file.path }?`,
			] ) {
				const response = await request.get( url );
				expect( response.status(), url ).toBe( 200 );
				expect( await response.text(), url ).toContain( validRecord );
			}
		} );

		test( 'returns 404 for a URL-encoded question mark', async ( {
			request,
		} ) => {
			const response = await request.get( `${ file.path }%3F` );

			expect( response.status() ).toBe( 404 );
		} );

		test( 'restores a previous revision', async ( { page, request } ) => {
			await save( page, validRecord );
			await save( page, otherValidRecord );

			// Rendering revision.php crashes headless Chromium on WordPress 7.0+, so read
			// the restore URL from the page's bootstrap data instead of clicking through it.
			const compareUrl = await page
				.locator( '.misc-pub-revisions a' )
				.getAttribute( 'href' );
			const html = await ( await page.request.get( compareUrl! ) ).text();
			const match = html.match( /var _wpRevisionsSettings = (.+);/ );
			expect( match, '_wpRevisionsSettings bootstrap' ).not.toBeNull();

			const revisions = JSON.parse( match![ 1 ] ).revisionData.filter(
				( revision ) => ! revision.autosave
			);
			const current = revisions.findIndex( ( revision ) => revision.current );
			const previous = revisions[ current - 1 ];
			expect( previous?.restoreUrl, 'previous revision restore URL' ).toEqual(
				expect.any( String )
			);

			await page.goto( previous.restoreUrl );

			await expect(
				page.getByText( 'Revision restored', { exact: true } )
			).toBeVisible();
			const served = await ( await request.get( file.path ) ).text();
			expect( served ).toContain( validRecord );
			expect( served ).not.toContain( otherValidRecord );
		} );
	} );
}

/**
 * External dependencies
 */
import type { APIRequestContext, Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const files = [
	{
		label: 'Ads.txt',
		path: '/ads.txt',
		page: 'options-general.php?page=adstxt-settings',
	},
	{
		label: 'App-ads.txt',
		path: '/app-ads.txt',
		page: 'options-general.php?page=app-adstxt-settings',
	},
];

const editorUser = {
	username: 'adstxt-editor',
	email: 'adstxt-editor@example.com',
	password: 'adstxt-editor-pass',
	roles: [ 'editor' ],
};

const originalRecord = 'example.com, pub-00000000000, DIRECT, f08c47fec0942fa0';
const attackRecord = 'attacker.example, pub-99999999999, DIRECT';

/**
 * Reads the settings form's fields, including its nonce and referer.
 *
 * @param page Playwright page on a settings screen.
 * @return Form fields keyed by name.
 */
async function getFormFields( page: Page ): Promise< Record< string, string > > {
	return page.evaluate( () =>
		Object.fromEntries(
			new FormData(
				document.querySelector(
					'.adstxt-settings-form'
				) as HTMLFormElement
			) as unknown as Iterable< [ string, string ] >
		)
	);
}

/**
 * Posts the settings form to admin-post.php, as a browser without JavaScript would.
 *
 * @param request Request context carrying the user's cookies.
 * @param fields  Form fields.
 * @return Response status.
 */
async function postForm(
	request: APIRequestContext,
	fields: Record< string, string >
): Promise< number > {
	const response = await request.post( '/wp-admin/admin-post.php', {
		form: fields,
		maxRedirects: 0,
	} );

	return response.status();
}

test.describe( 'Access control', () => {
	let editorRequest: APIRequestContext;

	test.beforeAll( async ( { requestUtils, playwright, baseURL } ) => {
		await requestUtils.createUser( editorUser );

		editorRequest = await playwright.request.newContext( { baseURL } );
		await editorRequest.post( '/wp-login.php', {
			failOnStatusCode: true,
			form: { log: editorUser.username, pwd: editorUser.password },
		} );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await editorRequest.dispose();
		await requestUtils.deleteAllUsers();
	} );

	for ( const file of files ) {
		test.describe( file.label, () => {
			let fields: Record< string, string >;

			test.beforeEach( async ( { admin, page } ) => {
				await admin.visitAdminPage( file.page );
				fields = await getFormFields( page );

				// Control: the admin's own form post saves, so the denied posts below
				// fail because of who sends them, not because the request is malformed.
				expect(
					await postForm( page.request, {
						...fields,
						adstxt: originalRecord,
					} )
				).toBe( 302 );
				expect(
					await ( await page.request.get( file.path ) ).text()
				).toContain( originalRecord );
			} );

			test( 'an editor cannot open the settings screen', async () => {
				const response = await editorRequest.get(
					`/wp-admin/${ file.page }`
				);

				expect( response.status() ).toBe( 403 );
			} );

			test( 'an editor cannot save the file', async ( { page } ) => {
				// The editor's own valid nonce, so only the capability check can refuse it.
				const nonce = await (
					await editorRequest.get(
						'/wp-admin/admin-ajax.php?action=ads_txt_e2e_save_nonce',
						{ failOnStatusCode: true }
					)
				).text();

				await postForm( editorRequest, {
					...fields,
					_wpnonce: nonce,
					adstxt: attackRecord,
				} );

				const served = await ( await page.request.get( file.path ) ).text();
				expect( served ).toContain( originalRecord );
				expect( served ).not.toContain( attackRecord );
			} );

			test( 'an administrator cannot save without a valid nonce', async ( {
				page,
			} ) => {
				const status = await postForm( page.request, {
					...fields,
					_wpnonce: 'invalid',
					adstxt: attackRecord,
				} );

				expect( status ).toBe( 403 );
				const served = await ( await page.request.get( file.path ) ).text();
				expect( served ).toContain( originalRecord );
				expect( served ).not.toContain( attackRecord );
			} );
		} );
	}
} );

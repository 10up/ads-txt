/**
 * Runs once before the E2E suite.
 *
 * Logs in as admin and saves the storage state every spec reuses, then resets
 * the site to a known state.
 */

/**
 * External dependencies
 */
import { execFileSync } from 'child_process';
import { request } from '@playwright/test';
import type { FullConfig } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';

async function globalSetup( config: FullConfig ) {
	const { storageState, baseURL } = config.projects[ 0 ].use;
	const storageStatePath =
		typeof storageState === 'string' ? storageState : undefined;

	const requestContext = await request.newContext( { baseURL } );
	const requestUtils = new RequestUtils( requestContext, {
		storageStatePath,
	} );

	// Authenticate and save the storageState to disk.
	await requestUtils.setupRest();

	// Reset the test environment before running the tests.
	await Promise.all( [
		requestUtils.deleteAllPosts(),
		requestUtils.resetPreferences(),
	] );

	// Pretty permalinks, so unknown paths such as /ads.txt%3F 404 instead of
	// showing the home page. permalink_structure isn't in /wp/v2/settings, and
	// PHPUnit's installer resets it, so set it on every run.
	execFileSync(
		'npm',
		[
			'run',
			'wp-env:test',
			'--',
			'run',
			'cli',
			'wp',
			'rewrite',
			'structure',
			'/%postname%/',
		],
		{ stdio: 'ignore' }
	);

	await requestContext.dispose();
}

export default globalSetup;

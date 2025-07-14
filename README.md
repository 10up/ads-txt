# Ads.txt Manager for WordPress

![Ads.txt Manager for WordPress](https://github.com/10up/ads-txt/blob/develop/.wordpress-org/banner-1544x500.png)

[![Support Level](https://img.shields.io/badge/support-stable-blue.svg)](#support-level) ![Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/ads-txt?label=Requires%20PHP) ![Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/ads-txt?label=Requires%20WordPress) ![WordPress tested up to version](https://img.shields.io/wordpress/plugin/tested/ads-txt?label=WordPress) [![GPLv2 License](https://img.shields.io/github/license/10up/ads-txt.svg)](https://github.com/10up/ads-txt/blob/develop/LICENSE.md) [![Dependency Review](https://github.com/10up/ads-txt/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/dependency-review.yml) [![End-to-end Tests](https://github.com/10up/ads-txt/actions/workflows/cypress.yml/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/cypress.yml) [![Unit Tests](https://github.com/10up/ads-txt/actions/workflows/phpunit.yml/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/phpunit.yml) [![Lint PHP](https://github.com/10up/ads-txt/actions/workflows/lint-php.yml/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/lint-php.yml) [![PHP Compatibility](https://github.com/10up/ads-txt/actions/workflows/php8-compatibility.yml/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/php8-compatibility.yml) [![CodeQL](https://github.com/10up/ads-txt/actions/workflows/github-code-scanning/codeql/badge.svg)](https://github.com/10up/ads-txt/actions/workflows/github-code-scanning/codeql) [![WordPress Playground Demo](https://img.shields.io/wordpress/plugin/v/ads-txt?logo=wordpress&logoColor=FFFFFF&label=Playground%20Demo&labelColor=3858E9&color=3858E9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/10up/ads-txt/develop/.wordpress-org/blueprints/blueprint.json)

> Create, manage, and validate your ads.txt and app-ads.txt from within WordPress, like any other content asset.

## Features

![Screenshot of ads.txt editor](.wordpress-org/screenshot-1.png "Example of editing an ads.txt file with errors and a link to browse ads.txt file revisions.")

[Ads.txt](https://iabtechlab.com/ads-txt/) is an initiative by the Interactive Advertising Bureau to enable publishers to take control over who can sell their ad inventory. Through our work at 10up with various publishers, we've created a way to manage and validate your ads.txt file from within WordPress, eliminating the need to upload a file. The validation baked into the plugin helps avoid malformed records, which can cause issues that end up cached for up to 24 hours and can lead to a drop in ad revenue.

### What about ads.cert?

We're closely monitoring continued developments in the ad fraud space, and see this plugin as not only a way to create and manage your ads.txt file but also be prepared for future changes and upgrades to specifications. ads.cert is still in the extremely early stages so we don't see any immediate concerns with implementing ads.txt.

### Can I use this with multisite?

Yes! However, if you are using a subfolder installation it will only work for the main site. This is because you can only have one ads.txt for a given domain or subdomain per the [ads.txt spec](https://iabtechlab.com/ads-txt/).  Our recommendation is to only activate Ads.txt Manager per-site.

## Requirements

* Requires PHP 7.4+.
* Requires WordPress 6.6+.
* Ad blockers may break syntax highlighting and pre-save error checking on the edit screen. See [#20](https://github.com/10up/ads-txt/issues/20).
* Rewrites need to be enabled. Without rewrites, WordPress cannot know to supply `/ads.txt` when requested.
* Your site URL must not contain a path (e.g. `https://example.com/site/` or path-based multisite installs). While the plugin will appear to function in the admin, it will not display the contents at `https://example.com/site/ads.txt`. This is because the plugin follows the IAB spec, which requires that the ads.txt file be located at the root of a domain or subdomain.

## Installation

1. Install the plugin via the plugin installer, either by searching for it or uploading a .zip file.
1. Activate the plugin.
1. Head to Settings → Ads.txt or App-ads.txt and add the records you need.
1. Check it out at yoursite.com/ads.txt or yoursite.com/app-ads.txt!

Note: If you already have an existing ads.txt or app-ads.txt file in the web root, the plugin will not read in the contents of the respective files, and changes you make in WordPress admin will not overwrite contents of the physical files.

You will need to rename or remove the existing (app-)ads.txt file (keeping a copy of the records it contains to put into the new settings screen) before you will be able to see any changes you make to (app-)ads.txt inside the WordPress admin.

## Screenshots

### 1. Example of editing an ads.txt file with errors and a link to browse ads.txt file revisions.

![Screenshot of ads.txt editor](.wordpress-org/screenshot-1.png "Example of editing an ads.txt file with errors and a link to browse ads.txt file revisions.")

### 2. Example of comparing ads.txt file revisions.

![Screenshot of ads.txt in Revisions editor](.wordpress-org/screenshot-2.png "Example of comparing ads.txt file revisions.")

### 3. Example of comparing two disparate ads.txt file revisions.

![Screenshot of ads.txt in Revisions editor](.wordpress-org/screenshot-3.png "Example of comparing two disparate ads.txt file revisions.")

## Support Level

**Stable:** 10up is not planning to develop any new features for this, but will still respond to bug reports and security concerns. We welcome PRs, but any that include new features should be small and easy to integrate and should not include breaking changes. We otherwise intend to keep this tested up to the most recent version of WordPress.

## Changelog

A complete listing of all notable changes to Ads.txt Manager are documented in [CHANGELOG.md](CHANGELOG.md).

## Contributing

Please read [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) for details on our code of conduct, [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us, and [CREDITS.md](CREDITS.md) for a listing of maintainers of, contributors to, and libraries used by Ads.txt Manager.

## Like what you see?

<a href="http://10up.com/contact/"><img src="https://github.com/10up/.github/blob/trunk/profile/10up-github-banner.jpg" width="850" alt="Work with the 10up WordPress Practice at Fueled"></a>

=== Ads.txt Manager ===
Contributors:      10up, helen, adamsilverstein, jakemgold, peterwilsoncc, jeffpaul
Tags:              ads.txt, app-ads.txt, ads, ad manager, advertising
Requires at least: 6.5
Tested up to:      6.7
Stable tag:        1.4.5
License:           GPL-2.0-or-later
License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html

Create, manage, and validate your ads.txt and app-ads.txt from within WordPress, like any other content asset.

== Description ==

Create, manage, and validate your ads.txt and app-ads.txt from within WordPress, like any other content asset. Requires PHP 7.4+ and WordPress 5.7+.

=== What is ads.txt? ===

Ads.txt is an initiative by the Interactive Advertising Bureau to enable publishers to take control over who can sell their ad inventory. Through our work at 10up with various publishers, we've created a way to manage and validate your ads.txt file from within WordPress, eliminating the need to upload a file. The validation baked into the plugin helps avoid malformed records, which can cause issues that end up cached for up to 24 hours and can lead to a drop in ad revenue.

=== Technical Notes ===

* Requires PHP 7.4+.
* Requires WordPress 6.3+.
* Ad blockers may break syntax highlighting and pre-save error checking on the edit screen.
* Rewrites need to be enabled. Without rewrites, WordPress cannot know to supply `/ads.txt` when requested.
* Your site URL must not contain a path (e.g. `https://example.com/site/` or path-based multisite installs). While the plugin will appear to function in the admin, it will not display the contents at `https://example.com/site/ads.txt`. This is because the plugin follows the IAB spec, which requires that the ads.txt file be located at the root of a domain or subdomain.

=== What about ads.cert? ===

We're closely monitoring continued developments in the ad fraud space, and see this plugin as not only a way to create and manage your ads.txt file but also be prepared for future changes and upgrades to specifications. Ads.cert is still in the extremely early stages so we don't see any immediate concerns with implementing ads.txt.

=== Can I use this with multisite? ===

Yes! However, if you are using a subfolder installation it will only work for the main site. This is because you can only have one ads.txt for a given domain or subdomain per the [ads.txt spec](https://iabtechlab.com/ads-txt/).  Our recommendation is to only activate Ads.txt Manager per-site.

== Screenshots ==

1. Example of editing an ads.txt file with errors and a link to browse ads.txt file revisions.
2. Example of comparing ads.txt file revisions.
3. Example of comparing two disparate ads.txt file revisions.

== Installation ==
1. Install the plugin via the plugin installer, either by searching for it or uploading a .zip file.
2. Activate the plugin.
3. Head to Settings → Ads.txt or App-ads.txt and add the records you need.
4. Check it out at yoursite.com/ads.txt or yoursite.com/app-ads.txt!

Note: If you already have an existing ads.txt or app-ads.txt file in the web root, the plugin will not read in the contents of the respective files, and changes you make in WordPress admin will not overwrite contents of the physical files.

You will need to rename or remove the existing (app-)ads.txt file (keeping a copy of the records it contains to put into the new settings screen) before you will be able to see any changes you make to (app-)ads.txt inside the WordPress admin.

== Changelog ==

= 1.4.5 - 2024-09-26 =
* **Changed:** Bump WordPress "tested up to" version 6.6 (props [@ankitguptaindia](https://github.com/ankitguptaindia), [@jeffpaul](https://github.com/jeffpaul), [@sudip-md](https://github.com/sudip-md) via [#172](https://github.com/10up/ads-txt/pull/172), [#173](https://github.com/10up/ads-txt/pull/173)).
* **Changed:** Bump WordPress minimum supported version from 6.3 to 6.4 (props [@ankitguptaindia](https://github.com/ankitguptaindia), [@jeffpaul](https://github.com/jeffpaul), [@sudip-md](https://github.com/sudip-md) via [#172](https://github.com/10up/ads-txt/pull/172), [#173](https://github.com/10up/ads-txt/pull/173)).
* **Security:** Bump `braces` from 3.0.2 to 3.0.3 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#168](https://github.com/10up/ads-txt/pull/168)).

= 1.4.4 - 2024-06-26 =
* **Added:** Placeholder record can be added with no authorized sellers or buyers (props [@ankitrox](https://github.com/ankitrox), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#129](https://github.com/10up/ads-txt/pull/129)).
* **Changed:** Bump WordPress "tested up to" version 6.5 (props [@zamanq](https://github.com/zamanq), [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul), [@qasumitbagthariya](https://github.com/qasumitbagthariya), [@sudip-md](https://github.com/sudip-md) via [#152](https://github.com/10up/ads-txt/pull/152), [#156](https://github.com/10up/ads-txt/pull/156), [#162](https://github.com/10up/ads-txt/issues/162)).
* **Fixed:** Better error handling for environments that don't match our minimum PHP version (props [@dkotter](https://github.com/dkotter), [@rahulsprajapati](https://github.com/rahulsprajapati), [@peterwilsoncc](https://github.com/peterwilsoncc), [@frankiebordone](https://github.com/frankiebordone), [@vikrampm1](https://github.com/vikrampm1) via [#149](https://github.com/10up/ads-txt/pull/149)).
* **Security:** Bump `semver` from 7.3.5 to 7.5.3 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#147](https://github.com/10up/ads-txt/pull/147)).

= 1.4.3 - 2023-06-21 =
* **Added:** `ads.txt` file exists check from the backend (props [@sksaju](https://github.com/sksaju), [@peterwilsoncc](https://github.com/peterwilsoncc), [@mmcachran](https://github.com/mmcachran), [@dinhtungdu](https://github.com/dinhtungdu), [@helen](https://github.com/helen), [@jeffpaul](https://github.com/jeffpaul) via [#131](https://github.com/10up/ads-txt/pull/131)).
* **Added:** Check for and delete orphan `(app-)ads.txt` posts not referenced in the option (props [@sksaju](https://github.com/sksaju), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#138](https://github.com/10up/ads-txt/pull/138)).
* **Added:** Mochawesome reporter added for Cypress test report (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#141](https://github.com/10up/ads-txt/pull/141)).
* **Changed:** Bump WordPress "tested up to" version 6.2 (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@faisal-alvi](https://github.com/faisal-alvi) via [#135](https://github.com/10up/ads-txt/pull/135)).
* **Changed:** Run E2E tests on the zip generated by the "Build Release ZIP" GitHub Action (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#137](https://github.com/10up/ads-txt/pull/137)).
* **Changed:** Update the Dependency Review GitHub Action (props [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#142](https://github.com/10up/ads-txt/pull/142)).
* **Fixed:** Remove PHP matrix from PHP8 Compatibility action (props [@Sidsector9](https://github.com/Sidsector9), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#127](https://github.com/10up/ads-txt/pull/127)).
* **Fixed:** Corrected names for PHP Unit test suite runs (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#133](https://github.com/10up/ads-txt/pull/133)).
* **Fixed:** Fatal error if the role `administrator`` does not exist (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@Sidsector9](https://github.com/Sidsector9) via [#140](https://github.com/10up/ads-txt/pull/140)).
* **Security:** Bump `simple-git` from 3.15.0 to 3.16.0 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#128](https://github.com/10up/ads-txt/pull/128)).
* **Security:** Bump `http-cache-semantics` from 4.1.0 to 4.1.1 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#130](https://github.com/10up/ads-txt/pull/130)).

= 1.4.2 - 2023-01-16 =
* **Changed:** Update Support Level from `Active` to `Stable` (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#123](https://github.com/10up/ads-txt/pull/123)).
* **Changed:** Update Cypress integration to use v11 (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#116](https://github.com/10up/ads-txt/pull/116)).
* **Fixed:** Display `ads.txt` files for crawlers using a cache busting query string (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic) via [#118](https://github.com/10up/ads-txt/pull/118)).

= 1.4.1 - 2022-12-14 =
* **Added:** Support for OWNERDOMAIN & MANAGERDOMAIN per version 1.1 of the spec (props [@SoftCreatR](https://github.com/SoftCreatR), [@tott](https://github.com/tott), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#108](https://github.com/10up/ads-txt/pull/108))
* **Added:** Unit tests (props [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic), [@faisal-alvi](https://github.com/faisal-alvi), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#87](https://github.com/10up/ads-txt/pull/87))
* **Added:** Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#97](https://github.com/10up/ads-txt/pull/97))
* **Changed:** Bump Wordpress tested up to to 6.1 (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#113](https://github.com/10up/ads-txt/pull/113))
* **Changed:** Minimum WP and PHP version requirement bumped to 5.7 and 7.4 respectively (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic) via [#103](https://github.com/10up/ads-txt/pull/103), [#117](https://github.com/10up/ads-txt/pull/117))
* **Fixed:** Base URL corrected for E2E test suite. (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic), [@dkotter](https://github.com/dkotter) via [#112](https://github.com/10up/ads-txt/pull/112))
* **Security:** Bump got and @wordpress/env (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc), [@dependabot](https://github.com/dependabot), [@dkotter](https://github.com/dkotter) via [#104](https://github.com/10up/ads-txt/pull/104))
* **Security:** Bump simple-git and @wordpress/env (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#105](https://github.com/10up/ads-txt/pull/105))

= 1.4.0 - 2022-04-13 =
* **Added:** Support for the `INVENTORYPARTNERDOMAIN` variable (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi))
* **Added:** End to end tests with Cypress (props [@cadic](https://github.com/cadic), [@dinhtungdu](https://github.com/dinhtungdu), [@darylldoyle](https://github.com/darylldoyle), [@Sidsector9](https://github.com/Sidsector9))
* **Changed:** Update dealerdirect/phpcodesniffer-composer-installer from 0.5.x to 0.7.1 (props [@evokelektrique](http://github.com/evokelektrique), [@peterwilsoncc](http://github.com/peterwilsoncc))
* **Changed:** Update minimist from 1.2.5 to 1.2.6
* **Changed:** Bump Wordpress tested up to to 6.0 (props [@mohitwp](https://github.com/mohitwp), [@cadic](https://github.com/cadic), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#85](https://github.com/10up/ads-txt/pull/85), [#90](https://github.com/10up/ads-txt/pull/90))
* **Changed:** Automated testing code compatibility against PHP versions from 5.3 to 8.1 (props [@cadic](https://github.com/cadic))
* **Fixed:** Allow admins to access revisions (props [@PypWalters](https://github.com/PypWalters), [@dinhtungdu](https://github.com/dinhtungdu))
* **Fixed:** Coding standards violations (props [@peterwilsoncc](http://github.com/peterwilsoncc))

= 1.3.0 - 2020-05-01 =
* **Added:** Support for app-ads.txt filetype (props [@helen](https://profiles.wordpress.org/helen/), [@westi](https://profiles.wordpress.org/westi/), [@p0mmy](https://github.com/p0mmy))
* **Removed:** Stop attempting to show an error notice about an existing `ads.txt` file due to too many false positives. We will bring this back later in a better way.
* **Changed:** Bump WordPress version support to 5.4 (props [@tmoorewp](https://profiles.wordpress.org/tmoorewp/), [@jeffpaul](https://profiles.wordpress.org/jeffpaul/))
* **Changed:** Switched to using GitHub Actions instead of Travis for Continuous Integration (props [@helen](https://profiles.wordpress.org/helen/))
* **Changed:** Updated plugin screenshots and FAQs (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul/), [@helen](https://profiles.wordpress.org/helen/))
* **Fixed:** Update capability check when saving ads.txt (props [@eclev91](https://profiles.wordpress.org/eclev91/))

Further changelog entries can be found in the [CHANGELOG.md](https://github.com/10up/ads-txt/blob/trunk/CHANGELOG.md) file.

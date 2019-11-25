=== Ads.txt Manager ===
Contributors: 10up, helen, adamsilverstein, jakemgold
Author URI: https://10up.com
Plugin URI: https://github.com/10up/ads-txt
Tags: ads.txt, ads, ad manager, advertising, publishing, publishers
Requires at least: 4.9
Tested up to: 5.2
Requires PHP: 5.3
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: ads-txt

Create, manage, and validate your ads.txt from within WordPress, just like any other content asset. Requires PHP 5.3+ and WordPress 4.9+.

== Description ==

Create, manage, and validate your ads.txt from within WordPress, just like any other content asset. Requires PHP 5.3+ and WordPress 4.9+.

=== What is ads.txt? ===

Ads.txt is an initiative by the Interactive Advertising Bureau to enable publishers to take control over who can sell their ad inventory. Through our work at 10up with various publishers, we've created a way to manage and validate your ads.txt file from within WordPress, eliminating the need to upload a file. The validation baked into the plugin helps avoid malformed records, which can cause issues that end up cached for up to 24 hours and can lead to a drop in ad revenue.

=== Technical Notes ===

* Requires PHP 5.3+.
* Requires WordPress 4.9+. Older versions of WordPress will not display any syntax highlighting and may break JavaScript and/or be unable to localize the plugin.
* Rewrites need to be enabled. Without rewrites, WordPress cannot know to supply `/ads.txt` when requested.
* Your site URL must not contain a path (e.g. `https://example.com/site/` or path-based multisite installs). While the plugin will appear to function in the admin, it will not display the contents at `https://example.com/site/ads.txt`. This is because the plugin follows the IAB spec, which requires that the ads.txt file be located at the root of a domain or subdomain.

=== What about ads.cert? ===

We're closely monitoring continued developments in the ad fraud space, and see this plugin as not only a way to create and manage your ads.txt file but also be prepared for future changes and upgrades to specifications. Ads.cert is still in the extremely early stages so we don't see any immediate concerns with implementing ads.txt.

== Screenshots ==

1. Example of editing an ads.txt file with errors

== Installation ==
1. Install the plugin via the plugin installer, either by searching for it or uploading a .zip file.
2. Activate the plugin.
3. Head to Settings → Ads.txt and add the records you need.
4. Check it out at yoursite.com/ads.txt!

Note: If you already have an existing ads.txt file in the web root, the plugin will not read in the contents of that file, and changes you make in WordPress admin will not overwrite contents of the physical file.

You will need to rename or remove the existing ads.txt file (keeping a copy of the records it contains to put into the new settings screen) before you will be able to see any changes you make to ads.txt inside the WordPress admin.

== Changelog ==

= 1.2.0 =
* Added: Revisions link and revisions screen integration for the ads.txt file (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein), [@helen](https://profiles.wordpress.org/helen))
* Added: Notice to the edit screen if an ads.txt file already exists (props [@kkoppenhaver](https://profiles.wordpress.org/kkoppenhaver), [@helen](https://profiles.wordpress.org/helen), [@tjnowell](https://profiles.wordpress.org/tjnowell), [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein))
* Added: Capability for granular assignment, default assignment to admins (props [@eclev91](https://profiles.wordpress.org/eclev91), [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein))
* Added: Filtering of the output ads.txt content (props [@eclev91](https://profiles.wordpress.org/eclev91))
* Changed: Updated documentation, automation, and coding standards (props [@jeffpaul](https://profiles.wordpress.org/jeffpaul), [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein), [@helen](https://profiles.wordpress.org/helen), [@mmcachran](https://profiles.wordpress.org/mmcachran)) 
* Fixed: Early escaping (props [@tjnowell](https://profiles.wordpress.org/tjnowell))
* Fixed: PHPCS issues and adds PHPCS scanning (props [@adamsilverstein](https://profiles.wordpress.org/adamsilverstein))

= 1.1 =
* Better error message formatting (wraps values in `<code>` tags for better readability)
* WordPress.com VIP-approved escaping

= 1.0 =
* Initial plugin release

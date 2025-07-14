# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

## [1.4.6] - 2025-07-14
**Note that this release bumps the WordPress minimum version from 6.4 to 6.6.**

### Changed
- Replace `dirname( __FILE__ )` calls with `__DIR__` magic constant (props [@Soean](https://github.com/Soean), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#187](https://github.com/10up/ads-txt/pull/187)).
- Bump WordPress "tested up to" version 6.8 (props [@jeffpaul](https://github.com/jeffpaul), [@godleman](https://github.com/godleman) via [#183](https://github.com/10up/ads-txt/pull/183), [#184](https://github.com/10up/ads-txt/pull/184), [#194](https://github.com/10up/ads-txt/pull/194), [#195](https://github.com/10up/ads-txt/pull/195)).
- Bump WordPress minimum supported version to 6.6 (props [@jeffpaul](https://github.com/jeffpaul), [@godleman](https://github.com/godleman) via [#183](https://github.com/10up/ads-txt/pull/183), [#184](https://github.com/10up/ads-txt/pull/184), [#194](https://github.com/10up/ads-txt/pull/194), [#195](https://github.com/10up/ads-txt/pull/195)).

### Fixed
- Add missing text domain and fix wrong text domain (props [@mehrazmorshed](https://github.com/mehrazmorshed), [@dkotter](https://github.com/dkotter) via [#182](https://github.com/10up/ads-txt/pull/182)).
- Remove unnecessary `echo` statement (props [@Soean](https://github.com/Soean), [@dkotter](https://github.com/dkotter) via [#186](https://github.com/10up/ads-txt/pull/186)).
- Improve performance of the `clean_orphaned_posts` function (props [@dilipbheda](https://github.com/dilipbheda), [@dkotter](https://github.com/dkotter) via [#192](https://github.com/10up/ads-txt/pull/192)).

### Security
- Bump `serialize-javascript` from 6.0.0 to 6.0.2 and `mocha` from 10.2.0 to 11.1.0 (props [@dependabot](https://github.com/apps/dependabot), [@faisal-alvi](https://github.com/faisal-alvi) via [#185](https://github.com/10up/ads-txt/pull/185)).

### Developer
- Update badges shown in the `README.md` file (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#179](https://github.com/10up/ads-txt/pull/179)).
- Update all out-dated GitHub Action Workflows we rely on to their latest version (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#188](https://github.com/10up/ads-txt/pull/188)).
- Update all third-party actions our workflows rely on to use versions based on specific commit hashes (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#190](https://github.com/10up/ads-txt/pull/190)).
- Ensure our GitHub Actions all have proper permissions (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#196](https://github.com/10up/ads-txt/pull/196)).
- Improve documentation of a few hooks (props [@Soean](https://github.com/Soean), [@dkotter](https://github.com/dkotter) via [#189](https://github.com/10up/ads-txt/pull/189)).

## [1.4.5] - 2024-09-26
**Note that this release bumps the WordPress minimum version from 6.3 to 6.4.**

### Changed
- Bump WordPress "tested up to" version 6.6 (props [@ankitguptaindia](https://github.com/ankitguptaindia), [@jeffpaul](https://github.com/jeffpaul), [@sudip-md](https://github.com/sudip-md) via [#172](https://github.com/10up/ads-txt/pull/172), [#173](https://github.com/10up/ads-txt/pull/173)).
- Bump WordPress minimum supported version from 6.3 to 6.4 (props [@ankitguptaindia](https://github.com/ankitguptaindia), [@jeffpaul](https://github.com/jeffpaul), [@sudip-md](https://github.com/sudip-md) via [#172](https://github.com/10up/ads-txt/pull/172), [#173](https://github.com/10up/ads-txt/pull/173)).

### Security
- Bump `braces` from 3.0.2 to 3.0.3 (props [@dependabot](https://github.com/apps/dependabot), [@iamdharmesh](https://github.com/iamdharmesh) via [#168](https://github.com/10up/ads-txt/pull/168)).

### Developer
- Update repo badges, add WordPress Playground badge (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#175](https://github.com/10up/ads-txt/pull/175)).
- Add the plugin banner image to the `README.md` file (props [@jeffpaul](https://github.com/jeffpaul), [@dkotter](https://github.com/dkotter) via [#176](https://github.com/10up/ads-txt/pull/176)).

## [1.4.4] - 2024-06-26
### Added
- Placeholder record can be added with no authorized sellers or buyers (props [@ankitrox](https://github.com/ankitrox), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#129](https://github.com/10up/ads-txt/pull/129)).

### Changed
- Bump WordPress "tested up to" version 6.5 (props [@zamanq](https://github.com/zamanq), [@QAharshalkadu](https://github.com/QAharshalkadu), [@jeffpaul](https://github.com/jeffpaul), [@qasumitbagthariya](https://github.com/qasumitbagthariya), [@sudip-md](https://github.com/sudip-md) via [#152](https://github.com/10up/ads-txt/pull/152), [#156](https://github.com/10up/ads-txt/pull/156), [#162](https://github.com/10up/ads-txt/issues/162)).

### Fixed
- Better error handling for environments that don't match our minimum PHP version (props [@dkotter](https://github.com/dkotter), [@rahulsprajapati](https://github.com/rahulsprajapati), [@peterwilsoncc](https://github.com/peterwilsoncc), [@frankiebordone](https://github.com/frankiebordone), [@vikrampm1](https://github.com/vikrampm1) via [#149](https://github.com/10up/ads-txt/pull/149)).

### Security
- Bump `semver` from 7.3.5 to 7.5.3 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#147](https://github.com/10up/ads-txt/pull/147)).

### Developer
- Added Repo Automator GitHub Action (props [@iamdharmesh](https://github.com/iamdharmesh) via [#167](https://github.com/10up/ads-txt/pull/167)).
- Added a "Testing" section in the `CONTRIBUTING.md` file (props [@kmgalanakis](https://github.com/kmgalanakis), [@jeffpaul](https://github.com/jeffpaul) via [#166](https://github.com/10up/ads-txt/pull/166)).
- Cleaned up NPM dependencies and update node to v20 (props [@Sidsector9](https://github.com/Sidsector9), [@dkotter](https://github.com/dkotter) via [#161](https://github.com/10up/ads-txt/pull/161)).
- Updated the `skaut/wordpress-version-checker` to check WordPress "tested up to" during the Release Candidate phase (props [@jeffpaul](https://github.com/jeffpaul), [@iamdharmesh](https://github.com/iamdharmesh) via [#145](https://github.com/10up/ads-txt/pull/145)).
- Upgraded the `download-artifact` from v3 to v4 (props [@iamdharmesh](https://github.com/iamdharmesh) via [#163](https://github.com/10up/ads-txt/pull/163)).
- Replaced `lee-dohm/no-response` with `actions/stale` to help with closing no-response/stale issues (props [@jeffpaul](https://github.com/jeffpaul) via [#164](https://github.com/10up/ads-txt/pull/164)).
- Bumped `Cypress` version from 11.2.0 to 13.2.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#154](https://github.com/10up/ads-txt/pull/154)).
- Bumped `@wordpress/env` version from 5.7.0 to 8.7.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#154](https://github.com/10up/ads-txt/pull/154)).
- Bumped `cypress-mochawesome-reporter` version 3.4.0 to 3.6.0 (props [@iamdharmesh](https://github.com/iamdharmesh), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#154](https://github.com/10up/ads-txt/pull/154)).

## [1.4.3] - 2023-06-21
### Added
- `ads.txt` file exists check from the backend (props [@sksaju](https://github.com/sksaju), [@peterwilsoncc](https://github.com/peterwilsoncc), [@mmcachran](https://github.com/mmcachran), [@dinhtungdu](https://github.com/dinhtungdu), [@helen](https://github.com/helen), [@jeffpaul](https://github.com/jeffpaul) via [#131](https://github.com/10up/ads-txt/pull/131)).
- Check for and delete orphan `(app-)ads.txt` posts not referenced in the option (props [@sksaju](https://github.com/sksaju), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#138](https://github.com/10up/ads-txt/pull/138)).
- Mochawesome reporter added for Cypress test report (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#141](https://github.com/10up/ads-txt/pull/141)).

### Changed
- Bump WordPress "tested up to" version 6.2 (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@faisal-alvi](https://github.com/faisal-alvi) via [#135](https://github.com/10up/ads-txt/pull/135)).
- Run E2E tests on the zip generated by the "Build Release ZIP" GitHub Action (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#137](https://github.com/10up/ads-txt/pull/137)).
- Update the Dependency Review GitHub Action (props [@jeffpaul](https://github.com/jeffpaul), [@Sidsector9](https://github.com/Sidsector9) via [#142](https://github.com/10up/ads-txt/pull/142)).

### Fixed
- Remove PHP matrix from PHP8 Compatibility action (props [@Sidsector9](https://github.com/Sidsector9), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#127](https://github.com/10up/ads-txt/pull/127)).
- Corrected names for PHP Unit test suite runs (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@jeffpaul](https://github.com/jeffpaul) via [#133](https://github.com/10up/ads-txt/pull/133)).
- Fatal error if the role `administrator`` does not exist (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@Sidsector9](https://github.com/Sidsector9) via [#140](https://github.com/10up/ads-txt/pull/140)).

### Security
- Bump `simple-git` from 3.15.0 to 3.16.0 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#128](https://github.com/10up/ads-txt/pull/128)).
- Bump `http-cache-semantics` from 4.1.0 to 4.1.1 (props [@dependabot](https://github.com/apps/dependabot), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#130](https://github.com/10up/ads-txt/pull/130)).

## [1.4.2] - 2023-01-16
### Changed
- Update Support Level from `Active` to `Stable` (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#123](https://github.com/10up/ads-txt/pull/123)).
- Update Cypress integration to use v11 (props [@jayedul](https://github.com/jayedul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#116](https://github.com/10up/ads-txt/pull/116)).

### Fixed
- Display `ads.txt` files for crawlers using a cache busting query string (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic) via [#118](https://github.com/10up/ads-txt/pull/118)).

## [1.4.1] - 2022-12-14
### Added
- Support for OWNERDOMAIN & MANAGERDOMAIN per version 1.1 of the spec (props [@SoftCreatR](https://github.com/SoftCreatR), [@tott](https://github.com/tott), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#108](https://github.com/10up/ads-txt/pull/108))
- Unit tests (props [@jeffpaul](https://github.com/jeffpaul), [@cadic](https://github.com/cadic), [@faisal-alvi](https://github.com/faisal-alvi), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#87](https://github.com/10up/ads-txt/pull/87))
- Dependency security scanning (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#97](https://github.com/10up/ads-txt/pull/97))

### Changed
- Bump WordPress tested up to to 6.1 (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter), [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#113](https://github.com/10up/ads-txt/pull/113))
- Minimum WP and PHP version requirement bumped to 5.7 and 7.4 respectively (props [@jayedul](https://github.com/jayedul), [@dkotter](https://github.com/dkotter), [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic) via [#103](https://github.com/10up/ads-txt/pull/103), [#117](https://github.com/10up/ads-txt/pull/117))

### Fixed
- Base URL corrected for E2E test suite. (props [@peterwilsoncc](https://github.com/peterwilsoncc), [@cadic](https://github.com/cadic), [@dkotter](https://github.com/dkotter) via [#112](https://github.com/10up/ads-txt/pull/112))

### Security
- Bump got and @wordpress/env (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc), [@dependabot](https://github.com/dependabot), [@dkotter](https://github.com/dkotter) via [#104](https://github.com/10up/ads-txt/pull/104))
- Bump simple-git and @wordpress/env (props [@jeffpaul](https://github.com/jeffpaul), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#105](https://github.com/10up/ads-txt/pull/105))

## [1.4.0] - 2022-04-13
### Added
- Support for the `INVENTORYPARTNERDOMAIN` variable (props [@dkotter](https://github.com/dkotter), [@faisal-alvi](https://github.com/faisal-alvi) via [#74](https://github.com/10up/ads-txt/pull/74))
- End to end tests with Cypress (props [@cadic](https://github.com/cadic), [@dinhtungdu](https://github.com/dinhtungdu), [@darylldoyle](https://github.com/darylldoyle), [@Sidsector9](https://github.com/Sidsector9) via [#82](https://github.com/10up/ads-txt/pull/82))

### Changed
- Update dealerdirect/phpcodesniffer-composer-installer from 0.5.x to 0.7.1 (props [@evokelektrique](http://github.com/evokelektrique), [@peterwilsoncc](http://github.com/peterwilsoncc) via [#77](https://github.com/10up/ads-txt/pull/77))
- Update minimist from 1.2.5 to 1.2.6 ([#88](https://github.com/10up/ads-txt/pull/88))
- Bump WordPress tested up to to 6.0 (props [@mohitwp](https://github.com/mohitwp), [@cadic](https://github.com/cadic), [@peterwilsoncc](https://github.com/peterwilsoncc) via [#85](https://github.com/10up/ads-txt/pull/85), [#90](https://github.com/10up/ads-txt/pull/90))
- Automated testing code compatibility against PHP versions from 5.3 to 8.1 (props [@cadic](https://github.com/cadic) via [#86](https://github.com/10up/ads-txt/pull/86))

### Fixed
- Allow admins to access revisions (props [@PypWalters](https://github.com/PypWalters), [@dinhtungdu](https://github.com/dinhtungdu) via [#68](https://github.com/10up/ads-txt/pull/68))
- Coding standards violations (props [@peterwilsoncc](http://github.com/peterwilsoncc) via [#81](https://github.com/10up/ads-txt/pull/81))

## [1.3.0] - 2020-05-01
### Added
- Support for app-ads.txt filetype (props [@helen](https://github.com/helen), [@westi](https://github.com/westi), [@p0mmy](https://github.com/p0mmy) via [#60](https://github.com/10up/ads-txt/pull/60))

### Removed
- Stop attempting to show an error notice about an existing `ads.txt` file due to too many false positives. We will bring this back later in a better way. (see [#61](https://github.com/10up/ads-txt/issues/61))

### Changed
- Bump WordPress version support to 5.4 (props [@tmoorewp](https://github.com/tmoorewp), [@jeffpaul](https://github.com/jeffpaul) via [#56](https://github.com/10up/ads-txt/pull/56))
- Switched to using GitHub Actions instead of Travis for Continuous Integration (props [@helen](https://github.com/helen) via [#54](https://github.com/10up/ads-txt/pull/54))
- Updated plugin screenshots and FAQs (props [@jeffpaul](https://github.com/jeffpaul), [@helen](https://github.com/helen) via [#58](https://github.com/10up/ads-txt/pull/58), [#55](https://github.com/10up/ads-txt/pull/55))

### Fixed
- Update capability check when saving ads.txt (props [@ethanclevenger91](https://github.com/ethanclevenger91) via [#51](https://github.com/10up/ads-txt/pull/51))

## [1.2.0] - 2019-11-26
### Added
- Make revisions accessible in the admin - now you can restore older versions of your ads.txt or view how it's changed over time (props [@adamsilverstein](https://github.com/adamsilverstein), [@helen](https://github.com/helen) via [#9](https://github.com/10up/ads-txt/pull/9))
- Show a notice on the edit screen if an ads.txt file exists on the server (props [@kkoppenhaver](https://github.com/kkoppenhaver), [@helen](https://github.com/helen), [@tomjn](https://github.com/tomjn), [@adamsilverstein](https://github.com/adamsilverstein) via [#19](https://github.com/10up/ads-txt/pull/19))
- Add a custom `edit_ads_txt` capability for granular assignment, which is assigned to administrators by default (props [@ethanclevenger91](https://github.com/ethanclevenger91), [@adamsilverstein](https://github.com/adamsilverstein) via [#29](https://github.com/10up/ads-txt/pull/29))
- Enable filtering of the output using `ads_txt_content` (props [@ethanclevenger91](https://github.com/ethanclevenger91) via [#36](https://github.com/10up/ads-txt/pull/36))

### Changed
- Updated documentation, automation, and coding standards (props [@jeffpaul](https://github.com/jeffpaul), [@adamsilverstein](https://github.com/adamsilverstein), [@helen](https://github.com/helen), [@mmcachran](https://github.com/mmcachran) via [#33](https://github.com/10up/ads-txt/pull/33), [#34](https://github.com/10up/ads-txt/pull/34), [#39](https://github.com/10up/ads-txt/pull/39), [#41](https://github.com/10up/ads-txt/pull/41), [#42](https://github.com/10up/ads-txt/pull/42))

### Fixed
- Early escaping (props [@tomjn](https://github.com/tomjn) via [#25](https://github.com/10up/ads-txt/pull/25))
- PHPCS issues and added PHPCS scanning (props [@adamsilverstein](https://github.com/adamsilverstein) via [#38](https://github.com/10up/ads-txt/pull/38))

## [1.1.0] - 2018-02-05
### Fixed
- Better error message formatting (wraps values in `<code>` tags for better readability)
- WordPress.com VIP-approved escaping

## [1.0.0] - 2017-12-18
- Initial plugin release

[Unreleased]: https://github.com/10up/ads-txt/compare/trunk...develop
[1.4.6]: https://github.com/10up/ads-txt/compare/1.4.5...1.4.6
[1.4.5]: https://github.com/10up/ads-txt/compare/1.4.4...1.4.5
[1.4.4]: https://github.com/10up/ads-txt/compare/1.4.3...1.4.4
[1.4.3]: https://github.com/10up/ads-txt/compare/1.4.2...1.4.3
[1.4.2]: https://github.com/10up/ads-txt/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/10up/ads-txt/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/10up/ads-txt/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/10up/ads-txt/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/10up/ads-txt/compare/1.1...1.2.0
[1.1.0]: https://github.com/10up/ads-txt/compare/1.0...1.1
[1.0.0]: https://github.com/10up/ads-txt/releases/tag/1.0

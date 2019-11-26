# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/).

## [Unreleased] - TBD

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

## [1.1] - 2018-02-05
### Fixed
- Better error message formatting (wraps values in <code> tags for better readability)
- WordPress.com VIP-approved escaping

## [1.0] - 2017-12-18
- Initial plugin release

[Unreleased]: https://github.com/10up/ads-txt/compare/master...develop
[1.2.0]: https://github.com/10up/ads-txt/compare/1.1...1.2.0
[1.1]: https://github.com/10up/ads-txt/compare/1.0...1.1
[1.0]: https://github.com/10up/ads-txt/releases/tag/1.0

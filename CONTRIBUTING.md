# Contributing and Maintaining

First, thank you for taking the time to contribute!

The following is a set of guidelines for contributors as well as information and instructions around our maintenance process. The two are closely tied together in terms of how we all work together and set expectations, so while you may not need to know everything in here to submit an issue or pull request, it's best to keep them in the same document.

## Ways to contribute

Contributing isn't just writing code - it's anything that improves the project. All contributions for Ads.txt Manager are managed right here on GitHub. Here are some ways you can help:

### Reporting bugs

If you're running into an issue with the plugin, please take a look through [existing issues](https://github.com/10up/ads-txt/issues) and [open a new one](https://github.com/10up/ads-txt/issues/new) if needed. If you're able, include steps to reproduce, environment information, and screenshots/screencasts as relevant.

### Suggesting enhancements

New features and enhancements are also managed via [issues](https://github.com/10up/ads-txt/issues). As project owners, 10up sets the [direction and roadmap](#roadmap) and may not prioritize or decide to implement if outside of the main goals of the plugin.

### Pull requests

Pull requests represent a proposed solution to a specified problem. They should always reference an issue that describes the problem and contains discussion about the problem itself. Discussion on pull requests should be limited to the pull request itself, i.e. code review.

For more on how 10up writes and manages code, check out our [10up Engineering Best Practices](https://10up.github.io/Engineering-Best-Practices/).

## Maintenance process

### Triage

Issues and WordPress.org forum posts should be reviewed weekly and triaged as necessary. Not all tasks have to be done at once or by the same person. Triage tasks include:

* Responding to new WordPress.org forum posts and GitHub issues/PRs with an acknolwedgment and following up on existing open/unresolved items that have had movement in the previous week.
* Marking forum posts as resolved when corresponding issues are fixed or as not a support issue if not relevant.
* Creating GitHub issues for WordPress.org forum posts as necessary or linking to them from existing related issues.
* Applying labels and milestones to GitHub issues.

#### Issue labels

All issues should be labeled as bugs (`type:bug`), enhancements/feature requests (`type:enhancement`), or questions/support (`type:question`). Each issue should only be of one "type".

Bugs and enhancements that are closed without a related change should be labeled as `declined`, `duplicate`, or `invalid`. Invalid issues would be where a problem is not reproducible or opened in the wrong repo and should be relatively uncommon. These labels are all prefixed with `closed:`.

There are two other labels that are GitHub defaults with more global meaning we've kept: `good first issue` and `help wanted`.

### Review against WordPress updates

During weekly triage, the tested up to version should be compared against the latest version of WordPress. If there's a newer version of WordPress, the plugin should be re-tested using any automated tests as well as any manual tests indicated below, and the tested up to version bumped and committed to both GitHub and the WordPress.org repository.

### Release cycle

New releases are targeted based on number and severity of changes along with human availability. When a release is targeted, a due date will be assigned to the appropriate milestone.

### Roadmap

We are currently working toward [version 1.2](https://github.com/10up/ads-txt/milestone/1), which will include exposing a UI for revisions and detecting the presence of an actual `ads.txt` file. There is not yet a targeted release date, and features and enhancements may still be added to the milestone.

### Testing

Make an ads.txt with the following contents and ensure that you receive the same errors as below. There are no automated tests at this time - contributions very welcome in this area!

#### Contents

```
# This is a comment
contact=test@example.com
subdomain=wrongdomain
subdomain=sub.domain.com

Invalid record

# Records
google.com, pub-1234567890, DIRECT, f08c47fec0942fa0
not-an-exchange, pub-1234567890, INVALID, f08c47fec0942
```

#### Errors

```
Line 3: wrongdomain does not appear to be a valid subdomain
Line 6: Invalid record
Line 10: not-an-exchange does not appear to be a valid exchange domain
Line 10: Third field should be RESELLER or DIRECT
Line 10: f08c47fec0942 does not appear to be a valid TAG-ID
```

### Release instructions

1. Version bump: Bump the version number in `ads-txt.php`.
2. Changelog: Add/update the changelog in both `readme.txt` and `README.md`
3. Readme updates: Make any other readme changes as necessary. `README.md` is geared toward GitHub and `readme.txt` contains WordPress.org-specific content. The two are slightly different.
4. Merge: Make a non-fast-forward merge from `develop` to `master`.
5. SVN update: Copy files over to the `trunk` folder of an SVN checkout of the plugin. If the plugin banner, icon, or screenshots have changed, copy those to the top-level `assets` folder. Commit those changes.
6. SVN tag: Make a folder inside `tags` with the current version number, copy the contents of `trunk` into it, and commit with the message `Tagging X.Y.Z`. There is also an SVN command for tagging; however, note that it runs on the remote and requires care because the entire WordPress.org plugins repo is actually single SVN repo.
7. Check WordPress.org: Ensure that the changes are live on https://wordpress.org/plugins/ads-txt/. This may take a few minutes.
8. Git tag: Tag the release in Git and push the tag to GitHub. It should now appear under [releases](https://github.com/10up/ads-txt/releases) there as well.

<p align="center">
<a href="http://10up.com/contact/"><img src="https://10updotcom-wpengine.s3.amazonaws.com/uploads/2016/10/10up-Github-Banner.png" width="850"></a>
</p>

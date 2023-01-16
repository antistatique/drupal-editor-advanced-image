# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- add official support of drupal 10.0 (with CKEditor 4)
- add support for CKEditor 5
- upgrade path from CKEdditor 4 to CKEditor 5
- add official support of drupal 9.5

### Removed
- drop support of drupal below 9.3.x
- drop support of drupal below 9.4.x

## [2.0.0] - 2022-10-21
### Changed
- move changelog format in order to use Keep a Changelog standard
- force functional tests to fail on risky (skipped) tests
- disable deprecation notice PHPUnit
- drop support of drupal 8.8 & 8.9

### Added
- add dependabot for Github Action dependency
- add upgrade-status check
- add coverage for Drupal 9.3, 9.4 & 9.5

### Removed
- remove satackey/action-docker-layer-caching on Github Actions
- remove trigger github actions on every pull-request, keep only push

### Fixed
- fixed docker test Javascript on CI
- fix issue #3257427: When adding an image with align filter selected it doesn't works properly

## [2.0.0-beta1] - 2020-07-03
### Fixed
- close #3045122 - fix Drupal-CI Composer failure since Drupal 8.7.x+ - Update of drupal/coder squizlabs/php_codesniffer"
- replace drupal_ti by wengerk/docker-drupal-for-contrib
- ensure compatibility with Drupal 8.8+
- ensure compatibility with Drupal 9

## [1.0.0-beta5] - 2018-11-29
### Fixed
- fix #2987982 - Classes not saving on captioned images
- fix #3010610 - Removes links on images

## [1.0.0-beta4] - 2018-02-21
### Fixed
- fix #2919949 - The originalUpcast trigger a false positif
- fix #2930454 - Doesn't work with imce image button
- fix #2943420 - A parsing error, in the code below, with title and id attributes, while editing content

## [0.0.1] - 2017-03-07
### Added
- First draft.

[Unreleased]: https://github.com/antistatique/drupal-editor-advanced-image/compare/8.x-2.0...HEAD
[2.0.0]: https://github.com/antistatique/drupal-editor-advanced-image/compare/8.x-2.0-beta1...8.x-2.0
[2.0.0-beta1]: https://github.com/antistatique/drupal-editor-advanced-image/compare/8.x-1.0-beta5...8.x-2.0-beta1
[1.0.0-beta5]: https://github.com/antistatique/drupal-editor-advanced-image/compare/8.x-1.0-beta4...8.x-1.0-beta5
[1.0.0-beta4]: https://github.com/antistatique/drupal-editor-advanced-image/compare/8.x-1.0-beta1...8.x-1.0-beta4
[0.0.1]: https://github.com/antistatique/drupal-editor-advanced-image/releases/tag/8.x-1.0-beta1

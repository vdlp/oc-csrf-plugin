# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.4] - 2022-03-08

- Update GitHub workflow configuration
- Add .gitattributes file
- Add version constraint for "october/system"

## [2.0.3] - 2021-09-24

### Fixes
- Fixes type error when using `csrf_token()` Twig function.

## [2.0.2] - 2021-09-14

### Changed
- Location of CsrfServiceProvider to comply to internal plugin standards.
- Update Plugin documentation.
- Change version constraint for package `composer/installers`.

### Added
- Changelog file.

## [2.0.1] - 2021-07-13

### Fixes
- Location of plugin config.php file

## [2.0.0] - 2021-07-13

### Added
- Support for PHP 7.4 or higher.

## [1.1.2] - 2021-05-28

### Changed
- Update plugin dependencies.

## [1.1.1] - 2020-06-08

### Fixes
- Prevent error on CSRF token conversion.

## [1.1.0] - 2019-10-09

### Added
- Added configuration to exclude paths from CSRF validation.

## [1.0.0] - 2019-07-19

- First version of Vdlp.Csrf 

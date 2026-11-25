---
title: Release notes
description: Laravel-Lang versioning and platform compatibility.
---

## Versioning and compatibility

Laravel-Lang follows Semantic Versioning. Breaking changes and dropped platform support belong in major releases. The first-party packages share a release version. [Semantic Versioning](https://semver.org/)

Mona 20.x requires PHP `^8.3` and supports Laravel 12 and 13. Check your Laravel version's PHP requirements as well. Common requires the `intl` and `json` extensions through its dependencies.

Release tags and patch notes are available in [Mona releases](https://github.com/Laravel-Lang/mona/releases). See the [Laravel support policy](https://laravel.com/docs/releases#support-policy) for Laravel's maintenance dates.

## Package availability

Mona is the source of the release notes. The package export workflow updates the package repositories' `main` branches after pushes to Mona's `main`. Publishing a Mona release starts a separate export of its tag to each package repository. If the Mona release is marked as a prerelease, the package releases are marked the same way.

Before installing or [upgrading to 20.x](./upgrade.md), check that Packagist lists the version of the Composer package you use, such as [`laravel-lang/common`](https://packagist.org/packages/laravel-lang/common). If the version appears in Mona first, check the [package export runs](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) and the package's own releases while Packagist updates.

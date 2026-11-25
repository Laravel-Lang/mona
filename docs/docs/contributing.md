---
title: Contributing
description: Contribute translations, PHP packages, and documentation from a Mona checkout.
---

Fork the [Mona repository](https://github.com/Laravel-Lang/mona) and open your pull request against its `main` branch. Mona holds the source templates, shared translations, PHP packages, and documentation. The individual package repositories receive their contents from Mona.

## Fix or add a translation

1. Find the English phrase in the pack's `src/<Package>/source/` directory. For example, Laravel Framework and first-party Laravel package sources are under `src/Lang/source/`. The pack's `laravel_lang.json` lists the source files used by each upstream package version.
2. Open `src/Library/locales/<locale>/default.json`. Edit the value of the English phrase or add the entry if it is missing. Use the exact source value as the JSON key, including for phrases from a PHP file.
3. Keep placeholders such as `:count`, `:other`, and `:attribute` in the translated value. For an inline validation message, use the same source key in `inline.json` and replace the `:attribute` placeholder with wording that avoids naming the field. Preserve any other placeholders and plural forms.
4. If a translation intentionally matches the English phrase, add the phrase to the locale's `_settings.json` under `excludes`. Toolkit uses this list when generating translation status.
5. Open a pull request with the locale code in the title, such as `[fr]: Updated validation messages`.

If you find a typo in a source phrase from an upstream project, report it there so the source and its lookup key can be updated together.

The French default entry in `src/Library/locales/fr/default.json` uses the field name.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

The corresponding inline entry in `src/Library/locales/fr/inline.json` refers to the field generically.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

For published Mona translations, work in Library's `default.json` and `inline.json`. Some packs still contain legacy `php.json` and `json.json` dictionaries, which are also used by the automated pack status workflow.

To update country, currency, or locale names, edit the locale JSON file under `src/CountryNames/data/`, `src/CurrencyNames/data/`, or `src/LocaleNames/data/`. See [locales and translated names](./locales.md).

## Propose a locale

Check the [canonical locale codes](./available-locales.md). For a code already in the list, add or update its dictionary under `src/Library/locales/`. Toolkit can [create missing locale directories](./toolkit.md#manage-locale-directories). To propose a new code, open a [Laravel-Lang discussion](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Develop packages

Use Git, Composer 2, and PHP `^8.3` with `intl`, `json`, and `zip`. Enable `pdo_sqlite` for the tests, which use an in-memory database. Run these commands from the repository root.

```bash
composer install
composer check-platform-reqs
```

The root Composer project autoloads the code in `src/<Package>/src/`. Keep dependencies for each published package in its own `src/<Package>/composer.json`. Internal package constraints use `self.version`, so packages published from the same release can be installed together.

Use the Testbench application in `workbench/` to exercise package behavior locally. `composer serve` builds the workbench and starts the development server. To inspect its routes or Artisan commands from the repository root, run the Testbench CLI.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

Package dependency CI installs each package separately with a Composer path repository pointing to the other checked-out packages. If you change a package's dependencies, check its individual install as well as the root test suite. The workflow contains the setup and Laravel version matrix. [Test Dependencies workflow](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## Run local checks

For dictionary changes, install the root JavaScript dependencies and run the Biome checks. Toolkit's `status` command writes reports for entries whose saved value is identical to their source key. Check the pack's source files for phrases that have no dictionary entry yet. Use `git status --short` to review any generated reports. [Toolkit commands](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

For PHP changes, run Pest and PHPStan from the repository root. Run feature tests serially, as CI does, because model generation tests share the Testbench application directory. The architecture tests can run in parallel. To focus on one feature, pass its file or directory to Pest.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

If the route tests generate URLs with `http://localhost:8000` from the Testbench development settings, set `APP_URL` to `http://localhost` in the test process before running Pest.

`composer style` applies Composer normalization, Rector, and Pint fixes. Review its changes before submitting your pull request. For line coverage, enable Xdebug and run `composer run-script --timeout=0 test:coverage`.

For manifest changes, run the same schema check used in CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

See [translation plugins](./plugins.md) for the manifest format.

## Edit the documentation

The English pages for the current release live in `docs/docs/`. Their translations live in `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Update the matching pages when instructions change, and keep the same heading IDs for links across languages. The archived pre-Mona version lives in `docs/versioned_docs/version-legacy/`, with translations in the corresponding `version-legacy/` directories under `docs/i18n/`.

Use the Node.js version required by `docs/package.json`. Run the documentation commands from `docs/`.

```bash
cd docs
npm ci
npm start
```

Use `npm start -- --locale fr` to preview one translation. Stop the development server and run the production build to check links, anchors, and search indexing in all configured languages.

```bash
npm run typecheck
npm run build
npm run serve
```

Open the address printed by `serve` to check search and the generated Markdown exports. The build writes the static site to `docs/build/`.

## Publish a release

Merging into `main` exports each package subtree to its repository's `main` branch and updates the Mona release draft. Review the draft and publish the release in Mona with the shared version tag. A tag alone does not trigger the release workflows.

Publishing the release exports its tag and creates the matching release in each package repository. It also triggers the documentation site build and deployment. Check both workflows after publication. To preview the package splits before publishing, run **Export Packages** manually from `main` with `dry-run` enabled. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

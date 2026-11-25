---
title: Translation Toolkit
description: Maintain, migrate, and import translation sources and dictionaries from a Mona checkout.
---

Toolkit is the command-line tool for maintaining translation packs. Run it from a Mona checkout after installing the root Composer dependencies. For publishing translations in a Laravel application, use the [Artisan commands](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Check translation status

```bash
php src/Toolkit/bin/toolkit status src/Library
```

By default, the command writes `locales/<locale>/_status.json` within the project. It lists entries whose saved translation matches the source phrase and groups them by style. It combines exclusions from `locales/_settings.json` and `locales/<locale>/_settings.json` and deletes empty reports. To find phrases entirely absent from a dictionary, compare it with the pack's source files.

Pass `--output-dir` to write `<locale>.json` reports to a separate directory. The command resolves relative paths from the current working directory and creates the destination when it has a report to write.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Manage locale directories

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` creates missing directories for all canonical [locale codes](./available-locales.md). The new directories start empty. `delete-locale` deletes each immediate directory under `locales/` whose name is outside that list. Check the target before running it, especially if it contains locale aliases or custom codes.

## Remove obsolete dictionary entries

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` collects source phrases from packages with a `laravel_lang.json` manifest under `src/` and rewrites dictionaries in `src/Library/locales/`.
- `sync:package` reads one pack's `source/` directory and prunes unused entries in its `locales/` directory. It deletes dictionaries left empty by pruning.

Both keep existing translations for active phrases and preserve underscore-prefixed files such as `_settings.json`. Add translations for new phrases directly to the dictionary.

## Migrate legacy dictionaries

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Pass a legacy pack containing `source/` and `locales/`. The target is the directory that should contain the locale subdirectories. Pass `--target` in scripts. An interactive run prompts for it when omitted.

- `php.json` and `json.json` become `default.json`. The new keys are the original source phrases.
- `php-inline.json` and `json-inline.json` become `inline.json` for source phrases that contain `:attribute`.
- `_excludes.json` entries move into the target locale's `_settings.json` under `excludes`.

Toolkit keeps the legacy files and merges into the target dictionaries. On a key collision, the migrated translation wins. If both legacy formats supply a translation for the same phrase, the PHP dictionary wins. Use a separate destination to review the migration before replacing an active dictionary. See the [upgrade guide](./upgrade.md) for changes to your application or plugin manifest.

## Import sources from a ZIP archive

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` sets the final destination. `--paths` can be repeated for directories relative to the archive root.
- Selected PHP and JSON files are copied by filename into the destination. PHP files are copied as bytes. JSON files with the same name are merged, with incoming values taking precedence.
- Without `--only-copy`, Toolkit scans for supported translation calls with literal keys. If it finds phrases, it writes a JSON source named for the destination's parent directory, such as `framework.json` in the example.
- The command requires PHP's ZIP extension. It stages changes before publishing and attempts to restore the previous destination if publication fails.

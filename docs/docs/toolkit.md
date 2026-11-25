---
title: Translation Toolkit
description: Maintain, migrate, and import translation sources and dictionaries from a Mona checkout.
---

Toolkit is the command-line tool for maintaining translation packs. Run it from a Mona checkout after installing the root Composer dependencies. See the [contributor setup](./contributing.md) for checkout requirements. For publishing translations in a Laravel application, use the [Artisan commands](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

Pass paths relative to the directory where you run the command, or use absolute paths. Run `php src/Toolkit/bin/toolkit <command> --help` to see the arguments and options for any command.

## Check translation status

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

By default, the command writes `locales/<locale>/_status.json` within the project. It lists entries whose saved translation matches the source phrase and groups them by style. It combines exclusions from `locales/_settings.json` and `locales/<locale>/_settings.json` and deletes empty reports. To find phrases entirely absent from a dictionary, compare it with the pack's source files.

Pass `--output-dir` to write `<locale>.json` reports to a separate directory. The command resolves relative paths from the current working directory and creates the destination when it has a report to write.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

To exclude a phrase whose translation is intentionally identical to the source, add it to `excludes` in the appropriate `_settings.json`.

```json
{
  "excludes": ["Laravel"]
}
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

- `sync:library` collects source phrases from packages with a `laravel_lang.json` manifest directly under `src/` and rewrites dictionaries in `src/Library/locales/`. It defaults to the checkout's `src/` directory when you omit the argument.
- `sync:package` reads one pack's `source/` directory and prunes unused entries in its `locales/` directory. It deletes dictionaries left empty by pruning. It defaults to the current working directory when you omit the argument.

Both process directories with a canonical locale code, keep existing translations for active phrases, and preserve underscore-prefixed files such as `_settings.json`. Add translations for new phrases directly to the dictionary.

## Migrate legacy dictionaries

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Pass a legacy pack containing `source/` and `locales/`. Use `--target` to specify the directory that should contain the locale subdirectories. The default is the pack's `locales/` directory.

- `php.json` and `json.json` become `default.json`.
- `php-inline.json` and `json-inline.json` become `inline.json`.
- `_excludes.json` entries move into the target locale's `_settings.json` under `excludes`.

Legacy JSON dictionaries already use source phrases as keys. For PHP dictionaries, Toolkit maps legacy keys to phrases from the PHP files in `source/`. The migrated translation takes precedence over an existing target entry. If both legacy formats supply a translation for the same phrase, the PHP dictionary wins.

Toolkit keeps files in `source/` and deletes the legacy dictionaries and exclusion files it migrates, including when you specify a separate target. See the [upgrade guide](./upgrade.md) for changes to your application or plugin manifest.

## Import sources from a ZIP archive

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--url` and `--directory` are required. The URL must serve a ZIP archive over HTTP or HTTPS. `--paths` can be repeated for directories relative to the repository root inside the archive.
- Selected directories are scanned recursively. PHP and JSON files are copied by filename into the destination. A PHP file with the same filename is replaced. JSON files with the same name are merged, with incoming values taking precedence.
- Without `--only-copy`, Toolkit scans the repository for literal keys in supported translation calls such as `__()` and `Lang::get()`. If it finds phrases, it writes a JSON source named for the destination's parent directory, such as `framework.json` in the example. Omit `--paths` to run discovery alone. Review the resulting keys before adding them to a pack.
- The command requires PHP's ZIP extension. It stages changes before publishing and attempts to restore the previous destination if publication fails.

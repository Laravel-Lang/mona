---
title: 翻译工具包
description: 在 Mona 的本地检出副本中维护、迁移和导入翻译源文件及词典。
---

Toolkit 是维护翻译包的命令行工具。请先在本地 Mona 仓库安装根项目的 Composer 依赖，再运行此工具。要在 Laravel 应用中发布译文，请使用 [Artisan 命令](./translations.md)。

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## 检查翻译状态 {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

默认情况下，该命令在项目内写入 `locales/<locale>/_status.json`。报告会列出译文与源短语相同的条目，并按风格分组。命令会合并 `locales/_settings.json` 和 `locales/<locale>/_settings.json` 中的排除项，并删除空报告。要找出词典完全没有收录的短语，请将词典与翻译包的源文件对照。

传入 `--output-dir` 可将 `<locale>.json` 报告写入单独的目录。命令以当前工作目录为基准解析相对路径，并在有报告需要写入时创建目标目录。

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## 管理语言区域目录 {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` 会为所有规范[语言区域代码](./available-locales.md)创建缺失的目录，新目录初始为空。`delete-locale` 会删除 `locales/` 下名称未列在该清单中的各个直接子目录。运行前请核对目标，尤其是其中含有语言区域别名或自定义代码时。

## 移除过时的词典条目 {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` 收集 `src/` 下含有 `laravel_lang.json` 清单的包中的源短语，并重写 `src/Library/locales/` 中的词典。
- `sync:package` 读取一个翻译包的 `source/` 目录，清理其 `locales/` 目录中未使用的条目，并删除清理后变空的词典。

这两个命令都保留仍在使用的短语的现有译文，以及 `_settings.json` 等以下划线开头的文件。要添加新短语的译文，请直接修改词典。

## 迁移旧版词典 {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

传入包含 `source/` 和 `locales/` 的旧版翻译包。目标路径应指向存放各语言区域子目录的目录。脚本中请传入 `--target`。交互式运行时，如果未传入该选项，命令会询问目标路径。

- `php.json` 和 `json.json` 合并为 `default.json`，新键为原始源短语。
- `php-inline.json` 和 `json-inline.json` 中包含 `:attribute` 的源短语会移入 `inline.json`。
- `_excludes.json` 中的条目会移到目标语言区域的 `_settings.json` 文件的 `excludes` 下。

Toolkit 保留旧版文件，并将内容合并到目标词典。如果键冲突，以迁移的译文为准。如果旧版的两种格式都包含同一短语的译文，以 PHP 词典为准。请先用单独的目标目录检查迁移结果，再替换正在使用的词典。应用或插件清单的修改请参阅[升级指南](./upgrade.md)。

## 从 ZIP 压缩包导入源文件 {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` 设置最终目标目录。可以重复使用 `--paths`，指定相对于压缩包根目录的多个目录。
- 所选的 PHP 文件和 JSON 文件按文件名复制到目标目录。PHP 文件按原始字节复制。同名 JSON 文件将合并，以导入的值为准。
- 若未指定 `--only-copy`，Toolkit 会扫描使用字符串字面量作键的受支持的翻译调用。如果找到短语，会生成以目标目录的上一级目录命名的 JSON 源文件，例如上例中的 `framework.json`。
- 命令需要 PHP ZIP 扩展。它会先暂存更改，再发布文件。如果发布失败，会尝试恢复此前的目标目录。

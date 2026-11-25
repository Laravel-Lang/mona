---
title: 配置
description: 配置 Mona 的翻译风格、语言区域别名、发布筛选规则、标点、路由和模型。
---

Mona 会自动加载默认配置。将需要自定义的配置文件发布到应用的 `config/` 目录。

```bash
php artisan vendor:publish --tag=localization_main
```

`localization` 标签会发布全部五个文件。各文件去掉 `.php` 后的文件名也可用作发布标签和 Laravel 配置键。

| 文件 | 设置 |
| --- | --- |
| `localization_main.php` | 翻译风格、别名和发布筛选规则 |
| `localization_punctuation.php` | 智能标点和特定语言区域的引号 |
| `localization_routes.php` | [路由名称、语言区域来源和中间件](./routes.md) |
| `localization_models.php` | [翻译模型后缀、筛选和生成路径](./models.md) |
| `localization_map.php` | 语言区域元数据，如文字系统、地区和书写方向 |

Laravel 会用应用中的值覆盖每个顶层配置项。如果修改 `localization_punctuation.php` 中的 `locales` 数组，请保留仍需使用的语言区域默认规则。

更改配置后，如果配置已缓存，请运行 `php artisan config:clear` 清除缓存。运行 `php artisan lang:update`，重新生成受发布设置影响的译文。

## 翻译风格 {#translation-styles}

翻译风格是一份有名称的词典，用于修改指定短语的措辞。比如，翻译包可提供 `informal.json` 以采用更亲切的语气，或者提供 `john.json` 以采用某个产品的惯用语。风格名称与语言区域代码、应用名称或包名无关。翻译包可针对所支持的语言，在 `locales/<locale>/<style>.json` 中提供一种风格，即使其中只有一个短语也可以。最初的用途是支持非正式称呼。[关于非正式译文的讨论](https://github.com/orgs/Laravel-Lang/discussions/124)

安装提供 `john` 的插件后，将它放在 `config/localization_main.php` 中列表的末尾，使其在各词典来源内部优先。

```php
'styles' => ['default', 'informal', 'john'],
```

运行 `php artisan lang:update` 时，Publisher 会合并每个已安装语言区域的风格文件。对于同一短语，若 `john` 提供了对应译文，则优先采用，其次是 `informal`，最后是 `default`。例如，法语包可在 `locales/fr/john.json` 中覆盖某个短语，而德语的对应短语仍来自 `locales/de/informal.json` 或 `locales/de/default.json`。Publisher 分别对共享的 Library 词典和提供文件的翻译包词典应用这一顺序。对该文件而言，翻译包中匹配的短语优先于 Library 中的条目。

默认设置为 `['default', 'inline']`。`inline` 风格提供通用的验证消息，例如「此字段必须被接受」。将 `styles` 设为 `['default']` 可仅使用标准措辞。如果列表中省略了 `default`，Publisher 会将它补到开头。

## 语言区域别名 {#locale-aliases}

在 `localization_main.aliases` 中设置从规范代码到别名的映射。例如，在 `config/localization_main.php` 返回的数组中添加以下条目。

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan 可以接受 `de-CH`，而 Laravel 和已发布的文件使用 `de_CH`。请将 `APP_LOCALE` 和 `APP_FALLBACK_LOCALE` 设置为规范代码。转换和查找示例请参阅[语言区域和名称](./locales.md)。

匹配别名时区分大小写。请为每个语言区域使用唯一的别名，并避免与其他语言区域的规范代码重复。

## 跳过文件和强制启用集成 {#skip-files-and-force-integrations}

- `localization_main.skip` 使用不区分大小写的 `*` 模式匹配以 `/` 分隔的完整路径，可排除源文件、词典和发布目标。例如，`['*/lang/fr/validation.php']` 会阻止 Publisher 在使用 `lang/` 的应用中写入该目标文件。
- `localization_main.force` 接受 Composer 包名。即使指定的依赖未安装、已安装版本不匹配或根项目名称不同，也可以让 Publisher 使用清单条目。当清单中的译文适用于你的应用时，可使用此设置。

`skip` 优先于 `force`。

## 智能标点 {#smart-punctuation}

设置 `LOCALIZATION_PUNCTUATION_ENABLED=true`，即可在发布的译文中启用排版用引号、破折号和省略号。在 `localization_punctuation.common` 中编辑通用规则，在 `localization_punctuation.locales` 中按规范语言区域代码设置覆盖规则。Publisher 下次写入译文时会应用这些规则。

## 语言区域元数据 {#locale-metadata}

`localization_map.locales` 将规范代码映射到书写系统（`type`）、地区语言区域（`regional`）和可选的书写方向（`direction`）。如果未设置方向，请使用 `Direction::LeftToRight`。

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```

---
title: 管理译文
description: 使用 Mona Publisher 添加、更新和删除应用及第三方包的译文。
---

官方翻译包包含 Publisher。其 Artisan 命令会将 PHP 和 JSON 译文写入应用的翻译目录，通常为 `lang/`。包专用文件也可能写入 `lang/vendor/`。

请在应用根目录执行 Artisan 命令。翻译目录由 `lang_path()` 确定。

## 添加语言区域 {#add-locales}

传入一个或多个[语言区域代码](./available-locales.md)，以空格分隔。命令接受已配置的别名，但生成的文件使用规范代码。

```bash
php artisan lang:add fr de_CH
```

此命令会创建语言区域目录，然后更新**所有已安装的语言区域**，包括应用当前使用的语言区域和回退语言区域。如果没有传入参数，命令会询问是否安装所有受支持的语言区域。自动化脚本中请显式传入代码。

如果参数中包含未知代码，命令会在创建目录之前失败。

## 更新译文 {#update-translations}

```bash
php artisan lang:update
```

`lang:update` 会更新所有已安装的语言区域，不能传入语言区域代码作为参数。

Publisher 从应用当前及回退语言区域、翻译目录根下的文件或目录，以及 `lang/vendor/<package>/` 下的 JSON 文件中发现语言区域。仅有第三方包的 PHP 翻译目录时，该语言区域不会被识别为已安装。对它运行 `lang:add`，可创建对应的应用目录。

翻译包在[插件清单](./plugins.md)中声明支持的包、版本和目标文件。Publisher 将共享词典和翻译包词典合并后，把译文合并到各个目标文件。它保留无关的键，并替换匹配的键（包括数字键）。如果词典中缺少某个短语，Publisher 会写入源短语。请将自定义覆盖项保存在单独的翻译文件中，或[跳过](./configure.md#skip-files-and-force-integrations)自行维护的生成目标。

修改 Composer 依赖或[发布设置](./configure.md)后，请运行 `lang:update` 并检查生成的变更。

在新检出的项目中，请先运行 `composer install`，再发布译文。部署时可以随应用一起发布生成的文件，也可以在翻译目录可写的情况下现场生成。如果尚无文件，请使用所需的代码运行 `php artisan lang:add fr de_CH --no-interaction`。已有文件可通过 `php artisan lang:update --no-interaction` 更新。

## 删除语言区域 {#remove-locales}

```bash
php artisan lang:rm de_CH
```

可以传入规范代码或已配置的别名。

此命令会从翻译目录根及各直接的 `lang/vendor/<package>/` 目录中删除对应规范代码的目录和 JSON 文件，包括其中的自定义文件。当前和回退语言区域受保护。不传参数时，命令会询问是否删除所有未受保护的语言区域，默认选择否。如果语言区域目录为链接，命令会删除链接本身。

命令针对每个语言区域报告 `DONE`、`PROTECTED`、`INVALID`、`SKIP` 或 `FAILED`。如果尝试删除后仍有文件或目录保留，命令会显示其路径并返回失败退出码。请逐项查看状态，因为 `PROTECTED`、`INVALID` 和 `SKIP` 也可能伴随成功退出码。

## 查找 Artisan 命令 {#find-the-artisan-commands}

运行 `php artisan help lang:add` 检查命令是否已注册。如果命令缺失，请先用 `composer show laravel-lang/publisher` 检查软件包是否已安装，再运行 `php artisan package:discover`。如果已禁用 Config 和 Publisher 的自动发现，请在 `bootstrap/providers.php` 中注册 `LaravelLang\Config\LangConfigServiceProvider::class` 和 `LaravelLang\Publisher\LangPublisherServiceProvider::class`。

## JSON 回退 {#json-fallback}

当 JSON 键缺失时，`laravel-lang/json-fallback` 会先在回退语言区域的 JSON 中查找，再查找 PHP 译文。Common 已包含此包。单独安装时，请运行 `composer require laravel-lang/json-fallback`。

将 `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` 添加到 `bootstrap/providers.php` 的应用服务提供者列表。如果应用显式列出了 `Illuminate\Translation\TranslationServiceProvider`，请将该条目替换为 Mona 的服务提供者。[Laravel 服务提供者注册说明](https://laravel.com/docs/13.x/providers#registering-providers)

如果当前语言区域为法语、回退语言区域为英语，那么 `lang/fr.json` 中缺少的键就可以使用 `lang/en.json` 中的值。

如果只需在指定的语言区域查找，请通过 `Illuminate\Support\Facades\Lang` 门面调用 `Lang::get($key, fallback: false)`。

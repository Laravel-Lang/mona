---
title: 快速入门
description: 安装 Laravel-Lang 并为 Laravel 应用发布第一批译文。
---

Laravel-Lang 为 Laravel 及其生态中的软件包发布译文。`laravel-lang/common` 还提供[语言区域和名称查询](./locales.md)、[路由本地化](./routes.md)和[模型翻译](./models.md)。如果只需安装 Laravel 翻译包及其发布依赖项，请选择 `laravel-lang/lang`。

## 快速开始 {#quick-start}

请在 Laravel 12 或 13 应用的根目录下运行这些命令。环境需安装 Composer、PHP `^8.3` 以及 PHP 的 `intl` 和 `json` 扩展。你的 Laravel 版本可能需要更新的 PHP 版本。

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

在应用中使用 Laravel 的翻译辅助函数，读取已发布的法语消息。

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

要将法语设为默认语言，请在应用的 `.env` 文件中设置 `APP_LOCALE=fr`。设置 `APP_FALLBACK_LOCALE=en` 后，支持回退的查找在当前语言区域找不到译文时会使用英语。如果配置已缓存，请运行 `php artisan config:clear` 清除缓存。此后，`__('auth.failed')` 等调用会使用应用当前的语言区域。

- [早期版本的 Laravel-Lang](/legacy)
- [管理译文](./translations.md)和[查看可用语言区域](./available-locales.md)
- [配置本地化](./configure.md)
- [检查兼容性](./release-notes.md#versioning-and-compatibility)或[升级现有安装](./upgrade.md)

## 试用候选版本 {#try-a-release-candidate}

请在单独的 Laravel 测试应用中试用已发布的 20.x 候选版本。将该应用的最低稳定性设为 `RC`，以便 Composer 同时安装版本匹配的 Laravel-Lang 依赖。在 [Packagist](https://packagist.org/packages/laravel-lang/common) 上选择已列出的版本。将 `<release-candidate>` 替换为所选版本。

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```

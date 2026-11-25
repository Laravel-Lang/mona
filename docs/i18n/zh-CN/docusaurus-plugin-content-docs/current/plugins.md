---
title: 翻译插件
description: 安装翻译包，或发布自己的 Laravel-Lang Composer 插件。
---

Publisher 会发现通过 Composer 安装的翻译包。翻译包提供 `laravel_lang.json` 清单、PHP 或 JSON 源文件，也可自行提供语言区域词典。

## 安装翻译包 {#install-a-pack}

在 Laravel 应用中安装翻译包，然后更新已使用的语言区域。例如，Actions 包会添加按钮和其他操作的标签。

```bash
composer require laravel-lang/actions
php artisan lang:update
```

添加语言区域请参阅[管理译文](./translations.md)，第三方翻译包请参阅[社区插件](./plugins-community.md)。

### 官方翻译包 {#official-translation-packs}

Common 包含以下翻译包，也可以使用 Composer 单独安装。

| 包 | 翻译内容 |
| --- | --- |
| `laravel-lang/lang` | Laravel 框架以及 Breeze、Cashier、Fortify、Jetstream、Nova、Passkeys 和 Laravel UI 等包 |
| `laravel-lang/starter-kits` | Laravel 的 Livewire、React、Svelte 和 Vue 入门套件 |
| `laravel-lang/moonshine` | MoonShine 管理后台 |
| `laravel-lang/actions` | 按钮和其他操作的标签 |
| `laravel-lang/attributes` | 便于阅读的验证属性名称 |
| `laravel-lang/http-statuses` | HTTP 状态说明 |

每个翻译包都会引入 Publisher 和共享的 Library。各包在清单中指定可以为哪些集成及版本发布译文。

## 创建翻译包 {#create-a-pack}

从翻译模板开始。在翻译包的 `composer.json` 中要求安装 `laravel-lang/publisher`，并声明支持的版本。如果翻译包依赖共享译文，还需要 `laravel-lang/library`。Publisher 会在应用的 `vendor/*/*/laravel_lang.json` 中查找清单。[翻译模板](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

将清单放在翻译包的根目录。下例为每个已安装的语言区域发布一个 JSON 文件和一个 PHP 翻译组。

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

每个 `source` 路径相对于翻译包的 `source/` 目录。每个 `target` 路径相对于应用的 Laravel 翻译目录，并包含 `{locale}`。

例如，在 `source/audit.json` 中添加以下内容。

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

在 `source/audit.php` 中添加以下内容。

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

在发布法语译文时，这两个文件都会在翻译包的 `locales/fr/default.json` 中查找该英文短语。

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

在应用中，`__('Enable audit trail')` 读取 JSON 译文，`__('audit.enable')` 读取 PHP 译文。同一短语的包内译文优先于共享 Library 条目。如果两份词典都不包含该短语，Publisher 会使用源值。

为翻译包设置独立的 Composer 名称，并将模板中的 `self.version` 替换为 适用于受支持版本的约束。词典目录使用规范语言区域代码。标准翻译放在 `default.json`，不同样式放在 `inline.json` 等文件中。

### 按条件应用译文 {#apply-translations-conditionally}

| 清单属性 | 应用映射的条件 |
| --- | --- |
| 无条件 | Publisher 始终包含该映射。 |
| `vendor` | 已安装指定的 Composer 包。 |
| `vendor` 和 `version` | 已安装的包满足 Composer 版本约束。 |
| `vendor` 和 `project: true` | 根项目的 Composer 名称与 `vendor` 匹配。在此模式下，Publisher 检查项目名。 |

如果映射既要适用于已安装的依赖，又要适用于同名的根项目，请使用两个清单条目。迁移旧版服务提供者请参阅[升级指南](./upgrade.md)，跳过规则和强制包匹配请参阅[配置](./configure.md)。

### 在 Laravel 应用中测试包 {#test-a-pack-in-a-laravel-application}

如果翻译包在测试应用的同级目录中，请在应用根目录运行这些命令。检查 `lang/fr.json` 和 `lang/fr/audit.php`，将应用的语言区域设为 `fr`，测试两个翻译键。修改翻译包后，运行 `php artisan lang:update`。

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```

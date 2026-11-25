---
title: 翻訳プラグイン
description: 翻訳パックをインストールしたり、Laravel-Lang 用の独自 Composer プラグインを公開したりします。
---

Publisher は Composer でインストールされた翻訳パックを検出します。パックは `laravel_lang.json` マニフェスト、PHP または JSON のソースファイルを提供し、独自のロケール辞書も含めることができます。

## パックをインストールする {#install-a-pack}

Laravel アプリケーションにパックをインストールし、すでに使用しているロケールを更新します。たとえば Actions パックはボタンなどの操作ラベルを追加します。

```bash
composer require laravel-lang/actions
php artisan lang:update
```

ロケールの追加については[翻訳の管理](./translations.md)、サードパーティーのパックについては[コミュニティプラグイン](./plugins-community.md)を参照してください。

### 公式翻訳パック {#official-translation-packs}

Common には、以下のパックが含まれています。Composer でパックごとにインストールすることもできます。

| パッケージ | 翻訳対象 |
| --- | --- |
| `laravel-lang/lang` | Laravel フレームワークと Breeze、Cashier、Fortify、Jetstream、Nova、Passkeys、Laravel UI などのパッケージ |
| `laravel-lang/starter-kits` | Laravel の Livewire、React、Svelte、Vue スターターキット |
| `laravel-lang/moonshine` | MoonShine 管理パネル |
| `laravel-lang/actions` | ボタンなどの操作ラベル |
| `laravel-lang/attributes` | 読みやすいバリデーション属性名 |
| `laravel-lang/http-statuses` | HTTP ステータスの文言 |

各パックは Publisher と共有の Library を依存関係として追加します。マニフェストには、どの連携機能とバージョンに対して翻訳を公開できるかを指定します。

## パックを作成する {#create-a-pack}

翻訳テンプレートをもとに作成します。パックの `composer.json` に `laravel-lang/publisher` を依存関係として追加し、対応するバージョンを宣言します。共有翻訳を利用する場合は `laravel-lang/library` も追加します。Publisher はアプリケーションの `vendor/*/*/laravel_lang.json` からマニフェストを探します。[翻訳テンプレート](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

マニフェストをパックのルートに配置します。この例は、インストール済みのロケールごとに JSON ファイルと PHP 翻訳グループを公開します。

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

`source` のパスは、パックの `source/` ディレクトリからの相対パスです。`target` はアプリケーションの Laravel 言語ディレクトリからの相対パスで、`{locale}` を含みます。

`source/audit.json` の例を次に示します。

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

`source/audit.php` は以下のように記述します。

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

フランス語を公開する場合、どちらもパックの `locales/fr/default.json` で英語の文言を検索します。

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

アプリケーションでは `__('Enable audit trail')` が JSON 翻訳を、`__('audit.enable')` が PHP 翻訳を読み取ります。同じ文言については、パックの翻訳が共有の Library の翻訳に優先します。どちらの辞書にも文言がなければ、Publisher は原文を使用します。

パック固有の Composer 名を付け、テンプレートの `self.version` を 対象リリースの制約に置き換えます。辞書ディレクトリには正規ロケールコードを使います。標準の翻訳は `default.json` に、スタイル別の翻訳は `inline.json` などに置きます。

### 条件付きで翻訳を適用する {#apply-translations-conditionally}

| マニフェストのプロパティ | マッピングの適用条件 |
| --- | --- |
| 条件なし | Publisher は常に適用します。 |
| `vendor` | 指定した Composer パッケージがインストールされています。 |
| `vendor` と `version` | インストール済みのパッケージが Composer のバージョン制約を満たします。 |
| `vendor` と `project: true` | ルートプロジェクトの Composer 名が `vendor` と一致します。このモードで Publisher は名前を照合します。 |

インストール済みの依存パッケージと、その名前のルートプロジェクトの両方にマッピングを適用するには、マニフェストにエントリを 2 つ記述します。旧プロバイダーの移行は[アップグレードガイド](./upgrade.md)、除外ルールとパッケージの強制照合は[設定](./configure.md)を参照してください。

### Laravel アプリケーションでパックをテストする {#test-a-pack-in-a-laravel-application}

パックが隣のディレクトリにある状態で、テスト用アプリケーションのルートから実行します。`lang/fr.json` と `lang/fr/audit.php` を確認し、アプリケーションのロケールを `fr` に設定して両方のキーをテストします。パックの変更後は `php artisan lang:update` を実行します。

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```

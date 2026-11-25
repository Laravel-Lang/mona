---
title: はじめに
description: Laravel-Lang をインストールし、Laravel に最初の翻訳を公開します。
---

Laravel-Lang は Laravel とその関連パッケージの翻訳を公開します。`laravel-lang/common` には、[ロケールと名前の取得](./locales.md)、[ルートのローカライズ](./routes.md)、[モデルの翻訳](./models.md) も含まれます。Laravel の翻訳パックと公開に必要な依存パッケージだけをインストールする場合は、`laravel-lang/lang` を選んでください。

## クイックスタート {#quick-start}

Composer、PHP `^8.3`、PHP の `intl` および `json` 拡張が利用できる Laravel 12 または 13 のアプリケーションのルートで、次のコマンドを実行します。Laravel のバージョンによっては、より新しい PHP が必要です。

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

アプリケーションで Laravel の翻訳ヘルパーを使い、公開したフランス語のメッセージを取得します。

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

フランス語を既定にするには、アプリケーションの `.env` ファイルに `APP_LOCALE=fr` を設定します。フォールバックが有効な検索で現在のロケールに訳がない場合に英語を使うには、`APP_FALLBACK_LOCALE=en` を設定します。設定がキャッシュされている場合は、`php artisan config:clear` でクリアします。これで `__('auth.failed')` などの呼び出しにアプリケーションの現在のロケールが使用されます。

- [旧バージョンの Laravel-Lang](/legacy)
- [翻訳を管理する](./translations.md)、[利用可能なロケールを確認する](./available-locales.md)
- [ローカライズを設定する](./configure.md)
- [互換性を確認する](./release-notes.md#versioning-and-compatibility)、[既存の環境をアップグレードする](./upgrade.md)

## リリース候補版を試す {#try-a-release-candidate}

公開済みの 20.x リリース候補版を試すには、専用の Laravel テストアプリケーションを使用します。Composer が対応する Laravel-Lang の依存パッケージもインストールできるよう、そのアプリケーションの最小安定度を `RC` に設定します。[Packagist](https://packagist.org/packages/laravel-lang/common) に掲載されているバージョンを選択してください。`<release-candidate>` を選んだバージョンに置き換えてください。

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```

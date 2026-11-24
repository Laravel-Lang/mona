---
title: 翻訳の管理
description: Mona の Publisher を使い、アプリケーションと依存パッケージの翻訳を追加、更新、削除します。
---

Publisher は公式翻訳パックに含まれています。Artisan コマンドは PHP および JSON の翻訳をアプリケーションの言語ディレクトリ（通常は `lang/`）に書き込みます。パッケージ固有のファイルは `lang/vendor/` 以下に書き込まれる場合もあります。

## ロケールを追加する {#add-locales}

1 つ以上の[ロケールコード](./available-locales.md)をスペースで区切って指定します。入力には設定済みのエイリアスも使用でき、生成されるファイルには正規コードが使われます。

```bash
php artisan lang:add fr de_CH
```

このコマンドはロケール用ディレクトリを作成し、アプリケーションの現在のロケールとフォールバックロケールを含む**インストール済みのすべてのロケール**を更新します。引数がなければ、対応するすべてのロケールをインストールするかどうかを確認します。自動化ではコードを明示してください。

## 翻訳を更新する {#update-translations}

```bash
php artisan lang:update
```

Publisher は、アプリケーションの現在のロケールとフォールバックロケール、言語ディレクトリ直下のファイルまたはディレクトリ、および `lang/vendor/<package>/` 以下の JSON ファイルからロケールを検出します。依存パッケージの PHP 翻訳ディレクトリがあるだけでは、インストール済みとは見なされません。そのロケールを `lang:add` に指定して、アプリケーションの言語ディレクトリを作成してください。

翻訳パックは[プラグインマニフェスト](./plugins.md)で、対応するパッケージとバージョン、出力先ファイルを宣言します。Publisher は共有辞書とパッケージ辞書を組み合わせ、翻訳を各出力先ファイルにマージします。無関係なキーは保持し、数値キーを含め、一致するキーは置き換えます。辞書に文言がなければ原文を書き込みます。独自の上書きは別の翻訳ファイルに保存するか、自分で管理する生成先を[除外](./configure.md#skip-files-and-force-integrations)してください。

Composer の依存関係や[公開設定](./configure.md)を変更した後は、`lang:update` を実行して生成された差分を確認します。

## ロケールを削除する {#remove-locales}

```bash
php artisan lang:rm de_CH
```

このコマンドは言語ディレクトリのルートと直下の各 `lang/vendor/<package>/` ディレクトリから、正規コードに対応するディレクトリと JSON ファイルを削除します。そこに含まれる独自ファイルも削除されます。現在のロケールとフォールバックロケールは保護されます。引数を省略すると保護されていないロケールをすべて削除するかどうかを確認し、既定では削除しません。ロケールディレクトリがリンクの場合、コマンドはリンク自体を削除します。

コマンドはロケールごとに `DONE`、`PROTECTED`、`INVALID`、`SKIP`、`FAILED` のいずれかを報告します。削除を試みた後もファイルやディレクトリが残っている場合は、そのパスを表示し、失敗を示す終了コードを返します。`PROTECTED`、`INVALID`、`SKIP` でも成功を示す終了コードが返るため、ロケールごとの結果を確認してください。

## JSON フォールバック {#json-fallback}

JSON にキーが見つからない場合、`laravel-lang/json-fallback` は PHP の翻訳を探す前にフォールバックロケールの JSON を検索します。このパッケージは Common に含まれます。個別にインストールするには `composer require laravel-lang/json-fallback` を実行してください。

`bootstrap/providers.php` のアプリケーションプロバイダーに `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` を追加します。`Illuminate\Translation\TranslationServiceProvider` を明示的に登録している場合は、そのエントリを Mona のプロバイダーに置き換えます。[Laravel のプロバイダー登録](https://laravel.com/docs/13.x/providers#registering-providers)

現在のロケールがフランス語で、フォールバックが英語の場合、`lang/fr.json` にないキーの値を `lang/en.json` から取得できます。

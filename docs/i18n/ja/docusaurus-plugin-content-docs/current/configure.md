---
title: 設定
description: Mona の翻訳スタイル、ロケールのエイリアス、公開時のフィルター、句読法、ルート、モデルを設定します。
---

Mona は既定の設定を自動的に読み込みます。カスタマイズするファイルをアプリケーションの `config/` ディレクトリに公開してください。

```bash
php artisan vendor:publish --tag=localization_main
```

`localization` タグは 5 ファイルすべてを公開します。また、各ファイル名から `.php` を除いたものが個別の公開タグと Laravel の設定キーになります。

| ファイル | 設定内容 |
| --- | --- |
| `localization_main.php` | 翻訳スタイル、エイリアス、公開時のフィルター |
| `localization_punctuation.php` | スマート句読法とロケール固有の引用符 |
| `localization_routes.php` | [ルート名、ロケールの取得元、ミドルウェア](./routes.md) |
| `localization_models.php` | [翻訳モデルの接尾辞、フィルタリング、生成先のパス](./models.md) |
| `localization_map.php` | 文字体系、地域、書字方向などのロケールのメタデータ |

Laravel は最上位の各設定項目をアプリケーションの値で置き換えます。`localization_punctuation.php` の `locales` 配列を編集するときは、引き続き使う既定のロケール別設定を残してください。

設定変更後、既存の設定キャッシュがあれば `php artisan config:clear` でクリアします。公開設定の影響を受ける翻訳は `php artisan lang:update` で再生成します。

## 翻訳スタイル {#translation-styles}

翻訳スタイルは、一部の文言を変更する名前付き辞書です。たとえば、パックには、親しみやすい語調の `informal.json` や、製品独自の文言を定義する `john.json` を含めることができます。スタイル名はロケールコード、アプリケーション名、パッケージ名と独立しています。翻訳パックは、対応する言語のスタイルを `locales/<locale>/<style>.json` に配置できます。スタイルは 1 つの文言だけでも定義できます。当初はくだけた呼びかけ方に対応するための機能でした。[カジュアルな翻訳に関する議論](https://github.com/orgs/Laravel-Lang/discussions/124)

`john` を提供するプラグインをインストールした後、各辞書の中で優先されるように、`config/localization_main.php` のスタイル一覧の末尾に追加します。

```php
'styles' => ['default', 'informal', 'john'],
```

`php artisan lang:update` を実行すると、Publisher はインストール済みのロケールごとにスタイルのファイルをマージします。各文言について `john` に翻訳があればそれが使われ、続いて `informal`、`default` の順に検索します。たとえば、フランス語パックが `locales/fr/john.json` で 1 つの文言を上書きする一方で、ドイツ語では同じ文言に `locales/de/informal.json` または `locales/de/default.json` が使われます。Publisher は共有の Library と、そのファイルを提供するパックそれぞれに対してこの順序を適用します。そのファイルに同じ文言があれば、パックの翻訳が Library のエントリより優先されます。

既定の設定は `['default', 'inline']` です。`inline` スタイルは、たとえば「このフィールドを承認してください」のような汎用のバリデーションメッセージを提供します。標準の文言のみを使用する場合は、`styles` を `['default']` に設定してください。リストに `default` がなければ、Publisher が先頭に追加します。

## ロケールのエイリアス {#locale-aliases}

`localization_main.aliases` に正規コードからエイリアスへの対応を設定します。たとえば、`config/localization_main.php` が返す配列に以下のエントリを追加します。

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan は `de-CH` を受け付けますが、Laravel と公開されるファイルでは `de_CH` を使用します。`APP_LOCALE` と `APP_FALLBACK_LOCALE` には正規コードを設定してください。変換や検索の例は[ロケールと名称](./locales.md)を参照してください。

エイリアスは大文字と小文字を区別します。ロケールごとに、他のロケールの正規コードと重複しない一意のエイリアスを使ってください。

## ファイルの除外と連携の強制 {#skip-files-and-force-integrations}

- `localization_main.skip` は、大文字と小文字を区別しない `*` パターンで、区切りに `/` を使用したフルパスに照合します。ソースファイル、辞書、出力先を除外できます。たとえば、`['*/lang/fr/validation.php']` を指定すると、`lang/` を使うアプリケーションで Publisher がその出力先に書き込まなくなります。
- `localization_main.force` は、Composer のパッケージ名を受け付けます。依存パッケージが存在しない場合、インストール済みのバージョンが一致しない場合、ルートプロジェクト名が異なる場合でも、Publisher はそのマニフェストの翻訳を使用できます。マニフェストの翻訳がアプリケーションに適用できる場合に使用してください。

`skip` は `force` より優先されます。

## スマート句読法 {#smart-punctuation}

`LOCALIZATION_PUNCTUATION_ENABLED=true` を設定すると、公開される翻訳で組版用の引用符、ダッシュ、省略記号を使用できます。共通ルールは `localization_punctuation.common` で編集し、ロケールごとの上書きは正規コードをキーとして `localization_punctuation.locales` に設定します。Publisher が次に翻訳を書き込むときに、これらのルールが適用されます。

## ロケールのメタデータ {#locale-metadata}

`localization_map.locales` は正規コードを、文字体系（`type`）、地域付きロケール（`regional`）、任意の書字方向（`direction`）に対応付けます。書字方向が設定されていない場合は `Direction::LeftToRight` を使います。

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```

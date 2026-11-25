---
title: Mona へのアップグレード
description: Laravel-Lang の依存関係、設定、公開済みの翻訳、アプリケーションとの連携を更新します。
---

Mona は Laravel-Lang のパッケージをまとめてリリースするリポジトリです。アプリケーションで必要な個別の Composer パッケージは引き続き使用できます。

:::info[なぜバージョン 20 なのか？]

Laravel-Lang は `laravel-lang/lang` から始まり、複数のパッケージからなるエコシステムへと成長しました。各パッケージは異なる時期に生まれ、それぞれ独自にバージョンを付けてリリースしていました。メジャーアップグレードのたびに、メンテナーはパッケージ間の調整を行い、利用者はどのバージョンを組み合わせてインストールできるかを調べる必要がありました。

そこで、互換性のある依存パッケージのバージョンをまとめて指定する `laravel-lang/common` を作りました。アップグレードでは、独自のバージョンを持つ複数のパッケージを一度に更新することもありました。

たとえば Mona 以前に Laravel 11 と PHP 8.2 を使っていたら、どのパッケージのどのバージョンをインストールすればよかったのでしょうか？

<details>
<summary>Mona 以前のパッケージバージョン</summary>

| パッケージ | バージョン |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1`（他の Laravel-Lang パッケージが引き続き使っていた 1.x 系のバージョン）と `2.3.2`（Mona 以前の最新安定版で、他のパッケージではほとんど使われていなかった） |
| Publisher | `16.9.0` |

</details>

Mona では、すべてのパッケージを同じバージョン番号でまとめてリリースします。以前は Publisher のバージョン番号が最も大きく、その番号を引き継ぐなら次のメジャーバージョンは `17.0` でした。エコシステムを一つにまとめるために時間と力を注いできた私たちは、この節目に 20 を選びました。**Laravel-Lang 2.0** とも読めます。 🙂

:::

:::info[ロケールコードとエイリアス]

Laravel-Lang では、公開する言語ファイルのパスやモデルの翻訳行など、保存する値に正規ロケールコードを使用します。Laravel の現在のロケールとフォールバックロケールも正規コードを使用します。実行時には URL セグメントや言語選択でエイリアスを使用できます。ロケールコードを返すファサードのメソッドは、既定で設定済みのエイリアスを返し、エイリアスがなければ正規コードを返します。たとえば `localization_main.aliases` に `'pt_BR' => 'pt-br'` を設定すると、`/pt-br/account` を維持しつつ `lang/pt_BR.json` に公開し、データベースには `pt_BR` を保存できます。

:::

## 依存関係を更新する {#updating-dependencies}

現行バージョンは Laravel 12 と 13 に対応し、PHP `^8.3` が必要です。アプリケーションの `composer.json` で、`require` または `require-dev` にある以下の各パッケージのバージョン制約を `^20.0` に設定します。`laravel-lang/common` だけをインストールしている場合は、その依存関係だけを変更してください。

`20.x` をインストールまたはアップグレードする前に、必要な Composer パッケージのバージョンが Packagist にあることを確認してください。Mona のリリースと対応する Composer パッケージは公開時期が異なる場合があります。

| 用途 | Composer パッケージ |
| --- | --- |
| バンドル | `laravel-lang/common` |
| 翻訳パック | `laravel-lang/lang`、`laravel-lang/starter-kits`、`laravel-lang/moonshine`、`laravel-lang/actions`、`laravel-lang/attributes`、`laravel-lang/http-statuses` |
| 公開と設定 | `laravel-lang/publisher`、`laravel-lang/config`、`laravel-lang/json-fallback`, `laravel-lang/library` |
| アプリケーションとの連携 | `laravel-lang/locales`、`laravel-lang/locale-list`、`laravel-lang/routes`、`laravel-lang/models` |
| 共通ユーティリティ | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| パックのテンプレート | `laravel-lang/translations-template` |

標準の翻訳手順だけを使用している環境では、依存関係を更新し、翻訳を再公開します。

```bash
composer update
php artisan lang:update
```

プロバイダーを登録した場合、カスタム設定を公開した場合、エイリアスやサードパーティーの翻訳パックを使う場合、またはアプリケーションからパッケージの API を呼び出す場合は、再公開の前に、以下の該当する節の手順を実行してください。

## 名前が変わった依存関係や廃止された依存関係を置き換える {#replace-renamed-or-retired-dependencies}

`composer update` を実行する前に、`composer.json` で旧版の名称パッケージへの直接依存を更新します。

| 旧 Composer パッケージ | `^20.0` で指定する依存関係 |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common はすでに 3 つの名称パッケージすべてに依存しています。PHP コードも更新してください。[名称検索 API の置き換え](#replace-the-name-lookup-apis)を参照してください。

各サードパーティープラグインに、新しい Publisher と互換性のあるリリースがあるかを確認します。`laravel-lang/translator` または `laravel-lang/status-generator` を使用している場合は、それぞれの依存関係を削除する前に移行してください。[翻訳ツール](#update-json-fallback-and-translation-tooling)を参照してください。

## サービスプロバイダーを更新する {#update-service-providers}

Laravel はパッケージマニフェストから Config、Publisher、Routes、Models のプロバイダーを自動検出します。自動検出を使用している場合は、旧プロバイダークラスを参照するアプリケーションコードやテストを更新します。手動で登録している場合は、`bootstrap/providers.php` や `config/app.php` など、同じプロバイダー一覧で次のように置き換えます。

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

`LaravelLang\Locales\LaravelLangLocalesServiceProvider` と、`LaravelLang\Actions`、`LaravelLang\Attributes`、`LaravelLang\HttpStatuses`、`LaravelLang\Lang`、`LaravelLang\MoonShine`、`LaravelLang\StarterKits` 以下にある旧 `ServiceProvider` クラスの手動登録を削除します。これらの翻訳パッケージはマニフェストで検出されます。

JSON フォールバックを使用している場合は、登録先で `LaravelLang\JsonFallback\TranslationServiceProvider` を `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` に置き換えます。これにより Laravel の翻訳プロバイダーの上書きが引き続き有効になります。

パッケージ検出時にアプリケーションを起動できるよう、`composer update` の前に古いプロバイダー参照を更新します。旧クラスの読み込みで Composer スクリプトが引き続き失敗する場合は、`composer update --no-scripts` を実行し、参照を更新してから `composer dump-autoload` で検出をやり直します。

## 設定を移行する {#migrate-configuration}

`config/localization.php` をカスタマイズしていた場合は、変更を移す間、参照用に保持してください。パッケージは新しい既定値を自動で読み込みます。編集する[設定ファイル](./configure.md)を公開してください。`localization` タグは 5 ファイルすべてを公開します。1 ファイルだけを公開するには、`.php` を除いたファイル名をタグに使用します。

```bash
php artisan vendor:publish --tag=localization
```

### エイリアス、翻訳スタイル、ファイルの書式 {#aliases-translation-styles-and-file-formatting}

- `aliases` の対応表を `config/localization_main.php` にコピーします。`'de_CH' => 'de-CH'` のように、正規コードをキー、エイリアスを値にします。
- `config/localization_main.php` の `inline` を `styles` に置き換えます。標準のバリデーションメッセージを使う以前の既定動作を維持するには `'styles' => ['default']` を指定します。インラインメッセージを使用していた場合は、新しい既定値の `['default', 'inline']` を使用します。環境で設定していた `LOCALIZATION_INLINE` と `LANG_PUBLISHER_INLINE` は削除します。
- `align` キーと、環境変数 `LOCALIZATION_ALIGN`、`LANG_PUBLISHER_ALIGN` を削除します。公開された PHP 配列で `=>` 演算子を揃えるには、プロジェクトの PHP フォーマッターを設定し、公開後に言語ファイルに適用します。たとえば Laravel Pint の `binary_operator_spaces` ルールでは、`=>` に `align_single_space_minimal` を適用できます。

### スマート句読法 {#smart-punctuation}

スマート句読法を有効にしていた場合は、`LOCALIZATION_SMART_ENABLED` を `LOCALIZATION_PUNCTUATION_ENABLED` に変更し、値を `true` のままにします。PHP で `smart_punctuation.enable` を直接設定していた場合は、真偽値を `config/localization_punctuation.php` の最上位の `enabled` キーへ移します。句読法は既定で無効です。

`smart_punctuation.common` と `smart_punctuation.locales` にあるカスタム引用符を、同じファイルの最上位の `common` と `locales` 配列へ移します。`locales` のキーには正規コードを使用します。ロケールごとの引用符は `common` の対応するキーを上書きします。`&nbsp;` を使用する古いフランス語の例をコピーしていた場合は、引用符の文字列に実際の改行しないスペースまたは `"\u{00A0}"` を使います。

### ローカライズされたルート {#localized-routes}

旧設定の `routes` で上書きしていた値を `config/localization_routes.php` に移します。

| カスタマイズした項目 | `config/localization_routes.php` での変更 | 名前を変更する環境変数 |
| --- | --- | --- |
| ロケールパラメーター、ヘッダー、Cookie、セッションキー、ユーザーモデルのカラム | `names` の上書きをコピーし、残りの既定値を保持します。 | |
| ローカライズされたルート名に付加するプレフィックス | `name_prefix` を `prefix` に移します。値が空の場合は `localized.` が使われます。 | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| プレフィックス付きルートからのリダイレクト | `redirect_default` を `redirect` へ移します。 | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| URL ヘルパーがフォールバックロケールを省略するかどうか | `hide_default` を `hide` へ移します。 | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| ローカライズされたルートグループのミドルウェア | プレフィックスなしのルートは `group.middlewares.default` から `middlewares.default`、プレフィックス付きのルートは `group.middlewares.prefix` から `middlewares.prefix` に移します。[ルートの移行](#update-localized-routes-and-middleware)に従ってミドルウェアのクラス名を更新します。 | |

### モデルの翻訳 {#model-translations}

`models` の上書きを `config/localization_models.php` に移します。カスタムの `suffix` と `helpers` の値は最上位へコピーします。旧 `filter.enabled` キーの代わりに真偽値の `filter` を使います。環境変数でフィルタリングや IDE ヘルパーのパスを設定している場合は、`LOCALIZATION_FILTER_ENABLED` と `VENDOR_PATH` を保持します。

モデル用コマンドは、既定で `app/Models` を走査します。旧版が `app/` 全体を走査することに依存していた場合や、`localization-private.models.directory` を自分で設定していた場合は、走査したいディレクトリを `directories` 配列に指定します。たとえば旧版と同じ範囲を走査するには `'directories' => [app_path()]` と設定します。

### アプリケーションコードが参照する設定 {#configuration-used-by-application-code}

- `LaravelLang\Config\Facades\Config` 経由の読み書きは、新しいキーを使った Laravel の設定呼び出しに置き換えます。たとえば、`Config::shared()->models->filter->enabled` を `config('localization_models.filter')` に置き換えます。型付きで取得したい場合は `LaravelLang\Config\Services\ModelLangConfig` を注入し、`filter` プロパティを読み取ります。ルート設定には、同じ名前空間の `RouteLangConfig` を注入できます。
- コードが `localization-private.map` からロケールのメタデータを読み取る場合は、`localization_map.locales` に切り替えます。上書きは `config/localization_map.php` の `locales` 配列にコピーします。
- `localization-private.plugins` と `localization-private.packages` による登録を[プラグインマニフェスト](./plugins.md)に置き換えます。
- 旧版の `translators` ブロックと、`laravel-lang/translator` 専用だった古い `TRANSLATION_*` 設定を削除します。

上書き内容を移し、アプリケーションの参照を更新したら、`config/localization.php` を削除して `php artisan config:clear` を実行します。

## 言語ファイルを正規コードのパスに移す {#move-language-files-to-canonical-paths}

1. `APP_LOCALE` と `APP_FALLBACK_LOCALE` に正規コードを設定します。URL とロケール入力が引き続き機能するように、エイリアスを `localization_main.aliases` に移します。
2. `lang/pt-br.json` や `lang/pt-br/` のようにエイリアスが使われているファイルとディレクトリを `lang/pt_BR.json` と `lang/pt_BR/` に変更します。`lang/vendor/<package>/` 以下にも同じエイリアスがないか確認します。両方の名前が存在する場合は、エイリアスのパスを削除する前に内容をマージし、カスタム翻訳の競合を解決します。
3. 依存パッケージの PHP 翻訳にしか存在しないロケールは、それぞれ `php artisan lang:add <locale>` でインストールします。インストール済みロケールの一覧は、言語ディレクトリ直下の項目、`lang/vendor/<package>/` 以下の JSON ファイル、および実際に有効な現在のロケールとフォールバックロケールに基づきます。
4. 設定とプラグインを移行したら `php artisan lang:update` を実行し、差分を確認します。Publisher は、ファイルの公開を終了した連携機能のキーを保持することがあります。アプリケーションがまだ使用するキーを残し、呼び出し元を確認してから不要になったキーを削除します。ファイルの再生成や保持については[公開スクリプトとカスタム翻訳](#update-publication-scripts-and-keep-custom-translations)を参照してください。

## 連携を更新する {#update-integrations}

### Locales パッケージ {#locales}

`LaravelLang\Locales\Facades\Locales` のインポートは保持し、呼び出し元が使用するデータに応じてメソッドを更新します。

| 以前の呼び出し | 置き換え |
| --- | --- |
| オブジェクトを返す `Locales::available()`、`installed()`、`protects()` | `Locales::data()` に対して同じメソッドを呼び出します。 |
| `Locales::raw()->available()`、`installed()`、`protects()` | `Locales` に対して同じメソッドを直接呼び出すと、エイリアス文字列のコレクションを取得できます。正規コードには `false` を渡し、呼び出し元に配列が必要なら `->all()` を呼び出します。 |
| `Locales::installed(false)` または `Locales::raw()->installed(false)` | `false` は正規コードを取得する指定に変わりました。言語ファイルがなくても現在のロケールとフォールバックロケールは含まれます。旧名前付き引数 `withProtects: false` も置き換えます。両方を除くには `Locales::installed(false)->diff(Locales::protects(false))->values()` を使用します。オブジェクトの場合は `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` を使用します。 |
| `Locales::notInstalled()` または `Locales::raw()->notInstalled()` | オブジェクトには `Locales::data()->uninstalled()`、エイリアス文字列には `Locales::uninstalled()` を使用します。正規コードを取得するには `false` を渡します。 |
| `Locales::getCurrent()` または `Locales::getDefault()` | DTO には `Locales::data()->current()`、文字列には `Locales::current()` を使います。`Locales::raw()->getDefault()` は `Locales::current()` に置き換えます。Laravel が保持する値を `Locales::raw()->getCurrent()` で取得していた場合は、`app()->getLocale()` を使用します。 |
| `Locales::getFallback()` または `Locales::raw()->getFallback()` | DTO には `Locales::data()->fallback()`、文字列には `Locales::fallback()` を使用します。未知のフォールバックロケールは英語に解決されます。未知の現在のロケールは有効なフォールバックロケールに解決されます。 |
| `Locales::get($locale)` または `Locales::raw()->get($locale)` | `isInstalled()` を使用し、フォールバックを明示的に選びます。対応していてもインストールされていない可能性があるロケールは、`isAvailable()` で確認してから `info()` を呼び出します。 |
| `Locales::info($locale)` または `Locales::raw()->info($locale)` | `Locales::info($locale)` を使用し、文字列が必要なら `->alias` または `->code` を読み取ります。現在のロケールには `null` を渡します。未知のコードまたは空のコードについては `LaravelLang\Exceptions\UnknownLocaleException` を処理するか、検索前に `isAvailable()` で確認します。 |

未インストールまたは無効な入力を現在のロケールにフォールバックする旧 `get()` の動作を維持する場合は、呼び出し元でその選択を行います。

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // パス、データベースの行、その他の永続データに使う
$alias = $locale->alias; // URL と言語選択に使う
```

以前に `raw()->get()` の結果を `app()->setLocale()` に渡していたリクエストハンドラーを更新します。リクエスト値を受け取った後は `Locales::set($locale)` を使います。インストール済みのロケールコード、設定済みのエイリアス、列挙型のケース、新しい DTO を受け取り、Laravel に正規コードを設定します。リクエスト値が `pt-br` の場合、Laravel には `pt_BR` が設定され、`Locales::current()` は `pt-br` を返します。正規コードが必要な箇所には `Locales::current(false)` または `Locales::fallback(false)` を使用してください。

アプリケーションのほかの箇所で値を永続化するには、まず `isAvailable()` または `isInstalled()` で値を検証し、`fromAlias()` で正規コードに解決します。表示用に正規コードを整形する場合は `toAlias()` を使います。旧 `Services\Resolver` は、許容される PHP 型の確認には `acceptsInput()`、型のある値から文字列を抽出するには `normalize()` に置き換えます。`normalize()` は `pt-br` のようなエイリアスも含め、渡された文字列をそのまま返します。

インポート、型ヒント、テストで `LaravelLang\Locales\Data\LocaleData` を `LaravelLang\LocaleNames\LocaleData` に置き換えます。新しい読み取り専用オブジェクトには文字列プロパティ `code`、`alias`、`native`、`localized` があります。URL や UI 用の値として旧 `code` プロパティを使っていた箇所は `alias` に変更します。以前に `$data->locale` を読み取っていた場合は `LaravelLang\LocaleList\Locale` をインポートし、`Locale::from($data->code)` を使用します。テンプレートやフォーマッターで `type`、`regional`、`direction` が必要な場合は `config("localization_map.locales.{$data->code}", [])` から読み取ります。エントリがなければ独自の既定値を指定します。旧版の既定値を維持するには、文字体系に `Latn`、地域に正規コード、書字方向に `Direction::LeftToRight` を使います。

`withCountry`、`withCurrency`、`withCountries`、`withCurrencies` フラグを削除します。ロケールの行から国と通貨を明示的に取得してください。旧 `LaravelLang\Locales\Data\CountryData` と `CurrencyData` 型のインポートは、対応する名称パッケージのデータクラスに更新します。

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

旧サービスで `localizedLocales()`、`localizedCountries()`、`localizedCurrencies()` を呼び出していた場合、対象の名称サービスで現地語表記には `all(null)`、翻訳済み表記には `all($displayLanguage)` を使用します。以前の `Services\Locales` や `Services\RawLocales` クラスを直接注入していた箇所は、ファサードまたは対応する `Services\MainService` と `Services\DataService` に置き換えます。新しいサービスの API は[ロケールと名称](./locales.md)を参照してください。

#### ロケール列挙型のヘルパー {#locale-enum-helpers}

`Locale` と `Direction` の列挙型ケースの値はそのままです。独自のロケール一覧や選択 UI を更新する際は、`archtechx/enums` の削除されたヘルパーを置き換えてください。

| 以前のヘルパー | 置き換え |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | `array_column(Locale::cases(), 'value', 'name')` で名前をキー、値を項目とするマップを作成します。 |
| `stringOptions()` | ビューで `Locale::cases()` から選択肢を生成します。正規コードにはケースの値を使い、表示用エイリアスには `Locales::toAlias($case)` を使います。 |
| `fromName()` または `tryFromName()` | `Locale::cases()` から `name` が一致するケースを検索し、一致しない場合を処理します。`pt_BR` のような列挙型の値には `Locale::from()` または `Locale::tryFrom()` を使用します。 |
| `Locale::onlyValues()` | `array_column(Locale::cases(), 'value', 'value')` で値をキーと項目の両方にしたマップを作ります。値の一覧には `Locale::values()` を使用します。 |
| `Direction::values()` | `array_column(Direction::cases(), 'value')` を使用します。 |

### 名称検索 API を置き換える {#replace-the-name-lookup-apis}

`LaravelLang\NativeCountryNames`、`LaravelLang\NativeCurrencyNames`、`LaravelLang\NativeLocaleNames` のインポートを、`LaravelLang\CountryNames`、`LaravelLang\CurrencyNames`、`LaravelLang\LocaleNames` に更新します。`CountryNames`、`CurrencyNames`、`LocaleNames` は Laravel のコンテナから解決するか、クラスに注入します。

| 以前の使い方 | 置き換え |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| 現地語表記を取得する `SomeNames::get()` | `$names->all(null)` |
| `SortBy::None` | 並べ替えの引数を省略します。 |
| `SortBy::Key` | 返されたコレクションで `->sortKeys()` を呼び出します。 |
| `SortBy::Value` | 翻訳済みの名前で並べ替えるには `all()` に `SortDirection::Ascending` を渡します。 |

既知の項目を取得するには `one($displayLocale, $entryLocale)` を使用します。検索キーには `fr` のようなロケールを指定します。たとえば、`app(CountryNames::class)->one('de', 'fr')` は、フランス語に対応する国の名前をドイツ語で返し、`code` は `FR` になります。国と通貨の DTO はそれぞれのパッケージのルート名前空間にあり、検索キーを `locale` として公開します。`code` フィールドは引き続き国または通貨のコードを保持します。

3 つのサービスはいずれも正規ロケールをキーとするコレクションを返します。旧ロケール名パッケージの文字列配列を使用していた場合は、各 DTO を `localized` にマップします。旧版の国や通貨の DTO で `toArray()` を呼び出していた場合や、プロパティを書き換えていた場合は、アプリケーションで必要な配列を明示的に組み立てます。新しい DTO は読み取り専用です。

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

`one()` の呼び出し前に、ユーザーが選択したロケールを対応するカタログで検証します。表示用のロケールが未知の場合、`all()` は空の一覧を返すことがあります。現地語表記が必要な場合は `null` を指定します。独自の値付き列挙型を使用している場合は、その文字列の値を渡します。[ロケールと名称](./locales.md)

### ローカライズされたルートとミドルウェアを更新する {#update-localized-routes-and-middleware}

ローカライズされた URL を生成する PHP ファイルや Blade テンプレートで、名前空間付きのヘルパーをインポートします。`\LaravelLang\Routes\localizedRoute()` を直接呼び出すこともできます。

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` と `LocalizationRoute::group()` は、プレフィックスなしのルートとロケールのプレフィックス付きのルートを両方登録します。既存のグループを保持し、URL ヘルパーを呼び出す箇所を確認してください。ロケールを省略するとフォールバックのエイリアスが使用されます。言語切り替えでは `Locales::toAlias($locale)` や `$localeData->alias` で希望するエイリアスを明示的に渡します。指定されたロケール文字列はそのまま使用されます。パラメーター名を変更した場合は、配列キーを `localization_routes.names.parameter` に合わせます。

ルート、`bootstrap/app.php`、独自のミドルウェア一覧でインポートと登録を置き換えます。表にあるクラスはすべて `LaravelLang\Routes\Middlewares` にあります。

| 旧ミドルウェア | 置き換え |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

モデルミドルウェアの生成時にガードを指定していた場合は、`LocalizationByModelMiddleware::class . ':admin'` のように Laravel のミドルウェア引数として渡します。ユーザーのリゾルバーには Eloquent モデルが必要です。独自のミドルウェアサブクラスでは `LaravelLang\Config\Services\RouteLangConfig` を注入し、`$this->routeConfig->name` から取得元の名前を読み取り、`detect()` から `int|string|null` を返します。`LaravelLang\Routes\Services\Route` への直接参照は `LaravelLang\Routes\Services\RouteService` に置き換えます。

アプリケーションが使用する取得元をそれぞれテストします。ヘッダーの検出では PHP Intl を使い、`Accept-Language` の優先順位に従って、インストール済みのロケールを調べます。完全な言語タグを先に、基底言語をその後に確認します。後続のミドルウェアが先に選ばれたロケールを上書きすることがあります。プレフィックス付きルートで URL のロケールを優先するには、そのルートのチェーンに URL からロケールを取得するミドルウェアだけを残します。

URL の生成とリクエスト処理は別々にテストしてください。`localization_routes.hide` は生成する URL からフォールバックロケールのセグメントを省略します。`localization_routes.redirect` は現在のロケールに対するプレフィックス付きリクエストをプレフィックスなしのルートへ転送します。名前付きのプレフィックス付きルートでロケールが無効または未インストールの場合も、プレフィックスなしのルートへ転送されます。

`LocaleHasBeenSetEvent` のリスナーでは、正規コードに `$event->locale->code`、表示に `$event->locale->alias` を使います。ペイロードは `LaravelLang\LocaleNames\LocaleData` に変わりました。ミドルウェアは、選択したロケールが変わったときにイベントを送信し、正規コードをセッションに保存します。手動で実行する `LocaleHasBeenSetEvent::dispatch($data)` は `event(new LocaleHasBeenSetEvent($data))` に置き換えます。[ローカライズされたルート](./routes.md)

### モデル翻訳のコードとデータを移行する {#migrate-model-translation-code-and-data}

各翻訳テーブルの `locale` カラムを確認し、エイリアスで保存した行を探します。データマイグレーションで、エイリアスごとに該当行を正規コードへ更新します。更新前に、同じ `item_id` と正規コードの行がすでにないか確認します。一意制約 `(item_id, locale)` を満たすよう、保持したい値を 1 行へマージしてください。旧トレイトでも正規コードを保存するバージョンがあるため、実際にテーブルに保存されている値を基に判断します。

インポートと生成されたクラスを更新します。

| 以前の使い方 | 置き換え |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` または `UnknownModelPathException` | `LaravelLang\Exceptions` の同名クラス。 |
| `translationModelName()` | `translationModel(): string`。独自の翻訳クラスを選ぶオーバーライドも更新します。 |
| `getTranslationTable()` | 修飾されたカラム名には `translationQualifyColumn($column)`、テーブル名には `translationsRaw()->getRelated()->getTable()` を使用します。 |

`getTranslation()`、`setTranslation()`、`hasTranslated()`、`forgetTranslation()` は、ロケール文字列、`Locale`、新しい `LocaleData` を受け付けます。これらのメソッドにロケールを明示して使う場合は、呼び出し前にインストールしてください。ロケールを指定せずに読み取ると、現在の値が `null` の場合にフォールバックを使用します。ロケールを明示すると、そのロケールの値を直接読み取ります。低水準の `translation()` メソッドにはロケール文字列または `null` を渡してください。DTO からは `$locale->code` で変換できます。

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

`de_CH` → `de-CH` のエイリアスを設定し、スイスドイツ語をインストールしていれば、どちらの呼び出しも `de_CH` として保存された行を参照します。1 つの翻訳対象フィールドに複数の値を設定する場合は、`['en' => 'Title', 'de-CH' => 'Schweizer Titel']` のようにロケールをキーとしたマップを渡します。添字付きのリストでは `InvalidLocaleInputException` が発生します。読み込み済みの `translations` リレーションを独自コードで設定する場合は、翻訳モデルの Laravel コレクションを渡してください。

`translated()`、`whereTranslation()`、関連スコープは Eloquent のクエリビルダーを通じて呼び出します。スコープのロケール文字列には正規コードまたはエイリアスを渡します。`orderByTranslation()` には `'asc'`、`'desc'`、`SortDirection` のいずれかを使います。`whereTranslation()` や `whereTranslationLike()` で現在またはフォールバック以外のロケールを検索するには、`localization_models.filter` を `false` にするか `translationsRaw()` を直接検索します。

`$event->locale->value` を読み取る箇所や `$event->locale` を `Locale` の列挙型と比較するイベントの購読側コードを更新します。`TranslationHasBeenSetEvent` と `TranslationHasBeenForgetEvent` は、明示されたロケールに対してエイリアス文字列を保持します。ロケールを省略すると、設定イベントのロケールは `null` になります。エイリアスを永続化するキーにする前に `Locales::fromAlias()` で変換してください。設定イベントは、`save()` より前の属性代入時に発生します。3 つのモデル翻訳イベントを手動で送信する場合は `event(new …)` を使います。

生成された IDE ヘルパーを保持する場合は、インポートとモデルの検出パスを更新してから `php artisan lang:models-helper` を実行してください。[モデルの翻訳](./models.md)

### 公開スクリプトを更新し、カスタム翻訳を保持する {#update-publication-scripts-and-keep-custom-translations}

| 以前のコマンドや前提 | 移行方法 |
| --- | --- |
| `lang:add <locale>` は追加対象のロケールだけを更新 | インストール済みのすべてのロケールを更新します。スクリプトではコードを明示し、既存ロケールのファイルも更新されることを考慮してください。 |
| `lang:reset` | マージするには `lang:update` を使用します。最初から作り直す場合はカスタムエントリをバックアップし、再生成するファイルを削除してから、対象のロケールコードを明示して `lang:add` を実行します。 |
| `lang:rm <locale> --force` | `lang:rm <locale>` を使います。Laravel の現在のロケールとフォールバックロケールは保護されます。 |
| `lang:rm` の終了コードのみを確認 | ロケールごとの結果を確認します。`DONE` は削除の完了を表し、`FAILED` は削除を試みた後もパスが残っていることを表します。`PROTECTED`、`INVALID`、`SKIP` でも終了コードが成功を示すことがあります。 |

コマンドクラス名で Artisan を呼び出す場合は、`LaravelLang\Publisher\Console\Add`、`Remove`、`Update` を `AddCommand`、`RemoveCommand`、`UpdateCommand` へ変更します。独自のテキストデコレーターは `Contracts\TextDecorator` の代わりに `LaravelLang\Publisher\Contracts\Decorator` を実装し、コンテナにバインドします。`convert(string $locale, string $value): string` は正規ロケールを受け取ります。

既存の言語ファイルをバージョン管理に置いた状態で、初回の `lang:update` を実行します。Publisher は既存の出力先にマージし、数値キーを含め、一致するキーを上書きします。可能ならカスタム翻訳は別のアプリケーション翻訳ファイルに保存してください。生成対象をファイル単位で自分で管理する場合は、`localization_main.skip` にパスのパターンを追加します。原文に戻った値がないか確認してください。辞書に翻訳がなければ、Publisher は原文を使用します。

以前の更新で `http-statuses.php` のキーが振り直された場合は、影響を受けた生成ファイルをバックアップしてから削除してください。回避策として `localization_main.skip` に `'*/http-statuses.php'` を追加していた場合は、その設定を取り除きます。`php artisan lang:update` でファイルを再生成し、カスタム翻訳を本来のステータスコードで復元します。[翻訳の管理](./translations.md)

### 旧版のプラグイン登録を置き換える {#replace-legacy-plugin-registration}

サードパーティー製の翻訳パックを使用している場合は、マニフェスト方式に対応したリリースへ更新します。パックの管理者は、旧 `LaravelLang\Publisher\Plugins\Provider` を登録するプロバイダーと Composer の自動検出エントリを削除してください。同じプロバイダーで無関係な Laravel サービスも設定している場合は、通常の Laravel サービスプロバイダーでその登録を保持します。

1. `vendor/` 以下にインストールされるパックのルートへ `laravel_lang.json` を追加します。`Plugin::files()` の各エントリを、パックの `source/` からの相対パスである `source` と、アプリケーションの言語ディレクトリからの相対パスである `target` に変換します。`target` には `{locale}` を使用してください。
2. Composer の照合ルールはマニフェストへ移します。インストール済みの依存パッケージには `vendor` と `version` を使い、ルートの Composer プロジェクトには `project: true` を指定した別エントリを使います。旧プラグインに `$with_project_name = true` があり、両モードに対応していた場合は、両方のエントリを作成します。
3. 辞書を `locales/<code>/<style>.json` へ変換します。ソースファイルの値が辞書の検索キーになります。たとえば、PHP エントリ `'enable' => 'Enable audit trail'` を `{locale}/audit.php` へ公開すると、引き続きアプリケーションのキー `audit.enable` ができます。翻訳は辞書の `"Enable audit trail"` キーに格納してください。
4. Mona をチェックアウトし、以下の Toolkit コマンドを旧パックに対して実行します。旧版の JSON と PHP の辞書から `default.json` と `inline.json` が作成され、ロケールの除外設定も引き継がれます。出力を確認し、マニフェストとソースファイルとともに新しいパックに追加します。エイリアスに基づく辞書ディレクトリ名は正規コードへ変更してください。
5. パックの Composer マニフェストで、現行の Publisher を依存関係に指定します。共有辞書の翻訳を使うパックは `laravel-lang/library` も追加します。作成したパックをアプリケーションにインストールし、`php artisan lang:update` を実行して公開された出力を確認します。

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

既存のアプリケーションでは、アップグレード後にどの組み込みパックのルールが適用されるかを確認してください。

- 以前に Spark Paddle、Spark Stripe、Nova Dusk Suite、Nova Log Viewer 向けに公開された Laravel-Lang の翻訳がまだ必要な場合は、それらのキーをアプリケーション側で管理するファイルに保存するか、旧原文と辞書をカスタムパックへ移します。
- スターターキットの翻訳は、プロジェクトの Composer の `name` と照合されるようになりました。プロジェクト名を変更した場合や、キットを依存パッケージとして使用している場合は、翻訳を公開できるようにキットの Composer 名（例 `laravel/livewire-starter-kit`）を `localization_main.force` に追加してください。[翻訳プラグイン](./plugins.md)

### JSON フォールバックと翻訳ツールを更新する {#update-json-fallback-and-translation-tooling}

アプリケーションコードで `LaravelLang\JsonFallback\Translator` をインポートまたは型指定している場合は、`LaravelLang\JsonFallback\OverridedTranslator` に変更するか、Laravel の翻訳コントラクトを使用してください。旧トランスレーターを継承していた場合は、独自の動作をアプリケーション側のトランスレーターや独自の翻訳サービスへ移します。キーを解決できない場合、置き換え後のトランスレーターは Laravel の翻訳未検出コールバックを呼び出します。アプリケーションがコールバックを登録していれば内容を確認し、フォールバックの JSON が先に検索されるため、フォールバックの JSON と現在の言語の PHP の両方に存在するキーもテストしてください。[JSON フォールバック](./translations.md#json-fallback)

機械翻訳に別パッケージの `laravel-lang/translator` を使っている場合は、依存関係を削除する前に、`Translate` ファサードと Google、DeepL、Yandex との連携を選択した翻訳サービスへ移行します。それらのスクリプトで生成済みの翻訳は、アプリケーションやパックの辞書に保持してください。

`laravel-lang/status-generator` の `vendor/bin/lang` を利用してパックのカタログを保守している場合は、チェックアウトした Mona から `composer install` を行い、新しいコマンドを実行します。

| 以前のスクリプト | 使用する Toolkit のスクリプト |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`。生成された `locales/<code>/_status.json` を参照します。 |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`。共有の Library を保守するには `sync:library` を使います。 |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`。既知のロケール用のディレクトリを作成します。 |
| `vendor/bin/lang download` | ZIP の URL を `--url`、出力先のフルパスを `--directory`、アーカイブ内のパスを必要な数だけ `--paths` で指定します。 |
| `vendor/bin/lang translate` | 機械翻訳は別のサービスに移します。保存された文字列が原文と同じままの項目は Toolkit の `status` で確認できます。 |

一連の例は [Toolkit のコマンド](./toolkit.md)を参照してください。

## アップグレードを検証する {#verify-the-upgrade}

アプリケーションのテストを実行します。重要なロケールごとに公開されたメッセージ、フォールバック翻訳、設定したエイリアスを含むローカライズ済み URL、正規コードで保存されたモデル翻訳を確認します。ソースの変更とあわせて、生成されたファイルや保存済みのロケール値も確認します。

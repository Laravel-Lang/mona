---
title: ロケールと名称
description: 対応するロケールとインストール済みのロケールを確認し、エイリアスを解決し、翻訳済みの名称を検索します。
---

Laravel-Lang は、Laravel の設定、翻訳ファイル名、モデルの翻訳行に `en`、`fr`、`pt_BR` などの正規コードを使用します。エイリアスは、URL での `pt-br` など、アプリケーション側で使うロケール名です。[`localization_main.aliases`](./configure.md) で正規コードとエイリアスの対応を定義します。

## 対応するロケールとインストール済みのロケールを確認する {#find-supported-and-installed-locales}

`laravel-lang/locale-list` は値付きの列挙型 `LaravelLang\LocaleList\Locale` を提供します。各ケースは[対応する正規コード](./available-locales.md)に対応します。

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // 正規コードの配列
Locale::collection();        // 正規コードのコレクション
Locale::collection(false);   // 列挙型のケースのコレクション
```

`laravel-lang/locales` はアプリケーションの設定を参照するファサード `LaravelLang\Locales\Facades\Locales` を追加します。以下のメソッドは文字列のコレクションを返します。正規コードを取得するには引数に `false` を指定します。既定では各コードのエイリアスが設定されていれば、その値が使われます。

| メソッド | 戻り値 |
| --- | --- |
| `Locales::available()` | すべての既知のロケール |
| `Locales::installed()` | アプリケーションの言語ファイルから検出されたロケールと保護されているロケール |
| `Locales::uninstalled()` | 利用可能なロケールのうちインストールされていないもの |
| `Locales::protects()` | 現在有効なロケールとフォールバックロケールから重複を除いたもの |

ロケールの検出では、`lang_path()` 直下の項目と、そこからの相対パスが `vendor/*/*.json` に一致する JSON ファイルを読み込みます。ファイル名またはディレクトリ名は、対応するコードに解決できる必要があります。`lang/vendor/<package>/` 以下に PHP の翻訳だけを含むディレクトリは検出の対象外です。ロケールの公開方法は[翻訳の管理](./translations.md)を参照してください。

## アプリケーションのロケールを取得または設定する {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` はコード、エイリアス、列挙型 `Locale` のケース、または `LocaleData` オブジェクトを受け取ります。インストール済みのロケールであれば、Laravel のロケールを正規コードに更新します。コードが対応しているかどうかは `isAvailable()`、ファイルの存在または保護の状態が必要な場合は `isInstalled()` で確認します。`isProtected()` は現在のロケールとフォールバックロケールを識別します。

`current()` は Laravel の現在のロケールが認識されていればそれを使用し、そうでなければ `fallback()` を使用します。`fallback()` は Laravel の設定からフォールバックを解決し、その値が未知の場合は `en` を使用します。どちらのメソッドも正規コードを取得するための引数 `false` を受け付けます。

エイリアスを正規コードに解決するには `fromAlias()`、正規コードからエイリアスを取得するには `toAlias()` を使います。認識できない空でない文字列は、そのまま返されます。入力を検証する場合は `isAvailable()` を使ってください。

## ロケールの選択肢を表示する {#display-locale-choices}

`Locales::info('fr')` は、`code`、`alias`、`native`、`localized` プロパティを持つ `LaravelLang\LocaleNames\LocaleData` オブジェクトを返します。表示言語にはアプリケーションの現在のロケールが使われます。現在のロケールそのものを取得する場合は引数を省略します。既知のロケールはインストール前でも検索できます。未知のコードでは `LaravelLang\Exceptions\UnknownLocaleException` が発生します。

`Locales::data()->installed()` を使うと、インストール済みロケールを `LocaleData` オブジェクトとして取得できます。`data()` には、別のコレクションを返す `available()`、`uninstalled()`、`protects()` と、個別のオブジェクトを返す `current()`、`fallback()` もあります。言語セレクターには、送信値として `alias`、表示名として `native` を使えます。

## ロケール名、国名、通貨名を検索する {#look-up-locale-country-and-currency-names}

`laravel-lang/locale-names`、`laravel-lang/country-names`、`laravel-lang/currency-names` はそれぞれ、Laravel のコンテナから注入できるサービスを提供します。Common には 3 つすべてが含まれます。

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

第 1 引数は表示言語です。現地語表記には `null` を指定します。`one()` の第 2 引数は対象の行のロケールで、上記の例では `fr` です。国名や通貨名の検索では、戻り値のオブジェクトに ISO コードが含まれます。どちらの引数にも、正規コード、設定済みのエイリアス、列挙型 `Locale` のケースを指定できます。

`all($forLocale)` は正規ロケールコードをキーとするコレクションを返します。`localized` プロパティで並べ替えるには `SortDirection::Ascending` または `SortDirection::Descending` を指定します。各値は読み取り専用オブジェクトです。国名と通貨名の値には、その行の `locale` も含まれます。

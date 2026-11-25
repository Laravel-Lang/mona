---
title: モデルの翻訳
description: Laravel-Lang Models を使って翻訳された Eloquent 属性を保存、取得、検索します。
---

`laravel-lang/models` は既存の Eloquent モデルに翻訳可能な属性を追加します。各翻訳行にはロケールとそのロケールの属性値を格納します。このパッケージは Common に含まれ、PHP の Intl 拡張機能が必要です。

```bash
composer require laravel-lang/models
```

## モデルに翻訳を追加する {#add-translations-to-a-model}

基本テーブルのマイグレーションがある既存の `App\Models\Article` モデルに対し、次を実行します。

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

ジェネレーターは基本モデルに `LaravelLang\Models\HasTranslations` トレイトを追加し、対応する `ArticleTranslation` モデル、マイグレーション、IDE ヘルパーを作成します。マイグレーションは記事への外部キー、一意な `(item_id, locale)` の組、および翻訳値を格納する null を許容する文字列カラムを作成します。値をすでに基本テーブルに格納している場合、新しいモデルのアクセサーを使う前に翻訳行へ移行してください。

モデルが `app/Models` の外にある場合は、ジェネレーターを実行する前にモデルのディレクトリを [`localization_models.directories`](./configure.md) に追加します。

`php artisan make:model-translation` を実行すると、モデルとカラムを対話的に選べます。カラムを省略すると `title` と `description` が使われます。後でフィールドを追加するには、マイグレーションを作成し、翻訳モデルの一括代入可能な属性とキャストに追加します。

## 翻訳済み属性を書き込み、読み取る {#write-and-read-translated-attributes}

[フランス語をインストール](./translations.md)していれば、正規コード、エイリアス、列挙型 `Locale` のケース、または `LocaleData` を指定して翻訳を設定できます。変更を永続化するには基本モデルの `save()` を使います。

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // 現在のロケール。値が null の場合はフォールバック
$article->getTranslation('title', 'fr');  // 'Premier article'
```

ロケールを明示すると、そのロケールの値を読み取ります。ロケール引数がなければ、書き込みには現在のロケールが使われ、読み取り時に現在の値が `null` なら、Laravel に設定されたフォールバックロケールが使われます。検索と保存では、エイリアスを正規コードに解決します。明示したロケールが未知または未インストールの場合は `LaravelLang\Exceptions\UnknownLocaleException` が発生します。

1 つのフィールドに複数の値を設定するには `setTranslations()` を使います。翻訳対象のフィールドごとにロケールをキーとするマップを、`create()` や `fill()` に渡すこともできます。以下の例では英語とフランス語がインストール済みであることを前提とします。

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

`isTranslatable('title')` はモデルの翻訳対象フィールドを確認し、`hasTranslated('title', 'fr')` は値が設定されているかを確認します。`forgetTranslation('fr')` はフランス語の行を削除し、`forgetAllTranslations()` はそのモデルのすべての行を削除します。

## 翻訳を検索する {#query-translations}

`translated()`、`whereTranslation()`、`whereTranslationLike()` は `translations()` リレーションを経由して検索します。既定では、このリレーションは現在のロケールとフォールバックロケールの行に絞ります。これらのスコープでほかのロケールを検索するには `localization_models.filter` を `false` にします。1 つのモデルのすべての行を読むには `translationsRaw()` を使います。

現在のロケールをフランス語にして、フランス語のタイトルで検索し並べ替えます。

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

モデルは `translations()` リレーションを事前読み込みします。`orWhereTranslation()` と `orWhereTranslationLike()` は代替条件を追加します。`orderByTranslation()` は 1 つのロケールで並べ替えます。省略時は現在のロケールを使います。

## 変更を監視して IDE ヘルパーを更新する {#observe-changes-and-refresh-ide-helpers}

`LaravelLang\Models\Events` 名前空間には `TranslationHasBeenSetEvent`、`TranslationHasBeenForgetEvent`、`AllTranslationsHasBeenForgetEvent` が含まれます。設定時のイベントにはモデル、カラム、任意のロケールエイリアス、変更前の値、変更後の値が含まれます。イベントは、後続の保存より前に、属性への代入時に発生します。

検出されたすべてのモデルのヘルパーを再生成するには `php artisan lang:models-helper` を実行します。既定の出力ディレクトリは `vendor/_laravel_lang` で、`localization_models.helpers` で設定できます。

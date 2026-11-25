---
title: ローカライズされたルート
description: ローカライズされた URL を登録し、リクエストから Laravel のロケールを選択します。
---

`laravel-lang/routes` はリクエスト時にロケールを選択し、ローカライズされたルートグループの URL を生成します。このパッケージは Common に含まれています。

```bash
composer require laravel-lang/routes
```

[URL で使用するロケールがインストール済み](./translations.md)であることを確認してください。パラメーター名、ミドルウェア、既定のロケールの動作は [`config/localization_routes.php`](./configure.md) で設定します。

## ローカライズされたルートグループを登録する {#register-a-localized-route-group}

`routes/web.php` では `Route::localizedGroup()` マクロを使用します。ファサード `LaravelLang\Routes\Facades\LocalizationRoute::group()` でも同様にグループ化できます。

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

既定の設定では、コールバックは両方の URL パターンを登録します。

| パターン | ルート名 |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

ロケールミドルウェアは、ルートハンドラーを呼ぶ前に `locale` パラメーターを消費します。URL のロケールが未知または未インストールで、名前付きルートへのリクエストであれば、プレフィックスのないルートへリダイレクトします。

Laravel はグループのコールバックを 2 回実行します。外側のグループの URL プレフィックスが `admin`、名前プレフィックスが `admin.` の場合、ローカライズされた名前は `admin.localized.products.show` になります。

## ローカライズされた URL を生成する {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

ヘルパーは設定されたロケールパラメーター名を使い、プレフィックス付きのルートがあれば優先します。指定されたロケール文字列はそのまま URL 生成に渡します。URL をエイリアスで生成するには、エイリアスをそのまま指定するか [`Locales::toAlias()`](./locales.md) であらかじめ取得します。ロケールを省略すると `Locales::fallback()` が使われます。現在のロケールを使うには `Locales::current()` を明示的に渡してください。

`localization_routes.hide` はヘルパーが生成する URL からフォールバックロケールを省略します。`localization_routes.redirect` は、現在のロケールへのリクエストをプレフィックス付きグループからプレフィックスのないルートへリダイレクトします。どちらも既定値は `false` です。

正規コードを使うには `Locale::French->value` を、エイリアスを使うには `LocaleData` オブジェクトを渡します。URL を生成する前に、ユーザーの選択を `Locales::isInstalled()` で確認します。

## ミドルウェアでロケールを選択する {#select-a-locale-with-middleware}

各ミドルウェアは `LaravelLang\Routes\Middlewares` のクラスを指定して直接適用できます。

| ミドルウェア | ロケールの取得元 |
| --- | --- |
| `LocalizationByParameterMiddleware` | ルートパラメーター。既定では `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | ルートパラメーター。名前付きルートにパラメーターが定義されている場合、指定されたロケールがないか未インストールなら、現在のロケールに置き換えます |
| `LocalizationByCookieMiddleware` | 既定では `Accept-Language` Cookie |
| `LocalizationByHeaderMiddleware` | `Accept-Language` ヘッダーの優先順位で最初に見つかったインストール済みロケール。完全な言語タグを先に照合し、次に基本言語を照合します。ワイルドカードと品質値が 0 の項目は無視します。 |
| `LocalizationBySessionMiddleware` | 既定では `Accept-Language` セッションキー |
| `LocalizationByModelMiddleware` | 既定では認証済みの Eloquent ユーザーの `locale` 属性。ガードを選択するにはミドルウェアクラスに `:guard_name` を追加します |

プレフィックス付きグループの既定の順序は、URL パラメーター、Cookie、ヘッダー、セッション、モデルです。プレフィックスがなければ、URL パラメーターを除いた同じ順序になります。後で判定される有効な取得元は先の取得元を上書きできます。ロケールの変更時は Laravel の設定を更新し、設定済みのセッションキーに正規コードを保存します。セッションと Cookie が必要な場合は `web` ミドルウェアグループを使います。ヘッダーのミドルウェアには PHP の Intl 拡張機能が必要です。

ミドルウェアはロケールを変更すると `LaravelLang\Routes\Events\LocaleHasBeenSetEvent` を発行します。`locale` プロパティは `LaravelLang\LocaleNames\LocaleData` オブジェクトです。正規コードを取得するには `$event->locale->code` を使用します。

URL を優先するには、`localization_routes.middlewares.prefix` に `LocalizationByParameterPrefixMiddlewareMiddleware::class` だけを残します。プレフィックスなしのルートのチェーンは別に設定します。

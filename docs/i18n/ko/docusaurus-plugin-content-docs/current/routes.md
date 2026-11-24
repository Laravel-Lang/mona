---
title: 현지화된 라우트
description: 현지화된 URL을 등록하고 요청에서 Laravel 로캘을 선택합니다.
---

`laravel-lang/routes`는 요청을 처리할 때 로캘을 선택하고 현지화 라우트 그룹의 URL을 생성합니다. 이 패키지는 Common에 포함됩니다.

```bash
composer require laravel-lang/routes
```

[URL에 사용하는 로캘이 설치되어 있는지 확인하세요](./translations.md). 매개변수 이름, 미들웨어, 기본 로캘 동작은 [`config/localization_routes.php`](./configure.md)에서 설정합니다.

## 현지화 라우트 그룹 등록하기 {#register-a-localized-route-group}

`routes/web.php`에 `Route::localizedGroup()` 매크로를 사용하세요. `LaravelLang\Routes\Facades\LocalizationRoute::group()` 파사드도 같은 그룹화 메서드를 제공합니다.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

기본 설정에서 콜백은 두 URL 패턴을 모두 등록합니다.

| 패턴 | 라우트 이름 |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

로캘 미들웨어는 라우트 핸들러를 호출하기 전에 `locale` 매개변수를 처리하고 제거합니다. 이름이 있는 라우트에서 URL의 로캘이 알 수 없거나 설치되지 않았다면 접두사 없는 라우트로 리디렉션합니다.

## 현지화 URL 생성하기 {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

헬퍼는 설정된 로캘 매개변수 이름을 사용하고, 접두사가 있는 라우트가 있으면 이를 우선합니다. 입력한 로캘 문자열은 그대로 URL 생성에 전달합니다. 별칭이 있는 URL을 만들려면 그 별칭을 전달하거나 먼저 [`Locales::toAlias()`](./locales.md)로 구하세요. 로캘을 생략하면 `Locales::fallback()`을 사용합니다. 현재 로캘을 사용하려면 `Locales::current()`를 명시적으로 전달하세요.

`localization_routes.hide`는 헬퍼가 생성하는 URL에서 대체 로캘을 생략합니다. `localization_routes.redirect`는 현재 로캘에 대한 요청을 접두사가 있는 그룹에서 접두사가 없는 라우트로 리디렉션합니다. 두 설정의 기본값은 `false`입니다.

## 미들웨어로 로캘 선택하기 {#select-a-locale-with-middleware}

각 미들웨어는 `LaravelLang\Routes\Middlewares`의 해당 클래스를 사용해 직접 적용할 수 있습니다.

| 미들웨어 | 소스 |
| --- | --- |
| `LocalizationByParameterMiddleware` | 라우트 매개변수. 기본값은 `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | 라우트 매개변수. 이름이 있는 라우트에 매개변수가 선언되어 있고 요청한 로캘이 없거나 미설치 상태면 현재 로캘을 대신 사용 |
| `LocalizationByCookieMiddleware` | 기본적으로 `Accept-Language` 쿠키 |
| `LocalizationByHeaderMiddleware` | `Accept-Language` 헤더의 선호 순서에 따라 처음으로 일치하는 설치된 로캘. 전체 언어 태그를 먼저 확인한 다음 기본 언어를 확인합니다. 와일드카드와 품질 값이 0인 항목은 건너뜁니다. |
| `LocalizationBySessionMiddleware` | 기본적으로 `Accept-Language` 세션 키 |
| `LocalizationByModelMiddleware` | 기본적으로 인증된 Eloquent 사용자의 `locale` 속성. 가드를 선택하려면 미들웨어 클래스 뒤에 `:guard_name`을 추가 |

접두사가 있는 그룹의 기본 순서는 URL 매개변수, 쿠키, 헤더, 세션, 모델입니다. 접두사가 없는 그룹은 여기서 URL 단계만 제외합니다. 뒤에 있는 유효한 소스가 앞 소스의 결과를 덮어쓸 수 있습니다. 로캘이 바뀌면 Laravel의 로캘을 업데이트하고 설정된 세션 키에 표준 코드를 저장합니다. 세션과 쿠키가 필요하면 `web` 미들웨어 그룹을 사용하세요. 헤더 미들웨어는 PHP Intl이 필요합니다.

미들웨어가 로캘을 변경하면 `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`를 디스패치합니다. 이벤트의 `locale` 속성은 `LaravelLang\LocaleNames\LocaleData` 객체입니다. 표준 코드는 `$event->locale->code`로 가져옵니다.

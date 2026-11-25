---
title: 로캘과 이름
description: 지원 및 설치된 로캘을 찾고 별칭을 해석하며 현지화된 이름을 조회합니다.
---

Laravel-Lang은 Laravel과 번역 파일 이름, 모델의 번역 행에서 `en`, `fr`, `pt_BR` 같은 표준 코드를 사용합니다. 로캘 별칭은 URL의 `pt-br`처럼 애플리케이션에 표시하는 이름입니다. [`localization_main.aliases`](./configure.md)에서 표준 코드에 대응하는 별칭을 정의하세요.

## 지원 및 설치된 로캘 찾기 {#find-supported-and-installed-locales}

`laravel-lang/locale-list`는 값을 갖는 열거형인 `LaravelLang\LocaleList\Locale`을 제공합니다. 각 열거형 케이스는 [지원되는 표준 코드](./available-locales.md)에 대응합니다.

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // 표준 코드의 배열
Locale::collection();        // 표준 코드의 컬렉션
Locale::collection(false);   // 열거형 케이스의 컬렉션
```

`laravel-lang/locales`는 애플리케이션의 상태를 반영하는 `LaravelLang\Locales\Facades\Locales` 파사드를 추가합니다. 아래 메서드는 문자열 컬렉션을 반환합니다. 표준 코드를 가져오려면 `false`를 인수로 전달하세요. 기본적으로 각 코드는 별칭이 설정되어 있으면 그 별칭으로 표시됩니다.

| 메서드 | 반환값 |
| --- | --- |
| `Locales::available()` | 알려진 모든 로캘 |
| `Locales::installed()` | 앱 언어 파일에서 찾은 로캘과 보호되는 로캘 |
| `Locales::uninstalled()` | 사용할 수 있지만 설치되지 않은 로캘 |
| `Locales::protects()` | 실제로 적용되는 현재 및 대체 로캘에서 중복을 제거한 목록 |

로캘을 찾을 때는 `lang_path()` 바로 아래의 항목과 그 경로를 기준으로 `vendor/*/*.json`에 일치하는 JSON 파일을 읽습니다. 파일이나 디렉터리 이름은 지원되는 코드로 해석할 수 있어야 합니다. `lang/vendor/<package>/` 아래에 PHP 번역만 있는 디렉터리는 검색 대상에 포함되지 않습니다. 로캘을 게시하는 방법은 [번역 관리](./translations.md)를 참조하세요.

## 애플리케이션 로캘 읽기 및 설정 {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()`은 코드, 별칭, `Locale` 열거형 케이스 또는 `LocaleData` 객체를 받습니다. 해당 로캘이 설치되어 있다면 Laravel 로캘을 표준 코드로 변경합니다. 코드가 지원되는지는 `isAvailable()`로, 파일이나 보호 여부가 필요한 경우에는 `isInstalled()`로 확인하세요. `isProtected()`는 현재 및 대체 로캘을 식별합니다.

`current()`는 Laravel의 현재 로캘이 인식되면 그 값을 쓰고, 그렇지 않으면 `fallback()`을 사용합니다. `fallback()`은 Laravel에 설정된 대체 로캘을 해석하고, 알 수 없는 값이면 `en`을 사용합니다. 두 메서드 모두 표준 코드가 필요할 때는 `false`를 인수로 받습니다.

`fromAlias()`는 별칭을 표준 코드로 변환하고 `toAlias()`는 표준 코드의 별칭을 가져옵니다. 두 변환 모두 인식되지 않는 비어 있지 않은 문자열을 그대로 반환합니다. 입력을 검증하려면 `isAvailable()`을 사용하세요.

## 로캘 선택 항목 표시하기 {#display-locale-choices}

`Locales::info('fr')`는 `code`, `alias`, `native`, `localized` 속성이 있는 `LaravelLang\LocaleNames\LocaleData` 객체를 반환합니다. 표시 언어는 애플리케이션의 현재 로캘입니다. 현재 로캘을 조회하려면 인수를 생략하세요. 알려진 로캘은 설치 전에도 조회할 수 있습니다. 알 수 없는 코드는 `LaravelLang\Exceptions\UnknownLocaleException`을 발생시킵니다.

`Locales::data()->installed()`로 설치된 로캘을 `LocaleData` 객체로 가져오세요. `data()`는 다른 컬렉션을 반환하는 `available()`, `uninstalled()`, `protects()`와 개별 객체를 반환하는 `current()`, `fallback()`도 제공합니다. 언어 선택기에서 `alias`를 전송 값으로, `native`를 표시할 이름으로 사용할 수 있습니다.

## 로캘, 국가 및 통화 이름 조회하기 {#look-up-locale-country-and-currency-names}

`laravel-lang/locale-names`, `laravel-lang/country-names`, `laravel-lang/currency-names` 패키지는 각각 Laravel 컨테이너에서 주입할 수 있는 서비스를 제공합니다. Common은 세 패키지를 모두 포함합니다.

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

첫 번째 매개변수는 표시 언어입니다. 고유 이름을 가져오려면 `null`을 사용하세요. `one()`의 두 번째 매개변수는 위 예제의 `fr`처럼 행을 식별하는 로캘입니다. 국가와 통화를 조회하면 반환 객체에 ISO 코드가 포함됩니다. 두 매개변수 모두 표준 코드, 설정된 별칭, `Locale` 열거형 케이스를 받을 수 있습니다.

`all($forLocale)`은 표준 로캘 코드를 키로 하는 컬렉션을 반환합니다. `localized` 속성으로 정렬하려면 `SortDirection::Ascending` 또는 `SortDirection::Descending`을 전달하세요. 각 값은 읽기 전용 객체입니다. 국가와 통화 값에는 해당 행의 `locale`도 포함됩니다.

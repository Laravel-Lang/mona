---
title: Mona로 업그레이드하기
description: Laravel-Lang 의존성, 설정, 게시된 번역 및 애플리케이션 통합을 업그레이드합니다.
---

Mona는 Laravel-Lang 패키지를 함께 릴리스하는 저장소입니다. 애플리케이션에 필요한 개별 Composer 패키지는 계속 사용하세요.

:::info[왜 버전 20인가요?]

Laravel-Lang은 `laravel-lang/lang`에서 시작해 여러 패키지를 아우르는 생태계로 성장했습니다. 각 패키지는 서로 다른 시기에 만들어졌고 버전 관리와 릴리스도 따로 이뤄졌습니다. 메이저 버전을 올릴 때마다 유지보수팀은 여러 패키지의 릴리스를 조율해야 했고, 사용자는 어떤 버전을 함께 설치할 수 있는지 알아봐야 했습니다.

이 과정을 간소화하기 위해 호환되는 의존성을 지정하는 `laravel-lang/common`을 만들었습니다. 한 번의 업그레이드에서 서로 다른 버전 체계를 쓰는 패키지를 여러 개 업데이트해야 할 때도 있었습니다.

가령 Mona 이전에 Laravel 11과 PHP 8.2를 쓰는 애플리케이션에는 어떤 버전의 패키지를 설치해야 했을까요?

<details>
<summary>Mona 이전의 패키지 버전</summary>

| 패키지 | 버전 |
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
| Config | `1.17.1`(다른 Laravel-Lang 패키지가 계속 사용하던 1.x 계열 버전) 및 `2.3.2`(Mona 이전의 최신 안정 버전으로, 다른 패키지에서는 거의 사용하지 않음) |
| Publisher | `16.9.0` |

</details>

Mona에서는 모든 패키지에 동일한 버전 번호를 붙여 함께 릴리스합니다. 기존 패키지 중 Publisher의 버전 번호가 가장 높았으므로, 그 번호를 이어 갔다면 다음 메이저 버전은 `17.0`이었을 겁니다. 이 생태계를 하나로 모으는 데 많은 시간과 정성을 들였고, 이 전환을 기념해 20을 선택했습니다. **Laravel-Lang 2.0**으로 읽어도 좋습니다. 🙂

:::

:::info[로캘 코드와 별칭]

이제 Laravel-Lang은 게시되는 언어 파일 경로와 모델의 번역 행을 비롯해 저장하는 값에 표준 로캘 코드를 사용합니다. Laravel의 현재 및 대체 로캘에도 표준 코드를 사용합니다. 실행 중에는 URL 세그먼트와 언어 선택기에 별칭을 사용하세요. 로캘 코드를 반환하는 파사드 메서드는 기본적으로 설정된 별칭을 사용하며, 별칭이 없으면 표준 코드를 반환합니다. 예를 들어 `localization_main.aliases`에 `'pt_BR' => 'pt-br'`을 설정하면 `/pt-br/account` URL을 유지하면서 `lang/pt_BR.json`에 게시하고 데이터베이스에는 `pt_BR`을 저장합니다.

:::

## 의존성 업데이트 {#updating-dependencies}

현재 버전은 Laravel 12와 13을 지원하며 PHP `^8.3`이 필요합니다. 애플리케이션의 `composer.json`에서 아래 패키지 중 `require` 또는 `require-dev`에 있는 항목의 버전 제약을 `^20.0`으로 설정하세요. `laravel-lang/common`만 설치했다면 이 항목만 바꾸면 됩니다.

`20.x`로 설치하거나 업그레이드하기 전에 필요한 Composer 패키지 버전이 Packagist에 등록되었는지 확인하세요. Mona 릴리스와 그에 해당하는 Composer 패키지가 공개되는 시점은 다를 수 있습니다.

| 용도 | Composer 패키지 |
| --- | --- |
| 번들 | `laravel-lang/common` |
| 번역 팩 | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| 게시 및 설정 | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| 애플리케이션 통합 | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| 공용 유틸리티 | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| 팩 템플릿 | `laravel-lang/translations-template` |

표준 번역 절차만 사용한다면 의존성을 업데이트한 뒤 번역을 다시 게시하세요.

```bash
composer update
php artisan lang:update
```

프로바이더를 등록했거나 사용자 지정 설정을 게시했거나 별칭, 서드 파티 번역 팩 또는 패키지 API를 사용한다면 다시 게시하기 전에 아래에서 해당하는 절을 따르세요.

## 이름이 바뀌었거나 더 이상 쓰이지 않는 의존성 교체 {#replace-renamed-or-retired-dependencies}

`composer update`를 실행하기 전에 이전 이름 조회 패키지를 직접 사용하는 `composer.json`의 의존성을 업데이트하세요.

| 이전 Composer 패키지 | `^20.0`으로 요구할 패키지 |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common은 이미 세 가지 이름 조회 패키지에 모두 의존합니다. PHP 코드도 업데이트하세요. [이름 조회 API 교체](#replace-the-name-lookup-apis)를 참조하세요.

각 서드 파티 플러그인에 새 Publisher와 호환되는 릴리스가 있는지 확인하세요. 의존성을 제거하기 전에 `laravel-lang/translator` 또는 `laravel-lang/status-generator`를 사용하는 코드를 마이그레이션합니다. [번역 도구](#update-json-fallback-and-translation-tooling)를 참조하세요.

## 서비스 프로바이더 업데이트 {#update-service-providers}

Laravel은 패키지 매니페스트에서 Config, Publisher, Routes, Models의 프로바이더를 찾습니다. 자동 검색을 사용한다면 이전 프로바이더 클래스를 참조하는 애플리케이션 코드와 테스트를 업데이트하세요. 패키지를 수동으로 등록한다면 `bootstrap/providers.php` 또는 `config/app.php`와 같은 기존 프로바이더 목록에서 아래 항목으로 교체합니다.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

`LaravelLang\Locales\LaravelLangLocalesServiceProvider`와 `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine`, `LaravelLang\StarterKits` 아래의 이전 `ServiceProvider` 클래스의 수동 등록을 제거하세요. 번역 패키지는 매니페스트를 통해 검색됩니다.

JSON 대체 번역을 사용한다면 `LaravelLang\JsonFallback\TranslationServiceProvider`를 등록했던 곳에서 `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider`로 교체하세요. 이렇게 하면 Laravel 번역 프로바이더에 대한 재정의가 유지됩니다.

패키지 자동 검색 중에도 애플리케이션이 시작할 수 있도록 `composer update`를 실행하기 전에 이전 프로바이더 참조를 업데이트하세요. 이전 클래스를 불러오는 Composer 스크립트가 계속 실패한다면 `composer update --no-scripts`를 실행하고 참조를 바꾼 뒤, `composer dump-autoload`로 자동 검색을 다시 실행합니다.

## 설정 마이그레이션 {#migrate-configuration}

`config/localization.php`를 수정했다면 변경 사항을 옮기는 동안 참조용으로 보관하세요. 패키지는 새 기본 설정을 자동으로 불러옵니다. 편집할 [설정 파일](./configure.md)을 게시하세요. `localization` 태그는 다섯 파일을 모두 게시하며, 하나만 게시하려면 `.php`를 뺀 파일 이름을 태그로 사용하세요.

```bash
php artisan vendor:publish --tag=localization
```

### 별칭, 번역 스타일 및 파일 서식 {#aliases-translation-styles-and-file-formatting}

- `aliases` 매핑을 `config/localization_main.php`로 복사합니다. `'de_CH' => 'de-CH'`처럼 표준 코드를 키로, 별칭을 값으로 유지하세요.
- `config/localization_main.php`에서 `inline`을 `styles`로 바꿉니다. 표준 유효성 검사 메시지를 쓰던 기존 기본 설정을 유지하려면 `'styles' => ['default']`로 설정하세요. 인라인 메시지를 사용했다면 새 기본값인 `['default', 'inline']`을 유지하세요. 설정했던 환경에서 `LOCALIZATION_INLINE`과 `LANG_PUBLISHER_INLINE`을 제거합니다.
- `align` 키와 `LOCALIZATION_ALIGN`, `LANG_PUBLISHER_ALIGN` 환경 변수를 제거하세요. 게시된 PHP 배열에서 `=>` 연산자를 정렬하려면 프로젝트의 PHP 포매터를 설정하고 게시 후 언어 파일에 실행합니다. 예를 들어 Laravel Pint의 `binary_operator_spaces` 규칙으로 `=>`에 `align_single_space_minimal`을 적용할 수 있습니다.

### 스마트 문장 부호 {#smart-punctuation}

스마트 문장 부호를 활성화했다면 `LOCALIZATION_SMART_ENABLED`를 `LOCALIZATION_PUNCTUATION_ENABLED`로 바꾸고 `true`를 유지하세요. PHP에서 `smart_punctuation.enable`을 직접 설정했다면 해당 불리언을 `config/localization_punctuation.php`의 최상위 `enabled` 키로 옮깁니다. 문장 부호 기능은 기본적으로 꺼져 있습니다.

사용자 지정 따옴표를 `smart_punctuation.common` 및 `smart_punctuation.locales`에서 같은 파일의 최상위 `common` 및 `locales` 배열로 옮기세요. `locales`의 키는 표준 코드입니다. 로캘별 따옴표는 `common`의 대응하는 키를 재정의합니다. 예전 프랑스어 예제에서 `&nbsp;`를 복사했다면 따옴표 문자열에 실제 줄 바꿈 없는 공백이나 `"\u{00A0}"`을 사용하세요.

### 현지화된 라우트 {#localized-routes}

이전 설정의 `routes`에 있는 재정의를 `config/localization_routes.php`로 옮깁니다.

| 사용자 지정한 항목 | `config/localization_routes.php`에서 바꿀 내용 | 이름을 바꿀 환경 변수 |
| --- | --- | --- |
| 로캘 매개변수, 헤더, 쿠키, 세션 키 또는 사용자 모델의 열 | `names` 재정의를 복사하고 나머지 기본 항목은 유지합니다. | |
| 현지화 라우트 이름의 접두사 | `name_prefix`를 `prefix`로 옮깁니다. 값이 비어 있으면 `localized.`를 사용합니다. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| 접두사가 있는 라우트에서 발생하는 리디렉션 | `redirect_default`를 `redirect`로 옮깁니다. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| URL 헬퍼에서 대체 로캘을 숨길지 여부 | `hide_default`를 `hide`로 옮깁니다. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| 현지화 라우트 그룹에 적용하는 미들웨어 | 접두사 없는 라우트는 `group.middlewares.default`를 `middlewares.default`로, 접두사 있는 라우트는 `group.middlewares.prefix`를 `middlewares.prefix`로 옮깁니다. [라우트 마이그레이션](#update-localized-routes-and-middleware)의 안내에 따라 미들웨어 클래스 이름을 업데이트하세요. | |

### 모델 번역 {#model-translations}

`models`의 재정의를 `config/localization_models.php`로 옮깁니다. 사용자 지정 `suffix` 및 `helpers` 값은 최상위에 복사하세요. 이전 `filter.enabled` 키는 불리언 `filter`로 교체합니다. 필터링과 IDE 헬퍼 경로를 환경 변수로 설정한다면 `LOCALIZATION_FILTER_ENABLED`와 `VENDOR_PATH`를 유지하세요.

모델 명령은 이제 기본적으로 `app/Models`를 검색합니다. 이전처럼 `app/` 전체를 검색했거나 `localization-private.models.directory`를 직접 지정했다면 검색할 디렉터리를 각각 `directories` 배열에 추가하세요. 예를 들어 기존 검색 범위를 유지하려면 `'directories' => [app_path()]`로 설정합니다.

### 애플리케이션 코드에서 사용하는 설정 {#configuration-used-by-application-code}

- `LaravelLang\Config\Facades\Config`를 통한 읽기와 쓰기를 새 키를 사용하는 Laravel 설정 호출로 바꿉니다. 예를 들어 `Config::shared()->models->filter->enabled`는 `config('localization_models.filter')`로 바꾸세요. 타입을 지정해 읽으려면 `LaravelLang\Config\Services\ModelLangConfig`를 주입하고 `filter` 속성을 읽거나, 라우트 설정의 경우 같은 네임스페이스의 `RouteLangConfig`를 주입합니다.
- 코드가 `localization-private.map`에서 로캘 메타데이터를 읽었다면 `localization_map.locales`를 사용합니다. 재정의는 `config/localization_map.php`의 `locales` 배열에 복사하세요.
- `localization-private.plugins`와 `localization-private.packages`를 통한 등록을 [플러그인 매니페스트](./plugins.md)로 교체합니다.
- 이전 `translators` 블록과 `laravel-lang/translator`에서만 사용하던 기존 `TRANSLATION_*` 설정을 제거하세요.

재정의를 옮기고 애플리케이션의 참조를 업데이트한 뒤 `config/localization.php`를 제거하고 `php artisan config:clear`를 실행합니다.

## 표준 코드가 포함된 경로로 언어 파일 이동 {#move-language-files-to-canonical-paths}

1. `APP_LOCALE`과 `APP_FALLBACK_LOCALE`을 표준 코드로 설정하세요. 별칭을 `localization_main.aliases`로 옮겨 URL과 로캘 입력을 계속 사용할 수 있게 합니다.
2. `lang/pt-br.json`과 `lang/pt-br/`처럼 별칭이 붙은 파일과 디렉터리의 이름을 `lang/pt_BR.json`, `lang/pt_BR/`로 바꾸세요. `lang/vendor/<package>/` 아래에도 같은 별칭이 있는지 확인합니다. 두 이름이 모두 있다면 별칭 경로를 삭제하기 전에 내용을 병합하고 서로 충돌하는 사용자 지정 번역을 정리하세요.
3. 벤더 PHP 번역에만 있는 로캘은 각각 `php artisan lang:add <locale>`로 설치하세요. 설치 목록에는 언어 디렉터리 바로 아래 항목, `lang/vendor/<package>/` 아래 JSON 파일, 실제로 적용되는 현재 및 대체 로캘이 포함됩니다.
4. 설정과 플러그인을 마이그레이션한 다음 `php artisan lang:update`를 실행하고 변경 사항을 검토하세요. Publisher는 더 이상 파일을 게시하지 않는 통합의 키도 남겨 둘 수 있습니다. 애플리케이션에서 계속 사용하는 키는 보존하고 호출부를 확인한 뒤 불필요한 키를 삭제하세요. 파일 재생성 및 보존 방법은 [게시 스크립트와 사용자 지정 번역](#update-publication-scripts-and-keep-custom-translations)을 참조하세요.

## 통합 업데이트 {#update-integrations}

### Locales {#locales}

`LaravelLang\Locales\Facades\Locales` 가져오기는 유지하고 각 호출부에 필요한 데이터 형태에 맞게 호출을 업데이트하세요.

| 이전 호출 | 대체 호출 |
| --- | --- |
| 객체를 반환하는 `Locales::available()`, `installed()` 또는 `protects()` | `Locales::data()`에서 같은 메서드를 호출합니다. |
| `Locales::raw()->available()`, `installed()` 또는 `protects()` | 같은 메서드를 `Locales`에서 직접 호출하여 별칭 문자열 컬렉션을 가져옵니다. 표준 코드는 `false`를 전달하고, 호출부에 배열이 필요하면 `->all()`을 호출합니다. |
| `Locales::installed(false)` 또는 `Locales::raw()->installed(false)` | 이제 `false`는 표준 코드를 선택합니다. 현재 및 대체 로캘은 언어 파일이 없어도 모두 포함됩니다. 이전 명명된 인수인 `withProtects: false`도 교체하세요. 보호되는 로캘을 제외하려면 `Locales::installed(false)->diff(Locales::protects(false))->values()`를, 객체가 필요하면 `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`를 사용합니다. |
| `Locales::notInstalled()` 또는 `Locales::raw()->notInstalled()` | 객체는 `Locales::data()->uninstalled()`로, 별칭 문자열은 `Locales::uninstalled()`로 가져옵니다. 표준 코드 문자열을 가져오려면 `false`를 전달합니다. |
| `Locales::getCurrent()` 또는 `Locales::getDefault()` | DTO는 `Locales::data()->current()`로, 문자열은 `Locales::current()`로 가져옵니다. `Locales::raw()->getDefault()`는 `Locales::current()`로 바꿉니다. Laravel이 보유한 값을 `Locales::raw()->getCurrent()`로 가져왔다면 `app()->getLocale()`을 사용하세요. |
| `Locales::getFallback()` 또는 `Locales::raw()->getFallback()` | DTO는 `Locales::data()->fallback()`으로, 문자열은 `Locales::fallback()`으로 가져옵니다. 이제 대체 로캘이 알 수 없는 값이면 영어를 사용합니다. 현재 로캘이 알 수 없는 값이면 실제로 적용되는 대체 로캘을 사용합니다. |
| `Locales::get($locale)` 또는 `Locales::raw()->get($locale)` | `isInstalled()`로 검사하고 대체 로캘을 명시적으로 선택합니다. 지원되지만 설치 여부를 알 수 없는 로캘은 `isAvailable()`로 확인한 후 `info()`를 호출합니다. |
| `Locales::info($locale)` 또는 `Locales::raw()->info($locale)` | `Locales::info($locale)`를 사용하고 문자열이 필요할 때 `->alias` 또는 `->code`를 읽습니다. 현재 로캘에는 `null`을 전달하세요. 알 수 없거나 빈 코드에는 `LaravelLang\Exceptions\UnknownLocaleException`을 처리하거나 조회 전에 `isAvailable()`로 검사합니다. |

설치되지 않았거나 유효하지 않은 입력에 대해 현재 로캘로 대체하던 예전 `get()`의 동작을 유지하려면 호출부에서 대체 로캘을 선택하세요.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // 경로, 데이터베이스 행 및 기타 저장 데이터에 사용하세요.
$alias = $locale->alias; // URL이나 언어 선택기에 사용하세요.
```

이전에 `raw()->get()`의 결과를 `app()->setLocale()`로 넘기던 요청 핸들러를 업데이트하세요. 요청값을 받은 뒤 `Locales::set($locale)`을 사용합니다. 이 메서드는 설치된 로캘 코드, 설정된 별칭, 열거형 케이스 및 새 DTO를 받고 Laravel에 표준 코드를 지정합니다. 요청값이 `pt-br`이면 Laravel은 `pt_BR`을 받고 `Locales::current()`는 `pt-br`을 반환합니다. 표준 코드가 필요하면 `Locales::current(false)` 또는 `Locales::fallback(false)`를 호출하세요.

애플리케이션의 다른 곳에 값을 저장할 때는 `isAvailable()` 또는 `isInstalled()`로 검증한 다음 `fromAlias()`로 표준 코드를 구합니다. 표시용으로 표준 코드를 변환하려면 `toAlias()`를 사용하세요. 예전 `Services\Resolver`는 허용하는 PHP 타입을 확인하는 `acceptsInput()`이나 타입이 있는 값에서 문자열을 꺼내는 `normalize()`로 바꾸세요. `normalize()`는 `pt-br` 같은 별칭도 전달된 문자열 그대로 보존합니다.

가져오기, 타입 힌트 및 테스트에서 `LaravelLang\Locales\Data\LocaleData`를 `LaravelLang\LocaleNames\LocaleData`로 바꾸세요. 새 읽기 전용 객체에는 `code`, `alias`, `native`, `localized` 문자열 속성이 있습니다. URL과 UI 값에 이전 `code`를 사용했다면 `alias`로 바꾸세요. 이전에 `$data->locale`을 읽던 곳에서는 `LaravelLang\LocaleList\Locale`을 가져오고 `Locale::from($data->code)`를 사용합니다. 템플릿이나 포매터가 `type`, `regional`, `direction`을 사용한다면 `config("localization_map.locales.{$data->code}", [])`에서 읽으세요. 항목이 없으면 애플리케이션의 기본값을 제공하세요. 이전 기본값을 유지하려면 문자 체계에는 `Latn`, 지역에는 표준 코드, 방향에는 `Direction::LeftToRight`를 사용합니다.

`withCountry`, `withCurrency`, `withCountries`, `withCurrencies` 플래그를 제거하세요. 해당 로캘 행의 국가와 통화는 명시적으로 가져옵니다. 이전 `LaravelLang\Locales\Data\CountryData` 및 `CurrencyData` 타입의 가져오기를 각 이름 조회 패키지의 데이터 클래스로 업데이트하세요.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

이전 서비스에서 `localizedLocales()`, `localizedCountries()`, `localizedCurrencies()`를 호출했다면 각 이름 조회 서비스의 `all(null)`과 `all($displayLanguage)`로 각각 고유 값과 현지화 값을 가져오세요. 이전 `Services\Locales` 또는 `Services\RawLocales` 클래스의 직접 주입을 파사드나 대응하는 `Services\MainService` 및 `Services\DataService`로 교체합니다. 새 서비스 API는 [로캘과 이름](./locales.md)을 참조하세요.

#### 로캘 열거형 헬퍼 {#locale-enum-helpers}

`Locale` 및 `Direction` 열거형의 케이스 값은 그대로입니다. 사용자 지정 로캘 목록이나 선택기를 업그레이드할 때는 `archtechx/enums`에서 제거된 헬퍼를 교체하세요.

| 이전 헬퍼 | 대체 방법 |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | `array_column(Locale::cases(), 'value', 'name')`으로 이름을 값에 매핑합니다. |
| `stringOptions()` | 뷰에서 `Locale::cases()`로 옵션을 생성합니다. 표준 코드는 케이스 값을, 표시용 별칭은 `Locales::toAlias($case)`를 사용합니다. |
| `fromName()` 또는 `tryFromName()` | `Locale::cases()`에서 `name`으로 케이스를 찾고 일치하는 케이스가 없는 경우를 처리하세요. `pt_BR`과 같은 열거형의 값에는 `Locale::from()` 또는 `Locale::tryFrom()`을 사용하세요. |
| `Locale::onlyValues()` | `array_column(Locale::cases(), 'value', 'value')`로 값을 같은 값에 매핑합니다. 값 목록에는 `Locale::values()`를 사용하세요. |
| `Direction::values()` | `array_column(Direction::cases(), 'value')`를 사용합니다. |

### 이름 조회 API 교체 {#replace-the-name-lookup-apis}

`LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames`, `LaravelLang\NativeLocaleNames`의 가져오기를 `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames`, `LaravelLang\LocaleNames`로 바꾸세요. Laravel 컨테이너를 통해 `CountryNames`, `CurrencyNames`, `LocaleNames`를 가져오거나 클래스에 서비스를 주입합니다.

| 이전 사용법 | 대체 방법 |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| 고유 이름을 위한 `SomeNames::get()` | `$names->all(null)` |
| `SortBy::None` | 정렬 인수를 생략합니다. |
| `SortBy::Key` | 반환된 컬렉션에서 `->sortKeys()`를 호출합니다. |
| `SortBy::Value` | 현지화된 이름으로 정렬하려면 `all()`에 `SortDirection::Ascending`을 전달합니다. |

알려진 항목을 가져오려면 `one($displayLocale, $entryLocale)`을 사용하세요. 조회 키는 `fr`과 같은 로캘입니다. 예를 들어 `app(CountryNames::class)->one('de', 'fr')`은 프랑스어에 해당하는 국가를 반환하며, 이름은 독일어로, `code`는 `FR`로 설정됩니다. 국가 및 통화 DTO는 각 패키지의 루트 네임스페이스에 있고, 조회에 사용한 키는 `locale`로 노출합니다. `code` 필드는 계속 국가 또는 통화 코드를 보유합니다.

세 서비스는 모두 표준 로캘을 키로 하는 컬렉션을 반환합니다. 이전 로캘 이름 패키지의 문자열 배열을 사용했다면 각 DTO를 `localized`로 매핑하세요. 이전 국가나 통화 DTO에 `toArray()`를 호출했거나 속성을 변경했다면 애플리케이션에서 사용할 배열을 명시적으로 구성하세요. 새 DTO는 읽기 전용입니다.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

`one()`을 호출하기 전에 사용자가 선택한 로캘을 해당 카탈로그와 대조해 검증하세요. 알 수 없는 표시 로캘로 `all()`을 호출하면 빈 목록이 나올 수 있습니다. 고유 이름이 필요하면 `null`을 전달하세요. 사용자 지정 값을 갖는 열거형을 썼다면 그 문자열 값을 전달합니다. [로캘과 이름](./locales.md)

### 현지화 라우트 및 미들웨어 업데이트 {#update-localized-routes-and-middleware}

현지화 URL을 생성하는 PHP 파일과 Blade 템플릿에 네임스페이스가 있는 헬퍼를 가져오세요. `\LaravelLang\Routes\localizedRoute()`를 직접 호출할 수도 있습니다.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` 및 `LocalizationRoute::group()`은 로캘 접두사가 있는 라우트와 없는 라우트를 모두 등록합니다. 기존 그룹은 유지하면서 URL 헬퍼 호출을 검토하세요. 로캘 매개변수를 생략하면 헬퍼가 대체 로캘의 별칭을 사용합니다. 언어 선택기에는 `Locales::toAlias($locale)` 또는 `$localeData->alias`로 원하는 별칭을 명시해 전달하세요. 전달한 로캘 문자열은 그대로 사용됩니다. 매개변수 이름을 바꿨다면 배열 키를 `localization_routes.names.parameter`와 일치시키세요.

라우트, `bootstrap/app.php`, 사용자 지정 미들웨어 목록의 가져오기와 등록을 교체합니다. 표의 모든 클래스는 `LaravelLang\Routes\Middlewares`에 있습니다.

| 이전 미들웨어 | 대체 클래스 |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

모델 미들웨어를 가드와 함께 생성했다면 `LocalizationByModelMiddleware::class . ':admin'`처럼 Laravel 미들웨어 매개변수로 전달하세요. 이 미들웨어의 사용자 리졸버에는 Eloquent 모델이 필요합니다. 사용자 지정 미들웨어 하위 클래스에서는 `LaravelLang\Config\Services\RouteLangConfig`를 주입하고 `$this->routeConfig->name`에서 소스 이름을 읽으며, `detect()`에서 `int|string|null`을 반환하세요. `LaravelLang\Routes\Services\Route`에 대한 직접 참조를 `LaravelLang\Routes\Services\RouteService`로 교체합니다.

애플리케이션이 사용하는 각 소스를 테스트하세요. 헤더 감지는 PHP Intl을 사용해 `Accept-Language`의 선호 순서대로 설치된 로캘을 확인합니다. 전체 언어 태그를 먼저 시도한 후 기본 언어를 확인합니다. 뒤에 실행되는 미들웨어는 앞서 선택된 로캘을 덮어쓸 수 있습니다. 접두사가 붙은 라우트에서 URL의 로캘을 우선하려면 해당 체인에 URL에서 로캘을 가져오는 미들웨어만 남겨 두세요.

URL 생성과 요청 처리를 별도로 테스트하세요. `localization_routes.hide`는 URL 생성 시 대체 로캘 세그먼트를 숨깁니다. `localization_routes.redirect`는 현재 로캘에 대한 접두사 있는 요청을 접두사 없는 라우트로 리디렉션합니다. 이름과 접두사가 있는 라우트는 로캘이 유효하지 않거나 설치되지 않았을 때도 접두사 없는 라우트로 리디렉션합니다.

`LocaleHasBeenSetEvent` 리스너에서는 표준 코드에 `$event->locale->code`를, 표시용 값에는 `$event->locale->alias`를 사용합니다. 페이로드는 이제 `LaravelLang\LocaleNames\LocaleData`입니다. 미들웨어는 선택한 로캘이 바뀌면 이벤트를 보내고 세션에 표준 코드를 저장합니다. 수동 `LocaleHasBeenSetEvent::dispatch($data)` 호출을 `event(new LocaleHasBeenSetEvent($data))`로 교체하세요. [현지화된 라우트](./routes.md)

### 모델 번역 코드와 데이터 마이그레이션 {#migrate-model-translation-code-and-data}

각 번역 테이블의 `locale` 열에 별칭을 저장한 행이 있는지 확인하세요. 발견한 별칭마다 데이터 마이그레이션으로 행의 값을 표준 코드로 업데이트합니다. 변경하기 전에 같은 `item_id`의 표준 코드 행이 이미 있는지 확인하세요. 고유한 `(item_id, locale)` 제약을 충족하도록 보존할 값은 한 행에 병합하세요. 이전 트레이트의 일부 버전도 표준 코드를 기록했으므로 실제로 테이블에 저장된 값에 따라 마이그레이션하세요.

가져오기와 생성된 클래스를 업데이트합니다.

| 이전 사용법 | 대체 방법 |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` 또는 `UnknownModelPathException` | `LaravelLang\Exceptions`에 있는 같은 이름의 클래스 |
| `translationModelName()` | `translationModel(): string`. 사용자 지정 번역 클래스를 선택하는 재정의도 업데이트하세요. |
| `getTranslationTable()` | 정규화된 열 이름은 `translationQualifyColumn($column)`으로, 테이블 이름은 `translationsRaw()->getRelated()->getTable()`로 가져옵니다. |

`getTranslation()`, `setTranslation()`, `hasTranslated()`, `forgetTranslation()`은 로캘 문자열, `Locale` 또는 새 `LocaleData`를 받습니다. 이 메서드를 호출하기 전에 명시적으로 쓸 로캘을 설치하세요. 로캘을 지정하지 않고 읽을 때 현재 값이 `null`이면 대체 로캘을 사용합니다. 로캘을 명시하면 해당 로캘의 값을 직접 읽습니다. 하위 수준의 `translation()` 메서드를 호출한다면 로캘 문자열이나 `null`을 전달합니다. DTO는 `$locale->code`로 변환하세요.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

`de_CH` → `de-CH` 별칭을 설정하고 스위스 독일어를 설치하면 두 호출 모두 `de_CH`로 저장된 행을 가리킵니다. 번역 필드에 여러 값을 채울 때는 `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`과 같이 로캘을 키로 하는 매핑을 전달하세요. 인덱스로 구성된 목록은 이제 `InvalidLocaleInputException`을 발생시킵니다. 로드한 `translations` 관계를 사용자 지정 코드에서 설정할 때는 번역 모델의 Laravel 컬렉션을 제공하세요.

`translated()`, `whereTranslation()` 및 관련 스코프는 Eloquent 쿼리 빌더에서 호출합니다. 스코프의 로캘 문자열로 코드나 별칭을 전달하세요. `orderByTranslation()`에는 `'asc'`, `'desc'` 또는 `SortDirection`을 사용합니다. `whereTranslation()`이나 `whereTranslationLike()`로 현재 및 대체 로캘 이외의 로캘을 조회하려면 `localization_models.filter`를 `false`로 설정하거나 `translationsRaw()`를 직접 조회하세요.

`$event->locale->value`를 호출하거나 `$event->locale`을 `Locale` 열거형과 비교하는 이벤트 소비 코드를 업데이트하세요. `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent`는 이제 명시한 로캘의 문자열 별칭을 갖습니다. 호출부에서 로캘을 생략하면 값 설정 이벤트는 `null`을 갖습니다. 저장 키로 쓰기 전에 `Locales::fromAlias()`로 별칭을 변환하세요. 값 설정 이벤트는 `save()` 전, 속성에 값을 할당할 때 발생합니다. 모델 번역 이벤트 세 가지를 수동으로 디스패치하려면 `event(new …)`를 사용하세요.

생성된 IDE 헬퍼를 유지한다면 가져오기와 모델 검색 경로를 업데이트한 뒤 `php artisan lang:models-helper`를 실행하세요. [모델 번역](./models.md)

### 게시 스크립트 업데이트 및 사용자 지정 번역 보존 {#update-publication-scripts-and-keep-custom-translations}

| 이전 명령 또는 전제 | 마이그레이션 방법 |
| --- | --- |
| `lang:add <locale>`은 추가하는 로캘만 새로 고침 | 이제 설치된 모든 로캘을 새로 고칩니다. 스크립트에서 코드를 명시하고 기존 로캘 파일의 업데이트를 고려하세요. |
| `lang:reset` | 병합에는 `lang:update`를 사용하세요. 처음부터 다시 만들려면 사용자 지정 항목을 백업하고 재생성할 파일을 제거한 뒤, 대상 로캘 코드를 명시해 `lang:add`를 실행하세요. |
| `lang:rm <locale> --force` | `lang:rm <locale>`을 사용하세요. Laravel의 현재 및 대체 로캘은 보호됩니다. |
| `lang:rm`의 종료 코드만 검사 | 로캘별 결과를 확인하세요. `DONE`은 제거가 완료되었다는 뜻이고 `FAILED`는 제거를 시도한 뒤에도 경로가 남아 있다는 뜻입니다. 성공 종료 코드에서도 `PROTECTED`, `INVALID`, `SKIP`이 나올 수 있습니다. |

Artisan을 명령 클래스로 호출한다면 `LaravelLang\Publisher\Console\Add`, `Remove`, `Update`를 `AddCommand`, `RemoveCommand`, `UpdateCommand`로 바꾸세요. 사용자 지정 텍스트 데코레이터는 `Contracts\TextDecorator` 대신 `LaravelLang\Publisher\Contracts\Decorator`를 구현하고 바인딩합니다. 이 계약의 `convert(string $locale, string $value): string`은 표준 로캘 코드를 받습니다.

기존 언어 파일을 버전 관리에 포함한 상태에서 처음 `lang:update`를 실행하세요. Publisher는 기존 대상에 병합하고 숫자 키를 포함해 일치하는 키를 교체합니다. 가능하면 사용자 지정 번역은 별도의 애플리케이션 번역 파일에 두세요. 생성 대상 전체를 직접 관리하려면 경로 패턴을 `localization_main.skip`에 추가합니다. 원문으로 돌아간 값이 있는지 확인하세요. Publisher는 사전에 번역이 없을 때 원문을 사용합니다.

이전 업데이트에서 `http-statuses.php`의 숫자 키가 다시 매겨졌다면 영향을 받은 생성 파일을 백업한 뒤 삭제하세요. 문제를 피하려고 `localization_main.skip`에 `'*/http-statuses.php'`를 추가했다면 해당 항목을 제거하세요. `php artisan lang:update`를 실행해 파일을 다시 만든 다음 사용자 지정 번역을 원래 상태 코드에 복원하세요. [번역 관리](./translations.md)

### 이전 플러그인 등록 방식 교체 {#replace-legacy-plugin-registration}

서드 파티 번역 팩을 쓴다면 매니페스트 기반 릴리스로 업그레이드하세요. 팩을 유지보수한다면 이전 `LaravelLang\Publisher\Plugins\Provider`를 등록하는 프로바이더 및 Composer 자동 검색 항목을 제거하세요. 해당 프로바이더가 관계없는 Laravel 서비스도 설정한다면 그 등록은 일반 Laravel 서비스 프로바이더에 유지합니다.

1. `vendor/` 아래에 설치되는 팩의 루트에 `laravel_lang.json`을 추가합니다. 각 `Plugin::files()` 항목을 팩의 `source/` 디렉터리 기준 `source` 경로와 애플리케이션 언어 디렉터리 기준 `target` 경로로 바꾸세요. 대상에는 `{locale}`을 사용합니다.
2. Composer 매칭 규칙을 매니페스트로 옮깁니다. 설치된 의존성에는 `vendor`와 `version`을 사용하고, 루트 Composer 프로젝트에는 `project: true`가 있는 별도 항목을 사용합니다. 이전 플러그인이 `$with_project_name = true`로 두 모드를 지원했다면 두 항목을 모두 만드세요.
3. 사전을 `locales/<code>/<style>.json`으로 변환합니다. 소스 파일의 값이 사전 조회 키입니다. 예를 들어 PHP 항목 `'enable' => 'Enable audit trail'`을 `{locale}/audit.php`에 게시하면 애플리케이션 키는 계속 `audit.enable`입니다. 사전에서 `"Enable audit trail"` 아래에 그 번역을 저장하세요.
4. Mona 체크아웃에서 아래 Toolkit 명령을 이전 팩에 대해 실행합니다. 이 명령은 이전 JSON 및 PHP 사전에서 `default.json`, `inline.json`을 만들고 로캘 제외 목록을 옮깁니다. 결과를 검토한 뒤 매니페스트 및 소스 파일과 함께 새 팩에 추가하세요. 별칭을 쓴 사전 디렉터리는 표준 코드로 이름을 바꿉니다.
5. 팩의 Composer 매니페스트에 현재 Publisher를 의존성으로 선언합니다. 공용 사전의 번역을 사용한다면 `laravel-lang/library`도 추가하세요. 애플리케이션에 팩을 설치하고 `php artisan lang:update`를 실행해 게시 결과를 확인합니다.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

기존 애플리케이션에서는 업그레이드 후 어떤 기본 제공 팩 규칙이 적용되는지 확인하세요.

- 이전에 Spark Paddle, Spark Stripe, Nova Dusk Suite 또는 Nova Log Viewer용으로 게시되던 Laravel-Lang 번역이 여전히 필요하다면 해당 키를 애플리케이션이 관리하는 파일에 남기거나, 예전 소스와 사전을 사용자 지정 팩으로 옮기세요.
- 스타터 키트 번역은 이제 프로젝트의 Composer `name`을 기준으로 매칭합니다. 프로젝트 이름을 바꿨거나 의존성으로 키트를 사용한다면 `laravel/livewire-starter-kit`처럼 키트의 Composer 이름을 `localization_main.force`에 추가해 번역을 게시하세요. [번역 플러그인](./plugins.md)

### JSON 대체 번역 및 번역 도구 업데이트 {#update-json-fallback-and-translation-tooling}

애플리케이션에서 `LaravelLang\JsonFallback\Translator`를 가져오거나 타입 힌트에 사용한다면 `LaravelLang\JsonFallback\OverridedTranslator`로 바꾸거나 Laravel의 번역기 계약에 의존하세요. 이전 번역기를 상속했다면 사용자 지정 동작을 애플리케이션 번역기나 별도 번역 서비스로 옮기세요. 대체 번역기는 해석하지 못한 키에 대해 Laravel의 누락 번역 콜백을 호출합니다. 애플리케이션에서 콜백을 등록한다면 검토하고, 대체 JSON이 먼저 검사되므로 대체 JSON과 현재 언어 PHP에 모두 있는 키도 테스트하세요. [JSON 대체 번역](./translations.md#json-fallback)

별도의 `laravel-lang/translator` 패키지로 기계 번역을 사용한다면 이 의존성을 제거하기 전에 `Translate` 파사드 호출과 Google, DeepL, Yandex 통합을 선택한 번역 서비스로 마이그레이션하세요. 이전 스크립트가 이미 생성한 번역은 애플리케이션이나 팩의 사전에 보존하세요.

`laravel-lang/status-generator`의 `vendor/bin/lang`으로 팩 카탈로그를 유지했다면 Mona 체크아웃에서 `composer install`을 실행한 뒤 아래 새 명령을 사용하세요.

| 이전 스크립트 | 사용할 Toolkit 스크립트 |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. 생성된 `locales/<code>/_status.json` 보고서를 읽으세요. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. 공용 Library를 관리하려면 `sync:library`를 사용하세요. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. 알려진 로캘의 디렉터리를 생성합니다. |
| `vendor/bin/lang download` | ZIP URL은 `--url`, 전체 대상 경로는 `--directory`, 아카이브 안의 경로는 반복 가능한 `--paths`로 전달하세요. |
| `vendor/bin/lang translate` | 기계 번역을 별도 서비스로 옮기세요. 저장된 문자열 중 원문과 여전히 일치하는 항목을 찾으려면 Toolkit의 `status`를 실행하세요. |

전체 예시는 [Toolkit 명령](./toolkit.md)을 참조하세요.

## 업그레이드 확인하기 {#verify-the-upgrade}

애플리케이션 테스트를 실행하세요. 주요 로캘마다 게시된 메시지를 확인하고, 대체 번역, 설정한 별칭이 들어간 현지화 URL, 표준 코드로 저장된 모델 번역도 점검하세요. 소스 변경 사항과 함께 생성된 파일 및 저장된 로캘 값을 검토합니다.

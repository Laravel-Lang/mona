---
title: 설정
description: Mona의 번역 스타일, 로캘 별칭, 게시 필터, 문장 부호, 라우트 및 모델을 설정합니다.
---

Mona는 기본 설정을 자동으로 불러옵니다. 수정할 파일을 애플리케이션의 `config/` 디렉터리에 게시하세요.

```bash
php artisan vendor:publish --tag=localization_main
```

`localization` 태그는 다섯 파일을 모두 게시합니다. 각 파일에서 `.php`를 뺀 이름은 게시 태그이자 Laravel 설정 키입니다.

| 파일 | 설정 |
| --- | --- |
| `localization_main.php` | 번역 스타일, 별칭, 게시 필터 |
| `localization_punctuation.php` | 스마트 문장 부호와 로캘별 따옴표 |
| `localization_routes.php` | [라우트 이름, 로캘 소스 및 미들웨어](./routes.md) |
| `localization_models.php` | [번역 모델 접미사, 필터링 및 생성기 경로](./models.md) |
| `localization_map.php` | 문자 체계, 지역, 텍스트 방향 등의 로캘 메타데이터 |

Laravel은 최상위 설정마다 애플리케이션에 지정된 값으로 바꿉니다. `localization_punctuation.php`의 `locales` 배열을 수정할 때는 계속 사용할 로캘별 기본값을 남겨 두세요.

설정을 변경한 뒤 기존 설정 캐시가 있으면 `php artisan config:clear`로 비우세요. 게시 설정에 영향을 받는 번역은 `php artisan lang:update`를 실행해 다시 생성하세요.

## 번역 스타일 {#translation-styles}

번역 스타일은 일부 문구의 표현을 바꾸는, 이름이 있는 사전입니다. 예를 들어 번역 팩은 친근한 어조를 위한 `informal.json`이나 제품에 맞는 표현을 위한 `john.json`을 제공할 수 있습니다. 스타일 이름은 로캘 코드, 애플리케이션 이름, 패키지 이름과 무관합니다. 모든 번역 팩은 지원하는 언어에 대해 `locales/<locale>/<style>.json`에 스타일을 제공할 수 있습니다. 스타일에는 문구가 하나만 있어도 됩니다. 처음에는 격식을 차리지 않는 호칭을 지원하기 위해 도입했습니다. [비격식 번역 논의](https://github.com/orgs/Laravel-Lang/discussions/124)

`john`을 제공하는 플러그인을 설치한 뒤 각 사전 소스에서 이 스타일이 우선하도록 `config/localization_main.php`의 목록 마지막에 배치합니다.

```php
'styles' => ['default', 'informal', 'john'],
```

`php artisan lang:update`를 실행하면 Publisher가 설치된 각 로캘의 스타일 파일을 병합합니다. 각 문구는 `john`에 있으면 그 값을 사용하고, 없으면 `informal`, `default` 순서로 찾습니다. 예를 들어 프랑스어 팩이 `locales/fr/john.json`에서 문구 하나를 재정의해도 독일어에서는 같은 문구를 `locales/de/informal.json`이나 `locales/de/default.json`에서 가져옵니다. Publisher는 이 순서를 공용 Library와 파일을 제공하는 팩에 각각 적용합니다. 그 파일에서는 팩에 있는 문구가 Library의 같은 항목보다 우선합니다.

기본 설정은 `['default', 'inline']`입니다. `inline` 스타일은 “이 필드는 수락해야 합니다.”와 같은 일반적인 유효성 검사 메시지를 제공합니다. 기본 표현만 사용하려면 `styles`를 `['default']`로 설정하세요. 목록에 `default`가 없으면 Publisher가 맨 앞에 추가합니다.

## 로캘 별칭 {#locale-aliases}

`localization_main.aliases`를 표준 코드와 별칭의 매핑으로 설정합니다. 예를 들어 `config/localization_main.php`가 반환하는 배열에 다음 항목을 추가하세요.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan은 `de-CH`를 받아들일 수 있지만 Laravel과 게시된 파일은 `de_CH`를 사용합니다. `APP_LOCALE`과 `APP_FALLBACK_LOCALE`에는 표준 코드를 설정하세요. 변환과 조회 예시는 [로캘과 이름](./locales.md)을 참조하세요.

별칭은 대소문자를 구분합니다. 로캘마다 고유한 별칭을 지정하고 다른 로캘의 표준 코드와 중복되지 않게 하세요.

## 파일 제외 및 통합 강제 적용 {#skip-files-and-force-integrations}

- `localization_main.skip`은 `/`를 구분자로 사용하는 전체 경로를 대소문자 구분 없이 `*` 패턴과 비교합니다. 소스 파일, 사전, 게시 대상을 제외할 수 있습니다. 예를 들어 `['*/lang/fr/validation.php']`는 `lang/`을 사용하는 애플리케이션에서 Publisher가 그 대상에 쓰지 않게 합니다.
- `localization_main.force`는 Composer 패키지 이름을 받습니다. 지정한 의존성이 없거나 설치된 버전이 일치하지 않거나 루트 프로젝트 이름이 달라도 Publisher가 매니페스트 항목을 사용할 수 있게 합니다. 매니페스트의 번역을 애플리케이션에 적용할 수 있을 때 사용하세요.

`skip`이 `force`보다 우선합니다.

## 스마트 문장 부호 {#smart-punctuation}

게시된 번역에서 조판용 따옴표, 대시, 줄임표를 사용하려면 `LOCALIZATION_PUNCTUATION_ENABLED=true`를 설정합니다. 일반 규칙은 `localization_punctuation.common`에서, 표준 로캘 코드별 재정의는 `localization_punctuation.locales`에서 수정하세요. Publisher가 다음에 문자열을 게시할 때 이 규칙이 적용됩니다.

## 로캘 메타데이터 {#locale-metadata}

`localization_map.locales`는 표준 코드를 문자 체계(`type`), 지역 로캘(`regional`), 선택 사항인 쓰기 방향(`direction`)과 연결합니다. 방향이 설정되어 있지 않으면 `Direction::LeftToRight`를 사용하세요.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```

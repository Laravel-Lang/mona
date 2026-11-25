---
title: 번역 플러그인
description: 번역 팩을 설치하거나 Laravel-Lang용 Composer 플러그인을 게시합니다.
---

Publisher는 Composer로 설치된 번역 팩을 찾습니다. 팩은 `laravel_lang.json` 매니페스트, PHP 또는 JSON 소스 파일을 제공하며 필요한 경우 자체 로캘 사전도 포함합니다.

## 번역 팩 설치하기 {#install-a-pack}

Laravel 애플리케이션에 팩을 설치하고 현재 사용하는 로캘을 업데이트하세요. 예를 들어 Actions 팩은 버튼과 기타 작업의 레이블을 추가합니다.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

로캘을 추가하려면 [번역 관리](./translations.md), 서드 파티 번역 팩을 찾으려면 [커뮤니티 플러그인](./plugins-community.md)을 참조하세요.

### 공식 번역 팩 {#official-translation-packs}

Common에는 아래 팩이 포함됩니다. Composer로 각 팩을 개별 설치할 수도 있습니다.

| 패키지 | 번역 내용 |
| --- | --- |
| `laravel-lang/lang` | Laravel 프레임워크와 Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys, Laravel UI 등의 패키지 |
| `laravel-lang/starter-kits` | Laravel의 Livewire, React, Svelte, Vue 스타터 키트 |
| `laravel-lang/moonshine` | MoonShine 관리자 패널 |
| `laravel-lang/actions` | 버튼과 기타 작업 레이블 |
| `laravel-lang/attributes` | 읽기 쉬운 유효성 검사 속성 이름 |
| `laravel-lang/http-statuses` | HTTP 상태 문구 |

각 팩은 Publisher와 공용 Library를 설치합니다. 매니페스트는 번역을 게시할 수 있는 통합 및 버전을 지정합니다.

## 번역 팩 만들기 {#create-a-pack}

번역 템플릿에서 시작하세요. 팩의 `composer.json`에 `laravel-lang/publisher`를 의존성으로 추가하고 지원 버전을 선언합니다. 공용 번역을 사용한다면 `laravel-lang/library`도 추가하세요. Publisher는 애플리케이션에서 `vendor/*/*/laravel_lang.json` 경로로 매니페스트를 찾습니다. [번역 템플릿](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

매니페스트는 팩 루트에 둡니다. 다음 예제는 설치된 로캘마다 JSON 파일과 PHP 번역 그룹을 게시합니다.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

각 `source` 경로는 팩의 `source/` 디렉터리를 기준으로 합니다. 각 `target` 경로는 애플리케이션의 Laravel 언어 디렉터리를 기준으로 하며 `{locale}`을 포함합니다.

예를 들어 `source/audit.json`에 다음과 같이 입력합니다.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

이어서 `source/audit.php`에는 다음과 같이 입력합니다.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

두 파일 모두 프랑스어로 게시할 때 팩 내부의 `locales/fr/default.json`에서 영어 문구를 조회합니다.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

애플리케이션에서 `__('Enable audit trail')`은 JSON 번역을, `__('audit.enable')`은 PHP 번역을 읽습니다. 동일한 문구에 대해서는 팩의 번역이 공용 Library 항목보다 우선합니다. 두 사전에 모두 문구가 없으면 Publisher는 원본 값을 사용합니다.

팩의 고유한 Composer 이름을 지정하고 템플릿의 `self.version`을 지원하는 버전에 맞는 제약으로 바꾸세요. 사전 디렉터리에는 표준 로캘 코드를 사용하세요. 기본 번역은 `default.json`에, 스타일 변형은 `inline.json` 같은 파일에 넣으세요.

### 조건부로 번역 적용하기 {#apply-translations-conditionally}

| 매니페스트 속성 | 매핑이 적용되는 조건 |
| --- | --- |
| 조건 없음 | Publisher가 항상 포함합니다. |
| `vendor` | 지정된 Composer 패키지가 설치되어 있습니다. |
| `vendor` 및 `version` | 설치 버전이 Composer 버전 제약을 충족합니다. |
| `vendor` 및 `project: true` | 루트 프로젝트의 Composer 이름이 `vendor`와 일치합니다. 이 모드에서 Publisher는 프로젝트 이름을 확인합니다. |

매핑을 설치된 의존성과 이름이 같은 루트 프로젝트에 모두 적용하려면 매니페스트 항목을 두 개 만드세요. 이전 프로바이더를 변환하려면 [업그레이드 가이드](./upgrade.md), 제외 규칙과 강제 패키지 매칭은 [설정](./configure.md)을 참조하세요.

### Laravel 애플리케이션에서 팩 테스트하기 {#test-a-pack-in-a-laravel-application}

팩이 테스트 앱과 같은 상위 디렉터리에 있을 때 앱 루트에서 다음 명령을 실행하세요. `lang/fr.json`과 `lang/fr/audit.php`를 확인하고 앱의 로캘을 `fr`로 설정해 두 키를 테스트하세요. 팩을 수정한 뒤 `php artisan lang:update`를 실행하세요.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```

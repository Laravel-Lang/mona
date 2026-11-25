---
title: 시작하기
description: Laravel-Lang을 설치하고 첫 Laravel 번역을 게시합니다.
---

Laravel-Lang은 Laravel과 관련 패키지의 번역을 게시합니다. `laravel-lang/common` 묶음에는 [로캘 및 이름 조회](./locales.md), [라우트 현지화](./routes.md), [모델 번역](./models.md) 기능도 포함됩니다. Laravel 번역 팩과 게시에 필요한 의존성만 설치하려면 `laravel-lang/lang`을 선택하세요.

## 빠른 시작 {#quick-start}

Composer, PHP `^8.3` 및 PHP `intl`, `json` 확장을 준비한 다음 Laravel 12 또는 13 애플리케이션의 루트에서 다음 명령을 실행하세요. Laravel 버전에 따라 더 최신 PHP 버전이 필요할 수 있습니다.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

애플리케이션에서 Laravel 번역 헬퍼를 사용해 게시한 프랑스어 메시지를 가져옵니다.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

프랑스어를 기본 언어로 설정하려면 애플리케이션의 `.env` 파일에 `APP_LOCALE=fr`을 설정하세요. 대체 로캘을 사용하는 조회에서 현재 로캘의 번역을 찾지 못하면 영어로 표시하도록 `APP_FALLBACK_LOCALE=en`을 설정하세요. 설정이 캐시되어 있으면 `php artisan config:clear`로 캐시를 비우세요. 이후 `__('auth.failed')` 같은 호출은 애플리케이션의 현재 로캘을 사용합니다.

- [이전 Laravel-Lang 버전](/legacy)
- [번역 관리](./translations.md) 및 [지원 로캘 살펴보기](./available-locales.md)
- [현지화 설정하기](./configure.md)
- [호환성 확인하기](./release-notes.md#versioning-and-compatibility) 또는 [기존 설치 업그레이드하기](./upgrade.md)

## 릴리스 후보 버전 사용해 보기 {#try-a-release-candidate}

게시된 20.x 릴리스 후보 버전을 사용해 보려면 별도의 Laravel 테스트 애플리케이션을 준비하세요. Composer가 같은 버전의 Laravel-Lang 의존성도 설치할 수 있도록 그 애플리케이션의 최소 안정성을 `RC`로 설정합니다. [Packagist](https://packagist.org/packages/laravel-lang/common)에 나열된 버전을 선택하세요. `<release-candidate>`를 선택한 버전으로 바꾸세요.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```

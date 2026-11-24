---
title: 번역 관리
description: Mona Publisher로 애플리케이션과 벤더의 번역을 추가, 갱신 및 제거합니다.
---

Publisher는 공식 번역 팩에 포함됩니다. Publisher의 Artisan 명령은 보통 `lang/`인 애플리케이션 언어 디렉터리에 PHP 및 JSON 번역을 기록합니다. 패키지별 파일은 `lang/vendor/` 아래에 기록할 수도 있습니다.

## 로캘 추가하기 {#add-locales}

하나 이상의 [로캘 코드](./available-locales.md)를 공백으로 구분해 전달합니다. 입력으로 설정된 별칭을 사용할 수 있으며, 생성 파일에는 표준 코드를 사용합니다.

```bash
php artisan lang:add fr de_CH
```

이 명령은 로캘 디렉터리를 만든 다음 애플리케이션의 현재 로캘과 대체 로캘을 포함해 **설치된 모든 로캘**을 갱신합니다. 인수가 없으면 지원 로캘 전체를 설치할지 묻습니다. 자동화에서는 코드를 명시하세요.

## 번역 갱신하기 {#update-translations}

```bash
php artisan lang:update
```

Publisher는 애플리케이션의 현재 및 대체 로캘, 언어 디렉터리 루트의 파일이나 디렉터리, `lang/vendor/<package>/`의 JSON 파일에서 로캘을 찾습니다. 벤더의 PHP 번역 디렉터리만으로는 설치된 로캘로 인식하지 않습니다. 해당 로캘에 `lang:add`를 실행하여 애플리케이션 디렉터리를 생성하세요.

번역 팩은 [플러그인 매니페스트](./plugins.md)에서 지원 패키지, 버전, 대상 파일을 선언합니다. Publisher는 공용 사전과 패키지 사전을 결합한 뒤 각 대상 파일에 번역을 병합합니다. 관계없는 키는 보존하고 숫자 키를 포함해 일치하는 키를 교체합니다. 사전에 문구가 없으면 원문을 기록합니다. 직접 관리하는 재정의는 별도의 번역 파일에 두거나, 직접 유지하는 생성 대상을 [제외](./configure.md#skip-files-and-force-integrations)하세요.

Composer 의존성이나 [게시 설정](./configure.md)을 바꾼 뒤에는 `lang:update`를 실행합니다. 생성된 변경 사항을 검토하세요.

## 로캘 제거하기 {#remove-locales}

```bash
php artisan lang:rm de_CH
```

이 명령은 언어 디렉터리 루트와 `lang/vendor/<package>/` 각 디렉터리에서 표준 로캘 디렉터리와 JSON 파일 및 그 안의 사용자 파일을 삭제합니다. 현재 로캘과 대체 로캘은 보호됩니다. 인수를 생략하면 보호되지 않는 로캘을 모두 지울지 묻고 기본 응답은 아니요입니다. 로캘 디렉터리가 링크인 경우 명령은 링크 자체를 제거합니다.

이 명령은 로캘마다 `DONE`, `PROTECTED`, `INVALID`, `SKIP`, `FAILED` 중 하나를 표시합니다. 삭제를 시도한 뒤에도 파일이나 디렉터리가 남으면 해당 경로를 표시하고 실패 종료 코드를 반환합니다. `PROTECTED`, `INVALID`, `SKIP`도 성공 종료 코드와 함께 반환될 수 있으므로 각 로캘의 상태를 확인하세요.

## JSON 대체 번역 {#json-fallback}

`laravel-lang/json-fallback`은 JSON 키가 없을 때 PHP 번역을 찾기 전에 대체 로캘의 JSON에서도 조회하도록 합니다. Common에 포함된 패키지입니다. 별도로 설치하려면 `composer require laravel-lang/json-fallback`을 실행하세요.

`bootstrap/providers.php`의 애플리케이션 프로바이더 목록에 `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class`를 추가하세요. 애플리케이션이 `Illuminate\Translation\TranslationServiceProvider`를 명시적으로 등록했다면 해당 항목을 Mona 프로바이더로 교체합니다. [Laravel 프로바이더 등록](https://laravel.com/docs/13.x/providers#registering-providers)

현재 로캘이 프랑스어이고 대체 로캘이 영어라면, 이제 `lang/fr.json`에 없는 키의 값을 `lang/en.json`에서 가져올 수 있습니다.

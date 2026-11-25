---
title: 번역 Toolkit
description: Mona 체크아웃에서 번역 소스와 사전을 유지보수하고 마이그레이션하며 가져옵니다.
---

Toolkit은 번역 팩 유지보수에 사용하는 명령줄 도구입니다. 루트 Composer 의존성을 설치한 뒤 Mona 체크아웃에서 실행하세요. Laravel 애플리케이션에 번역을 게시하려면 [Artisan 명령](./translations.md)을 사용하세요.

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## 번역 상태 확인하기 {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

기본적으로 이 명령은 프로젝트 안에 `locales/<locale>/_status.json`을 작성합니다. 저장한 번역이 원문과 일치하는 항목을 나열하고 스타일별로 묶습니다. `locales/_settings.json`과 `locales/<locale>/_settings.json`의 제외 항목을 합치고 빈 보고서를 지웁니다. 사전에 아예 없는 문구를 찾으려면 팩의 소스 파일과 비교하세요.

`--output-dir`을 지정하면 별도의 디렉터리에 `<locale>.json` 보고서를 작성합니다. 상대 경로는 현재 작업 디렉터리를 기준으로 해석하며, 작성할 보고서가 있을 때 대상 디렉터리를 만듭니다.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

의도적으로 원문과 같게 번역한 문구는 해당 `_settings.json` 파일의 `excludes`에 추가하세요.

```json
{
  "excludes": ["Laravel"]
}
```

## 로캘 디렉터리 관리하기 {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale`은 모든 표준 [로캘 코드](./available-locales.md)에서 빠진 디렉터리를 생성합니다. 새 디렉터리는 비어 있습니다. `delete-locale`은 `locales/` 바로 아래에서 디렉터리 이름이 이 목록에 없는 항목을 각각 삭제합니다. 로캘 별칭이나 사용자 지정 코드를 포함한다면 특히 실행하기 전에 대상을 확인하세요.

## 오래된 사전 항목 삭제하기 {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library`는 `src/` 아래에서 `laravel_lang.json` 매니페스트가 있는 패키지의 원문을 수집하고 `src/Library/locales/`의 사전을 다시 작성합니다.
- `sync:package`는 한 팩의 `source/` 디렉터리를 읽고 그 팩의 `locales/` 디렉터리에서 미사용 항목을 제거합니다. 이 과정에서 빈 사전도 삭제합니다.

두 명령 모두 사용 중인 문구의 기존 번역과 `_settings.json`처럼 밑줄로 시작하는 파일을 보존합니다. 새 문구의 번역은 사전에 직접 추가하세요.

인수를 생략하면 `sync:library`는 체크아웃의 `src/` 디렉터리를, `sync:package`는 현재 작업 디렉터리를 사용합니다. 두 명령 모두 표준 로캘 코드인 디렉터리를 처리합니다.

## 이전 사전 마이그레이션하기 {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

`source/`와 `locales/`를 포함한 이전 번역 팩을 전달하세요. 로캘 하위 디렉터리를 저장할 위치는 `--target`으로 지정합니다. 이 옵션을 생략하면 이전 프로젝트의 `locales/`에 결과를 씁니다.

- `json.json`과 `json-inline.json`은 각각 `default.json`과 `inline.json`으로 옮깁니다. 기존 키에 쓰인 원문을 그대로 사용합니다.
- `php.json`과 `php-inline.json`은 각각 `default.json`과 `inline.json`으로 옮깁니다. `source/`의 PHP 파일을 참조해 중첩된 키를 대응하는 원문으로 바꿉니다.
- `_excludes.json`의 항목은 대상 로캘의 `_settings.json`에 있는 `excludes`로 옮깁니다.

Toolkit은 번역을 대상 사전에 병합합니다. 키가 겹치면 마이그레이션한 번역이 우선합니다. 두 이전 형식이 같은 문구를 제공하면 PHP 사전이 우선합니다.

Toolkit은 `source/`의 파일을 보존합니다. 마이그레이션에 성공한 이전 사전 및 제외 목록 파일은 별도의 대상을 지정한 경우에도 삭제됩니다. 애플리케이션 또는 플러그인 매니페스트 변경 사항은 [업그레이드 가이드](./upgrade.md)를 참조하세요.

## ZIP 아카이브에서 소스 가져오기 {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory`는 최종 대상을 지정합니다. 아카이브 루트를 기준으로 디렉터리를 지정하며 `--paths`를 여러 번 전달할 수 있습니다.
- 선택한 PHP 및 JSON 파일은 파일 이름을 유지한 채 대상으로 복사됩니다. PHP 파일은 바이트 단위로 복사합니다. 이름이 같은 JSON 파일은 병합하고 새로 가져온 값이 우선합니다.
- `--only-copy`가 없으면 Toolkit은 리터럴 키가 있는 지원 대상 번역 호출을 검색합니다. 문구를 찾으면 예제의 `framework.json`처럼 대상의 상위 디렉터리 이름을 딴 JSON 소스를 기록합니다.
- 이 명령에는 PHP ZIP 확장이 필요합니다. 게시 전에 변경 사항을 임시로 준비하고, 게시에 실패하면 이전 대상의 복원을 시도합니다.

`--url`과 `--directory`는 필수입니다. `--paths` 디렉터리는 재귀적으로 검색합니다. 번역 키만 찾으려면 `--paths`를 생략하세요. 찾은 키를 팩에 추가하기 전에 검토하세요.

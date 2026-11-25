---
title: 기여하기
description: "Mona를 체크아웃해 번역, PHP 패키지, 문서에 기여합니다."
---

[Mona 저장소](https://github.com/Laravel-Lang/mona)를 포크하고 원본의 `main` 브랜치로 풀 리퀘스트를 엽니다. Mona는 소스 템플릿, 공용 번역, PHP 패키지와 문서를 관리합니다. 개별 패키지 저장소는 Mona에서 내용을 내보내 채웁니다.

## 번역 수정 또는 추가 {#fix-or-add-a-translation}

1. 팩의 `src/<Package>/source/` 디렉터리에서 영어 문구를 찾습니다. Laravel Framework와 Laravel 공식 패키지의 소스는 `src/Lang/source/`에 있습니다. 팩의 `laravel_lang.json`에는 상위 패키지 버전별로 사용하는 소스 파일이 나열됩니다.
2. `src/Library/locales/<locale>/default.json`을 엽니다. 영어 문구와 일치하는 JSON 키를 찾고 값을 수정합니다. 원문이 팩에 있지만 사전에 없으면 항목을 추가하세요.
3. 원문을 그대로 두고 `:count`, `:other`, `:attribute` 같은 자리표시자를 보존합니다. 인라인 유효성 검사 메시지는 `inline.json`의 동일한 원문 키에 필드 이름을 쓰지 않는 표현으로 작성하세요. 나머지 자리표시자와 복수형도 보존합니다.
4. 번역문을 의도적으로 영어 원문과 같게 두었다면 해당 로캘의 `_settings.json`에 있는 `excludes`에 문구를 추가합니다. Toolkit은 번역 상태를 생성할 때 이 목록을 사용합니다.
5. 제목에 로캘 코드가 있는 풀 리퀘스트를 엽니다. 예시는 `[fr]: Updated validation messages`입니다.

상위 프로젝트의 원문에 오타가 있으면 원문과 조회 키를 함께 수정할 수 있도록 그 프로젝트에 신고하세요.

다음은 `src/Library/locales/fr/default.json`에 있는 프랑스어 기본 항목의 예입니다.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

`src/Library/locales/fr/inline.json`의 대응하는 인라인 항목은 아래와 같습니다.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Mona에서 배포하는 번역은 Library의 `default.json`과 `inline.json`에서 수정합니다. 일부 팩에는 이전 형식인 `php.json`과 `json.json` 사전이 남아 있으며, 팩의 번역 상태를 집계하는 자동 워크플로에서도 사용합니다.

국가, 통화 또는 로캘의 이름을 바꾸려면 해당 패키지의 `data/` 디렉터리에 있는 사전을 수정하세요. [로캘과 번역된 이름](./locales.md)을 참조하세요.

## 로캘 제안하기 {#propose-a-locale}

[표준 로캘 코드](./available-locales.md)를 확인하세요. 목록에 코드가 있으면 `src/Library/locales/`에서 사전을 추가하거나 업데이트합니다. Toolkit으로 [누락된 디렉터리를 생성](./toolkit.md#manage-locale-directories)할 수 있습니다. 새 코드는 [Laravel-Lang 토론](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization)에서 제안하세요.

## 패키지 개발 {#develop-packages}

Git, Composer 2, PHP `^8.3`과 `intl`, `json`, `zip` 확장을 사용합니다. 인메모리 데이터베이스를 사용하는 테스트를 위해 `pdo_sqlite`를 활성화합니다. 저장소 루트에서 명령을 실행하세요.

```bash
composer install
composer check-platform-reqs
```

루트 Composer 프로젝트는 `src/<Package>/src/`의 코드를 자동으로 로드합니다. 배포할 패키지의 의존성은 해당 `src/<Package>/composer.json`에 선언합니다. 내부 의존성의 `self.version` 제약을 통해 같은 릴리스의 패키지를 함께 설치할 수 있습니다.

패키지 동작을 로컬에서 확인하려면 `workbench/`의 Testbench 애플리케이션을 사용합니다. `composer serve`는 애플리케이션을 빌드하고 개발 서버를 시작합니다. 저장소 루트에서 라우트와 Artisan 명령을 보려면 Testbench CLI를 사용하세요.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

의존성 CI는 다른 로컬 패키지를 가리키는 Composer `path` 저장소를 사용해 각 패키지를 별도로 설치합니다. 패키지 의존성을 변경한 경우 개별 설치를 확인하고 루트 테스트 모음도 실행합니다. 설정과 Laravel 버전 구성은 워크플로에 있습니다. [의존성 테스트 워크플로](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## 로컬 검사 실행하기 {#run-local-checks}

사전을 변경했다면 루트에서 JavaScript 의존성을 설치하고 Biome 검사를 실행합니다. Toolkit의 `status` 명령은 저장된 값이 소스 키와 같은 항목을 보고합니다. 사전에 없는 문구는 팩의 소스 파일에서 확인하고 생성된 보고서는 `git status --short`로 검토하세요. [Toolkit 명령](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

PHP 변경 사항은 저장소 루트에서 Pest와 PHPStan으로 검사합니다. 모델 생성 테스트가 Testbench 애플리케이션 디렉터리를 공유하므로 기능 테스트는 CI와 같이 순차 실행합니다. 아키텍처 테스트는 병렬 실행할 수 있습니다. 한 기능에 집중하려면 테스트 파일이나 디렉터리를 Pest에 전달합니다.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Testbench 개발 설정으로 인해 라우트 테스트가 `http://localhost:8000` URL을 생성한다면 Pest를 실행하기 전에 테스트 프로세스의 `APP_URL`을 `http://localhost`로 설정하세요.

`composer style`은 Composer 파일을 정규화하고 Rector와 Pint의 수정 사항을 적용합니다. 풀 리퀘스트를 제출하기 전에 변경 내용을 확인합니다. 줄 커버리지를 확인하려면 Xdebug를 활성화하고 `composer run-script --timeout=0 test:coverage`를 실행합니다.

매니페스트 변경 사항은 CI와 동일한 스키마 검사로 확인합니다.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

매니페스트 형식은 [번역 플러그인](./plugins.md)을 참조하세요.

## 문서 수정 {#edit-the-documentation}

현재 버전의 영어 문서는 `docs/docs/`에 있고 번역은 `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`에 있습니다. 지침이 바뀌면 해당 번역도 갱신하고 언어 간 링크를 위해 제목 ID를 유지하세요. Mona 이전 문서는 `docs/versioned_docs/version-legacy/`에 보관하며, 번역은 `docs/i18n/` 아래의 해당 `version-legacy/` 디렉터리에 있습니다.

`docs/package.json`에 지정된 Node.js 버전을 사용하세요. 문서 관련 명령은 `docs/`에서 실행합니다.

```bash
cd docs
npm ci
npm start
```

한 언어를 미리 보려면 `npm start -- --locale fr`을 실행합니다. 개발 서버를 종료한 다음 프로덕션 빌드를 실행하여 모든 언어의 링크, 앵커, 검색 인덱스를 확인하세요.

```bash
npm run typecheck
npm run build
npm run serve
```

`serve`에 표시된 주소를 열어 검색과 생성된 Markdown을 확인합니다. 정적 사이트는 `docs/build/`에 생성됩니다.

## 릴리스 게시 {#publish-a-release}

`main`에 병합하면 패키지의 하위 트리가 각 저장소의 `main` 브랜치로 내보내지고 Mona 릴리스 초안이 갱신됩니다. 초안을 검토하고 공통 버전 태그로 Mona 릴리스를 게시합니다. 태그 생성만으로는 릴리스 워크플로가 실행되지 않습니다.

릴리스를 게시하면 태그를 내보내고 각 패키지 저장소에 해당 릴리스를 생성합니다. 문서 사이트 빌드와 배포도 시작됩니다. 게시 후 두 워크플로를 확인하세요. 미리 패키지 분할 결과를 보려면 `main`에서 **Export Packages**를 수동 실행하고 `dry-run`을 활성화합니다. [패키지 내보내기](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [문서](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

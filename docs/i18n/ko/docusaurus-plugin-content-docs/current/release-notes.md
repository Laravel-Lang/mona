---
title: 릴리스 노트
description: Laravel-Lang의 버전 관리와 플랫폼 호환성을 확인합니다.
---

## 버전 관리와 호환성 {#versioning-and-compatibility}

Laravel-Lang은 시맨틱 버전 관리를 따릅니다. 호환성이 깨지는 변경 사항과 플랫폼 지원 종료는 메이저 릴리스에 적용합니다. 공식 패키지는 동일한 릴리스 버전을 사용합니다. [시맨틱 버전 관리](https://semver.org/)

현재 버전은 PHP `^8.3`이 필요하며 Laravel 12와 13을 지원합니다. 사용하는 Laravel 버전에는 더 최신 PHP 버전이 필요할 수 있습니다. Common은 의존성을 통해 `intl`과 `json` 확장도 필요로 합니다.

릴리스 태그와 패치 내역은 [Mona 릴리스](https://github.com/Laravel-Lang/mona/releases)에서 확인할 수 있습니다. Laravel의 유지보수 일정은 [Laravel 지원 정책](https://laravel.com/docs/releases#support-policy)을 참조하세요.

## 패키지 공개 여부 {#package-availability}

Mona의 `main`에 푸시하면 내보내기 워크플로가 각 패키지 저장소의 `main` 브랜치를 업데이트합니다. Mona 릴리스를 게시하면 같은 태그를 각 패키지 저장소로 내보냅니다.

`20.x`로 설치하거나 업그레이드하기 전에 필요한 Composer 패키지 버전이 Packagist에 등록되었는지 확인하세요. Mona 릴리스와 그에 해당하는 Composer 패키지가 공개되는 시점은 다를 수 있습니다. [패키지 내보내기 실행 기록](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

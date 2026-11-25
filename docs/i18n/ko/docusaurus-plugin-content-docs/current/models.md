---
title: 모델 번역
description: Laravel-Lang Models로 현지화된 Eloquent 속성을 저장하고 읽고 쿼리합니다.
---

`laravel-lang/models`는 기존 Eloquent 모델에 번역 가능한 속성을 추가합니다. 각 번역 행은 로캘과 해당 로캘의 속성값을 저장합니다. 이 패키지는 Common에 포함되며 PHP Intl이 필요합니다.

```bash
composer require laravel-lang/models
```

## 모델에 번역 추가하기 {#add-translations-to-a-model}

기본 테이블 마이그레이션이 있는 기존 `App\Models\Article` 모델에는 다음 명령을 실행하세요.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

생성기는 기본 모델에 `LaravelLang\Models\HasTranslations` 트레이트를 추가하고, 대응하는 `ArticleTranslation` 모델, 마이그레이션, IDE 헬퍼를 생성합니다. 마이그레이션은 게시글을 참조하는 외래 키, 고유한 `(item_id, locale)` 쌍, 번역값을 담는 널 허용 문자열 열을 만듭니다. 이미 기본 테이블에 이 값을 저장했다면 새 모델의 접근자를 사용하기 전에 값을 번역 행으로 마이그레이션하세요.

모델이 `app/Models` 밖에 있다면 생성기를 실행하기 전에 그 디렉터리를 [`localization_models.directories`](./configure.md)에 추가합니다.

`php artisan make:model-translation`을 실행해 모델과 열을 대화형으로 지정하세요. 열 목록을 비우면 `title`과 `description`을 사용합니다. 나중에 필드를 추가하려면 마이그레이션을 만들고 번역 모델의 할당 가능 속성과 캐스트에 필드를 추가하세요.

## 번역된 속성 쓰기 및 읽기 {#write-and-read-translated-attributes}

[프랑스어를 설치](./translations.md)한 뒤 표준 코드, 별칭, `Locale` 열거형 케이스 또는 `LocaleData`를 전달해 번역을 저장할 수 있습니다. 변경 사항은 기본 모델의 `save()`로 저장하세요.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // 현재 로캘의 값이 null이면 대체 로캘을 사용합니다.
$article->getTranslation('title', 'fr');  // 'Premier article'
```

로캘을 명시하면 해당 로캘의 값을 읽습니다. 로캘 인수가 없으면 현재 로캘에 쓰고, 읽을 때 현재 값이 `null`이면 Laravel에 설정된 대체 로캘을 사용합니다. 별칭은 조회와 저장 시 모두 표준 코드로 변환됩니다. 알 수 없거나 설치되지 않은 로캘을 명시하면 `LaravelLang\Exceptions\UnknownLocaleException`이 발생합니다.

한 필드에 여러 값을 설정할 때는 `setTranslations()`를 사용하세요. 각 번역 필드에 대해 로캘을 키로 하는 매핑을 `create()`나 `fill()`에 전달할 수도 있습니다. 예시는 영어와 프랑스어가 설치되어 있다고 가정합니다.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

`isTranslatable('title')`은 모델에 설정된 필드를 확인하고, `hasTranslated('title', 'fr')`은 채워진 값이 있는지 확인합니다. `forgetTranslation('fr')`은 프랑스어 행을 삭제하고, `forgetAllTranslations()`는 모델의 모든 번역 행을 삭제합니다.

## 번역 쿼리하기 {#query-translations}

`translated()`, `whereTranslation()`, `whereTranslationLike()`는 `translations()` 관계를 통해 쿼리합니다. 이 관계는 기본적으로 현재 및 대체 로캘의 행으로 제한됩니다. 이 스코프로 다른 로캘을 조회하려면 `localization_models.filter`를 `false`로 설정하세요. 한 모델의 모든 행을 읽으려면 `translationsRaw()`를 사용합니다.

현재 로캘이 프랑스어라면 다음과 같이 프랑스어 제목으로 조회하고 정렬할 수 있습니다.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

모델은 `translations()` 관계를 미리 로드합니다. `orWhereTranslation()`과 `orWhereTranslationLike()`는 대체 조건을 추가합니다. `orderByTranslation()`은 하나의 로캘을 기준으로 정렬하며 기본값은 현재 로캘입니다.

## 변경 감지 및 IDE 헬퍼 갱신하기 {#observe-changes-and-refresh-ide-helpers}

`LaravelLang\Models\Events` 네임스페이스에는 `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent`, `AllTranslationsHasBeenForgetEvent`가 있습니다. 값 설정 이벤트에는 모델, 열, 선택적 로캘 별칭, 이전 값과 새 값이 들어 있습니다. 이 이벤트는 후속 저장이 이루어지기 전, 속성에 값을 할당할 때 발생합니다.

발견된 모든 모델의 헬퍼를 다시 만들려면 `php artisan lang:models-helper`를 실행하세요. 기본 디렉터리는 `vendor/_laravel_lang`이며, `localization_models.helpers`로 설정할 수 있습니다.

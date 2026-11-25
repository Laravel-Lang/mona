---
title: Переводы моделей
description: Сохраняйте, читайте и запрашивайте локализованные атрибуты Eloquent с Laravel-Lang Models.
---

`laravel-lang/models` добавляет переводимые атрибуты в существующую модель Eloquent. Каждая строка перевода хранит локаль и значения атрибутов для неё. Пакет входит в Common и требует расширение Intl для PHP.

```bash
composer require laravel-lang/models
```

## Добавление переводов в модель {#add-translations-to-a-model}

Если модель `App\Models\Article` и миграция её основной таблицы уже есть, выполните команду:

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Генератор добавляет трейт `LaravelLang\Models\HasTranslations` в основную модель и создаёт модель `ArticleTranslation`, миграцию и вспомогательный файл для IDE. Миграция создаёт внешний ключ на статью, уникальную пару `(item_id, locale)` и строковые столбцы для переводимых значений, допускающие `null`. Если значения уже хранятся в основной таблице, перенесите их в строки переводов прежде, чем использовать методы доступа новой модели.

Если модель находится за пределами `app/Models`, добавьте её каталог в [`localization_models.directories`](./configure.md) перед запуском генератора.

Запустите `php artisan make:model-translation`, чтобы выбрать модель и столбцы в интерактивном режиме. Если оставить список пустым, генератор использует `title` и `description`. Чтобы добавить поле позже, создайте миграцию и добавьте поле в заполняемые атрибуты и преобразования модели перевода.

## Чтение и запись переводимых атрибутов {#write-and-read-translated-attributes}

Когда [французская локаль установлена](./translations.md), можно сохранить перевод, передав её канонический код, алиас, элемент enum `Locale` или объект `LocaleData`. Для записи изменений вызовите `save()` у основной модели.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Если явно передать локаль, метод прочитает значение для неё. Без указания локали запись идёт в текущую локаль, а чтение обращается к настроенной в Laravel резервной локали, если текущее значение равно `null`. При поиске и сохранении алиасы преобразуются в канонические коды. Явно переданная неизвестная или неустановленная локаль вызывает `LaravelLang\Exceptions\UnknownLocaleException`.

Используйте `setTranslations()`, чтобы задать несколько переводов одного поля. В `create()` или `fill()` также можно передать массив с локалями в качестве ключей для каждого переводимого поля. В примере предполагается, что английская и французская локали установлены.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Чтобы проверить, настроено ли поле как переводимое, используйте `isTranslatable('title')`. Чтобы проверить, заполнен ли перевод, используйте `hasTranslated('title', 'fr')`. `forgetTranslation('fr')` удаляет строку французского перевода, а `forgetAllTranslations()` — все строки переводов модели.

## Запросы переводов {#query-translations}

`translated()`, `whereTranslation()` и `whereTranslationLike()` выполняют запросы через связь `translations()`. По умолчанию эта связь выбирает только строки текущей и резервной локалей. Чтобы запрашивать другие локали через эти области запросов, задайте для `localization_models.filter` значение `false`. Чтобы прочитать все строки одной модели, используйте `translationsRaw()`.

Когда текущая локаль — французская, отберите и отсортируйте записи по французскому заголовку:

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Модели заранее загружают связь `translations()`. Методы `orWhereTranslation()` и `orWhereTranslationLike()` добавляют альтернативное условие. `orderByTranslation()` сортирует по одной локали, по умолчанию по текущей.

## Отслеживание изменений и обновление файлов для IDE {#observe-changes-and-refresh-ide-helpers}

В пространстве имён `LaravelLang\Models\Events` есть события `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` и `AllTranslationsHasBeenForgetEvent`. Событие установки перевода содержит модель, столбец, необязательный алиас локализации, старое и новое значения. Оно срабатывает при присвоении атрибута перед последующим сохранением.

Выполните `php artisan lang:models-helper`, чтобы обновить вспомогательные файлы для всех обнаруженных моделей. Их каталог по умолчанию — `vendor/_laravel_lang`. Его можно изменить в `localization_models.helpers`.

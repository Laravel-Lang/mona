---
title: Переклади моделей
description: Зберігайте, читайте й запитуйте локалізовані атрибути Eloquent за допомогою Laravel-Lang Models.
---

Пакет `laravel-lang/models` додає перекладені атрибути до наявної моделі Eloquent. Кожен рядок перекладу містить локаль і значення атрибутів для неї. Пакет входить до Common і потребує розширення PHP Intl.

```bash
composer require laravel-lang/models
```

## Додавання перекладів до моделі {#add-translations-to-a-model}

Для наявної моделі `App\Models\Article` з міграцією базової таблиці виконайте таку команду.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Генератор додає трейт `LaravelLang\Models\HasTranslations` до базової моделі й створює супровідну модель `ArticleTranslation`, міграцію та допоміжний файл для IDE. Міграція створює зовнішній ключ до статті, унікальну пару `(item_id, locale)` та рядкові стовпці, які допускають `null`, для перекладених значень. Якщо ви вже зберігаєте ці значення в базовій таблиці, перенесіть їх у рядки перекладів, перш ніж користуватися аксесорами нової моделі.

Якщо модель розташована поза `app/Models`, додайте її каталог до [`localization_models.directories`](./configure.md) перед запуском генератора.

Запустіть `php artisan make:model-translation`, щоб інтерактивно вибрати модель і стовпці. Якщо лишити список порожнім, генератор використає `title` і `description`. Щоб додати поле згодом, створіть міграцію та додайте поле до заповнюваних атрибутів і перетворень моделі перекладу.

## Запис і читання перекладених атрибутів {#write-and-read-translated-attributes}

Після [встановлення французької локалі](./translations.md) можна записати переклад, передавши її канонічний код, аліас, елемент enum-а `Locale` або `LocaleData`. Викличте `save()` базової моделі, щоб зберегти зміни.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // Поточна локаль, а для значення null — резервна.
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Якщо вказано локаль, метод читає її значення. Без аргументу локалі запис використовує поточну локаль, а під час читання, якщо поточне значення дорівнює `null`, використовується налаштована резервна локаль Laravel. Під час пошуку та збереження аліаси перетворюються на канонічні коди. Явно вказана невідома або невстановлена локаль спричиняє виняток `LaravelLang\Exceptions\UnknownLocaleException`.

Використовуйте `setTranslations()` для кількох значень одного поля. Також можна передати до `create()` або `fill()` мапу з ключами локалей для кожного перекладеного поля. У прикладі англійську та французьку локалі вже встановлено.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Використовуйте `isTranslatable('title')`, щоб перевірити налаштовані поля моделі, а `hasTranslated('title', 'fr')` — щоб перевірити наявність значення. Метод `forgetTranslation('fr')` видаляє французький рядок, а `forgetAllTranslations()` — усі рядки моделі.

## Запити до перекладів {#query-translations}

Методи `translated()`, `whereTranslation()` і `whereTranslationLike()` виконують запити через зв'язок `translations()`. За замовчуванням він відфільтровує рядки за поточною та резервною локалями. Щоб виконувати запити до інших локалей через ці області запиту, установіть `localization_models.filter` у `false`. Щоб прочитати всі рядки окремої моделі, використовуйте `translationsRaw()`.

Коли поточною є французька локаль, виконайте запит і відсортуйте результати за французьким заголовком.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Моделі завчасно завантажують зв’язок `translations()`. `orWhereTranslation()` і `orWhereTranslationLike()` додають альтернативні умови. `orderByTranslation()` сортує за однією локаллю, типово за поточною.

## Відстеження змін і оновлення допоміжних файлів IDE {#observe-changes-and-refresh-ide-helpers}

Простір імен `LaravelLang\Models\Events` містить події `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` і `AllTranslationsHasBeenForgetEvent`. Подія встановлення перекладу містить модель, стовпець, необов'язковий аліас локалі, попереднє та нове значення. Вона спрацьовує під час присвоєння атрибута перед наступним збереженням.

Виконайте `php artisan lang:models-helper`, щоб згенерувати заново допоміжні файли для всіх знайдених моделей. Їхній типовий каталог — `vendor/_laravel_lang`. Його можна змінити через `localization_models.helpers`.

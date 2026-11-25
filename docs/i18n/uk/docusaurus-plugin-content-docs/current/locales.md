---
title: Локалі й назви
description: Знайдіть підтримувані та встановлені локалі, перетворюйте аліаси та отримуйте локалізовані назви.
---

Laravel-Lang використовує канонічні коди, як-от `en`, `fr` та `pt_BR`, у Laravel, назвах файлів перекладів і рядках перекладів моделей. Аліас локалі — це назва для використання в застосунку, наприклад `pt-br` в URL. Задайте відповідність між канонічним кодом і аліасом у [`localization_main.aliases`](./configure.md).

## Пошук підтримуваних і встановлених локалей {#find-supported-and-installed-locales}

Пакет `laravel-lang/locale-list` надає enum зі скалярними значеннями `LaravelLang\LocaleList\Locale`. Елементи enum-а відповідають [підтримуваним канонічним кодам](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // Масив канонічних кодів.
Locale::collection();        // Колекція канонічних кодів.
Locale::collection(false);   // Колекція елементів enum-а.
```

Пакет `laravel-lang/locales` додає фасад `LaravelLang\Locales\Facades\Locales`, який враховує налаштування застосунку. Ці методи повертають колекції рядків. Передайте `false` як аргумент, щоб отримати канонічні коди. За замовчуванням кожен код представлено налаштованим аліасом, якщо він існує.

| Метод | Що повертає |
| --- | --- |
| `Locales::available()` | Усі відомі локалі |
| `Locales::installed()` | Локалі, розпізнані за мовними файлами застосунку, і захищені локалі |
| `Locales::uninstalled()` | Доступні локалі, які не встановлено |
| `Locales::protects()` | Фактичну поточну та резервну локалі без дублікатів |

Пошук охоплює записи безпосередньо в `lang_path()` та файли JSON за шаблоном `vendor/*/*.json` відносно цього шляху. Назва файла або каталогу має відповідати підтримуваному коду. Каталоги в `lang/vendor/<package>/`, які містять лише переклади PHP, не підпадають під ці правила пошуку. Як опублікувати локаль, описано в розділі [Керування перекладами](./translations.md).

## Отримання та встановлення локалі застосунку {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

Метод `set()` приймає код, аліас, елемент enum-а `Locale` або об'єкт `LocaleData`. Якщо локаль установлено, він задає її канонічний код як локаль Laravel. Використовуйте `isAvailable()` для перевірки підтримки коду, а `isInstalled()`, коли потрібна наявність файлів або захисту. Метод `isProtected()` визначає, чи є локаль поточною або резервною.

Метод `current()` повертає поточну локаль Laravel, якщо її розпізнано, інакше викликає `fallback()`. Метод `fallback()` визначає налаштовану в Laravel резервну локаль і повертає `en`, якщо вона невідома. Обидва методи приймають аргумент `false` для отримання канонічних кодів.

Використовуйте `fromAlias()`, щоб отримати канонічний код за аліасом, і `toAlias()`, щоб отримати аліас за канонічним кодом. Ці перетворення залишають нерозпізнані непорожні рядки без змін. Для перевірки вхідних даних скористайтеся `isAvailable()`.

## Показ варіантів локалі {#display-locale-choices}

Метод `Locales::info('fr')` повертає об'єкт `LaravelLang\LocaleNames\LocaleData` з властивостями `code`, `alias`, `native` та `localized`. Мовою відображення є поточна локаль застосунку. Щоб отримати дані поточної локалі, не передавайте аргумент. Дані відомої локалі можна отримати до її встановлення. Невідомий код спричиняє виняток `LaravelLang\Exceptions\UnknownLocaleException`.

Використовуйте `Locales::data()->installed()`, щоб отримати встановлені локалі як об’єкти `LocaleData`. `data()` також надає `available()`, `uninstalled()` і `protects()` для інших колекцій, а також `current()` і `fallback()` для окремих об’єктів. У списку вибору мови можна передавати `alias` як значення й показувати назву `native`.

## Пошук назв локалей, країн і валют {#look-up-locale-country-and-currency-names}

Пакети `laravel-lang/locale-names`, `laravel-lang/country-names` і `laravel-lang/currency-names` надають по одному сервісу, який можна впровадити через контейнер Laravel. Усі три пакети входять до Common.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

Перший параметр задає мову відображення. Передайте `null`, щоб отримати назви мовою відповідної локалі. Другий параметр `one()` — локаль рядка, наприклад `fr` у наведених прикладах. Під час пошуку назв країн і валют код ISO міститься в поверненому об'єкті. Обидва параметри приймають канонічні коди, налаштовані аліаси й елементи enum-а `Locale`.

Метод `all($forLocale)` повертає колекцію з канонічними кодами локалей як ключами. Передайте `SortDirection::Ascending` або `SortDirection::Descending`, щоб відсортувати за властивістю `localized`. Кожне значення — об'єкт лише для читання. Значення для країн і валют також містять `locale` відповідного рядка.

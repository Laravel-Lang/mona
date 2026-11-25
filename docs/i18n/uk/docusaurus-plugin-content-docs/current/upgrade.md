---
title: Апгрейд до Mona
description: Оновіть залежності Laravel-Lang, конфігурацію, опубліковані переклади й інтеграції застосунку.
---

Mona — репозиторій, у якому пакети Laravel-Lang публікуються разом. Продовжуйте використовувати окремі пакети Composer, які потрібні вашому застосунку.

:::info[Чому версія 20?]

Екосистема Laravel-Lang виросла з `laravel-lang/lang`. Пакети з’являлися в різний час, і кожен мав власну нумерацію та графік випусків. Під час мажорних оновлень нам доводилося узгоджувати зміни в кількох пакетах, а користувачам — з’ясовувати, які версії можна встановити разом.

Щоб спростити вибір, ми створили `laravel-lang/common`, який задавав сумісні версії залежностей. Однак одне оновлення й далі могло зачіпати кілька пакетів із власною нумерацією.

Ось як виглядали версії пакетів до переходу на Mona. Які з них ви б установили для Laravel 11 із PHP 8.2?

<details>
<summary>Версії пакетів до переходу на Mona</summary>

| Пакет | Версія |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (версія гілки 1.x, яку й далі використовували інші пакети Laravel-Lang) і `2.3.2` (остання стабільна версія до Mona, яку ці пакети використовували рідко) |
| Publisher | `16.9.0` |

</details>

З монорепозиторію Mona ми випускаємо пакети разом, під одним номером версії. Найвищий номер раніше був у Publisher, тож наступною могла б стати версія `17.0`. Ми вклали багато праці й душі в об’єднання екосистеми й обрали версію 20, щоб відзначити цей етап. Її також можна прочитати як **Laravel-Lang 2.0**. 🙂

:::

:::info[Коди й аліаси локалей]

Laravel-Lang тепер використовує канонічні коди локалей для збережених значень, зокрема шляхів опублікованих мовних файлів і рядків перекладів моделей. Поточна й резервна локалі Laravel також використовують канонічні коди. Під час роботи застосунку використовуйте аліаси для сегментів URL і перемикачів мови. Методи фасаду, що повертають коди локалей, типово повертають налаштований аліас, а за його відсутності — канонічний код. Наприклад, налаштуйте `'pt_BR' => 'pt-br'` у `localization_main.aliases`, щоб зберегти `/pt-br/account`, публікувати переклади в `lang/pt_BR.json` і зберігати `pt_BR` у базі даних.

:::

## Оновлення залежностей {#updating-dependencies}

Поточна версія підтримує Laravel 12 і 13 та потребує PHP `^8.3`. У `composer.json` застосунку задайте обмеження `^20.0` для кожного пакета нижче, який є в `require` або `require-dev`. Якщо ви встановили лише `laravel-lang/common`, достатньо змінити цю одну залежність.

Перед встановленням `20.x` або оновленням до цієї версії перевірте, чи потрібна версія пакета Composer доступна на Packagist. Випуск Mona та відповідні пакети Composer можуть стати доступними в різний час.

| Призначення | Пакети Composer |
| --- | --- |
| Збірка пакетів | `laravel-lang/common` |
| Пакети перекладів | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Публікування й налаштування | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Інтеграції застосунку | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Спільні утиліти | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Шаблон пакета | `laravel-lang/translations-template` |

Якщо інсталяція використовує лише стандартний процес перекладу, оновіть залежності та опублікуйте переклади ще раз.

```bash
composer update
php artisan lang:update
```

Якщо ви реєстрували сервіс-провайдерів, публікували власні налаштування, використовуєте аліаси або сторонні пакети перекладів чи викликаєте API пакетів у застосунку, виконайте відповідні нижченаведені кроки перед повторним публікуванням.

## Заміна перейменованих або застарілих залежностей {#replace-renamed-or-retired-dependencies}

Оновіть усі прямі залежності від старих пакетів назв у `composer.json`, перш ніж виконати `composer update`.

| Попередній пакет Composer | Залежність версії `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common уже залежить від усіх трьох пакетів назв. Оновіть також код PHP. Див. [заміну API пошуку назв](#replace-the-name-lookup-apis).

Перевірте, чи має кожен сторонній плагін реліз, сумісний із новим Publisher. Якщо ви використовуєте `laravel-lang/translator` або `laravel-lang/status-generator`, виконайте міграцію перед вилученням цих залежностей. Див. [інструменти перекладу](#update-json-fallback-and-translation-tooling).

## Оновлення сервіс-провайдерів {#update-service-providers}

Laravel знаходить провайдерів Config, Publisher, Routes і Models за маніфестами пакетів. Якщо ви покладаєтеся на автоматичний пошук, оновіть посилання на старі класи провайдерів у коді й тестах. Якщо застосунок реєструє пакети вручну, виконайте наведені нижче заміни в тому самому списку провайдерів, наприклад у `bootstrap/providers.php` або `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Видаліть ручну реєстрацію `LaravelLang\Locales\LaravelLangLocalesServiceProvider` і старих класів `ServiceProvider` у `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` та `LaravelLang\StarterKits`. Ці пакети перекладів знаходяться за маніфестами.

Якщо ви використовуєте резервні переклади JSON, замініть `LaravelLang\JsonFallback\TranslationServiceProvider` на `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` у місці реєстрації. Це збереже перевизначення провайдера перекладів Laravel.

Оновіть застарілі посилання на провайдерів перед `composer update`, щоб під час пошуку пакетів застосунок міг запуститися. Якщо сценарій Composer досі завершується помилкою через старий клас, виконайте `composer update --no-scripts`, оновіть посилання й виконайте `composer dump-autoload`, щоб повторити пошук.

## Міграція налаштувань {#migrate-configuration}

Якщо ви змінювали `config/localization.php`, збережіть його для звірки під час перенесення змін. Пакети автоматично завантажують нові типові налаштування. Опублікуйте [файли конфігурації](./configure.md), які потрібно змінити. Тег `localization` публікує всі п'ять файлів. Для публікування одного файла використовуйте його назву без `.php` як тег.

```bash
php artisan vendor:publish --tag=localization
```

### Аліаси, стилі перекладу й форматування файлів {#aliases-translation-styles-and-file-formatting}

- Скопіюйте мапу `aliases` до `config/localization_main.php`. Залиште канонічні коди ключами, а аліаси — значеннями, наприклад `'de_CH' => 'de-CH'`.
- Замініть `inline` на `styles` у `config/localization_main.php`. Укажіть `'styles' => ['default']`, щоб зберегти попередню типову поведінку зі стандартними повідомленнями перевірки. Якщо ви використовували повідомлення без назви поля, залиште нове типове значення `['default', 'inline']`. Видаліть `LOCALIZATION_INLINE` і `LANG_PUBLISHER_INLINE` із середовищ, де ви їх установили.
- Видаліть ключ `align` і змінні середовища `LOCALIZATION_ALIGN` і `LANG_PUBLISHER_ALIGN`. Щоб вирівняти оператори `=>` в опублікованих масивах PHP, налаштуйте форматувальник PHP вашого проєкту й запустіть його для мовних файлів після публікування. Наприклад, правило `binary_operator_spaces` у Laravel Pint може застосувати `align_single_space_minimal` до `=>`.

### Автоматична типографіка {#smart-punctuation}

Якщо ви ввімкнули автоматичну типографіку, перейменуйте `LOCALIZATION_SMART_ENABLED` на `LOCALIZATION_PUNCTUATION_ENABLED` і залиште значення `true`. Якщо ви задавали `smart_punctuation.enable` безпосередньо в PHP, перенесіть булеве значення до ключа `enabled` верхнього рівня у `config/localization_punctuation.php`. Типово автоматичну типографіку вимкнено.

Перенесіть власні лапки зі `smart_punctuation.common` і `smart_punctuation.locales` до масивів `common` і `locales` верхнього рівня в цьому файлі. Використовуйте канонічні коди як ключі `locales`. Лапки кожної локалі перевизначають відповідні ключі `common`. Якщо ви скопіювали старий французький приклад із `&nbsp;`, використовуйте у рядках лапок справжній нерозривний пробіл або `"\u{00A0}"`.

### Локалізовані маршрути {#localized-routes}

Перенесіть власні налаштування з `routes` у старій конфігурації до `config/localization_routes.php`.

| Що було змінено | Зміна у `config/localization_routes.php` | Змінна середовища для перейменування |
| --- | --- | --- |
| Параметр локалі, заголовок, cookie, ключ сеансу або стовпець моделі користувача | Скопіюйте власні налаштування `names` і залиште решту типових записів. | |
| Префікс назв локалізованих маршрутів | Перенесіть `name_prefix` до `prefix`. Якщо значення порожнє, використовується `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Перенаправлення з маршруту з префіксом | Перенесіть `redirect_default` до `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Чи приховує допоміжна функція URL резервну локаль | Перенесіть `hide_default` до `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Мідлвари для груп локалізованих маршрутів | Перенесіть `group.middlewares.default` до `middlewares.default` для маршрутів без префікса, а `group.middlewares.prefix` — до `middlewares.prefix` для маршрутів із префіксом. Оновіть назви класів, як показано в [міграції маршрутів](#update-localized-routes-and-middleware). | |

### Переклади моделей {#model-translations}

Перенесіть власні налаштування з `models` до `config/localization_models.php`. Скопіюйте значення `suffix` і `helpers` на верхній рівень. Використовуйте булевий параметр `filter` замість старого ключа `filter.enabled`. Якщо ви задаєте фільтрування та шлях допоміжних файлів IDE через середовище, залиште `LOCALIZATION_FILTER_ENABLED` і `VENDOR_PATH`.

Команди моделей тепер типово сканують `app/Models`. Якщо ви покладалися на старе сканування всього `app/` або самостійно задавали `localization-private.models.directory`, додайте кожен потрібний каталог до масиву `directories`. Наприклад, `'directories' => [app_path()]` зберігає старе сканування.

### Налаштування, які використовує код застосунку {#configuration-used-by-application-code}

- Замініть читання й запис через `LaravelLang\Config\Facades\Config` викликами конфігурації Laravel із новими ключами. Наприклад, замініть `Config::shared()->models->filter->enabled` на `config('localization_models.filter')`. Для типізованого читання можна впровадити `LaravelLang\Config\Services\ModelLangConfig` і читати його властивість `filter`, або впровадити `RouteLangConfig` із того самого простору імен для налаштувань маршруту.
- Якщо код читає метадані локалі з `localization-private.map`, використовуйте `localization_map.locales`. Скопіюйте власні налаштування до масиву `locales` у `config/localization_map.php`.
- Замініть реєстрацію через `localization-private.plugins` і `localization-private.packages` на [маніфест плагіна](./plugins.md).
- Видаліть старий блок `translators` та всі старі налаштування `TRANSLATION_*`, які використовував лише `laravel-lang/translator`.

Після перенесення власних налаштувань і оновлення посилань у застосунку видаліть `config/localization.php` та виконайте `php artisan config:clear`.

## Перенесення мовних файлів на шляхи з канонічними кодами {#move-language-files-to-canonical-paths}

1. Укажіть канонічні коди в `APP_LOCALE` та `APP_FALLBACK_LOCALE`. Перенесіть аліаси до `localization_main.aliases`, щоб URL та введені значення локалей продовжили працювати.
2. Перейменуйте файли й каталоги з аліасами, наприклад `lang/pt-br.json` і `lang/pt-br/`, на `lang/pt_BR.json` і `lang/pt_BR/`. Перевірте наявність тих самих аліасів у `lang/vendor/<package>/`. Якщо обидві назви існують, об'єднайте вміст і вирішіть конфлікти власних перекладів, перш ніж видаляти шлях із аліасом.
3. Установіть кожну локаль, для якої є лише переклади PHP залежності, командою `php artisan lang:add <locale>`. Список установлених локалей враховує записи безпосередньо в мовному каталозі, файли JSON у `lang/vendor/<package>/` і фактичні поточну та резервну локалі.
4. Після міграції конфігурації та плагінів виконайте `php artisan lang:update` і перегляньте зміни. Publisher може зберегти ключі інтеграцій, які більше не публікують файл. Залиште ключі, що їх застосунок досі використовує, і видаліть застарілі після перевірки місць виклику. Як згенерувати файли заново або зберегти їх, див. у розділі [Скрипти публікування та власні переклади](#update-publication-scripts-and-keep-custom-translations).

## Оновлення інтеграцій {#update-integrations}

### Локалі {#locales}

Залиште імпорт `LaravelLang\Locales\Facades\Locales` та оновіть виклики відповідно до даних, які потрібні кожному місцю виклику.

| Попередній виклик | Заміна |
| --- | --- |
| `Locales::available()`, `installed()` або `protects()`, що повертають об'єкти | Викличте той самий метод на `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` або `protects()` | Викличте метод безпосередньо на `Locales`, щоб отримати колекцію рядків із аліасами. Для канонічних кодів передайте `false`, а якщо потрібен масив, викличте `->all()`. |
| `Locales::installed(false)` або `Locales::raw()->installed(false)` | `false` тепер вибирає канонічні коди. Поточна й резервна локалі включаються навіть без мовних файлів. Також замініть старий іменований аргумент `withProtects: false`. Щоб виключити ці локалі, використовуйте `Locales::installed(false)->diff(Locales::protects(false))->values()`. Для об'єктів скористайтеся `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` або `Locales::raw()->notInstalled()` | Використовуйте `Locales::data()->uninstalled()` для об'єктів або `Locales::uninstalled()` для рядків із аліасами. Для канонічних рядків передайте `false`. |
| `Locales::getCurrent()` або `Locales::getDefault()` | Використовуйте `Locales::data()->current()` для DTO або `Locales::current()` для рядка. Замініть `Locales::raw()->getDefault()` на `Locales::current()`. Якщо через `Locales::raw()->getCurrent()` ви отримували значення, яке зберігає Laravel, використовуйте `app()->getLocale()`. |
| `Locales::getFallback()` або `Locales::raw()->getFallback()` | Використовуйте `Locales::data()->fallback()` для DTO або `Locales::fallback()` для рядка. Для невідомої резервної локалі тепер використовується англійська. Для невідомої поточної локалі використовується фактична резервна. |
| `Locales::get($locale)` або `Locales::raw()->get($locale)` | Використовуйте `isInstalled()` і явно вибирайте резервну локаль. Для підтримуваної локалі, яка може бути не встановлена, викличте `isAvailable()`, а потім `info()`. |
| `Locales::info($locale)` або `Locales::raw()->info($locale)` | Використовуйте `Locales::info($locale)` і читайте `->alias` або `->code`, коли потрібен рядок. Для поточної локалі передайте `null`. Для невідомого чи порожнього коду опрацюйте `LaravelLang\Exceptions\UnknownLocaleException` або перевірте `isAvailable()` перед пошуком. |

Щоб зберегти попередню поведінку `get()`, коли для невстановлених або некоректних значень використовувалася поточна локаль, зробіть цей вибір у коді виклику.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Використовуйте для шляхів, рядків бази даних та інших збережених даних.
$alias = $locale->alias; // Використовуйте для URL і перемикачів мови.
```

Оновіть обробники запитів, які раніше передавали результат `raw()->get()` до `app()->setLocale()`. Після отримання значення запиту використовуйте `Locales::set($locale)`. Метод приймає коди встановлених локалей, налаштовані аліаси, елементи enum-а і новий DTO та задає в Laravel канонічний код. Для значення запиту `pt-br` Laravel отримує `pt_BR`, а `Locales::current()` повертає `pt-br`. Викликайте `Locales::current(false)` або `Locales::fallback(false)`, коли потрібні канонічні коди.

Для інших операцій збереження в застосунку перевірте значення через `isAvailable()` або `isInstalled()`, а потім отримайте канонічний код через `fromAlias()`. Щоб підготувати канонічний код для відображення, використовуйте `toAlias()`. Замість старого `Services\Resolver` використовуйте `acceptsInput()` для перевірки прийнятних типів PHP або `normalize()` для отримання рядка з типізованого значення. `normalize()` зберігає рядки як передано, зокрема аліаси на кшталт `pt-br`.

Замініть `LaravelLang\Locales\Data\LocaleData` на `LaravelLang\LocaleNames\LocaleData` в імпортах, оголошеннях типів і тестах. Новий об'єкт лише для читання має рядкові властивості `code`, `alias`, `native` і `localized`. Для URL і значень інтерфейсу замініть використання старої властивості `code` на `alias`. Там, де раніше читали `$data->locale`, використовуйте `Locale::from($data->code)`, імпортуючи `LaravelLang\LocaleList\Locale`. Отримуйте `type`, `regional` і `direction` через `config("localization_map.locales.{$data->code}", [])`, якщо їх використовують шаблони чи форматувальники. Укажіть власні типові значення для відсутніх записів. Щоб зберегти старі типові значення, використовуйте `Latn` для писемності, канонічний код для регіону та `Direction::LeftToRight` для напрямку.

Видаліть прапорці `withCountry`, `withCurrency`, `withCountries` і `withCurrencies`. Явно отримуйте країну й валюту для рядка локалі. Замініть імпорти старих типів `LaravelLang\Locales\Data\CountryData` та `CurrencyData` на класи даних із відповідних пакетів назв.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Якщо на старому сервісі ви викликали `localizedLocales()`, `localizedCountries()` або `localizedCurrencies()`, використовуйте `all(null)` і `all($displayLanguage)` на відповідному сервісі назв для самоназв і локалізованих значень відповідно. Замість прямого впровадження попередніх класів `Services\Locales` або `Services\RawLocales` використовуйте фасад або відповідні класи `Services\MainService` та `Services\DataService`. Нові API сервісів описано в розділі [Локалі й назви](./locales.md).

#### Допоміжні методи enum-а `Locale` {#locale-enum-helpers}

Елементи enum-ів `Locale` та `Direction` зберігають свої значення. Під час оновлення власних списків локалей або перемикачів замініть виклики вилучених допоміжних методів `archtechx/enums`.

| Попередній метод | Заміна |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Побудуйте мапу від назви до значення за допомогою `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Згенеруйте варіанти у представленні з `Locale::cases()`. Використовуйте значення елемента для канонічного коду або `Locales::toAlias($case)` для аліаса на екрані. |
| `fromName()` або `tryFromName()` | Знайдіть елемент за `name` у `Locale::cases()` та опрацюйте відсутність збігу. Для скалярного значення на кшталт `pt_BR` скористайтеся `Locale::from()` або `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Побудуйте мапу від значення до значення через `array_column(Locale::cases(), 'value', 'value')`. Для списку значень використовуйте `Locale::values()`. |
| `Direction::values()` | Використовуйте `array_column(Direction::cases(), 'value')`. |

### Заміна API пошуку назв {#replace-the-name-lookup-apis}

Оновіть імпорти з `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` і `LaravelLang\NativeLocaleNames` на `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` і `LaravelLang\LocaleNames`. Отримуйте `CountryNames`, `CurrencyNames` або `LocaleNames` через контейнер Laravel чи впроваджуйте відповідний сервіс у свій клас.

| Попереднє використання | Заміна |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` для самоназв | `$names->all(null)` |
| `SortBy::None` | Пропустіть аргумент сортування. |
| `SortBy::Key` | Викличте `->sortKeys()` на поверненій колекції. |
| `SortBy::Value` | Передайте `SortDirection::Ascending` до `all()`, щоб сортувати за локалізованою назвою. |

Використовуйте `one($displayLocale, $entryLocale)` для отримання відомого запису. Ключ пошуку — локаль, наприклад `fr`. Так, `app(CountryNames::class)->one('de', 'fr')` повертає країну для французької локалі з назвою німецькою та значенням `FR` у `code`. DTO країн і валют містяться в кореневих просторах імен своїх пакетів і надають ключ пошуку як `locale`. Поля `code` і далі містять код країни або валюти.

Усі три сервіси повертають колекції з канонічними кодами локалей як ключами. Якщо ви використовували масив рядків зі старого пакета назв локалей, зіставте кожен DTO з його `localized`. Якщо ви викликали `toArray()` на старих DTO країн або валют чи змінювали їхні властивості, явно побудуйте масив застосунку. Нові DTO доступні лише для читання.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Перевіряйте вибрані користувачем локалі за відповідним каталогом перед викликом `one()`. Невідома локаль відображення може призвести до порожнього списку від `all()`. Передавайте `null`, якщо застосунку потрібні самоназви. Якщо код використовує власний enum зі скалярними значеннями, передайте його рядкове значення. [Локалі й назви](./locales.md)

### Оновлення локалізованих маршрутів і мідлварів {#update-localized-routes-and-middleware}

Імпортуйте допоміжну функцію з простору імен у файлах PHP і шаблонах Blade, які генерують локалізовані URL. Також можна безпосередньо викликати `\LaravelLang\Routes\localizedRoute()`.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

Методи `Route::localizedGroup()` і `LocalizationRoute::group()` реєструють маршрути як без префікса, так і з префіксом локалі. Збережіть наявну групу й перевірте виклики допоміжної функції URL. Вона використовує аліас резервної локалі, коли параметр локалі пропущено. Для перемикача мови явно передайте потрібний аліас через `Locales::toAlias($locale)` або `$localeData->alias`. Переданий рядок локалі використовується без змін. Якщо ви перейменували параметр, використовуйте ключ масиву, що відповідає `localization_routes.names.parameter`.

Замініть імпорти й реєстрації в маршрутах, у `bootstrap/app.php` та у власних списках мідлварів. Усі класи з таблиці містяться в `LaravelLang\Routes\Middlewares`.

| Попередній мідлвар | Заміна |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Якщо ви створювали мідлвар для моделі з механізмом автентифікації, передайте його як параметр мідлвара Laravel, наприклад `LocalizationByModelMiddleware::class . ':admin'`. Код визначення користувача очікує модель Eloquent. У власних підкласах мідлвара впровадьте `LaravelLang\Config\Services\RouteLangConfig`, читайте назви джерел із `$this->routeConfig->name` і повертайте `int|string|null` із `detect()`. Замініть прямі посилання на `LaravelLang\Routes\Services\Route` посиланнями на `LaravelLang\Routes\Services\RouteService`.

Перевірте кожне джерело, яке використовує застосунок. Визначення за заголовком використовує PHP Intl для перевірки встановлених мов у порядку пріоритетів `Accept-Language`. Спочатку перевіряється повний мовний тег, потім базова мова. Наступний мідлвар може змінити локаль, вибрану раніше. Щоб локаль із URL визначала мову маршрутів із префіксом, залиште в їхньому ланцюжку тільки мідлвар вибору локалі з URL.

Перевіряйте генерування URL і обробку запитів окремо. Параметр `localization_routes.hide` приховує сегмент резервної локалі під час генерування URL. Параметр `localization_routes.redirect` перенаправляє запити з префіксом поточної локалі на маршрут без префікса. Іменований маршрут із префіксом також перенаправляє некоректну або невстановлену локаль на маршрут без префікса.

В обробниках `LocaleHasBeenSetEvent` використовуйте `$event->locale->code` для канонічного коду та `$event->locale->alias` для відображення. Дані події тепер мають тип `LaravelLang\LocaleNames\LocaleData`. Мідлвар надсилає подію при зміні вибраної локалі та зберігає канонічний код у сеансі. Замініть ручні виклики `LocaleHasBeenSetEvent::dispatch($data)` на `event(new LocaleHasBeenSetEvent($data))`. [Локалізовані маршрути](./routes.md)

### Міграція коду та даних перекладів моделей {#migrate-model-translation-code-and-data}

Перевірте, чи є в стовпці `locale` кожної таблиці перекладів рядки з аліасами. Для кожного аліаса замініть його в цих рядках канонічним кодом під час міграції даних. Перед оновленням перевірте, чи для того самого `item_id` уже існує рядок із канонічним кодом. Об'єднайте значення, які хочете зберегти, в одному рядку, щоб дотриматися обмеження унікальності `(item_id, locale)`. Деякі версії старого трейта вже записували канонічні коди, тому орієнтуйтеся на фактичні значення в таблицях.

Оновіть імпорти та згенеровані класи.

| Попереднє використання | Заміна |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` або `UnknownModelPathException` | Клас із тією самою назвою в `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Оновіть перевизначення, які вибирають власний клас перекладу. |
| `getTranslationTable()` | Використовуйте `translationQualifyColumn($column)` для повної назви стовпця або `translationsRaw()->getRelated()->getTable()` для назви таблиці. |

Методи `getTranslation()`, `setTranslation()`, `hasTranslated()` та `forgetTranslation()` приймають рядок локалі, `Locale` або новий `LocaleData`. Установіть усі локалі, які явно передаєте цим методам, перш ніж їх викликати. Якщо локаль явно не передано й поточне значення дорівнює `null`, читання використовує резервну локаль. Явно вказана локаль читає її значення безпосередньо. Якщо ви викликаєте низькорівневий метод `translation()`, передайте рядок локалі або `null`. Щоб отримати код із DTO, використовуйте `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Якщо аліас задано як `de_CH` → `de-CH` і встановлено швейцарську німецьку, обидва виклики звертаються до збереженого рядка з кодом `de_CH`. Щоб заповнити кілька значень перекладеного поля, передайте мапу з ключами локалей, наприклад `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Індексовані списки тепер спричиняють `InvalidLocaleInputException`. Власний код, що задає завантажений зв'язок `translations`, має передавати колекцію Laravel із моделями перекладів.

Викликайте `translated()`, `whereTranslation()` та пов'язані області запиту через конструктор запитів Eloquent. Передавайте код або аліас як рядок локалі області запиту. Для `orderByTranslation()` використовуйте `'asc'`, `'desc'` або `SortDirection`. Якщо в `whereTranslation()` або `whereTranslationLike()` ви запитуєте локаль, відмінну від поточної або резервної, задайте `localization_models.filter` як `false` або запитуйте `translationsRaw()` безпосередньо.

Оновіть споживачів подій, які викликають `$event->locale->value` або порівнюють `$event->locale` з елементом enum-а `Locale`. Події `TranslationHasBeenSetEvent` і `TranslationHasBeenForgetEvent` тепер містять рядковий аліас для явно вказаної локалі. Подія встановлення містить `null`, якщо локаль пропущено. Перетворіть аліас через `Locales::fromAlias()`, перш ніж використовувати його як збережений ключ. Подія встановлення спрацьовує під час присвоєння атрибута, перед `save()`. Щоб надіслати будь-яку з трьох подій перекладу моделі вручну, використовуйте `event(new …)`.

Якщо ви зберігаєте згенеровані допоміжні файли IDE, виконайте `php artisan lang:models-helper` після оновлення імпортів і шляхів пошуку моделей. [Переклади моделей](./models.md)

### Оновлення скриптів публікування та збереження власних перекладів {#update-publication-scripts-and-keep-custom-translations}

| Попередня команда або припущення | Міграція |
| --- | --- |
| `lang:add <locale>` оновлювала лише локалі, які додавалися | Тепер вона оновлює всі встановлені локалі. У скриптах передавайте коди явно та враховуйте оновлення наявних мовних файлів. |
| `lang:reset` | Для об'єднання використовуйте `lang:update`. Для повторного генерування з нуля створіть резервну копію власних записів, видаліть лише файли, які плануєте згенерувати заново, а потім виконайте `lang:add` із явними кодами потрібних локалей. |
| `lang:rm <locale> --force` | Використовуйте `lang:rm <locale>`. Поточну та резервну локалі Laravel захищено. |
| Перевірка лише коду завершення `lang:rm` | Перевіряйте кожен результат. `DONE` підтверджує видалення, а `FAILED` означає, що після спроби видалення залишився файл або каталог. Стани `PROTECTED`, `INVALID` і `SKIP` можуть супроводжувати код успішного завершення. |

Якщо ви викликаєте Artisan через клас команди, замініть `LaravelLang\Publisher\Console\Add`, `Remove` та `Update` на `AddCommand`, `RemoveCommand` і `UpdateCommand`. Для власного декоратора тексту реалізуйте та прив'яжіть `LaravelLang\Publisher\Contracts\Decorator` замість `Contracts\TextDecorator`. Його метод `convert(string $locale, string $value): string` отримує канонічну локаль.

Виконайте перший `lang:update`, коли поточні мовні файли перебувають під контролем версій. Publisher об'єднує дані з наявними цільовими файлами й замінює збіжні ключі, зокрема числові. За можливості зберігайте власні переклади в окремому файлі перекладів застосунку. Щоб самостійно підтримувати весь згенерований цільовий файл, додайте шаблон його шляху до `localization_main.skip`. Перевірте всі значення, що повернулися до вихідної фрази. Publisher використовує її, коли у словниках немає перекладу.

Якщо попереднє оновлення змінило індекси ключів у файлах `http-statuses.php`, створіть резервні копії відповідних згенерованих файлів і видаліть їх. Приберіть `'*/http-statuses.php'` з `localization_main.skip`, якщо додали цей запис, щоб обійти проблему. Виконайте `php artisan lang:update`, щоб згенерувати файли заново, а потім відновіть власні переклади за відповідними кодами стану. [Керування перекладами](./translations.md)

### Заміна старого способу реєстрації плагінів {#replace-legacy-plugin-registration}

Якщо ви використовуєте сторонній пакет перекладів, оновіть його до релізу з маніфестом. Якщо ви підтримуєте пакет, видаліть провайдер і запис автоматичного пошуку Composer, які реєструють старий `LaravelLang\Publisher\Plugins\Provider`. Якщо цей провайдер також налаштовує інші сервіси Laravel, залиште ці реєстрації у звичайному сервіс-провайдеру Laravel.

1. Додайте `laravel_lang.json` до кореня пакета, який установлюється в `vendor/`. Перетворіть кожен запис `Plugin::files()` на шлях `source` відносно каталогу `source/` пакета та шлях `target` відносно мовного каталогу застосунку. Використовуйте `{locale}` у цільовому шляху.
2. Перенесіть усі правила зіставлення з пакетами Composer до маніфесту. Використовуйте `vendor` і `version` для встановленої залежності та окремий запис із `project: true` для кореневого проєкту Composer. Якщо старий плагін використовував `$with_project_name = true` й підтримував обидва режими, створіть обидва записи.
3. Перетворіть словники на `locales/<code>/<style>.json`. Значення вихідних файлів — це ключі пошуку у словнику. Наприклад, запис PHP `'enable' => 'Enable audit trail'` і далі створює ключ застосунку `audit.enable` під час публікування до `{locale}/audit.php`. Зберігайте його переклад у словнику за ключем `"Enable audit trail"`.
4. З локальної копії Mona виконайте наведену нижче команду Toolkit для пакета попередньої версії. Вона створює `default.json` та `inline.json` зі старих словників JSON і PHP та переносить винятки локалей. Перегляньте результат і додайте його до нового пакета разом із маніфестом і вихідними файлами. Перейменуйте каталоги словників із аліасів на канонічні коди.
5. Додайте актуальний Publisher як залежність у маніфесті Composer пакета. Також додайте `laravel-lang/library`, якщо пакет очікує переклади зі спільного словника. Установіть отриманий пакет у застосунок і виконайте `php artisan lang:update`, щоб перевірити опубліковані переклади.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Для наявних застосунків перевірте, які правила вбудованих пакетів діють після оновлення.

- Якщо вам ще потрібні переклади Laravel-Lang, які раніше публікувалися для Spark Paddle, Spark Stripe, Nova Dusk Suite або Nova Log Viewer, залиште ці ключі у файлах застосунку чи перенесіть старі вихідні файли й словники до власного пакета.
- Переклади стартових наборів тепер зіставляються з полем `name` проєкту в Composer. Якщо ви перейменували проєкт або використовуєте набір як залежність, додайте назву набору в Composer, наприклад `laravel/livewire-starter-kit`, до `localization_main.force`, щоб опублікувати його переклади. [Плагіни перекладу](./plugins.md)

### Оновлення резервних перекладів JSON та інструментів перекладу {#update-json-fallback-and-translation-tooling}

Якщо код застосунку імпортує або використовує як тип `LaravelLang\JsonFallback\Translator`, змініть тип на `LaravelLang\JsonFallback\OverridedTranslator` або використовуйте контракт перекладача Laravel. Якщо ви створили підклас старого перекладача, перенесіть власну поведінку до перекладача застосунку або власного сервісу перекладу. Якщо ключ не знайдено, новий перекладач викликає функцію зворотного виклику Laravel для відсутнього перекладу. Перевірте її, якщо застосунок реєструє таку функцію. Також перевірте ключі, які одночасно є в резервному JSON та PHP-файлі поточної мови, оскільки спочатку перевіряється резервний JSON. [Резервні переклади JSON](./translations.md#json-fallback)

Якщо для машинного перекладу ви використовуєте окремий пакет `laravel-lang/translator`, перенесіть використання фасаду `Translate` та інтеграцій Google, DeepL або Yandex до вибраного сервісу перекладу перед видаленням залежності. Збережіть усі переклади, уже створені цими скриптами, у словниках застосунку або пакета.

Якщо ви обслуговуєте каталоги пакетів за допомогою `vendor/bin/lang` із `laravel-lang/status-generator`, виконуйте нові команди з локальної копії Mona після `composer install`.

| Попередній скрипт | Нова команда Toolkit |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Перегляньте згенеровані звіти `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Для обслуговування спільного пакета Library використовуйте `sync:library`. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Створює каталоги для відомих локалей. |
| `vendor/bin/lang download` | Передайте URL архіву ZIP через `--url`, повний цільовий шлях через `--directory` і шляхи в архіві через повторюваний параметр `--paths`. |
| `vendor/bin/lang translate` | Перенесіть машинний переклад до окремого сервісу. Виконайте команду Toolkit `status`, щоб знайти збережені рядки, які досі збігаються з вихідними. |

Повні приклади наведено в розділі [Команди Toolkit](./toolkit.md).

## Перевірка апгрейду {#verify-the-upgrade}

Запустіть тести застосунку. Перевірте опубліковане повідомлення в кожній важливій локалі, резервний переклад, локалізовану URL-адресу з вашим аліасом і переклад моделі, збережений під канонічним кодом. Перегляньте згенеровані файли й збережені значення локалей разом зі змінами вихідного коду.

---
title: Апгрейд до Mona
description: Обновите зависимости Laravel-Lang, конфигурацию, опубликованные переводы и интеграции приложения.
---

Mona — репозиторий, из которого пакеты Laravel-Lang выходят вместе. Продолжайте использовать отдельные пакеты Composer, которые нужны приложению.

:::info[Почему версия 20?]

Экосистема Laravel-Lang выросла из `laravel-lang/lang`. Пакеты появлялись в разное время, выходили независимо друг от друга и имели собственную нумерацию версий. Чем больше становилось пакетов, тем сложнее нам было согласовывать мажорные обновления, а пользователям — разбираться, какие версии работают вместе.

Мы создали `laravel-lang/common`, чтобы упростить этот выбор. С ним устанавливается совместимый набор зависимостей. При этом обновление всё равно могло затронуть сразу несколько пакетов, каждый со своей версией.

Например, какие версии вы бы установили в проект на Laravel 11 и PHP 8.2 до перехода на Mona?

<details>
<summary>Версии пакетов до Mona</summary>

| Пакет | Версия |
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
| Config | `1.17.1` (версия из ветки 1.x, которую продолжали использовать другие пакеты Laravel-Lang) и `2.3.2` (последняя стабильная версия до Mona, которую эти пакеты использовали редко) |
| Publisher | `16.9.0` |

</details>

В монорепозитории Mona мы выпускаем пакеты вместе под общей версией.

Самый высокий номер версии был у Publisher, и его следующим мажорным релизом мог бы стать `17.0`. Мы вложили много сил и души в то, чтобы собрать экосистему воедино, и выбрали версию 20, чтобы отметить этот переход. А ещё её можно прочитать как **Laravel-Lang 2.0**. 🙂

:::

:::info[Коды и алиасы локализаций]

Laravel-Lang теперь использует канонические коды локалей в сохраняемых значениях, включая пути публикации и строки переводов моделей. Текущая и резервная локали Laravel также используют канонические коды. В URL и переключателях языков используйте алиасы. Методы фасада возвращают настроенный алиас либо канонический код, если алиас не задан. Например, настройка `'pt_BR' => 'pt-br'` в `localization_main.aliases` сохраняет адрес `/pt-br/account` при публикации в `lang/pt_BR.json` и хранении `pt_BR` в базе.

:::

## Обновление зависимостей {#updating-dependencies}

Текущая версия поддерживает Laravel 12 и 13 и требует PHP `^8.3`. В `composer.json` приложения задайте ограничение `^20.0` для каждого пакета из таблицы, присутствующего в `require` или `require-dev`. Если установлен только `laravel-lang/common`, достаточно обновить его.

Перед установкой `20.x` или обновлением проверьте, что нужная версия пакета Composer доступна на Packagist. Релиз Mona и соответствующие пакеты Composer могут появиться в разное время.

| Назначение | Пакеты Composer |
| --- | --- |
| Комплект | `laravel-lang/common` |
| Пакеты переводов | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Публикация и конфигурация | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Интеграции приложения | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Общие утилиты | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Шаблон пакета | `laravel-lang/translations-template` |

Если вы используете только стандартный процесс работы с переводами, обновите зависимости и повторно опубликуйте переводы.

```bash
composer update
php artisan lang:update
```

Если вы регистрировали сервис-провайдеров, публиковали собственные настройки, используете алиасы, сторонние пакеты переводов или API пакетов в приложении, перед публикацией выполните подходящие шаги из разделов ниже.

## Замена переименованных или устаревших зависимостей {#replace-renamed-or-retired-dependencies}

Перед запуском `composer update` обновите прямые зависимости от старых пакетов названий в `composer.json`.

| Прежний пакет Composer | Зависимость версии `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common уже зависит от всех трёх пакетов названий. Обновите также PHP-код. См. [замену API поиска названий](#replace-the-name-lookup-apis).

Проверьте, есть ли у каждого стороннего плагина релиз, совместимый с новым Publisher. Перенесите код, использующий `laravel-lang/translator` или `laravel-lang/status-generator`, перед удалением этих зависимостей. См. [инструменты для переводов](#update-json-fallback-and-translation-tooling).

## Обновление сервис-провайдеров {#update-service-providers}

Laravel обнаруживает сервис-провайдеры Config, Publisher, Routes и Models в манифестах пакетов. Если вы используете автообнаружение, обновите ссылки на старые классы в коде и тестах. Если вы регистрируете пакеты вручную, замените классы в том же списке, например в `bootstrap/providers.php` или `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Удалите ручную регистрацию `LaravelLang\Locales\LaravelLangLocalesServiceProvider` и старых классов `ServiceProvider` в пространствах имён `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` и `LaravelLang\StarterKits`. Эти пакеты переводов обнаруживаются через манифесты.

Если вы используете резервный поиск в JSON, замените `LaravelLang\JsonFallback\TranslationServiceProvider` на `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` в том же месте. Так переопределение сервис-провайдера переводов Laravel продолжит действовать.

Обновите ссылки на прежние классы до запуска `composer update`, чтобы обнаружение пакетов могло загрузить приложение. Если скрипт Composer по-прежнему завершается ошибкой при загрузке старого класса, запустите `composer update --no-scripts`, обновите ссылки и выполните `composer dump-autoload` для повторного обнаружения.

## Перенос конфигурации {#migrate-configuration}

Если вы меняли `config/localization.php`, сохраните его для справки на время переноса. Пакеты автоматически загружают новые настройки по умолчанию. Опубликуйте нужные [файлы конфигурации](./configure.md). Тег `localization` публикует все пять. Для одного файла используйте его имя без `.php` как тег.

```bash
php artisan vendor:publish --tag=localization
```

### Алиасы, стили перевода и форматирование файлов {#aliases-translation-styles-and-file-formatting}

- Скопируйте карту `aliases` в `config/localization_main.php`. Сохраните канонические коды в качестве ключей и алиасы в качестве значений, например `'de_CH' => 'de-CH'`.
- Замените `inline` на `styles` в `config/localization_main.php`. Чтобы сохранить прежнее поведение со стандартными сообщениями валидации, задайте `'styles' => ['default']`. Если вы использовали сообщения без имени поля, оставьте новое значение по умолчанию `['default', 'inline']`. Удалите `LOCALIZATION_INLINE` и `LANG_PUBLISHER_INLINE` из окружений, где вы их задавали.
- Удалите ключ `align` и переменные окружения `LOCALIZATION_ALIGN` и `LANG_PUBLISHER_ALIGN`. Для выравнивания операторов `=>` в опубликованных массивах настройте форматирование PHP в проекте и запускайте его после публикации. Например, правило `binary_operator_spaces` в Laravel Pint может применить `align_single_space_minimal` к `=>`.

### Типографская пунктуация {#smart-punctuation}

Если вы включали типографскую пунктуацию, переименуйте `LOCALIZATION_SMART_ENABLED` в `LOCALIZATION_PUNCTUATION_ENABLED` и сохраните значение `true`. Если вы задавали `smart_punctuation.enable` в PHP, перенесите логическое значение в ключ верхнего уровня `enabled` в `config/localization_punctuation.php`. По умолчанию пунктуация отключена.

Перенесите настроенные кавычки из `smart_punctuation.common` и `smart_punctuation.locales` в массивы верхнего уровня `common` и `locales` в этом файле. Используйте канонические коды в качестве ключей `locales`. Кавычки локали переопределяют соответствующие ключи в `common`. Если вы скопировали старый французский пример с `&nbsp;`, используйте в строках кавычек настоящий неразрывный пробел или `"\u{00A0}"`.

### Локализованные маршруты {#localized-routes}

Перенесите свои настройки из прежнего раздела `routes` в `config/localization_routes.php`.

| Что вы настроили | Изменение в `config/localization_routes.php` | Переименование переменной окружения |
| --- | --- | --- |
| Параметр локали, заголовок, cookie, ключ сеанса или столбец модели пользователя | Скопируйте свои значения `names` и сохраните остальные значения по умолчанию. | |
| Префикс имён локализованных маршрутов | Перенесите `name_prefix` в `prefix`. Если значение пустое, используется `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Перенаправление с маршрута с префиксом | Перенесите `redirect_default` в `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Скрытие резервной локали при генерации URL | Перенесите `hide_default` в `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Мидлвари групп локализованных маршрутов | Перенесите `group.middlewares.default` в `middlewares.default` для маршрутов без префикса и `group.middlewares.prefix` в `middlewares.prefix` для маршрутов с префиксом. Замените имена классов по разделу [Обновление маршрутов](#update-localized-routes-and-middleware). | |

### Переводы моделей {#model-translations}

Перенесите настройки из `models` в `config/localization_models.php`. Скопируйте собственные значения `suffix` и `helpers` на верхний уровень. Используйте логический ключ `filter` вместо `filter.enabled`. Если задаёте фильтрацию и путь к файлам для IDE через окружение, сохраните `LOCALIZATION_FILTER_ENABLED` и `VENDOR_PATH`.

По умолчанию команды для моделей теперь сканируют `app/Models`. Если вы полагались на сканирование всего `app/` или задавали `localization-private.models.directory`, внесите нужные каталоги в массив `directories`. Например, `'directories' => [app_path()]` сохраняет прежний поиск.

### Настройки, используемые кодом приложения {#configuration-used-by-application-code}

- Замените чтение и запись через `LaravelLang\Config\Facades\Config` на вызовы конфигурации Laravel с новыми ключами. Например, замените `Config::shared()->models->filter->enabled` на `config('localization_models.filter')`. Для типизированного доступа внедрите `LaravelLang\Config\Services\ModelLangConfig` и читайте его свойство `filter`. Для настроек маршрутов используйте `RouteLangConfig` из того же пространства имён.
- Если код читает метаданные локалей из `localization-private.map`, используйте `localization_map.locales`. Скопируйте переопределения в массив `locales` файла `config/localization_map.php`.
- Замените регистрацию через `localization-private.plugins` и `localization-private.packages` на [манифест плагина](./plugins.md).
- Удалите старый блок `translators` и настройки `TRANSLATION_*`, которые использовал только `laravel-lang/translator`.

После переноса настроек и замены ссылок в приложении удалите `config/localization.php` и выполните `php artisan config:clear`.

## Перенос языковых файлов на канонические пути {#move-language-files-to-canonical-paths}

1. Задайте для `APP_LOCALE` и `APP_FALLBACK_LOCALE` канонические коды. Перенесите алиасы в `localization_main.aliases`, чтобы URL и ввод локалей продолжили работать.
2. Переименуйте файлы и каталоги с алиасами, например `lang/pt-br.json` и `lang/pt-br/`, в `lang/pt_BR.json` и `lang/pt_BR/`. Проверьте те же алиасы в `lang/vendor/<package>/`. Если существуют оба имени, объедините их содержимое и разрешите конфликты пользовательских переводов, прежде чем удалить путь с алиасом.
3. Установите каждую локаль, представленную только PHP-переводами пакетов, через `php artisan lang:add <locale>`. Список установленных локалей учитывает файлы и каталоги непосредственно в языковом каталоге, JSON-файлы в `lang/vendor/<package>/` и текущую и резервную локали.
4. После переноса конфигурации и плагинов выполните `php artisan lang:update` и просмотрите изменения. Publisher может сохранить ключи интеграций, которые больше не публикуют файл. Сохраняйте используемые приложением ключи и удаляйте устаревшие после проверки мест вызова. Как создать файлы заново или сохранить их, см. в разделе [Скрипты публикации и пользовательские переводы](#update-publication-scripts-and-keep-custom-translations).

## Обновление интеграций {#update-integrations}

### Локали {#locales}

Сохраните импорт `LaravelLang\Locales\Facades\Locales` и обновите вызовы в зависимости от данных, необходимых вызывающему коду.

| Прежний вызов | Замена |
| --- | --- |
| `Locales::available()`, `installed()` или `protects()` для получения объектов | Вызовите тот же метод у `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` или `protects()` | Вызовите тот же метод напрямую у `Locales`, чтобы получить коллекцию строк с алиасами. Передайте `false` для канонических кодов и вызовите `->all()`, если нужен массив. |
| `Locales::installed(false)` или `Locales::raw()->installed(false)` | Теперь `false` выбирает канонические коды. Текущая и резервная локали включены даже без языковых файлов. Замените и прежний именованный аргумент `withProtects: false`. Чтобы исключить эти локали, используйте `Locales::installed(false)->diff(Locales::protects(false))->values()`. Для объектов используйте `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` или `Locales::raw()->notInstalled()` | Используйте `Locales::data()->uninstalled()` для объектов и `Locales::uninstalled()` для строк с алиасами. Передайте `false` для канонических кодов. |
| `Locales::getCurrent()` или `Locales::getDefault()` | Используйте `Locales::data()->current()` для DTO или `Locales::current()` для строки. Замените `Locales::raw()->getDefault()` на `Locales::current()`. Если через `Locales::raw()->getCurrent()` вы читали значение из Laravel, используйте `app()->getLocale()`. |
| `Locales::getFallback()` или `Locales::raw()->getFallback()` | Используйте `Locales::data()->fallback()` для DTO или `Locales::fallback()` для строки. Неизвестная резервная локаль теперь преобразуется в английскую, а неизвестная текущая — в фактическую резервную. |
| `Locales::get($locale)` или `Locales::raw()->get($locale)` | Используйте `isInstalled()` и выбирайте резервное значение явно. Для поддерживаемой локали, которая может быть не установлена, вызывайте `isAvailable()`, затем `info()`. |
| `Locales::info($locale)` или `Locales::raw()->info($locale)` | Вызывайте `Locales::info($locale)`, а для строкового значения читайте `->alias` или `->code`. Для текущей локали передайте `null`. Для неизвестного или пустого кода обработайте `LaravelLang\Exceptions\UnknownLocaleException` либо проверьте код с помощью `isAvailable()` до поиска. |

Чтобы сохранить прежний возврат к текущей локали в `get()` для неустановленного или недопустимого значения, добавьте этот выбор в вызывающий код.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Use this in paths, database rows, and other persisted data.
$alias = $locale->alias; // Use this in URLs and language selectors.
```

Обновите обработчики, передававшие результат `raw()->get()` в `app()->setLocale()`. Получив значение из запроса, вызовите `Locales::set($locale)`. Метод принимает коды установленных локалей, алиасы, элементы enum и новый DTO и передаёт Laravel канонический код. Для `pt-br` в запросе Laravel получает `pt_BR`, а `Locales::current()` возвращает `pt-br`. Если нужны канонические коды, вызывайте `Locales::current(false)` или `Locales::fallback(false)`.

При сохранении значения в других местах приложения проверьте его через `isAvailable()` или `isInstalled()` и получите канонический код через `fromAlias()`. Для отображения используйте `toAlias()`. Замените обращения к старому `Services\Resolver` на `acceptsInput()` для проверки допустимых типов PHP или на `normalize()` для получения строки из типизированного значения. `normalize()` оставляет строки без изменений, включая алиас вроде `pt-br`.

Замените `LaravelLang\Locales\Data\LocaleData` на `LaravelLang\LocaleNames\LocaleData` в импортах, указаниях типов и тестах. Новый объект доступен только для чтения и содержит строки `code`, `alias`, `native` и `localized`. Для URL и интерфейса используйте `alias` вместо старого свойства `code`. Вместо `$data->locale` используйте `Locale::from($data->code)` с импортом `LaravelLang\LocaleList\Locale`. Если шаблоны и форматтеры используют `type`, `regional` или `direction`, читайте их через `config("localization_map.locales.{$data->code}", [])`. Задайте значения по умолчанию для отсутствующих записей. Прежние значения по умолчанию — `Latn` для письменности, канонический код для региона и `Direction::LeftToRight` для направления.

Удалите флаги `withCountry`, `withCurrency`, `withCountries` и `withCurrencies`. Получайте страну и валюту для записи локали явно. Замените импорты старых типов `LaravelLang\Locales\Data\CountryData` и `CurrencyData` классами данных из соответствующих пакетов названий.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Если вы использовали `localizedLocales()`, `localizedCountries()` или `localizedCurrencies()` у старого сервиса, вызывайте `all(null)` для получения самоназваний и `all($displayLanguage)` для локализованных значений у соответствующего сервиса названий. Замените прямое внедрение классов `Services\Locales` и `Services\RawLocales` фасадом или соответствующими `Services\MainService` и `Services\DataService`. См. новые API в разделе [Локали и названия](./locales.md).

#### Вспомогательные методы enums {#locale-enum-helpers}

Элементы enums `Locale` и `Direction` сохраняют значения. При обновлении собственных списков и переключателей замените вызовы удалённых методов `archtechx/enums`.

| Прежний метод | Замена |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Составьте соответствие имён значениям через `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Создайте варианты в представлении из `Locale::cases()`. Используйте значение элемента для канонического кода или `Locales::toAlias($case)` для отображаемого алиаса. |
| `fromName()` или `tryFromName()` | Найдите элемент по `name` в `Locale::cases()` и обработайте отсутствие совпадения. Для значения вроде `pt_BR` используйте `Locale::from()` или `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Составьте карту со значениями в качестве ключей и значений через `array_column(Locale::cases(), 'value', 'value')`. Для списка используйте `Locale::values()`. |
| `Direction::values()` | Используйте `array_column(Direction::cases(), 'value')`. |

### Замена API поиска названий {#replace-the-name-lookup-apis}

Замените импорты из `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` и `LaravelLang\NativeLocaleNames` на `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` и `LaravelLang\LocaleNames`. Получайте `CountryNames`, `CurrencyNames` или `LocaleNames` из контейнера Laravel либо внедряйте сервис в класс.

| Прежний вызов | Замена |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` для самоназваний | `$names->all(null)` |
| `SortBy::None` | Не передавайте аргумент сортировки. |
| `SortBy::Key` | Вызовите `->sortKeys()` у полученной коллекции. |
| `SortBy::Value` | Передайте `SortDirection::Ascending` в `all()` для сортировки по локализованному названию. |

Получайте известную запись через `one($displayLocale, $entryLocale)`. В качестве ключа поиска используется локаль, например `fr`. Вызов `app(CountryNames::class)->one('de', 'fr')` возвращает страну для французской локали с названием на немецком и `FR` в `code`. DTO стран и валют находятся в корневом пространстве имён своих пакетов. В них ключ поиска доступен в `locale`, а код страны или валюты по-прежнему в `code`.

Все три сервиса возвращают коллекции с каноническими кодами локалей в качестве ключей. Если вы использовали массив строк из старого пакета названий локалей, читайте `localized` из каждого DTO. Если вы вызывали `toArray()` у прежних DTO стран или валют либо меняли их свойства, постройте массив приложения явно. Новые DTO доступны только для чтения.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Перед вызовом `one()` проверяйте выбранные пользователем локали по соответствующему каталогу. При неизвестном языке отображения `all()` может вернуть пустой список. Передавайте `null` для получения самоназваний. Если использовали собственный enum со значениями, передавайте его строковое значение. [Локали и названия](./locales.md)

### Обновление локализованных маршрутов и мидлварей {#update-localized-routes-and-middleware}

Импортируйте вспомогательную функцию из её пространства имён в файлах PHP и шаблонах Blade, которые генерируют локализованные URL. Также можно напрямую вызвать `\LaravelLang\Routes\localizedRoute()`.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` и `LocalizationRoute::group()` регистрируют маршруты без префикса и с префиксом локали. Сохраните группу и проверьте вызовы генератора URL. Когда параметр локали опущен, используется алиас резервной локали. Для переключателя языков передавайте нужный алиас через `Locales::toAlias($locale)` или `$localeData->alias`. Переданная строка используется как есть. Если параметр переименован, укажите в массиве ключ из `localization_routes.names.parameter`.

Обновите импорты и регистрацию в маршрутах, `bootstrap/app.php` и пользовательских списках мидлварей. Все классы в таблице находятся в `LaravelLang\Routes\Middlewares`.

| Прежний класс | Замена |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Если при создании мидлварей для модели вы указывали guard, передайте его как параметр мидлвари Laravel, например `LocalizationByModelMiddleware::class . ':admin'`. Функция поиска пользователя ожидает модель Eloquent. В собственных дочерних классах внедряйте `LaravelLang\Config\Services\RouteLangConfig`, читайте имена источников из `$this->routeConfig->name` и возвращайте из `detect()` значение типа `int|string|null`. Замените прямые ссылки на `LaravelLang\Routes\Services\Route` ссылками на `LaravelLang\Routes\Services\RouteService`.

Проверьте каждый используемый приложением источник локали. Мидлварь заголовка использует PHP Intl, чтобы проверить установленные языки в порядке предпочтений из `Accept-Language`. Она проверяет полный языковой тег, затем базовый язык. Последующая мидлварь может переопределить ранее выбранную локаль. Чтобы локаль из URL определяла язык маршрутов с префиксом, оставьте в их цепочке только мидлварь выбора локали из URL.

Проверьте генерацию URL и обработку запросов отдельно. `localization_routes.hide` скрывает сегмент резервной локали при генерации URL. `localization_routes.redirect` перенаправляет запросы с префиксом текущей локали на маршрут без префикса. Именованный маршрут с префиксом также перенаправляет запрос с недопустимой или неустановленной локалью на маршрут без префикса.

В слушателях `LocaleHasBeenSetEvent` используйте `$event->locale->code` для канонического кода и `$event->locale->alias` для отображения. Событие теперь содержит `LaravelLang\LocaleNames\LocaleData`. Мидлварь отправляет событие при смене выбранной локали и сохраняет канонический код в сеансе. Замените ручной вызов `LocaleHasBeenSetEvent::dispatch($data)` на `event(new LocaleHasBeenSetEvent($data))`. [Локализованные маршруты](./routes.md)

### Перенос кода и данных переводов моделей {#migrate-model-translation-code-and-data}

Проверьте столбец `locale` каждой таблицы переводов на наличие алиасов. В миграции данных замените каждый алиас каноническим кодом. Сначала проверьте, есть ли у того же `item_id` строка с каноническим кодом. Объедините нужные значения в одной строке с учётом ограничения уникальности `(item_id, locale)`. Версии прежнего трейта уже записывали канонические коды, поэтому ориентируйтесь на значения, фактически сохранённые в таблицах.

Обновите импорты и сгенерированные классы.

| Прежний вызов | Замена |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` или `UnknownModelPathException` | Класс с тем же именем в `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Обновите переопределения, выбирающие пользовательский класс перевода. |
| `getTranslationTable()` | Для полного имени столбца используйте `translationQualifyColumn($column)`, для имени таблицы — `translationsRaw()->getRelated()->getTable()`. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` и `forgetTranslation()` принимают строку локали, `Locale` или новый `LocaleData`. До вызова метода установите любую явно передаваемую локаль. Чтение без явной локали использует резервную, когда текущее значение равно `null`. При явной локали читается значение именно для неё. При вызове низкоуровневого `translation()` передайте строку или `null`. Для преобразования DTO используйте `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

При алиасе `de_CH` → `de-CH` и установленном швейцарском немецком оба вызова обращаются к строке `de_CH`. Для заполнения нескольких переводов поля передавайте массив с ключами по локалям, например `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Индексированные списки теперь вызывают `InvalidLocaleInputException`. Пользовательский код, устанавливающий загруженную связь `translations`, должен передавать коллекцию Laravel из моделей переводов.

Вызывайте `translated()`, `whereTranslation()` и связанные области запросов через построитель запросов Eloquent. В качестве строки локали передавайте код или алиас. Для `orderByTranslation()` используйте `'asc'`, `'desc'` или `SortDirection`. Если `whereTranslation()` или `whereTranslationLike()` должны искать по локали, отличной от текущей и резервной, задайте `localization_models.filter` равным `false` или запрашивайте `translationsRaw()` напрямую.

Обновите слушателей, которые читают `$event->locale->value` или сравнивают `$event->locale` с элементом enum `Locale`. `TranslationHasBeenSetEvent` и `TranslationHasBeenForgetEvent` теперь содержат строковый алиас явно переданной локали. Если локаль опущена, событие установки содержит `null`. Преобразуйте алиас через `Locales::fromAlias()` перед сохранением в качестве ключа. Событие установки срабатывает при присвоении атрибута, до `save()`. Для ручной отправки любого из трёх событий перевода используйте `event(new …)`.

Если вы храните сгенерированные файлы для IDE, выполните `php artisan lang:models-helper` после замены импортов и путей поиска моделей. [Переводы моделей](./models.md)

### Обновление скриптов публикации и сохранение пользовательских переводов {#update-publication-scripts-and-keep-custom-translations}

| Прежняя команда или поведение | Как перейти |
| --- | --- |
| `lang:add <locale>` обновляла только добавляемые локали | Теперь обновляются все установленные локали. В скриптах передавайте явные коды и учитывайте изменения файлов существующих локалей. |
| `lang:reset` | Используйте `lang:update` для объединения. Чтобы создать файлы заново, сделайте резервную копию пользовательских записей, удалите файлы, которые хотите пересоздать, и выполните `lang:add` с явными кодами нужных локалей. |
| `lang:rm <locale> --force` | Используйте `lang:rm <locale>`. Текущая и резервная локали Laravel защищены. |
| Проверяется только код завершения `lang:rm` | Проверяйте каждый результат. `DONE` подтверждает удаление, а `FAILED` означает, что после попытки удаления остался файл или каталог. `PROTECTED`, `INVALID` и `SKIP` возможны при успешном коде завершения. |

Если вы вызываете Artisan по классу команды, замените `LaravelLang\Publisher\Console\Add`, `Remove` и `Update` на `AddCommand`, `RemoveCommand` и `UpdateCommand`. Для пользовательского декоратора текста реализуйте и зарегистрируйте `LaravelLang\Publisher\Contracts\Decorator` вместо `Contracts\TextDecorator`. Метод `convert(string $locale, string $value): string` получает канонический код.

Выполните первый `lang:update`, сохранив существующие языковые файлы в системе контроля версий. Publisher объединяет переводы с существующими файлами и заменяет значения совпадающих ключей, в том числе числовых. По возможности храните пользовательские переводы в отдельных языковых файлах приложения. Чтобы вести весь генерируемый файл самостоятельно, добавьте шаблон его пути в `localization_main.skip`. Проверьте значения, вернувшиеся к исходным фразам. Publisher использует их, когда перевода нет в словарях.

Если при предыдущем обновлении ключи в файлах `http-statuses.php` изменились, сделайте резервные копии затронутых сгенерированных файлов и удалите их. Удалите `'*/http-statuses.php'` из `localization_main.skip`, если добавили это исключение для обхода проблемы. Выполните `php artisan lang:update`, чтобы сгенерировать файлы заново, и восстановите пользовательские переводы под соответствующими кодами статусов. [Управление переводами](./translations.md)

### Замена способа регистрации прежних плагинов {#replace-legacy-plugin-registration}

Если вы используете сторонний пакет переводов, обновите его до релиза с манифестом. Если вы сопровождаете пакет, удалите сервис-провайдер и запись автообнаружения Composer, регистрирующие старый `LaravelLang\Publisher\Plugins\Provider`. Если сервис-провайдер настраивает также другие службы Laravel, сохраните их регистрацию в обычном сервис-провайдере Laravel.

1. Добавьте `laravel_lang.json` в корень пакета, установленного в `vendor/`. Преобразуйте каждую запись `Plugin::files()` в путь `source` относительно каталога пакета `source/` и путь `target` относительно языкового каталога приложения. В `target` укажите подстановку `{locale}`.
2. Перенесите условия сопоставления Composer в манифест. Используйте `vendor` и `version` для установленной зависимости, а для корневого проекта Composer создайте отдельную запись с `project: true`. Если старый плагин использовал `$with_project_name = true` и поддерживал оба режима, добавьте обе записи.
3. Преобразуйте словари в `locales/<code>/<style>.json`. Значения в исходных файлах служат ключами поиска в словарях. Например, запись PHP `'enable' => 'Enable audit trail'` по-прежнему создаёт ключ приложения `audit.enable` при публикации в `{locale}/audit.php`. Храните её перевод в словаре под ключом `"Enable audit trail"`.
4. Запустите приведённую ниже команду Toolkit из локальной копии Mona для старого пакета. Она создаёт `default.json` и `inline.json` из прежних словарей JSON и PHP и переносит исключения локалей. Проверьте результат и добавьте его в новый пакет вместе с манифестом и исходными файлами. Переименуйте каталоги словарей с алиасами, используя канонические коды.
5. Укажите зависимость от текущего Publisher в манифесте Composer пакета. Добавьте `laravel-lang/library`, если пакету нужны общие переводы. Установите полученный пакет в приложение и выполните `php artisan lang:update`, чтобы проверить результат публикации.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

В существующих приложениях проверьте, какие правила встроенных пакетов действуют после обновления.

- Если нужны переводы Laravel-Lang, которые раньше публиковались для Spark Paddle, Spark Stripe, Nova Dusk Suite или Nova Log Viewer, храните эти ключи в файлах приложения либо перенесите старые исходники и словари в собственный пакет.
- Переводы стартовых наборов теперь сопоставляются с `name` проекта в Composer. Если вы переименовали проект или установили набор как зависимость, добавьте имя набора в Composer, например `laravel/livewire-starter-kit`, в `localization_main.force`. [Плагины переводов](./plugins.md)

### Обновление резервного поиска в JSON и инструментов переводов {#update-json-fallback-and-translation-tooling}

Если код приложения импортирует `LaravelLang\JsonFallback\Translator` или использует его как тип, замените его на `LaravelLang\JsonFallback\OverridedTranslator` или используйте контракт переводчика Laravel. Если вы создавали подкласс старого переводчика, перенесите своё поведение в переводчик приложения или отдельный сервис переводов. При ненайденном ключе новый переводчик вызывает обработчик Laravel для отсутствующих переводов. Если приложение регистрирует такой обработчик, проверьте его. Проверьте также ключи, присутствующие в резервном JSON и PHP текущей локали: резервный JSON проверяется первым. [Резервный поиск в JSON](./translations.md#json-fallback)

Если вы используете отдельный пакет `laravel-lang/translator` для машинного перевода, до удаления зависимости перенесите вызовы его фасада `Translate` и интеграции Google, DeepL или Yandex в выбранный сервис. Сохраните уже сгенерированные переводы в словарях приложения или пакета.

Если вы ведёте каталоги пакетов через `vendor/bin/lang` из `laravel-lang/status-generator`, выполняйте новые команды в локальной копии Mona после `composer install`.

| Прежний скрипт | Новый скрипт Toolkit |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Изучите отчёты `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Для сопровождения общей Library используйте `sync:library`. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Команда создаёт каталоги для известных локалей. |
| `vendor/bin/lang download` | Передайте URL архива через `--url`, полный путь назначения через `--directory` и пути внутри архива через повторяемый `--paths`. |
| `vendor/bin/lang translate` | Перенесите машинный перевод в отдельный сервис. Запустите `status` в Toolkit, чтобы найти сохранённые строки, которые всё ещё совпадают с исходными. |

Полные примеры см. в разделе [Команды Toolkit](./toolkit.md).

## Проверка обновления {#verify-the-upgrade}

Выполните тесты приложения. Проверьте опубликованное сообщение в каждой важной для приложения локали, резервный перевод, локализованный URL с настроенным алиасом и перевод модели, сохранённый под каноническим кодом. Просмотрите сгенерированные файлы и сохранённые значения локалей вместе с изменениями исходного кода.

---
title: Надоградња на Mona
description: Надоградите Laravel-Lang зависности, подешавања, објављене преводе и интеграције апликације.
---

Mona је репозиторијум из ког се Laravel-Lang пакети објављују заједно. Наставите да користите појединачне Composer пакете који су потребни вашој апликацији.

:::info[Зашто верзија 20?]

Екосистем Laravel-Lang израстао је из пакета `laravel-lang/lang`. Пакети су настајали у различито време и сваки је имао своје бројеве верзија и распоред издања. При већим надоградњама морали смо да ускладимо измене у више пакета, а корисници су морали да утврде које верзије могу заједно да инсталирају.

Направили смо `laravel-lang/common` да олакшамо избор. Тај пакет је задавао компатибилне верзије зависности, али је једна надоградња и даље могла да обухвати више пакета са засебним верзијама.

Овако су изгледале верзије пакета пре преласка на Mona. Које бисте верзије инсталирали за Laravel 11 са PHP 8.2?

<details>
<summary>Верзије пакета пре преласка на Mona</summary>

| Пакет | Верзија |
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
| Config | `1.17.1` (верзија из серије 1.x коју су други Laravel-Lang пакети наставили да користе) и `2.3.2` (последња стабилна верзија пре преласка на Mona, коју су ти пакети ретко користили) |
| Publisher | `16.9.0` |

</details>

Из монорепозиторијума Mona објављујемо пакете заједно, под истим бројем верзије. Publisher је имао највиши број верзије, па је следећа могла да буде `17.0`. Уложили смо много труда и пажње у обједињавање екосистема и изабрали верзију 20 да обележимо ту прекретницу. Можете је прочитати и као **Laravel-Lang 2.0**. 🙂

:::

:::info[Кодови и алијаси локала]

Laravel-Lang сада користи канонске кодове локала за вредности које чува, укључујући путање објављених превода и редове превода модела. Активни и резервни локал Laravel-а такође користе канонске кодове. При извршавању користите алијасе за сегменте URL-а и избор језика. Методи фасаде који враћају кодове локала подразумевано користе подешени алијас или канонски код ако алијас није подешен. На пример, у `localization_main.aliases` подесите `'pt_BR' => 'pt-br'` да задржите `/pt-br/account` док се преводи објављују у `lang/pt_BR.json`, а у бази се чува `pt_BR`.

:::

## Ажурирање зависности {#updating-dependencies}

Тренутна верзија подржава Laravel 12 и 13 и захтева PHP `^8.3`. У `composer.json` апликације подесите ограничење `^20.0` за сваки пакет испод који се појављује у `require` или `require-dev`. Ако сте инсталирали само `laravel-lang/common`, потребно је да промените само ту зависност.

Пре инсталирања `20.x` или преласка на ту верзију проверите да ли је верзија Composer пакета доступна на Packagist-у. Издање Mona и одговарајући Composer пакети могу постати доступни у различито време.

| Намена | Composer пакети |
| --- | --- |
| Збирни пакет | `laravel-lang/common` |
| Пакети превода | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Објављивање и подешавање | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Интеграције апликације | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Заједнички помоћни пакети | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Шаблон пакета | `laravel-lang/translations-template` |

За инсталацију која користи само стандардни поступак превођења ажурирајте зависности и поново објавите преводе.

```bash
composer update
php artisan lang:update
```

Ако сте регистровали сервисне провајдере, објавили прилагођена подешавања, користите алијасе или пакете превода трећих страна, или позивате API-је пакета из апликације, пре поновног објављивања пратите одељке ниже који се односе на вас.

## Замена преименованих или застарелих зависности {#replace-renamed-or-retired-dependencies}

Ажурирајте непосредне зависности од старих пакета за називе у `composer.json` пре покретања `composer update`.

| Претходни Composer пакет | Наведите зависност са `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common већ зависи од сва три пакета за називе. Ажурирајте и PHP код. Погледајте [замену API-ја за претрагу назива](#replace-the-name-lookup-apis).

За сваки додатак треће стране проверите да ли постоји издање компатибилно с новим Publisher-ом. Пре уклањања зависности `laravel-lang/translator` или `laravel-lang/status-generator` мигрирајте све њихове употребе. Погледајте [алатке за превођење](#update-json-fallback-and-translation-tooling).

## Ажурирање сервисних провајдера {#update-service-providers}

Laravel открива сервисне провајдере Config, Publisher, Routes и Models из манифеста пакета. Ако се ослањате на откривање, ажурирајте код апликације и тестове који упућују на старе класе провајдера. Ако апликација ручно региструје пакете, употребите следеће замене у истом списку провајдера, попут `bootstrap/providers.php` или `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Уклоните ручне регистрације `LaravelLang\Locales\LaravelLangLocalesServiceProvider` и старих класа `ServiceProvider` из `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` и `LaravelLang\StarterKits`. Ови пакети превода откривају се преко својих манифеста.

Ако користите резервне JSON преводе, замените `LaravelLang\JsonFallback\TranslationServiceProvider` са `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` на месту регистрације. Тако замена Laravel-овог провајдера превода остаје активна.

Ажурирајте застарела упућивања на провајдере пре покретања `composer update` како би откривање пакета могло да покрене апликацију. Ако Composer скрипта и даље не успева јер учитава стару класу, покрените `composer update --no-scripts`, ажурирајте упућивања и покрените `composer dump-autoload` да поновите откривање.

## Миграција подешавања {#migrate-configuration}

Ако сте прилагодили `config/localization.php`, задржите га као референцу док премештате измене. Пакети аутоматски учитавају нова подразумевана подешавања. Објавите [датотеке подешавања](./configure.md) које треба да измените. Ознака `localization` објављује свих пет. За објављивање једне користите назив датотеке без `.php` као ознаку.

```bash
php artisan vendor:publish --tag=localization
```

### Алијаси, стилови превода и форматирање датотека {#aliases-translation-styles-and-file-formatting}

- Копирајте мапу `aliases` у `config/localization_main.php`. Задржите канонске кодове као кључеве и алијасе као вредности, на пример `'de_CH' => 'de-CH'`.
- Замените `inline` са `styles` у `config/localization_main.php`. Подесите `'styles' => ['default']` да задржите претходно подразумевано понашање са стандардним порукама валидације. Задржите ново подразумевано подешавање `['default', 'inline']` ако сте користили поруке без назива поља. Уклоните `LOCALIZATION_INLINE` и `LANG_PUBLISHER_INLINE` из окружења у којима сте их подесили.
- Уклоните кључ `align` и променљиве окружења `LOCALIZATION_ALIGN` и `LANG_PUBLISHER_ALIGN`. Да поравнате операторе `=>` у објављеним PHP низовима, подесите алатку пројекта за форматирање PHP кода и покрените је над језичким датотекама после објављивања. На пример, правило `binary_operator_spaces` у Laravel Pint-у може применити `align_single_space_minimal` на `=>`.

### Паметна интерпункција {#smart-punctuation}

Ако сте укључили паметну интерпункцију, преименујте `LOCALIZATION_SMART_ENABLED` у `LOCALIZATION_PUNCTUATION_ENABLED` и задржите вредност `true`. Ако сте `smart_punctuation.enable` подесили непосредно у PHP-у, преместите логичку вредност у кључ највишег нивоа `enabled` у `config/localization_punctuation.php`. Интерпункција је подразумевано искључена.

Пренесите прилагођене наводнике из `smart_punctuation.common` и `smart_punctuation.locales` у низове највишег нивоа `common` и `locales` у тој датотеци. Као кључеве низа `locales` користите канонске кодове. Наводници за сваки локал замењују одговарајуће кључеве у `common`. Ако сте копирали старији француски пример који користи `&nbsp;`, у нискама за наводнике користите прави непреломиви размак или `"\u{00A0}"`.

### Локализоване руте {#localized-routes}

Пренесите прилагођавања из `routes` у старим подешавањима у `config/localization_routes.php`.

| Шта сте прилагодили | Измена у `config/localization_routes.php` | Променљива окружења за преименовање |
| --- | --- | --- |
| Параметар локала, заглавље, колачић, кључ сесије или колона корисничког модела | Копирајте прилагођавања `names` и задржите преостале подразумеване уносе. | |
| Префикс додат називима локализованих рута | Преместите `name_prefix` у `prefix`. Ако је вредност празна, користи се `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Преусмеравања са руте с префиксом | Преместите `redirect_default` у `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Да ли помоћна функција за URL скрива резервни локал | Преместите `hide_default` у `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Мидлвери за групе локализованих рута | Преместите `group.middlewares.default` у `middlewares.default` за руте без префикса и `group.middlewares.prefix` у `middlewares.prefix` за руте с префиксом. Ажурирајте називе класа као што је приказано у [миграцији рута](#update-localized-routes-and-middleware). | |

### Преводи модела {#model-translations}

Пренесите прилагођавања из `models` у `config/localization_models.php`. Копирајте прилагођене вредности `suffix` и `helpers` на највиши ниво. Користите логичку вредност `filter` уместо старог кључа `filter.enabled`. Ако филтрирање и путању IDE помоћних датотека подешавате преко окружења, задржите `LOCALIZATION_FILTER_ENABLED` и `VENDOR_PATH`.

Команде за моделе сада подразумевано претражују `app/Models`. Ако сте се ослањали на стару претрагу целог `app/` или сте сами подесили `localization-private.models.directory`, наведите сваки директоријум који желите да претражите у низу `directories`. На пример, `'directories' => [app_path()]` задржава стару претрагу.

### Подешавања која користи код апликације {#configuration-used-by-application-code}

- Читање и упис преко `LaravelLang\Config\Facades\Config` замените Laravel позивима за подешавања са новим кључевима. На пример, замените `Config::shared()->models->filter->enabled` са `config('localization_models.filter')`. Ако желите читање с типовима, убризгајте `LaravelLang\Config\Services\ModelLangConfig` и прочитајте својство `filter`. За подешавања рута убризгајте `RouteLangConfig` из истог именског простора.
- Ако код чита метаподатке локала из `localization-private.map`, читајте `localization_map.locales`. Копирајте прилагођавања у низ `locales` у `config/localization_map.php`.
- Регистрацију преко `localization-private.plugins` и `localization-private.packages` замените [манифестом додатка](./plugins.md).
- Уклоните стари блок `translators` и стара подешавања `TRANSLATION_*` која је користио само `laravel-lang/translator`.

Када преместите прилагођавања и ажурирате упућивања у апликацији, уклоните `config/localization.php` и покрените `php artisan config:clear`.

## Премештање језичких датотека на канонске путање {#move-language-files-to-canonical-paths}

1. За `APP_LOCALE` и `APP_FALLBACK_LOCALE` подесите канонске кодове. Пренесите алијасе у `localization_main.aliases` да URL-ови и унос локала наставе да раде.
2. Датотеке и директоријуме који користе алијасе, попут `lang/pt-br.json` и `lang/pt-br/`, преименујте у `lang/pt_BR.json` и `lang/pt_BR/`. Проверите да ли се исти алијаси налазе у `lang/vendor/<package>/`. Ако постоје оба назива, спојите садржај и разрешите сукобе прилагођених превода пре уклањања путање с алијасом.
3. Инсталирајте сваки локал који је заступљен само у PHP преводима зависности помоћу `php artisan lang:add <locale>`. Списак инсталираних локала користи ставке непосредно у језичком директоријуму, JSON датотеке испод `lang/vendor/<package>/` и ефективне текући и резервни локал.
4. Покрените `php artisan lang:update` после миграције подешавања и додатака, па прегледајте разлике. Publisher може да задржи кључеве из интеграција које више не објављују дату датотеку. Задржите кључеве које апликација још користи, а застареле уклоните након провере места позива. За поновно генерисање или чување датотека погледајте [скрипте за објављивање и прилагођене преводе](#update-publication-scripts-and-keep-custom-translations).

## Ажурирање интеграција {#update-integrations}

### Locales {#locales}

Задржите увоз `LaravelLang\Locales\Facades\Locales` и ажурирајте позиве према подацима које сваки позивач користи.

| Претходни позив | Замена |
| --- | --- |
| `Locales::available()`, `installed()` или `protects()` који враћају објекте | Позовите исти метод на `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` или `protects()` | Позовите исти метод директно на `Locales` за колекцију ниски са алијасима. Проследите `false` за канонске кодове и позовите `->all()` ако је позивачу потребан низ. |
| `Locales::installed(false)` или `Locales::raw()->installed(false)` | `false` сада бира канонске кодове. И текући и резервни локал су укључени, чак и без језичких датотека. Замените и стари именовани аргумент `withProtects: false`. Да их изузмете, користите `Locales::installed(false)->diff(Locales::protects(false))->values()`. За објекте користите `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` или `Locales::raw()->notInstalled()` | Користите `Locales::data()->uninstalled()` за објекте или `Locales::uninstalled()` за ниске са алијасима. Проследите `false` за канонске ниске. |
| `Locales::getCurrent()` или `Locales::getDefault()` | Користите `Locales::data()->current()` за DTO или `Locales::current()` за ниску. Замените `Locales::raw()->getDefault()` са `Locales::current()`. Ако сте користили `Locales::raw()->getCurrent()` за вредност коју држи Laravel, користите `app()->getLocale()`. |
| `Locales::getFallback()` или `Locales::raw()->getFallback()` | Користите `Locales::data()->fallback()` за DTO или `Locales::fallback()` за ниску. Непознат резервни локал сада се разрешава у енглески. Непознат активни локал разрешава се у ефективни резервни. |
| `Locales::get($locale)` или `Locales::raw()->get($locale)` | Користите `isInstalled()` и изричито изаберите резервни локал. За подржани локал који можда није инсталиран, користите `isAvailable()` па `info()`. |
| `Locales::info($locale)` или `Locales::raw()->info($locale)` | Користите `Locales::info($locale)` и прочитајте `->alias` или `->code` када вам је потребна ниска. За текући локал проследите `null`. За непознат или празан код обрадите `LaravelLang\Exceptions\UnknownLocaleException` или проверите `isAvailable()` пре претраге. |

Да очувате старо понашање `get()`, које за неинсталирани или неважећи улаз користи текући локал, направите тај избор на месту позива.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Користите у путањама, редовима базе и другим трајно сачуваним подацима.
$alias = $locale->alias; // Користите у URL-овима и бирачима језика.
```

Ажурирајте руковаоце захтева који су раније прослеђивали резултат `raw()->get()` у `app()->setLocale()`. Када прихватите вредност из захтева, користите `Locales::set($locale)`. Овај позив прихвата кодове инсталираних локала, подешене алијасе, чланове енума и нови DTO, а Laravel-у поставља канонски код. За вредност захтева `pt-br`, Laravel добија `pt_BR`, док `Locales::current()` враћа `pt-br`. Позовите `Locales::current(false)` или `Locales::fallback(false)` када вам требају канонски кодови.

За друга места чувања у апликацији проверите вредност помоћу `isAvailable()` или `isInstalled()`, а затим употребите `fromAlias()` за разрешавање канонског кода. Користите `toAlias()` да форматирате канонски код за приказ. Уместо старог `Services\Resolver` користите `acceptsInput()` за проверу прихваћених PHP типова или `normalize()` за издвајање ниске из типизиране вредности. `normalize()` оставља ниске какве су прослеђене, укључујући алијасе попут `pt-br`.

Замените `LaravelLang\Locales\Data\LocaleData` са `LaravelLang\LocaleNames\LocaleData` у увозима, декларацијама типова и тестовима. Нови објекат само за читање има текстуална својства `code`, `alias`, `native` и `localized`. Уместо старог својства `code` за URL-ове и вредности интерфејса користите `alias`. Где сте читали `$data->locale`, користите `Locale::from($data->code)` уз увоз `LaravelLang\LocaleList\Locale`. Ако шаблони или форматери користе `type`, `regional` и `direction`, читајте их из `config("localization_map.locales.{$data->code}", [])`. Наведите сопствене подразумеване вредности за уносе који недостају. Да задржите старе подразумеване вредности, користите `Latn` за писмо, канонски код за регион и `Direction::LeftToRight` за смер.

Уклоните заставице `withCountry`, `withCurrency`, `withCountries` и `withCurrencies`. Експлицитно преузмите земљу и валуту за ред локала. Увозе старих типова `LaravelLang\Locales\Data\CountryData` и `CurrencyData` замените класама података из одговарајућих пакета за називе.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Ако сте на старој услузи позивали `localizedLocales()`, `localizedCountries()` или `localizedCurrencies()`, на одговарајућој услузи за називе користите `all(null)` за изворне и `all($displayLanguage)` за локализоване вредности. Непосредно убризгавање претходних класа `Services\Locales` или `Services\RawLocales` замените фасадом или одговарајућим `Services\MainService` и `Services\DataService`. Нови API-ји услуга описани су у [Локалима и називима](./locales.md).

#### Помоћни методи енума локала {#locale-enum-helpers}

Чланови енума `Locale` и `Direction` задржавају вредности. При надоградњи прилагођених спискова или бирача локала замените позиве уклоњених помоћних метода из `archtechx/enums`.

| Претходни помоћни метод | Замена |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Направите мапу назива у вредности помоћу `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Генеришите опције у приказу из `Locale::cases()`. За канонски код користите вредност члана, а за приказ алијаса `Locales::toAlias($case)`. |
| `fromName()` или `tryFromName()` | Пронађите члана по `name` у `Locale::cases()` и обрадите случај без подударања. За придружену вредност попут `pt_BR` користите `Locale::from()` или `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Направите мапу вредности у исте вредности помоћу `array_column(Locale::cases(), 'value', 'value')`. За списак вредности користите `Locale::values()`. |
| `Direction::values()` | Користите `array_column(Direction::cases(), 'value')`. |

### Замена API-ја за претрагу назива {#replace-the-name-lookup-apis}

Увозе из `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` и `LaravelLang\NativeLocaleNames` замените увозима из `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` и `LaravelLang\LocaleNames`. Разрешите `CountryNames`, `CurrencyNames` или `LocaleNames` преко Laravel контејнера или убризгајте услугу у своју класу.

| Претходна употреба | Замена |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` за изворне називе | `$names->all(null)` |
| `SortBy::None` | Изоставите аргумент сортирања. |
| `SortBy::Key` | Позовите `->sortKeys()` на враћеној колекцији. |
| `SortBy::Value` | Проследите `SortDirection::Ascending` методу `all()` за сортирање по локализованом називу. |

Користите `one($displayLocale, $entryLocale)` да преузмете познат унос. Кључ претраге је локал, на пример `fr`. Позив `app(CountryNames::class)->one('de', 'fr')` враћа земљу за француски са називом на немачком и `code` постављеним на `FR`. DTO-ови за земље и валуте налазе се у коренском именском простору својих пакета и излажу кључ претраге као `locale`. Поља `code` и даље чувају код земље или валуте.

Све три услуге враћају колекције чији су кључеви канонски локали. Ако сте користили низ ниски из старог пакета за називе локала, пресликајте сваки DTO на `localized`. Ако сте позивали `toArray()` на старим DTO-овима за земље или валуте или мењали њихова својства, изричито направите низ за апликацију. Нови DTO-ови су само за читање.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Проверите локале које корисник бира према одговарајућем каталогу пре позива `one()`. Непознат локал приказа може дати празан списак из `all()`. Проследите `null` када су апликацији потребни изворни називи. Ако код користи прилагођени енум са придруженим вредностима, проследите његову текстуалну вредност. [Локали и називи](./locales.md)

### Ажурирање локализованих рута и мидлвера {#update-localized-routes-and-middleware}

Увезите помоћну функцију из именског простора у PHP датотеке и Blade шаблоне који генеришу локализоване URL-ове. Можете и непосредно позвати `\LaravelLang\Routes\localizedRoute()`.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` и `LocalizationRoute::group()` региструју руте без префикса и руте са префиксом локала. Задржите постојећу групу и проверите позиве помоћне функције за URL. Она користи резервни алијас када је параметар локала изостављен. За бирач језика изричито проследите жељени алијас преко `Locales::toAlias($locale)` или `$localeData->alias`. Прослеђена ниска локала користи се без измена. Ако сте преименовали параметар, ускладите кључ низа са `localization_routes.names.parameter`.

Замените увозе и регистрације у рутама, `bootstrap/app.php` и прилагођеним списковима мидлвера. Све класе у овој табели су у `LaravelLang\Routes\Middlewares`.

| Претходна класа мидлвера | Замена |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Ако сте мидлвер за модел конструисали са механизмом аутентификације, проследите га као параметар Laravel мидлвера, на пример `LocalizationByModelMiddleware::class . ':admin'`. Механизам за разрешавање корисника очекује Eloquent модел. У сопственим поткласама мидлвера убризгајте `LaravelLang\Config\Services\RouteLangConfig`, читајте називе извора из `$this->routeConfig->name` и из `detect()` вратите `int|string|null`. Упућивања на `LaravelLang\Routes\Services\Route` замените са `LaravelLang\Routes\Services\RouteService`.

Тестирајте сваки извор који апликација користи. Откривање преко заглавља користи PHP Intl и проверава инсталиране језике према редоследу из `Accept-Language`. Прво покушава да пронађе целу ознаку, па основни језик. Каснији мидлвер може да замени локал из ранијег извора. Да би URL одређивао локал за руте са префиксом, у њиховом ланцу оставите само мидлвер за избор локала из URL-а.

Засебно тестирајте генерисање URL-а и захтеве. `localization_routes.hide` изоставља сегмент резервног локала при генерисању URL-а. `localization_routes.redirect` преусмерава захтеве са префиксом за текући локал на руту без префикса. Рута са називом и префиксом такође преусмерава неважећи или неинсталирани локал на руту без префикса.

У слушаоцима догађаја `LocaleHasBeenSetEvent` користите `$event->locale->code` за канонски код и `$event->locale->alias` за приказ. Подаци догађаја су сада `LaravelLang\LocaleNames\LocaleData`. Мидлвер шаље догађај при промени изабраног локала и чува канонски код у сесији. Ручне позиве `LocaleHasBeenSetEvent::dispatch($data)` замените са `event(new LocaleHasBeenSetEvent($data))`. [Локализоване руте](./routes.md)

### Миграција кода и података за преводе модела {#migrate-model-translation-code-and-data}

Проверите колону `locale` у свакој табели превода да пронађете редове са алијасима. За сваки алијас ажурирајте те редове на његов канонски код у миграцији података. Пре ажурирања проверите да ли исти `item_id` већ има ред за канонски код. Спојите све вредности које желите да задржите у један ред како бисте испунили јединствено ограничење `(item_id, locale)`. Верзије старе особине већ су уписивале канонске кодове, па миграцију заснујте на вредностима које су заиста сачуване у табелама.

Ажурирајте увозе и генерисане класе.

| Претходна употреба | Замена |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` или `UnknownModelPathException` | Исти назив класе у `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Ажурирајте редефиниције које бирају сопствену класу превода. |
| `getTranslationTable()` | Користите `translationQualifyColumn($column)` за квалификовану колону или `translationsRaw()->getRelated()->getTable()` за назив табеле. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` и `forgetTranslation()` прихватају ниску локала, `Locale` или нови `LocaleData`. Пре позива инсталирајте сваки локал који изричито прослеђујете овим методима. Читање без изричитог локала користи резервни ако је текућа вредност `null`. Изричит локал непосредно чита вредност тог локала. Ако позивате метод нижег нивоа `translation()`, проследите ниску локала или `null`. DTO претворите помоћу `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Уз алијас `de_CH` → `de-CH` и инсталирани швајцарски немачки, оба позива приступају реду сачуваном као `de_CH`. Када попуњавате више вредности преведеног поља, проследите мапу чији су кључеви локали, попут `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Индексирани спискови сада изазивају `InvalidLocaleInputException`. Сопствени код који поставља учитану везу `translations` треба да проследи Laravel колекцију модела превода.

Позовите `translated()`, `whereTranslation()` и сродне опсеге преко Eloquent градитеља упита. Проследите код или алијас као текстуални локал опсега. За `orderByTranslation()` користите `'asc'`, `'desc'` или `SortDirection`. Када преко `whereTranslation()` или `whereTranslationLike()` претражујете локал који није текући или резервни, подесите `localization_models.filter` на `false` или непосредно извршите упит преко `translationsRaw()`.

Ажурирајте потрошаче догађаја који позивају `$event->locale->value` или пореде `$event->locale` са енумом `Locale`. `TranslationHasBeenSetEvent` и `TranslationHasBeenForgetEvent` сада носе текстуални алијас за изричито наведен локал. Догађај постављања садржи `null` када позивалац изостави локал. Претворите алијас помоћу `Locales::fromAlias()` пре него што га употребите као трајно сачуван кључ. Догађај постављања покреће се при додели атрибута, пре `save()`. За ручно слање било ког од три догађаја превода модела користите `event(new …)`.

Ако задржавате генерисане IDE помоћне датотеке, покрените `php artisan lang:models-helper` после ажурирања увоза и путања за откривање модела. [Преводи модела](./models.md)

### Ажурирање скрипти за објављивање и чување прилагођених превода {#update-publication-scripts-and-keep-custom-translations}

| Претходна команда или претпоставка | Миграција |
| --- | --- |
| `lang:add <locale>` освежавао је само локале који се додају | Сада освежава све инсталиране локале. Проследите изричите кодове у скриптама и узмите у обзир ажурирање постојећих датотека локала. |
| `lang:reset` | За спајање користите `lang:update`. За поновну изградњу од почетка направите резервну копију прилагођених уноса, уклоните датотеке које намеравате да поново генеришете, затим покрените `lang:add` са изричитим кодовима локала за поновну изградњу. |
| `lang:rm <locale> --force` | Користите `lang:rm <locale>`. Текући и резервни локал Laravel-а су заштићени. |
| Провера само излазног кода `lang:rm` | Проверите сваки резултат. `DONE` потврђује уклањање, а `FAILED` указује на путању која остаје после покушаја уклањања. `PROTECTED`, `INVALID` и `SKIP` могу се појавити уз излазни код за успех. |

Ако Artisan позивате преко класе команде, замените `LaravelLang\Publisher\Console\Add`, `Remove` и `Update` класама `AddCommand`, `RemoveCommand` и `UpdateCommand`. За сопствени декоратор текста имплементирајте и повежите `LaravelLang\Publisher\Contracts\Decorator` уместо `Contracts\TextDecorator`. Његов `convert(string $locale, string $value): string` прима канонски локал.

Прво покретање `lang:update` обавите док су постојеће језичке датотеке под контролом верзија. Publisher спаја преводе у постојећа одредишта и замењује подударне кључеве, укључујући нумеричке. Када је могуће, чувајте прилагођене преводе у засебној датотеци превода апликације. Да сами одржавате цело генерисано одредиште, додајте образац његове путање у `localization_main.skip`. Проверите сваку вредност која се врати на изворни израз. Publisher користи изворни израз када у речницима нема превода.

Ако је претходно ажурирање преиндексирало кључеве у датотекама `http-statuses.php`, направите резервне копије погођених генерисаних датотека и уклоните их. Уклоните `'*/http-statuses.php'` из `localization_main.skip` ако сте тај унос додали да бисте заобишли проблем. Покрените `php artisan lang:update` да поново генеришете датотеке, па вратите прилагођене преводе под одговарајуће статусне кодове. [Управљање преводима](./translations.md)

### Замена старе регистрације додатака {#replace-legacy-plugin-registration}

Ако користите пакет превода треће стране, надоградите га на издање засновано на манифесту. Ако одржавате пакет, уклоните провајдера и Composer унос за откривање који региструју стари `LaravelLang\Publisher\Plugins\Provider`. Ако тај провајдер подешава и неповезане Laravel услуге, задржите те регистрације у редовном Laravel сервисном провајдеру.

1. Додајте `laravel_lang.json` у корен пакета инсталираног у `vendor/`. Сваки унос `Plugin::files()` претворите у путању `source` релативну у односу на директоријум пакета `source/` и путању `target` релативну у односу на језички директоријум апликације. У одредишту користите `{locale}`.
2. Пренесите правила за подударање Composer-а у манифест. Користите `vendor` и `version` за инсталирану зависност, а засебан унос са `project: true` за коренски Composer пројекат. Ако је стари додатак користио `$with_project_name = true` и подржавао оба режима, направите оба уноса.
3. Претворите речнике у `locales/<code>/<style>.json`. Вредности у изворним датотекама су кључеви за претрагу речника. На пример, PHP унос `'enable' => 'Enable audit trail'` и даље прави кључ апликације `audit.enable` када се објави у `{locale}/audit.php`. Његов превод сачувајте у речнику под `"Enable audit trail"`.
4. У локалној копији Mona репозиторијума покрените следећу Toolkit команду над старим пакетом. Она прави `default.json` и `inline.json` из старих JSON и PHP речника и преноси изузимања за локале. Прегледајте резултат и додајте га новом пакету заједно са манифестом и изворним датотекама. Преименујте директоријуме речника засноване на алијасима у канонске кодове.
5. Додајте зависност од текућег Publisher-а у Composer манифест пакета. Додајте и `laravel-lang/library` ако пакет очекује преводе из заједничког речника. Инсталирајте добијени пакет у апликацију и покрените `php artisan lang:update` да проверите објављени резултат.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

За постојеће апликације проверите која правила уграђених пакета важе после надоградње.

- Ако су вам и даље потребни Laravel-Lang преводи раније објављивани за Spark Paddle, Spark Stripe, Nova Dusk Suite или Nova Log Viewer, задржите те кључеве у датотекама апликације или преместите старе изворе и речнике у сопствени пакет.
- Преводи почетних комплета сада се подударају са Composer пољем `name` пројекта. Ако сте преименовали пројекат или користите комплет као зависност, додајте Composer назив комплета, на пример `laravel/livewire-starter-kit`, у `localization_main.force` да објавите његове преводе. [Додаци за преводе](./plugins.md)

### Ажурирање резервних JSON превода и алатки за превођење {#update-json-fallback-and-translation-tooling}

Ако код апликације увози или декларише тип `LaravelLang\JsonFallback\Translator`, промените тип у `LaravelLang\JsonFallback\OverridedTranslator` или се ослоните на Laravel уговор за преводиоца. Ако сте наследили старог преводиоца, преместите сопствено понашање у преводиоца апликације или сопствену услугу превођења. За неразрешени кључ замена позива Laravel повратну функцију за недостајуће преводе. Прегледајте ту функцију ако је апликација региструје и тестирајте кључеве који постоје у резервном JSON-у и PHP-у текућег језика, јер се резервни JSON проверава први. [Резервни JSON преводи](./translations.md#json-fallback)

Ако користите засебан пакет `laravel-lang/translator` за машинско превођење, пре уклањања зависности пренесите употребу његове фасаде `Translate` и све Google, DeepL или Yandex интеграције у изабрану услугу превођења. Преводе које су те скрипте већ генерисале сачувајте у речницима апликације или пакета.

Ако одржавате каталоге пакета помоћу `vendor/bin/lang` из `laravel-lang/status-generator`, покрените нове команде из локалне копије Mona репозиторијума после `composer install`.

| Претходна скрипта | Toolkit скрипта за употребу |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Прочитајте генерисане извештаје `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. За одржавање заједничког Library-а користите `sync:library`. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Ова команда прави директоријуме за познате локале. |
| `vendor/bin/lang download` | Проследите URL ZIP архиве преко `--url`, пуно одредиште преко `--directory` и путање из архиве преко поновљиве опције `--paths`. |
| `vendor/bin/lang translate` | Преместите машинско превођење у засебну услугу. Покрените Toolkit `status` да пронађете сачуване ниске које се и даље поклапају са извором. |

Потпуне примере потражите у [Toolkit командама](./toolkit.md).

## Провера надоградње {#verify-the-upgrade}

Покрените тестове апликације. Проверите објављену поруку у сваком важном локалу, резервни превод, локализовани URL са подешеним алијасом и превод модела сачуван под канонским кодом. Прегледајте генерисане датотеке и сачуване вредности локала заједно са изменама извора.

---
title: "Апгрэйд да Mona"
description: "Абнавіце залежнасці Laravel-Lang, канфігурацыю, апублікаваныя пераклады і інтэграцыі прыкладання."
---

Mona — гэта рэпазіторый, з якога пакеты Laravel-Lang публікуюцца разам. Па-ранейшаму выкарыстоўвайце асобныя пакеты Composer, патрэбныя вашаму прыкладанню.

:::info[Чаму версія 20?]

Экасістэма Laravel-Lang вырасла з `laravel-lang/lang`. Пакеты з’яўляліся ў розны час, і кожны меў уласную нумарацыю і графік рэлізаў. Нам даводзілася ўзгадняць змены ў некалькіх пакетах пры мажорных абнаўленнях, а карыстальнікам — высвятляць, якія версіі можна ўсталяваць разам.

Каб спрасціць выбар, мы стварылі `laravel-lang/common`, які задаваў сумяшчальныя версіі залежнасцей. Але нават з ім адно абнаўленне магло закрануць некалькі пакетаў з рознай нумарацыяй.

Да Mona нумары версій выглядалі так. Якія з іх вы б выбралі для Laravel 11 з PHP 8.2?

<details>
<summary>Версіі пакетаў да Mona</summary>

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
| Config | `1.17.1` (версія галіны 1.x, якую працягвалі выкарыстоўваць іншыя пакеты Laravel-Lang) і `2.3.2` (апошняя стабільная версія да Mona, якую гэтыя пакеты выкарыстоўвалі рэдка) |
| Publisher | `16.9.0` |

</details>

У манарэпазіторыі Mona мы выпускаем пакеты разам пад адным нумарам версіі. Раней найвышэйшы нумар быў у Publisher, таму наступнай магла б стаць версія `17.0`. Мы ўклалі шмат працы і душы ў аб’яднанне экасістэмы і выбралі версію 20, каб адзначыць гэты этап. Яе можна прачытаць і як **Laravel-Lang 2.0**. 🙂

:::

:::info[Коды і аліясы лакаляў]

Laravel-Lang цяпер выкарыстоўвае кананічныя коды лакаляў для значэнняў, якія захоўвае, у тым ліку для шляхоў публікацыі перакладаў і радкоў перакладу мадэляў. Бягучая і рэзервовая лакалі Laravel таксама выкарыстоўваюць кананічныя коды. Падчас працы прыкладання выкарыстоўвайце аліясы ў сегментах URL і пераключальніках мовы. Метады фасадаў, якія вяртаюць коды лакаляў, па змаўчанні вяртаюць наладжаны аліяс, а калі яго няма — кананічны код. Напрыклад, задайце `'pt_BR' => 'pt-br'` у `localization_main.aliases`, каб захаваць `/pt-br/account`, публікаваць пераклады ў `lang/pt_BR.json` і захоўваць у базе даных `pt_BR`.

:::

## Абнаўленне залежнасцей {#updating-dependencies}

Бягучая версія падтрымлівае Laravel 12 і 13 і патрабуе PHP `^8.3`. У `composer.json` прыкладання задайце абмежаванне `^20.0` для кожнага з пакетаў ніжэй, які ўваходзіць у `require` або `require-dev`. Калі ў вас усталяваны толькі `laravel-lang/common`, дастаткова змяніць толькі гэтую залежнасць.

Перад усталяваннем `20.x` або абнаўленнем да яго праверце, ці ёсць патрэбная версія пакета Composer на Packagist. Выпуск Mona і адпаведныя пакеты Composer могуць з’явіцца ў розны час.

| Прызначэнне | Пакеты Composer |
| --- | --- |
| Камплект | `laravel-lang/common` |
| Пакеты перакладаў | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Публікацыя і канфігурацыя | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Інтэграцыі прыкладання | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Агульныя ўтыліты | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Шаблон пакета | `laravel-lang/translations-template` |

Калі ваша ўсталяванне выкарыстоўвае толькі стандартную працэдуру перакладу, абнавіце залежнасці і апублікуйце пераклады нанова.

```bash
composer update
php artisan lang:update
```

Калі вы рэгістравалі правайдараў, публікавалі ўласныя налады, выкарыстоўваеце аліясы або староннія пакеты перакладаў ці выклікаеце API пакетаў у прыкладанні, перад паўторнай публікацыяй выканайце прыдатныя для вас крокі ніжэй.

## Замена перайменаваных або састарэлых залежнасцей {#replace-renamed-or-retired-dependencies}

Абнавіце ўсе прамыя залежнасці ад старых пакетаў назваў у `composer.json` перад запускам `composer update`.

| Ранейшы пакет Composer | Залежнасць з версіяй `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common ужо залежыць ад усіх трох пакетаў назваў. Абнавіце таксама код PHP. Гл. [замену API пошуку назваў](#replace-the-name-lookup-apis).

Для кожнага старонняга плагіна праверце, ці ёсць рэліз, сумяшчальны з новым Publisher. Перш чым выдаляць залежнасці `laravel-lang/translator` і `laravel-lang/status-generator`, перанясіце код, які іх выкарыстоўвае. Гл. [інструменты перакладу](#update-json-fallback-and-translation-tooling).

## Абнаўленне сэрвіс-правайдараў {#update-service-providers}

Laravel знаходзіць правайдараў Config, Publisher, Routes і Models праз маніфесты пакетаў. Калі вы разлічваеце на аўтаматычны пошук, абнавіце код прыкладання і тэсты, якія спасылаюцца на старыя класы правайдараў. Калі прыкладанне рэгіструе пакеты ўручную, замяніце класы ў тым жа спісе правайдараў, напрыклад у `bootstrap/providers.php` або `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Выдаліце ручную рэгістрацыю `LaravelLang\Locales\LaravelLangLocalesServiceProvider` і старых класаў `ServiceProvider` у `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` і `LaravelLang\StarterKits`. Гэтыя пакеты перакладаў знаходзяцца праз іх маніфесты.

Калі вы выкарыстоўваеце рэзервовы пошук у JSON, замяніце `LaravelLang\JsonFallback\TranslationServiceProvider` на `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` там, дзе ён зарэгістраваны. Так перавызначэнне правайдара перакладаў Laravel застанецца актыўным.

Абнавіце састарэлыя спасылкі на правайдараў перад запускам `composer update`, каб аўтаматычны пошук пакетаў мог запусціць прыкладанне. Калі скрыпт Composer па-ранейшаму завяршаецца памылкай праз загрузку старога класа, выканайце `composer update --no-scripts`, абнавіце спасылкі і запусціце `composer dump-autoload`, каб паўтарыць пошук.

## Перанос канфігурацыі {#migrate-configuration}

Калі вы змянялі `config/localization.php`, пакіньце яго як узор на час пераносу змен. Пакеты аўтаматычна падгружаюць новыя налады па змаўчанні. Апублікуйце [файлы канфігурацыі](./configure.md), якія трэба адрэдагаваць. Тэг `localization` публікуе ўсе пяць. Каб апублікаваць адзін файл, выкарыстоўвайце яго імя без `.php` як тэг.

```bash
php artisan vendor:publish --tag=localization
```

### Аліясы, стылі перакладу і фарматаванне файлаў {#aliases-translation-styles-and-file-formatting}

- Скапіруйце адпаведнасць `aliases` у `config/localization_main.php`. Пакіньце кананічныя коды ключамі, а аліясы — значэннямі, напрыклад `'de_CH' => 'de-CH'`.
- Замяніце `inline` на `styles` у `config/localization_main.php`. Задайце `'styles' => ['default']`, каб захаваць ранейшыя стандартныя паведамленні праверкі. Калі вы выкарыстоўвалі ўбудаваныя паведамленні, пакіньце `['default', 'inline']`, новае значэнне па змаўчанні. Выдаліце `LOCALIZATION_INLINE` і `LANG_PUBLISHER_INLINE` з асяроддзяў, у якіх яны зададзеныя.
- Выдаліце ключ `align` і пераменныя асяроддзя `LOCALIZATION_ALIGN` і `LANG_PUBLISHER_ALIGN`. Каб выраўнаваць аператары `=>` у апублікаваных масівах PHP, наладзьце фарматавальнік кода PHP вашага праекта і запусціце яго на моўных файлах пасля публікацыі. Напрыклад, правіла `binary_operator_spaces` у Laravel Pint можа прымяніць `align_single_space_minimal` да `=>`.

### Разумная пунктуацыя {#smart-punctuation}

Калі вы ўключалі разумную пунктуацыю, пераймянуйце `LOCALIZATION_SMART_ENABLED` у `LOCALIZATION_PUNCTUATION_ENABLED` і пакіньце значэнне `true`. Калі вы задавалі `smart_punctuation.enable` непасрэдна ў PHP, перанясіце лагічнае значэнне ў ключ верхняга ўзроўню `enabled` у `config/localization_punctuation.php`. Па змаўчанні пунктуацыя выключаная.

Перанясіце ўсе ўласныя двукоссі з `smart_punctuation.common` і `smart_punctuation.locales` у масівы верхняга ўзроўню `common` і `locales` у гэтым файле. Ключамі `locales` зрабіце кананічныя коды. Двукоссі кожнай лакалі перавызначаюць адпаведныя ключы ў `common`. Калі вы скапіявалі ранейшы французскі прыклад з `&nbsp;`, выкарыстоўвайце ў радках двукоссяў сапраўдны непарыўны прабел або `"\u{00A0}"`.

### Лакалізаваныя маршруты {#localized-routes}

Перанясіце свае перавызначэнні з `routes` старой канфігурацыі ў `config/localization_routes.php`.

| Што вы наладжвалі | Змена ў `config/localization_routes.php` | Пераменная асяроддзя для перайменавання |
| --- | --- | --- |
| Параметр лакалі, загаловак, cookie, ключ сесіі або слупок мадэлі карыстальніка | Скапіруйце свае перавызначэнні `names` і захавайце астатнія стандартныя запісы. | |
| Прэфікс імёнаў лакалізаваных маршрутаў | Перанясіце `name_prefix` у `prefix`. Калі значэнне пустое, выкарыстоўваецца `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Перанакіраванні з маршруту з прэфіксам | Перанясіце `redirect_default` у `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Ці хавае дапаможная функцыя URL рэзервовую лакаль | Перанясіце `hide_default` у `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Мідлвары для груп лакалізаваных маршрутаў | Перанясіце `group.middlewares.default` у `middlewares.default` для маршрутаў без прэфікса, а `group.middlewares.prefix` — у `middlewares.prefix` для маршрутаў з прэфіксам. Абнавіце імёны класаў мідлвараў паводле [дапаможніка па пераносе маршрутаў](#update-localized-routes-and-middleware). | |

### Пераклады мадэляў {#model-translations}

Перанясіце свае перавызначэнні з `models` у `config/localization_models.php`. Скапіруйце свае значэнні `suffix` і `helpers` на верхні ўзровень. Замест старога ключа `filter.enabled` выкарыстоўвайце лагічнае значэнне `filter`. Калі фільтрацыя і шлях дапаможных файлаў IDE задаюцца праз асяроддзе, захавайце `LOCALIZATION_FILTER_ENABLED` і `VENDOR_PATH`.

Цяпер каманды для мадэляў па змаўчанні скануюць `app/Models`. Калі вы разлічвалі на ранейшае сканаванне ўсяго `app/` або самастойна задавалі `localization-private.models.directory`, змясціце ўсе патрэбныя каталогі ў масіў `directories`. Напрыклад, задайце `'directories' => [app_path()]`, каб захаваць ранейшае сканаванне.

### Канфігурацыя ў кодзе прыкладання {#configuration-used-by-application-code}

- Замяніце чытанне і запіс праз `LaravelLang\Config\Facades\Config` на выклікі канфігурацыі Laravel з новымі ключамі. Напрыклад, замяніце `Config::shared()->models->filter->enabled` на `config('localization_models.filter')`. Для тыпізаванага чытання ўкараніце `LaravelLang\Config\Services\ModelLangConfig` і чытайце яго ўласцівасць `filter` або ўкараніце `RouteLangConfig` з той жа прасторы імёнаў для налад маршрутаў.
- Калі код чытае метаданыя лакаляў з `localization-private.map`, замест гэтага чытайце `localization_map.locales`. Скапіруйце перавызначэнні ў масіў `locales` у `config/localization_map.php`.
- Замяніце рэгістрацыю праз `localization-private.plugins` і `localization-private.packages` на [маніфест плагіна](./plugins.md).
- Выдаліце стары блок `translators` і ранейшыя налады `TRANSLATION_*`, якімі карыстаўся толькі `laravel-lang/translator`.

Пасля пераносу перавызначэнняў і абнаўлення спасылак у прыкладанні выдаліце `config/localization.php` і запусціце `php artisan config:clear`.

## Перанос моўных файлаў у кананічныя шляхі {#move-language-files-to-canonical-paths}

1. Задайце для `APP_LOCALE` і `APP_FALLBACK_LOCALE` кананічныя коды. Перанясіце аліясы ў `localization_main.aliases`, каб URL і ўваходныя значэнні лакаляў працягвалі працаваць.
2. Пераназавіце файлы і каталогі з аліясамі, напрыклад `lang/pt-br.json` і `lang/pt-br/`, у `lang/pt_BR.json` і `lang/pt_BR/`. Праверце наяўнасць тых самых аліясаў у `lang/vendor/<package>/`. Калі ёсць абедзве назвы, аб’яднайце іх змесціва і развяжыце канфлікты ўласных перакладаў, перш чым выдаляць шлях з аліясам.
3. Усталюйце кожную лакаль, якая прадстаўленая толькі перакладамі PHP залежнасцей, з дапамогай `php artisan lang:add <locale>`. Спіс усталяваных лакаляў улічвае запісы непасрэдна ў моўным каталогу, файлы JSON у `lang/vendor/<package>/` і фактычныя бягучую і рэзервовую лакалі.
4. Выканайце `php artisan lang:update` пасля пераносу канфігурацыі і плагінаў, затым праверце адрозненні. Publisher можа захаваць ключы інтэграцый, якія больш не публікуюць файлы. Пакіньце ключы, якімі яшчэ карыстаецца прыкладанне. Састарэлыя ключы выдаляйце пасля праверкі месцаў іх выкарыстання. Пра паўторную генерацыю або захаванне файлаў гл. [Скрыпты публікацыі і ўласныя пераклады](#update-publication-scripts-and-keep-custom-translations).

## Абнаўленне інтэграцый {#update-integrations}

### Лакалі {#locales}

Захавайце імпарт `LaravelLang\Locales\Facades\Locales` і абнавіце выклікі ў залежнасці ад даных, патрэбных кожнаму з іх.

| Ранейшы выклік | Замена |
| --- | --- |
| `Locales::available()`, `installed()` або `protects()`, якія вяртаюць аб’екты | Выклічце той жа метад у `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` або `protects()` | Выклічце той жа метад наўпрост у `Locales`, каб атрымаць калекцыю радкоў-аліясаў. Перадайце `false` для кананічных кодаў і выклічце `->all()`, калі патрэбны масіў. |
| `Locales::installed(false)` або `Locales::raw()->installed(false)` | Цяпер `false` выбірае кананічныя коды. Бягучая і рэзервовая лакалі ўключаюцца нават без моўных файлаў. Замяніце таксама стары найменаваны аргумент `withProtects: false`. Каб выключыць гэтыя лакалі, выкарыстоўвайце `Locales::installed(false)->diff(Locales::protects(false))->values()`. Каб атрымаць аб’екты, выкарыстоўвайце `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` або `Locales::raw()->notInstalled()` | Выкарыстоўвайце `Locales::data()->uninstalled()` для аб’ектаў або `Locales::uninstalled()` для радкоў-аліясаў. Перадайце `false`, каб атрымаць кананічныя радкі. |
| `Locales::getCurrent()` або `Locales::getDefault()` | Выкарыстоўвайце `Locales::data()->current()` для DTO або `Locales::current()` для радка. Замяніце `Locales::raw()->getDefault()` на `Locales::current()`. Калі для значэння з Laravel вы выкарыстоўвалі `Locales::raw()->getCurrent()`, выкарыстоўвайце `app()->getLocale()`. |
| `Locales::getFallback()` або `Locales::raw()->getFallback()` | Выкарыстоўвайце `Locales::data()->fallback()` для DTO або `Locales::fallback()` для радка. Невядомая рэзервовая лакаль цяпер вызначаецца як англійская. Для невядомай актыўнай лакалі выкарыстоўваецца фактычная рэзервовая. |
| `Locales::get($locale)` або `Locales::raw()->get($locale)` | Выкарыстоўвайце `isInstalled()` і выбірайце рэзервовую лакаль яўна. Калі падтрыманая лакаль можа быць неўсталяванай, выкарыстоўвайце `isAvailable()`, а пасля — `info()`. |
| `Locales::info($locale)` або `Locales::raw()->info($locale)` | Выкарыстоўвайце `Locales::info($locale)` і чытайце `->alias` або `->code`, калі патрэбны радок. Перадайце `null` для бягучай лакалі. Калі код невядомы або пусты, апрацоўвайце `LaravelLang\Exceptions\UnknownLocaleException` або праверце `isAvailable()` перад пошукам. |

Каб захаваць старыя паводзіны `get()` з пераходам да бягучай лакалі для неўсталяваных або несапраўдных уваходных значэнняў, рабіце гэты выбар у кодзе выкліку.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Выкарыстоўвайце ў шляхах, радках базы даных і іншых захаваных даных.
$alias = $locale->alias; // Выкарыстоўвайце ў URL і пераключальніках мовы.
```

Абнавіце апрацоўнікі запытаў, якія перадавалі вынік `raw()->get()` у `app()->setLocale()`. Атрымаўшы значэнне з запыту, выкарыстоўвайце `Locales::set($locale)`. Ён прымае коды ўсталяваных лакаляў, наладжаныя аліясы, варыянты enum-а і новы DTO і задае ў Laravel кананічны код. Калі значэнне з запыту — `pt-br`, Laravel атрымлівае `pt_BR`, а `Locales::current()` вяртае `pt-br`. Калі патрэбныя кананічныя коды, выклікайце `Locales::current(false)` або `Locales::fallback(false)`.

У іншых выпадках захавання значэнняў у прыкладанні правярайце значэнне праз `isAvailable()` або `isInstalled()`, пасля атрымлівайце яго кананічны код праз `fromAlias()`. Каб адфарматаваць кананічны код для паказу, выкарыстоўвайце `toAlias()`. Замяніце выкарыстанне старога `Services\Resolver` на `acceptsInput()` для праверкі прынятых тыпаў PHP або на `normalize()` для атрымання радка з тыпізаванага значэння. `normalize()` захоўвае радкі ў перададзеным выглядзе, у тым ліку аліясы накшталт `pt-br`.

Замяніце `LaravelLang\Locales\Data\LocaleData` на `LaravelLang\LocaleNames\LocaleData` у імпартах, указаннях тыпаў і тэстах. Новы аб’ект толькі для чытання мае радковыя ўласцівасці `code`, `alias`, `native` і `localized`. Замяніце старую ўласцівасць `code` на `alias` для URL і значэнняў інтэрфейсу. Замест чытання `$data->locale` выкарыстоўвайце `Locale::from($data->code)`, імпартаваўшы `LaravelLang\LocaleList\Locale`. Чытайце `type`, `regional` і `direction` з `config("localization_map.locales.{$data->code}", [])`, калі імі карыстаюцца вашы шаблоны або фарматавальнікі. Для адсутных запісаў задавайце свае стандартныя значэнні. Каб захаваць ранейшыя значэнні па змаўчанні, выкарыстоўвайце `Latn` для пісьма, кананічны код для рэгіёна і `Direction::LeftToRight` для кірунку тэксту.

Выдаліце сцягі `withCountry`, `withCurrency`, `withCountries` і `withCurrencies`. Атрымлівайце краіну і валюту для запісу лакалі яўна. Замяніце імпарт старых тыпаў `LaravelLang\Locales\Data\CountryData` і `CurrencyData` на класы даных з адпаведных пакетаў назваў.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Калі вы выклікалі `localizedLocales()`, `localizedCountries()` або `localizedCurrencies()` у старым сэрвісе, выкарыстоўвайце `all(null)` і `all($displayLanguage)` адпаведнага сэрвісу назваў для атрымання родных і лакалізаваных значэнняў адпаведна. Замяніце непасрэднае ўкараненне старых класаў `Services\Locales` або `Services\RawLocales` на фасад або адпаведныя `Services\MainService` і `Services\DataService`. Новыя API сэрвісаў апісаныя ў раздзеле [Лакалі і назвы](./locales.md).

#### Дапаможныя метады enum-а `Locale` {#locale-enum-helpers}

Варыянты enum-аў `Locale` і `Direction` захоўваюць свае значэнні. Пры абнаўленні ўласных спісаў лакаляў і пераключальнікаў замяніце выклікі выдаленых дапаможных метадаў з `archtechx/enums`.

| Ранейшы дапаможны метад | Замена |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Пабудуйце адпаведнасць імёнаў і значэнняў з дапамогай `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Згенеруйце варыянты ў прадстаўленні з `Locale::cases()`. Для кананічнага кода выкарыстоўвайце значэнне варыянта, а для аліяса для паказу — `Locales::toAlias($case)`. |
| `fromName()` або `tryFromName()` | Знайдзіце варыянт паводле яго `name` у `Locale::cases()` і апрацуйце адсутнасць супадзення. Для значэння варыянта, напрыклад `pt_BR`, выкарыстоўвайце `Locale::from()` або `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Пабудуйце адпаведнасць значэнняў самім сабе праз `array_column(Locale::cases(), 'value', 'value')`. Для спіса значэнняў выкарыстоўвайце `Locale::values()`. |
| `Direction::values()` | Выкарыстоўвайце `array_column(Direction::cases(), 'value')`. |

### Замена API пошуку назваў {#replace-the-name-lookup-apis}

Замяніце імпарт з `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` і `LaravelLang\NativeLocaleNames` на імпарт з `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` і `LaravelLang\LocaleNames`. Атрымлівайце `CountryNames`, `CurrencyNames` або `LocaleNames` праз кантэйнер Laravel або ўкараняйце сэрвіс у свой клас.

| Ранейшае выкарыстанне | Замена |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` для родных назваў | `$names->all(null)` |
| `SortBy::None` | Апусціце аргумент сартавання. |
| `SortBy::Key` | Выклічце `->sortKeys()` у вернутай калекцыі. |
| `SortBy::Value` | Перадайце `SortDirection::Ascending` у `all()`, каб адсартаваць паводле лакалізаванай назвы. |

Выкарыстоўвайце `one($displayLocale, $entryLocale)`, каб атрымаць вядомы запіс. Ключ пошуку — лакаль, напрыклад `fr`. Напрыклад, `app(CountryNames::class)->one('de', 'fr')` вяртае краіну для французскай лакалі з назвай па-нямецку і значэннем `FR` у полі `code`. DTO краін і валют знаходзяцца ў каранёвай прасторы імёнаў свайго пакета і адкрываюць доступ да ключа пошуку праз `locale`. Іх палі `code` па-ранейшаму змяшчаюць код краіны або валюты.

Усе тры сэрвісы вяртаюць калекцыі з кананічнымі лакалямі ў якасці ключоў. Калі вы выкарыстоўвалі масіў радкоў са старога пакета назваў лакаляў, пераўтварыце кожны DTO ў яго `localized`. Калі вы выклікалі `toArray()` для старых DTO краін ці валют або змянялі іх уласцівасці, збірайце масіў прыкладання яўна. Новыя DTO даступныя толькі для чытання.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Правярайце выбраныя карыстальнікам лакалі па адпаведным каталогу перад выклікам `one()`. Невядомая лакаль паказу можа прывесці да таго, што `all()` верне пусты спіс. Калі прыкладанню патрэбныя родныя назвы, перадайце `null`. Калі код выкарыстоўваў уласны enum з базавым тыпам, перадавайце яго радковае значэнне. [Лакалі і назвы](./locales.md)

### Абнаўленне лакалізаваных маршрутаў і мідлвараў {#update-localized-routes-and-middleware}

Імпартуйце дапаможную функцыю з яе прасторы імёнаў у файлах PHP і шаблонах Blade, якія генеруюць лакалізаваныя URL. Таксама можна выклікаць `\LaravelLang\Routes\localizedRoute()` непасрэдна.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` і `LocalizationRoute::group()` рэгіструюць маршруты і без прэфікса, і з прэфіксам лакалі. Пакіньце ранейшую групу і праверце выклікі дапаможнай функцыі URL. Калі параметр лакалі апушчаны, функцыя выкарыстоўвае рэзервовы аліяс. Для пераключальніка моў перадавайце патрэбны аліяс яўна праз `Locales::toAlias($locale)` або `$localeData->alias`. Перададзены радок лакалі выкарыстоўваецца без змен. Калі вы змянілі імя параметра лакалі, выкарыстоўвайце значэнне `localization_routes.names.parameter` як ключ масіву.

Замяніце імпарт і рэгістрацыю ў маршрутах, у `bootstrap/app.php` і ва ўласных спісах мідлвараў. Усе класы з табліцы знаходзяцца ў `LaravelLang\Routes\Middlewares`.

| Ранейшы мідлвар | Замена |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Калі вы стваралі мідлвар мадэлі з пэўным механізмам аўтэнтыфікацыі, перадавайце яго як параметр мідлвара Laravel, напрыклад `LocalizationByModelMiddleware::class . ':admin'`. Яго механізм вызначэння карыстальніка чакае мадэль Eloquent. Ва ўласных падкласах мідлвара ўкараняйце `LaravelLang\Config\Services\RouteLangConfig`, чытайце назвы крыніц з `$this->routeConfig->name` і вяртайце `int|string|null` з `detect()`. Замяніце непасрэдныя спасылкі на `LaravelLang\Routes\Services\Route` спасылкамі на `LaravelLang\Routes\Services\RouteService`.

Праверце кожную крыніцу, якой карыстаецца прыкладанне. PHP Intl правярае ўсталяваныя мовы ў парадку пераваг з `Accept-Language` і спачатку спрабуе поўны тэг, а потым базавую мову. Наступны мідлвар можа перавызначыць лакаль з папярэдняй крыніцы. Каб лакаль з URL мела прыярытэт для маршрутаў з прэфіксам, пакіньце ў іх ланцужку толькі мідлвар выбару лакалі з URL.

Асобна пратэстуйце генерацыю URL і апрацоўку запытаў. `localization_routes.hide` прыбірае сегмент рэзервовай лакалі пры стварэнні URL. `localization_routes.redirect` перанакіроўвае запыты з прэфіксам для бягучай лакалі на маршрут без прэфікса. Найменаваны маршрут з прэфіксам таксама перанакіроўвае запыты з несапраўднай або неўсталяванай лакаллю на маршрут без прэфікса.

У слухачах падзеі `LocaleHasBeenSetEvent` выкарыстоўвайце `$event->locale->code` для кананічнага кода і `$event->locale->alias` для паказу. Цяпер падзея перадае `LaravelLang\LocaleNames\LocaleData`. Мідлвар адпраўляе падзею, калі выбраная лакаль змяняецца, і захоўвае кананічны код у сесіі. Замяніце ручныя выклікі `LocaleHasBeenSetEvent::dispatch($data)` на `event(new LocaleHasBeenSetEvent($data))`. [Лакалізаваныя маршруты](./routes.md)

### Перанос кода і даных перакладу мадэляў {#migrate-model-translation-code-and-data}

Праверце слупок `locale` у кожнай табліцы перакладаў на наяўнасць радкоў з аліясамі. Для кожнага аліяса абнавіце гэтыя радкі да кананічнага кода ў міграцыі даных. Перад абнаўленнем праверце, ці мае той самы `item_id` радок з кананічным кодам. Аб’яднайце ўсе значэнні, якія хочаце захаваць, у адным радку, каб выканаць абмежаванне ўнікальнасці `(item_id, locale)`. Некаторыя версіі старога трэйта ўжо запісвалі кананічныя коды, таму арыентуйцеся на значэнні, якія сапраўды захоўваюцца ў вашых табліцах.

Абнавіце імпарт і згенераваныя класы.

| Ранейшае выкарыстанне | Замена |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` або `UnknownModelPathException` | Тое ж імя класа ў `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Абнавіце перавызначэнні, якія выбіраюць уласны клас перакладу. |
| `getTranslationTable()` | Выкарыстоўвайце `translationQualifyColumn($column)` для поўнай назвы слупка або `translationsRaw()->getRelated()->getTable()` для назвы табліцы. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` і `forgetTranslation()` прымаюць радок лакалі, `Locale` або новы `LocaleData`. Усталюйце ўсе яўна зададзеныя лакалі перад выклікам гэтых метадаў. Пры чытанні без яўнай лакалі выкарыстоўваецца рэзервовая, калі бягучае значэнне роўнае `null`. Пры яўна зададзенай лакалі чытаецца яе значэнне непасрэдна. Калі вы выклікаеце метад ніжэйшага ўзроўню `translation()`, вам трэба перадаць радок лакалі або `null`. Пераўтварайце DTO праз `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Калі зададзены аліяс `de_CH` → `de-CH` і ўсталявана швейцарская нямецкая лакаль, абодва выклікі звяртаюцца да радка, захаванага як `de_CH`. Пры запаўненні некалькіх значэнняў перакладзенага поля перадавайце масіў, у якім ключы — лакалі, напрыклад `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Індэксаваныя спісы цяпер выклікаюць `InvalidLocaleInputException`. Ва ўласным кодзе, які задае загружаную сувязь `translations`, варта перадаваць калекцыю Laravel з мадэлямі перакладаў.

Выклікайце `translated()`, `whereTranslation()` і звязаныя вобласці запытаў праз канструктар запытаў Eloquent. У якасці радка лакалі перадавайце код або аліяс. Для `orderByTranslation()` выкарыстоўвайце `'asc'`, `'desc'` або `SortDirection`. Калі вы запытваеце лакаль, адрозную ад бягучай і рэзервовай, праз `whereTranslation()` або `whereTranslationLike()`, задайце `localization_models.filter` значэнне `false` або звяртайцеся непасрэдна да `translationsRaw()`.

Абнавіце спажыўцоў падзей, якія выклікаюць `$event->locale->value` або параўноўваюць `$event->locale` з enum-ам `Locale`. Падзеі `TranslationHasBeenSetEvent` і `TranslationHasBeenForgetEvent` цяпер змяшчаюць радковы аліяс яўна зададзенай лакалі. Падзея прысваення змяшчае `null`, калі лакаль пры выкліку прапушчаная. Пераўтварыце аліяс праз `Locales::fromAlias()`, перш чым захоўваць яго як ключ. Падзея прысваення спрацоўвае пры заданні атрыбута, перад `save()`. Для адпраўкі любой з трох падзей перакладу мадэлі ўручную выкарыстоўвайце `event(new …)`.

Калі вы захоўваеце згенераваныя дапаможныя файлы IDE, выканайце `php artisan lang:models-helper` пасля абнаўлення імпарту і шляхоў пошуку мадэляў. [Пераклады мадэляў](./models.md)

### Абнаўленне скрыптоў публікацыі і захаванне ўласных перакладаў {#update-publication-scripts-and-keep-custom-translations}

| Ранейшая каманда або меркаванне | Перанос |
| --- | --- |
| `lang:add <locale>` абнаўляла толькі дададзеныя лакалі | Цяпер яна абнаўляе ўсе ўсталяваныя лакалі. У скрыптах перадавайце коды яўна і ўлічвайце абнаўленні наяўных файлаў лакаляў. |
| `lang:reset` | Для аб’яднання выкарыстоўвайце `lang:update`. Для поўнай паўторнай генерацыі зрабіце рэзервовую копію ўласных запісаў, выдаліце файлы, якія хочаце згенераваць нанова, і выканайце `lang:add` з яўнымі кодамі патрэбных лакаляў. |
| `lang:rm <locale> --force` | Выкарыстоўвайце `lang:rm <locale>`. Бягучая і рэзервовая лакалі Laravel абароненыя. |
| Праверка толькі кода завяршэння `lang:rm` | Правярайце кожны вынік. `DONE` пацвярджае выдаленне, а `FAILED` паказвае, што пасля спробы выдалення шлях застаўся. `PROTECTED`, `INVALID` і `SKIP` могуць суправаджацца паспяховым кодам завяршэння. |

Калі вы выклікаеце Artisan праз класы каманд, замяніце `LaravelLang\Publisher\Console\Add`, `Remove` і `Update` на `AddCommand`, `RemoveCommand` і `UpdateCommand`. Для ўласнага дэкаратара тэксту рэалізуйце і прывяжыце `LaravelLang\Publisher\Contracts\Decorator` замест `Contracts\TextDecorator`. Яго метад `convert(string $locale, string $value): string` атрымлівае кананічную лакаль.

Выканайце `lang:update` упершыню, калі наяўныя моўныя файлы знаходзяцца пад кантролем версій. Publisher аб’ядноўвае пераклады з наяўнымі мэтавымі файламі і замяняе ключы, якія супадаюць, у тым ліку лікавыя. Па магчымасці захоўвайце ўласныя пераклады ў асобным файле перакладу прыкладання. Калі вам трэба самастойна падтрымліваць увесь згенераваны файл, дадайце шаблон яго шляху ў `localization_main.skip`. Праверце ўсе значэнні, што вярнуліся да зыходнай фразы. Publisher выкарыстоўвае яе, калі слоўнікі не змяшчаюць перакладу.

Калі папярэдняе абнаўленне перанумаравала ключы ў файлах `http-statuses.php`, зрабіце рэзервовыя копіі закранутых згенераваных файлаў і выдаліце іх. Прыбярыце `'*/http-statuses.php'` з `localization_main.skip`, калі дадалі гэты запіс для абыходу праблемы. Выканайце `php artisan lang:update`, каб згенераваць файлы нанова, затым аднавіце ўласныя пераклады пад адпаведнымі кодамі стану. [Кіраванне перакладамі](./translations.md)

### Замена рэгістрацыі плагінаў папярэдніх версій {#replace-legacy-plugin-registration}

Калі вы карыстаецеся староннім пакетам перакладаў, абнавіце пакет да рэлізу з маніфестам. Калі вы суправаджаеце пакет, выдаліце правайдара і запіс аўтаматычнага пошуку Composer, якія рэгіструюць стары `LaravelLang\Publisher\Plugins\Provider`. Калі правайдар таксама наладжвае іншыя сэрвісы Laravel, захавайце іх рэгістрацыю ў звычайным сэрвіс-правайдары Laravel.

1. Дадайце `laravel_lang.json` у корань пакета, усталяванага ў `vendor/`. Пераўтварыце кожны запіс `Plugin::files()` у шлях `source` адносна каталога `source/` пакета і шлях `target` адносна моўнага каталога прыкладання. У мэтавым шляху выкарыстоўвайце `{locale}`.
2. Перанясіце ў маніфест правілы супастаўлення Composer. Выкарыстоўвайце `vendor` і `version` для ўсталяванай залежнасці і асобны запіс з `project: true` для каранёвага праекта Composer. Калі ваш стары плагін выкарыстоўваў `$with_project_name = true` і падтрымліваў абодва рэжымы, стварыце абодва запісы.
3. Пераўтварыце слоўнікі ў `locales/<code>/<style>.json`. Значэнні ў зыходных файлах служаць ключамі пошуку ў слоўніку. Напрыклад, запіс PHP `'enable' => 'Enable audit trail'` па-ранейшаму стварае ключ прыкладання `audit.enable` пры публікацыі ў `{locale}/audit.php`. Захоўвайце яго пераклад у слоўніку пад ключом `"Enable audit trail"`.
4. Запусціце каманду Toolkit ніжэй з лакальнай копіі Mona для пакета папярэдняй версіі. Яна створыць `default.json` і `inline.json` са старых слоўнікаў JSON і PHP і перанясе выключэнні лакаляў. Праверце вынік і дадайце яго ў новы пакет разам з маніфестам і зыходнымі файламі. Пераймянуйце ўсе каталогі слоўнікаў, названыя паводле аліясаў, у кананічныя коды.
5. Дадайце бягучы Publisher у залежнасці маніфеста Composer пакета. Дадайце таксама `laravel-lang/library`, калі пакет чакае перакладаў з агульнага слоўніка. Усталюйце атрыманы пакет у прыкладанне і выканайце `php artisan lang:update`, каб праверыць апублікаваны вынік.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Для наяўных прыкладанняў праверце, якія ўбудаваныя правілы пакетаў дзейнічаюць пасля абнаўлення.

- Калі вам па-ранейшаму патрэбныя раней апублікаваныя пераклады Laravel-Lang для Spark Paddle, Spark Stripe, Nova Dusk Suite або Nova Log Viewer, захавайце адпаведныя ключы ў файлах прыкладання або перанясіце старыя зыходныя файлы і слоўнікі ва ўласны пакет.
- Цяпер пераклады стартавых набораў супастаўляюцца з `name` праекта ў Composer. Калі вы перайменавалі праект або выкарыстоўваеце набор як залежнасць, дадайце імя набору ў Composer, напрыклад `laravel/livewire-starter-kit`, у `localization_main.force`, каб апублікаваць яго пераклады. [Плагіны перакладаў](./plugins.md)

### Абнаўленне рэзервовага пошуку ў JSON і інструментаў перакладу {#update-json-fallback-and-translation-tooling}

Калі код прыкладання імпартуе `LaravelLang\JsonFallback\Translator` або выкарыстоўвае яго як тып, замяніце тып на `LaravelLang\JsonFallback\OverridedTranslator` або залежце ад кантракта перакладчыка Laravel. Калі вы стварылі падклас старога перакладчыка, перанясіце ўласныя паводзіны ў перакладчык прыкладання або ва ўласны сэрвіс перакладу. Калі ключ не знойдзены, новы перакладчык выклікае функцыю зваротнага выкліку Laravel для адсутных перакладаў. Праверце яе, калі ваша прыкладанне яе рэгіструе, і пратэстуйце ключы, якія ёсць і ў рэзервовым JSON, і ў PHP бягучай мовы, бо спачатку правяраецца рэзервовы JSON. [Рэзервовы пошук у JSON](./translations.md#json-fallback)

Калі вы выкарыстоўваеце асобны пакет `laravel-lang/translator` для машыннага перакладу, перанясіце выклікі яго фасада `Translate` і ўсе інтэграцыі з Google, DeepL або Yandex у выбраны сэрвіс перакладу, перш чым выдаляць залежнасць. Захавайце пераклады, ужо атрыманыя гэтымі скрыптамі, у слоўніках прыкладання або пакета.

Калі вы абслугоўваеце каталогі пакетаў з дапамогай `vendor/bin/lang` з `laravel-lang/status-generator`, запускайце новыя каманды з лакальнай копіі Mona пасля `composer install`.

| Ранейшы скрыпт | Адпаведны скрыпт Toolkit |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Прагледзьце згенераваныя справаздачы `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Для абслугоўвання агульнага слоўніка Library выкарыстоўвайце `sync:library`. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Каманда стварае каталогі для вядомых лакаляў. |
| `vendor/bin/lang download` | Перадайце URL ZIP праз `--url`, поўны мэтавы шлях праз `--directory` і патрэбныя шляхі з архіва праз `--paths`, які можна паўтарыць. |
| `vendor/bin/lang translate` | Перанясіце машынны пераклад у асобны сэрвіс. Запусціце `status` з Toolkit, каб знайсці захаваныя радкі, якія ўсё яшчэ супадаюць з зыходнымі. |

Поўныя прыклады гл. у раздзеле [Каманды Toolkit](./toolkit.md).

## Праверка апгрэйду {#verify-the-upgrade}

Запусціце тэсты прыкладання. Праверце апублікаванае паведамленне ў кожнай важнай лакалі, рэзервовы пераклад, лакалізаваны URL з наладжаным аліясам і пераклад мадэлі, захаваны пад кананічным кодам. Праверце згенераваныя файлы і захаваныя значэнні лакаляў разам са зменамі ў зыходным кодзе.

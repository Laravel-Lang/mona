---
title: 升级到 Mona
description: 升级 Laravel-Lang 依赖、配置、已发布的译文和应用集成。
---

Mona 是统一发布 Laravel-Lang 各包的仓库。应用仍可继续使用各自需要的 Composer 包。

:::info[为什么是 20 版？]

Laravel-Lang 最初只有 `laravel-lang/lang`，后来逐渐发展成由多个包组成的生态。各个包创建于不同时间，有各自的版本号和发布节奏。每次大版本升级，维护者都要协调多个包的发布，用户也要弄清哪些版本可以一起安装。

为了简化这个过程，我们创建了 `laravel-lang/common`，在其中声明一组兼容的依赖。一次升级仍可能涉及多个独立编号的包。

比如在 Mona 推出之前，如果你的应用用的是 Laravel 11 和 PHP 8.2，这些包分别该装哪个版本？

<details>
<summary>Mona 推出前的包版本</summary>

| 包 | 版本 |
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
| Config | `1.17.1`（其他 Laravel-Lang 包继续使用的 1.x 版本）和 `2.3.2`（Mona 推出前的最新稳定版，其他包很少使用） |
| Publisher | `16.9.0` |

</details>

在 Mona 中，各个包使用同一个版本号，并一起发布。此前 Publisher 的版本号最高，沿用它的编号，下一次大版本就会是 `17.0`。我们花了很多心思把整个生态整合到同一个仓库，选择用 20 纪念这个里程碑。你也可以把它读作 **Laravel-Lang 2.0**。🙂

:::

:::info[语言区域代码和别名]

Laravel-Lang 现在使用规范语言区域代码存储数据，包括已发布的翻译文件路径和模型翻译记录。Laravel 当前和回退语言区域同样使用规范代码。运行时可在 URL 路径段和语言选择器中使用别名。返回语言区域代码的门面方法默认使用已配置的别名。未配置时返回规范代码。例如，在 `localization_main.aliases` 中配置 `'pt_BR' => 'pt-br'` 后，仍可使用 `/pt-br/account`，而文件发布到 `lang/pt_BR.json`，数据库中保存 `pt_BR`。

:::

## 更新依赖 {#updating-dependencies}

当前版本支持 Laravel 12 和 13，需要 PHP `^8.3`。如果下面的包出现在应用 `composer.json` 的 `require` 或 `require-dev` 中，请将相应版本约束设为 `^20.0`。如果仅安装了 `laravel-lang/common`，只需修改这一项。

安装 `20.x` 或升级至该版本前，请确认 Packagist 已提供所需的 Composer 软件包版本。Mona 发行版和对应的 Composer 软件包可能在不同时间发布。

| 用途 | Composer 包 |
| --- | --- |
| 套装 | `laravel-lang/common` |
| 翻译包 | `laravel-lang/lang`、`laravel-lang/starter-kits`、`laravel-lang/moonshine`、`laravel-lang/actions`、`laravel-lang/attributes`、`laravel-lang/http-statuses` |
| 发布和配置 | `laravel-lang/publisher`、`laravel-lang/config`、`laravel-lang/json-fallback`, `laravel-lang/library` |
| 应用集成 | `laravel-lang/locales`、`laravel-lang/locale-list`、`laravel-lang/routes`、`laravel-lang/models` |
| 共享工具包 | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| 翻译包模板 | `laravel-lang/translations-template` |

如果仅使用标准翻译流程，请更新依赖并重新发布译文。

```bash
composer update
php artisan lang:update
```

如果注册过服务提供者、发布过自定义配置、使用别名或第三方翻译包，或在应用中调用包的 API，请在重新发布前按下文迁移相关部分。

## 替换已改名或已废弃的依赖 {#replace-renamed-or-retired-dependencies}

运行 `composer update` 前，请在 `composer.json` 中更新对旧版名称包的直接依赖。

| 旧 Composer 包 | 所需的 `^20.0` 依赖 |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common 已依赖这三个名称包。也请更新 PHP 代码，见[替换名称查询 API](#replace-the-name-lookup-apis)。

检查每个第三方插件是否有与新版 Publisher 兼容的版本。移除 `laravel-lang/translator` 或 `laravel-lang/status-generator` 前，请先迁移对它们的用法，见[翻译工具](#update-json-fallback-and-translation-tooling)。

## 更新服务提供者 {#update-service-providers}

Laravel 会通过包清单发现 Config、Publisher、Routes 和 Models 的服务提供者。如果依赖自动发现，请更新应用代码或测试中对旧服务提供者类的引用。如果应用手动注册这些包，请在原有服务提供者列表中进行下列替换，例如 `bootstrap/providers.php` 或 `config/app.php`。

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

删除对 `LaravelLang\Locales\LaravelLangLocalesServiceProvider` 的手动注册，以及对 `LaravelLang\Actions`、`LaravelLang\Attributes`、`LaravelLang\HttpStatuses`、`LaravelLang\Lang`、`LaravelLang\MoonShine` 和 `LaravelLang\StarterKits` 下旧版 `ServiceProvider` 类的手动注册。这些翻译包通过清单发现。

如果使用 JSON 回退，请在注册处将 `LaravelLang\JsonFallback\TranslationServiceProvider` 替换为 `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider`，以保持对 Laravel 翻译服务提供者的覆盖。

运行 `composer update` 前，请更新旧服务提供者的引用，以便自动发现流程能启动应用。如果 Composer 脚本仍因加载旧类而失败，请运行 `composer update --no-scripts`，更正引用，再运行 `composer dump-autoload` 以重新进行自动发现。

## 迁移配置 {#migrate-configuration}

如果自定义过 `config/localization.php`，请在迁移期间保留它作为参考。各包会自动加载新版默认设置。发布需要修改的[配置文件](./configure.md)。`localization` 标签会发布五个文件，也可以使用去掉 `.php` 后的文件名作为标签，单独发布某个文件。

```bash
php artisan vendor:publish --tag=localization
```

### 别名、翻译风格和文件格式 {#aliases-translation-styles-and-file-formatting}

- 将 `aliases` 映射复制到 `config/localization_main.php`。仍然使用规范代码作为键、别名作为值，例如 `'de_CH' => 'de-CH'`。
- 在 `config/localization_main.php` 中用 `styles` 替换 `inline`。将其设为 `'styles' => ['default']` 可沿用旧版默认的标准验证消息。如果原来使用行内消息，就保留新版默认值 `['default', 'inline']`。从设置过它们的环境中删除 `LOCALIZATION_INLINE` 和 `LANG_PUBLISHER_INLINE`。
- 删除 `align` 键和 `LOCALIZATION_ALIGN`、`LANG_PUBLISHER_ALIGN` 环境变量。若要对齐发布的 PHP 数组中的 `=>` 运算符，请配置项目的 PHP 代码格式化工具，并在发布后对翻译文件运行它。例如，可用 Laravel Pint 的 `binary_operator_spaces` 规则对 `=>` 应用 `align_single_space_minimal`。

### 智能标点 {#smart-punctuation}

如果启用了智能标点，请将 `LOCALIZATION_SMART_ENABLED` 改名为 `LOCALIZATION_PUNCTUATION_ENABLED`，并保持其值为 `true`。如果直接在 PHP 中设置了 `smart_punctuation.enable`，请将其布尔值移到 `config/localization_punctuation.php` 顶层的 `enabled` 键。默认不启用智能标点。

将 `smart_punctuation.common` 和 `smart_punctuation.locales` 中的自定义引号移到该文件顶层的 `common` 和 `locales` 数组。`locales` 使用规范代码作为键。各语言区域的引号会覆盖 `common` 中对应的键。如果复制过使用 `&nbsp;` 的旧版法语示例，请在引号字符串中使用真正的不换行空格，或使用 `"\u{00A0}"`。

### 本地化路由 {#localized-routes}

将旧配置 `routes` 下的覆盖项移到 `config/localization_routes.php`。

| 原自定义项 | 在 `config/localization_routes.php` 中的改动 | 需要改名的环境变量 |
| --- | --- | --- |
| 语言区域参数、请求头、Cookie、会话键或用户模型字段 | 复制 `names` 下的覆盖项，保留其余默认条目。 | |
| 本地化路由名称的前缀 | 将 `name_prefix` 改为 `prefix`。值为空时使用 `localized.`。 | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| 从带前缀的路由重定向 | 将 `redirect_default` 改为 `redirect`。 | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| URL 辅助函数是否隐藏回退语言区域 | 将 `hide_default` 改为 `hide`。 | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| 本地化路由组使用的中间件 | 对于无前缀的路由，将 `group.middlewares.default` 移到 `middlewares.default`。对于带前缀的路由，将 `group.middlewares.prefix` 移到 `middlewares.prefix`。按[路由迁移](#update-localized-routes-and-middleware)中的说明更新中间件类名。 | |

### 模型翻译 {#model-translations}

将 `models` 下的覆盖项移到 `config/localization_models.php`。将自定义的 `suffix` 和 `helpers` 值复制到顶层。用布尔值 `filter` 替换旧的 `filter.enabled` 键。如果通过环境变量控制筛选和 IDE 辅助文件路径，请保留 `LOCALIZATION_FILTER_ENABLED` 和 `VENDOR_PATH`。

模型命令现在默认扫描 `app/Models`。如果之前依赖对整个 `app/` 的扫描，或者自行设置过 `localization-private.models.directory`，请将每个待扫描目录加入 `directories` 数组。例如，设置 `'directories' => [app_path()]` 可沿用旧版扫描范围。

### 应用代码使用的配置 {#configuration-used-by-application-code}

- 将通过 `LaravelLang\Config\Facades\Config` 进行的读写改为用 Laravel 的配置函数访问新键。例如，用 `config('localization_models.filter')` 替换 `Config::shared()->models->filter->enabled`。如果希望以带类型的方式读取，可注入 `LaravelLang\Config\Services\ModelLangConfig` 并读取其 `filter` 属性。路由设置可通过同一命名空间下的 `RouteLangConfig` 注入。
- 如果代码从 `localization-private.map` 读取语言区域元数据，请改为读取 `localization_map.locales`。将覆盖项复制到 `config/localization_map.php` 的 `locales` 数组中。
- 将使用 `localization-private.plugins` 和 `localization-private.packages` 的注册方式替换为[插件清单](./plugins.md)。
- 删除旧版的 `translators` 配置块及只供 `laravel-lang/translator` 使用的旧 `TRANSLATION_*` 设置。

迁移覆盖项并更新应用代码中的引用后，删除 `config/localization.php` 并运行 `php artisan config:clear`。

## 将翻译文件移到使用规范代码的路径 {#move-language-files-to-canonical-paths}

1. 将 `APP_LOCALE` 和 `APP_FALLBACK_LOCALE` 设置为规范代码。将旧别名移到 `localization_main.aliases`，使 URL 和语言区域输入继续有效。
2. 重命名使用别名的文件和目录，例如将 `lang/pt-br.json` 和 `lang/pt-br/` 改为 `lang/pt_BR.json` 和 `lang/pt_BR/`。也请检查 `lang/vendor/<package>/` 下是否存在相同别名。如果两套名称同时存在，请先合并内容并解决自定义译文冲突，再移除别名路径。
3. 对于只有第三方包 PHP 译文的语言区域，请逐一运行 `php artisan lang:add <locale>`。已安装列表根据翻译目录下的直接子项、`lang/vendor/<package>/` 下的 JSON 文件，以及实际采用的当前及回退语言区域确定。
4. 完成配置和插件迁移后，运行 `php artisan lang:update` 并检查改动。Publisher 可能保留来自某些已不再发布该文件的集成的键。请保留应用仍使用的键，检查调用方后再移除过时的键。重新生成或保留文件请参阅[发布脚本和自定义译文](#update-publication-scripts-and-keep-custom-translations)。

## 更新集成 {#update-integrations}

### 语言区域 {#locales}

保留对 `LaravelLang\Locales\Facades\Locales` 的导入，并根据各调用方所需的数据更新调用方式。

| 旧调用 | 替代方式 |
| --- | --- |
| 返回对象的 `Locales::available()`、`installed()` 或 `protects()` | 在 `Locales::data()` 上调用同名方法。 |
| `Locales::raw()->available()`、`installed()` 或 `protects()` | 直接在 `Locales` 上调用同名方法，即可获得使用别名的字符串集合。传入 `false` 可获取规范代码，如果调用方需要数组，可再调用 `->all()`。 |
| `Locales::installed(false)` 或 `Locales::raw()->installed(false)` | `false` 现在用于选择规范代码。即使没有翻译文件，仍会包含当前和回退语言区域。也请替换旧的命名参数 `withProtects: false`。使用 `Locales::installed(false)->diff(Locales::protects(false))->values()` 排除它们。对于对象，请使用 `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`。 |
| `Locales::notInstalled()` 或 `Locales::raw()->notInstalled()` | 对象使用 `Locales::data()->uninstalled()`，使用别名的字符串则用 `Locales::uninstalled()`。传入 `false` 可获取规范代码字符串。 |
| `Locales::getCurrent()` 或 `Locales::getDefault()` | 使用 `Locales::data()->current()` 获取 DTO，或用 `Locales::current()` 获取字符串。用 `Locales::current()` 替换 `Locales::raw()->getDefault()`。如果原来依赖 `Locales::raw()->getCurrent()` 获取 Laravel 保存的原值，请改用 `app()->getLocale()`。 |
| `Locales::getFallback()` 或 `Locales::raw()->getFallback()` | DTO 使用 `Locales::data()->fallback()`，字符串使用 `Locales::fallback()`。未知的回退语言区域现在会解析为英语，未知的当前语言区域会解析为实际采用的回退语言区域。 |
| `Locales::get($locale)` 或 `Locales::raw()->get($locale)` | 用 `isInstalled()` 检查，并显式选择回退语言区域。对于受支持但可能未安装的语言区域，先调用 `isAvailable()`，再调用 `info()`。 |
| `Locales::info($locale)` 或 `Locales::raw()->info($locale)` | 使用 `Locales::info($locale)`，需要字符串时读取 `->alias` 或 `->code`。传入 `null` 可查询当前语言区域。对于未知或空代码，请处理 `LaravelLang\Exceptions\UnknownLocaleException`，或在查询前用 `isAvailable()` 检查。 |

如果希望保留旧版 `get()` 在输入无效或未安装时使用当前语言区域的行为，请在调用方做此判断。

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // 在路径、数据库记录和其他持久化数据中使用此值。
$alias = $locale->alias; // 在 URL 和语言选择器中使用此值。
```

如果请求处理程序曾将 `raw()->get()` 的结果传给 `app()->setLocale()`，请更新该处理程序。接收请求值后，使用 `Locales::set($locale)`。此方法接受已安装的语言区域代码、已配置的别名、枚举项和新版 DTO，并将规范代码赋给 Laravel。例如，请求值为 `pt-br` 时，Laravel 得到 `pt_BR`，而 `Locales::current()` 返回 `pt-br`。需要规范代码时，调用 `Locales::current(false)` 或 `Locales::fallback(false)`。

应用中有其他持久化操作时，先用 `isAvailable()` 或 `isInstalled()` 验证该值，再用 `fromAlias()` 解析出规范代码。显示规范代码时，可用 `toAlias()` 转换成别名。将旧版 `Services\Resolver` 的用法替换为 `acceptsInput()` 以检查可接受的 PHP 类型，或用 `normalize()` 从带类型的值提取字符串。`normalize()` 会原样保留传入的字符串，包括 `pt-br` 这类别名。

在导入、类型声明和测试中，将 `LaravelLang\Locales\Data\LocaleData` 替换为 `LaravelLang\LocaleNames\LocaleData`。新的只读对象包含 `code`、`alias`、`native` 和 `localized` 字符串属性。原先为 URL 和界面提供值而读取旧 `code` 属性的地方，改用 `alias`。原来读取 `$data->locale` 的地方，请导入 `LaravelLang\LocaleList\Locale` 并改用 `Locale::from($data->code)`。如果模板或格式化程序使用 `type`、`regional` 和 `direction`，请从 `config("localization_map.locales.{$data->code}", [])` 读取，并为缺失条目提供默认值。若要沿用旧版默认值，请以 `Latn` 为文字系统、规范代码为地区、`Direction::LeftToRight` 为书写方向。

移除 `withCountry`、`withCurrency`、`withCountries` 和 `withCurrencies` 标志。请为语言区域条目显式查询国家或地区和货币。将旧版 `LaravelLang\Locales\Data\CountryData` 和 `CurrencyData` 类型的导入替换为相应名称包中的数据类。

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

如果曾在旧服务中调用 `localizedLocales()`、`localizedCountries()` 或 `localizedCurrencies()`，请在相应名称服务中使用 `all(null)` 和 `all($displayLanguage)`，分别获取本地名称和本地化名称。将旧版 `Services\Locales` 或 `Services\RawLocales` 类的直接注入替换为门面，或注入相应的 `Services\MainService` 和 `Services\DataService`。新版服务 API 请参阅[语言区域和名称](./locales.md)。

#### 语言区域枚举辅助方法 {#locale-enum-helpers}

`Locale` 和 `Direction` 的枚举项保留原值。升级自定义语言区域列表或选择器时，请替换已移除的 `archtechx/enums` 辅助方法。

| 旧辅助方法 | 替代方式 |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | 使用 `array_column(Locale::cases(), 'value', 'name')` 构建从名称到值的映射。 |
| `stringOptions()` | 在视图中通过 `Locale::cases()` 生成选项。用枚举项的值作为规范代码，或用 `Locales::toAlias($case)` 获取显示用的别名。 |
| `fromName()` 或 `tryFromName()` | 在 `Locale::cases()` 中按 `name` 查找枚举项，并处理找不到匹配项的情况。对于 `pt_BR` 这样的枚举标量值，使用 `Locale::from()` 或 `Locale::tryFrom()`。 |
| `Locale::onlyValues()` | 使用 `array_column(Locale::cases(), 'value', 'value')` 构建从值到值的映射。仅需值列表时，用 `Locale::values()`。 |
| `Direction::values()` | 使用 `array_column(Direction::cases(), 'value')`。 |

### 替换名称查询 API {#replace-the-name-lookup-apis}

将 `LaravelLang\NativeCountryNames`、`LaravelLang\NativeCurrencyNames` 和 `LaravelLang\NativeLocaleNames` 的导入改为 `LaravelLang\CountryNames`、`LaravelLang\CurrencyNames` 和 `LaravelLang\LocaleNames`。通过 Laravel 容器解析 `CountryNames`、`CurrencyNames` 或 `LocaleNames`，或在类中注入相应服务。

| 旧用法 | 替代方式 |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| 用 `SomeNames::get()` 获取本地名称 | `$names->all(null)` |
| `SortBy::None` | 省略排序参数。 |
| `SortBy::Key` | 对返回的集合调用 `->sortKeys()`。 |
| `SortBy::Value` | 向 `all()` 传入 `SortDirection::Ascending`，按本地化名称排序。 |

使用 `one($displayLocale, $entryLocale)` 获取已知条目。查找键为 `fr` 等语言区域。例如，`app(CountryNames::class)->one('de', 'fr')` 返回法语对应的国家，其国家名称使用德语，且 `code` 为 `FR`。国家或地区及货币的 DTO 位于各自包的根命名空间中，使用 `locale` 暴露查找键。`code` 字段仍保存国家或地区代码或货币代码。

三个服务都返回以规范语言区域代码为键的集合。如果原来使用旧版语言区域名称包返回的字符串数组，请将各 DTO 映射为其 `localized` 属性。如果原来对国家或地区及货币 DTO 调用 `toArray()`，或修改了它们的属性，请显式构建应用所需的数组。新版 DTO 为只读对象。

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

调用 `one()` 前，请用相应目录验证用户选择的语言区域。显示语言区域未知时，`all()` 可能返回空列表。应用需要本地名称时，传入 `null`。如果原来使用自定义的有标量值的枚举，请传入其字符串值。[语言区域和名称](./locales.md)

### 更新本地化路由和中间件 {#update-localized-routes-and-middleware}

在生成本地化 URL 的 PHP 和 Blade 模板中，导入带命名空间的辅助函数。也可直接调用 `\LaravelLang\Routes\localizedRoute()`。

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` 和 `LocalizationRoute::group()` 同时注册无前缀路由和带语言区域前缀的路由。请保留原有分组，并检查 URL 辅助函数的调用。省略语言区域参数时，它会使用回退语言区域的别名。对于语言切换器，请通过 `Locales::toAlias($locale)` 或 `$localeData->alias` 显式传入所需别名。传入的语言区域字符串将原样使用。如果改过参数名，请使数组键与 `localization_routes.names.parameter` 一致。

替换路由、`bootstrap/app.php` 和自定义中间件列表中的导入和注册。下表中的类都在 `LaravelLang\Routes\Middlewares` 中。

| 旧中间件 | 替代方式 |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

如果创建模型中间件时指定了身份验证守卫，请将其作为 Laravel 中间件参数传入，例如 `LocalizationByModelMiddleware::class . ':admin'`。其用户解析器期望收到 Eloquent 模型。对于自定义中间件子类，请注入 `LaravelLang\Config\Services\RouteLangConfig`，从 `$this->routeConfig->name` 读取来源名称，并从 `detect()` 返回 `int|string|null`。将对 `LaravelLang\Routes\Services\Route` 的直接引用替换为 `LaravelLang\Routes\Services\RouteService`。

测试应用使用的每个来源。请求头检测借助 PHP Intl，按 `Accept-Language` 的偏好顺序检查已安装的语言区域，先尝试完整的语言标签，再尝试基础语言。后运行的中间件可覆盖先前选定的语言区域。要让 URL 决定带前缀路由的语言区域，请在这些路由的中间件链中仅保留从 URL 选择语言区域的中间件。

请分别测试 URL 生成和请求处理。`localization_routes.hide` 在生成 URL 时省略回退语言区域的路径段。`localization_routes.redirect` 会将当前语言区域下带前缀的请求重定向到无前缀路由。带前缀且已命名的路由也会将无效或未安装的语言区域重定向到无前缀路由。

在 `LocaleHasBeenSetEvent` 的监听器中，使用 `$event->locale->code` 获取规范代码，使用 `$event->locale->alias` 获取显示用的别名。事件数据现在为 `LaravelLang\LocaleNames\LocaleData`。选中的语言区域发生变更时，中间件会派发事件，并在会话中保存规范代码。将手动调用的 `LocaleHasBeenSetEvent::dispatch($data)` 替换为 `event(new LocaleHasBeenSetEvent($data))`。[本地化路由](./routes.md)

### 迁移模型翻译代码和数据 {#migrate-model-translation-code-and-data}

检查各翻译表的 `locale` 列是否含有使用别名的记录。对每个别名，通过数据迁移将其记录改为规范代码。更新前，请检查同一个 `item_id` 是否已有使用规范代码的记录。将要保留的值合并到一条记录中，以满足 `(item_id, locale)` 的唯一约束。旧版 trait 的一些版本已经使用规范代码，请根据表中的实际存储值执行迁移。

更新导入和生成的类。

| 旧用法 | 替代方式 |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` 或 `UnknownModelPathException` | `LaravelLang\Exceptions` 中的同名类。 |
| `translationModelName()` | `translationModel(): string`。更新为自定义翻译类提供选择逻辑的覆盖方法。 |
| `getTranslationTable()` | 使用 `translationQualifyColumn($column)` 获取带表名的列名，或用 `translationsRaw()->getRelated()->getTable()` 获取表名。 |

`getTranslation()`、`setTranslation()`、`hasTranslated()` 和 `forgetTranslation()` 接受语言区域字符串、`Locale` 或新版 `LocaleData`。调用这些方法前，请先安装要显式指定的语言区域。读取时，如果未显式指定语言区域且当前值为 `null`，将读取回退译文。显式指定语言区域时，直接读取对应的值。调用较底层的 `translation()` 方法时，传入语言区域字符串或 `null`。如需转换 DTO，请使用 `$locale->code`。

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

配置 `de_CH` → `de-CH` 别名并安装瑞士德语后，两种调用均指向以 `de_CH` 存储的记录。要为可翻译字段填充多个值，请传入以语言区域为键的映射，例如 `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`。按数字索引的列表现在会抛出 `InvalidLocaleInputException`。为已加载的 `translations` 关联赋值的自定义代码应传入由翻译模型组成的 Laravel 集合。

通过 Eloquent 查询构建器调用 `translated()`、`whereTranslation()` 及相关作用域。向作用域传入代码或别名作为语言区域字符串。`orderByTranslation()` 使用 `'asc'`、`'desc'` 或 `SortDirection`。若通过 `whereTranslation()` 或 `whereTranslationLike()` 查询当前和回退语言区域之外的译文，请将 `localization_models.filter` 设为 `false`，或直接查询 `translationsRaw()`。

更新调用 `$event->locale->value` 或将 `$event->locale` 与 `Locale` 枚举项比较的事件处理代码。现在，显式指定语言区域时，`TranslationHasBeenSetEvent` 和 `TranslationHasBeenForgetEvent` 中保存字符串别名。省略语言区域时，设置事件的该属性为 `null`。将别名用作持久化键前，请用 `Locales::fromAlias()` 转换它。设置事件在属性赋值时触发，先于 `save()`。如需手动派发任意一种模型翻译事件，共三种，请使用 `event(new …)`。

如果保留已生成的 IDE 辅助文件，请在更新导入及模型扫描路径后运行 `php artisan lang:models-helper`。[模型翻译](./models.md)

### 更新发布脚本并保留自定义译文 {#update-publication-scripts-and-keep-custom-translations}

| 旧命令或原假设 | 迁移方式 |
| --- | --- |
| `lang:add <locale>` 仅更新新增的语言区域 | 现在会更新所有已安装的语言区域。请在脚本中显式传入代码，并考虑现有翻译文件也会更新。 |
| `lang:reset` | 合并时使用 `lang:update`。需要清理后重新生成时，先备份自定义条目，删除要重新生成的文件，然后运行 `lang:add` 并显式传入要重建的语言区域代码。 |
| `lang:rm <locale> --force` | 使用 `lang:rm <locale>`。Laravel 当前和回退语言区域受保护。 |
| 仅检查 `lang:rm` 的退出码 | 检查每项结果。`DONE` 确认删除成功，`FAILED` 表示尝试删除后仍有路径保留。`PROTECTED`、`INVALID` 和 `SKIP` 也可能伴随成功退出码。 |

如果通过命令类调用 Artisan，请将 `LaravelLang\Publisher\Console\Add`、`Remove` 和 `Update` 替换为 `AddCommand`、`RemoveCommand` 和 `UpdateCommand`。如有自定义文本装饰器，请改为实现并绑定 `LaravelLang\Publisher\Contracts\Decorator`，替换 `Contracts\TextDecorator`。其 `convert(string $locale, string $value): string` 方法接收规范语言区域代码。

首次运行 `lang:update` 前，请将现有翻译文件纳入版本控制。Publisher 会合并到现有目标文件中，并替换匹配的键（包括数字键）。尽可能将自定义译文保存在单独的应用翻译文件中。若要自行维护整个生成的目标文件，请将其路径模式添加到 `localization_main.skip`。检查是否有值变回源短语。词典缺少译文时，Publisher 会使用源短语。

如果先前的更新重建了 `http-statuses.php` 文件中的键索引，请备份并移除受影响的生成文件。如果曾将 `'*/http-statuses.php'` 添加到 `localization_main.skip` 来规避此问题，请移除该条目。运行 `php artisan lang:update` 重新生成文件，再按对应的状态码恢复自定义译文。[管理译文](./translations.md)

### 替换旧版插件注册方式 {#replace-legacy-plugin-registration}

如果使用第三方翻译包，请升级到支持清单的版本。如果维护自己的翻译包，请移除注册旧版 `LaravelLang\Publisher\Plugins\Provider` 的服务提供者和 Composer 自动发现条目。如果该服务提供者还配置了其他 Laravel 服务，请将这些注册保留在常规的 Laravel 服务提供者中。

1. 将 `laravel_lang.json` 添加到安装在 `vendor/` 下的翻译包根目录。将每个 `Plugin::files()` 条目转换成相对于翻译包 `source/` 目录的 `source` 路径，以及相对于应用翻译目录的 `target` 路径。在目标路径中使用 `{locale}`。
2. 将所有 Composer 匹配规则移到清单中。已安装的依赖使用 `vendor` 和 `version`。根 Composer 项目使用带有 `project: true` 的独立条目。如果旧插件使用了 `$with_project_name = true` 并同时支持这两种模式，请分别创建条目。
3. 将词典转换为 `locales/<code>/<style>.json`。源文件中的值是词典的查找键。例如，将 PHP 条目 `'enable' => 'Enable audit trail'` 发布到 `{locale}/audit.php` 后，应用中的键仍是 `audit.enable`。请在词典中以 `"Enable audit trail"` 为键保存译文。
4. 在本地检出的 Mona 仓库中，对旧版翻译包运行下面的 Toolkit 命令。它将把旧 JSON 和 PHP 词典转换为 `default.json` 和 `inline.json`，并迁移语言区域排除项。检查生成内容，然后连同清单和源文件一起加入新翻译包。将按别名命名的词典目录改为规范代码。
5. 在翻译包的 Composer 清单中要求安装当前版本的 Publisher。如果需要共享词典的译文，也请添加 `laravel-lang/library`。在应用中安装生成的翻译包，并运行 `php artisan lang:update` 检查发布结果。

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

对于现有应用，请检查升级后哪些内置翻译包规则仍然适用。

- 如果仍需要 Laravel-Lang 以前为 Spark Paddle、Spark Stripe、Nova Dusk Suite 或 Nova Log Viewer 发布的译文，请把这些键保留在应用自有文件中，或将旧版源文件及词典移到自定义翻译包。
- 入门套件译文现在根据项目的 Composer `name` 匹配。如果改过项目名称，或将套件作为依赖使用，请将套件的 Composer 包名（例如 `laravel/livewire-starter-kit`）添加到 `localization_main.force`，以发布其译文。[翻译插件](./plugins.md)

### 更新 JSON 回退和翻译工具 {#update-json-fallback-and-translation-tooling}

如果应用代码导入了 `LaravelLang\JsonFallback\Translator` 或将其用于类型声明，请改用 `LaravelLang\JsonFallback\OverridedTranslator`，或依赖 Laravel 的翻译器契约。如果继承了旧版翻译器，请将自定义行为移到应用的翻译器或自建翻译服务中。新版翻译器遇到无法解析的键时，会调用 Laravel 的缺失译文回调。如果应用注册了该回调，请检查它。回退 JSON 优先，因此也请测试同时存在于回退 JSON 和当前语言 PHP 文件中的键。[JSON 回退](./translations.md#json-fallback)

如果使用独立的 `laravel-lang/translator` 包进行机器翻译，请在移除此依赖前，将对其 `Translate` 门面及 Google、DeepL 或 Yandex 集成的使用迁移到选定的翻译服务。请保留这些脚本已在应用或翻译包词典中生成的译文。

如果用 `laravel-lang/status-generator` 提供的 `vendor/bin/lang` 维护翻译包目录，请在本地检出的 Mona 仓库中先运行 `composer install`，再使用新命令。

| 旧脚本 | 替代的 Toolkit 命令 |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`。查看生成的 `locales/<code>/_status.json` 报告。 |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`。使用 `sync:library` 维护共享 Library。 |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`。为已知的语言区域创建目录。 |
| `vendor/bin/lang download` | 使用 `--url` 传入 ZIP URL，使用 `--directory` 传入完整目标路径，并通过可重复指定的 `--paths` 传入压缩包内的路径。 |
| `vendor/bin/lang translate` | 将机器翻译移到独立服务。运行 Toolkit 的 `status` 命令，查找保存值仍与源文本相同的字符串。 |

完整示例请参阅 [Toolkit 命令](./toolkit.md)。

## 验证升级 {#verify-the-upgrade}

运行应用测试。检查各重要语言区域中已发布的一条消息、一条回退译文、一个使用已配置别名的本地化 URL，以及一条以规范代码存储的模型译文。结合源代码改动，检查生成的文件和保存的语言区域值。

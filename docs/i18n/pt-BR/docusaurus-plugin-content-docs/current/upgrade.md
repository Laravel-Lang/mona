---
title: Atualizar para o Mona
description: Atualize as dependências do Laravel-Lang, a configuração, as traduções publicadas e as integrações da aplicação.
---

O Mona é o repositório que publica os pacotes Laravel-Lang em conjunto. Continue usando os pacotes Composer individuais de que sua aplicação precisa.

:::info[Por que a versão 20?]

O Laravel-Lang cresceu a partir do `laravel-lang/lang`. Os pacotes surgiram em momentos diferentes, cada um com sua própria numeração e seu próprio ritmo de lançamento. As atualizações para novas versões principais exigiam cada vez mais coordenação da equipe. Quem usava os pacotes precisava descobrir quais versões podiam ser instaladas juntas.

Criamos o `laravel-lang/common` para facilitar a instalação ao declarar dependências compatíveis entre si. Ainda assim, uma atualização podia envolver vários pacotes com versões independentes.

Por exemplo, quais versões dos pacotes você teria instalado na época em uma aplicação com Laravel 11 e PHP 8.2?

<details>
<summary>Versões dos pacotes antes do Mona</summary>

| Pacote | Versão |
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
| Config | `1.17.1` (versão da série 1.x que os outros pacotes do Laravel-Lang continuavam usando) e `2.3.2` (última versão estável antes do Mona, pouco usada por esses pacotes) |
| Publisher | `16.9.0` |

</details>

Com o monorepositório Mona, lançamos os pacotes juntos e com o mesmo número de versão.

O Publisher tinha o número de versão mais alto, então `17.0` seria sua próxima versão principal. Dedicamos muito trabalho e cuidado para reunir o ecossistema e escolhemos a versão 20 para marcar esse momento. Você também pode ler esse número como **Laravel-Lang 2.0**. 🙂

:::

:::info[Códigos de localidades e aliases]

O Laravel-Lang agora usa códigos canônicos para os valores que armazena, incluindo caminhos de idiomas publicados e linhas de tradução de modelos. As localidades ativa e de fallback do Laravel também usam códigos canônicos. Em tempo de execução, use aliases em segmentos de URL e seletores de idioma. Os métodos das fachadas que retornam códigos de localidades usam o alias configurado por padrão ou o código canônico quando não há alias. Por exemplo, configure `'pt_BR' => 'pt-br'` em `localization_main.aliases` para manter `/pt-br/account` enquanto publica em `lang/pt_BR.json` e armazena `pt_BR` no banco.

:::

## Atualizar as dependências {#updating-dependencies}

A versão atual é compatível com Laravel 12 e 13 e requer PHP `^8.3`. No `composer.json` da aplicação, defina a restrição `^20.0` para cada pacote abaixo que conste em `require` ou `require-dev`. Se você instalou apenas `laravel-lang/common`, esse é o único requisito que precisa mudar.

Antes de instalar `20.x` ou atualizar para essa versão, verifique no Packagist se a versão do pacote do Composer está disponível. A versão do Mona e os pacotes do Composer correspondentes podem ser publicados em momentos diferentes.

| Uso | Pacotes Composer |
| --- | --- |
| Conjunto de pacotes | `laravel-lang/common` |
| Pacotes de tradução | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Publicação e configuração | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Integrações da aplicação | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Utilitários compartilhados | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Modelo de pacote | `laravel-lang/translations-template` |

Em uma instalação que usa apenas o fluxo padrão de tradução, aplique a atualização das dependências e publique as traduções novamente.

```bash
composer update
php artisan lang:update
```

Se registrou provedores, publicou configurações personalizadas, usa aliases ou pacotes de tradução de terceiros ou chama APIs dos pacotes na aplicação, siga as seções aplicáveis abaixo antes de publicar novamente.

## Substituir dependências renomeadas ou obsoletas {#replace-renamed-or-retired-dependencies}

Atualize no `composer.json` as dependências diretas dos pacotes antigos de nomes antes de executar `composer update`.

| Pacote Composer anterior | Exigir na versão `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

O Common já depende dos três pacotes de nomes. Atualize também seu código PHP. Consulte [substituir as APIs de consulta de nomes](#replace-the-name-lookup-apis).

Verifique se cada plugin de terceiros tem uma versão compatível com o novo Publisher. Migre qualquer uso de `laravel-lang/translator` ou `laravel-lang/status-generator` antes de remover essas dependências. Consulte [ferramentas de tradução](#update-json-fallback-and-translation-tooling).

## Atualizar provedores de serviços {#update-service-providers}

O Laravel descobre os provedores de Config, Publisher, Routes e Models pelos manifestos dos pacotes. Se depende da descoberta, atualize o código da aplicação ou os testes que façam referência às classes antigas dos provedores. Se a aplicação registra os pacotes manualmente, aplique estas substituições na mesma lista de provedores, como `bootstrap/providers.php` ou `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Remova registros manuais de `LaravelLang\Locales\LaravelLangLocalesServiceProvider` e das classes antigas `ServiceProvider` de `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` e `LaravelLang\StarterKits`. Esses pacotes de tradução são descobertos por seus manifestos.

Se você usa fallback de JSON, substitua `LaravelLang\JsonFallback\TranslationServiceProvider` por `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` onde o registrou. Isso mantém ativa a sobrescrita do provedor de tradução do Laravel.

Atualize referências antigas a provedores antes de executar `composer update` para que a descoberta de pacotes consiga inicializar a aplicação. Se um script do Composer ainda falhar por carregar uma classe antiga, execute `composer update --no-scripts`, atualize as referências e execute `composer dump-autoload` para rodar a descoberta novamente.

## Migrar a configuração {#migrate-configuration}

Se personalizou `config/localization.php`, mantenha-o como referência ao mover suas mudanças. Os pacotes carregam os novos padrões automaticamente. Publique os [arquivos de configuração](./configure.md) que precisa editar. A tag `localization` publica todos os cinco. Para publicar apenas um, use seu nome sem `.php` como tag.

```bash
php artisan vendor:publish --tag=localization
```

### Aliases, estilos de tradução e formatação de arquivos {#aliases-translation-styles-and-file-formatting}

- Copie seu mapa `aliases` para `config/localization_main.php`. Mantenha os códigos canônicos como chaves e os aliases como valores, como `'de_CH' => 'de-CH'`.
- Substitua `inline` por `styles` em `config/localization_main.php`. Defina `'styles' => ['default']` para manter o comportamento padrão anterior com mensagens de validação padrão. Mantenha `['default', 'inline']`, o novo padrão, se você usava mensagens inline. Remova `LOCALIZATION_INLINE` e `LANG_PUBLISHER_INLINE` dos ambientes em que as definiu.
- Remova a chave `align` e as variáveis de ambiente `LOCALIZATION_ALIGN` e `LANG_PUBLISHER_ALIGN`. Para alinhar os operadores `=>` nos arrays PHP publicados, configure o formatador de código PHP do seu projeto e execute-o nos arquivos de idioma depois da publicação. Por exemplo, a regra `binary_operator_spaces` do Laravel Pint pode aplicar `align_single_space_minimal` a `=>`.

### Pontuação tipográfica {#smart-punctuation}

Se você ativou a pontuação tipográfica, renomeie `LOCALIZATION_SMART_ENABLED` para `LOCALIZATION_PUNCTUATION_ENABLED` e mantenha o valor `true`. Se definiu `smart_punctuation.enable` diretamente no PHP, mova o booleano para a chave `enabled` no nível superior de `config/localization_punctuation.php`. A pontuação fica desativada por padrão.

Mova aspas personalizadas de `smart_punctuation.common` e `smart_punctuation.locales` para os arrays `common` e `locales` no nível superior desse arquivo. Use códigos canônicos como chaves de `locales`. As aspas de cada localidade sobrescrevem as chaves correspondentes de `common`. Se copiou um exemplo antigo em francês que usa `&nbsp;`, use um espaço inseparável real ou `"\u{00A0}"` nas strings de aspas.

### Rotas localizadas {#localized-routes}

Mova as substituições de `routes` na configuração antiga para `config/localization_routes.php`.

| O que você personalizou | Mudança em `config/localization_routes.php` | Variável de ambiente a renomear |
| --- | --- | --- |
| Parâmetro de localidade, cabeçalho, cookie, chave de sessão ou coluna do modelo de usuário | Copie suas substituições de `names` e mantenha as demais entradas padrão. | |
| Prefixo acrescentado aos nomes das rotas localizadas | Mova `name_prefix` para `prefix`. Se o valor estiver vazio, será usado `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Redirecionamentos da rota com prefixo | Mova `redirect_default` para `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Se o auxiliar de URL oculta a localidade de fallback | Mova `hide_default` para `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Middleware aplicado a grupos de rotas localizadas | Mova `group.middlewares.default` para `middlewares.default` no caso de rotas sem prefixo e `group.middlewares.prefix` para `middlewares.prefix` no caso de rotas com prefixo. Atualize os nomes das classes de middleware como mostra a [migração de rotas](#update-localized-routes-and-middleware). | |

### Traduções de modelos {#model-translations}

Mova as substituições de `models` para `config/localization_models.php`. Copie valores personalizados de `suffix` e `helpers` para o nível superior. Use `filter` booleano no lugar da chave antiga `filter.enabled`. Se controla a filtragem e o caminho dos auxiliares da IDE pelo ambiente, mantenha `LOCALIZATION_FILTER_ENABLED` e `VENDOR_PATH`.

Os comandos de modelos agora verificam `app/Models` por padrão. Se dependia da busca anterior em todo `app/` ou configurava `localization-private.models.directory` diretamente, coloque cada diretório a verificar no array `directories`. Por exemplo, defina `'directories' => [app_path()]` para manter a busca anterior.

### Configuração usada pelo código da aplicação {#configuration-used-by-application-code}

- Substitua leituras e gravações por `LaravelLang\Config\Facades\Config` por chamadas à configuração do Laravel com as novas chaves. Por exemplo, substitua `Config::shared()->models->filter->enabled` por `config('localization_models.filter')`. Se preferir leituras tipadas, injete `LaravelLang\Config\Services\ModelLangConfig` e leia sua propriedade `filter`, ou injete `RouteLangConfig` do mesmo namespace para configurações de rotas.
- Se seu código lê metadados de localidades de `localization-private.map`, passe a ler `localization_map.locales`. Copie as substituições para o array `locales` em `config/localization_map.php`.
- Substitua o registro por `localization-private.plugins` e `localization-private.packages` por um [manifesto de plugin](./plugins.md).
- Remova o bloco legado `translators` e qualquer configuração antiga `TRANSLATION_*` usada apenas por `laravel-lang/translator`.

Depois de mover as substituições e atualizar as referências na aplicação, remova `config/localization.php` e execute `php artisan config:clear`.

## Mover arquivos de idioma para caminhos canônicos {#move-language-files-to-canonical-paths}

1. Defina `APP_LOCALE` e `APP_FALLBACK_LOCALE` com códigos canônicos. Mova os aliases para `localization_main.aliases` para que URLs e entradas de localidades continuem funcionando.
2. Renomeie arquivos e diretórios que usam aliases, como `lang/pt-br.json` e `lang/pt-br/`, para `lang/pt_BR.json` e `lang/pt_BR/`. Procure os mesmos aliases em `lang/vendor/<package>/`. Onde os dois nomes existirem, mescle o conteúdo e resolva conflitos entre traduções personalizadas antes de remover o caminho com alias.
3. Instale cada localidade representada apenas por traduções PHP de dependências com `php artisan lang:add <locale>`. A lista de localidades instaladas usa entradas diretamente no diretório de idiomas, arquivos JSON em `lang/vendor/<package>/` e as localidades atual e de fallback efetivas.
4. Execute `php artisan lang:update` depois de migrar a configuração e os plugins e revise as diferenças. O Publisher pode manter chaves de integrações que já não publicam um arquivo. Mantenha as chaves que a aplicação ainda usa e remova as obsoletas depois de conferir onde são usadas. Consulte [scripts de publicação e traduções personalizadas](#update-publication-scripts-and-keep-custom-translations) para gerar novamente ou preservar arquivos.

## Atualizar integrações {#update-integrations}

### Locales {#locales}

Mantenha a importação de `LaravelLang\Locales\Facades\Locales` e atualize as chamadas conforme os dados usados em cada ponto.

| Chamada anterior | Substituição |
| --- | --- |
| `Locales::available()`, `installed()` ou `protects()` retornando objetos | Chame o mesmo método em `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` ou `protects()` | Chame o mesmo método diretamente em `Locales` para obter uma coleção de strings com aliases. Passe `false` para obter códigos canônicos e chame `->all()` se precisar de um array. |
| `Locales::installed(false)` ou `Locales::raw()->installed(false)` | Agora, `false` seleciona códigos canônicos. Tanto a localidade atual quanto a de fallback são incluídas mesmo sem arquivos de idioma. Substitua também o antigo argumento nomeado `withProtects: false`. Use `Locales::installed(false)->diff(Locales::protects(false))->values()` para excluí-las. Para objetos, use `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` ou `Locales::raw()->notInstalled()` | Use `Locales::data()->uninstalled()` para objetos ou `Locales::uninstalled()` para strings com aliases. Passe `false` para obter strings canônicas. |
| `Locales::getCurrent()` ou `Locales::getDefault()` | Use `Locales::data()->current()` para obter um DTO ou `Locales::current()` para obter uma string. Substitua `Locales::raw()->getDefault()` por `Locales::current()`. Se dependia de `Locales::raw()->getCurrent()` para o valor armazenado pelo Laravel, use `app()->getLocale()`. |
| `Locales::getFallback()` ou `Locales::raw()->getFallback()` | Use `Locales::data()->fallback()` para obter um DTO ou `Locales::fallback()` para obter uma string. Uma localidade de fallback desconhecida agora é resolvida para inglês. Uma localidade ativa desconhecida é resolvida para o fallback efetivo. |
| `Locales::get($locale)` ou `Locales::raw()->get($locale)` | Use `isInstalled()` e selecione o fallback explicitamente. Para uma localidade compatível que pode não estar instalada, use `isAvailable()` seguido por `info()`. |
| `Locales::info($locale)` ou `Locales::raw()->info($locale)` | Use `Locales::info($locale)` e leia `->alias` ou `->code` quando precisar de uma string. Passe `null` para a localidade atual. Para um código vazio ou desconhecido, trate `LaravelLang\Exceptions\UnknownLocaleException` ou verifique `isAvailable()` antes da consulta. |

Para manter o comportamento antigo de `get()`, que recorria à localidade atual para entradas inválidas ou de localidades não instaladas, faça essa escolha no código que chama o método.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Use em caminhos, linhas do banco de dados e outros dados persistidos.
$alias = $locale->alias; // Use em URLs e seletores de idioma.
```

Atualize os manipuladores de requisições que passavam o resultado de `raw()->get()` para `app()->setLocale()`. Use `Locales::set($locale)` depois de aceitar o valor da requisição. Ele aceita códigos de localidades instaladas, aliases configurados, casos do enum e o novo DTO, e atribui ao Laravel o código canônico. Para o valor `pt-br`, o Laravel recebe `pt_BR`, enquanto `Locales::current()` retorna `pt-br`. Chame `Locales::current(false)` ou `Locales::fallback(false)` onde precisar de códigos canônicos.

Para outros casos de persistência na aplicação, valide o valor com `isAvailable()` ou `isInstalled()` e depois use `fromAlias()` para resolver o código canônico. Use `toAlias()` para formatar um código canônico para exibição. Substitua usos do antigo `Services\Resolver` por `acceptsInput()` para verificar os tipos PHP aceitos ou por `normalize()` para extrair uma string de um valor tipado. `normalize()` preserva as strings como foram informadas, inclusive aliases como `pt-br`.

Substitua `LaravelLang\Locales\Data\LocaleData` por `LaravelLang\LocaleNames\LocaleData` em importações, declarações de tipo e testes. O novo objeto somente leitura tem as propriedades string `code`, `alias`, `native` e `localized`. Passe a usar `alias` no lugar da antiga propriedade `code` para URLs e valores da interface. Use `Locale::from($data->code)` onde lia `$data->locale`, importando `LaravelLang\LocaleList\Locale`. Se seus modelos de visualização ou formatadores usarem `type`, `regional` e `direction`, leia-os de `config("localization_map.locales.{$data->code}", [])`. Informe seus valores padrão para entradas ausentes. Para manter os padrões legados, use `Latn` para o sistema de escrita, o código canônico para a região e `Direction::LeftToRight` para a direção.

Remova os sinalizadores `withCountry`, `withCurrency`, `withCountries` e `withCurrencies`. Consulte o país e a moeda explicitamente para a linha da localidade. Atualize as importações dos tipos antigos `LaravelLang\Locales\Data\CountryData` e `CurrencyData` para as classes de dados nos pacotes de nomes correspondentes.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Se chamava `localizedLocales()`, `localizedCountries()` ou `localizedCurrencies()` no serviço antigo, use `all(null)` e `all($displayLanguage)` no serviço de nomes correspondente para os valores nativos e localizados, respectivamente. Substitua a injeção direta das classes antigas `Services\Locales` ou `Services\RawLocales` pela fachada ou pelos serviços correspondentes `Services\MainService` e `Services\DataService`. Consulte [Localidades e nomes](./locales.md) para as novas APIs de serviços.

#### Auxiliares do enum de localidades {#locale-enum-helpers}

Os casos dos enums `Locale` e `Direction` mantêm seus valores. Substitua os auxiliares removidos de `archtechx/enums` ao atualizar listas ou seletores de localidades personalizados.

| Auxiliar anterior | Substituição |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Monte o mapa de nomes para valores com `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Gere as opções na visualização a partir de `Locale::cases()`. Use o valor do caso para obter um código canônico ou `Locales::toAlias($case)` para um alias de exibição. |
| `fromName()` ou `tryFromName()` | Encontre o caso pelo `name` em `Locale::cases()` e trate a ausência de correspondência. Para um valor associado como `pt_BR`, use `Locale::from()` ou `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Monte o mapa de valores para valores com `array_column(Locale::cases(), 'value', 'value')`. Para uma lista de valores, use `Locale::values()`. |
| `Direction::values()` | Use `array_column(Direction::cases(), 'value')`. |

### Substituir as APIs de consulta de nomes {#replace-the-name-lookup-apis}

Atualize as importações de `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` e `LaravelLang\NativeLocaleNames` para `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` e `LaravelLang\LocaleNames`. Resolva `CountryNames`, `CurrencyNames` ou `LocaleNames` pelo contêiner do Laravel ou injete o serviço em sua classe.

| Uso anterior | Substituição |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` para nomes nativos | `$names->all(null)` |
| `SortBy::None` | Omita o argumento de ordenação. |
| `SortBy::Key` | Chame `->sortKeys()` na coleção retornada. |
| `SortBy::Value` | Passe `SortDirection::Ascending` para `all()` a fim de ordenar pelo nome localizado. |

Use `one($displayLocale, $entryLocale)` para consultar uma entrada conhecida. A chave de consulta é uma localidade, como `fr`. Por exemplo, `app(CountryNames::class)->one('de', 'fr')` retorna o país da localidade francesa com seu nome em alemão e `code` igual a `FR`. Os DTOs de países e moedas ficam no namespace raiz do pacote e expõem a chave de consulta como `locale`. Seus campos `code` continuam armazenando o código do país ou da moeda.

Os três serviços retornam coleções indexadas pela localidade canônica. Se usava o array de strings do pacote antigo de nomes de localidades, mapeie cada DTO para `localized`. Se chamava `toArray()` nos DTOs antigos de países ou moedas ou alterava suas propriedades, monte o array da aplicação explicitamente. Os novos DTOs são somente leitura.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Valide as localidades selecionadas pelos usuários no catálogo correspondente antes de chamar `one()`. Uma localidade de exibição desconhecida pode fazer `all()` retornar uma lista vazia. Passe `null` quando a aplicação precisar dos nomes nativos. Se seu código usava um enum personalizado com valores associados, passe seu valor string. [Localidades e nomes](./locales.md)

### Atualizar rotas localizadas e middlewares {#update-localized-routes-and-middleware}

Importe o auxiliar com namespace nos arquivos PHP e modelos Blade que geram URLs localizadas. Também é possível chamar `\LaravelLang\Routes\localizedRoute()` diretamente.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` e `LocalizationRoute::group()` registram rotas sem prefixo e com prefixo de localidade. Mantenha o grupo existente e revise as chamadas ao auxiliar de URL. Ele usa o alias da localidade de fallback quando o parâmetro de localidade é omitido. Em um seletor de idioma, passe o alias desejado explicitamente com `Locales::toAlias($locale)` ou `$localeData->alias`. Uma string de localidade informada é usada como está. Se renomeou o parâmetro, faça a chave do array corresponder a `localization_routes.names.parameter`.

Substitua importações e registros nas rotas, em `bootstrap/app.php` e nas listas de middlewares personalizados. Todas as classes da tabela estão em `LaravelLang\Routes\Middlewares`.

| Middleware anterior | Substituição |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Se criou um middleware de modelo com um guard, passe-o como parâmetro de middleware do Laravel, por exemplo, `LocalizationByModelMiddleware::class . ':admin'`. Seu resolvedor de usuários espera um modelo Eloquent. Em subclasses de middleware personalizadas, injete `LaravelLang\Config\Services\RouteLangConfig`, leia os nomes das origens de `$this->routeConfig->name` e retorne `int|string|null` em `detect()`. Substitua referências diretas a `LaravelLang\Routes\Services\Route` por `LaravelLang\Routes\Services\RouteService`.

Teste cada origem que sua aplicação usa. A detecção por cabeçalho usa PHP Intl para verificar os idiomas instalados na ordem de preferência de `Accept-Language`. Ela tenta a tag completa antes do idioma base. Um middleware posterior pode substituir a localidade escolhida por uma origem anterior. Para dar prioridade à URL nas rotas com prefixo, mantenha apenas o middleware que seleciona a localidade da URL na cadeia dessas rotas.

Teste a geração de URLs e as requisições separadamente. `localization_routes.hide` omite o segmento da localidade de fallback na geração da URL. `localization_routes.redirect` redireciona requisições com prefixo da localidade atual para a rota sem prefixo. Uma rota nomeada com prefixo também redireciona uma localidade inválida ou não instalada para a rota sem prefixo.

Em ouvintes de `LocaleHasBeenSetEvent`, use `$event->locale->code` para obter o código canônico e `$event->locale->alias` para exibição. Os dados do evento agora são um `LaravelLang\LocaleNames\LocaleData`. O middleware dispara o evento quando a localidade selecionada muda e salva o código canônico na sessão. Substitua chamadas manuais a `LocaleHasBeenSetEvent::dispatch($data)` por `event(new LocaleHasBeenSetEvent($data))`. [Rotas localizadas](./routes.md)

### Migrar código e dados de traduções de modelos {#migrate-model-translation-code-and-data}

Verifique a coluna `locale` de cada tabela de traduções em busca de linhas com aliases. Para cada alias, atualize essas linhas para o código canônico em uma migração de dados. Antes de atualizar, verifique se o mesmo `item_id` já tem uma linha para o código canônico. Mescle em uma só linha os valores que deseja preservar para atender à restrição de unicidade `(item_id, locale)`. Versões da trait antiga já gravavam códigos canônicos. Baseie a migração nos valores de fato armazenados nas tabelas.

Atualize as importações e as classes geradas.

| Uso anterior | Substituição |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` ou `UnknownModelPathException` | O mesmo nome de classe em `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Atualize as sobrescritas que selecionam uma classe de tradução personalizada. |
| `getTranslationTable()` | Use `translationQualifyColumn($column)` para uma coluna qualificada ou `translationsRaw()->getRelated()->getTable()` para o nome da tabela. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` e `forgetTranslation()` aceitam uma string de localidade, `Locale` ou a nova `LocaleData`. Instale qualquer localidade explícita usada por esses métodos antes de chamá-los. Uma leitura sem localidade explícita usa a localidade de fallback quando o valor atual é `null`. Com uma localidade explícita, a leitura obtém diretamente o valor dessa localidade. Se chamar o método de nível mais baixo `translation()`, passe uma string de localidade ou `null`. Converta um DTO com `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Com o alias `de_CH` → `de-CH` e o alemão suíço instalado, as duas chamadas acessam a linha armazenada como `de_CH`. Ao preencher vários valores de um campo traduzido, passe um mapa indexado por localidade, como `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Listas indexadas agora lançam `InvalidLocaleInputException`. O código personalizado que define a relação `translations` já carregada deve fornecer uma coleção Laravel de modelos de tradução.

Chame `translated()`, `whereTranslation()` e os escopos relacionados no construtor de consultas do Eloquent. Passe um código ou um alias como string de localidade do escopo. Para `orderByTranslation()`, use `'asc'`, `'desc'` ou `SortDirection`. Ao consultar uma localidade diferente da atual ou da de fallback com `whereTranslation()` ou `whereTranslationLike()`, defina `localization_models.filter` como `false` ou consulte `translationsRaw()` diretamente.

Atualize os consumidores de eventos que chamam `$event->locale->value` ou comparam `$event->locale` com um enum `Locale`. `TranslationHasBeenSetEvent` e `TranslationHasBeenForgetEvent` agora carregam um alias string para uma localidade explícita. O evento de atribuição contém `null` quando a localidade é omitida. Converta um alias com `Locales::fromAlias()` antes de usá-lo como chave persistida. O evento de atribuição é disparado ao atribuir o atributo, antes de `save()`. Para disparar manualmente qualquer um dos três eventos de tradução de modelos, use `event(new …)`.

Se mantém os auxiliares gerados para a IDE, execute `php artisan lang:models-helper` depois de atualizar as importações e os caminhos de descoberta de modelos. [Traduções de modelos](./models.md)

### Atualizar scripts de publicação e preservar traduções personalizadas {#update-publication-scripts-and-keep-custom-translations}

| Comando ou premissa anterior | Migração |
| --- | --- |
| `lang:add <locale>` atualizava apenas as localidades adicionadas | Agora, atualiza todas as localidades instaladas. Passe códigos explícitos em scripts e leve em conta as atualizações de arquivos de localidades existentes. |
| `lang:reset` | Use `lang:update` para mesclar. Para reconstruir do zero, faça backup das entradas personalizadas, remova os arquivos que pretende gerar novamente e execute `lang:add` com os códigos das localidades a reconstruir. |
| `lang:rm <locale> --force` | Use `lang:rm <locale>`. As localidades atual e de fallback do Laravel são protegidas. |
| Verificar apenas o código de saída de `lang:rm` | Verifique cada resultado. `DONE` confirma a remoção e `FAILED` indica que um caminho permaneceu após a tentativa de remoção. `PROTECTED`, `INVALID` e `SKIP` podem acompanhar um código de saída de sucesso. |

Se chama o Artisan por classe de comando, troque `LaravelLang\Publisher\Console\Add`, `Remove` e `Update` por `AddCommand`, `RemoveCommand` e `UpdateCommand`. Para um decorador de texto personalizado, implemente e registre `LaravelLang\Publisher\Contracts\Decorator` no lugar de `Contracts\TextDecorator`. Seu `convert(string $locale, string $value): string` recebe a localidade canônica.

Execute o primeiro `lang:update` com os arquivos de idioma existentes sob controle de versão. O Publisher mescla nos destinos existentes e substitui as chaves correspondentes, inclusive as numéricas. Sempre que possível, mantenha traduções personalizadas em um arquivo de tradução separado da aplicação. Para manter por conta própria um destino gerado inteiro, adicione seu padrão de caminho a `localization_main.skip`. Verifique os valores que voltarem para a frase de origem. O Publisher usa essa frase quando os dicionários não têm uma tradução.

Se uma atualização anterior reindexou os arquivos `http-statuses.php`, faça uma cópia de segurança dos arquivos gerados afetados e remova-os. Remova `'*/http-statuses.php'` de `localization_main.skip` se você adicionou essa regra para contornar o problema. Execute `php artisan lang:update` para gerar os arquivos novamente e restaure as traduções personalizadas nos códigos de status correspondentes. [Gerenciar traduções](./translations.md)

### Substituir o registro de plugins legados {#replace-legacy-plugin-registration}

Se usa um pacote de traduções de terceiros, atualize para sua versão baseada em manifesto. Se mantém um pacote, remova o provedor e a entrada de descoberta do Composer que registram um `LaravelLang\Publisher\Plugins\Provider` legado. Se esse provedor também configura serviços do Laravel não relacionados, mantenha os registros deles em um provedor de serviços comum do Laravel.

1. Adicione `laravel_lang.json` à raiz do pacote instalado em `vendor/`. Converta cada entrada de `Plugin::files()` em um caminho `source` relativo a `source/` no pacote e um caminho `target` relativo ao diretório de idiomas da aplicação. Use `{locale}` no destino.
2. Mova para o manifesto quaisquer regras de correspondência do Composer. Use `vendor` e `version` para uma dependência instalada e uma entrada separada com `project: true` para um projeto Composer raiz. Se seu plugin antigo usava `$with_project_name = true` e aceitava os dois modos, crie as duas entradas.
3. Converta os dicionários para `locales/<code>/<style>.json`. Os valores nos arquivos de origem são as chaves de consulta do dicionário. Por exemplo, uma entrada PHP `'enable' => 'Enable audit trail'` ainda cria a chave `audit.enable` na aplicação quando publicada para `{locale}/audit.php`. Armazene sua tradução em `"Enable audit trail"` no dicionário.
4. A partir de uma cópia local do Mona, execute o comando Toolkit abaixo para o pacote legado. Isso cria `default.json` e `inline.json` a partir dos dicionários JSON e PHP antigos e transfere as exclusões de localidades. Revise o resultado e adicione-o ao novo pacote junto com o manifesto e os arquivos de origem. Renomeie para códigos canônicos os diretórios de dicionário baseados em aliases.
5. Exija o Publisher atual no manifesto Composer do pacote. Adicione também `laravel-lang/library` se o pacote espera traduções do dicionário compartilhado. Instale o pacote resultante em uma aplicação e execute `php artisan lang:update` para verificar os arquivos publicados.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Em aplicações existentes, verifique quais regras dos pacotes integrados se aplicam depois da atualização.

- Se ainda precisa das traduções do Laravel-Lang antes publicadas para Spark Paddle, Spark Stripe, Nova Dusk Suite ou Nova Log Viewer, mantenha essas chaves em arquivos da aplicação ou mova as fontes e dicionários antigos para um pacote personalizado.
- As traduções dos kits iniciais agora correspondem ao `name` do projeto no Composer. Se renomeou o projeto ou usa um kit como dependência, adicione a `localization_main.force` o nome Composer do kit, como `laravel/livewire-starter-kit`, para publicar suas traduções. [Plugins de tradução](./plugins.md)

### Atualizar o fallback de JSON e as ferramentas de tradução {#update-json-fallback-and-translation-tooling}

Se o código da aplicação importa ou declara tipos com `LaravelLang\JsonFallback\Translator`, mude o tipo para `LaravelLang\JsonFallback\OverridedTranslator` ou use o contrato de tradução do Laravel. Se criou uma subclasse do tradutor antigo, mova o comportamento personalizado para um tradutor ou serviço de tradução da aplicação. Para uma chave não resolvida, o tradutor substituto chama o callback do Laravel para traduções ausentes. Revise esse callback se sua aplicação registra um e teste chaves que existem no JSON de fallback e no PHP do idioma atual, pois o JSON de fallback é consultado primeiro. [Fallback de JSON](./translations.md#json-fallback)

Se usa o pacote separado `laravel-lang/translator` para tradução automática, migre os usos da fachada `Translate` e as integrações com Google, DeepL ou Yandex para o serviço de tradução escolhido antes de remover essa dependência. Mantenha as traduções já geradas pelos scripts nos dicionários da aplicação ou do pacote.

Se mantém catálogos de pacotes com `vendor/bin/lang` do `laravel-lang/status-generator`, execute os novos comandos a partir de uma cópia local do Mona após `composer install`.

| Script anterior | Script do Toolkit a usar |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Leia os relatórios `locales/<code>/_status.json` gerados. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Use `sync:library` para manter a Library compartilhada. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. O comando cria diretórios para localidades conhecidas. |
| `vendor/bin/lang download` | Informe a URL do ZIP com `--url`, o destino completo com `--directory` e os caminhos do arquivo com opções `--paths` repetíveis. |
| `vendor/bin/lang translate` | Mova a tradução automática para um serviço separado. Execute `status` do Toolkit para encontrar as strings salvas que ainda correspondem à origem. |

Consulte os [comandos do Toolkit](./toolkit.md) para ver exemplos completos.

## Verificar a atualização {#verify-the-upgrade}

Execute os testes da aplicação. Verifique uma mensagem publicada em cada localidade importante, uma tradução de fallback, uma URL localizada com o alias configurado e uma tradução de modelo armazenada com o código canônico. Revise os arquivos gerados e os valores de localidade salvos junto com as alterações no código-fonte.

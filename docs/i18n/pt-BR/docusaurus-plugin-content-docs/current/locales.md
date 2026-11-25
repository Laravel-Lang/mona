---
title: Localidades e nomes
description: Encontre localidades compatíveis e instaladas, resolva aliases e consulte nomes localizados.
---

O Laravel-Lang usa códigos canônicos como `en`, `fr` e `pt_BR` no Laravel, nos nomes de arquivos de tradução e nas linhas de tradução de modelos. Um alias de localidade é um nome usado pela aplicação, como `pt-br` em uma URL. Defina o mapeamento do código canônico para o alias em [`localization_main.aliases`](./configure.md).

## Encontrar localidades compatíveis e instaladas {#find-supported-and-installed-locales}

`laravel-lang/locale-list` fornece o enum com valores associados `LaravelLang\LocaleList\Locale`. Seus casos correspondem aos [códigos canônicos compatíveis](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // array de códigos canônicos
Locale::collection();        // Coleção de códigos canônicos
Locale::collection(false);   // Coleção de casos do enum
```

`laravel-lang/locales` adiciona a fachada `LaravelLang\Locales\Facades\Locales`, que considera a configuração da aplicação. Esses métodos retornam coleções de strings. Passe `false` como argumento para obter códigos canônicos. Por padrão, cada código é representado pelo alias configurado, quando houver.

| Método | Retorno |
| --- | --- |
| `Locales::available()` | Todas as localidades conhecidas |
| `Locales::installed()` | Localidades reconhecidas pelos arquivos de idioma da aplicação e localidades protegidas |
| `Locales::uninstalled()` | Localidades disponíveis que não estão instaladas |
| `Locales::protects()` | Localidades atual e de fallback efetivas, sem duplicatas |

A descoberta lê as entradas imediatas em `lang_path()` e os arquivos JSON que correspondem a `vendor/*/*.json` nesse caminho. O nome do arquivo ou diretório precisa corresponder a um código compatível. Diretórios de tradução que contêm apenas PHP em `lang/vendor/<package>/` não são abrangidos por esses padrões. Consulte [gerenciar traduções](./translations.md) para publicar uma localidade.

## Ler ou definir a localidade da aplicação {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` aceita um código, alias, caso do enum `Locale` ou objeto `LocaleData`. Ele atualiza a localidade do Laravel para o código canônico quando ela está instalada. Use `isAvailable()` para verificar o suporte ao código e `isInstalled()` quando forem necessários arquivos ou proteção. `isProtected()` identifica as localidades atual e de fallback.

`current()` usa a localidade atual do Laravel se ela for reconhecida e recorre a `fallback()` caso contrário. `fallback()` resolve o fallback configurado no Laravel e usa `en` quando ele é desconhecido. Os dois métodos aceitam o mesmo argumento `false` para retornar códigos canônicos.

Use `fromAlias()` para resolver um alias para um código canônico e `toAlias()` para obter o alias de um código canônico. Essas conversões preservam strings não vazias e não reconhecidas. Use `isAvailable()` ao validar entradas.

## Exibir opções de localidades {#display-locale-choices}

`Locales::info('fr')` retorna um objeto `LaravelLang\LocaleNames\LocaleData` com as propriedades `code`, `alias`, `native` e `localized`. O idioma de exibição é o da localidade atual da aplicação. Omita o argumento para consultar a localidade atual. Uma localidade conhecida pode ser consultada antes da instalação. Um código desconhecido lança `LaravelLang\Exceptions\UnknownLocaleException`.

Use `Locales::data()->installed()` para obter as localidades instaladas como objetos `LocaleData`. `data()` também fornece `available()`, `uninstalled()` e `protects()` para outras coleções, além de `current()` e `fallback()` para objetos individuais. Um seletor de idioma pode enviar `alias` como valor e exibir o nome `native` como rótulo.

## Consultar nomes de localidades, países e moedas {#look-up-locale-country-and-currency-names}

Cada pacote, `laravel-lang/locale-names`, `laravel-lang/country-names` e `laravel-lang/currency-names`, fornece um serviço que você pode injetar a partir do contêiner do Laravel. O Common inclui os três.

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

O primeiro parâmetro é o idioma de exibição. Use `null` para nomes nativos. O segundo parâmetro de `one()` é a localidade da linha, como `fr` nos exemplos acima. Nas consultas de países e moedas, o código ISO consta no objeto retornado. Os dois parâmetros aceitam códigos canônicos, aliases configurados e casos do enum `Locale`.

`all($forLocale)` retorna uma coleção indexada pelo código canônico da localidade. Passe `SortDirection::Ascending` ou `SortDirection::Descending` para ordenar pela propriedade `localized`. Cada valor é um objeto somente leitura. Valores de países e moedas também incluem a propriedade `locale` da linha.

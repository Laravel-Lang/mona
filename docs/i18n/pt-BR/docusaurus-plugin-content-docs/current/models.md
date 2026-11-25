---
title: Traduções de modelos
description: Armazene, leia e consulte atributos localizados do Eloquent com o Laravel-Lang Models.
---

`laravel-lang/models` adiciona atributos traduzidos a um modelo Eloquent existente. Cada linha de tradução armazena uma localidade e seus valores de atributos. O pacote está incluído no Common e requer a extensão Intl do PHP.

```bash
composer require laravel-lang/models
```

## Adicionar traduções a um modelo {#add-translations-to-a-model}

Para um modelo `App\Models\Article` existente com uma migração para a tabela base, execute os comandos abaixo.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

O gerador adiciona a trait `LaravelLang\Models\HasTranslations` ao modelo base e cria o modelo de tradução correspondente `ArticleTranslation`, uma migração e um auxiliar para a IDE. A migração cria uma chave estrangeira para o artigo, um par `(item_id, locale)` único e colunas de texto que aceitam `null` para os valores traduzidos. Se você já armazena esses valores na tabela base, migre-os para as linhas de tradução antes de depender dos métodos de acesso do novo modelo.

Se o modelo estiver fora de `app/Models`, adicione seu diretório a [`localization_models.directories`](./configure.md) antes de executar o gerador.

Execute `php artisan make:model-translation` para escolher o modelo e as colunas de forma interativa. Se a lista ficar vazia, serão usadas `title` e `description`. Para adicionar um campo depois, crie uma migração e inclua o campo nos atributos preenchíveis e nos casts do modelo de tradução.

## Gravar e ler atributos traduzidos {#write-and-read-translated-attributes}

Com o [francês instalado](./translations.md), você pode armazenar uma tradução informando o código canônico, o alias, o caso do enum `Locale` ou `LocaleData`. Use `save()` no modelo base para persistir as mudanças.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // localidade atual e fallback se o valor for nulo
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Uma localidade explícita lê o valor dessa localidade. Sem informar a localidade, a gravação usa a localidade atual e a leitura recorre à localidade de fallback configurada no Laravel quando o valor atual é `null`. Os aliases são resolvidos para códigos canônicos na consulta e no armazenamento. Uma localidade explicitamente informada que seja desconhecida ou não esteja instalada lança `LaravelLang\Exceptions\UnknownLocaleException`.

Use `setTranslations()` para gravar vários valores de um campo. Você também pode passar, para cada campo traduzido, um mapa indexado por localidade a `create()` ou `fill()`. O exemplo considera que inglês e francês estão instalados.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Use `isTranslatable('title')` para verificar os campos configurados do modelo e `hasTranslated('title', 'fr')` para verificar se há um valor preenchido. `forgetTranslation('fr')` exclui a linha do francês, e `forgetAllTranslations()` exclui todas as linhas do modelo.

## Consultar traduções {#query-translations}

`translated()`, `whereTranslation()` e `whereTranslationLike()` consultam pela relação `translations()`. Por padrão, essa relação filtra as linhas para as localidades atual e de fallback. Para consultar outras localidades com esses escopos, defina `localization_models.filter` como `false`. Para ler todas as linhas de um modelo, use `translationsRaw()`.

Com o francês como localidade atual, consulte e ordene pelo título em francês.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Os modelos carregam a relação `translations()` antecipadamente. `orWhereTranslation()` e `orWhereTranslationLike()` acrescentam condições alternativas. `orderByTranslation()` ordena por uma única localidade, a atual por padrão.

## Observar mudanças e atualizar auxiliares para IDE {#observe-changes-and-refresh-ide-helpers}

O namespace `LaravelLang\Models\Events` contém `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` e `AllTranslationsHasBeenForgetEvent`. O evento de atribuição contém o modelo, a coluna, um alias de localidade opcional, o valor anterior e o novo valor. Ele é disparado na atribuição do atributo, antes da gravação posterior.

Execute `php artisan lang:models-helper` para gerar novamente os auxiliares de todos os modelos encontrados. Seu diretório padrão é `vendor/_laravel_lang`, controlado por `localization_models.helpers`.

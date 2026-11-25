---
title: Configuração
description: Configure estilos de tradução, aliases de localidades, filtros de publicação, pontuação, rotas e modelos do Mona.
---

O Mona carrega as configurações padrão automaticamente. Publique os arquivos que precisa personalizar no diretório `config/` da aplicação.

```bash
php artisan vendor:publish --tag=localization_main
```

A tag `localization` publica os cinco arquivos. Cada nome de arquivo sem `.php` também funciona como tag de publicação e chave de configuração do Laravel.

| Arquivo | Configurações |
| --- | --- |
| `localization_main.php` | Estilos de tradução, aliases e filtros de publicação |
| `localization_punctuation.php` | Pontuação tipográfica e aspas específicas de cada localidade |
| `localization_routes.php` | [Nomes de rotas, origens da localidade e middleware](./routes.md) |
| `localization_models.php` | [Sufixo de modelos de tradução, filtros e caminhos do gerador](./models.md) |
| `localization_map.php` | Metadados de localidades, como sistema de escrita, região e direção |

O Laravel substitui cada configuração de nível superior pelo valor definido na aplicação. Ao editar o array `locales` de `localization_punctuation.php`, preserve os ajustes padrão das localidades que ainda quiser usar.

Depois de alterar a configuração, limpe o cache existente com `php artisan config:clear`. Execute `php artisan lang:update` para gerar novamente as traduções afetadas pelos ajustes de publicação.

## Estilos de tradução {#translation-styles}

Um estilo de tradução é um dicionário com nome próprio que muda a redação de frases selecionadas. Por exemplo, um pacote pode fornecer `informal.json` para um tom amigável ou `john.json` com o vocabulário de um produto. Os nomes dos estilos independem dos códigos de localidades e dos nomes de aplicações ou pacotes. Qualquer pacote de tradução pode oferecer um estilo em `locales/<locale>/<style>.json` para os idiomas que abrange. Um estilo pode conter uma única frase. O caso de uso original eram as formas de tratamento informais. [Discussão sobre traduções informais](https://github.com/orgs/Laravel-Lang/discussions/124)

Depois de instalar um plugin que fornece `john`, coloque esse estilo por último em `config/localization_main.php` para dar prioridade a ele em cada origem de dicionário.

```php
'styles' => ['default', 'informal', 'john'],
```

Ao executar `php artisan lang:update`, o Publisher mescla os arquivos de estilo de cada localidade instalada. Para cada frase, `john` tem prioridade se contiver a frase, seguido de `informal` e depois de `default`. Por exemplo, um pacote de francês pode substituir uma frase em `locales/fr/john.json`, enquanto o alemão usa `locales/de/informal.json` ou `locales/de/default.json` para essa mesma frase. O Publisher aplica a ordem separadamente à Library compartilhada e ao pacote que fornece o arquivo. Para esse arquivo, uma frase correspondente no pacote prevalece sobre a entrada da Library.

A configuração padrão é `['default', 'inline']`. O estilo `inline` fornece mensagens genéricas de validação, como “This field must be accepted.”. Defina `styles` como `['default']` para usar só o texto padrão. Se você omitir `default` da lista, o Publisher o acrescentará no início.

## Aliases de localidades {#locale-aliases}

Defina `localization_main.aliases` como um mapa de códigos canônicos para aliases. Por exemplo, adicione a entrada abaixo ao array retornado por `config/localization_main.php`.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

O Artisan pode aceitar `de-CH`, enquanto o Laravel e os arquivos publicados usam `de_CH`. Defina `APP_LOCALE` e `APP_FALLBACK_LOCALE` com códigos canônicos. Consulte [Localidades e nomes](./locales.md) para exemplos de conversão e consulta.

Os aliases distinguem maiúsculas de minúsculas. Use um alias exclusivo para cada localidade, diferente dos códigos canônicos das outras localidades.

## Ignorar arquivos e forçar integrações {#skip-files-and-force-integrations}

- `localization_main.skip` compara padrões `*` sem distinção entre maiúsculas e minúsculas com caminhos completos separados por `/`. Pode excluir arquivos de origem, dicionários e destinos de publicação. Por exemplo, `['*/lang/fr/validation.php']` impede que o Publisher grave esse destino em uma aplicação que usa `lang/`.
- `localization_main.force` aceita nomes de pacotes Composer. Permite que o Publisher use entradas do manifesto mesmo quando a dependência informada está ausente, a versão instalada não corresponde ou o nome do projeto raiz difere. Use essa opção quando as traduções do manifesto se aplicarem à sua aplicação.

`skip` tem prioridade sobre `force`.

## Pontuação tipográfica {#smart-punctuation}

Defina `LOCALIZATION_PUNCTUATION_ENABLED=true` para ativar aspas tipográficas, travessões e reticências nas traduções publicadas. Edite `localization_punctuation.common` para regras gerais e `localization_punctuation.locales` para substituições indexadas por código canônico da localidade. As strings publicadas passam a seguir essas regras na próxima vez que o Publisher as gravar.

## Metadados de localidades {#locale-metadata}

`localization_map.locales` mapeia os códigos canônicos para sistema de escrita (`type`), variante regional (`regional`) e direção de escrita opcional (`direction`). Use `Direction::LeftToRight` quando a direção não estiver configurada.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```

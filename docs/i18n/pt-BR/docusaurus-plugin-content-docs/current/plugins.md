---
title: Plugins de tradução
description: Instale pacotes de tradução ou publique seu próprio plugin Composer para o Laravel-Lang.
---

O Publisher descobre pacotes de tradução instalados pelo Composer. Um pacote fornece um manifesto `laravel_lang.json`, arquivos de origem PHP ou JSON e, opcionalmente, seus próprios dicionários de localidades.

## Instalar um pacote {#install-a-pack}

Instale o pacote na aplicação Laravel e atualize as localidades que já usa. Por exemplo, o pacote Actions adiciona rótulos para botões e outras ações.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Consulte [gerenciar traduções](./translations.md) para adicionar uma localidade e [plugins da comunidade](./plugins-community.md) para encontrar pacotes de terceiros.

### Pacotes oficiais de tradução {#official-translation-packs}

O Common inclui esses pacotes. Você também pode instalar cada um separadamente pelo Composer.

| Pacote | Traduções |
| --- | --- |
| `laravel-lang/lang` | Framework Laravel e pacotes como Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys e Laravel UI |
| `laravel-lang/starter-kits` | Kits iniciais do Laravel para Livewire, React, Svelte e Vue |
| `laravel-lang/moonshine` | Painel administrativo MoonShine |
| `laravel-lang/actions` | Rótulos de botões e outras ações |
| `laravel-lang/attributes` | Nomes de atributos de validação legíveis para usuários |
| `laravel-lang/http-statuses` | Frases de status HTTP |

Cada pacote traz o Publisher e a Library compartilhada. Seu manifesto especifica para quais integrações e versões ele pode publicar traduções.

## Criar um pacote {#create-a-pack}

Comece pelo modelo de tradução. No `composer.json` do seu pacote, exija `laravel-lang/publisher` e declare as versões compatíveis. Exija `laravel-lang/library` se o pacote depender das traduções compartilhadas. O Publisher procura manifestos em `vendor/*/*/laravel_lang.json` na aplicação. [Modelo de tradução](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Coloque o manifesto na raiz do pacote. Este exemplo publica um arquivo JSON e um grupo de tradução PHP para cada localidade instalada.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

Cada caminho `source` é relativo ao diretório `source/` do pacote. Cada `target` é relativo ao diretório de idiomas do Laravel na aplicação e contém `{locale}`.

Por exemplo, use o conteúdo abaixo em `source/audit.json`.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

Use este conteúdo em `source/audit.php`.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Ao publicar para o francês, ambos procuram a frase em inglês em `locales/fr/default.json` no seu pacote.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

Na aplicação, `__('Enable audit trail')` lê a tradução JSON e `__('audit.enable')` lê a tradução PHP. As traduções do pacote têm prioridade sobre as entradas da Library compartilhada para a mesma frase. Se nenhum dicionário contiver a frase, o Publisher usa o valor de origem.

Dê um nome próprio de Composer ao pacote e substitua `self.version` no modelo por uma restrição compatível com a versão que você oferece suporte. Use códigos canônicos de localidade nos diretórios dos dicionários. Guarde as traduções padrão em `default.json` e as variantes em arquivos como `inline.json`.

### Aplicar traduções condicionalmente {#apply-translations-conditionally}

| Propriedade do manifesto | Quando o mapeamento se aplica |
| --- | --- |
| Sem condição | O Publisher sempre inclui o mapeamento. |
| `vendor` | O pacote Composer indicado está instalado. |
| `vendor` e `version` | O pacote instalado satisfaz a restrição de versão do Composer. |
| `vendor` e `project: true` | O nome do projeto raiz no Composer corresponde a `vendor`. Nesse modo, o Publisher verifica o nome. |

Use duas entradas no manifesto se um mapeamento precisar se aplicar a uma dependência instalada e a um projeto raiz com esse nome. Consulte o [guia de atualização](./upgrade.md) para converter um provedor legado e a [configuração](./configure.md) para regras de exclusão e inclusão forçada de pacotes.

### Testar um pacote em uma aplicação Laravel {#test-a-pack-in-a-laravel-application}

Execute os comandos na raiz da aplicação de teste se o pacote estiver em um diretório irmão. Confira `lang/fr.json` e `lang/fr/audit.php`, defina `fr` como a localidade da aplicação e teste as duas chaves. Depois de mudar o pacote, execute `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```

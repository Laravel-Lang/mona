---
title: Toolkit de tradução
description: Mantenha, migre e importe fontes e dicionários de tradução a partir de uma cópia local do Mona.
---

O Toolkit é a ferramenta de linha de comando para a manutenção de pacotes de tradução. Execute-o a partir de uma cópia local do Mona depois de instalar as dependências do Composer na raiz. Para publicar traduções em uma aplicação Laravel, use os [comandos Artisan](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Verificar o status das traduções {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

Por padrão, o comando grava `locales/<locale>/_status.json` no projeto. Ele lista entradas cuja tradução salva corresponde à frase de origem e as agrupa por estilo. Combina as exclusões de `locales/_settings.json` e `locales/<locale>/_settings.json` e exclui relatórios vazios. Para encontrar frases inteiramente ausentes de um dicionário, compare-o com os arquivos de origem do pacote.

Use `--output-dir` para gravar os relatórios com o nome `<locale>.json` em um diretório separado. O comando resolve caminhos relativos a partir do diretório de trabalho atual e cria o diretório de destino quando há um relatório a gravar.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

Adicione as expressões mantidas iguais de propósito a `excludes` no arquivo `_settings.json` correspondente.

```json
{
  "excludes": ["Laravel"]
}
```

## Gerenciar diretórios de localidades {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` cria os diretórios que faltam para todos os [códigos de localidade](./available-locales.md) canônicos. Os diretórios novos começam vazios. `delete-locale` exclui cada diretório imediatamente abaixo de `locales/` cujo nome não consta da lista. Verifique o destino antes de executá-lo, sobretudo se ele contiver aliases de localidades ou códigos personalizados.

## Remover entradas obsoletas de dicionários {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` coleta frases de origem de pacotes com manifesto `laravel_lang.json` em `src/` e regrava os dicionários em `src/Library/locales/`.
- `sync:package` lê o diretório `source/` de um pacote e remove as entradas não utilizadas de seu diretório `locales/`. Ele exclui os dicionários que ficarem vazios depois da remoção.

Os dois comandos mantêm as traduções existentes para frases ativas e preservam arquivos iniciados por sublinhado, como `_settings.json`. Adicione traduções de frases novas diretamente ao dicionário.

Sem argumento, `sync:library` usa o diretório `src/` da cópia do repositório e `sync:package` usa o diretório de trabalho atual. Ambos processam diretórios com códigos canônicos de localidade.

## Migrar dicionários legados {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Informe um pacote legado que contenha `source/` e `locales/`. Use `--target` para indicar o diretório que deve conter os subdiretórios de localidades. Por padrão, o Toolkit usa o diretório `locales/` do pacote legado.

- As entradas de `php.json` e `json.json` são mescladas em `default.json`.
- As entradas de `php-inline.json` e `json-inline.json` são mescladas em `inline.json`.
- As entradas de `_excludes.json` passam para `excludes` no `_settings.json` da localidade de destino.

O Toolkit transfere diretamente as entradas JSON, que já usam as frases de origem como chaves. Para os dicionários PHP, ele procura as chaves aninhadas nos arquivos PHP de `source/` e usa as frases correspondentes como chaves. Quando as chaves coincidem, a tradução migrada tem prioridade. Se os dois formatos legados fornecerem uma tradução para a mesma frase, o dicionário PHP tem prioridade.

O Toolkit mantém os arquivos de `source/`. Ele remove os arquivos de dicionário legados e os arquivos `_excludes.json` dos quais transferiu entradas, mesmo quando o destino é separado. Consulte o [guia de atualização](./upgrade.md) para alterações na sua aplicação ou no manifesto do plugin.

## Importar fontes de um arquivo ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` define o destino final. Repita `--paths` para informar diretórios relativos à raiz do arquivo.
- Os arquivos PHP e JSON selecionados são copiados com o nome do arquivo para o destino. Arquivos PHP são copiados byte a byte. Arquivos JSON com o mesmo nome são mesclados, com prioridade para os valores recebidos.
- Sem `--only-copy`, o Toolkit procura chamadas de tradução compatíveis com chaves literais. Se encontrar frases, grava uma fonte JSON com o nome do diretório pai do destino, como `framework.json` no exemplo.
- O comando exige a extensão ZIP do PHP. Ele prepara as alterações antes da publicação e tenta restaurar o destino anterior se a publicação falhar.

`--url` e `--directory` são obrigatórios. Os diretórios de `--paths` são percorridos recursivamente. Omita `--paths` para buscar apenas chaves de tradução. Revise as chaves encontradas antes de adicioná-las ao pacote.

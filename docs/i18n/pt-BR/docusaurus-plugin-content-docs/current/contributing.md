---
title: Contribuir
description: "Contribua com traduções, pacotes PHP e documentação a partir de uma cópia local do Mona."
---

Crie um fork do [repositório do Mona](https://github.com/Laravel-Lang/mona) e abra um pull request para a branch `main`. O Mona reúne os modelos de origem, as traduções compartilhadas, os pacotes PHP e a documentação. Os repositórios individuais dos pacotes recebem seu conteúdo do Mona.

## Corrigir ou adicionar uma tradução {#fix-or-add-a-translation}

1. Encontre a frase em inglês no diretório `src/<Package>/source/` do pacote. Por exemplo, as fontes do Laravel Framework e dos pacotes oficiais do Laravel ficam em `src/Lang/source/`. O arquivo `laravel_lang.json` do pacote lista os arquivos de origem usados para cada versão do pacote de referência.
2. Abra `src/Library/locales/<locale>/default.json`. Edite o valor da frase em inglês ou inclua a entrada se ela estiver ausente. Use o valor exato da origem como chave JSON, inclusive para frases de arquivos PHP.
3. Preserve marcadores como `:count`, `:other` e `:attribute` no texto traduzido. Para uma mensagem de validação inline, use a mesma chave de origem em `inline.json` e substitua `:attribute` por uma redação que dispense o nome do campo. Preserve os demais marcadores e as formas do plural.
4. Se uma tradução for intencionalmente igual à frase em inglês, adicione a frase a `excludes` no `_settings.json` da localidade. O Toolkit usa essa lista ao gerar o status das traduções.
5. Abra um pull request com o código de localidade no título, como `[fr]: Updated validation messages`.

Se encontrar um erro de digitação em uma frase de um projeto de origem, comunique-o a esse projeto para que a frase e sua chave de consulta sejam atualizadas juntas.

Veja, por exemplo, a entrada francesa padrão em `src/Library/locales/fr/default.json`.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

A entrada inline correspondente fica em `src/Library/locales/fr/inline.json`.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Para traduções que o Mona publica nos aplicativos, edite `default.json` e `inline.json` em Library. Alguns pacotes ainda mantêm os dicionários antigos `php.json` e `json.json`, também usados na verificação automática do status dos pacotes.

Para atualizar nomes de países, moedas ou localidades, edite o arquivo JSON da localidade em `src/CountryNames/data/`, `src/CurrencyNames/data/` ou `src/LocaleNames/data/`. Consulte [localidades e nomes traduzidos](./locales.md).

## Propor uma localidade {#propose-a-locale}

Consulte os [códigos canônicos de localidades](./available-locales.md). Se o código já estiver na lista, adicione ou atualize seu dicionário em `src/Library/locales/`. O Toolkit pode [criar os diretórios de localidades que faltam](./toolkit.md#manage-locale-directories). Para propor um código novo, abra uma [discussão no Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Desenvolver pacotes {#develop-packages}

Use Git, Composer 2 e PHP `^8.3` com `intl`, `json` e `zip`. Ative `pdo_sqlite` para os testes, que usam um banco de dados em memória. Execute os comandos na raiz do repositório.

```bash
composer install
composer check-platform-reqs
```

O projeto Composer da raiz carrega automaticamente o código de `src/<Package>/src/`. Declare as dependências de cada pacote publicado no arquivo `src/<Package>/composer.json` correspondente. As dependências internas usam `self.version` para permitir a instalação conjunta de pacotes da mesma versão.

Use o aplicativo Testbench em `workbench/` para testar o comportamento dos pacotes localmente. `composer serve` prepara o Workbench e inicia o servidor de desenvolvimento. Para consultar suas rotas ou comandos Artisan, execute a interface de linha de comando do Testbench na raiz.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

A CI instala cada pacote separadamente. Um repositório Composer do tipo `path` disponibiliza os demais pacotes da cópia local. Se você alterar as dependências de um pacote, confira sua instalação individual e execute os testes da raiz. A configuração e a matriz de versões do Laravel estão no [fluxo Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Executar verificações locais {#run-local-checks}

Para mudanças em dicionários, instale as dependências JavaScript na raiz e execute as verificações do Biome. O comando `status` do Toolkit grava relatórios das entradas cujo valor coincide com a chave de origem. Confira os arquivos de origem do pacote para encontrar frases sem entrada no dicionário. Use `git status --short` para conferir os relatórios gerados. [Comandos do Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Para alterações em PHP, execute Pest e PHPStan na raiz do repositório. Execute os testes funcionais em série, como na CI, porque os testes de geração de modelos compartilham o diretório do aplicativo Testbench. Os testes de arquitetura podem rodar em paralelo. Para testar uma funcionalidade específica, passe seu arquivo ou diretório de testes ao Pest.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Se, por causa da configuração de desenvolvimento do Testbench, os testes de rotas gerarem URLs com `http://localhost:8000`, defina `APP_URL` como `http://localhost` no processo de teste antes de executar o Pest.

O script `composer style` normaliza os arquivos do Composer e aplica correções do Rector e do Pint. Confira as alterações antes de enviar o pull request. Para medir a cobertura de linhas, habilite o Xdebug e execute `composer run-script --timeout=0 test:coverage`.

Para alterações em manifestos, execute a mesma verificação de esquema usada na CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Consulte [plugins de tradução](./plugins.md).

## Editar a documentação {#edit-the-documentation}

As páginas em inglês da versão atual ficam em `docs/docs/` e as traduções em `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Quando as instruções mudarem, atualize as páginas correspondentes e preserve os mesmos IDs de títulos para links em todos os idiomas. A versão anterior ao Mona está arquivada em `docs/versioned_docs/version-legacy/` e as traduções ficam nos diretórios `version-legacy/` correspondentes em `docs/i18n/`.

Use a versão do Node.js exigida em `docs/package.json`. Execute os comandos de documentação em `docs/`.

```bash
cd docs
npm ci
npm start
```

Use `npm start -- --locale fr` para visualizar uma tradução. Pare o servidor de desenvolvimento e faça a compilação de produção para conferir links, âncoras e índices de busca em todos os idiomas configurados.

```bash
npm run typecheck
npm run build
npm run serve
```

Abra o endereço exibido por `serve` para conferir a busca e as exportações Markdown geradas. A compilação grava o site estático em `docs/build/`.

## Publicar uma versão {#publish-a-release}

Uma mesclagem na `main` exporta a subárvore de cada pacote para a branch `main` do seu repositório e atualiza o rascunho da versão do Mona. Revise o rascunho e publique a versão no Mona com a tag compartilhada. Os fluxos de lançamento são acionados pelo evento de publicação da versão.

A publicação exporta a tag e cria a versão correspondente no repositório de cada pacote. Também inicia a compilação e a implantação do site de documentação. Confira os dois fluxos após a publicação. Para visualizar as árvores dos pacotes antes da publicação, execute **Export Packages** manualmente na `main` com `dry-run` ativado. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

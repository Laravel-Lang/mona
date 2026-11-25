---
title: Gerenciar traduções
description: Adicione, atualize e remova traduções da aplicação e de dependências com o Publisher do Mona.
---

O Publisher acompanha os pacotes oficiais de tradução. Seus comandos Artisan gravam traduções PHP e JSON no diretório de idiomas da aplicação, geralmente `lang/`. Arquivos específicos de pacotes também podem ser gravados em `lang/vendor/`.

Execute os comandos Artisan na raiz da aplicação. `lang_path()` indica o diretório de traduções.

## Adicionar localidades {#add-locales}

Informe um ou mais [códigos de localidade](./available-locales.md) separados por espaços. O comando aceita aliases configurados, e os arquivos gerados usam os códigos canônicos.

```bash
php artisan lang:add fr de_CH
```

O comando cria os diretórios das localidades e depois atualiza **todas as localidades instaladas**, incluindo a atual e a de fallback da aplicação. Sem argumentos, ele pergunta se deve instalar todas as localidades compatíveis. Informe os códigos explicitamente em automações.

Se um código for desconhecido, o comando falha antes de criar qualquer diretório.

## Atualizar traduções {#update-translations}

```bash
php artisan lang:update
```

`lang:update` atualiza todas as localidades instaladas e não aceita códigos como argumentos.

O Publisher identifica localidades a partir das localidades atual e de fallback da aplicação, de arquivos ou diretórios na raiz do diretório de idiomas e de arquivos JSON em `lang/vendor/<package>/`. Um diretório de traduções PHP de uma dependência, por si só, não faz uma localidade ser considerada instalada. Use `lang:add` com essa localidade para criar o diretório correspondente na aplicação.

Os pacotes de tradução declaram os pacotes, versões e arquivos de destino compatíveis em [manifestos de plugins](./plugins.md). O Publisher combina os dicionários compartilhados e os dos pacotes, depois mescla as traduções em cada arquivo de destino. Ele preserva as chaves sem correspondência e substitui as chaves correspondentes, inclusive as numéricas. Quando uma frase está ausente dos dicionários, o Publisher grava a frase de origem. Mantenha suas substituições em arquivos de tradução separados ou [ignore](./configure.md#skip-files-and-force-integrations) os destinos gerados que você mantém.

Nos arquivos `validation.php` da aplicação, o Publisher coloca as chaves de nível superior `custom` e `attributes` no fim do arquivo, nessa ordem, quando presentes. As demais chaves de nível superior ficam em ordem alfabética.

Execute `lang:update` depois de alterar dependências do Composer ou [configurações de publicação](./configure.md). Revise as diferenças geradas.

Em uma cópia nova do projeto, execute `composer install` antes de publicar traduções. Implante os arquivos gerados com a aplicação ou gere-os durante a implantação se houver permissão de escrita no diretório de traduções. Se os arquivos estiverem ausentes, execute `php artisan lang:add fr de_CH --no-interaction` com os códigos necessários. Para atualizar arquivos existentes, execute `php artisan lang:update --no-interaction`.

## Remover localidades {#remove-locales}

```bash
php artisan lang:rm de_CH
```

Informe os códigos canônicos ou os aliases configurados.

O comando exclui o diretório e o arquivo JSON da localidade canônica na raiz do diretório de idiomas e em cada diretório imediato de `lang/vendor/<package>/`, incluindo quaisquer arquivos personalizados que contenham. As localidades atual e de fallback são protegidas. Sem argumentos, o comando pergunta se deve remover todas as localidades desprotegidas e a resposta padrão é não. Se o diretório de uma localidade for um link, o comando remove o próprio link.

O comando informa `DONE`, `PROTECTED`, `INVALID`, `SKIP` ou `FAILED` para cada localidade. Se um arquivo ou diretório permanecer após a tentativa de remoção, o comando mostra seu caminho e retorna um código de falha. Verifique o status de cada localidade, pois `PROTECTED`, `INVALID` e `SKIP` podem acompanhar um código de saída de sucesso.

## Encontrar os comandos Artisan {#find-the-artisan-commands}

Execute `php artisan help lang:add` para verificar se o comando está registrado. Se estiver ausente, verifique a instalação com `composer show laravel-lang/publisher` e execute `php artisan package:discover`. Se você desativou a descoberta automática de Config e Publisher, registre `LaravelLang\Config\LangConfigServiceProvider::class` e `LaravelLang\Publisher\LangPublisherServiceProvider::class` em `bootstrap/providers.php`.

## Fallback de JSON {#json-fallback}

Quando uma chave JSON está ausente, `laravel-lang/json-fallback` acrescenta uma consulta ao JSON da localidade de fallback antes de seguir para as traduções PHP. O Common inclui o pacote. Para instalá-lo separadamente, execute `composer require laravel-lang/json-fallback`.

Adicione `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` aos provedores da aplicação em `bootstrap/providers.php`. Se a aplicação listar explicitamente `Illuminate\Translation\TranslationServiceProvider`, substitua essa entrada pelo provedor do Mona. [Registro de provedores no Laravel](https://laravel.com/docs/13.x/providers#registering-providers)

Com o francês como localidade atual e o inglês como fallback, uma chave ausente em `lang/fr.json` pode agora retornar o valor correspondente em `lang/en.json`.

Para restringir a busca à localidade solicitada, use `Lang::get($key, fallback: false)` pela fachada `Illuminate\Support\Facades\Lang`.

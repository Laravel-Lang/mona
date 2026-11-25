---
title: Primeiros passos
description: Instale o Laravel-Lang e publique suas primeiras traduções do Laravel.
---

O Laravel-Lang publica traduções para o Laravel e seu ecossistema. O pacote `laravel-lang/common` também permite [consultar idiomas e nomes](./locales.md), [localizar rotas](./routes.md) e [traduzir modelos](./models.md). Para instalar apenas o pacote de traduções do Laravel e as dependências de publicação, escolha `laravel-lang/lang`.

## Início rápido {#quick-start}

Execute estes comandos na raiz de uma aplicação Laravel 12 ou 13 com Composer, PHP `^8.3` e as extensões PHP `intl` e `json`. Sua versão do Laravel pode exigir uma versão mais recente do PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Use a função de tradução do Laravel na sua aplicação para consultar uma mensagem publicada em francês.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Para usar o francês como idioma padrão, defina `APP_LOCALE=fr` no arquivo `.env` da aplicação. Defina `APP_FALLBACK_LOCALE=en` para usar o inglês quando uma busca com idioma alternativo não encontrar a tradução no idioma atual. Se houver um cache de configuração, limpe-o com `php artisan config:clear`. Chamadas como `__('auth.failed')` passam a usar o idioma atual da aplicação.

- [Versões anteriores do Laravel-Lang](/legacy)
- [Gerencie as traduções](./translations.md) e [consulte os idiomas disponíveis](./available-locales.md)
- [Configure a localização](./configure.md)
- [Verifique a compatibilidade](./release-notes.md#versioning-and-compatibility) ou [atualize uma instalação existente](./upgrade.md)

## Testar uma versão candidata {#try-a-release-candidate}

Use uma aplicação Laravel de teste separada para experimentar uma versão candidata da série 20.x já publicada. Defina a estabilidade mínima dessa aplicação como `RC` para que o Composer também consiga instalar as dependências correspondentes do Laravel-Lang. Escolha uma versão listada no [Packagist](https://packagist.org/packages/laravel-lang/common). Substitua `<release-candidate>` pela versão escolhida.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```

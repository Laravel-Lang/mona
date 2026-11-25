---
title: Rotas localizadas
description: Registre URLs localizadas e selecione a localidade do Laravel a partir de uma requisição.
---

`laravel-lang/routes` seleciona a localidade durante a requisição e gera URLs para grupos de rotas localizadas. O pacote está incluído no Common.

```bash
composer require laravel-lang/routes
```

Verifique se as [localidades usadas nas URLs estão instaladas](./translations.md). Configure os nomes de parâmetros, os middlewares e o comportamento da localidade padrão em [`config/localization_routes.php`](./configure.md).

## Registrar um grupo de rotas localizadas {#register-a-localized-route-group}

Em `routes/web.php`, use a macro `Route::localizedGroup()`. A fachada `LaravelLang\Routes\Facades\LocalizationRoute::group()` fornece o mesmo método de agrupamento.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Com a configuração padrão, o callback registra os dois padrões de URL.

| Padrão | Nome da rota |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

O middleware de localidade consome o parâmetro `locale` antes de chamar o manipulador da rota. Quando a rota tem nome, uma requisição com localidade desconhecida ou não instalada na URL é redirecionada para a rota sem prefixo.

O Laravel executa a função do grupo duas vezes. Com `admin` como prefixo de URL e `admin.` como prefixo de nome do grupo externo, a rota localizada se chama `admin.localized.products.show`.

## Gerar uma URL localizada {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

O auxiliar usa o nome de parâmetro de localidade configurado e prefere a rota com prefixo, se houver. Ele repassa a string de localidade informada à geração da URL. Para gerar uma URL com um alias, informe o alias ou resolva-o antes com [`Locales::toAlias()`](./locales.md). Se você omitir a localidade, o auxiliar usa `Locales::fallback()`. Para usar a localidade atual, passe `Locales::current()` explicitamente.

`localization_routes.hide` omite a localidade de fallback das URLs geradas pelo auxiliar. `localization_routes.redirect` redireciona requisições da localidade atual do grupo com prefixo para a rota sem prefixo. As duas configurações têm `false` como padrão.

Passe `Locale::French->value` para usar o código canônico ou um objeto `LocaleData` para usar o alias. Valide a escolha do usuário com `Locales::isInstalled()` antes de gerar a URL.

## Selecionar uma localidade com middleware {#select-a-locale-with-middleware}

Cada middleware pode ser aplicado diretamente por sua classe de `LaravelLang\Routes\Middlewares`.

| Middleware | Origem |
| --- | --- |
| `LocalizationByParameterMiddleware` | Um parâmetro da rota, `locale` por padrão |
| `LocalizationByParameterWithRedirectMiddleware` | Um parâmetro da rota. Com uma rota nomeada e um parâmetro declarado, substitui pela localidade atual quando a solicitada está ausente ou não foi instalada |
| `LocalizationByCookieMiddleware` | Um cookie `Accept-Language` por padrão |
| `LocalizationByHeaderMiddleware` | A primeira localidade instalada na ordem de preferência de `Accept-Language`. O middleware tenta a tag completa antes do idioma base e ignora curingas e entradas com valor de qualidade 0. |
| `LocalizationBySessionMiddleware` | Uma chave de sessão `Accept-Language` por padrão |
| `LocalizationByModelMiddleware` | O atributo `locale` do usuário autenticado do Eloquent por padrão. Acrescente `:guard_name` à classe do middleware para selecionar um guard |

No grupo com prefixo, a ordem padrão é parâmetro da URL, cookie, cabeçalho, sessão e, por fim, modelo. O grupo sem prefixo segue a mesma ordem sem a etapa da URL. Uma origem válida posterior pode prevalecer sobre uma anterior. A mudança de localidade atualiza o Laravel e persiste o código canônico na chave de sessão configurada. Use o grupo de middleware `web` quando precisar de sessões e cookies. O middleware de cabeçalho requer a extensão Intl do PHP.

Quando o middleware muda a localidade, ele dispara `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. A propriedade `locale` do evento é um objeto `LaravelLang\LocaleNames\LocaleData`. Use `$event->locale->code` para obter o código canônico.

Para dar prioridade à URL, deixe apenas `LocalizationByParameterPrefixMiddlewareMiddleware::class` em `localization_routes.middlewares.prefix`. Configure separadamente a cadeia sem prefixo.

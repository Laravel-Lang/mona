---
title: "Lokalisierte Routen"
description: "Registriere lokalisierte URLs und ermittle die Laravel-Locale aus einer Anfrage."
---

`laravel-lang/routes` ermittelt die Locale beim Verarbeiten der Anfrage und erstellt URLs für lokalisierte Routengruppen. Das Paket ist in Common enthalten.

```bash
composer require laravel-lang/routes
```

Stelle sicher, dass die [in deinen URLs verwendeten Locales installiert sind](./translations.md). Parameternamen, Middleware und das Verhalten der Standard-Locale konfigurierst du in [`config/localization_routes.php`](./configure.md).

## Eine lokalisierte Routengruppe registrieren {#register-a-localized-route-group}

Verwende in `routes/web.php` das Makro `Route::localizedGroup()`. Die Fassade `LaravelLang\Routes\Facades\LocalizationRoute::group()` stellt dieselbe Gruppierungsmethode bereit.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Mit der Standardkonfiguration registriert die Callback-Funktion beide URL-Muster.

| Muster | Routenname |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Die Locale-Middleware verarbeitet und entfernt den Parameter `locale` vor dem Aufruf des Routen-Handlers. Bei einer Anfrage mit einer unbekannten oder nicht installierten URL-Locale leitet sie zur Route ohne Präfix um, sofern die Route einen Namen hat.

Laravel führt den Callback der Gruppe zweimal aus. Hat die äußere Gruppe den URL-Präfix `admin` und den Namenspräfix `admin.`, heißt die lokalisierte Route `admin.localized.products.show`.

## Eine lokalisierte URL generieren {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Der Helper verwendet den konfigurierten Locale-Parameternamen und bevorzugt die Route mit Präfix, falls sie existiert. Eine übergebene Locale-Zeichenkette wird an die URL-Generierung durchgereicht. Für eine URL mit Alias übergib den Alias oder ermittle ihn vorher mit [`Locales::toAlias()`](./locales.md). Ohne Locale-Argument verwendet der Helper `Locales::fallback()`. Um die aktuelle Locale zu verwenden, übergib `Locales::current()` ausdrücklich.

`localization_routes.hide` lässt die Fallback-Locale in den vom Helper generierten URLs weg. `localization_routes.redirect` leitet Anfragen für die aktuelle Locale von der Gruppe mit Präfix auf die Route ohne Präfix um. Der Standardwert beider Einstellungen ist `false`.

Übergib `Locale::French->value` für den kanonischen Code oder ein `LocaleData`-Objekt für dessen Alias. Prüfe die Auswahl eines Benutzers mit `Locales::isInstalled()`, bevor du die URL erzeugst.

## Eine Locale per Middleware bestimmen {#select-a-locale-with-middleware}

Du kannst jede Middleware direkt über ihre Klasse aus `LaravelLang\Routes\Middlewares` anwenden.

| Middleware | Quelle |
| --- | --- |
| `LocalizationByParameterMiddleware` | Ein Routenparameter, standardmäßig `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Ein Routenparameter. Bei einer benannten Route mit deklariertem Parameter verwendet die Middleware die aktuelle Locale, wenn die angeforderte fehlt oder nicht installiert ist |
| `LocalizationByCookieMiddleware` | Standardmäßig ein Cookie `Accept-Language` |
| `LocalizationByHeaderMiddleware` | Die erste installierte Locale in der von `Accept-Language` vorgegebenen Reihenfolge. Die Middleware prüft zuerst das vollständige Sprach-Tag und dann die Basissprache. Sie überspringt Platzhalter und Einträge mit dem Qualitätswert 0. |
| `LocalizationBySessionMiddleware` | Standardmäßig der Session-Schlüssel `Accept-Language` |
| `LocalizationByModelMiddleware` | Standardmäßig das Attribut `locale` des authentifizierten Eloquent-Benutzers. Hänge `:guard_name` an die Middleware-Klasse an, um einen Guard auszuwählen |

Für die Gruppe mit Präfix gilt standardmäßig die Reihenfolge URL-Parameter, Cookie, Header, Session und Modell. Die Gruppe ohne Präfix verwendet dieselbe Reihenfolge ohne den URL-Schritt. Eine spätere gültige Quelle kann eine frühere überschreiben. Bei einem Wechsel der Locale aktualisiert die Middleware Laravel und speichert den kanonischen Code unter dem konfigurierten Session-Schlüssel. Verwende die Middleware-Gruppe `web`, wenn du Sessions und Cookies benötigst. Die Header-Middleware benötigt PHP Intl.

Wenn die Middleware die Locale ändert, löst sie `LaravelLang\Routes\Events\LocaleHasBeenSetEvent` aus. Die Eigenschaft `locale` des Ereignisses ist ein Objekt vom Typ `LaravelLang\LocaleNames\LocaleData`. Verwende `$event->locale->code` für den kanonischen Code.

Soll die URL maßgeblich sein, verwende in `localization_routes.middlewares.prefix` nur `LocalizationByParameterPrefixMiddlewareMiddleware::class`. Die Kette für Routen ohne Präfix kannst du separat konfigurieren.

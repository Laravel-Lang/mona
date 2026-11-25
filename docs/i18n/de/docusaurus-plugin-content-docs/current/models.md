---
title: "Modellübersetzungen"
description: "Speichere und lies lokalisierte Eloquent-Attribute und frage sie mit Laravel-Lang Models ab."
---

`laravel-lang/models` ergänzt ein vorhandenes Eloquent-Modell um übersetzte Attribute. Jeder Übersetzungsdatensatz speichert eine Locale und die Attributwerte für diese Locale. Das Paket ist in Common enthalten und benötigt die PHP-Erweiterung Intl.

```bash
composer require laravel-lang/models
```

## Übersetzungen zu einem Modell hinzufügen {#add-translations-to-a-model}

Führe für ein vorhandenes Modell `App\Models\Article` mit einer Migration für die Basistabelle den folgenden Befehl aus.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Der Generator ergänzt das Basismodell um den Trait `LaravelLang\Models\HasTranslations` und erstellt das zugehörige Modell `ArticleTranslation`, eine Migration und einen IDE-Helper. Die Migration legt einen Fremdschlüssel zum Artikel, ein eindeutiges Wertepaar `(item_id, locale)` und String-Spalten für die übersetzten Werte an, die NULL zulassen. Wenn du diese Werte bereits in der Basistabelle speicherst, migriere sie in die Übersetzungsdatensätze, bevor du die Accessors des neuen Modells verwendest.

Liegt das Modell außerhalb von `app/Models`, ergänze sein Verzeichnis in [`localization_models.directories`](./configure.md), bevor du den Generator ausführst.

Starte `php artisan make:model-translation`, um Modell und Spalten interaktiv auszuwählen. Ohne Spalteneingabe verwendet der Generator `title` und `description`. Lege für ein späteres Feld eine neue Migration an und ergänze die zuweisbaren Attribute und Casts im Übersetzungsmodell.

## Übersetzte Attribute schreiben und lesen {#write-and-read-translated-attributes}

Wenn [Französisch installiert ist](./translations.md), kannst du eine Übersetzung speichern, indem du den kanonischen Code, den Alias, einen Fall des Enums `Locale` oder `LocaleData` übergibst. Speichere die Änderungen mit `save()` am Basismodell.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // Aktuelle Locale, bei einem null-Wert die Fallback-Locale
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Wenn du eine Locale ausdrücklich angibst, liest das Modell deren Wert. Ohne Locale-Argument wird beim Schreiben die aktuelle Locale verwendet. Beim Lesen greift das Modell auf die in Laravel konfigurierte Fallback-Locale zurück, wenn der aktuelle Wert `null` ist. Für Abfrage und Speicherung löst das Modell Aliasse zu kanonischen Codes auf. Eine ausdrücklich angegebene unbekannte oder nicht installierte Locale löst `LaravelLang\Exceptions\UnknownLocaleException` aus.

Verwende `setTranslations()` für mehrere Werte desselben Felds. Für jedes übersetzte Feld kannst du auch eine Zuordnung mit Locale-Codes als Schlüsseln an `create()` oder `fill()` übergeben. Im Beispiel sind Englisch und Französisch installiert.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Prüfe die konfigurierten Felder des Modells mit `isTranslatable('title')` und mit `hasTranslated('title', 'fr')`, ob ein Wert vorhanden ist. `forgetTranslation('fr')` löscht den französischen Datensatz und `forgetAllTranslations()` alle Übersetzungsdatensätze des Modells.

## Übersetzungen abfragen {#query-translations}

`translated()`, `whereTranslation()` und `whereTranslationLike()` fragen über die Relation `translations()` ab. Standardmäßig beschränkt diese Relation die Datensätze auf die aktuelle Locale und die Fallback-Locale. Setze `localization_models.filter` auf `false`, um andere Locales über diese Scopes abzufragen. Verwende `translationsRaw()`, um alle Datensätze für ein einzelnes Modell zu lesen.

Verwende Französisch als aktuelle Locale und frage die Datensätze nach dem französischen Titel ab, um sie danach zu sortieren.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Modelle laden die Relation `translations()` vorab. `orWhereTranslation()` und `orWhereTranslationLike()` ergänzen alternative Bedingungen. `orderByTranslation()` sortiert nach einer einzelnen Locale, standardmäßig der aktuellen.

## Änderungen beobachten und IDE-Helper aktualisieren {#observe-changes-and-refresh-ide-helpers}

Der Namespace `LaravelLang\Models\Events` enthält `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` und `AllTranslationsHasBeenForgetEvent`. Das Set-Ereignis enthält das Modell, die Spalte, einen optionalen Locale-Alias, den alten Wert und den neuen Wert. Es wird beim Zuweisen des Attributs vor dem anschließenden Speichern ausgelöst.

Führe `php artisan lang:models-helper` aus, um die Helper für alle erkannten Modelle neu zu generieren. Ihr Standardverzeichnis ist `vendor/_laravel_lang`. Es wird von `localization_models.helpers` gesteuert.

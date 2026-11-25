---
title: Traducciones de modelos
description: Almacena, lee y consulta atributos de Eloquent localizados con Laravel-Lang Models.
---

`laravel-lang/models` agrega atributos traducidos a un modelo de Eloquent existente. Cada fila de traducción almacena una configuración regional y los valores de los atributos para ella. El paquete se incluye en Common y requiere la extensión Intl de PHP.

```bash
composer require laravel-lang/models
```

## Agregar traducciones a un modelo {#add-translations-to-a-model}

Si ya tienes un modelo `App\Models\Article` con una migración para su tabla base, ejecuta estos comandos.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

El generador agrega el trait `LaravelLang\Models\HasTranslations` al modelo base y crea el modelo asociado `ArticleTranslation`, una migración y un archivo de ayuda para el IDE. La migración crea una clave foránea hacia el artículo, un par `(item_id, locale)` único y columnas de texto que admiten valores nulos para las traducciones. Si estos valores ya se guardan en la tabla base, migra los datos a filas de traducción antes de usar los métodos de acceso del nuevo modelo.

Si el modelo está fuera de `app/Models`, agrega su directorio a [`localization_models.directories`](./configure.md) antes de ejecutar el generador.

Ejecuta `php artisan make:model-translation` para elegir el modelo y sus columnas de forma interactiva. Si dejas la lista vacía, usa `title` y `description`. Para añadir un campo más adelante, crea una migración y añade el campo a los atributos asignables y a las conversiones del modelo de traducción.

## Escribir y leer atributos traducidos {#write-and-read-translated-attributes}

Con [el francés instalado](./translations.md), puedes guardar una traducción pasando su código canónico, alias, caso de la enumeración `Locale` u objeto `LocaleData`. Usa `save()` del modelo base para guardar los cambios.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // configuración regional actual, luego la de reserva si el valor es nulo
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Si especificas una configuración regional al leer, se obtiene el valor para ella. Sin ese argumento, las escrituras usan la configuración regional actual y las lecturas recurren a la configuración regional de reserva de Laravel si el valor actual es `null`. Los alias se convierten en códigos canónicos para la consulta y el almacenamiento. Si indicas una configuración regional desconocida o no instalada, se produce `LaravelLang\Exceptions\UnknownLocaleException`.

Usa `setTranslations()` para asignar varios valores a un campo. También puedes pasar a `create()` o `fill()` un mapa por configuración regional para cada campo traducido. El ejemplo supone que el inglés y el francés están instalados.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Usa `isTranslatable('title')` para comprobar los campos configurados en el modelo y `hasTranslated('title', 'fr')` para saber si el campo tiene un valor. `forgetTranslation('fr')` elimina la fila del francés y `forgetAllTranslations()` elimina todas las filas del modelo.

## Consultar traducciones {#query-translations}

`translated()`, `whereTranslation()` y `whereTranslationLike()` consultan mediante la relación `translations()`. De forma predeterminada, esta relación limita las filas a las configuraciones regionales actual y de reserva. Para consultar otras configuraciones regionales con estos ámbitos, establece `localization_models.filter` en `false`. Para leer todas las filas de un modelo, usa `translationsRaw()`.

Con el francés como configuración regional actual, consulta y ordena por el título en francés.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Los modelos precargan la relación `translations()`. `orWhereTranslation()` y `orWhereTranslationLike()` añaden condiciones alternativas. `orderByTranslation()` ordena por una sola configuración regional, la actual por defecto.

## Observar cambios y actualizar archivos de ayuda del IDE {#observe-changes-and-refresh-ide-helpers}

El espacio de nombres `LaravelLang\Models\Events` contiene `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` y `AllTranslationsHasBeenForgetEvent`. El evento de asignación contiene el modelo, la columna, el alias opcional de configuración regional, el valor anterior y el nuevo valor. Se activa al asignar el atributo, antes de guardar.

Ejecuta `php artisan lang:models-helper` para regenerar los archivos de ayuda de todos los modelos detectados. Su directorio predeterminado es `vendor/_laravel_lang` y se configura en `localization_models.helpers`.

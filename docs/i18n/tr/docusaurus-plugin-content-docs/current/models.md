---
title: "Model çevirileri"
description: "Laravel-Lang Models ile yerelleştirilmiş Eloquent özniteliklerini saklayın, okuyun ve sorgulayın."
---

`laravel-lang/models`, mevcut bir Eloquent modeline çevrilebilir öznitelikler ekler. Her çeviri satırı bir yerel ayarı ve o yerel ayara ait öznitelik değerlerini saklar. Paket Common’a dahildir ve PHP Intl gerektirir.

```bash
composer require laravel-lang/models
```

## Modele çeviri ekleme {#add-translations-to-a-model}

Temel tablosu için bir migration dosyası bulunan mevcut `App\Models\Article` modeliyle aşağıdaki komutu çalıştırın.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Oluşturucu, temel modele `LaravelLang\Models\HasTranslations` trait’ini ekler ve ona eşlik eden `ArticleTranslation` modelini, bir migration dosyasını ve bir IDE yardımcı dosyasını oluşturur. Migration, makaleye başvuran bir yabancı anahtar, benzersiz bir `(item_id, locale)` çifti ve çeviri değerleri için null kabul eden metin sütunları oluşturur. Bu değerleri zaten temel tabloda tutuyorsanız yeni modelin erişim metotlarına güvenmeden önce bunları çeviri satırlarına taşıyın.

Model `app/Models` dışındaysa oluşturucuyu çalıştırmadan önce modelin dizinini [`localization_models.directories`](./configure.md) ayarına ekleyin.

Modeli ve sütunları etkileşimli olarak seçmek için `php artisan make:model-translation` çalıştırın. Listeyi boş bırakırsanız üreteç `title` ve `description` kullanır. Sonradan alan eklemek için yeni bir migration oluşturun ve alanı çeviri modelinin doldurulabilir özniteliklerine ve dönüşümlerine ekleyin.

## Çevrilmiş öznitelikleri yazma ve okuma {#write-and-read-translated-attributes}

[Fransızca kuruluysa](./translations.md) standart kod, takma ad, `Locale` enum değeri veya `LocaleData` vererek çeviri saklayabilirsiniz. Değişiklikleri kalıcı olarak saklamak için temel modelin `save()` metodunu kullanın.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // Önce geçerli yerel ayar, değer null ise yedek yerel ayar
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Yerel ayar açıkça verildiğinde o yerel ayarın değeri okunur. Yerel ayar argümanı olmadan, yazma işlemi geçerli yerel ayarı kullanır. Okuma işlemi, geçerli değer `null` olduğunda Laravel’de yapılandırılan yedek yerel ayarı kullanır. Arama ve saklama için takma adlar standart kodlara çözümlenir. Bilinmeyen veya kurulu olmayan bir yerel ayar açıkça verilirse `LaravelLang\Exceptions\UnknownLocaleException` hatası oluşur.

Tek bir alanın birkaç değerini ayarlamak için `setTranslations()` kullanın. Her çevrilen alan için `create()` veya `fill()` metoduna yerel ayarla anahtarlanmış bir eşleme de verebilirsiniz. Örnek, İngilizce ve Fransızcanın kurulu olduğunu varsayar.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Modelde yapılandırılmış alanları kontrol etmek için `isTranslatable('title')`, bir değerin doldurulup doldurulmadığını kontrol etmek için `hasTranslated('title', 'fr')` kullanın. `forgetTranslation('fr')` Fransızca satırını, `forgetAllTranslations()` ise modelin tüm çeviri satırlarını siler.

## Çevirileri sorgulama {#query-translations}

`translated()`, `whereTranslation()` ve `whereTranslationLike()`, `translations()` ilişkisi üzerinden sorgu çalıştırır. Bu ilişki varsayılan olarak satırları geçerli ve yedek yerel ayarlarla sınırlar. Bu sorgu kapsamlarından diğer yerel ayarları da sorgulamak için `localization_models.filter` ayarını `false` yapın. Tek bir modelin tüm satırlarını okumak için `translationsRaw()` kullanın.

Geçerli yerel ayar Fransızcaysa Fransızca başlığa göre sorgulayıp sıralayabilirsiniz.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Modeller `translations()` ilişkisini önceden yükler. `orWhereTranslation()` ve `orWhereTranslationLike()` alternatif koşullar ekler. `orderByTranslation()` tek bir yerel ayara göre sıralar ve varsayılan olarak geçerli yerel ayarı kullanır.

## Değişiklikleri izleme ve IDE yardımcı dosyalarını yenileme {#observe-changes-and-refresh-ide-helpers}

`LaravelLang\Models\Events` ad alanı `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` ve `AllTranslationsHasBeenForgetEvent` olaylarını içerir. Değer atama olayı modeli, sütunu, isteğe bağlı yerel ayar takma adını, eski değeri ve yeni değeri içerir. Olay, sonraki kaydetme işleminden önce, özniteliğe değer atanırken tetiklenir.

Bulunan tüm modeller için yardımcı dosyaları yeniden oluşturmak üzere `php artisan lang:models-helper` çalıştırın. Varsayılan dizin `vendor/_laravel_lang` olup `localization_models.helpers` ile ayarlanır.

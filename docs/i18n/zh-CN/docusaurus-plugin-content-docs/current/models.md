---
title: 模型翻译
description: 使用 Laravel-Lang Models 存储、读取和查询 Eloquent 的本地化属性。
---

`laravel-lang/models` 可为现有 Eloquent 模型添加可翻译属性。每条翻译记录保存一个语言区域及该语言区域的属性值。Common 已包含此包，使用时需要 PHP Intl 扩展。

```bash
composer require laravel-lang/models
```

## 为模型添加翻译 {#add-translations-to-a-model}

如果已经有 `App\Models\Article` 模型及其主表迁移，请运行以下命令。

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

生成器会为基础模型添加 `LaravelLang\Models\HasTranslations` trait，并创建配套的 `ArticleTranslation` 模型、数据库迁移和 IDE 辅助文件。迁移将创建关联文章的外键、唯一的 `(item_id, locale)` 组合和可为空的字符串列，用于保存译文。如果这些值已经存于主表，请先迁移到翻译表，再使用新模型的访问器。

如果模型不在 `app/Models` 下，请先将其目录添加到 [`localization_models.directories`](./configure.md)，再运行生成器。

运行 `php artisan make:model-translation` 可交互选择模型和列。留空时，生成器使用 `title` 和 `description`。之后要新增字段，请创建迁移，并将字段加入翻译模型的可批量赋值属性与类型转换。

## 写入和读取已翻译的属性 {#write-and-read-translated-attributes}

[安装法语译文](./translations.md)后，可传入法语的规范代码、别名、`Locale` 枚举项或 `LocaleData` 对象来存储译文。使用基础模型的 `save()` 持久化更改。

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // 先读取当前语言区域，为 null 时读取回退语言区域
$article->getTranslation('title', 'fr');  // 'Premier article'
```

显式指定语言区域时，会读取该语言区域的值。省略语言区域参数时，写入使用当前语言区域，读取在当前值为 `null` 时使用 Laravel 配置的回退语言区域。查找和存储时，别名会解析为规范代码。显式指定未知或未安装的语言区域将引发 `LaravelLang\Exceptions\UnknownLocaleException`。

要为一个字段设置多种语言的值，请使用 `setTranslations()`。也可以在 `create()` 或 `fill()` 中为每个可翻译字段传入以语言区域为键的映射。下例假定已安装英语和法语。

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

使用 `isTranslatable('title')` 检查模型中配置的可翻译字段，使用 `hasTranslated('title', 'fr')` 检查是否已有值。`forgetTranslation('fr')` 删除法语记录，`forgetAllTranslations()` 删除该模型的全部翻译记录。

## 查询译文 {#query-translations}

`translated()`、`whereTranslation()` 和 `whereTranslationLike()` 通过 `translations()` 关联查询。默认情况下，此关联只包含当前和回退语言区域的记录。要通过这些查询作用域查询其他语言区域，将 `localization_models.filter` 设为 `false`。要读取单个模型的全部记录，请使用 `translationsRaw()`。

将法语设为当前语言区域后，可以按法语标题查询并排序。

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

模型会预加载 `translations()` 关系。`orWhereTranslation()` 和 `orWhereTranslationLike()` 可添加备选条件。`orderByTranslation()` 按单个语言区域排序，默认为当前语言区域。

## 监听更改和更新 IDE 辅助文件 {#observe-changes-and-refresh-ide-helpers}

`LaravelLang\Models\Events` 命名空间包含 `TranslationHasBeenSetEvent`、`TranslationHasBeenForgetEvent` 和 `AllTranslationsHasBeenForgetEvent`。设置事件包含模型、列、可选的语言区域别名、旧值和新值。它在属性赋值时触发，早于随后的保存。

运行 `php artisan lang:models-helper`，可为所有已发现的模型重新生成 IDE 辅助文件。文件默认保存在 `vendor/_laravel_lang`，可通过 `localization_models.helpers` 修改。

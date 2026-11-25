---
title: 参与贡献
description: "在本地检出 Mona，为译文、PHP 包和文档作出贡献。"
---

复刻 [Mona 仓库](https://github.com/Laravel-Lang/mona)，然后向其 `main` 分支提交拉取请求。Mona 集中管理源模板、共享译文、PHP 包和文档。各个包的独立仓库从 Mona 接收导出的内容。

## 修正或添加译文 {#fix-or-add-a-translation}

1. 在翻译包的 `src/<Package>/source/` 目录中找到英文短语。例如，Laravel 框架和官方包的源文件位于 `src/Lang/source/`。 翻译包的 `laravel_lang.json` 列出了各上游包版本所用的源文件。
2. 打开 `src/Library/locales/<locale>/default.json`，找到以该英文短语为键的 JSON 条目并修改其值。如果翻译包中已有源短语，但词典中尚无对应键，请添加条目。
3. 保持源短语不变，并保留 `:count`、`:other` 和 `:attribute` 等占位符。编写行内验证消息时，在 `inline.json` 中使用相同的源键，并采用无需提及字段名称的措辞。 保留其他占位符和复数形式。
4. 如果有意让译文与英文短语一致，请将该短语添加到此语言区域 `_settings.json` 的 `excludes` 列表中。Toolkit 会在生成翻译状态时读取此列表。
5. 提交拉取请求，在标题中注明语言区域代码，例如 `[fr]: Updated validation messages`。

如果发现上游项目的源短语有拼写错误，请向上游报告，以便同时更正源文本和查找键。

例如，`src/Library/locales/fr/default.json` 中的法语默认条目如下。

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

它在 `src/Library/locales/fr/inline.json` 中对应的行内条目如下。

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

要修改 Mona 发布的译文，请编辑 Library 中的 `default.json` 和 `inline.json`。部分包仍保留旧版的 `php.json` 和 `json.json` 词典，生成翻译包状态报告的自动化工作流也会读取这些文件。

要更新国家或地区、货币或语言区域名称，请编辑相应包的 `data/` 目录下的词典。请参阅[语言区域和译名](./locales.md)。

## 提议添加语言区域 {#propose-a-locale}

先查看[规范语言区域代码](./available-locales.md)。如果代码已在列表中，请在 `src/Library/locales/` 中添加或更新对应词典。Toolkit 可以[创建缺少的目录](./toolkit.md#manage-locale-directories)。如需提议新代码，请在 [Laravel-Lang 讨论区](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization)发帖。

## 开发软件包 {#develop-packages}

使用 Git、Composer 2、PHP `^8.3` 以及 `intl`、`json` 和 `zip` 扩展。测试使用内存数据库，因此还需启用 `pdo_sqlite`。在仓库根目录执行命令。

```bash
composer install
composer check-platform-reqs
```

根目录的 Composer 项目会自动加载 `src/<Package>/src/` 下的代码。请在每个发布包自己的 `src/<Package>/composer.json` 中声明依赖。内部依赖使用 `self.version` 约束，以便将同一版本发布的软件包一起安装。

使用 `workbench/` 中的 Testbench 应用在本地验证包的行为。`composer serve` 会构建该应用并启动开发服务器。要从仓库根目录检查路由和 Artisan 命令，请使用 Testbench 命令行。

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

依赖项检查工作流会单独安装每个包，并通过 Composer 的 `path` 仓库引用其他已检出的包。更改包的依赖后，请检查该包能否独立安装，并运行仓库根目录的测试。安装步骤和 Laravel 版本矩阵见工作流。[依赖项测试工作流](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## 在本地运行检查 {#run-local-checks}

修改词典后，在仓库根目录安装 JavaScript 依赖并运行 Biome 检查。Toolkit 的 `status` 命令会报告值与源键相同的条目。请检查翻译包的源文件以找出词典中尚无条目的短语，并用 `git status --short` 查看生成的报告。[Toolkit 命令](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

修改 PHP 代码后，在仓库根目录运行 Pest 和 PHPStan。模型生成测试共用 Testbench 的应用目录，因此功能测试应与 CI 一样串行运行。架构测试可以并行运行。要检查单个功能，请将其测试文件或目录作为参数传给 Pest。

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

如果路由测试因 Testbench 的开发设置而生成以 `http://localhost:8000` 开头的 URL，请在运行 Pest 前将测试进程的 `APP_URL` 设为 `http://localhost`。

`composer style` 会规范化 Composer 文件并应用 Rector 和 Pint 的修复。提交拉取请求前请检查改动。要查看行覆盖率，请启用 Xdebug 并运行 `composer run-script --timeout=0 test:coverage`。

修改清单后，请运行 CI 使用的架构验证。

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

清单格式见[翻译插件](./plugins.md)。

## 编辑文档 {#edit-the-documentation}

当前版本的英文页面位于 `docs/docs/`，译文位于 `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`。更改说明时，请更新对应的译文，并保持标题 ID 一致，确保各语言的链接可用。Mona 之前的存档位于 `docs/versioned_docs/version-legacy/`，译文在 `docs/i18n/` 下对应的 `version-legacy/` 目录中。

使用 `docs/package.json` 要求的 Node.js 版本。在 `docs/` 下执行文档命令。

```bash
cd docs
npm ci
npm start
```

使用 `npm start -- --locale fr` 预览单种语言。停止开发服务器，再执行生产构建，以验证所有语言的链接、锚点和搜索索引。

```bash
npm run typecheck
npm run build
npm run serve
```

打开 `serve` 输出的地址，检查搜索和生成的 Markdown 文件。静态网站会输出到 `docs/build/`。

## 发布版本 {#publish-a-release}

合并到 `main` 后，各包的子树会导出到对应仓库的 `main` 分支，Mona 的发布草稿也会更新。检查草稿，然后在 Mona 中使用统一的版本标签发布。仅创建标签不会触发发布工作流。

发布版本后，工作流会导出标签并在各包仓库中创建对应的版本，同时触发文档网站的构建和部署。发布后请检查这两个工作流。要先预览包的拆分结果，请从 `main` 手动运行 **Export Packages**，并启用 `dry-run`。[导出软件包](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [文档](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

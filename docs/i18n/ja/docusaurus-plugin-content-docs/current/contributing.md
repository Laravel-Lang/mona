---
title: コントリビュート
description: "Mona のチェックアウトから翻訳、PHP パッケージ、ドキュメントに貢献します。"
---

[Mona リポジトリ](https://github.com/Laravel-Lang/mona)をフォークし、Mona の `main` ブランチに対してプルリクエストを作成します。Mona は原文テンプレート、共有翻訳、PHP パッケージ、ドキュメントを管理します。個別のパッケージリポジトリには Mona から内容がエクスポートされます。

## 翻訳を修正または追加する {#fix-or-add-a-translation}

1. パックの `src/<Package>/source/` ディレクトリで英語の文言を探します。たとえば、Laravel Framework と Laravel 公式パッケージの原文は `src/Lang/source/` 以下にあります。 各上流パッケージのバージョンで使用する原文ファイルは、パックの `laravel_lang.json` に記載されています。
2. `src/Library/locales/<locale>/default.json` を開き、英語の文言を JSON キーとして検索し、その値を編集します。原文がパックにあり辞書になければ、エントリを追加します。
3. 原文は維持し、`:count`、`:other`、`:attribute` などのプレースホルダーも保持します。インラインのバリデーションメッセージでは、`inline.json` の同じ原文キーにフィールド名を含めない文言を使用します。 ほかのプレースホルダーや複数形の形式も保持します。
4. 翻訳を意図的に英語の原文と同じにする場合は、そのロケールの `_settings.json` にある `excludes` に文言を追加します。Toolkit は翻訳状況の生成時にこの一覧を使用します。
5. タイトルにロケールコードを付けてプルリクエストを作成します。例は `[fr]: Updated validation messages` です。

上流プロジェクトの原文に誤字を見つけた場合は、原文と検索キーを同時に更新できるよう、上流に報告してください。

`src/Library/locales/fr/default.json` のフランス語の既定のエントリを例にします。

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

対応する `src/Library/locales/fr/inline.json` のインラインエントリを次に示します。

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Mona で公開する翻訳は Library の `default.json` と `inline.json` を編集します。一部のパックには旧形式の `php.json` と `json.json` が残っており、パックの翻訳状況を集計する自動ワークフローもこれらを使用します。

国名、通貨名、ロケール名を更新するには、該当するパッケージの `data/` ディレクトリにある辞書を編集してください。[ロケールと翻訳済みの名称](./locales.md)も参照してください。

## ロケールを提案する {#propose-a-locale}

[正規ロケールコード](./available-locales.md)を確認してください。コードが一覧にあれば、`src/Library/locales/` で辞書を追加または更新します。Toolkit で[不足しているディレクトリを作成](./toolkit.md#manage-locale-directories)できます。新しいコードは [Laravel-Lang のディスカッション](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization)で提案してください。

## パッケージを開発する {#develop-packages}

Git、Composer 2、PHP `^8.3` と `intl`、`json`、`zip` 拡張を使用します。テストはインメモリデータベースを使用するため、`pdo_sqlite` を有効にしてください。コマンドはリポジトリのルートから実行します。

```bash
composer install
composer check-platform-reqs
```

ルートの Composer プロジェクトは `src/<Package>/src/` のコードを自動読み込みします。公開する各パッケージの依存関係は、それぞれの `src/<Package>/composer.json` に記述します。内部依存関係の `self.version` 制約により、同じリリースのパッケージを一緒にインストールできます。

ローカルでパッケージの動作を試すには、`workbench/` の Testbench アプリケーションを使用します。`composer serve` でアプリケーションをビルドして開発サーバーを起動します。ルートからルート一覧や Artisan コマンドを確認するには Testbench CLI を使います。

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

依存関係を確認する CI では、チェックアウト済みの他のパッケージを指す Composer の `path` リポジトリを使って各パッケージを個別にインストールします。依存関係を変更した場合は、単独でインストールできることを確認し、ルートのテストスイートも実行します。設定と Laravel のバージョン構成はワークフローに記載されています。[依存関係テストのワークフロー](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## ローカルで検証する {#run-local-checks}

辞書を変更した場合は、ルートで JavaScript の依存関係をインストールし、Biome の検証を実行します。Toolkit の `status` は値が原文キーと一致する項目を報告します。辞書にない文言はパックの原文ファイルで確認し、生成されたレポートは `git status --short` で確認してください。[Toolkit のコマンド](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

PHP を変更した場合は、リポジトリのルートで Pest と PHPStan を実行します。モデル生成のテストは Testbench のアプリケーションディレクトリを共有するため、機能テストは CI と同様に直列で実行します。アーキテクチャテストは並列実行できます。単一機能を検証するには、そのファイルやディレクトリを Pest に渡します。

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Testbench の開発設定によってルーティングテストが `http://localhost:8000` の URL を生成する場合は、Pest の実行前にテストプロセスの `APP_URL` を `http://localhost` に設定します。

`composer style` は Composer のファイルを正規化し、Rector と Pint でコードを修正します。プルリクエストを送る前に変更を確認してください。行カバレッジを計測するには Xdebug を有効にし、`composer run-script --timeout=0 test:coverage` を実行します。

マニフェストを変更した場合は CI と同じスキーマ検証を実行します。

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

マニフェストの形式は[翻訳プラグイン](./plugins.md)を参照してください。

## ドキュメントを編集する {#edit-the-documentation}

現行版の英語ページは `docs/docs/` に、翻訳は `docs/i18n/<language>/docusaurus-plugin-content-docs/current/` にあります。手順を変更したときは対応する翻訳ページも更新し、言語をまたぐリンクのために見出し ID を維持します。Mona 以前のアーカイブは `docs/versioned_docs/version-legacy/` にあり、翻訳は `docs/i18n/` 以下の対応する `version-legacy/` ディレクトリにあります。

`docs/package.json` が指定するバージョンの Node.js を使用します。ドキュメントのコマンドは `docs/` で実行してください。

```bash
cd docs
npm ci
npm start
```

翻訳を個別にプレビューするには `npm start -- --locale fr` を実行します。開発サーバーを停止し、本番ビルドで各言語のリンク、見出しアンカー、検索インデックスを検証します。

```bash
npm run typecheck
npm run build
npm run serve
```

`serve` が表示するアドレスを開き、検索と生成された Markdown を確認します。静的サイトの出力先は `docs/build/` です。

## リリースを公開する {#publish-a-release}

`main` にマージすると、各パッケージのサブツリーが対応するリポジトリの `main` にエクスポートされ、Mona のリリース下書きが更新されます。下書きを確認し、共通のバージョンタグで Mona のリリースを公開します。タグを作成するだけでは公開ワークフローは実行されません。

公開すると、各パッケージリポジトリにタグと対応するリリースが作成され、ドキュメントサイトのビルドとデプロイが始まります。公開後に両方のワークフローを確認してください。公開前に分割結果を確認するには、`main` で **Export Packages** を手動実行し、`dry-run` を有効にします。[パッケージのエクスポート](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [ドキュメント](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

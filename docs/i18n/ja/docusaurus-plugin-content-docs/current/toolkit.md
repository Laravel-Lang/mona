---
title: 翻訳 Toolkit
description: チェックアウトした Mona から翻訳の原文や辞書を保守、移行、インポートします。
---

Toolkit は翻訳パックを保守するためのコマンドラインツールです。Mona をチェックアウトし、ルートの Composer 依存関係をインストールしてから実行します。Laravel アプリケーションに翻訳を公開するには [Artisan コマンド](./translations.md)を使用します。

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## 翻訳状況を確認する {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

デフォルトでは、このコマンドはプロジェクト内に `locales/<locale>/_status.json` を書き出します。保存された翻訳が原文と同じエントリをスタイル別に列挙します。`locales/_settings.json` と `locales/<locale>/_settings.json` の除外設定を組み合わせ、空のレポートは削除します。辞書にまったく含まれない文言を見つけるには、パックのソースファイルと比較してください。

レポートを別のディレクトリに `<locale>.json` として書き出すには、`--output-dir` を指定します。相対パスは現在の作業ディレクトリを基準に解決され、書き出すレポートがある場合に出力先ディレクトリが作成されます。

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

意図的に原文と同じ語句は、該当する `_settings.json` の `excludes` に追加します。

```json
{
  "excludes": ["Laravel"]
}
```

## ロケールのディレクトリを管理する {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` は、すべての正規[ロケールコード](./available-locales.md)に対して不足しているディレクトリを作成します。新しいディレクトリは空です。`delete-locale` は `locales/` の直下にある、一覧に名前がないディレクトリを削除します。実行前に対象を確認してください。ロケールのエイリアスや独自コードを含む場合は特に確認が必要です。

## 古くなった辞書エントリを削除する {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` は `src/` の下に `laravel_lang.json` マニフェストを持つパッケージから原文を収集し、`src/Library/locales/` の辞書を書き直します。
- `sync:package` は 1 つのパックの `source/` ディレクトリを読み込み、`locales/` にある辞書の未使用エントリを削除します。削除後に空になった辞書も削除します。

どちらも使用中の文言の既存翻訳を維持し、`_settings.json` のように名前がアンダースコアで始まるファイルを保持します。新しい文言の翻訳は辞書に直接追加してください。

引数を省略すると、`sync:library` は作業コピーの `src/`、`sync:package` は現在の作業ディレクトリを使います。どちらも正規ロケールコードのディレクトリを処理します。

## 旧版の辞書を移行する {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

`source/` と `locales/` を含む旧版のパックを指定します。ロケールのサブディレクトリを置く移行先は `--target` で指定します。このオプションを省略すると、旧版のプロジェクトの `locales/` に結果が書き込まれます。

- `json.json` と `json-inline.json` はそれぞれ `default.json` と `inline.json` に移行されます。キーには既存の原文をそのまま使用します。
- `php.json` と `php-inline.json` はそれぞれ `default.json` と `inline.json` に移行されます。`source/` 内の PHP ファイルを参照し、ネストされたキーを対応する原文に置き換えます。
- `_excludes.json` のエントリは、移行先のロケールの `_settings.json` にある `excludes` に移動します。

Toolkit は翻訳を移行先の辞書にマージします。キーが重複した場合は移行した翻訳が優先されます。旧版の両方の形式に同じ文言の翻訳がある場合は、PHP 辞書が優先されます。

Toolkit は `source/` のファイルを保持します。移行に成功した旧版の辞書ファイルと除外リストは、別の移行先を指定した場合も削除されます。アプリケーションやプラグインマニフェストの変更については[アップグレードガイド](./upgrade.md)を参照してください。

## ZIP アーカイブから原文をインポートする {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` は最終的な出力先を指定します。`--paths` はアーカイブのルートからの相対ディレクトリごとに繰り返し指定できます。
- 対象の PHP ファイルと JSON ファイルは、ファイル名を保って出力先にコピーされます。PHP ファイルはバイト列としてコピーされます。同名の JSON ファイルはマージされ、インポート元の値が優先されます。
- `--only-copy` を指定しない場合、Toolkit はリテラルキーを持つ対応済み翻訳呼び出しを検索します。文言が見つかれば、例の `framework.json` のように、出力先の親ディレクトリにちなんだ名前の JSON ソースを生成します。
- このコマンドには PHP の ZIP 拡張機能が必要です。公開前に変更を一時領域に準備し、公開が失敗した場合は出力先を以前の状態に戻そうとします。

`--url` と `--directory` は必須です。`--paths` で指定したディレクトリは再帰的に走査します。翻訳キーの検出だけを行うには `--paths` を省略します。見つかったキーはパックに追加する前に確認してください。

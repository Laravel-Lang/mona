---
title: リリースノート
description: Laravel-Lang のバージョン管理とプラットフォームの互換性について説明します。
---

## バージョン管理と互換性 {#versioning-and-compatibility}

Laravel-Lang はセマンティック バージョニングに従います。互換性を破る変更やプラットフォームのサポート終了はメジャーリリースで行います。公式パッケージは同じリリースバージョンを使用します。[セマンティック バージョニング](https://semver.org/)

現行バージョンは PHP `^8.3` が必要で、Laravel 12 と 13 に対応しています。使用する Laravel のバージョンによっては、より新しい PHP が必要です。Common は依存パッケージを通じて `intl` 拡張機能と `json` 拡張機能も必要とします。

リリースタグと更新内容は [Mona のリリース](https://github.com/Laravel-Lang/mona/releases)を参照してください。Laravel のメンテナンス期間は [Laravel のサポートポリシー](https://laravel.com/docs/releases#support-policy)に記載されています。

## パッケージの公開状況 {#package-availability}

Mona の `main` にプッシュすると、エクスポートワークフローが各パッケージの `main` ブランチを更新します。Mona のリリースを公開すると、そのタグを各パッケージのリポジトリにエクスポートします。

`20.x` をインストールまたはアップグレードする前に、必要な Composer パッケージのバージョンが Packagist にあることを確認してください。Mona のリリースと対応する Composer パッケージは公開時期が異なる場合があります。 [パッケージエクスポートの実行履歴](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

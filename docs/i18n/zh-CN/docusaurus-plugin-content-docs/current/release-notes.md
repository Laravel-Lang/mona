---
title: 发布说明
description: Laravel-Lang 的版本管理和平台兼容性。
---

## 版本管理和兼容性 {#versioning-and-compatibility}

Laravel-Lang 遵循语义化版本规范。破坏性变更及停止支持某个平台均在主版本中引入。官方包使用统一的发布版本。[语义化版本](https://semver.org/)

当前版本要求 PHP `^8.3`，支持 Laravel 12 和 13。你所用的 Laravel 版本可能要求更新的 PHP 版本。Common 还通过依赖项要求安装 `intl` 和 `json` 扩展。

发布标签和补丁说明见 [Mona 发布记录](https://github.com/Laravel-Lang/mona/releases)。Laravel 的维护日期请参阅 [Laravel 支持政策](https://laravel.com/docs/releases#support-policy)。

## 软件包可用性 {#package-availability}

向 Mona 的 `main` 推送更改后，导出工作流会更新各软件包仓库的 `main` 分支。发布 Mona 版本时，工作流会将其标签导出至各软件包仓库。

安装 `20.x` 或升级至该版本前，请确认 Packagist 已提供所需的 Composer 软件包版本。Mona 发行版和对应的 Composer 软件包可能在不同时间发布。 [软件包导出运行记录](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

---
title: Notas de versão
description: Versionamento e compatibilidade de plataformas do Laravel-Lang.
---

## Versionamento e compatibilidade {#versioning-and-compatibility}

O Laravel-Lang segue o versionamento semântico. Alterações incompatíveis e encerramentos de suporte a plataformas ficam para as versões principais. Os pacotes oficiais compartilham o mesmo número de versão. [Versionamento semântico](https://semver.org/)

A versão atual requer PHP `^8.3` e é compatível com Laravel 12 e 13. Sua versão do Laravel pode exigir uma versão mais recente do PHP. O Common também requer as extensões `intl` e `json` por meio de suas dependências.

As tags e notas de correção estão disponíveis nos [lançamentos do Mona](https://github.com/Laravel-Lang/mona/releases). Consulte a [política de suporte do Laravel](https://laravel.com/docs/releases#support-policy) para ver os prazos de manutenção.

## Disponibilidade dos pacotes {#package-availability}

O fluxo de exportação atualiza os branches `main` dos repositórios de pacotes após um push para `main` no Mona. Quando uma versão do Mona é publicada, sua tag é exportada para esses repositórios.

Antes de instalar `20.x` ou atualizar para essa versão, verifique no Packagist se a versão do pacote do Composer está disponível. A versão do Mona e os pacotes do Composer correspondentes podem ser publicados em momentos diferentes. [Execuções da exportação de pacotes](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

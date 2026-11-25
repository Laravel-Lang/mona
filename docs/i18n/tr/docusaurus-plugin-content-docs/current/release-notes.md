---
title: "Sürüm notları"
description: "Laravel-Lang’in sürümleme ve platform uyumluluğu."
---

## Sürümleme ve uyumluluk {#versioning-and-compatibility}

Laravel-Lang, Semantik Sürümleme kurallarını izler. Geriye dönük uyumluluğu bozan değişiklikler ve platform desteğinin kaldırılması ana sürümlerde yapılır. Resmî paketler aynı sürüm numarasını kullanır. [Semantik Sürümleme](https://semver.org/)

Mevcut sürüm PHP `^8.3` gerektirir ve Laravel 12 ile 13’ü destekler. Kullandığınız Laravel sürümü daha yeni bir PHP sürümü gerektirebilir. Common, bağımlılıkları aracılığıyla `intl` ve `json` eklentilerini de gerektirir.

Sürüm etiketlerini ve yama notlarını [Mona sürümlerinde](https://github.com/Laravel-Lang/mona/releases) bulabilirsiniz. Laravel’in bakım takvimi için [Laravel destek politikasına](https://laravel.com/docs/releases#support-policy) bakın.

## Paketlerin kullanılabilirliği {#package-availability}

Mona’nın `main` dalına gönderim yapıldığında dışa aktarma iş akışı paket depolarındaki `main` dallarını günceller. Bir Mona sürümü yayımlandığında sürüm etiketi paket depolarına aktarılır.

`20.x` sürümünü kurmadan veya bu sürüme yükseltmeden önce gereken Composer paketi sürümünün Packagist’te bulunduğunu doğrulayın. Mona sürümü ve karşılık gelen Composer paketleri farklı zamanlarda yayımlanabilir. [Paket dışa aktarma çalıştırmaları](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

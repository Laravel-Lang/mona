---
title: "Reliz qaydlari"
description: "Laravel-Lang versiyalash tartibi va platformalar bilan mosligi."
---

## Versiyalash va moslik {#versioning-and-compatibility}

Laravel-Lang semantik versiyalashga amal qiladi. Moslikni buzuvchi o‘zgarishlar va platformani qo‘llab-quvvatlashni to‘xtatish yirik relizlarda amalga oshiriladi. Rasmiy paketlar bir xil versiya raqamiga ega. [Semantik versiyalash](https://semver.org/)

Joriy versiya PHP `^8.3` ni talab qiladi va Laravel 12 hamda 13 ni qo‘llab-quvvatlaydi. Laravel versiyangiz yangiroq PHP versiyasini talab qilishi mumkin. Common o‘z bog‘liqliklari orqali `intl` va `json` kengaytmalarini ham talab qiladi.

Reliz teglari va tuzatish qaydlari [Mona relizlarida](https://github.com/Laravel-Lang/mona/releases) mavjud. Laravel’ga xizmat ko‘rsatish muddatlari uchun [Laravel qo‘llab-quvvatlash siyosatiga](https://laravel.com/docs/releases#support-policy) qarang.

## Paketlarning mavjudligi {#package-availability}

Mona’ning `main` tarmog‘iga o‘zgarish yuborilganda eksport jarayoni paket repozitoriylaridagi `main` tarmoqlarini yangilaydi. Mona relizi e’lon qilinganda uning tegi paket repozitoriylariga eksport qilinadi.

`20.x` ni o‘rnatish yoki unga yangilashdan oldin kerakli Composer paketi versiyasi Packagist’da mavjudligini tekshiring. Mona relizi va unga mos Composer paketlari turli vaqtda e’lon qilinishi mumkin. [Paket eksporti ishlari](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)

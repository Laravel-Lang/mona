---
title: "Hissa qo‘shish"
description: "Mona’ning mahalliy nusxasidan tarjimalar, PHP paketlari va hujjatlarga hissa qo‘shing."
---

[Mona repozitoriysini](https://github.com/Laravel-Lang/mona) fork qiling va uning `main` tarmog‘iga pull request oching. Mona manba andozalari, umumiy tarjimalar, PHP paketlari va hujjatlarni saqlaydi. Alohida paket repozitoriylariga kontent Mona’dan eksport qilinadi.

## Tarjimani tuzatish yoki qo‘shish {#fix-or-add-a-translation}

1. Inglizcha iborani paketning `src/<Package>/source/` katalogidan toping. Masalan, Laravel Framework va rasmiy Laravel paketlarining manbalari `src/Lang/source/` katalogida. Paketning `laravel_lang.json` faylida asl paketning har bir versiyasida ishlatiladigan manba fayllari ko‘rsatilgan.
2. `src/Library/locales/<locale>/default.json` faylini oching. Inglizcha iborani JSON kaliti sifatida topib, qiymatini tahrirlang. Manba ibora paketda bor-u, lug‘atda bo‘lmasa, yozuv qo‘shing.
3. Manba iborani o‘zgartirmang va `:count`, `:other` hamda `:attribute` kabi o‘rinbosarlarni saqlang. Maydon yonida ko‘rsatiladigan tekshiruv xabari uchun `inline.json` faylida o‘sha manba kalitidan foydalaning va maydon nomini tilga olmagan holda ifodalang. Boshqa o‘rinbosarlar va ko‘plik shakllarini ham saqlang.
4. Agar tarjima ataylab inglizcha ibora bilan bir xil bo‘lsa, uni lokalning `_settings.json` faylidagi `excludes` ro‘yxatiga kiriting. Toolkit tarjima holatini shakllantirganda bu ro‘yxatdan foydalanadi.
5. Sarlavhasida lokal kodi bo‘lgan pull request oching, masalan, `[fr]: Updated validation messages`.

Yuqori oqim loyihasining manba iborasida imlo xatosini topsangiz, manba ibora va unga mos qidiruv kaliti birga yangilanishi uchun o‘sha loyihaga xabar bering.

Masalan, `src/Library/locales/fr/default.json` faylidagi fransuzcha standart yozuv quyidagicha:

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

`src/Library/locales/fr/inline.json` faylidagi unga mos yozuv:

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Mona e’lon qiladigan tarjimalar uchun Library’dagi `default.json` va `inline.json` fayllarini tahrirlang. Ba’zi paketlarda eski `php.json` va `json.json` lug‘atlari saqlangan. Paket holatining avtomatik hisoboti ham shu fayllardan foydalanadi.

Mamlakat, valyuta yoki lokal nomlarini yangilash uchun tegishli paketning `data/` katalogidagi lug‘atni tahrirlang. [Lokallar va tarjima qilingan nomlar](./locales.md) bo‘limiga qarang.

## Lokal taklif qilish {#propose-a-locale}

[Kanonik lokal kodlarini](./available-locales.md) tekshiring. Kod ro‘yxatda bo‘lsa, `src/Library/locales/` ichidagi lug‘atni qo‘shing yoki yangilang. Toolkit [yetishmayotgan kataloglarni yaratishi](./toolkit.md#manage-locale-directories) mumkin. Yangi kod taklif qilish uchun [Laravel-Lang muhokamasini](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization) oching.

## Paketlarni ishlab chiqish {#develop-packages}

Git, Composer 2 va `intl`, `json`, `zip` kengaytmalari bilan PHP `^8.3` dan foydalaning. Sinovlar xotiradagi ma’lumotlar bazasidan foydalanadi, shuning uchun `pdo_sqlite` kengaytmasini yoqing. Buyruqlarni repozitoriy ildizidan bajaring.

```bash
composer install
composer check-platform-reqs
```

Ildizdagi Composer loyihasi `src/<Package>/src/` dagi kodni avtomatik yuklaydi. Har bir e’lon qilinadigan paket bog‘liqliklarini uning `src/<Package>/composer.json` faylida ko‘rsating. Ichki bog‘liqliklar `self.version` cheklovidan foydalanadi. Bu bir xil relizdagi paketlarni birga o‘rnatish imkonini beradi.

Paket ishlashini mahalliy tekshirish uchun `workbench/` dagi Testbench ilovasidan foydalaning. `composer serve` ilovani yig‘adi va ishlab chiqish serverini ishga tushiradi. Repozitoriy ildizidan marshrutlar va Artisan buyruqlarini tekshirish uchun Testbench buyruq qatoridan foydalaning.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

Bog‘liqliklar uchun CI jarayoni mahalliy nusxadagi boshqa paketlarni ko‘rsatuvchi Composer `path` repozitoriysi yordamida har bir paketni alohida o‘rnatadi. Paket bog‘liqliklari o‘zgarsa, paketni alohida o‘rnatishni tekshiring va ildizdagi sinovlarni bajaring. Sozlash qadamlari va Laravel versiyalari ish jarayonida ko‘rsatilgan. [Bog‘liqliklarni sinash jarayoni](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## Mahalliy tekshiruvlarni bajarish {#run-local-checks}

Lug‘at o‘zgarganda repozitoriy ildizida JavaScript bog‘liqliklarini o‘rnating va Biome tekshiruvlarini bajaring. Toolkit’dagi `status` buyrug‘i qiymati manba kalitiga teng yozuvlar uchun hisobot yaratadi. Lug‘atda yo‘q iboralarni paketning manba fayllaridan tekshiring. Yaratilgan hisobotlarni `git status --short` bilan ko‘rib chiqing. [Toolkit buyruqlari](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

PHP o‘zgarishlari uchun repozitoriy ildizida Pest va PHPStan’ni ishga tushiring. Modellarni yaratish sinovlari Testbench ilovasi katalogidan birgalikda foydalanadi, shuning uchun funksional sinovlarni CI’dagi kabi ketma-ket bajaring. Arxitektura sinovlarini parallel bajarish mumkin. Bitta funksiyani tekshirish uchun Pest’ga mos fayl yoki katalogni bering.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Agar marshrut sinovlari Testbench ishlab chiqish sozlamalari sababli `http://localhost:8000` manzilidan foydalansa, Pest’ni ishga tushirishdan oldin sinov jarayonida `APP_URL` qiymatini `http://localhost` qilib belgilang.

`composer style` Composer fayllarini bir xil ko‘rinishga keltiradi hamda Rector va Pint tuzatishlarini qo‘llaydi. Pull request yuborishdan oldin o‘zgarishlarni ko‘rib chiqing. Qatorlar qamrovini o‘lchash uchun Xdebug’ni yoqing va `composer run-script --timeout=0 test:coverage` buyrug‘ini bajaring.

Manifest o‘zgarganda CI’da ishlatiladigan sxema tekshiruvini bajaring.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Manifest formati uchun [tarjima plaginlari](./plugins.md) bo‘limiga qarang.

## Hujjatlarni tahrirlash {#edit-the-documentation}

Joriy inglizcha sahifalar `docs/docs/` katalogida, tarjimalari `docs/i18n/<language>/docusaurus-plugin-content-docs/current/` katalogida. Ko‘rsatmalar o‘zgarganda mos tarjima sahifalarini yangilang va tillar orasidagi havolalar ishlashi uchun sarlavha identifikatorlarini saqlang. Mona’dan oldingi hujjatlar `docs/versioned_docs/version-legacy/` katalogida, tarjimalari esa `docs/i18n/` ichidagi tegishli `version-legacy/` kataloglarida arxivlanadi.

`docs/package.json` talab qiladigan Node.js versiyasini ishlating. Hujjatlar buyruqlarini `docs/` katalogidan bajaring.

```bash
cd docs
npm ci
npm start
```

Bitta tarjimani ko‘rish uchun `npm start -- --locale fr` buyrug‘ini bajaring. Ishlab chiqish serverini to‘xtating va barcha tillardagi havolalar, sarlavha ankerlari hamda qidiruv indeksini tekshirish uchun yakuniy yig‘ishni bajaring.

```bash
npm run typecheck
npm run build
npm run serve
```

Qidiruv va yaratilgan Markdown fayllarini tekshirish uchun `serve` ko‘rsatgan manzilni oching. Statik sayt `docs/build/` katalogiga yoziladi.

## Relizni e’lon qilish {#publish-a-release}

`main` tarmog‘iga birlashtirish har bir paket qismini o‘z repozitoriysidagi `main` tarmog‘iga eksport qiladi va Mona reliz qoralamasini yangilaydi. Qoralamani ko‘rib chiqing va Mona’da umumiy versiya tegi bilan relizni e’lon qiling. Faqat teg yaratish reliz jarayonlarini boshlamaydi.

Relizni e’lon qilish uning tegini eksport qiladi va har bir paket repozitoriysida mos relizni yaratadi. Hujjatlar saytini yig‘ish va joylashtirish ham boshlanadi. Relizdan keyin ikkala jarayonni tekshiring. Paketlar qanday ajratilishini oldindan ko‘rish uchun `main` tarmog‘idan **Export Packages** jarayonini `dry-run` yoqilgan holda ishga tushiring. [Paketlarni eksport qilish](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Hujjatlar](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)

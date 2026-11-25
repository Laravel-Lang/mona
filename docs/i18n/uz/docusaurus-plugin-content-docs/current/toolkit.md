---
title: "Tarjima vositalari to‘plami"
description: "Mona’ning mahalliy nusxasida tarjima manbalari va lug‘atlarga xizmat ko‘rsating, ularni ko‘chiring va import qiling."
---

Toolkit — tarjima paketlariga xizmat ko‘rsatadigan buyruq satri vositasi. Repozitoriy ildizidagi Composer bog‘liqliklari o‘rnatilgach, uni Mona’ning mahalliy nusxasidan ishga tushiring. Laravel ilovasida tarjima nashr qilish uchun [Artisan buyruqlaridan](./translations.md) foydalaning.

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Tarjima holatini tekshirish {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

Standart holatda buyruq loyiha ichiga `locales/<locale>/_status.json` faylini yozadi. Unda saqlangan tarjimasi manba iboraga mos keladigan yozuvlar uslub bo‘yicha guruhlanadi. Buyruq `locales/_settings.json` va `locales/<locale>/_settings.json` dagi istisnolarni birlashtiradi hamda bo‘sh hisobotlarni o‘chiradi. Lug‘atda umuman yo‘q iboralarni topish uchun lug‘atni paket manba fayllari bilan taqqoslang.

Hisobotlarni alohida katalogga `<locale>.json` fayllari sifatida yozish uchun `--output-dir` parametrini ko‘rsating. Joriy ish katalogi nisbiy yo‘llar uchun asos bo‘ladi. Agar belgilangan katalog hali mavjud bo‘lmasa, buyruq uni birinchi hisobotni yozayotganda yaratadi.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Lokal kataloglarini boshqarish {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` barcha kanonik [lokal kodlari](./available-locales.md) uchun yetishmayotgan kataloglarni yaratadi. Yangi kataloglar dastlab bo‘sh bo‘ladi. `delete-locale` `locales/` ostida bevosita joylashgan va nomi shu ro‘yxatga kirmaydigan har bir katalogni o‘chiradi. Uni bajarishdan oldin maqsad katalogni tekshiring, ayniqsa unda lokal muqobil nomlari yoki maxsus kodlar bo‘lsa.

## Eskirgan lug‘at yozuvlarini olib tashlash {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` `src/` ostida `laravel_lang.json` manifesti bor paketlardan manba iboralarni yig‘adi va `src/Library/locales/` lug‘atlarini qayta yozadi.
- `sync:package` bitta paketning `source/` katalogini o‘qiydi va `locales/` katalogidagi ishlatilmayotgan yozuvlarni olib tashlaydi. Bu tozalashdan keyin bo‘sh qolgan lug‘atlarni o‘chiradi.

Ikkala buyruq ham faol iboralarning mavjud tarjimalarini hamda `_settings.json` singari pastki chiziq bilan boshlanadigan fayllarni saqlaydi. Yangi iboralarning tarjimasini bevosita lug‘atga qo‘shing.

## Eski lug‘atlarni ko‘chirish {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Tarkibida `source/` va `locales/` bo‘lgan eski paketni uzating. Maqsad — lokal quyi kataloglari joylashishi kerak bo‘lgan katalog. Skriptlarda `--target` ni uzating. Interaktiv ishga tushirishda u berilmasa, so‘raladi.

- `php.json` va `json.json` fayllari `default.json` fayliga birlashadi. Yangi kalitlar asl manba iboralaridir.
- `php-inline.json` va `json-inline.json` fayllari `:attribute` bor manba iboralar uchun `inline.json` fayliga birlashadi.
- `_excludes.json` yozuvlari maqsad lokalning `_settings.json` faylidagi `excludes` ro‘yxatiga o‘tkaziladi.

Toolkit eski fayllarni saqlaydi va yozuvlarni maqsad lug‘atlari bilan birlashtiradi. Kalitlar to‘qnashsa, ko‘chirilgan tarjima ustun turadi. Ikkala eski format ham bitta iboraning tarjimasini bersa, PHP lug‘ati ustun turadi. Faol lug‘atni almashtirishdan oldin migratsiyani ko‘rib chiqish uchun alohida maqsad katalogidan foydalaning. Ilova yoki plagin manifestidagi o‘zgarishlar haqida [yangilash qo‘llanmasiga](./upgrade.md) qarang.

## ZIP arxividan manbalarni import qilish {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` yakuniy maqsad manzilini belgilaydi. `--paths` ni arxiv ildiziga nisbatan kataloglar uchun bir necha marta uzatish mumkin.
- Tanlangan PHP va JSON fayllari katalog tuzilmasisiz, o‘z nomi bilan bevosita maqsad katalogiga nusxalanadi. PHP fayllari baytma-bayt nusxalanadi. Bir xil nomdagi JSON fayllari birlashtiriladi va kiruvchi qiymatlar ustun turadi.
- `--only-copy` berilmasa, Toolkit literal kalitli qo‘llab-quvvatlanadigan tarjima chaqiruvlarini qidiradi. Iboralarni topsa, maqsad katalogining ota katalogi nomida JSON manba faylini yozadi, masalan, yuqoridagi `framework.json`.
- Buyruq PHP’ning ZIP kengaytmasini talab qiladi. U nashr qilishdan oldin o‘zgarishlarni vaqtincha joyga tayyorlaydi va nashr muvaffaqiyatsiz bo‘lsa, oldingi maqsad katalogini tiklashga urinadi.

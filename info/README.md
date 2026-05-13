# Опорный корпус гайдов (редакционная стадия)

Каталог `info/guides/` — черновик статей для GenshinTop: новые тексты с нуля, без копирования из [`content/guides/`](../content/guides). После выкатки текущие гайды переносятся в [`content/guides-archive/`](../content/guides-archive), а файлы из `info/guides/` копируются в `content/guides/`.

## Инвентаризация исходного корпуса (на момент работ)

| Кластер | Ориентиры в старом `content/guides` | Куда смысл уходит в новом корпусе |
|---------|-------------------------------------|-----------------------------------|
| Баннеры | `banner-*`, десятки датированных URL | Один вечнозелёный гайд по молитвам + хаб `/guides/banners` |
| Патчи | `update-*` | Гайд «как читать патч» + хаб `/guides/patches` |
| Новичок | файлы с «нович», прохождение AR | Три столпа: быстрый старт, AR, типичные ошибки |
| Экономика | промокоды, примогемы, крутки | Примогемы, молитвы/питти, промокоды (`promocodes`) |
| Мета | тир-листы, бездна | Как читать тир-листы + введение во Витую Бездну |
| Отряды | «отряд», роли, синергии | Один гайд по ролям и резонансам |
| Ивенты | `events-*`, временные режимы | Ивенты: награды и приоритеты |
| TCG | `tcg-*`, Священный призыв | Вход в TCG |
| Домены | `domain-*`, расписание | Смола, домены, приоритет фарма |
| Боссы | `bossy-*`, тактики | Мировые и еженедельные, материалы |
| Квесты | дорожная карта архонтов | Квесты архонтов без спойлеров |
| Техника | ПК, железо, FPS | Платформы и производительность |
| Прочее | артефакты, оружие, мир | Отдельные базовые столпы |

**Счётчик:** в `content/guides` было **232** файла `.md` (актуализация через `php scripts/guides-refactor-inventory.php`, если PHP доступен в среде).

## Матрица: хаб → статья (финальные slug)

Все статьи плоским списком в `info/guides/*.md` — так же их заберёт [`ContentRepository`](../lib/ContentRepository.php).

| Хаб сайта | Файл / slug | Назначение |
|-----------|------------|------------|
| newbie | `bystriy-start-teyvat` | Первые часы в Teyvat: что делать и в каком порядке |
| newbie | `uroven-priklyucheniya-sovety` | AR, расход смолы, ранние вехи |
| newbie | `oshibki-novichka` | Частые ловушки нового игрока |
| economy | `primogemy-kopim-tratim` | Источники примогемов и планирование |
| economy | `molitvy-piti-zhurnal` | Виды молитв, мягкий/твёрдый питти, ожидание |
| tier-list | `tir-listy-kak-chitat` | Как относиться к тир-листам и смене меты |
| tier-list | `vitaya-bezdna-vvedenie` | Витая Бездна: цели, циклы, минимум теории |
| tier-list / party | `otryady-roli-elementy` | Роли, резонансы, скелет отряда |
| events | `iventy-nagrady-vremya` | Временные ивенты и приоритет наград |
| tcg | `tcg-vvod-svyashchennyy-prizyv` | Старт в Священном призыве семи |
| domains | `smola-domeny-raspisanie` | Смола, подземелья, логика фарма |
| bosses | `bossy-materialy-talantov` | Типы боссов и материалы прокачки |
| quests | `kvesty-arhontov-bez-spoilerov` | Дорожная карта сюжета (осторожно со спойлерами) |
| codes | `promocodes` | Промокоды: активация и официальные источники |
| banners | `bannery-sobytiya-molitvy` | Стандартная и событийная молитва, «кто в ротации» |
| patches | `obnovleniya-patch-notes` | Как читать анонсы патчей и что проверить в игре |
| tech | `pc-mobilnaya-optimizaciya` | ПК и мобильные клиенты, производительность |
| economy / domains | `artefakty-farm-i-vybor` | Артефакты: сеты, статус линий, фарм |
| newbie | `oruzhie-vozvyshenie-materialy` | Оружие и возвышение без углубления в каждый тип |
| newbie | `issledovanie-mira-prioritety` | Открытие мира: очки, телепорты, приоритет контента |

### Волна 2 (расширение базы — 9 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| party | `stihii-i-reaktsii-baza` | Стихии и реакции без перегруза таблицами |
| economy | `mora-i-traty-akkaunta` | Мора: траты и дисциплина |
| newbie | `ochki-smelosti-statui-arhontov` | Окуляры и стамина со статуй |
| general | `reputaciya-regionov-i-porucheniya` | Репутация и поручения |
| general | `kooperativ-miry-i-farm` | Кооператив и этикет |
| economy / бой | `eda-i-kulinariya-nabory` | Еда, бафы, алхимия |
| tier-list | `spiral-i-ciklicheskie-boevye` | Бездна и циклические боевые режимы |
| lore | `teyvat-lor-bez-spoilerov` | Вход в лор без спойлеров |
| newbie | `zhurnal-priklyucheniy-nagrady` | Справочник приключений и награды |

**Итого после волны 2:** 29 статей в `info/guides/` и `content/guides/`; slug `promocodes` сохранён для преемственности URL.

### Волна 3 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| economy / прокачка | `talanty-knigi-i-korony` | Таланты: книги по дням, еженедельники, короны |
| party | `energiya-vosstanovlenie-vzryva` | Энергия, частицы и стабильные ульты |
| newbie | `chekhlist-materialov-personazha` | Чеклист материалов на нового героя |
| general | `chaynik-bezmyatezhnosti-vvedenie` | Чайник безмятежности: награды и доверие |
| economy | `sozvezdiya-i-krutki-investicii` | Созвездия и разумный бюджет круток |
| party / выживание | `schit-lechenie-i-vyzhivaemost` | Щит vs лечение, нагрузка на отряд |
| party | `elementarnyy-masterstvo-kogda-kachat` | EM для реакций и билдов |
| newbie / мир | `gadzhety-i-komfort-issledovaniya` | Гаджеты и удобство исследования |

**Итого:** 37 статей в `info/guides/` и `content/guides/`.

## Редкие стандарты

См. [`docs/GUIDE_EDITORIAL.md`](../docs/GUIDE_EDITORIAL.md). Внутренние ссылки — на `/guides/...` и `/characters/...` только к тем slug, что реально существуют после выкатки.

# Опорный корпус контента (редакция GenshinTop)

Два параллельных контура: **гайды** и **персонажи**. Сайт отдаёт страницы только из **`content/guides/`** и **`content/characters/`** (см. [`ContentRepository`](../lib/ContentRepository.php)). Каталоги под `info/` — редакционное зеркало: правки и новые материалы ведите в `info/…`, затем **копируйте** в `content/…` для публикации.

## Гайды

Каталог `info/guides/` — черновик статей: новые опорные тексты с нуля по плану матрицы (без смыслового копирования из устаревшего архива). После смены корпуса старые URL уходят в [`content/guides-archive/`](../content/guides-archive); актуальные файлы из `info/guides/` копируются в [`content/guides/`](../content/guides).

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

Все статьи плоским списком в `info/guides/*.md`; после выкатки в **`content/guides/`** их подхватывает [`ContentRepository`](../lib/ContentRepository.php).

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

### Волна 4 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| party | `krit-shans-i-krit-uron-baza` | Шанс и урон крита, ориентир 1:2 |
| party | `bonus-stihii-i-soprotivleniya-vragov` | Бонус стихии, физический урон, резисты |
| economy | `magazin-bleska-prioritety-zvezdnogo` | Магазин звёздного блеска и блеска |
| newbie | `syuzhetnye-klyuchi-i-zadaniya-legend` | Сюжетные ключи и легенды |
| party | `poryadok-slota-otryada-i-snimok` | Слоты отряда и снимок бафов |
| newbie | `kompas-sokrovishch-i-poiski-sundukov` | Компас сокровищ и сундуки |
| party | `kodeks-protivnikov-kak-chitat` | Кодекс противников перед боем |
| economy / прокачка | `rastvoritel-snov-i-materialy-bossov` | Растворитель снов и материалы боссов |

**Итого:** 45 статей в `info/guides/` и `content/guides/`.

## Персонажи

Каталог `info/characters/` — зеркало всех карточек из [`content/characters/`](../content/characters). Редактируйте здесь (или добавляйте новые `*.md`), затем копируйте в `content/characters/` тем же способом, что гайды из `info/guides/` → `content/guides/`.

- **Frontmatter:** `name`, `title`, `element`, `weapon`, `rating`, `sourceSlug`, списки `relatedWeapons`, `relatedArtifacts`, `relatedGuides` — как в продакшене; slug файла = slug URL (`sourceSlug`).
- **`relatedGuides`:** только slug из актуального опорного набора гайдов (таблицы выше).

**Счётчик:** **197** файлов `.md` в `info/characters/` и в `content/characters/` (включая подборки `why-pull-*` и сравнения `*-vs-*` — они тоже лежат в общем каталоге персонажей).

## Редкие стандарты

См. [`docs/GUIDE_EDITORIAL.md`](../docs/GUIDE_EDITORIAL.md). Внутренние ссылки — на `/guides/...` и `/characters/...` только к тем slug, что реально существуют после выкатки.

# Опорный корпус контента (редакция GenshinTop)

Два параллельных контура: **гайды** и **персонажи**. Сайт отдаёт **гайды** из **`info/guides/*.md`** и **карточки персонажей** из **`info/characters/*.md`** (см. [`ContentRepository`](../lib/ContentRepository.php)). **Новые и правки гайдов** — только в **`info/guides/`**; **карточки персонажей** — только в **`info/characters/`** (без дублирования в `content/characters/` — там пояснение в **`content/characters/README.md`**).

## Гайды

Каталог **`info/guides/`** — канон опорных гайдов для сайта (новые тексты по матрице ниже, без смыслового копирования из архива). Массовый старый корпус лежит в [`content/guides-archive/`](../content/guides-archive); пустой слой **`content/guides/`** без `.md` — см. [`content/guides/README.md`](../content/guides/README.md).

## Инвентаризация исходного корпуса (на момент работ)

| Кластер | Ориентиры в архиве `content/guides-archive` | Куда смысл уходит в новом корпусе |
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

**Счётчик:** в **`content/guides-archive`** было **232** файла `.md` (актуализация: `php scripts/guides-refactor-inventory.php`, если PHP доступен в среде).

## Матрица: хаб → статья (финальные slug)

Все статьи плоским списком в **`info/guides/*.md`**; [`ContentRepository`](../lib/ContentRepository.php) читает их оттуда напрямую.

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

**Итого после волны 2:** 29 статей в `info/guides/`; slug `promocodes` сохранён для преемственности URL.

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

**Итого:** 37 статей в `info/guides/`.

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

**Итого:** 45 статей в `info/guides/`.

### Волна 5 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| general | `uroven-mira-i-nagrady-teyvata` | Уровень мира: награды, сложность, понижение |
| economy | `regionalnaya-valyuta-magaziny` | Региональная валюта и городские магазины |
| newbie | `rybolovstvo-v-teyvate` | Рыбалка: старт, наживка, обмен |
| economy | `pochtovye-nagrady-i-rozygryshi` | Почта, веб-награды, официальные раздачи |
| general | `akkaunt-hoyoverse-i-kross-save` | Аккаунт HoYoverse и кросс-платформа |
| economy | `besplatnye-geroi-sobytiya` | Бесплатные герои: ивенты и магазины |
| newbie | `sunduki-respawn-i-fakty` | Сундуки и мифы про респавн |
| newbie | `karta-metki-i-navigatsiya` | Карта: метки, фильтры, навигация |

### Волна 6 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| economy / прокачка | `sunduk-artefaktov-i-obmen` | Сундук артефактов и обмен осколков |
| economy | `ekspeditsii-i-zabory-resursov` | Экспедиции и пассивная добыча |
| newbie | `lokalnye-spetsialtety-sbor` | Локальные специалитеты регионов |
| general | `nedelnij-rutin-smola-bossy` | Недельный рутин: смола и боссы |
| newbie | `fotorezhim-teyvata` | Фоторежим и снимки |
| general | `bezopasnost-akkaunta-2fa` | Безопасность аккаунта и фишинг |
| general | `storonnie-instrumenty-i-meta` | Сторонние сайты и калькуляторы |
| economy | `stol-alhimii-i-konvertatsiya` | Стол алхимии и конверсия дропа |

### Волна 7 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| general | `nastrojki-klienta-i-dostupnost` | Настройки клиента и комфорт |
| newbie | `smena-vremeni-sutok-v-igre` | Смена времени суток в меню |
| newbie | `mobilnye-teleporty-i-ankory` | Персональные телепорты и якоря |
| economy | `strannye-torgovtsy-i-limity` | Лимитные витрины NPC |
| general | `inventar-i-sortirovka` | Инвентарь и работа с артефактами |
| newbie | `povtornyj-prohod-podzemelij` | Одноразовые подземелья vs фарм |
| general | `skrytye-dostizheniya-i-namekarty` | Скрытые ачивки и карточки профиля |
| newbie | `knigi-receptov-i-kraft-list` | Рецепты, крафт и учёт чертежей |

**Итого после волн 5–7:** 69 статей в `info/guides/`.

### Волна 8 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| economy | `boevoy-propusk-i-gnosticheskiy-gimn` | Боевой пропуск и Gnostic Hymn |
| economy | `parametricheskiy-transformator` | Параметрический преобразователь |
| economy | `ley-linii-tsvety-i-resursy` | Лей-линии, цветы, книги опыта и мора |
| economy | `ezhednevnye-porucheniya-nagrady` | Ежедневные поручения (комиссии) |
| economy | `kondensirovannaya-smola-i-khrupkaya` | Конденсированная и крепкая смола |
| general | `druzhba-personazhey-i-kompanyonstvo` | Дружба и компаньонство |
| newbie | `zagotovki-oruzhiya-i-prototipy` | Заготовки и прототипы оружия |
| general | `hangauty-i-istorii-zavisimyh` | Hangout и истории зависимых |

### Волна 9 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| economy | `hoyolab-ezhednevnyy-vhod` | HoYoLAB и ежедневные отметки |
| economy | `probnye-personazhi-i-nagrady` | Пробные персонажи и награды |
| newbie | `opasnosti-sredy-led-pesok-buri` | Опасности среды регионов |
| newbie | `vyinoslivost-lezt-plavat-boy` | Выносливость: лазание, плавание, бой |
| general | `fizicheskiy-i-elementalnyy-uron` | Физический и стихийный урон |
| general | `elementarnye-shchity-protivnikov` | Стихийные щиты врагов |
| newbie | `verstak-krafta-i-materialy` | Верстак крафта и материалы чайника |
| economy | `chaynik-monety-ukrasheniya-doverie` | Чайник: монеты, украшения, доверие |

### Волна 10 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| tech | `grafika-kachestvo-render-masshtab` | Графика: качество и масштаб рендера |
| banner | `pity-garant-i-sobytiya-oruzhiya` | Пити, гарант и события оружия |
| newbie | `opyt-priklyucheniya-istochniki-ar` | Источники опыта приключения (AR) |
| economy | `uluchshenie-oruzhiya-dublikaty` | Пробуждение оружия дубликатами |
| general | `navigatsiya-po-kvestam-i-markeram` | Навигация по квестам |
| economy | `valyuta-sobytiy-lavki-iventov` | Ивентовая валюта и лавки |
| patch | `predzagruzka-patch-mesto-na-diske` | Предзагрузка патча и место на диске |
| economy | `genesis-kristally-i-oplata` | Genesis Crystals и оплата |

**Итого после волн 8–10:** 93 статьи в `info/guides/`.

### Волна 11 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| general | `derevya-podnosheniya-i-kollektsii` | Деревья подношений и коллекции |
| newbie | `seelie-i-mir-golovolomok` | Феи Seelie и мирные головоломки |
| economy | `ruda-usileniya-oruzhiya` | Руда и кристаллы опыта оружия |
| newbie | `vezerver-i-vodnye-marshruty` | Везервёр и водные маршруты |
| tech | `zvuk-ozvuchka-yazykovye-pakety` | Звук и языковые пакеты |
| economy | `stellarnoe-vozobnovlenie-veteran` | Звёздное воссоединение |
| lore | `mirovye-kvesty-i-arhonty` | Мировые квесты и архонты |
| newbie | `kniga-iskatelya-nagrady` | Путеводитель искателя |

### Волна 12 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| newbie | `regionalnye-gadzhety-elektrogranum` | Региональные гаджеты (электрогранум и др.) |
| newbie | `tipy-sundukov-nagrady-shkala` | Типы сундуков и шкала наград |
| general | `ispytaniya-vremeni-trialy` | Испытания времени |
| general | `teatr-demo-personazhey` | Театр и демо персонажей |
| general | `bystrye-sbory-otryada` | Пресеты отряда |
| general | `kooperativ-kick-limity` | Кик, лимиты и этикет коопа |
| tech | `ofitsialnaya-karta-interaktivnaya` | Официальная интерактивная карта |
| newbie | `server-region-uid-asiya-evropa` | Выбор сервера и UID |

### Волна 13 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| general | `tcg-reyting-taverny-nagrady` | Рейтинг TCG и таверны |
| general | `namekarty-profil-igroka` | Неймкарты и профиль |
| general | `podzemelya-solo-ili-koop-smola` | Домены соло и кооп |
| economy | `korona-zhemchuzhina-mudrosti` | Корона талантов |
| general | `boevye-parametry-ekrana` | Боевые параметры на экране |
| economy | `oprosy-hoyoverse-nagrady` | Опросы HoYoverse |
| tech | `mobilnye-dop-resursy-skachka` | Доп. ресурсы на мобильных |
| tech | `ping-zaderzhka-kooperativ` | Пинг в кооперативе |

### Волна 14 (расширение базы — 8 статей)

| Хаб / тема | Файл / slug | Назначение |
|------------|-------------|------------|
| economy | `konvertatsiya-knig-talantov` | Конвертация книг талантов |
| general | `mirovyi-boss-i-materialy-domena` | Боссы и материалы доменов |
| economy | `partnerskiy-razdel-topap-genshintop` | Партнёрский раздел и топ-ап |
| newbie | `vosstanovlenie-akkaunta-podderzhka` | Восстановление аккаунта |
| tech | `uvedomleniya-privatnost-klient` | Уведомления и приватность |
| general | `vozrast-regionalnye-ogranicheniya` | Возраст и региональные ограничения |
| economy | `kosmeticheskie-krylya-skin` | Крылья и скины |
| general | `cheat-modding-politika` | Читы, моды и честная игра |

**Итого после волн 11–14:** 125 статей в `info/guides/`.

## Персонажи

Каталог **`info/characters/`** — канон карточек для сайта (см. **`content/characters/README.md`** про пустой слой `content/characters/`).

- **Стандарт текста:** [`docs/CHARACTER_EDITORIAL.md`](../docs/CHARACTER_EDITORIAL.md) — короткий профиль без копипасты талантов из клиента, ссылками на опорные гайды.
- **Снимок до пересборки:** при массовом переходе на новый формат прежние тексты копируются скриптом в **`content/characters-archive/`** (резерв, не в индексе сайта).
- **Пересборка тел из YAML:** `pwsh scripts/rebuild-character-pages.ps1` из корня — копирует `*.md` из **`info/characters/`** в `characters-archive/`, генерирует новое **тело** из существующего frontmatter (~**114** профильных slug + компактный формат для **~83** вспомогательных страниц).
- **Frontmatter:** `name`, `title`, `element`, `weapon`, `rating` (опц.), `sourceSlug`, `relatedWeapons`, `relatedArtifacts`, `relatedGuides`; при пересборке добавляются `summary`, `gameVersion`, `reviewedAt`, если их не было.

**Счётчик:** **197** файлов `.md` (включая `why-pull-*`, `*-vs-*` и прочие вспомогательные URL в том же каталоге).

## Редкие стандарты

См. [`docs/GUIDE_EDITORIAL.md`](../docs/GUIDE_EDITORIAL.md) и [`docs/CHARACTER_EDITORIAL.md`](../docs/CHARACTER_EDITORIAL.md). Внутренние ссылки — на `/guides/...` и `/characters/...` только к тем slug, что реально существуют после выкатки.

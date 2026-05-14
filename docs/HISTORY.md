# История проекта

Журнал ключевых итераций. Подробные правки — в commit-ах git.

## Фаза 1.13.0 — content/ как параллельный каталог нового сайта

### Цель

Создать в `content/` полный IA-каркас по [`docs/PLAN.md`](PLAN.md). Все разделы PLAN с _index.md, статьями и каталогами. Источники: `info/`, `archive/` + веб-добор. Полные wiki-карточки на marquee-героев и оружие/артефакты, draft на остальное. Сайт пока продолжает читать `info/` через `lib/ContentRepository.php`; `content/` — параллельный каталог под будущее переключение.

### Создан каркас content/ по docs/PLAN.md (B0)
- Что: новый каталог `content/` с 60+ файлами каркаса: README, STYLE, шаблоны frontmatter (`_templates/` × 11), индексы всех разделов PLAN.
- Почему: `info/` остаётся живым каталогом сайта; новая структура нужна для последующего перевода под обновлённый дизайн.
- Файлы: `content/README.md`, `content/STYLE.md`, `content/_templates/*.md`, `content/{characters,weapons,artifacts,materials,enemies,guides,tools,world,news,community}/_index.md` и `_by-*` подиндексы.
- Решение: жёсткое зеркало PLAN; разные оси навигации (по стихии/оружию/региону для героев; по типу/звёздности для оружия; по источнику для артефактов).

### Гайды: основы и прохождения (B1)
- Что: 5 walkthroughs (`archon-quests`, `story-quests`, `hangouts`, `world-quests`, `quest-navigation`) — полные. 4 PLAN-anchored основы (`adventure-rank`, `elements-overview`, `elemental-reactions`, `character-roles`) — полные. ~16 поддерживающих основ — компактные draft.
- Почему: основа для перевода новичков и core-аудитории на новую редакцию.
- Источник: `info/guides/*.md` + веб-добор для актуальных формулировок.

### Гайды: продвинутые (B2)
- Что: `build-theory`, `damage-formula`, `energy-and-rotations`, `resonance-and-auras`, `patch-analysis`, `tier-lists`, `abyss-and-cycles` (полные); `crit-stats`, `elemental-mastery`, `enemy-resistances`, `pity-and-banners`, `external-tools` (draft).
- Почему: PLAN-блок «Advanced Guides» закрыт по опорным темам.

### Мир и Лор (B3)
- Что: 7 регионов (Mondstadt, Liyue, Inazuma, Sumeru, Fontaine, Natlan, Snezhnaya) — полные. 6 лор-материалов (`teyvat-overview`, `archons`, `abyss`, `khaenri-ah`, `celestia`, `descenders`) — полные. 12 фракций (Фатуи + предвестники-индекс, Орден Фавониус, адепты, яксы, Орден Бездны, Академия Сумеру, Эремиты, Марешоссе, племена Натлана, Сёгунат, Tri-Commission, Миллелит, Цисин). 2 NPC (Паймон, Катерина).
- Почему: каркас лора готов к расширению.

### Новости и баннеры (B4)
- Что: опорный `evergreen-prayer-guide` + `standard-banner` + `chronicled-banner`. 6 ключевых патчей (1.0, 2.0, 3.0, 4.0, 5.0, 6.0). 2 sample баннера (Чжон Ли, Кадзуха).
- Почему: дать систему координат до фарма всех 60 патчей и 80 баннеров.

### Tools / Community / Materials / Enemies (B5)
- Что: 4 tools (`interactive-map`, `damage-calculator`, `team-builder`, `daily-planner`). 4 community (`forum`, `user-guides`, `best-guides`, `faq`). 3 sample materials (`cecilia`, `silk-flower`, `anemo-gem`). 4 sample enemies (`hilichurl`, `abyss-mage`, `anemo-hypostasis`, `childe`).
- Почему: показать формат и наполнение каждого подраздела.

### Оружие (B6)
- Что: 5 sample 5★ по типам (`staff-of-homa`, `mistsplitter-reforged`, `wolfs-gravestone`, `amos-bow`, `skyward-atlas`, `primordial-jade-winged-spear`). 5 ключевых 4★ (`favonius-sword`, `dragons-bane`, `sacrificial-fragments`, `rust`, `prototype-archaic`).
- Почему: задать формат карточки и покрыть meta-критичные оружия.

### Артефакты (B7)
- Что: 11 ключевых сетов: VV, Crimson Witch, Noblesse Oblige, Emblem of Severed Fate, Blizzard Strayer, Gilded Dreams, Thundering Fury, Heart of Depth, Marechaussee Hunter, Golden Troupe, Deepwood Memories, Archaic Petra.
- Почему: покрытие 80% реальных билдов меты.

### Персонажи (B8 wave 1)
- Что: 14 marquee-карточек в полном wiki-формате (таланты, созвездия, билды, команды): Чжон Ли, Беннетт, Кадзуха, Ху Тао, Райдэн Сёгун, Нахида, Фурина, Невилетт, Сяо, Айяка, Гань Юй, Сян Лин, Тарталья, Сюй Цю, Е Лань, Венти, Мавуика, Скирк (draft), Дилюк. Плюс 8 индексов (по стихии/оружию/региону).
- Почему: задать стандарт глубины + закрыть meta-критичных героев. Остальные ~180 — wave 2.

### Синхронизация (B9)
- Что: VERSION 1.12.2 → 1.13.0 (MINOR — новая возможность контента, обратно-совместимая). Этот журнал. Обновлён `docs/AGENTS.md` (новый раздел про `M-CONTENT-V2`). GRACE-артефакты: добавлен модуль `M-CONTENT-V2` в `grace/knowledge-graph/knowledge-graph.xml`, фаза `Phase-7 ContentV2CatalogScaffold` в `grace/plan/development-plan.xml`, сценарий `V-M-CONTENT-V2` в `grace/verification/verification-plan.xml`.
- Почему: правила проекта (`agents-grace`, `git-version-commit`, `project-history`).

### Что не делалось в этой итерации
- Не перенастраивали `lib/ContentRepository.php` на чтение `content/` (отдельная задача после ревью контента — Phase-7 step-4).
- Не удаляли `info/` (живой каталог сайта).
- Не реализовали интерактивные инструменты (карта, калькулятор, тимбилдер, ежедневник) — только описательные страницы.
- Не сделали форум и UGC-систему — только описательные страницы.
- Wave 2 закроет ~180 оставшихся карточек персонажей, ~150 карточек 4★/3★ оружия, ~50 сетов артефактов, оставшиеся basics-гайды и расширенный бестиарий.

## Фаза 1.14.0 — Wave 2 контента

### Цель

Расширить корпус `content/` до уровня, при котором сайт можно переключать на новую редакцию по большинству разделов. Все новые материалы — компактные wiki-карточки (250-400 слов на персонажа, 100-150 на оружие/артефакт/патч/баннер) с YAML frontmatter, web-источниками и кросс-линками.

### Персонажи Wave 2 (W2-1, W2-2, W2-3)
- Что: +74 компактные карточки персонажей. Mondstadt 16, Liyue 12, Inazuma 11, Sumeru 13, Fontaine 13, Natlan 8, Aloy + Traveler.
- Файлы: `content/characters/*.md` (74 новых).
- Решение: компактный формат (Кратко / Роль / Таланты / Созвездия / Билд / Оружие / Команды / Кому подойдёт / Источники), без полной wiki-глубины — это закроем в Wave 3 для топ-50 героев.

### Оружие Wave 2 (W2-4)
- Что: +36 карточек: 18 5★ (Aquila Favonia, Primordial Jade Cutter, Freedom-Sworn, Light of Foliar Incision, Skyward Pride, Song of Broken Pines, Redhorn Stonethresher, Verdict, Engulfing Lightning, Vortex Vanquisher, Calamity Queller, Skyward Harp, Thundering Pulse, Polar Star, Aqua Simulacra, Elegy for the End, Hunter's Path, Lost Prayer, Memory of Dust, Kagura's Verity, Tulaytullah's Remembrance) + 18 4★/3★.
- Файлы: `content/weapons/*.md`.

### Артефакты Wave 2 (W2-5)
- Что: +16 ключевых сетов (Husk of Opulent Dreams, Tenacity, Bloodstained Chivalry, Pale Flame, Wanderer's Troupe, Gladiator's Finale, Maiden Beloved, Ocean-Hued Clam, Shimenawa, Echoes, Vermillion, Desert Pavilion, Flower of Paradise Lost, Nymph's Dream, Vourukasha's Glow, Song of Days Past, Nighttime Whispers, Fragment of Harmonic Whimsy, Unfinished Reverie, Scroll of the Hero of Cinder City, Obsidian Codex, Retracing Bolide). Итого 38 сетов.
- Файлы: `content/artifacts/*.md`.

### Патчи Wave 2 (W2-6)
- Что: +24 патча: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 3.1–3.8, 4.1–4.8, 5.1–5.7. Итого 42 патча в `live`.
- Файлы: `content/news/patches/*.md`.

### Баннеры Wave 2 (W2-7)
- Что: +9 character-баннеров (Furina Cloud-Strider, Raiden Reign of Serenity, Хутао Moment of Bloom v2, Ёимия, Кадзуха повтор, Венти повтор, Альбедо повтор, Аяка повтор, Эола повтор) + 5 weapon-баннеров (Epitome Invocation для патчей 4.2, 4.6, 5.0, 5.3, 5.7).
- Файлы: `content/news/banners/character-banners/*.md`, `content/news/banners/weapon-banners/*.md`. Итого 19.

### Фракции и NPC (W2-8)
- Что: 11 карточек Фатуи Harbingers (Pierro, Il Dottore, Columbina, Arlecchino-harbinger, Sandrone, Scaramouche, Pulcinella, La Signora, Pantalone, Il Capitano, Tartaglia/Childe). +5 NPC (Дайнслейф, Тимей, Хертa, Линнея, Rex Lapis-NPC, Доктор Ливингстон stub).
- Файлы: `content/world/factions/fatui-harbingers/*.md`, `content/world/npc/*.md`.

### Бестиарий (W2-9)
- Что: +7 common families (Fatui Skirmisher, Slime, Treasure Hoarder, Nobushi, Eremite, Clockwork Meka, Sauroform Tribal Warrior). +6 world-bosses (Primo Geovishap, Maguu Kenki, Jadeplume Terrorshroom, Algorithm of Semi-Intransient Matrix, Emperor of Fire and Iron, Secret-Source Automaton). +3 weekly-bosses (Dvalin/Stormterror, Azhdaha, La Signora). Итого 20 врагов.
- Файлы: `content/enemies/{common,world-bosses,weekly-bosses}/*.md`.

### Materials (W2-10)
- Что: +12 local-specialties (Mondstadt: Windwheel Aster, Calla Lily; Liyue: Glaze Lily, Qingxin; Inazuma: Sea Ganoderma, Naku Weed; Sumeru: Nilotpala Lotus, Padisarah; Fontaine: Lumidouce Bell, Romaritime Flower; Natlan: Bestriding Bear Flower, Dracolite). +6 ascension-gems по стихиям (Pyro/Hydro/Electro/Cryo/Geo/Dendro). Итого 21 материал.
- Файлы: `content/materials/local-specialties/<region>/*.md`, `content/materials/ascension-materials/character/*-gem.md`.

### Синхронизация (W2-11)
- Что: VERSION 1.13.0 → 1.14.0 (MINOR — крупное расширение корпуса контента, обратно совместимое). Этот журнал. Обновлён снимок прогресса в `content/README.md`. GRACE: статусы фазы Phase-7 в `grace/plan/development-plan.xml`, дополнен `M-CONTENT-V2` в `grace/knowledge-graph/knowledge-graph.xml`, чек-лист Wave 2 в `grace/verification/verification-plan.xml`.
- Почему: правила `git-version-commit`, `project-history`, `agents-grace`.

### Итоги Wave 2 (снимок 2026-05-13)

| Раздел | live | draft | stub | total |
|--------|-----:|------:|-----:|------:|
| characters | 114 | 1 | 0 | 115 |
| weapons | 57 | 0 | 0 | 57 |
| artifacts | 38 | 0 | 0 | 38 |
| materials | 20 | 1 | 0 | 21 |
| enemies | 20 | 0 | 0 | 20 |
| guides | 20 | 17 | 0 | 37 |
| world/factions | 16 | 8 | 0 | 24 |
| world/npc | 7 | 0 | 2 | 9 |
| news/banners | 19 | 0 | 0 | 19 |
| news/patches | 42 | 1 | 0 | 43 |
| community | 1 | 3 | 0 | 4 |
| tools | 4 | 0 | 0 | 4 |
| world/regions | 7 | 0 | 0 | 7 |
| world/lore | 6 | 0 | 0 | 6 |

### Что не делалось в Wave 2
- Не переключали `lib/ContentRepository.php` на `content/`.
- Не закрывали полные wiki-карточки персонажей вне marquee-14 (это Wave 3).
- Не наполняли `news/events`, `news/announcements`, расширенный `world/npc`.
- Не правили draft-гайды (basics, advanced) — они получат итоговую редактуру в Wave 3.

## Фаза 1.15.0 — Wave 3 контента (закрытие долгов)

### Цель

Закрыть **все основные draft** Wave 1-2: гайды (basics + advanced), фракции, расширить тонкие разделы (events, announcements, npc, materials), и расширить 4 ключевые compact-карточки персонажей до полного wiki-формата.

### NPC (W3-1)
- Что: +12 NPC (Дайнслейф расширен в W2 ранее, новые: Бо Лай, Верр Голдет, Мадам Пин, Дуньярзад, Николь, Сирин + 5 stub: Дилюк-master, Тома-housekeeper, Мико-shrine, Нахида-archon, Фокалор, Xbalanque + удалён вымышленный el-monstadt-doctor).
- Файлы: `content/world/npc/*.md`. Итого 20 NPC (13 live + 7 stub).

### Фракции (W3-2)
- Что: 6 фракций draft → live с расширенным контентом: eremites, fontaine-marechaussee, millelith, qixing, shogunate, sumeru-akademiya, tri-commission, tribes-of-natlan.
- Файлы: `content/world/factions/*.md`. Итого 24 фракций — все live.

### Ивенты (W3-3)
- Что: 6 ключевых ежегодных ивентов (Lantern Rite, Summer Fantasia, Anniversary, Windblume, Irodori, Sabzeruz). Закрывает раздел `news/events`.
- Файлы: `content/news/events/*.md`.

### Анонсы (W3-4)
- Что: 5 формат-обзоров: Special Program, Maintenance & Compensation, HoYoLAB Web Events, Redeem Codes, Version Roadmap.
- Файлы: `content/news/announcements/*.md`.

### Гайды basics (W3-5)
- Что: 10 draft → live с подробным content: wishes-pity-banners (полная таблица soft-pity), primogems-economy (per-patch budgets), daily-commissions (rewards table), cooking-and-buffs (specialty dishes table), co-op-mode (правила), fishing (bait + The Catch), promo-codes (указатель на announcements), chests-and-respawn (5 типов + respawn timers), enemies-and-codex (категории + reaction-pairing), local-specialties (per-region table + ascension cost).
- Файлы: `content/guides/basics/*.md`. Итого 35 live + 2 draft.

### Гайды advanced (W3-6)
- Что: 5 draft → live: crit-stats (1:2 формула), elemental-mastery (EM-scaling table per reaction), enemy-resistances (RES-таблица + reduction effects), external-tools (Genshin Optimizer, KQM, Akasha и др.), pity-and-banners (точные вероятности + Welkin/BP value).
- Файлы: `content/guides/advanced/*.md`.

### Materials (W3-7)
- Что: +7 материалов: ingredients (raw-meat, fowl, fish, sunsettia, sweet-flower) + ascension-materials (hurricane-seed boss material + book-of-resistance talent-book).
- Файлы: `content/materials/{ingredients,ascension-materials}/*.md`. Итого 28 материалов.

### Расширение персонажей (W3-8)
- Что: 4 ключевые compact-карточки расширены до полного wiki-формата (1500-2500 слов): Альбедо (Geo sub-DPS), Куки Шинобу (Hyperbloom 4★), Алхайтам (Spread main), Камисато Аяка (Freeze main). Включает: лор+сюжет, полная разбивка талантов, созвездия с tier-list, билд (артефакты + статы), оружие (с tier), ротации, 3-4 командные комбо, Spiral Abyss заметки.
- Файлы: `content/characters/{albedo,kuki-shinobu,alhaitham,ayaka}.md`.

### Sync (W3-9)
- Что: VERSION 1.14.0 → 1.15.0 (MINOR — закрытие долгов, расширение, обратно совместимое). Этот журнал. Снимок прогресса в `content/README.md`. GRACE: статус Phase-7 step-3 done, step-4 in-progress (Wave 3 факты), `M-CONTENT-V2` ax-status под Wave 3, `V-M-CONTENT-V2` check-8 (минимумы Wave 3) и scenario-5.
- Почему: правила `git-version-commit`, `project-history`, `agents-grace`.

### Итоги Wave 3 (снимок 2026-05-13)

| Раздел | live | draft | stub | total |
|--------|-----:|------:|-----:|------:|
| characters | 114 | 1 | 0 | 115 |
| weapons | 57 | 0 | 0 | 57 |
| artifacts | 38 | 0 | 0 | 38 |
| materials | 27 | 1 | 0 | 28 |
| enemies | 20 | 0 | 0 | 20 |
| guides | 35 | 2 | 0 | 37 |
| world/factions | 24 | 0 | 0 | 24 |
| world/npc | 13 | 0 | 7 | 20 |
| world/regions | 7 | 0 | 0 | 7 |
| world/lore | 6 | 0 | 0 | 6 |
| news/events | 6 | 0 | 0 | 6 |
| news/announcements | 5 | 0 | 0 | 5 |
| news/banners | 19 | 0 | 0 | 19 |
| news/patches | 42 | 1 | 0 | 43 |
| community | 1 | 3 | 0 | 4 |
| tools | 4 | 0 | 0 | 4 |

### Что не делалось в Wave 3
- Не расширяли все compact-карточки персонажей до full wiki (только 4 примера; 70+ остаются compact).
- Не наполняли community форум-структуру (3 draft остаются).
- Не переключали `lib/ContentRepository.php` на `content/` (Phase-7 step-5).
- Не дополняли `world/npc` 7 stub до full live (требуют отдельных квестовых исследований).

## Фаза 1.16.0 — Wave 4 контента (нулевой draft/stub в рабочих разделах)

### Цель

Закрыть оставшиеся рабочие `draft`/`stub` после Wave 3, очистить индексные страницы персонажей от черновых/ошибочных имён и расширить ещё 4 compact-карточки до full wiki-уровня. Шаблонные `status: stub` в `content/_templates/` оставлены намеренно как пример для будущих материалов.

### Community (W4-2)
- Что: `forum.md`, `user-guides.md`, `best-guides.md` переведены из `draft` в `live`.
- Почему: закрыть community-долг Wave 3 и дать редакционную спецификацию будущего форума, пользовательских гайдов и Best Guides.
- Файлы: `content/community/forum.md`, `content/community/user-guides.md`, `content/community/best-guides.md`.
- Решение: описательные страницы без реализации форумного движка; платформа остаётся отдельной разработкой.

### Basics / одиночные draft (W4-2b, W4-2c)
- Что: `teyvat-lore-no-spoilers.md`, `furniture-housing.md`, `silk-flower.md`, `6.0.md`, `skirk.md` переведены в `live`.
- Почему: в рабочих разделах не должно оставаться draft, если материал уже можно безопасно публиковать.
- Файлы: `content/guides/basics/teyvat-lore-no-spoilers.md`, `content/guides/basics/furniture-housing.md`, `content/materials/local-specialties/liyue/silk-flower.md`, `content/news/patches/6.0.md`, `content/characters/skirk.md`.
- Решение: для `skirk.md` и `6.0.md` убрали неподтверждённые механики/даты и оформили как безопасные live-страницы ожидания с TBA-полями.

### NPC stub → live (W4-3)
- Что: 7 NPC-stub переписаны в `live`: `thoma-housekeeper`, `ningguang-secretary`, `xbalanque`, `focalors`, `miko-shrine`, `nahida-archon`, `diluc-master`.
- Почему: это не полноценные новые NPC, а сюжетные роли играбельных персонажей / лор-фигуры; они нужны для навигации мира, но не должны висеть как пустые стабы.
- Файлы: `content/world/npc/*.md`.
- Решение: оставить как короткие `NPC-role` / `lore-role` страницы с явной связью на основную карточку персонажа.

### Индексы персонажей (W4-4)
- Что: очищены индексы `_by-element`, `_by-region`, `_by-weapon` от черновых и ошибочных имён; обновлены representative lists и ссылки.
- Почему: старые индексы содержали мусорные имена и неверные типы оружия/регионов.
- Файлы: `content/characters/_index.md`, `content/characters/_by-element/*.md`, `content/characters/_by-region/*.md`, `content/characters/_by-weapon/*.md`, точечная очистка старых редакторских пометок в compact-карточках персонажей.
- Решение: индексы не объявлены автогенерированными полными списками; это редакционные навигационные страницы с ключевыми представителями.

### Персонажи compact → full wiki (W4-5)
- Что: 4 карточки расширены до full wiki-уровня: Фишль, Сахароза, Бэй Доу, Навия.
- Почему: закрыть следующий блок мета-важных 4★/5★ персонажей после Wave 3 (Albedo, Kuki, Alhaitham, Ayaka).
- Файлы: `content/characters/fischl.md`, `content/characters/sucrose.md`, `content/characters/beidou.md`, `content/characters/navia.md`.
- Решение: добавлены роли, подробные таланты, созвездия, билды, оружие, команды, ротации, связанные материалы и KQM-источники.

### Синхронизация (W4-6)
- Что: VERSION 1.15.0 → 1.16.0 (MINOR — расширение корпуса и закрытие статусов). Обновлён `content/README.md`, GRACE `M-CONTENT-V2`, Phase-7 step-5 и verification check/scenario Wave 4.
- Почему: правила `project-history`, `agents-grace`, `git-version-commit`.
- Файлы: `VERSION`, `content/README.md`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`.

### Итоги Wave 4

| Раздел | live | draft | stub | total |
|--------|-----:|------:|-----:|------:|
| characters | 115 | 0 | 0 | 115 |
| weapons | 57 | 0 | 0 | 57 |
| artifacts | 38 | 0 | 0 | 38 |
| materials | 28 | 0 | 0 | 28 |
| enemies | 20 | 0 | 0 | 20 |
| guides | 37 | 0 | 0 | 37 |
| world/factions | 24 | 0 | 0 | 24 |
| world/npc | 20 | 0 | 0 | 20 |
| world/regions | 7 | 0 | 0 | 7 |
| world/lore | 6 | 0 | 0 | 6 |
| news/events | 6 | 0 | 0 | 6 |
| news/announcements | 5 | 0 | 0 | 5 |
| news/banners | 19 | 0 | 0 | 19 |
| news/patches | 43 | 0 | 0 | 43 |
| community | 4 | 0 | 0 | 4 |
| tools | 4 | 0 | 0 | 4 |

### Что не делалось в Wave 4
- Не расширяли все remaining compact-карточки персонажей до full wiki; сделаны ещё 4.
- Не переключали сайт на `content/`; `lib/ContentRepository.php` всё ещё читает `info/`.
- Не реализовывали форум/UGC как runtime-функции — только редакционные страницы.

## Фаза 1.17.0 — Миграция Навигации На Content

- Что: Переключение публичного сайта с `info/` на `content/`. Старый `info/` перенесён в `archive/info/`.
- Почему: Запрос пользователя на окончательный перенос контента. План `content-navigation-migration`.
- Файлы: `lib/ContentRepository.php`, `lib/Router.php`, `lib/PageRenderer.php`, `lib/header.php`, `lib/SiteRoutes.php`, `lib/build-sitemap.php`, `docker/genshintop-redirects.conf`, `info/*`.
- Решение:
  - `ContentRepository::allLive()` рекурсивно индексирует `content/`, исключая `_templates`.
  - Добавлены универсальные методы `PageRenderer::contentSectionIndex` и `contentArticle`.
  - В `Router.php` добавлен generic роутинг. Старые адаптеры (`guides()`, `characters()`) оставлены для обратной совместимости, если где-то еще вызываются.
  - Меню, sitemap и 301-редиректы обновлены с учётом новой иерархии (по дереву папок `content/`).

## Фаза 1.17.1 — CI Fix (GitHub Actions)
- Что: Обновление `.github/workflows/docker-image.yml` — добавлена переменная окружения `FORCE_JAVASCRIPT_ACTIONS_TO_NODE24: true`.
- Почему: Исправление предупреждения об устаревании Node.js 20 в GitHub Actions runners (Deprecation of Node 20 on GitHub Actions).
- Файлы: `.github/workflows/docker-image.yml`, `VERSION`.
- Решение: Переход на Node.js 24 для устаревших экшенов (`actions/checkout`, `docker/build-push-action` и др.), чтобы CI продолжил стабильно работать без warning'ов.

## Фаза 1.18.0 — Ревизия навигации и статический sitemap

### Навигация поверх content/
- Что: перестроен порядок роутинга: `/guides` и `/characters` снова обслуживаются обогащёнными страницами с фильтрами, остальные разделы идут через generic content-роутер. Служебные `content/**/_by-*` исключены из индексации. Хлебные крошки для content-страниц переведены на русские лейблы через `SectionLabels`.
- Почему: после миграции на `content/` generic-роутер перехватывал каталоги раньше специальных страниц, а секции вроде `/world` и `/news` могли выглядеть пустыми.
- Файлы: `lib/Router.php`, `lib/ContentRepository.php`, `lib/PageRenderer.php`, `lib/HtmlComponents.php`, `lib/SectionLabels.php`, `lib/bootstrap.php`.
- Решение: специальные маршруты имеют приоритет, карточки гайдов строят URL по фактическому `section/slug`, секционные индексы показывают подразделы и статьи.

### Удаление legacy-редиректов и регионов
- Что: удалены legacy `/regions`-роуты, `regions_data.php`, `docker/genshintop-redirects.conf` и копирование редиректов в Dockerfile.
- Почему: сайт ещё не использовался публично, внешних старых URL нет; 301-слой только усложнял навигацию и ломал часть `/guides/*`.
- Файлы: `lib/Router.php`, `lib/PageRenderer.php`, `lib/regions_data.php`, `docker/genshintop-redirects.conf`, `docker/Dockerfile`, `docker/README.md`, `README.md`.
- Решение: канон регионов — только `/world/regions/*`; неизвестные старые URL получают 404 без редиректов.

### Статический sitemap
- Что: удалён `lib/build-sitemap.php`, из Dockerfile убран `RUN php lib/build-sitemap.php`, добавлен ручной `public/sitemap.xml` на 475 URL.
- Почему: пользователь запросил статический sitemap без генератора; sitemap теперь обычный файл в git.
- Файлы: `public/sitemap.xml`, `lib/build-sitemap.php`, `docker/Dockerfile`, `README.md`, `docs/AGENTS.md`, `grace/**/*.xml`.
- Решение: sitemap поддерживается вручную при добавлении/удалении страниц; без `<lastmod>`, только `loc`, `changefreq`, `priority`.

## Фаза 1.18.1 — Рефакторинг раздела /lootbar

### Плашка-баннер ведёт в одну точку
- Что: `lib/lootbar_banner.php` переписан с `<div>` + inline-ссылка «Хаб пополнения» + CTA → единый кликабельный `<a href="/lootbar/skidki-i-kupony">` на всю плашку с одним `data-reach-goal="lootbar_banner_cta"`.
- Почему: пользователь заметил, что баннер ведёт в две разные точки (`/lootbar` и `/lootbar/skidki-i-kupony`); конверсия должна идти в посадку купонов.
- Файлы: `lib/lootbar_banner.php`, `public/css/site.css` (`.lootbar-banner` стал `display:block` без подчёркивания/синего цвета; удалены неиспользуемые `.lootbar-banner-inline-link` правила; добавлен `:hover`).

### Удаление тонких подстраниц
- Что: удалены 4 «почти пустых» URL `/lootbar/promokod`, `/lootbar/kristally-sotvoreniya`, `/lootbar/blagoslovenie-luny`, `/lootbar/bezopasnost-i-oplata`. В `PageRenderer::lootbarSubpage()` остались только 2 ветки (`skidki-i-kupony`, `kak-popolnit-genshin-impact`); неизвестные слаги — 404 из роутера.
- Почему: каждая подстраница была только заголовок + 1 абзац lead + кнопка наружу — thin content, портит UX и SEO. Сайт ещё не публиковался, ничего перенаправлять не надо.
- Файлы: `lib/PageRenderer.php`, `lib/SiteRoutes.php`, `public/sitemap.xml`.
- Решение: полезная начинка (промокоды, кристаллы, Welkin, безопасность) перенесена секциями с якорями `#promokod`, `#kristally`, `#welkin`, `#bezopasnost` в хаб `/lootbar`.

### Хаб /lootbar обогащён секциями
- Что: в `PageRenderer::lootbarIndex()` под FAQ добавлены 4 содержательные секции с якорями + якорная навигация в начале страницы. Каждая секция содержит короткий полезный абзац и CTA (внутренний на лендинг скидок или внешний партнёрский с собственным UTM: `lootbar_hub_crystals`, `lootbar_hub_welkin`).
- Почему: хаб ощущался полупустым (список подстраниц + 2-вопросный FAQ); теперь это полноценная страница с навигацией и контентом.
- Файлы: `lib/PageRenderer.php`, `public/css/site.css` (`.lootbar-anchor-nav`).

### Удалены упоминания «не официальный магазин HoYoverse» применительно к LootBar
- Что: убраны 2 FAQ-вопроса («LootBar.gg — официальный магазин HoYoverse?» в JSON-LD и HTML на хабе, «LootBar — официальный магазин HoYoverse?» в JSON-LD на лендинге скидок) и предупредительный абзац `Важно: это не официальный магазин HoYoverse…` на лендинге скидок.
- Почему: пользователь попросил убрать ссылку/упоминание, что это неофициальный магазин — формулировка дискредитирует партнёра; информация о домене `lootbar.gg` и безопасности сохранена в нейтральном виде.
- Файлы: `lib/PageRenderer.php`.
- Решение: фразу «GenshinTop — неофициальный фан-сайт» в `/about` и `lib/footer.php` оставили — это про сам сайт, а не про LootBar.

### Внутренние ссылки и редиректы внутри раздела
- Что: коллаут «Топ-ап» на `/about` теперь ссылается на `/lootbar/skidki-i-kupony`, `/lootbar/kak-popolnit-genshin-impact`, `/lootbar`. Кнопки «Безопасность и оплата →» на лендингах скидок и инструкции ведут на якорь `/lootbar#bezopasnost`. FAQ-ссылки в обоих лендингах обновлены на ту же секцию.
- Файлы: `lib/PageRenderer.php`.

### Sync
- Что: VERSION 1.18.0 → 1.18.1 (PATCH — рефакторинг существующего раздела без новых фич). Этот журнал. GRACE — обновлены аннотации `M-PHP-SITE` под новый набор lootbar-маршрутов; в верификации обновлён сценарий по lootbar.
- Файлы: `VERSION`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/**/*.xml`.

## Фаза 1.18.2 — Фавиконки и веб-манифест для роботов

### Полный набор фавиконок
- Что: добавлены растровые фавиконки `public/favicon.ico` (мультиразмерный 16+32 с PNG-payload), `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png` (180×180), `public/icon-192.png`, `public/icon-512.png` (Android/PWA), а также `public/site.webmanifest`. Иконки сгенерированы программно через `System.Drawing` из той же геометрии, что в существующем `public/favicon.svg` (тёмно-фиолетовый фон `#1a1a2e` + золотистая буква «A» `#e8b445`).
- Почему: Яндекс.Вебмастер и часть других роботов писали «не смог загрузить фавиконку», потому что в репозитории был только SVG, а на `/favicon.ico` nginx через `rewrite` отдавал SVG-байты с MIME `image/svg+xml` — некоторые краулеры ожидают именно растровый ICO/PNG.
- Файлы: `public/favicon.ico`, `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png`, `public/icon-192.png`, `public/icon-512.png`, `public/site.webmanifest`.
- Решение: дизайн повторяет SVG-иконку, цвет и пропорции сохранены; ICO собран вручную (header + 2 entry + 2 PNG-blocks), без сторонних библиотек.

### HTML-теги в layout
- Что: в `lib/layout.php` блок `<link rel="icon">` расширен до полного набора: `favicon.ico` (sizes=any для роботов), SVG, PNG 16/32, `apple-touch-icon` 180, ссылка на `site.webmanifest`.
- Файлы: `lib/layout.php`.

### Nginx
- Что: в `docker/nginx-default.conf` убран `rewrite ^ /favicon.svg last;` (он отдавал SVG-байты по `.ico` URL с неверным MIME). Теперь `/favicon.ico` — `try_files $uri /favicon.svg` (реальный ICO с правильным `image/x-icon`, SVG как фолбэк). Добавлены `location = /site.webmanifest` с `application/manifest+json` и `location = /apple-touch-icon.png` со своими cache-headers.
- Почему: краулеры запрашивают именно `/favicon.ico` и проверяют MIME; без корректного MIME иконка считается невалидной.
- Файлы: `docker/nginx-default.conf`.

### Sync
- Что: VERSION 1.18.1 → 1.18.2 (PATCH — SEO-фикс без новой функциональности). Этот журнал. `docs/AGENTS.md` и GRACE — упоминание favicon-набора и manifest в M-PHP-SITE.
- Файлы: `VERSION`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/**/*.xml`.

## Фаза 1.18.3 — Чистка файловой навигации внутри страниц content/

### Контекст
- Что: на публичных страницах разделов (например, `/artifacts`) светилась «редакторская» навигация по файлам репозитория: ссылки `_by-source/domain.md`, `boss.md`, `_templates/artifact.md`, `STYLE.md`, `info/...md`. Лейблы шли в backticks (выглядели как inline-код), а сами URL вели в 404, потому что `_by-*`, `_templates`, `STYLE`, `README`, `info/` исключены из индексации в `lib/ContentRepository.php`. Такие списки дублировали автоматическую сетку «Подразделы / Статьи» в `PageRenderer::contentSectionIndex`.
- Почему: пользователь показал скриншот `/artifacts` и попросил всё переделать.

### Зачистка контента
- Что: переписаны/удалены секции «Навигация / Файлы / Семейства / Шаблон / Стандарт / Индексы» в `_index.md` всех затронутых разделов и две статьи. Где список даёт ценность — переписан на работающие URL без `.md` (например, `/characters/anemo`, `/guides/basics`, `/world/regions/mondstadt`). Где он дублировал автонавигацию — удалён.
- Параллельно: исправлены frontmatter-`related:` и видимые ссылки `/characters/_by-element/X`, `/characters/_by-weapon/X`, `/characters/_by-region/X`, `/weapons/_by-type/X` → на работающие хабы (`/characters/anemo`, `/characters/sword` и т.п.) или общие каталоги (`/characters`, `/weapons`).
- Файлы: `content/artifacts/_index.md`, `content/characters/_index.md`, `content/community/_index.md`, `content/community/user-guides.md`, `content/enemies/_index.md`, `content/enemies/common/_index.md`, `content/enemies/elite/_index.md`, `content/enemies/world-bosses/_index.md`, `content/enemies/weekly-bosses/_index.md`, `content/enemies/world-bosses/anemo-hypostasis.md`, `content/guides/_index.md`, `content/guides/basics/adventure-rank.md`, `content/guides/basics/quick-start-teyvat.md`, `content/materials/_index.md`, `content/materials/ascension-materials/character/{anemo,geo,pyro,dendro,electro,cryo,hydro}-gem.md`, `content/news/_index.md`, `content/news/banners/_index.md`, `content/news/patches/3.0.md`, `content/tools/_index.md`, `content/weapons/_index.md`, `content/weapons/{favonius-sword,mistsplitter-reforged,primordial-jade-winged-spear,rust,wolfs-gravestone,staff-of-homa,amos-bow,skyward-atlas,prototype-archaic}.md`, `content/world/_index.md`, `content/world/factions/{_index,knights-of-favonius,tribes-of-natlan,sumeru-akademiya,fontaine-marechaussee,eremites}.md`, `content/world/lore/_index.md`, `content/world/npc/_index.md`, `content/world/npc/paimon.md`, `content/world/regions/_index.md`, `content/world/regions/{mondstadt,liyue,inazuma,sumeru,fontaine,natlan,snezhnaya}.md`, `content/artifacts/{viridescent-venerer,crimson-witch-of-flames,golden-troupe,thundering-fury,heart-of-depth,archaic-petra}.md`, `content/artifacts/_by-source/strongbox.md`.

### Защита в рендере
- Что: в `ContentRepository::markdownToHtml` после `Parsedown::text()` добавлен private-метод `sanitizeContentLinks()`. Логика: для относительных href срезается суффикс `.md`; ссылки на служебные пути (`_templates/`, `_by-`, `STYLE.md`, `README.md`, `info/`) превращаются в plain text; для лейблов-`<code>foo.md</code>` `<code>` разворачивается в обычный текст без хвоста `.md`. Абсолютные `http(s)://`, `mailto:`, `tel:`, `#` href не трогаются.
- Почему: даёт страховку — даже если автор снова напишет редакторский паттерн `[\`thing.md\`](thing.md)`, страница не получит уродливую плашку и битую ссылку.
- Файлы: `lib/ContentRepository.php`.

### Sync
- Что: VERSION 1.18.2 → 1.18.3 (PATCH — UX-фикс публичной навигации без новых фич). Этот журнал. GRACE: аннотация `M-PHP-SITE` про `sanitizeContentLinks` в `markdownToHtml`; чек в `V-M-PHP-SITE` про отсутствие inline `.md`-ссылок и ссылок на служебные пути в публичном HTML.
- Файлы: `VERSION`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/verification/verification-plan.xml`.

## Фаза 1.18.4 — Hotfix регулярок sanitizeContentLinks

### Что сломалось
- Что: после деплоя 1.18.3 на любой content-странице сыпались PHP-варнинги `preg_match(): Unknown modifier ')'` (строка 200), `Unknown modifier ']'` (строки 221, 222), а следом — `Cannot modify header information - headers already sent`. Вёрстка ломалась: warnings выводились до `<html>`, и шапка/контент рендерились без layout-стилей.
- Почему: в `sanitizeContentLinks` я использовал `#` в качестве delimiter PCRE и одновременно `#` в самих паттернах (`...|#)` для якорной ссылки и `[/?#]` в character-class). PHP-парсер видел первый внутренний `#` как конец паттерна и считал всё после него «модификаторами» (`)`, `]`).

### Фикс
- Что: в `lib/ContentRepository.php::sanitizeContentLinks` сменён delimiter с `#` на `~` для трёх регулярок (`isAbsolute`, `STYLE.md`, `README.md`, `info/`); литерал `#` внутри паттернов экранирован как `\#`. Регулярки `<a ...>` и `<code>...</code>` оставлены на `#` — там в паттернах нет `#`. Регулярка `/\.md(?=$|[?#])/i` использует `/` как delimiter, конфликта нет.
- Файлы: `lib/ContentRepository.php`.
- Решение: универсальный приём — для паттернов, где может встретиться `#` (URL-якоря), использовать другой delimiter. Logic фильтра не меняется; покрывает те же случаи (абсолютные URL, якоря, служебные пути, `.md`-suffix).

### Sync
- Что: VERSION 1.18.3 → 1.18.4 (PATCH — hotfix регрессии). Этот журнал. `grace/knowledge-graph/knowledge-graph.xml` — версия проекта.
- Файлы: `VERSION`, `docs/HISTORY.md`, `lib/ContentRepository.php`, `grace/knowledge-graph/knowledge-graph.xml`.

## Фаза 1.19.0 — Yandex SEO Pass

### Контекст
- Что: пользователь попросил пройтись по гайду Яндекс.Вебмастера и сделать «полную SEO-оптимизацию». Подтверждённые рамки: sitemap остаётся статическим без `<lastmod>` (решение 1.18.0); `og-default.svg` удаляется, fallback OG-картинки — `/apple-touch-icon.png` (180×180).
- Почему: цель — лучше ранжироваться в Яндексе и сократить количество замечаний в Вебмастере.

### A. OG-картинки и мета (`lib/Seo.php`, `lib/layout.php`, `lib/OgManifest.php`, `public/`)
- Что: удалён `public/og-default.svg`. В `Seo` `DEFAULT_OG_IMAGE_PATH = '/apple-touch-icon.png'`, добавлены `DEFAULT_OG_W/H = 180`, helper `Seo::ogImageDimensions()`, у Organization добавлен `description`, `logo` теперь 180×180 PNG. В `OgManifest::imageForEntry()` fallback переведён на `Seo::DEFAULT_OG_IMAGE_PATH`. В `layout.php` размеры `<meta og:image:width/height>` берутся динамически из `Seo::ogImageDimensions()`, добавлены `<meta name="format-detection" content="telephone=no">`, `<link rel="alternate" hreflang="ru">` и `<link rel="alternate" hreflang="x-default">`, расширен `<meta name="robots">` директивой по умолчанию `index, follow, max-image-preview:large, max-snippet:-1` (страницы с явным `noindex, nofollow` сохраняют своё значение).
- Решение: per-page OG-PNG не генерируется (решение пользователя); один растровый fallback вместо SVG, чтобы Яндекс/VK/Telegram не игнорировали превью.

### B. Индексирование и канонизация (`lib/Router.php`, `lib/PageRenderer.php`, `lib/ContentRepository.php`)
- Что: в `Router::dispatch` запросы с trailing slash (кроме `/`) отдают `301` на путь без слеша (с сохранением query). В `Router::send` добавлены HTTP-заголовки `Cache-Control: public, max-age=0, must-revalidate` для всех HTML и `Last-Modified: <RFC1123 GMT>` если страница передала `lastModifiedTs`. Реализован `If-Modified-Since`-handler: если клиент прислал >= нашему mtime и страница не `noindex` — отдаём `304 Not Modified` без тела.
- В `ContentRepository` добавлены `itemMtime(array $item): int` (meta.updatedAt → meta.reviewedAt → meta.date → filemtime) и `latestMtime(?callable $filter): int` (макс. mtime по подмножеству контента).
- В `PageRenderer` все рендеры (`home`, `guidesIndex`, `guideHub`, `guideArticle`, `charactersIndex`, `characterFilteredHubPage`, `characterArticle`, `contentSectionIndex`, `contentArticle`) пробрасывают `lastModifiedTs`. У `guidesIndex` при непустом `?q=` отдаётся `'robots' => 'noindex, follow'`.
- Решение: пара `Last-Modified + If-Modified-Since + 304` с `must-revalidate` экономит обходный бюджет Яндекса и снижает нагрузку на nginx/PHP-FPM без риска показать устаревший контент.

### C. robots.txt (`public/robots.txt`)
- Что: добавлено `Clean-param: q&topic&cat&status&category /guides` в секцию `User-agent: Yandex` для свёртывания клиент-сайд фильтров. Существующие UTM/реф-метки и `Host`/`Sitemap` сохранены.

### D. Markdown-постпроцессор (`lib/ContentRepository.php`)
- Что: после `Parsedown::text()` и `sanitizeContentLinks()` добавлен `seoEnhanceContentHtml()`: всем `<img>` без `loading=` приклеивается `loading="lazy" decoding="async"`; внешним абсолютным `<a href="http(s)://other-host">` без `rel=` добавляется `rel="nofollow noopener" target="_blank"`. Внутренние ссылки `genshintop.ru` и партнёрские LootBar-ссылки (которые собираются вручную в шаблонах с `rel="sponsored"` и не проходят через Markdown) не трогаются.

### E. Структурированные данные (`lib/Seo.php`, `lib/PageRenderer.php`)
- Что: добавлен `Seo::genshinVideoGameNode()` — переиспользуемый узел `VideoGame` (Genshin Impact, HoYoverse). `characterArticle` теперь подмешивает его в `about` рядом с per-character `Thing` и пишет `dateModified`. `contentSectionIndex` получил полноценный `CollectionPage` (inLanguage, isPartOf, breadcrumb-id) рядом с `BreadcrumbList`. `contentArticle` теперь генерирует `Article` (headline, description, inLanguage, image=apple-touch-icon, author/publisher, dateModified, about=VideoGame) — раньше отдавался только Breadcrumbs.

### F. IndexNow (`lib/IndexNow.php`, `bin/indexnow-ping.php`, `public/<key>.txt`, `docker/env.example`)
- Что: добавлен клиент протокола IndexNow (Яндекс — официальный участник). Ключ по умолчанию `cfee9df50829202f695cf93c8b24f554` положен в `public/cfee9df50829202f695cf93c8b24f554.txt` (требование протокола — файл с тем же содержимым, что и ключ). Имя ключа можно переопределить переменной `INDEXNOW_KEY`. CLI-скрипт `bin/indexnow-ping.php` читает `public/sitemap.xml`, поддерживает `--match=REGEX`, `--limit=N`, `--dry-run`, отправляет URL пакетами по 10000 на `https://yandex.com/indexnow`. Шаблон `docker/env.example` дополнен секцией IndexNow и примерами вызова.
- Решение: авто-триггер не делаем — у проекта нет CMS-хуков; пинг запускается руками после деплоя контента.

### Sync
- Что: `VERSION` 1.18.4 → 1.19.0 (MINOR — добавлены новые возможности: HTTP-условные ответы, IndexNow, доп. JSON-LD-узлы). Этот журнал. `docs/AGENTS.md` обновлён под новую модель OG/IndexNow/Last-Modified. `grace/knowledge-graph/knowledge-graph.xml` — экспорты `IndexNow`, `Router::send` (304/Last-Modified), `ContentRepository::{itemMtime,latestMtime,seoEnhanceContentHtml}`, `Seo::{ogImageDimensions,genshinVideoGameNode}`, добавлен путь `bin/indexnow-ping.php`. `grace/plan/development-plan.xml` — шаг «Yandex SEO pass». `grace/verification/verification-plan.xml` — `V-M-PHP-SITE` сценарии: 301 для trailing slash, 304 для If-Modified-Since, noindex при `?q=`, отсутствие 404 на `/og-default.svg`, лениво-загружаемые `<img>` в Markdown.
- Файлы: `VERSION`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/**/*.xml`, `lib/Seo.php`, `lib/layout.php`, `lib/OgManifest.php`, `lib/Router.php`, `lib/PageRenderer.php`, `lib/ContentRepository.php`, `lib/IndexNow.php`, `bin/indexnow-ping.php`, `public/robots.txt`, `public/cfee9df50829202f695cf93c8b24f554.txt`, удалён `public/og-default.svg`, обновлён `docker/env.example`.

### Аудит кода сайта
- Что: проведён read-only аудит PHP-рантайма, контентной маршрутизации, sitemap/robots, Docker/nginx и документации проекта.
- Почему: запрос пользователя на полный аудит ошибок, нестыковок, мусорного кода, оптимизаций и улучшений.
- Файлы: просмотрены `lib/**/*.php`, `content/**/*.md`, `public/{sitemap.xml,robots.txt,site.webmanifest}`, `docker/**`, `.github/workflows/docker-image.yml`, `docs/AGENTS.md`, `grace/**/*.xml`; изменён только этот журнал.
- Решение: правки кода не вносились; вывод оформлен как список findings с приоритетами для последующего исправления.

## Фаза 1.19.1 — Исправления по аудиту

### Рантайм и SEO
- Что: `ContentRepository::itemUrl()` переведён на canonical URL по фактическому пути файла в `content/`; добавлен `urlPathFromContentPath`; `HtmlComponents::contentCard()` и `PageRenderer::contentSectionIndex()` используют `itemUrl()`. `Router` отдаёт `/404` как HTTP 404 и направляет все не-index `content/guides/**` через `PageRenderer::guideArticle()`. `Seo::breadcrumbListSchema()` получил optional `@id`; `ItemList.numberOfItems` выровнен с фактическим `itemListElement`; удалён неиспользуемый `SiteRoutes` и `Seo::lootbarServiceSchema()`.
- Почему: аудит выявил двойные URL-сегменты, коллизии `_index.md`, мёртвый guideArticle, `/404` с 200 OK и битые JSON-LD ссылки.
- Файлы: `lib/ContentRepository.php`, `lib/Router.php`, `lib/PageRenderer.php`, `lib/Seo.php`, `lib/HtmlComponents.php`, `lib/bootstrap.php`, удалён `lib/SiteRoutes.php`.
- Решение: не переписывать массово frontmatter; canonical URL теперь следует структуре `content/**`, что совпадает с sitemap и файловой моделью.

### Frontmatter и ссылки
- Что: `Frontmatter` теперь понимает стандартные YAML-списки, booleans, inline arrays и `sources.web`; исправлены битые ссылки `/characters/childe`, `/world/lore/khaenriah` и несуществующие региональные лор-ссылки; `content/README.md` обновлён под текущий runtime.
- Почему: связанные материалы/источники терялись из-за парсера, а часть видимых ссылок вела на 404.
- Файлы: `lib/Frontmatter.php`, `content/characters/**`, `content/world/npc/*.md`, `content/world/regions/_index.md`, `content/community/best-guides.md`, `content/news/announcements/version-roadmap.md`, `content/README.md`.

### Docker/nginx/docs
- Что: `docker/Dockerfile` копирует `VERSION` и `bin/`; nginx больше не подключает отсутствующий `genshintop-redirects.conf*`, дублирует security-заголовки в location с собственными `add_header` и убирает `immutable` с общего regex статики; добавлен `docs/SEO-CHECKLIST.md`.
- Почему: в образе не было версии футера и IndexNow CLI; на статике могли теряться security headers; документация ссылалась на отсутствующий SEO-чеклист.
- Файлы: `VERSION`, `docker/Dockerfile`, `docker/nginx-default.conf`, `docker/env.example`, `docs/SEO-CHECKLIST.md`, `docs/AGENTS.md`, `grace/**/*.xml`.
- Решение: версия поднята `1.19.0 → 1.19.1` как PATCH; GRACE и карта агентов синхронизированы.

## Фаза 1.19.2 — аудит кодовой базы по плану

### Объём проверки
- Что: план «проверка всего кода на ошибки и нестыковки»: синтаксис PHP (локально недоступен `php.exe` в PATH — линтер IDE по `lib/` без замечаний), обход ключевых модулей (Router, PageRenderer, ContentRepository, Frontmatter, IndexNow), уникальность канонических URL live-контента, сверка `public/sitemap.xml`, статический SEO (`robots.txt`, `site.webmanifest`, nginx), GRACE/`docs/AGENTS.md`/верификация.
- Почему: запрос пользователя выполнить план аудита полностью.
- Файлы: этот журнал; правки ниже по списку.

### Результаты (без ошибок блокирующего уровня)
- **critical**: не выявлено.
- **major**: исправлена нестыковка **GRACE** — `grace/verification/verification-plan.xml` → `V-M-CONTENT-V2/check-6` утверждало, что рантайм читает `info/` и `content/` не подключён; фактически `lib/ContentRepository.php` уже индексирует только `content/`. Чек переформулирован под текущее поведение.
- **minor**:
  - **IndexNow CLI** (`bin/indexnow-ping.php`): прежний разделитель PCRE `'#' . $match . '#'` ломал валидные шаблоны с символом `#` в строке URL; заменено на разделитель **U+0007 (BEL)** с запретом BEL в паттерне, при ошибке разбора PCRE — STDERR и exit 4 (вместо тихого сбоя `@preg_match`).
  - **`php -l`**: на агентской Windows PHP CLI не установлен в PATH — рекомендация держать PHP в PATH или добавить lint-джоб в CI.
  - **Frontmatter**: ключи frontmatter топ-уровня только `[A-Za-z0-9_]`; при появлении `kebab-case` ключей в YAML они молча не попадают в `meta` — риск на будущее, не наблюдался в текущем выборке.
  - **Старые блоки HISTORY** про «не переключали ContentRepository на content/» в ранних фазах исторически устарели (нагрузочно см. актуальный `docs/AGENTS.md`).

### Автоматизированная сверка контента и sitemap (PowerShell)
- Живых канонических URL из `content/` (по тем же фильтрам, что и PHP: исключены `_templates/`, `/_by-*`, `README.md`, `STYLE.md`, статус только `live`): **437** уникальных путей без дубликатов `urlPath`.
- Коллизий slug персонажей со служебными сегментами хабов (`/characters/*`, тематические `/guides/*`) не найдено.
- `public/sitemap.xml`: путей **471** после нормализации `https://genshintop.ru` → путь; **0** живых канонических URL отсутствуют в sitemap; **0** «висящих» `<loc>` вне объединённого набора «live content + whitelist статических и хаб-маршрутов» (whitelist: `/about`, контакты, политики, `/guides`, 13 thematic hubs, три LootBar URL, фильтры персонажей).

### Sync
- Что: `VERSION` 1.19.1 → **1.19.2** (PATCH — надёжность CLI + синхронизация верификации). Обновлены `docs/AGENTS.md` (заметка про `--match` и BEL), `grace/knowledge-graph/knowledge-graph.xml` (`Project` VERSION и `fn-indexnow`), `grace/verification/verification-plan.xml` (`check-6` M-CONTENT-V2, `check-25` V-M-PHP-SITE).

### Повторная сверка (2026-05-14)
- Что: повторный прогон того же контура: диагностика IDE по `lib/` и `bin/indexnow-ping.php` (замечаний нет); `VERSION` ↔ атрибут `Project` в `grace/knowledge-graph/knowledge-graph.xml` — **1.19.2**; PowerShell-сверка live-URL из `content/` с `public/sitemap.xml` и whitelist статических/хаб-маршрутов; фактический список `lib/*.php` на диске (22 файла — без «фантомных» `SiteRoutes`/`build-sitemap`/`regions_data` из индекса IDE); выборочно `rg` по `content/**/*.md` на топ-уровневые `kebab-case` ключи в первом блоке frontmatter — не найдено.
- Почему: запрос пользователя «проверь ещё раз».
- Результат: **437** канонических live-URL; **0** дубликатов `urlPath`; **0** отсутствующих в sitemap; **0** sitemap-«сирот» вне whitelist+content. На машине агента **PHP по-прежнему не в PATH** — `php -l` локально не выполнялся.

## Фаза 1.19.3 — внутреннее SEO (план)

### Редакционный playbook
- Что: раздел **«SEO и поиск»** в [`content/STYLE.md`](content/STYLE.md): summary, даты, интент, внутренние ссылки, alt у изображений, напоминание про `status: live`.
- Почему: фиксация редакционного процесса из плана «Внутреннее SEO сайта».
- Файлы: `content/STYLE.md`, перекрёстная ссылка на `docs/SEO-CHECKLIST.md`.

### Динамический sitemap с lastmod
- Что: удалён статический [`public/sitemap.xml`](public/sitemap.xml); добавлены [`lib/SitemapBuild.php`](lib/SitemapBuild.php), ранний обработчик [`Router::sendSitemap`](lib/Router.php), в [`docker/nginx-default.conf`](docker/nginx-default.conf) — `location = /sitemap.xml` → FastCGI. [`bin/generate-sitemap.php`](bin/generate-sitemap.php) выводит тот же XML в CLI. [`bin/indexnow-ping.php`](bin/indexnow-ping.php) берёт URL из `SitemapBuild::absoluteUrls` (simplexml по файлу не используется).
- Почему: `<lastmod>` и отказ от расхождения git-файла с контентом.
- Решение: приоритеты/changefreq как раньше по типам путей; корневые разделы одним сегментом — weekly 0.9.

### OG 1200×630 для топ-страниц
- Что: [`lib/og-manifest.json`](lib/og-manifest.json) с записями; [`OgManifest::imageForCanonicalPath`](lib/OgManifest.php); плейсхолдеры PNG 1200×630 в [`public/og/`](public/og/) (PowerShell + System.Drawing). В [`PageRenderer`](lib/PageRenderer.php) заданы `ogImage`/`ogAlt` для главной, `/guides`, `/characters`, трёх страниц LootBar; гайды и персонажи из manifest по-прежнему через `imageForEntry`.
- Почему: плановое расширение OG для соцсниппетов.

### Документация и GRACE
- Что: обновлены [`docs/AGENTS.md`](docs/AGENTS.md), [`docs/SEO-CHECKLIST.md`](docs/SEO-CHECKLIST.md), [`README.md`](README.md), [`docker/README.md`](docker/README.md); `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`.
- Синхронизация: `VERSION` **1.19.2 → 1.19.3** (PATCH).

## Фаза 1.20.0 — Волна контента Luna 6.x (план «недостающие статьи»)

### Патчи и новости
- Что: добавлены страницы **`content/news/patches/6.1.md`–`6.6.md`**; полностью переписан **`6.0.md`** (убрана заглушка TBA, данные по *A Dance of Snowy Tides and Hoarfrost Groves*); обновлены индексы **`content/news/patches/_index.md`**, связь **`5.7.md` → `6.0`**.
- Почему: закрыть разрыв версий после **5.7** по официально документированной цепочке **Genshin Impact Wiki** (сверка 2026-05-14).

### Персонажи
- Что: **15** новых карточек: **flins, aino, nefer, wonderland-manekin, durin, jahoda, columbina, zibai, illuga, ineffa, varka, linnea, nicole, prune, lohen** — компактный wiki-формат + `sources.web`.
- Почему: дебюты **Luna I–VII** по вики-координатам.

### Оружие и артефакты
- Что: **14** оружий (6.0–6.6) и **6** артефактных сетов (**silken-moons-serenade**, **night-of-the-skys-unveiling**, **aubade-of-morningstar-and-moon**, **a-day-carved-from-rising-winds**, **celestial-gift**, **disenchantment-in-deep-shadow** и др.).
- Почему: параллельная нить плана — расширение каталога по **docs/PLAN.md**.

### Баннеры и ивенты
- Что: **6** файлов в **`news/banners/**`** (Rubedo 6.2, Somnias 6.3, Ya-hoho 6.5, Angel's Reverie 6.6, Epitome 6.0 / 6.6) + **`news/events/luna-vii-launch-events.md`**.
- Почему: привязать баннерные названия к патчам и датам вики.

### Карта прогресса
- Что: обновлены **`content/README.md`**, **`content/characters/_index.md`** (актуальные счётчики файлов).
- Решение: статического `public/sitemap.xml` в репозитории **нет** — после 1.19.3 карта генерируется **динамически** (`lib/SitemapBuild.php`); новые URL попадают в sitemap автоматически.

### Sync
- Что: `VERSION` **1.19.3 → 1.20.0** (MINOR — крупное расширение корпуса). `grace/knowledge-graph/knowledge-graph.xml` — `Project VERSION`, аннотация **ax-status** под Luna 6.x. Этот журнал.
- Файлы: `VERSION`, `content/**`, `grace/knowledge-graph/knowledge-graph.xml`, `docs/HISTORY.md`.


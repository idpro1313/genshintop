---
title: Enemy Resistances — таблица сопротивлений
section: guides/advanced
slug: enemy-resistances
status: live
summary: "Каждый враг имеет 7 элементальных сопротивлений + Physical. Reduction через Viridescent Venerer (-40%), Lyney (-RES), Zhongli (-20% all)."
gameVersion: "6.x"
updatedAt: 2026-05-13
planTrack: advanced
sources:
  info: [info/guides/enemy-resistances.md]
  web:
    - {url: "https://keqingmains.com/q/enemy-resistance-table/", title: "KQM Enemy RES Table", accessed: "2026-05-13"}
related: [/enemies, /guides/advanced/damage-formula]
---

В Genshin Impact каждый враг имеет **8 элементальных сопротивлений** (Pyro/Hydro/Electro/Cryo/Anemo/Geo/Dendro + Physical). Это критично для billing damage и выбора командой.

## Формула RES

`Final Reduction = (1 - RES) × DMG`

При RES > 75% применяется специальная формула:
- `0% ≤ RES ≤ 75%`: `Multiplier = 1 - RES`
- `RES > 75%`: `Multiplier = 1 / (1 + 4×RES)`

## Базовые RES — типичные значения

### Hilichurls
- All elements: **10% RES**
- Physical: **10% RES**

### Abyss Mages
- **Свой щит** (Pyro Mage = Pyro): **+200% RES** при щите.
- **Опровержение щита** (Hydro для Pyro): **уязвимость +1.5×**.
- После щита: **30% RES** к свой стихии, **10%** к остальным.

### Hypostases
- **Anemo Hypostasis**: 10% к большинству + Anemo **immunity**.
- **Pyro Hypostasis**: 10% Pyro RES + 10% all.
- **Cryo Hypostasis**: 10% Cryo RES + 10% all.
- **Dendro Hypostasis**: 10% Dendro RES + 10% all.

### Specific Bosses

| Босс | Высокая RES | Низкая RES |
|------|-------------|------------|
| **Maguu Kenki** | Anemo (90%), Cryo (90%) | Все остальные (10%) |
| **Primo Geovishap** | Geo (immune) + Цикл-стихия (90%) | Соответствующая anti-element |
| **Childe Phase 3** | Hydro (90%), Electro-Charged buff | Pyro/Cryo |
| **La Signora** | Cryo Phase 1 (40% Cryo, -20% Pyro), Pyro Phase 2 (40% Pyro, -20% Hydro) | Pyro (Phase 1), Hydro (Phase 2) |
| **Apep** | Dendro (90%), Cryo (90%) | Pyro/Hydro/Electro |
| **All-Devouring Narwhal** | Hydro (40%) | Cryo, Anemo |

Полные таблицы — [KQM Enemy RES Table](https://keqingmains.com/q/enemy-resistance-table/).

## Reduction-эффекты

| Источник | Reduction | Длительность |
|----------|-----------|--------------|
| **Viridescent Venerer 4pc** (Кадзуха, Сахароза, Венти) | **-40%** к Swirl-стихии | 10s после Swirl |
| **Zhongli passive** + Pillar | **-20% all RES** | Длительность щита |
| **Sucrose A4** | +20% EM team (не RES, но реакции усиливаются) | 8s после Swirl |
| **Lyney A4** | -20% Pyro RES | 6s |
| **Furina E** | +Hydro DMG % to whole team | пока Furina на сцене |
| **Faruzan A4** | -30% Anemo RES + +Anemo DMG team | 12s |
| **Nilou Bountiful Cores** | Bloom DMG ↑ (если Hydro/Dendro only) | 30s |
| **Yelan A4** | +1% DMG / 1s on field, max +50% | 30s |

## Element-Pairing для billing damage

| Враг | Best stratagem |
|------|----------------|
| **Pyro Abyss Mage** | Hydro burst (Тарталья E) → Vape spam |
| **Cryo Abyss Mage** | Pyro burst (Дилюк, Беннетт) → Melt |
| **Electro Hypostasis** | Cryo (Кэйа, Гань Юй) → Superconduct → Physical Eola |
| **Maguu Kenki** | Pyro/Hydro (Сан Лин Vape, Аяка/Гань Юй freeze не работает) |
| **Primo Geovishap** | Anti-cycle element (если Pyro-цикл — Hydro spam) |
| **Apep** | Pyro/Hydro vape spam (Ху Тао, Ёимия) |

## Physical RES

| Враг | Phys RES |
|------|----------|
| **Slimes** (most) | -50% Phys (sehr низкая) |
| **Hilichurl Berserker** | 10% |
| **Abyss Mage** | 10% (но щит делает immune) |
| **Maguu Kenki** | 10% |
| **Cicin Mage Fatui** | 10% |
| **Ruin Guard back-weak-spot** | -100% Phys (1.5× damage) |
| **Stonehide Lawachurl шипы** | 70% Geo + 50% Phys |

## Tip: проверяй ПЕРЕД Spiral Abyss

Перед каждым новым floor Spiral Abyss / Imaginarium Theatre:

1. Открой [`/enemies`](/enemies) или KQM table → найди врагов.
2. Подбери team под их weakness.
3. Если все враги Pyro — возьми Hydro/Cryo + Vape/Freeze.
4. Если миксованные — Aggravate / Quicken универсальны (особенно Алхайтам/Кэцин).

## Связанные материалы

- [Enemies каталог](/enemies)
- [Damage Formula](/guides/advanced/damage-formula)
- [Build Theory](/guides/advanced/build-theory)
- [Tier Lists](/guides/advanced/tier-lists)

## Источники

- [KQM Enemy RES Table](https://keqingmains.com/q/enemy-resistance-table/)

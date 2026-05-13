---
title: Elemental Mastery (EM) — теория и применение
section: guides/advanced
slug: elemental-mastery
status: live
summary: "EM усиливает Reaction Damage по нелинейной формуле; ключ к Vape, Melt, Aggravate, Spread, Hyperbloom, Burgeon, Burning."
gameVersion: "6.x"
updatedAt: 2026-05-13
planTrack: advanced
sources:
  info: [info/guides/elemental-mastery.md]
  web:
    - {url: "https://keqingmains.com/misc/damage/em/", title: "KQM EM scaling", accessed: "2026-05-13"}
    - {url: "https://genshin-impact.fandom.com/wiki/Elemental_Mastery", title: "EM — Wiki", accessed: "2026-05-13"}
related: [/guides/basics/elemental-reactions, /guides/advanced/damage-formula, /guides/advanced/resonance-and-auras]
---

Elemental Mastery (EM) — стат, усиливающий **Reaction Damage**: Vaporize, Melt, Aggravate, Spread, Hyperbloom, Burgeon, Burning, Swirl, Overloaded, Electro-Charged. Не влияет на Crit или базовый ATK damage.

## Формула EM-усиления

Базовая формула:

`Reaction Bonus = 1 + (16 × EM) / (EM + 2000) + Reaction DMG%`

| EM | Reaction Bonus (базовый) |
|----|--------------------------|
| 100 | +0.76× (1.76×) |
| 200 | +1.45× (2.45×) |
| 400 | +2.67× (3.67×) |
| 600 | +3.69× (4.69×) |
| **800 (типичный max)** | **+4.57×** (5.57×) |
| 1000 | +5.33× |
| 1200 | +6× |

После 800 EM marginal returns резко падают. Поэтому **800-900 EM — золотой стандарт**.

## Какие реакции масштабируются от EM

### Amplifying (Multiplier)

| Реакция | База | EM-формула | Применение |
|---------|------|------------|------------|
| **Vaporize** (Hydro × Pyro) | 1.5× / 2× | × (1 + EM-bonus) | Ху Тао, Ёимия, Дилюк vapes |
| **Melt** (Cryo × Pyro) | 1.5× / 2× | × (1 + EM-bonus) | Гань Юй+Сахароза, Аяка melt |

### Catalyze (Quicken)

| Реакция | База | EM-формула | Применение |
|---------|------|------------|------------|
| **Aggravate** (Quicken+Electro) | NA × 1.15 + flat (EM) | flat = `1.5 × char_lvl × EM-bonus` | Кэцин, Райдэн, Алхайтам, Сетос |
| **Spread** (Quicken+Dendro) | NA × 1.25 + flat (EM) | flat = `1.5 × char_lvl × EM-bonus` | Алхайтам, Тигнари, Кавэ, Эмили |

### Transformative

| Реакция | Base DMG | EM-формула |
|---------|----------|------------|
| **Hyperbloom** (Dendro Core × Electro) | 3 × char_lvl_scaling | × (1 + 16×EM/(EM+2000)) |
| **Burgeon** (Dendro Core × Pyro) | 3 × char_lvl_scaling | same |
| **Burning** (Dendro × Pyro DoT) | 0.25× | same |
| **Overloaded** (Pyro × Electro) | 2× scaling | same |
| **Electro-Charged** (Hydro × Electro) | 2× scaling | same |
| **Swirl** (Anemo × X) | 0.6× scaling | same |

## Целевые EM по героям

| Роль | Цель EM | Источник |
|------|---------|----------|
| **Sub-DPS Vape/Melt** (Сян Лин, Бэннетт-нет) | 200-300 | EM-substats + Sucrose buff |
| **Vape/Melt main DPS** (Ху Тао R5 Homa) | 100-200 (приоритет — ATK/HP/CR/CD) | мало EM |
| **Quicken main** (Алхайтам, Кэцин) | 200-400 | артефакты, EM-substats |
| **Hyperbloom-driver** (Куки Шинобу, Сян, Райдэн в HB-team) | **800-1000** | full EM build |
| **Bloom Amber catalyst** (Найхида) | **600-800** | EM-circlet, EM Sands |
| **Swirl** (Кадзуха, Сахароза, Венти) | **800-1000** | full EM |

## Артефакты с EM

### Mainstat
- **Sands of Eon**: EM (флоу-реакции).
- **Goblet**: EM (реже; обычно Elemental DMG%).
- **Circlet**: EM (Hyperbloom, Bloom).

### Substat
- **EM substat:** до **+22.5 EM** на одну линию (без rolls). С 5 rolls = до **123 EM** на одной линии.

### Сеты
- **Wanderer's Troupe (4pc)**: +80 EM + +35% Charged Attack DMG (Гань Юй, Венти, лучники).
- **Gilded Dreams (4pc)**: до +200 EM (Sumeru/Dendro теамс).
- **Flower of Paradise Lost (4pc)**: +100% Bloom DMG (Найхида/Куки).
- **Instructor (4pc)**: +120 EM team-wide (старый сет; обычно для Сахарозы/Беннетта).
- **Deepwood Memories (4pc)**: -30% Dendro RES enemies (для Dendro-team).
- **Viridescent Venerer (4pc)**: -40% Swirl-element RES enemies (Кадзуха-team).

### EM-оружие
- **Freedom-Sworn** (Sword 5★): +198 EM.
- **Iron Sting** (Sword 4★): +165 EM.
- **Dragon's Bane** (Polearm 4★): +221 EM (R5).
- **Sacrificial Fragments** (Catalyst 4★): +221 EM.

## Стратегия EM-team-builder

1. **Driver** (тот, кто триггер реакцию): full EM build.
2. **Aura applier** (наносящий первую стихию): без EM, фокус на Resin/ER.
3. **Buffer** (Сахароза, Беннетт): TTOP / Instructor / VV для team-EM share.

## Связанные материалы

- [Elemental Reactions (basics)](/guides/basics/elemental-reactions)
- [Damage Formula](/guides/advanced/damage-formula)
- [Resonance & Auras](/guides/advanced/resonance-and-auras)

## Источники

- [KQM EM scaling](https://keqingmains.com/misc/damage/em/)
- [Elemental Mastery — Genshin Wiki](https://genshin-impact.fandom.com/wiki/Elemental_Mastery)

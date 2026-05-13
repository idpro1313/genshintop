---
title: Формула урона — что перемножается, а что складывается
section: guides/advanced
slug: damage-formula
status: live
summary: "Структура урона в Genshin Impact: базовая, бонус стихии, крит, реакция, резистенс. Что складывается и что умножается."
gameVersion: "6.x"
updatedAt: 2026-05-13
planTrack: advanced
topic: damage
sources:
  info:
    - info/guides/raschet-urona-mnozhiteli-i-kalkulyatory.md
    - info/guides/fizicheskiy-i-elementalnyy-uron.md
    - info/guides/bonus-stihii-i-soprotivleniya-vragov.md
  web:
    - {url: "https://genshin-impact.fandom.com/wiki/Damage", title: "Damage — Genshin Impact Wiki", accessed: "2026-05-13"}
    - {url: "https://library.keqingmains.com/", title: "KQM Damage Formula", accessed: "2026-05-13"}
related:
  - /guides/advanced/build-theory
  - /guides/advanced/crit-stats
  - /guides/advanced/enemy-resistances
---

Урон в Genshin Impact считается по «цепочке множителей». Зная её, легко понять, какой бафф даёт прирост, а какой «упирается в стену».

## Цепочка

```
DMG =
   (base_attack * talent_multiplier + flat_attack)
   * (1 + total_dmg_bonus)
   * crit_multiplier
   * reaction_multiplier
   * defense_factor
   * resistance_factor
```

Здесь:

- **base_attack** = (атака оружия + атака героя) * (1 + sum %атаки) + flat-атака.
- **talent_multiplier** — таб­личный множитель таланта (например, Q Ху Тао на 13-м уровне = ~6.66×).
- **total_dmg_bonus** — сумма % бонусов: бонус стихии (артефакты, оружие), эффект сета 4★, бафф команды.
- **crit_multiplier** = 1 + crit_dmg, если крит сработал; иначе 1.
- **reaction_multiplier** — для усиливающих реакций (×2.0 пар, ×1.5 расплавление, +дельта от EM).
- **defense_factor** = (level_attacker + 100) / (level_attacker + level_defender + 100 - реакции).
- **resistance_factor** — функция резистенса цели (положит. — снижение, отрицат. — усиление).

## Что **складывается**

- Все % атаки → одна сумма перед умножением.
- Все % бонуса стихии → один множитель.

## Что **умножается** отдельно

- Крит — отдельно от бонуса.
- Реакция — отдельно.
- Резистенс — отдельно.

## Почему пар + Беннетт сильнее, чем «много атаки»

Беннетт даёт +1000 атаки flat в очень большой множитель базы. Пар даёт ×2.0 поверх. Эти два — разные «слои» формулы; они не «съедают» друг друга, а перемножаются.

## Кальсуляторы

- genshin-optimizer (открытый код).
- Akasha System.
- KQM-калькуляторы по командам.

## Связанные материалы

- [Билдостроение](/guides/advanced/build-theory)
- [Крит-шанс и крит-урон](/guides/advanced/crit-stats)
- [Бонус стихии и резистенсы](/guides/advanced/enemy-resistances)

## Источники

- Genshin Impact Wiki — Damage.
- KQM Theorycrafting Library.
- Внутренние материалы редакции.

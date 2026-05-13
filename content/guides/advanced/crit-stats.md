---
title: Crit Rate / Crit DMG — соотношение 1:2
section: guides/advanced
slug: crit-stats
status: live
summary: "Crit Rate (CR) и Crit DMG (CD) — два главных damage-стата в Genshin. Золотое соотношение CR:CD = 1:2; типичные цели и антипаттерны."
gameVersion: "6.x"
updatedAt: 2026-05-13
planTrack: advanced
sources:
  info: [info/guides/krit-shans-i-krit-uron-baza.md]
  web:
    - {url: "https://keqingmains.com/misc/damage/", title: "KQM Damage Formula", accessed: "2026-05-13"}
    - {url: "https://genshin-impact.fandom.com/wiki/Damage_Formula", title: "Damage Formula — Wiki", accessed: "2026-05-13"}
related: [/guides/advanced/build-theory, /guides/advanced/damage-formula]
---

Crit Rate (CR) и Crit DMG (CD) — два главных damage-стата в Genshin Impact. Контролируют **частоту** и **силу** критических ударов. Их баланс даёт максимальный effective DMG.

## Формула среднего damage

Average DMG multiplier = `1 + CR × CD`

При фиксированном «бюджете» (сумме CR + CD во всех слотах) производная даёт оптимум:

`CD = 2 × CR`

То есть **на каждый 1% Crit Rate** оптимально иметь **2% Crit DMG**.

## Базовые значения по героям

| Герой | Базовый CR (90 lvl) | Базовый CD (90 lvl) |
|-------|---------------------|---------------------|
| **Большинство 5★** | 5% | 50% |
| **CR-Ascension stat** (Кэйа, Гань Юй, Е Лань, Сяо, Аяка, Ху Тао, Кадзуха-нет) | 5% + 24.2% = 29.2% | 50% |
| **CD-Ascension stat** (Беннетт, Сян Лин, Аято, Тарталья, Дилюк, Чжон Ли, Райдэн, Альбедо) | 5% | 50% + 38.4% = 88.4% |
| **Hu Tao Passive (CR на A4)** | 5% + 12% = 17% | 50% |
| **Yelan Passive** | 5% + (HP-based) | 50% |

## Источники CR/CD

| Источник | CR | CD |
|----------|----|----|
| **Circlet (mainstat)** | до **+31.1%** | до **+62.2%** |
| **Sub-stat per roll** | +2.7-3.9% | +5.4-7.8% |
| **5★ оружие (CR)** | Mistsplitter (+19.2-44.1%), Aqua Simulacra (+44.1%), Polar Star (+33.1%), Verdict (+44.1%) | — |
| **5★ оружие (CD)** | Calamity Queller, Vortex Vanquisher (если ATK%) | Wolf's Gravestone (— ATK%), Skyward Atlas (— ATK%), Staff of Homa (CR — нет, HP%) |
| **Артефактные сеты (2pc/4pc)** | Blizzard Strayer (+15% против frozen) | Wanderer's Troupe (нет — EM), Shimenawa (нет — ATK%) |

## Целевые значения

### Универсальная цель (без auto-crit)

- **CR:** 60-70%
- **CD:** 140-180%
- **Соотношение:** ~1:2.4 (чуть в сторону CD из-за артефактных линий)

### С Crit-buffer (Беннетт C6, Шэнь Хэ, Альбедо C6)

Когда команда даёт +CR:
- **CR:** 50-60% (лиш CR — over-investment)
- **CD:** 180-220%

### Auto-crit билды (Cyno Talisman of Brilliance, Ху Тао от Vermillion 4pc)

Когда персонаж **гарантированно** крит:
- **CR:** игнорировать (10-30%)
- **CD:** **maximize** (250-300%)

## Антипаттерны

| Билд | Что не так | Решение |
|------|------------|---------|
| **80% CR / 100% CD** | Over-invest в CR | Reroll Circlet на CD; брать CD-substat артефакты |
| **30% CR / 280% CD** | Under-invest в CR | Циркулет на CR; substat-rolls в CR |
| **CR + CD только от substats без circlet** | Очень слабо | Поменяй circlet на CR/CD mainstat (никогда не ATK% circlet) |
| **CR/CD на саппорте без damage** (Беннетт, Чжон Ли, Куки, Сахароза) | Бессмысленно | Investиров в ER, EM, HP%, DEF% |

## Когда CR/CD НЕ нужно

- **Saббaт-герои:** Беннетт (Noblesse), Кадзуха (VV, EM), Сахароза (TTOP, EM), Куки (Noblesse, HP%), Бай Чжу (Tenacity, HP%).
- **Reaction-driver:** Сяо Лин (EM Quicken), Алхайтам (EM Spread).
- **Healer-only:** Барбара, Кокоми (если без damage), Сигевинн (HP%).

## Расчёт effective stats

Используй [Aspirine Theory] (KQM):
1. Каждый CR sub-roll = 6.5 substat value (если бюджет = 50 sub-rolls).
2. Каждый CD sub-roll = 6.5 substat value (×2 weight).
3. **EHP** = average damage / non-crit damage.

## Связанные материалы

- [Build Theory](/guides/advanced/build-theory)
- [Damage Formula](/guides/advanced/damage-formula)
- [Tier Lists](/guides/advanced/tier-lists)

## Источники

- [KQM Damage Formula](https://keqingmains.com/misc/damage/)
- [Damage Formula — Genshin Wiki](https://genshin-impact.fandom.com/wiki/Damage_Formula)

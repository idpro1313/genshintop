---
title: External Tools — внешние инструменты для билдов
section: guides/advanced
slug: external-tools
status: live
summary: "Damage calculators, optimizers, ресурс-планировщики: GO (Genshin Optimizer), Aspirine, Genshin.gg, KQM Theorycrafting, HoYoLAB BBS."
gameVersion: "6.x"
updatedAt: 2026-05-13
planTrack: advanced
sources:
  info: [info/guides/external-tools.md]
  web:
    - {url: "https://frzyc.github.io/genshin-optimizer/", title: "Genshin Optimizer", accessed: "2026-05-13"}
    - {url: "https://keqingmains.com/", title: "KQM Theorycrafting", accessed: "2026-05-13"}
    - {url: "https://genshin.gg/", title: "Genshin.gg Builds", accessed: "2026-05-13"}
related: [/tools/external-tools, /guides/advanced/build-theory]
---

В Genshin Impact активная коммьюнити-разработка дополнительных инструментов; они помогают ускорить билд, расчёт damage, фарм и планирование.

## Главные инструменты

### Damage / Build Optimizers

| Tool | URL | Что делает |
|------|-----|------------|
| **Genshin Optimizer** | [frzyc.github.io/genshin-optimizer](https://frzyc.github.io/genshin-optimizer/) | Vue.js билд-оптимизатор: импорт артефактов, поиск best 5+5+5 комбо |
| **Aspirine** (KQM TCL) | [github.com/Albedo-Player](https://github.com/Albedo-Player) | Library / Scoring system артефактов |
| **Akasha System** | [akasha.cv](https://akasha.cv/) | Топ-100 артефакт-билдов на UID; сравнение DPS |
| **Cantarella** | [cantarella.app](https://cantarella.app/) | DPS-калькулятор, builder |

### Базы данных и билд-гайды

| Tool | URL | Что делает |
|------|-----|------------|
| **KQM Theorycrafting** | [keqingmains.com](https://keqingmains.com/) | Оф. KQM-гайды: damage formula, EM, Reaction, RES tables |
| **Genshin.gg** | [genshin.gg](https://genshin.gg/) | Quick-tier-list, билды, оружия, артефакты |
| **Game8** | [game8.co/games/Genshin-Impact](https://game8.co/games/Genshin-Impact) | Полный wiki + tier lists + tier-обновления |
| **Genshin Wiki (Fandom)** | [genshin-impact.fandom.com](https://genshin-impact.fandom.com) | Самая полная EN-вики |
| **HoYoWiki** | [wiki.hoyolab.com](https://wiki.hoyolab.com) | Официальная EN/MULTI-вики HoYoverse |

### Карты и сундуки

| Tool | URL | Что делает |
|------|-----|------------|
| **HoYoLAB Interactive Map** | [webstatic-sea.hoyoverse.com](https://webstatic-sea.hoyoverse.com/app/ys-map-sea/index.html) | Все сундуки, oculi, локальные ингредиенты с фильтрами |
| **Genshin-Impact Map (Mapgenie)** | [mapgenie.io/genshin-impact](https://mapgenie.io/genshin-impact) | Альтернативная карта, иногда быстрее |
| **Genshin Center** | [genshin-center.com](https://genshin-center.com) | Daily Planner, Resin tracker, sequence-планировщик |

### Tracker / Wish History

| Tool | URL | Что делает |
|------|-----|------------|
| **Paimon.moe** | [paimon.moe](https://paimon.moe/) | Wish-history import, tracker, calculator |
| **Genshin Hunter** | [hunter.aspirine.com](https://hunter.aspirine.com/) | Optimizer для пулов, gacha-tracker |
| **Genshin-Wishes** | [genshin-wishes.com](https://genshin-wishes.com/) | Полный wish-history dashboard |

### Spiral Abyss / Theater

| Tool | URL | Что делает |
|------|-----|------------|
| **Genshin Optimizer Theater** | (часть GO) | Расчёт floor 12 |
| **Akasha Abyss Stats** | [akasha.cv](https://akasha.cv/profile/<UID>/abyss) | Топ-1% статистика по версии |

### Сообщества

| Tool | URL | Что делает |
|------|-----|------------|
| **Reddit r/Genshin_Impact** | [reddit.com/r/Genshin_Impact](https://reddit.com/r/Genshin_Impact) | Megathreads патчей, Special Programs, Theorycrafting |
| **KQM Discord** | [discord.gg/keqing](https://discord.gg/keqing) | Главный Theorycraft-сервер |
| **HoYoLAB Forums** | [hoyolab.com](https://www.hoyolab.com) | Официальное сообщество, web-events |

## Как использовать workflow

1. **Ежедневно**: Genshin Center / HoYoLAB → daily check-in.
2. **При роллах**: [Paimon.moe](https://paimon.moe/) → tracker pity.
3. **При новом артефакте**: Genshin Optimizer → import → check best slot.
4. **Перед Spiral Abyss**: KQM gameplan, Akasha team examples.
5. **Special Program**: KQM analysis для tier-list движений.

## Безопасность

Все эти инструменты — **read-only** (используют export wish-history через JSON). HoYoverse **не банит** за их использование. Однако:
- **НЕ** используй tools требующие пароля HoYoverse аккаунта.
- **НЕ** используй macro / auto-clicker (это может бан).
- **НЕ** делись AccessToken (в URL запросов).

## Связанные материалы

- [Tools каталог](/tools/external-tools)
- [Build Theory](/guides/advanced/build-theory)
- [Damage Formula](/guides/advanced/damage-formula)

## Источники

- [Genshin Optimizer](https://frzyc.github.io/genshin-optimizer/)
- [KQM Theorycrafting](https://keqingmains.com/)
- [Genshin.gg Builds](https://genshin.gg/)

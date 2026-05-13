# genshintop — семантическая карта

> Расположение в репозитории: **`docs/AGENTS.md`**.  
> Каноническая карта для ИИ-агентов и разработчиков (правила Cursor: читать при старте после **`docs/HISTORY.md`**).

**GRACE-артефакты:** `grace/requirements/requirements.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `grace/knowledge-graph/knowledge-graph.xml`. Обзор каталога: **`grace/README.md`**.

## Назначение проекта

Публичный **сайт GenshinTop** (домен **genshintop.ru**): **PHP front-controller** (стек как [dandangers](https://github.com/idpro1313/dandangers)), **nginx + PHP-FPM** в Docker, SEO, Яндекс.Метрика, каталоги **персонажей**, **гайдов**, оружия, артефактов, мира, новостей, инструментов, партнёрский раздел **`/lootbar`**, футер с версией из **`VERSION`**. **Живой контент** — каталог **`content/`** (см. **`lib/ContentRepository.php`**). Служебные `content/**/_by-*` не индексируются рантаймом и sitemap.

Рантайм и репозиторий **без Node/npm**: только PHP, Markdown и статика. **`public/sitemap.xml`** — ручной статический файл в git; при добавлении/удалении публичных страниц обновляйте его вместе с навигацией. Маршрут **`/rss.xml` не используется** (ответ 404).

### Команды и операции

| Команда / действие | Назначение |
|-------------------|------------|
| Docker | **`docker/README.md`** — образ GHCR, **`docker/docker-compose.yml`**, Traefik |
| GitHub Actions | `.github/workflows/docker-image.yml` — `docker build -f docker/Dockerfile .` из корня репозитория |
| Обновление на сервере | **`bash ./update-from-github.sh`** из корня репозитория — pull образа, `up -d` |



### Модули (GRACE)

- **M-PHP-SITE** — **`public/index.php`** (тонкая точка входа nginx), **`lib/`** (весь PHP приложения без подпапок: **`bootstrap.php`**, **`config.php`**, **`web_dispatch.php`**, классы, **`layout.php`**, **`header.php`**, **`footer.php`**, **`lootbar_banner.php`**, **`og-manifest.json`**), **`public/css/site.css`** (тёмная тема и компоненты в духе [idpro1313/dandangers](https://github.com/idpro1313/dandangers) `modern-styles.css`: teal/violet, карточный prose; опционально light через `prefers-color-scheme`), **`public/sitemap.xml`**, **`public/og/`**, фавиконки **`public/favicon.{ico,svg}`**, **`public/favicon-{16x16,32x32}.png`**, **`public/apple-touch-icon.png`**, **`public/icon-{192,512}.png`**, **`public/site.webmanifest`**, Docker/nginx. JSON-LD и мета через **`lib/Seo.php`**, OG через **`OgManifest`**. Партнёрские ссылки — **`lib/Partners.php`**, LootBar — **`lib/LootbarConfig.php`**. Раздел **`/lootbar`** состоит из трёх страниц: хаб **`/lootbar`** (с якорными секциями `#promokod`, `#kristally`, `#welkin`, `#bezopasnost`), лендинг **`/lootbar/skidki-i-kupony`**, инструкция **`/lootbar/kak-popolnit-genshin-impact`**; плашка-баннер **`lib/lootbar_banner.php`** — единственная кликабельная ссылка на лендинг скидок. Каталог гайдов: поиск `?q=` и фильтры в **`lib/PageRenderer.php`**; каталог персонажей: фильтры по стихии/оружию/редкости; generic content-роутер обслуживает остальные `content/`-разделы. `ContentRepository::markdownToHtml` после Parsedown пропускает HTML через `sanitizeContentLinks`: для относительных href срезается `.md`, ссылки на служебные пути (`_templates/`, `_by-`, `STYLE.md`, `README.md`, `info/`) разворачиваются в plain text без href, а лейблы вида `<code>foo.md</code>` чистятся от `.md` и обёртки `<code>`.
- **M-CONTENT-GUIDE-REFACTOR** — контент **`info/guides`**, **`info/characters`**;  редиректы **`docker/genshintop-redirects.conf`**.
- **M-CONTENT-V2** — параллельный каталог **`content/`** для новой редакции сайта по [`docs/PLAN.md`](PLAN.md). Зеркалит все разделы PLAN: characters/, weapons/, artifacts/, materials/, enemies/, guides/{basics,advanced,walkthroughs}/, tools/, world/{regions,lore,factions,npc}/, news/{events,announcements,banners,patches}/, community/. Стандарты — **`content/STYLE.md`**, шаблоны frontmatter — **`content/_templates/*.md`**. На момент 1.17.0 сайт переключён на чтение из `content/`. Старый контент перенесён в `archive/info/` и отключён от публичного роутера. Начиная с 1.16.0 все рабочие разделы `content/` имеют `status: live` во frontmatter.

### Гайды: таксономия и frontmatter

В **`content/guides/**/*.md`**: как минимум **`title`**, **`section`**, **`slug`**, **`status`**, **`summary`**, **`planTrack`** (`basics` | `advanced` | `walkthroughs`). Эвристики на сайте — **`lib/GuideTaxonomy.php`**. Столпы PLAN обслуживаются content-индексами **`/guides/basics`**, **`/guides/advanced`**, **`/guides/walkthroughs`**; тематические хабы **`/guides/*`** — **`lib/guide_hub_definitions.php`** / **`lib/GuideHub.php`**. Партнёрские URL — **`lib/Partners.php`**.

### Деплой

Инструкция для Traefik + Docker: **`docker/README.md`** (готовый образ GHCR; локально — **`docker build -f docker/Dockerfile .`** из корня репозитория).

# GRACE Framework - Project Engineering Protocol

## Keywords
genshin-impact, php, nginx, seo, genshintop.ru, guides, characters, lootbar, docker

## Annotation
Репозиторий сайта на PHP + Markdown, без Node/npm. При изменении маршрутов, nginx или деплоя обновляйте `grace/**`, этот файл и `docs/HISTORY.md`.

## Core Principles

### 1. Never Write Code Without a Contract
Before generating or editing any module, create or update its MODULE_CONTRACT with PURPOSE, SCOPE, INPUTS, and OUTPUTS. The contract is the source of truth. Code implements the contract, not the other way around.

### 2. Semantic Markup Is Load-Bearing Structure
Markers like `// START_BLOCK_<NAME>` and `// END_BLOCK_<NAME>` are navigation anchors, not documentation. They must be:
- uniquely named
- paired
- proportionally sized so one block fits inside an LLM working window

### 3. Knowledge Graph Is Always Current
`grace/knowledge-graph/knowledge-graph.xml` is the project map. When you add a module, move a module, rename exports, or add dependencies, update the graph so future agents can navigate deterministically.

### 4. Verification Is a First-Class Artifact
Testing, traces, and log anchors are designed before large execution waves. `grace/verification/verification-plan.xml` is part of the architecture, not an afterthought. Logs are evidence. Tests are executable contracts.

### 5. Top-Down Synthesis
Code generation follows:
`RequirementsAnalysis -> TechnologyStack -> DevelopmentPlan -> VerificationPlan -> Code + Tests`

Never jump straight to code when requirements, architecture, or verification intent are still unclear.

### 6. Governed Autonomy
Agents have freedom in HOW to implement, but not in WHAT to build. Contracts, plans, graph references, and verification requirements define the allowed space.

## Semantic Markup Reference

### Module Level
```
// FILE: path/to/file.ext
// VERSION: 1.0.0
// START_MODULE_CONTRACT
//   PURPOSE: [What this module does - one sentence]
//   SCOPE: [What operations are included]
//   DEPENDS: [List of module dependencies]
//   LINKS: [Knowledge graph references]
// END_MODULE_CONTRACT
//
// START_MODULE_MAP
//   exportedSymbol - one-line description
// END_MODULE_MAP
```

### Function or Component Level
```
// START_CONTRACT: functionName
//   PURPOSE: [What it does]
//   INPUTS: { paramName: Type - description }
//   OUTPUTS: { ReturnType - description }
//   SIDE_EFFECTS: [External state changes or "none"]
//   LINKS: [Related modules/functions]
// END_CONTRACT: functionName
```

### Code Block Level
```
// START_BLOCK_VALIDATE_INPUT
// ... code ...
// END_BLOCK_VALIDATE_INPUT
```

### Change Tracking
```
// START_CHANGE_SUMMARY
//   LAST_CHANGE: [v1.2.0 - What changed and why]
// END_CHANGE_SUMMARY
```

## Logging and Trace Convention

All important logs must point back to semantic blocks:
```
logger.info(`[ModuleName][functionName][BLOCK_NAME] message`, {
  correlationId,
  stableField: value,
});
```

Rules:
- prefer structured fields over prose-heavy log lines
- redact secrets and high-risk payloads
- treat missing log anchors on critical branches as a verification defect
- update tests when log markers change intentionally

## Verification Conventions

`grace/verification/verification-plan.xml` is the project-wide verification contract. Keep it current when module scope, test files, commands, critical log markers, or gate expectations change.

Testing rules:
- deterministic assertions first
- trace or log assertions when trajectory matters
- test files may also carry MODULE_CONTRACT, MODULE_MAP, semantic blocks, and CHANGE_SUMMARY when they are substantial
- module-local tests should stay close to the module they verify
- wave-level and phase-level checks should be explicit in the verification plan

## File Structure
```
public/index.php        - Только require lib/web_dispatch.php (nginx → PHP-FPM)
lib/
  bootstrap.php, config.php, web_dispatch.php
  *.php                 - Router, PageRenderer, Seo, контент, хабы, OG
  layout.php, header.php, footer.php, lootbar_banner.php — общая оболочка страниц
  og-manifest.json      - список ключей OG-PNG для OgManifest (ручная правка / внешний генератор)
content/                - Канон Markdown для сайта (читает ContentRepository); _by-* служебные и не индексируются
public/
  css/site.css          - Ванильный CSS (dandangers-like dark + optional light)
  sitemap.xml, robots.txt, favicon, og/** (PNG при необходимости)
docker/
  Dockerfile, docker-compose.yml, nginx-default.conf, supervisord.conf, docker-entrypoint.sh
  env.example (шаблон docker/.env), README.md — деплой Traefik/GHCR
update-from-github.sh   - С сервера: git ff + compose pull/up (исполнять из корня репо)
grace/
  requirements/requirements.xml
  technology/technology.xml
  plan/development-plan.xml
  verification/verification-plan.xml
  knowledge-graph/knowledge-graph.xml
docs/
  AGENTS.md             - Этот документ
  HISTORY.md            - Журнал итераций для агентов
  SEO-CHECKLIST.md      - Чек-лист после выката (URL, sitemap)
.cursor/rules/          - Правила Cursor (GRACE, история, git)
.kilo/                  - Навыки Kilo / GRACE
```
При добавлении каталога `tests/` — описать здесь и в `grace/knowledge-graph/knowledge-graph.xml`.

## Documentation Artifacts - Unique Tag Convention

In `grace/**/*.xml`, repeated entities must use their unique ID as the XML tag name instead of a generic tag with an `ID` attribute. This reduces closing-tag ambiguity and gives LLMs stronger anchors.

### Tag naming conventions

| Entity type | Anti-pattern | Correct (unique tags) |
|---|---|---|
| Module | `<Module ID="M-CONFIG">...</Module>` | `<M-CONFIG NAME="Config" TYPE="UTILITY">...</M-CONFIG>` |
| Verification module | `<Verification ID="V-M-AUTH">...</Verification>` | `<V-M-AUTH MODULE="M-AUTH">...</V-M-AUTH>` |
| Phase | `<Phase number="1">...</Phase>` | `<Phase-1 name="Foundation">...</Phase-1>` |
| Flow | `<Flow ID="DF-SEARCH">...</Flow>` | `<DF-SEARCH NAME="...">...</DF-SEARCH>` |
| Use case | `<UseCase ID="UC-001">...</UseCase>` | `<UC-001>...</UC-001>` |
| Step | `<step order="1">...</step>` | `<step-1>...</step-1>` |
| Export | `<export name="config" .../>` | `<export-config .../>` |
| Function | `<function name="search" .../>` | `<fn-search .../>` |
| Type | `<type name="SearchResult" .../>` | `<type-SearchResult .../>` |
| Class | `<class name="Error" .../>` | `<class-Error .../>` |

### What NOT to change
- `CrossLink` tags stay self-closing
- single-use structural wrappers like `<contract>`, `<inputs>`, `<outputs>`, `<annotations>`, `<test-files>`, `<module-checks>`, and `<phase-gates>` stay generic
- code-level markup already uses unique names and stays as-is

## Rules for Modifications

1. Read the MODULE_CONTRACT before editing any file.
2. After editing source or test files, update MODULE_MAP if exports or helper surfaces changed.
3. After adding or removing modules, update `grace/knowledge-graph/knowledge-graph.xml`.
4. After changing test files, commands, critical scenarios, or log markers, update `grace/verification/verification-plan.xml`.
5. After fixing bugs, add a CHANGE_SUMMARY entry and strengthen nearby verification if the old evidence was weak.
6. Never remove semantic markup anchors unless the structure is intentionally replaced with better anchors.

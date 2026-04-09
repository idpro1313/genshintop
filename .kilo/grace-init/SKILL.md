---
name: grace-init
description: "Bootstrap GRACE framework structure for a new project. Use when starting a new project with GRACE methodology — creates grace/ (XML by lifecycle), docs/AGENTS.md from template, docs/HISTORY.md if missing, and grace/README.md."
---

Initialize GRACE framework structure for this project.

## Template Files

All documents MUST be created from template files located in this skill's `assets/` directory.
Read each template file, replace the `$PLACEHOLDER` variables with actual values gathered from the user, and write the result to the target project path.

| Template source | Target in project |
|-----------------|-------------------|
| `assets/AGENTS.md.template` | `docs/AGENTS.md` |
| `assets/grace/README.md` | `grace/README.md` |
| `assets/grace/knowledge-graph/knowledge-graph.xml.template` | `grace/knowledge-graph/knowledge-graph.xml` |
| `assets/grace/requirements/requirements.xml.template` | `grace/requirements/requirements.xml` |
| `assets/grace/technology/technology.xml.template` | `grace/technology/technology.xml` |
| `assets/grace/plan/development-plan.xml.template` | `grace/plan/development-plan.xml` |
| `assets/grace/verification/verification-plan.xml.template` | `grace/verification/verification-plan.xml` |

> **Important:** Never hardcode template content inline. Always read from the `.template` files (and `README.md`) — they are the single source of truth for document structure.

## Upstream GRACE ([osovv/grace-marketplace](https://github.com/osovv/grace-marketplace))

- Canonical **skills** live under `skills/grace/*` in that repo (packaged for Claude / OpenPackage / copy-install).
- Optional **CLI** `@osovv/grace-cli`: `grace lint --path <project-root>` for artifact and markup integrity; `grace module find|show`, `grace file show` for navigation.
- Upstream **grace-init** still targets `docs/*.xml` + root `AGENTS.md`; **this** skill writes **`grace/**`** and **`docs/AGENTS.md`** / **`docs/HISTORY.md`** to match workspace Cursor rules (GRACE XML only under `grace/`).
- Upstream may ship **`operational-packets.xml`** in init assets; it is **not** included here. Add it from marketplace when you need canonical execution packet / delta templates for `$grace-multiagent-execute`.

## Steps

1. **Gather project info from the user.** Ask for:
   - Project name and short annotation
   - Main keywords (for domain activation)
   - Primary language, runtime, and framework (with versions)
   - Key libraries/dependencies (if known)
   - Testing stack (test runner, assertion style, mock/fake approach)
   - Observability stack (logger, structured log fields, redaction constraints)
   - High-level module list (if known)
   - 2–5 critical flows or risky surfaces that must be verifiable early

2. **Create `grace/` subdirectories** if missing: `grace/requirements`, `grace/plan`, `grace/technology`, `grace/verification`, `grace/knowledge-graph`.

3. **Populate GRACE XML from templates:** for each `assets/grace/**/*.xml.template` file:
   - Read the template
   - Replace `$PLACEHOLDER` variables with user-provided values
   - Write the result to the corresponding `grace/...` path in the table above

4. **Copy `assets/grace/README.md`** to project `grace/README.md` (no placeholder substitution unless you extend the template later).

5. **Create or verify `docs/AGENTS.md`:**
   - If `docs/AGENTS.md` does not exist — read `assets/AGENTS.md.template`, fill in `$KEYWORDS` and `$ANNOTATION`, write to `docs/AGENTS.md`
   - If it already exists — warn the user and ask whether to overwrite or keep the existing one

6. **Ensure `docs/HISTORY.md` exists:** if missing, create with a short title (e.g. `# История проекта`) so session history can be logged per project rules.

7. **Print a summary** of all created files and suggest the next step:
   > Run `$grace-plan` to design modules, data flows, and verification references. Then use `$grace-verification` to deepen tests, traces, and log-driven evidence before large execution waves.

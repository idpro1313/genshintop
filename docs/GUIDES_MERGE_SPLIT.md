# Merge, split и смена slug гайдов

Операционный playbook для информационной архитектуры **живых** гайдов [`info/guides/`](../info/guides/) (архив массового корпуса — [`content/guides-archive/`](../content/guides-archive/)).

## Принципы

1. **Канонический URL** = имя файла без `.md` (`banner-example.md` → `/guides/banner-example`).
2. При **слиянии** двух статей оставляется одна каноническая страница; остальные URL закрываются **301 Permanent** в nginx.
3. Запрещено полагаться на meta refresh или «пустые» файлы-заглушки вместо редиректа.
4. Поле **`sourceSlug`** в frontmatter сохраняет исходный идентификатор миграции, если имя файла менялось.

## Merge (объединение)

1. Выбрать **канонический** slug (латиница, читаемость, SEO).
2. Перенести уникальный контент со вторичных статей в каноническую; вычитать дубли.
3. Удалить вторичные файлы из `info/guides/`.
4. Добавить строки в **[`docker/genshintop-redirects.conf`](../docker/genshintop-redirects.conf)**:

```nginx
rewrite ^/guides/old-slug/?$ /guides/canonical-slug permanent;
```

5. Пройтись по **`info/`** и **`content/`** (guides + characters, архивы при необходимости): заменить ссылки `/guides/old-slug` и пункты `relatedGuides` со старым slug.
6. Обновить **`public/sitemap.xml`** не вручную — он собирается при `php lib/build-sitemap.php` в образе после изменений контента.

## Split (разбиение)

1. Ввести новые файлы с новыми slug; в каждом — перекрёстные ссылки «часть 1 / 2».
2. Старый URL: либо редирект на **хаб** темы (`/guides/banners`, `/guides/patches`, …), либо на первую часть серии — по редакционному решению.
3. Зафиксировать редиректы в `docker/genshintop-redirects.conf`.

## Инвентаризация дублей

Запуск:

```powershell
php scripts/guides-refactor-inventory.php
```

Если PHP не в PATH (типично для Windows без установленного интерпретатора):

```powershell
pwsh scripts/guides-refactor-inventory.ps1
```

Отчёт: [`reports/guides-refactor-inventory.json`](../reports/guides-refactor-inventory.json) — блок `mergeCandidatesByTitle` и совпадения `sourceSlug`.

## Пример (реализовано в репозитории)

- Легаси-страница **`/guides/banner`** (монолитный список молитв) объединена по смыслу с хабом баннеров: редирект **`/guides/banner` → `/guides/banners`**, файл `banner.md` удалён.

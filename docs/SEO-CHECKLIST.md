# SEO-чек-лист после выката

Проверять после изменений маршрутов, sitemap, robots, favicon, IndexNow или крупных правок `content/`.

## URL и индексация

- `/sitemap.xml` отдаётся **PHP** (`lib/SitemapBuild.php`): все публичные URL и **`<lastmod>`** согласованы с `ContentRepository::itemMtime` и служебными маршрутами; статического `public/sitemap.xml` нет.
- Превью карты сайта: `php bin/generate-sitemap.php > build/sitemap-preview.xml` (опционально).
- Все URL из sitemap соответствуют фактическим путям `content/**` или явным маршрутам в `Router`.
- `/guides?q=...` отдаёт `noindex, follow`, а canonical остаётся `/guides`.
- `/404` и неизвестные URL отвечают 404, не 200.
- `/rss.xml` остаётся 404, если RSS не возвращён в контракт.

## Мета и структурированные данные

- Канонический URL совпадает с текущим путём без trailing slash.
- `og:image` указывает на существующий PNG/SVG и имеет согласованные размеры.
- `BreadcrumbList` в JSON-LD не содержит битых `@id`-ссылок.
- `ItemList.numberOfItems` совпадает с количеством элементов в `itemListElement`.

## Статика и роботы

- `/favicon.ico`, `/apple-touch-icon.png`, `/site.webmanifest`, `/robots.txt`, `/sitemap.xml` отдаются с корректным MIME.
- Статические файлы получают базовые security-заголовки nginx.
- После деплоя контента IndexNow запускается вручную при необходимости:
  `php bin/indexnow-ping.php --dry-run --limit=5`, затем без `--dry-run` (URL берутся из `SitemapBuild`, файл `public/sitemap.xml` не нужен).

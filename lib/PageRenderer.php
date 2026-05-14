<?php

declare(strict_types=1);

final class PageRenderer
{
    private static function metaIso(mixed $v): ?string
    {
        if (!is_string($v) || $v === '') {
            return null;
        }
        $t = strtotime($v);

        return $t !== false ? gmdate('c', $t) : null;
    }

    /** @return callable(array):bool */
    private static function hubMatcher(string $hub): callable
    {
        return match ($hub) {
            'banners' => fn (array $g) => GuideHub::matchHubBanners($g),
            'codes' => fn (array $g) => GuideHub::matchHubCodes($g),
            'patches' => fn (array $g) => GuideHub::matchHubPatches($g),
            'newbie' => fn (array $g) => GuideHub::matchHubNewbie($g),
            'economy' => fn (array $g) => GuideHub::matchHubEconomy($g),
            'tier-list' => fn (array $g) => GuideHub::matchHubTierList($g),
            'events' => fn (array $g) => GuideHub::matchHubEvents($g),
            'tcg' => fn (array $g) => GuideHub::matchHubTcg($g),
            'domains' => fn (array $g) => GuideHub::matchHubDomains($g),
            'bosses' => fn (array $g) => GuideHub::matchHubBosses($g),
            'quests' => fn (array $g) => GuideHub::matchHubQuests($g),
            default => fn () => false,
        };
    }

    /** @return list<string> */
    private static function sourceLines(mixed $sources): array
    {
        if (is_string($sources) && $sources !== '') {
            return [$sources];
        }
        if (!is_array($sources)) {
            return [];
        }

        $out = [];
        foreach ($sources as $value) {
            foreach (self::sourceLines($value) as $line) {
                $out[] = $line;
            }
        }
        return $out;
    }

    /** @param list<string> $relativePaths */
    private static function filesMtime(array $relativePaths): ?int
    {
        $max = 0;
        foreach ($relativePaths as $rel) {
            $path = SITE_ROOT . '/' . ltrim($rel, '/');
            $t = @filemtime($path);
            if ($t !== false) {
                $max = max($max, $t);
            }
        }
        return $max > 0 ? $max : null;
    }

    /** @param array<string,mixed> $cfg */
    public static function notFound(array $cfg): array
    {
        return [
            'pageTitle' => 'Страница не найдена',
            'pageDescription' => 'Запрошенная страница GenshinTop не существует.',
            'canonicalPath' => '/404',
            'robots' => 'noindex, nofollow',
            'slot' => '<article class="article"><h1>404</h1><p>Такой страницы нет. Перейдите на <a href="/">главную</a> или в <a href="/guides">гайды</a>.</p></article>',
            'jsonLd' => Seo::jsonLdGraph([
                Seo::publisherOrganization($cfg),
                Seo::webSiteNode($cfg),
            ]),
        ];
    }

    /** @return array<string,array<string,mixed>> */
    public static function staticExactPages(array $cfg): array
    {
        $aboutSlot = <<<'HTML'
<article class="article">
<h1>О проекте</h1>
<nav class="inline-nav"><a href="/editorial-policy">Редакционная политика</a><span>·</span><a href="/partnership-disclosure">Партнёрство</a><span>·</span><a href="/content-updates">Обновление контента</a><span>·</span><a href="/contacts">Контакты</a></nav>
<div class="stack">
<p><strong>GenshinTop</strong> — неофициальный сайт для тех, кто играет в <strong>Genshin Impact</strong>: гайды, справка по персонажам и разборы патчей.</p>
<p>Мы не связаны с HoYoverse; материалы основаны на открытых данных игры и редакции сайта.</p>
<p>Раздел <a href="/lootbar">пополнение через LootBar.gg</a> — партнёрский топ-ап с прозрачными условиями.</p>
</div>
<section class="callout"><h2>Топ-ап</h2><p><a href="/lootbar/skidki-i-kupony">Скидки и купоны LootBar</a> · <a href="/lootbar/kak-popolnit-genshin-impact">Как пополнить Genshin Impact</a> · <a href="/lootbar">хаб раздела</a>.</p></section>
</article>
HTML;

        $contactsSlot = <<<'HTML'
<article class="article"><h1>Контакты</h1><p>Сайт носит информационный характер. Вопросы по материалам — см. редакционную политику.</p><p><a href="/editorial-policy">Редакционная политика</a></p></article>
HTML;

        $editorialSlot = <<<'HTML'
<article class="article prose-flow"><h1>Редакционная политика</h1><p>Мы стремимся к точности и актуальности; партнёрские разделы маркируются отдельно.</p><p><a href="/partnership-disclosure">Раскрытие партнёрства</a></p></article>
HTML;

        $partnerSlot = <<<'HTML'
<article class="article prose-flow"><h1>Раскрытие партнёрства</h1><p>Раздел <a href="/lootbar">/lootbar</a> содержит партнёрские ссылки на LootBar.gg (rel=sponsored). Условия и цены определяются на стороне партнёра.</p></article>
HTML;

        $updatesSlot = <<<'HTML'
<article class="article prose-flow"><h1>Обновление контента</h1><p>Материалы обновляются по мере выхода патчей и правок редакции.</p></article>
HTML;

        $fourOhFourSlot = <<<'HTML'
<article class="article"><h1>Страница не найдена</h1><p><a href="/">На главную</a></p></article>
HTML;

        return [
            '/about' => [
                'pageTitle' => 'О проекте GenshinTop',
                'pageDescription' => 'GenshinTop — гайды, персонажи и обновления Genshin Impact для русскоязычных игроков.',
                'canonicalPath' => '/about',
                'slot' => $aboutSlot,
                'jsonLd' => Seo::jsonLdGraph([
                    Seo::publisherOrganization($cfg),
                    Seo::webSiteNode($cfg),
                    [
                        '@type' => 'AboutPage',
                        '@id' => Seo::absoluteUrl($cfg, '/about') . '#webpage',
                        'name' => 'О проекте GenshinTop',
                        'url' => Seo::absoluteUrl($cfg, '/about'),
                        'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
                    ],
                ]),
            ],
            '/contacts' => [
                'pageTitle' => 'Контакты GenshinTop',
                'pageDescription' => 'Контакты и справочная информация GenshinTop.',
                'canonicalPath' => '/contacts',
                'slot' => $contactsSlot,
                'jsonLd' => Seo::jsonLdGraph([
                    Seo::publisherOrganization($cfg),
                    Seo::webSiteNode($cfg),
                    [
                        '@type' => 'ContactPage',
                        '@id' => Seo::absoluteUrl($cfg, '/contacts') . '#webpage',
                        'url' => Seo::absoluteUrl($cfg, '/contacts'),
                    ],
                ]),
            ],
            '/editorial-policy' => [
                'pageTitle' => 'Редакционная политика GenshinTop',
                'pageDescription' => 'Принципы публикации и обновления материалов GenshinTop.',
                'canonicalPath' => '/editorial-policy',
                'slot' => $editorialSlot,
                'jsonLd' => Seo::jsonLdGraph([
                    Seo::publisherOrganization($cfg),
                    Seo::webSiteNode($cfg),
                    Seo::editorialTeamPerson($cfg),
                    [
                        '@type' => 'AboutPage',
                        '@id' => Seo::absoluteUrl($cfg, '/editorial-policy') . '#webpage',
                        'url' => Seo::absoluteUrl($cfg, '/editorial-policy'),
                        'author' => ['@id' => Seo::siteUrl($cfg) . '/#editorial-team'],
                    ],
                ]),
            ],
            '/partnership-disclosure' => [
                'pageTitle' => 'Раскрытие партнёрства GenshinTop',
                'pageDescription' => 'Информация о партнёрских ссылках и разделе LootBar.',
                'canonicalPath' => '/partnership-disclosure',
                'slot' => $partnerSlot,
                'jsonLd' => Seo::jsonLdGraph([
                    Seo::publisherOrganization($cfg),
                    Seo::webSiteNode($cfg),
                    [
                        '@type' => 'WebPage',
                        '@id' => Seo::absoluteUrl($cfg, '/partnership-disclosure') . '#webpage',
                        'url' => Seo::absoluteUrl($cfg, '/partnership-disclosure'),
                    ],
                ]),
            ],
            '/content-updates' => [
                'pageTitle' => 'Обновление контента GenshinTop',
                'pageDescription' => 'Как мы обновляем материалы по Genshin Impact.',
                'canonicalPath' => '/content-updates',
                'slot' => $updatesSlot,
                'jsonLd' => Seo::jsonLdGraph([
                    Seo::publisherOrganization($cfg),
                    Seo::webSiteNode($cfg),
                    [
                        '@type' => 'WebPage',
                        '@id' => Seo::absoluteUrl($cfg, '/content-updates') . '#webpage',
                        'url' => Seo::absoluteUrl($cfg, '/content-updates'),
                    ],
                ]),
            ],
            '/404' => [
                'pageTitle' => 'Страница не найдена',
                'pageDescription' => '404 на GenshinTop.',
                'canonicalPath' => '/404',
                'robots' => 'noindex, nofollow',
                'slot' => $fourOhFourSlot,
                'jsonLd' => Seo::jsonLdGraph([Seo::publisherOrganization($cfg), Seo::webSiteNode($cfg)]),
            ],
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function home(array $cfg): array
    {
        $chars = array_slice(ContentRepository::characters(), 0, 8);
        $guides = array_slice(ContentRepository::guidesSortedByRecent(), 0, 6);
        $cardsC = implode('', array_map(fn ($c) => HtmlComponents::characterCard($c), $chars));
        $cardsG = implode('', array_map(fn ($g) => HtmlComponents::guideCatalogCard($g), $guides));
        $slot = <<<HTML
<section class="home-hero-wrap" aria-labelledby="home-hero-title">
  <div class="hero-card">
    <div class="hero-card-brand"><span class="hero-card-logo" aria-hidden="true">GT</span></div>
    <div class="hero-card-main">
      <p class="hero-kicker">Гайды · билды · баннеры</p>
      <h1 class="hero-title" id="home-hero-title">Твой <span class="hero-title-accent">Teyvat</span> без лишней суеты</h1>
      <p class="hero-lead">Персонажи, свежие гайды и коды — меньше гуглить, больше играть. Пополнение — <a href="/lootbar" class="link-mint" data-reach-goal="lootbar_home_hero_link">раздел LootBar</a>.</p>
      <div class="hero-actions"><a class="btn btn-primary" href="/characters">К персонажам</a><a class="btn btn-secondary" href="/guides">К гайдам</a></div>
    </div>
  </div>
</section>
<p class="site-intro">Добро пожаловать в справочник по Genshin Impact: персонажи, гайды и актуальные материалы на русском — в одном месте.</p>
<section class="section section-wiki">
  <h2 class="section-heading"><span class="section-heading-icon" aria-hidden="true">⚔️</span> Персонажи</h2>
  <p class="section-lead">Карточки стихий и оружия — быстрый переход к странице героя.</p>
  <h3 class="section-subheading"><span class="section-subheading-bar" aria-hidden="true"></span> Популярное на главной</h3>
  <div class="grid-cards grid-cards-wiki">{$cardsC}</div>
  <p class="section-more"><a href="/characters">Все персонажи →</a></p>
</section>
<section class="section section-wiki">
  <h2 class="section-heading"><span class="section-heading-icon" aria-hidden="true">📚</span> Свежие гайды</h2>
  <p class="section-lead">Недавно обновлённые материалы из каталога.</p>
  <h3 class="section-subheading"><span class="section-subheading-bar" aria-hidden="true"></span> Подборка для старта</h3>
  <div class="grid-guides">{$cardsG}</div>
  <p class="section-more"><a href="/guides">Все гайды →</a></p>
</section>
HTML;
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            array_merge(Seo::webSiteNode($cfg), [
                'description' => 'Гайды, персонажи и свежие обновления Genshin Impact по-русски.',
            ]),
        ]);

        return [
            'pageTitle' => 'GenshinTop — гайды и база по Genshin Impact',
            'pageDescription' => 'Билды, баннеры, коды и патчи Genshin Impact на русском.',
            'canonicalPath' => '/',
            'ogImage' => OgManifest::imageForCanonicalPath('/'),
            'ogAlt' => 'GenshinTop — гайды и справка по Genshin Impact',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => ContentRepository::latestMtime() ?: null,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function guidesIndex(array $cfg): array
    {
        $all = ContentRepository::guidesSortedByRecent();
        $countAll = count($all);
        $qRaw = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
        $qEsc = Html::e($qRaw);
        $cards = implode('', array_map(fn ($g) => HtmlComponents::guideCatalogCard($g), $all));

        $categories = [
            ['id' => '', 'label' => 'Все типы'],
            ['id' => 'banner', 'label' => 'Баннеры'],
            ['id' => 'patch', 'label' => 'Обновления'],
            ['id' => 'newbie', 'label' => 'Новичкам'],
            ['id' => 'codes', 'label' => 'Промокоды'],
            ['id' => 'tier', 'label' => 'Тир-листы'],
            ['id' => 'hardware', 'label' => 'ПК / железо'],
            ['id' => 'general', 'label' => 'Прочее'],
        ];
        $topicFilters = [['id' => '', 'label' => 'Все темы']];
        foreach (GuideTaxonomy::TOPICS as $tid) {
            $topicFilters[] = ['id' => $tid, 'label' => GuideTaxonomy::topicLabelsRu()[$tid] ?? $tid];
        }
        $statusFilters = [['id' => '', 'label' => 'Любой статус']];
        foreach (GuideTaxonomy::STATUSES as $sid) {
            $statusFilters[] = ['id' => $sid, 'label' => GuideTaxonomy::statusLabelsRu()[$sid] ?? $sid];
        }

        $catButtons = '';
        foreach ($categories as $i => $c) {
            $active = $i === 0 ? ' is-active' : '';
            $catButtons .= '<button type="button" class="filter-pill' . $active . '" data-cat="' . Html::e((string) $c['id']) . '">' . Html::e($c['label']) . '</button>';
        }
        $topicButtons = '';
        foreach ($topicFilters as $i => $c) {
            $active = $i === 0 ? ' is-active' : '';
            $topicButtons .= '<button type="button" class="filter-pill' . $active . '" data-topic="' . Html::e((string) $c['id']) . '">' . Html::e($c['label']) . '</button>';
        }
        $statusButtons = '';
        foreach ($statusFilters as $i => $c) {
            $active = $i === 0 ? ' is-active' : '';
            $statusButtons .= '<button type="button" class="filter-pill' . $active . '" data-status="' . Html::e((string) $c['id']) . '">' . Html::e($c['label']) . '</button>';
        }

        $recentForSchema = array_slice($all, 0, 24);
        $itemListElement = [];
        foreach ($recentForSchema as $i => $g) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $g['title'] ?? (string) ($g['slug'] ?? ''),
                'item' => Seo::absoluteUrl($cfg, ContentRepository::itemUrl($g)),
            ];
        }
        $pageUrl = Seo::absoluteUrl($cfg, '/guides');
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            [
                '@type' => 'CollectionPage',
                '@id' => $pageUrl . '#webpage',
                'name' => 'Гайды Genshin Impact',
                'description' => 'Гайды по Genshin Impact на русском: баннеры, патчи, промокоды и советы.',
                'url' => $pageUrl,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'name' => 'Недавно обновлённые гайды',
                    'description' => 'Фрагмент каталога (' . count($recentForSchema) . ' из ' . $countAll . ' материалов).',
                    'numberOfItems' => count($recentForSchema),
                    'itemListElement' => $itemListElement,
                ],
            ],
        ]);

        $slot = <<<HTML
<article class="article catalog-page">
<h1>Гайды Genshin Impact</h1>
<p class="lead">Материалы по игре: баннеры, патчи, промокоды. Фильтры работают на устройстве; каждая статья — отдельный URL.</p>
<section class="callout">
  <h2>По структуре сайта</h2>
  <p class="hub-links">
    <a href="/guides/basics">Основы игры</a> ·
    <a href="/guides/advanced">Продвинутые гайды</a> ·
    <a href="/guides/walkthroughs">Квесты и прохождения</a>
  </p>
</section>
<form class="guide-search-form" method="get" action="/guides" role="search">
  <label class="sr-only" for="guide-search">Поиск по гайдам</label>
  <input id="guide-search" type="search" name="q" data-guide-search value="{$qEsc}" maxlength="200" placeholder="Поиск по названию и описанию…" autocomplete="off" />
  <button type="submit" class="btn btn-primary">Найти</button>
</form>
<section class="callout">
  <h2>Связанные разделы</h2>
  <p><a href="/characters">Каталог персонажей</a> · <a href="/">Главная</a></p>
</section>
<section class="callout">
  <h2>Тематические хабы</h2>
  <p class="hub-links">
    <a href="/guides/banners">Баннеры</a> · <a href="/guides/patches">Обновления</a> · <a href="/guides/codes">Промокоды</a> ·
    <a href="/guides/newbie">Новичкам</a> · <a href="/guides/economy">Экономика</a> · <a href="/guides/tier-list">Тир-листы</a>
  </p>
</section>
<div class="filter-section">
  <p class="filter-label">Тип (коллекция)</p>
  <div class="filter-row" data-guide-cats>{$catButtons}</div>
  <p class="filter-label">Тема для игрока</p>
  <div class="filter-row" data-guide-topics>{$topicButtons}</div>
  <p class="filter-label">Актуальность</p>
  <div class="filter-row" data-guide-status>{$statusButtons}</div>
</div>
<p class="catalog-count">Показано: <span data-guide-count>{$countAll}</span> из {$countAll}</p>
<div class="grid-guides" id="guide-grid">{$cards}</div>
</article>
<script>
(function(){
  function paintGroup(root, attr, active) {
    var wrap = document.querySelector(root);
    if (!wrap) return;
    wrap.querySelectorAll('button').forEach(function(b) {
      var id = b.getAttribute(attr) || '';
      b.classList.toggle('is-active', id === active);
    });
  }
  function cardEls() { return Array.prototype.slice.call(document.querySelectorAll('[data-guide-card]')); }
  var countEl = document.querySelector('[data-guide-count]');
  var activeCat = '', activeTopic = '', activeStatus = '';
  function apply() {
    var input = document.querySelector('[data-guide-search]');
    var q = (input && input.value ? input.value : '').trim().toLowerCase();
    var visible = 0;
    cardEls().forEach(function(el) {
      var cat = el.dataset.category || '';
      var topic = el.dataset.topic || '';
      var status = el.dataset.status || '';
      var hay = el.dataset.searchHaystack || '';
      var show = (!activeCat || cat === activeCat) && (!activeTopic || topic === activeTopic) && (!activeStatus || status === activeStatus) && (!q || hay.indexOf(q) !== -1);
      el.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (countEl) countEl.textContent = String(visible);
  }
  function repaintAll() {
    paintGroup('[data-guide-cats]', 'data-cat', activeCat);
    paintGroup('[data-guide-topics]', 'data-topic', activeTopic);
    paintGroup('[data-guide-status]', 'data-status', activeStatus);
    apply();
  }
  var ec = document.querySelector('[data-guide-cats]');
  if (ec) ec.addEventListener('click', function(ev) {
    var btn = ev.target.closest('button'); if (!btn) return;
    activeCat = btn.getAttribute('data-cat') || '';
    repaintAll();
  });
  var et = document.querySelector('[data-guide-topics]');
  if (et) et.addEventListener('click', function(ev) {
    var btn = ev.target.closest('button'); if (!btn) return;
    activeTopic = btn.getAttribute('data-topic') || '';
    repaintAll();
  });
  var es = document.querySelector('[data-guide-status]');
  if (es) es.addEventListener('click', function(ev) {
    var btn = ev.target.closest('button'); if (!btn) return;
    activeStatus = btn.getAttribute('data-status') || '';
    repaintAll();
  });
  var inp = document.querySelector('[data-guide-search]');
  if (inp) inp.addEventListener('input', apply);
  repaintAll();
})();
</script>
HTML;

        $guidesMtime = ContentRepository::latestMtime(static fn (array $i) => !$i['isIndex'] && str_starts_with((string) $i['section'], 'guides'));

        return [
            'pageTitle' => 'Гайды Genshin Impact',
            'pageDescription' => 'Гайды по Genshin Impact: баннеры, патчи, промокоды, тир-листы.',
            'canonicalPath' => '/guides',
            'robots' => $qRaw !== '' ? 'noindex, follow' : 'index, follow',
            'ogImage' => OgManifest::imageForCanonicalPath('/guides'),
            'ogAlt' => 'Каталог гайдов GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $guidesMtime ?: null,
        ];
    }

    /**
     * @param array<string,mixed> $cfg
     * @param array{title:string,description:string,intro:string} $hubDef
     */
    public static function guideHub(array $cfg, string $hubId, array $hubDef): array
    {
        $match = self::hubMatcher($hubId);
        $guides = ContentRepository::filterGuides($match);
        usort($guides, fn ($a, $b) => ContentRepository::guideTimestamp($b) <=> ContentRepository::guideTimestamp($a));
        $cards = implode('', array_map(fn ($g) => HtmlComponents::guideCatalogCard($g), $guides));
        $canonicalPath = '/guides/' . $hubId;
        $countHub = count($guides);
        $bc = HtmlComponents::breadcrumbs($cfg, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Гайды', 'href' => '/guides'],
            ['label' => $hubDef['title'], 'href' => $canonicalPath],
        ]);
        $slot = $bc
            . '<article class="article catalog-page"><h1>' . Html::e($hubDef['title']) . '</h1>'
            . '<p class="lead">' . Html::e($hubDef['intro']) . '</p>'
            . '<p class="catalog-count">Материалов в хабе: ' . $countHub . '</p>'
            . '<div class="grid-guides">' . $cards . '</div></article>';

        $listedGuides = array_slice($guides, 0, 48);
        $items = [];
        foreach ($listedGuides as $i => $g) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $g['title'] ?? (string) ($g['slug'] ?? ''),
                'item' => Seo::absoluteUrl($cfg, ContentRepository::itemUrl($g)),
            ];
        }
        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $desc = $hubDef['description'];
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'Гайды', 'href' => '/guides'],
                ['label' => $hubDef['title'], 'href' => $canonicalPath],
            ]),
            [
                '@type' => 'CollectionPage',
                '@id' => $url . '#webpage',
                'name' => $hubDef['title'],
                'description' => $desc,
                'url' => $url,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($listedGuides),
                    'itemListElement' => $items,
                ],
            ],
        ]);

        $hubMtime = 0;
        foreach ($guides as $hg) {
            $t = ContentRepository::itemMtime($hg);
            if ($t > $hubMtime) {
                $hubMtime = $t;
            }
        }

        return [
            'pageTitle' => $hubDef['title'],
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $hubMtime ?: null,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function guideArticle(array $cfg, array $g): array
    {
        $slug = (string) ($g['slug'] ?? '');
        $canonicalPath = ContentRepository::itemUrl($g);
        $meta = is_array($g['meta'] ?? null) ? $g['meta'] : [];
        $title = (string) ($g['title'] ?? $slug);
        $summary = isset($g['summary']) && is_string($g['summary']) ? $g['summary'] : '';
        $bodyMd = (string) ($g['body_md'] ?? '');
        $desc = Seo::cleanMetaDescription($summary, $title);
        $fileHint = $slug . '.md';
        $topic = GuideTaxonomy::effectiveTopic($meta, $fileHint, $summary);
        $status = GuideTaxonomy::effectiveStatus($meta, $slug, $summary);
        $gv = GuideTaxonomy::effectiveGameVersion($meta, $slug, $summary);
        $topicLabels = GuideTaxonomy::topicLabelsRu();
        $statusLabels = GuideTaxonomy::statusLabelsRu();
        $topicRu = Html::e($topicLabels[$topic] ?? $topic);
        $statusRu = Html::e($statusLabels[$status] ?? $status);
        $gvEsc = $gv ? Html::e('Версия: ' . $gv) : '';

        $publishedIso = self::metaIso(isset($meta['date']) ? $meta['date'] : null);
        $modifiedIso = self::metaIso($meta['reviewedAt'] ?? null)
            ?? self::metaIso($meta['updatedAt'] ?? null)
            ?? self::metaIso(isset($meta['date']) ? $meta['date'] : null);

        $articleTimes = [];
        if ($publishedIso) {
            $articleTimes['publishedTime'] = $publishedIso;
        }
        if ($modifiedIso) {
            $articleTimes['modifiedTime'] = $modifiedIso;
        }

        $relatedGuideSlugs = [];
        if (!empty($meta['relatedGuides']) && is_array($meta['relatedGuides'])) {
            foreach ($meta['relatedGuides'] as $rs) {
                if (is_string($rs) && $rs !== '') {
                    $relatedGuideSlugs[] = $rs;
                }
            }
        }
        $relatedCharSlugs = [];
        if (!empty($meta['relatedCharacters']) && is_array($meta['relatedCharacters'])) {
            foreach ($meta['relatedCharacters'] as $rs) {
                if (is_string($rs) && $rs !== '') {
                    $relatedCharSlugs[] = $rs;
                }
            }
        }
        $relatedGuidesHtml = HtmlComponents::guideBadgeLinks($relatedGuideSlugs);
        $relatedCharsHtml = HtmlComponents::characterBadgeLinks($relatedCharSlugs);

        $sourcesHtml = '';
        $sourceLines = self::sourceLines($meta['sources'] ?? []);
        if ($sourceLines !== []) {
            $sourcesHtml = '<section class="sources"><h2>Источники</h2><ul>';
            foreach ($sourceLines as $s) {
                $sourcesHtml .= '<li>' . Html::e($s) . '</li>';
            }
            $sourcesHtml .= '</ul></section>';
        }

        $reviewedHtml = '';
        if (!empty($meta['reviewedAt']) && is_string($meta['reviewedAt'])) {
            $isoR = self::metaIso($meta['reviewedAt']);
            $reviewedHtml = '<p class="meta-reviewed">Проверено редакцией: <time' . ($isoR ? ' datetime="' . Html::e($isoR) . '"' : '') . '>' . Html::e($meta['reviewedAt']) . '</time></p>';
        }

        $htmlBody = ContentRepository::markdownToHtml($bodyMd);
        $bc = HtmlComponents::breadcrumbs($cfg, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Гайды', 'href' => '/guides'],
            ['label' => $title, 'href' => $canonicalPath],
        ]);

        $badges = '<div class="article-badges"><span class="pill">' . $topicRu . '</span><span class="pill pill-muted">' . $statusRu . '</span>';
        if ($gvEsc !== '') {
            $badges .= '<span class="pill pill-gold">' . $gvEsc . '</span>';
        }
        $badges .= '</div>';

        $slot = $bc . '<article class="article prose-flow">' . $badges . '<header class="article-head"><h1>' . Html::e($title) . '</h1></header>'
            . $reviewedHtml . '<div class="prose">' . $htmlBody . '</div>'
            . ($relatedGuidesHtml !== '' ? '<section class="related-block"><h2>Связанные гайды</h2>' . $relatedGuidesHtml . '</section>' : '')
            . ($relatedCharsHtml !== '' ? '<section class="related-block"><h2>Связанные персонажи</h2>' . $relatedCharsHtml . '</section>' : '')
            . $sourcesHtml . '</article>';

        $ogPath = OgManifest::imageForEntry('guides', $slug);
        $jsonLdDates = [];
        if ($publishedIso) {
            $jsonLdDates['datePublished'] = $publishedIso;
        }
        if ($modifiedIso) {
            $jsonLdDates['dateModified'] = $modifiedIso;
        }
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'Гайды', 'href' => '/guides'],
                ['label' => $title, 'href' => $canonicalPath],
            ]),
            array_merge([
                '@type' => 'BlogPosting',
                '@id' => Seo::absoluteUrl($cfg, $canonicalPath) . '#article',
                'headline' => $title,
                'description' => $desc,
                'inLanguage' => 'ru-RU',
                'url' => Seo::absoluteUrl($cfg, $canonicalPath),
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => Seo::absoluteUrl($cfg, $canonicalPath),
                ],
                'image' => [Seo::absoluteUrl($cfg, $ogPath)],
                'author' => ['@id' => Seo::siteUrl($cfg) . '/#editorial-team'],
                'publisher' => ['@id' => Seo::siteUrl($cfg) . '/#organization'],
            ], $jsonLdDates),
            Seo::editorialTeamPerson($cfg),
        ]);

        return [
            'pageTitle' => $title,
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'ogType' => 'article',
            'ogImage' => $ogPath,
            'ogAlt' => $title . ' — гайд GenshinTop',
            'articleTimes' => $articleTimes !== [] ? $articleTimes : null,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => ContentRepository::itemMtime($g) ?: null,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function charactersIndex(array $cfg): array
    {
        $all = ContentRepository::characters();
        $countAll = count($all);
        $cards = implode('', array_map(fn ($c) => HtmlComponents::characterCard($c), $all));

        $elements = ['Pyro', 'Hydro', 'Electro', 'Cryo', 'Anemo', 'Geo', 'Dendro'];
        $weapons = ['Одноручное', 'Двуручное', 'Древковое', 'Катализатор', 'Лук', 'Прочее'];

        $elBtns = '<button type="button" class="filter-pill is-active" data-filter-value="">Все</button>';
        foreach ($elements as $el) {
            $elBtns .= '<button type="button" class="filter-pill" data-filter-value="' . Html::e($el) . '">' . Html::e($el) . '</button>';
        }
        $wBtns = '<button type="button" class="filter-pill is-active" data-filter-value="">Все</button>';
        foreach ($weapons as $w) {
            $wBtns .= '<button type="button" class="filter-pill" data-filter-value="' . Html::e($w) . '">' . Html::e($w) . '</button>';
        }

        $slot = <<<HTML
<article class="article catalog-page">
<h1>Персонажи Genshin Impact</h1>
<p class="lead">Каталог героев: стихия, оружие, редкость. Фильтры на клиенте; все карточки — в HTML для индексации.</p>
<section class="callout">
  <h2>Ещё по теме</h2>
  <p><a href="/guides/basics">Основы</a> · <a href="/guides/advanced">Продвинутые</a> · <a href="/guides/walkthroughs">Квесты</a> · <a href="/guides/banners">Баннеры</a> · <a href="/guides/patches">Патчи</a> · <a href="/guides/tier-list">Тир-листы</a> · <a href="/guides/newbie">Новичкам</a> · <a href="/guides/codes">Промокоды</a></p>
  <p class="hub-links">
    <a href="/characters/pyro">Пиро</a> <a href="/characters/hydro">Гидро</a> <a href="/characters/electro">Электро</a>
    <a href="/characters/cryo">Крио</a> <a href="/characters/anemo">Анемо</a> <a href="/characters/geo">Гео</a> <a href="/characters/dendro">Дендро</a>
    · <a href="/characters/5-star">5★</a> <a href="/characters/4-star">4★</a>
    · <a href="/characters/sword">Меч</a> <a href="/characters/claymore">Двуручное</a> <a href="/characters/polearm">Копьё</a>
    <a href="/characters/catalyst">Катализатор</a> <a href="/characters/bow">Лук</a>
  </p>
</section>
<div class="char-catalog-layout">
  <aside class="char-filters">
    <label class="filter-label" for="char-search">Поиск по имени</label>
    <input id="char-search" type="search" placeholder="Например, Нахида" data-char-search />
    <p class="filter-label">Стихия</p>
    <div class="filter-row" data-filter-group="element">{$elBtns}</div>
    <p class="filter-label">Оружие</p>
    <div class="filter-row" data-filter-group="weapon">{$wBtns}</div>
    <p class="filter-label">Редкость</p>
    <div class="filter-row" data-filter-group="rarity">
      <button type="button" class="filter-pill is-active" data-filter-value="">Все</button>
      <button type="button" class="filter-pill" data-filter-value="5">★★★★★</button>
      <button type="button" class="filter-pill" data-filter-value="4">★★★★</button>
    </div>
  </aside>
  <div class="char-catalog-main">
    <p class="catalog-count">Показано: <span data-char-count>{$countAll}</span> из {$countAll}</p>
    <div class="grid-cards" id="character-grid">{$cards}</div>
  </div>
</div>
</article>
<script>
(function(){
  function cards() { return Array.prototype.slice.call(document.querySelectorAll('[data-character-card]')); }
  var search = document.querySelector('[data-char-search]');
  var countEl = document.querySelector('[data-char-count]');
  var active = { element: '', weapon: '', rarity: '' };
  function applyFilters() {
    var q = (search && search.value ? search.value : '').trim().toLowerCase();
    var visible = 0;
    cards().forEach(function(el) {
      var name = (el.dataset.name || '').toLowerCase();
      var element = el.dataset.element || '';
      var weapon = el.dataset.weapon || '';
      var rarity = el.dataset.rarity || '';
      var okName = !q || name.indexOf(q) !== -1;
      var okEl = !active.element || element === active.element;
      var okW = !active.weapon || weapon === active.weapon;
      var okR = !active.rarity || rarity === active.rarity;
      var show = okName && okEl && okW && okR;
      el.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (countEl) countEl.textContent = String(visible);
  }
  if (search) search.addEventListener('input', applyFilters);
  ['element','weapon','rarity'].forEach(function(group) {
    var wrap = document.querySelector('[data-filter-group="' + group + '"]');
    if (!wrap) return;
    wrap.addEventListener('click', function(ev) {
      var btn = ev.target.closest('button');
      if (!btn) return;
      active[group] = btn.getAttribute('data-filter-value') || '';
      wrap.querySelectorAll('button').forEach(function(b) {
        b.classList.toggle('is-active', b === btn);
      });
      applyFilters();
    });
  });
  applyFilters();
})();
</script>
HTML;

        $pageUrl = Seo::absoluteUrl($cfg, '/characters');
        $itemListElement = [];
        foreach ($all as $i => $c) {
            $slug = (string) ($c['slug'] ?? '');
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c['name'] ?? $slug,
                'item' => Seo::absoluteUrl($cfg, '/characters/' . $slug),
            ];
        }
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            [
                '@type' => 'CollectionPage',
                '@id' => $pageUrl . '#webpage',
                'name' => 'Персонажи Genshin Impact — каталог',
                'description' => 'Каталог персонажей Genshin Impact на русском: стихия, оружие, редкость.',
                'url' => $pageUrl,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($all),
                    'itemListElement' => $itemListElement,
                ],
            ],
        ]);

        $charsMtime = ContentRepository::latestMtime(static fn (array $i) => !$i['isIndex'] && (string) $i['section'] === 'characters');

        return [
            'pageTitle' => 'Персонажи Genshin Impact',
            'pageDescription' => 'Каталог персонажей Genshin Impact: стихия, оружие, редкость и ссылки на материалы.',
            'canonicalPath' => '/characters',
            'ogImage' => OgManifest::imageForCanonicalPath('/characters'),
            'ogAlt' => 'Каталог персонажей Genshin Impact — GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $charsMtime ?: null,
        ];
    }

    /**
     * @param array<string,mixed> $cfg
     * @param array{element:string,title:string,description:string,intro:string} $meta
     */
    public static function characterElementHub(array $cfg, string $urlKey, array $meta): array
    {
        $filter = CharacterHub::filterElement($urlKey);
        $list = ContentRepository::filterCharacters($filter);
        return self::characterFilteredHubPage($cfg, '/characters/' . $urlKey, $meta['title'], $meta['description'], $meta['intro'], $list, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Персонажи', 'href' => '/characters'],
            ['label' => $meta['element'], 'href' => '/characters/' . $urlKey],
        ]);
    }

    /**
     * @param array<string,mixed> $cfg
     * @param array{weapon:string,title:string,description:string,intro:string} $meta
     */
    public static function characterWeaponHub(array $cfg, string $urlKey, array $meta): array
    {
        $filter = CharacterHub::filterWeapon($meta['weapon']);
        $list = ContentRepository::filterCharacters($filter);

        return self::characterFilteredHubPage($cfg, '/characters/' . $urlKey, $meta['title'], $meta['description'], $meta['intro'], $list, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Персонажи', 'href' => '/characters'],
            ['label' => $meta['weapon'], 'href' => '/characters/' . $urlKey],
        ]);
    }

    /**
     * @param array<string,mixed> $cfg
     * @param array{rarity:int,title:string,description:string,intro:string} $meta
     */
    public static function characterRarityHub(array $cfg, string $urlKey, array $meta): array
    {
        $filter = CharacterHub::filterRarity($meta['rarity']);
        $list = ContentRepository::filterCharacters($filter);

        return self::characterFilteredHubPage($cfg, '/characters/' . $urlKey, $meta['title'], $meta['description'], $meta['intro'], $list, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Персонажи', 'href' => '/characters'],
            ['label' => (string) $meta['rarity'] . '★', 'href' => '/characters/' . $urlKey],
        ]);
    }

    /**
     * @param list<array<string,mixed>> $items
     * @param list<array{label:string,href:string}> $crumbs
     */
    private static function characterFilteredHubPage(array $cfg, string $canonicalPath, string $title, string $description, string $intro, array $items, array $crumbs): array
    {
        $cards = implode('', array_map(fn ($c) => HtmlComponents::characterCard($c), $items));
        $bc = HtmlComponents::breadcrumbs($cfg, $crumbs);
        $slot = $bc . '<article class="article catalog-page"><h1>' . Html::e($title) . '</h1><p class="lead">' . Html::e($intro) . '</p>'
            . '<p class="catalog-count">Персонажей: ' . count($items) . '</p><div class="grid-cards">' . $cards . '</div></article>';

        $listedCharacters = array_slice($items, 0, 60);
        $listLd = [];
        foreach ($listedCharacters as $i => $c) {
            $slug = (string) ($c['slug'] ?? '');
            $listLd[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c['name'] ?? $slug,
                'item' => Seo::absoluteUrl($cfg, '/characters/' . $slug),
            ];
        }
        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, $crumbs),
            [
                '@type' => 'CollectionPage',
                '@id' => $url . '#webpage',
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($listedCharacters),
                    'itemListElement' => $listLd,
                ],
            ],
        ]);

        $hubMtime = 0;
        foreach ($items as $hi) {
            $t = ContentRepository::itemMtime($hi);
            if ($t > $hubMtime) {
                $hubMtime = $t;
            }
        }

        return [
            'pageTitle' => $title,
            'pageDescription' => $description,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $hubMtime ?: null,
        ];
    }

    private static function elementRu(string $el): string
    {
        return match ($el) {
            'Pyro' => 'Пиро',
            'Hydro' => 'Гидро',
            'Electro' => 'Электро',
            'Cryo' => 'Крио',
            'Anemo' => 'Анемо',
            'Geo' => 'Гео',
            'Dendro' => 'Дендро',
            default => $el,
        };
    }

    /** @param array<string,mixed> $cfg */
    public static function characterArticle(array $cfg, array $c): array
    {
        $slug = (string) ($c['slug'] ?? '');
        $canonicalPath = '/characters/' . $slug;
        $meta = is_array($c['meta'] ?? null) ? $c['meta'] : [];
        $name = (string) ($c['name'] ?? $slug);
        $displayTitle = isset($meta['title']) && is_string($meta['title']) && $meta['title'] !== '' ? $meta['title'] : $name;
        $element = (string) ($c['element'] ?? 'Anemo');
        $weapon = (string) ($c['weapon'] ?? '');
        $fallbackDesc = $name . ' — ' . self::elementRu($element) . ', ' . $weapon . '. Гайд и описание в GenshinTop.';
        $metaTitleForDesc = isset($meta['title']) && is_string($meta['title']) ? $meta['title'] : null;
        $desc = Seo::cleanMetaDescription($metaTitleForDesc, $fallbackDesc);

        $relatedGuideSlugs = [];
        if (!empty($meta['relatedGuides']) && is_array($meta['relatedGuides'])) {
            foreach ($meta['relatedGuides'] as $rs) {
                if (is_string($rs) && $rs !== '') {
                    $relatedGuideSlugs[] = $rs;
                }
            }
        }
        $relatedGuidesHtml = HtmlComponents::guideBadgeLinks($relatedGuideSlugs);

        $bodyMd = (string) ($c['body_md'] ?? '');
        $htmlBody = ContentRepository::markdownToHtml($bodyMd);

        $bc = HtmlComponents::breadcrumbs($cfg, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Персонажи', 'href' => '/characters'],
            ['label' => $name, 'href' => $canonicalPath],
        ]);

        $slot = $bc . '<article class="article prose-flow"><header class="article-head"><h1>' . Html::e($displayTitle) . '</h1>'
            . '<p class="char-inline-meta">' . Html::e(self::elementRu($element)) . ' · ' . Html::e($weapon) . '</p></header>'
            . '<div class="prose">' . $htmlBody . '</div>'
            . ($relatedGuidesHtml !== '' ? '<section class="related-block"><h2>Связанные гайды</h2>' . $relatedGuidesHtml . '</section>' : '')
            . '</article>';

        $ogPath = OgManifest::imageForEntry('characters', $slug);
        $charMtime = ContentRepository::itemMtime($c);
        $charDateModified = $charMtime > 0 ? gmdate('c', $charMtime) : null;
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'Персонажи', 'href' => '/characters'],
                ['label' => $name, 'href' => $canonicalPath],
            ]),
            array_filter([
                '@type' => 'Article',
                '@id' => Seo::absoluteUrl($cfg, $canonicalPath) . '#article',
                'headline' => $displayTitle,
                'description' => $desc,
                'inLanguage' => 'ru-RU',
                'url' => Seo::absoluteUrl($cfg, $canonicalPath),
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => Seo::absoluteUrl($cfg, $canonicalPath),
                ],
                'image' => [Seo::absoluteUrl($cfg, $ogPath)],
                'author' => ['@id' => Seo::siteUrl($cfg) . '/#editorial-team'],
                'publisher' => ['@id' => Seo::siteUrl($cfg) . '/#organization'],
                'dateModified' => $charDateModified,
                'about' => [
                    [
                        '@type' => 'Thing',
                        'name' => $name,
                        'description' => self::elementRu($element) . ', ' . $weapon . '. Персонаж Genshin Impact.',
                    ],
                    Seo::genshinVideoGameNode(),
                ],
            ], static fn ($v) => $v !== null && $v !== ''),
            Seo::editorialTeamPerson($cfg),
        ]);

        return [
            'pageTitle' => $displayTitle,
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'ogType' => 'article',
            'ogImage' => $ogPath,
            'ogAlt' => $name . ' — ' . self::elementRu($element) . ', ' . $weapon . ' | GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $charMtime ?: null,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function lootbarIndex(array $cfg): array
    {
        $topupUrl = Partners::lootbarGenshinTopupUrl('lootbar_hero');
        $crystalsUrl = Partners::lootbarGenshinTopupUrl('lootbar_hub_crystals');
        $welkinUrl = Partners::lootbarGenshinTopupUrl('lootbar_hub_welkin');
        $howTo = Seo::howToSchema([
            'name' => 'Как получить и применить купон на LootBar.gg для Genshin Impact',
            'description' => 'Общий порядок действий для скидок на стороннем топ-апе.',
            'steps' => LootbarConfig::howToStepTexts(),
        ]);
        $faqs = [
            ['question' => 'Как купить Genshin Impact дешевле через LootBar?', 'answer' => 'Откройте страницу Genshin Impact на LootBar.gg по партнёрской ссылке, выберите пакет и оплатите. Условия зависят от региона и акций сервиса.'],
            ['question' => 'Безопасно ли пополнение через LootBar?', 'answer' => 'Обычно используется UID без пароля; проверяйте домен lootbar.gg и сохраняйте чек. Подробнее — секция «Безопасность» на /lootbar.'],
            ['question' => 'Где посмотреть UID и какой регион выбрать?', 'answer' => 'UID виден в клиенте: меню паузы → Настройки → раздел про аккаунт. Регион в форме LootBar должен совпадать с регионом аккаунта Genshin Impact (Asia, Europe, America, TW/HK/MO).'],
        ];
        $faqSchema = Seo::faqPageSchema($faqs);
        $url = Seo::absoluteUrl($cfg, '/lootbar');
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            [
                '@type' => 'WebPage',
                '@id' => $url . '#webpage',
                'name' => 'Пополнение Genshin Impact через LootBar.gg',
                'url' => $url,
                'description' => 'Хаб партнёрского топ-апа Genshin Impact на LootBar.gg: шаги и FAQ.',
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
            ],
            $howTo,
            $faqSchema,
        ]);

        $outUrl = Html::e($topupUrl);
        $crystalsOut = Html::e($crystalsUrl);
        $welkinOut = Html::e($welkinUrl);
        $slot = <<<HTML
<article class="article prose-flow lootbar-hub">
<p class="muted"><a href="/partnership-disclosure">Раскрытие партнёрства</a></p>
<h1>Пополнение Genshin Impact через LootBar.gg</h1>
<p class="lead">Партнёрский раздел: внешние ссылки помечены как <code>rel=sponsored</code>. Цены и условия — на стороне LootBar.</p>
<p><a class="btn btn-lootbar" href="{$outUrl}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_hub_cta">Перейти к топ-апу на LootBar.gg</a></p>
<nav class="lootbar-anchor-nav" aria-label="Содержание раздела LootBar">
  <a href="/lootbar/skidki-i-kupony">Скидки и купоны</a>
  <a href="/lootbar/kak-popolnit-genshin-impact">Как пополнить</a>
  <a href="#promokod">Промокод</a>
  <a href="#kristally">Кристаллы</a>
  <a href="#welkin">Welkin Moon</a>
  <a href="#bezopasnost">Безопасность</a>
</nav>
<section id="promokod">
  <h2>Промокод и купоны LootBar</h2>
  <p>Магазинный купон LootBar — это скидка сервиса, она применяется при оплате на <strong>lootbar.gg</strong> и не пересекается с кодами обмена HoYoverse (HoYoLAB / Daily Login). Промокоды HoYoverse дают примогемы и материалы внутри игры; купоны LootBar дают скидку на покупку валюты у партнёра.</p>
  <p>После регистрации по партнёрской ссылке в профиле LootBar обычно появляются два купона для новых пользователей — 6% и 10% OFF. Срок и потолок скидки указаны в самом профиле.</p>
  <p><a class="btn btn-secondary" href="/lootbar/skidki-i-kupony">Все купоны и условия →</a></p>
</section>
<section id="kristally">
  <h2>Кристаллы Сотворения (Genesis Crystals)</h2>
  <p>Genesis Crystals — премиумная валюта Genshin Impact. В игре они конвертируются в Камни Истока (Primogems), которые тратятся на молитвы, восстановление Смолы и Battle Pass. На витрине LootBar доступны пакеты разного объёма; цена и доступность зависят от региона аккаунта.</p>
  <p><a class="btn btn-lootbar" href="{$crystalsOut}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_hub_crystals_cta">Открыть пакеты кристаллов</a></p>
</section>
<section id="welkin">
  <h2>Благословение Полой Луны (Welkin Moon)</h2>
  <p>Welkin Moon — месячная подписка: разовая выдача Genesis Crystals и затем по 90 Primogems каждый день в течение 30 дней при заходе в игру. В пересчёте на день обычно выгоднее разовых пакетов кристаллов того же бюджета — но требует регулярного входа.</p>
  <p><a class="btn btn-lootbar" href="{$welkinOut}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_hub_welkin_cta">Купить Welkin Moon</a></p>
</section>
<section id="bezopasnost">
  <h2>Безопасность и оплата</h2>
  <ul>
    <li>LootBar пополняет аккаунт по <strong>UID</strong> — пароль от HoYoverse сторонним сервисам передавать не нужно.</li>
    <li>Проверяйте домен <code>lootbar.gg</code> в адресной строке перед оплатой; партнёрская ссылка GenshinTop ведёт сразу на нужную витрину с UTM-метками.</li>
    <li>Сохраняйте чек и номер заказа — они нужны для поддержки LootBar в случае спорной ситуации.</li>
    <li>Регион в форме оплаты должен совпадать с регионом аккаунта (Asia, Europe, America, TW/HK/MO), иначе доставка может не пройти.</li>
  </ul>
  <p><a class="btn btn-secondary" href="/lootbar/kak-popolnit-genshin-impact">Подробная инструкция по пополнению →</a></p>
</section>
<section>
  <h2>FAQ</h2>
  <dl class="faq-dl">
    <dt>Как купить дешевле через LootBar?</dt><dd>Используйте партнёрскую ссылку и проверьте купоны в профиле сервиса перед оплатой.</dd>
    <dt>Безопасно ли пополнение?</dt><dd>Платёж по UID без пароля; домен — <code>lootbar.gg</code>; чек сохраняйте до выполнения заказа.</dd>
    <dt>Где посмотреть UID?</dt><dd>В клиенте: меню паузы → Настройки → раздел про аккаунт. Регион в форме LootBar должен совпадать с регионом аккаунта.</dd>
  </dl>
</section>
</article>
HTML;

        return [
            'pageTitle' => 'Пополнение Genshin Impact через LootBar.gg — GenshinTop',
            'pageDescription' => 'Партнёрский топ-ап Genshin Impact на LootBar.gg: безопасность, промокоды, кристаллы и луна.',
            'canonicalPath' => '/lootbar',
            'hideLootBarPromo' => true,
            'ogImage' => OgManifest::imageForCanonicalPath('/lootbar'),
            'ogAlt' => 'Раздел пополнения Genshin Impact через LootBar — GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => self::filesMtime(['lib/PageRenderer.php', 'lib/LootbarConfig.php', 'lib/Partners.php']),
        ];
    }

    /** @return array<string,mixed>|null */
    public static function lootbarSubpage(array $cfg, string $slug): ?array
    {
        if ($slug === 'kak-popolnit-genshin-impact') {
            return self::lootbarHowToGenshinLanding($cfg);
        }
        if ($slug === 'skidki-i-kupony') {
            return self::lootbarDiscountsLanding($cfg);
        }
        return null;
    }

    /**
     * Инструкция пополнения по потоку витрины LootBar (ru/top-up/genshin-impact).
     *
     * @param array<string,mixed> $cfg
     *
     * @return array<string,mixed>
     */
    private static function lootbarHowToGenshinLanding(array $cfg): array
    {
        $canonicalPath = '/lootbar/kak-popolnit-genshin-impact';
        $topupUrl = Partners::lootbarGenshinTopupUrl('lootbar_howto');
        $outUrl = Html::e($topupUrl);
        $pageTitle = 'Как пополнить Genshin Impact через LootBar.gg — пошаговый топ-ап';
        $pageDescription = 'Инструкция по пополнению через витрину LootBar для Genshin Impact: UID, сервер, купон и оплата — как на странице lootbar.gg/ru/top-up/genshin-impact.';

        $steps = LootbarConfig::howToStepTexts();
        $stepsLi = '';
        foreach ($steps as $i => $text) {
            $n = $i + 1;
            $stepsLi .= '<li><strong>Шаг ' . $n . '.</strong> ' . Html::e($text) . '</li>';
        }

        $howTo = Seo::howToSchema([
            'name' => 'Как пополнить Genshin Impact через LootBar.gg',
            'description' => 'Порядок действий на витрине Top Up для Genshin Impact.',
            'steps' => $steps,
        ]);

        $slot = <<<HTML
<article class="article prose-flow lootbar-howto-page">
<p class="back-link"><a href="/lootbar">← Хаб LootBar</a></p>
<p class="muted"><a href="/partnership-disclosure">Раскрытие партнёрства</a></p>
<h1>{$pageTitle}</h1>
<p class="lead">Ниже — типовой сценарий того же экрана заказа, что открывается по адресу <strong>lootbar.gg/ru/top-up/genshin-impact</strong> (в т.ч. если вы попали на сайт по ссылке с параметрами вроде <code>utm_campaign=p_invite</code>). Подписи полей и порядок шагов на стороне LootBar могут слегка меняться — ориентируйтесь на актуальную форму на сайте партнёра.</p>
<p><a class="btn btn-lootbar" href="{$outUrl}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_howto_open_vitrina">Открыть пополнение Genshin Impact на LootBar.gg</a></p>
<h2>Пошагово</h2>
<ol class="lootbar-howto-steps">
{$stepsLi}
</ol>
<h2>Где посмотреть UID в Genshin Impact</h2>
<p>В клиенте откройте меню паузы (иконка Паймона) → <strong>Настройки</strong> (шестерёнка, слева внизу) → раздел про аккаунт / пользовательское соглашение — там отображается <strong>UID</strong>. Его копируют в форму на LootBar; <strong>пароль от аккаунта HoYoverse не передавайте</strong> сторонним сервисам.</p>
<h2>Купоны и скидки</h2>
<p>Промокоды магазина LootBar применяются на шаге оплаты. Если только что зарегистрировались, проверьте профиль и страницу <a href="/lootbar/skidki-i-kupony">Скидки и купоны</a>.</p>
<p><a class="btn btn-secondary" href="/lootbar#bezopasnost">Безопасность и оплата →</a></p>
</article>
HTML;

        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'LootBar', 'href' => '/lootbar'],
                ['label' => 'Как пополнить', 'href' => $canonicalPath],
            ]),
            [
                '@type' => 'WebPage',
                '@id' => $url . '#webpage',
                'name' => $pageTitle,
                'url' => $url,
                'description' => $pageDescription,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
            ],
            $howTo,
        ]);

        return [
            'pageTitle' => $pageTitle . ' — GenshinTop',
            'pageDescription' => $pageDescription,
            'canonicalPath' => $canonicalPath,
            'hideLootBarPromo' => true,
            'ogImage' => OgManifest::imageForCanonicalPath($canonicalPath),
            'ogAlt' => 'Как пополнить Genshin Impact через LootBar — GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => self::filesMtime(['lib/PageRenderer.php', 'lib/LootbarConfig.php', 'lib/Partners.php']),
        ];
    }

    /**
     * Страница уровня dandangers.ru/lootbar-discounts.html — цены, купоны, шаги (Genshin Impact).
     *
     * @param array<string,mixed> $cfg
     *
     * @return array<string,mixed>
     */
    private static function lootbarDiscountsLanding(array $cfg): array
    {
        $canonicalPath = '/lootbar/skidki-i-kupony';
        $topupUrl = Partners::lootbarGenshinTopupUrl('lootbar_discounts');
        $outUrl = Html::e($topupUrl);
        $pageTitle = 'Выгодные цены на Genshin Impact — скидки до 32% и купоны LootBar';
        $pageDescription = 'Партнёрский топ-ап Genshin Impact на LootBar.gg: скидки до 32%, купоны 6% и 10% для новых пользователей, как получить промокод и безопасно пополнить аккаунт.';
        $h1 = 'Самые выгодные цены на топ-ап Genshin Impact';

        $faqs = [
            ['question' => 'Как долго действуют купоны LootBar?', 'answer' => 'Срок и лимиты отображаются в вашем профиле на LootBar.gg после регистрации по партнёрской ссылке — обычно это ограниченное окно (несколько дней). Уточняйте на стороне сервиса перед оплатой.'],
            ['question' => 'Можно ли использовать оба купона — 6% и 10%?', 'answer' => 'Как правило, каждый купон можно применить к отдельной покупке согласно правилам LootBar. Точные условия указаны в кабинете пользователя.'],
            ['question' => 'Это безопасно?', 'answer' => 'Используйте только домен lootbar.gg, не передавайте пароль от аккаунта и сохраняйте чеки. Подробнее — секция «Безопасность» на /lootbar.'],
            ['question' => 'Где посмотреть актуальные цены на кристаллы и Welkin?', 'answer' => 'Цены и пакеты зависят от региона и акций — откройте витрину Genshin Impact на LootBar.gg по партнёрской ссылке ниже.'],
        ];
        $faqSchema = Seo::faqPageSchema($faqs);

        $slot = <<<HTML
<article class="article prose-flow lootbar-discounts-page">
<p class="back-link"><a href="/lootbar">← Хаб LootBar</a></p>
<p class="muted"><a href="/partnership-disclosure">Раскрытие партнёрства</a></p>
<h1>{$h1}</h1>
<p class="lead">Покупайте <strong>Genesis Crystals</strong>, <strong>Благословение Полой Луны</strong> и другие пакеты на LootBar — часто выгоднее, чем напрямую в игре. Новые пользователи получают купоны на дополнительную скидку.</p>
<p><a class="btn btn-lootbar" href="{$outUrl}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_discounts_top_cta">Получить купон на скидку</a></p>
<hr />
<h2>LootBar.gg — партнёрский сервис</h2>
<p>Сервис специализируется на пополнении популярных игр. Для Genshin Impact доступны пакеты кристаллов и подписки. Оплата проходит на стороне LootBar; доставка привязана к вашему UID. Условия, налоги и доступность способов оплаты зависят от региона.</p>
<hr />
<h2>Доступные купоны</h2>
<p>При регистрации по партнёрской ссылке GenshinTop новые пользователи LootBar могут получить два купона на скидку (конкретные проценты и потолок скидки отображаются в профиле на lootbar.gg):</p>
<h3>Купон 10% OFF</h3>
<ul>
  <li><strong>Условие:</strong> действует для подходящих заказов согласно правилам купона в кабинете LootBar.</li>
  <li><strong>Срок:</strong> ограниченный период после получения — проверьте таймер в профиле.</li>
</ul>
<h3>Купон 6% OFF</h3>
<ul>
  <li><strong>Условие:</strong> аналогично правилам сервиса для второго купона.</li>
  <li><strong>Срок:</strong> смотрите в профиле LootBar.</li>
</ul>
<hr />
<h2>Как получить купоны</h2>
<ol>
  <li>Перейдите по нашей партнёрской ссылке на LootBar.gg.</li>
  <li>Зарегистрируйте аккаунт на платформе (если его ещё нет).</li>
  <li>Проверьте раздел купонов или профиль — промокоды должны появиться автоматически.</li>
  <li>Выберите игру <strong>Genshin Impact</strong> и нужный пакет.</li>
  <li>Примените купон при оформлении заказа.</li>
</ol>
<p>Детальная инструкция по полям формы — на странице <a href="/lootbar/kak-popolnit-genshin-impact">Как пополнить Genshin Impact через LootBar</a>.</p>
<hr />
<h2>Почему LootBar?</h2>
<table class="lootbar-benefits-table">
  <thead><tr><th>Преимущество</th><th>Описание</th></tr></thead>
  <tbody>
    <tr><td>🔒 Безопасность</td><td>Платёж проводится у провайдера; не делитесь паролем от аккаунта Genshin.</td></tr>
    <tr><td>⚡ Доставка</td><td>Пополнение по UID — следуйте подсказкам после оплаты на LootBar.</td></tr>
    <tr><td>💰 Цены</td><td>Часто ниже витрины в клиенте за счёт акций и купонов.</td></tr>
    <tr><td>🎧 Поддержка</td><td>Вопросы по заказу — через поддержку LootBar.gg.</td></tr>
  </tbody>
</table>
<hr />
<h2>Таблица цен</h2>
<p><strong>Экономия до 32%</strong> — типичный маркетинговый ориентир относительно базовых цен в игре; фактическая выгода зависит от пакета, курса и купона. Актуальные суммы всегда смотрите на витрине после перехода по ссылке.</p>
<table class="lootbar-benefits-table">
  <thead><tr><th>Категория</th><th>Что купить</th></tr></thead>
  <tbody>
    <tr><td>💎 Кристаллы</td><td>Пакеты Genesis Crystals разного объёма.</td></tr>
    <tr><td>🌙 Подписка</td><td>Благословение Полой Луны (Welkin).</td></tr>
    <tr><td>🎁 Наборы</td><td>Сезонные и промо-предложения на странице игры на LootBar.</td></tr>
  </tbody>
</table>
<hr />
<h2>Частые вопросы</h2>
<dl class="faq-dl">
  <dt>Как долго действуют купоны?</dt>
  <dd>Срок указан в профиле LootBar после получения купона.</dd>
  <dt>Можно ли использовать оба купона?</dt>
  <dd>Обычно каждый купон применяется к отдельной покупке по правилам сервиса.</dd>
  <dt>Минимальная сумма заказа?</dt>
  <dd>Зависит от условий конкретного купона на LootBar.gg.</dd>
  <dt>Это безопасно?</dt>
  <dd>Используйте только домен lootbar.gg и инструкции сервиса; см. также <a href="/lootbar#bezopasnost">секцию о безопасности</a>.</dd>
</dl>
<hr />
<h2>Готовы получить скидку?</h2>
<p><a class="btn btn-lootbar" href="{$outUrl}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_discounts_bottom_cta">Перейти на LootBar.gg</a></p>
<p class="muted"><a href="/lootbar/kak-popolnit-genshin-impact">Инструкция по пополнению →</a></p>
</article>
HTML;

        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'LootBar', 'href' => '/lootbar'],
                ['label' => 'Скидки и купоны', 'href' => $canonicalPath],
            ]),
            [
                '@type' => 'WebPage',
                '@id' => $url . '#webpage',
                'name' => $pageTitle,
                'url' => $url,
                'description' => $pageDescription,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
            ],
            $faqSchema,
        ]);

        return [
            'pageTitle' => $pageTitle . ' — GenshinTop',
            'pageDescription' => $pageDescription,
            'canonicalPath' => $canonicalPath,
            'hideLootBarPromo' => true,
            'ogImage' => OgManifest::imageForCanonicalPath($canonicalPath),
            'ogAlt' => 'Скидки и купоны LootBar для Genshin Impact — GenshinTop',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => self::filesMtime(['lib/PageRenderer.php', 'lib/LootbarConfig.php', 'lib/Partners.php']),
        ];
    }

    public static function contentSectionIndex(array $cfg, array $item): array
    {
        $title = (string) $item['title'];
        $desc = (string) ($item['summary'] ?? $title);
        $canonicalPath = ContentRepository::itemUrl($item);
        $sectionPath = trim($canonicalPath, '/');

        $htmlBody = ContentRepository::markdownToHtml($item['body_md']);

        $subsections = [];
        $articles = [];
        foreach (ContentRepository::allLive() as $child) {
            $childPath = trim(ContentRepository::itemUrl($child), '/');
            if ($child === $item || $childPath === '') {
                continue;
            }

            if ($child['isIndex']) {
                $prefix = $sectionPath === '' ? '' : $sectionPath . '/';
                if (str_starts_with($childPath, $prefix)) {
                    $relative = substr($childPath, strlen($prefix));
                    if ($relative !== '' && !str_contains($relative, '/')) {
                        $subsections[] = $child;
                    }
                }
                continue;
            }

            $parentPath = trim(str_replace('\\', '/', dirname($childPath)), '.');
            $parentPath = trim($parentPath, '/');
            if ($parentPath === $sectionPath) {
                $articles[] = $child;
            }
        }
        usort($subsections, fn ($a, $b) => strcmp((string) $a['title'], (string) $b['title']));
        usort($articles, fn ($a, $b) => strcmp((string) $a['title'], (string) $b['title']));

        $subsectionsHtml = '';
        if ($subsections !== []) {
            $subsectionsHtml = '<section class="section"><h2 class="section-heading">Подразделы</h2><div class="grid-guides">';
            foreach ($subsections as $c) {
                $subsectionsHtml .= HtmlComponents::contentCard($c);
            }
            $subsectionsHtml .= '</div></section>';
        }

        $articlesHtml = '';
        if ($articles !== []) {
            $articlesHtml = '<section class="section"><h2 class="section-heading">Статьи</h2><div class="grid-guides">';
            foreach ($articles as $c) {
                $articlesHtml .= HtmlComponents::contentCard($c);
            }
            $articlesHtml .= '</div></section>';
        }

        $bcList = SectionLabels::breadcrumbsForSection($sectionPath);
        if (isset($bcList[array_key_last($bcList)])) {
            $bcList[array_key_last($bcList)] = ['label' => $title, 'href' => $canonicalPath];
        }
        $bc = HtmlComponents::breadcrumbs($cfg, $bcList);

        $slot = $bc . '<article class="article prose-flow"><header class="article-head"><h1>' . Html::e($title) . '</h1></header><div class="prose">' . $htmlBody . '</div></article>' . $subsectionsHtml . $articlesHtml;

        $sectionUrl = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, $bcList, $sectionUrl . '#breadcrumb'),
            [
                '@type' => 'CollectionPage',
                '@id' => $sectionUrl . '#webpage',
                'name' => $title,
                'description' => $desc,
                'url' => $sectionUrl,
                'inLanguage' => 'ru-RU',
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
                'breadcrumb' => ['@id' => $sectionUrl . '#breadcrumb'],
            ],
        ]);

        $sectionMtime = ContentRepository::itemMtime($item);
        foreach ($subsections as $sc) {
            $t = ContentRepository::itemMtime($sc);
            if ($t > $sectionMtime) {
                $sectionMtime = $t;
            }
        }
        foreach ($articles as $ac) {
            $t = ContentRepository::itemMtime($ac);
            if ($t > $sectionMtime) {
                $sectionMtime = $t;
            }
        }

        return [
            'pageTitle' => $title . ' — GenshinTop',
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $sectionMtime ?: null,
        ];
    }

    public static function contentArticle(array $cfg, array $item): array
    {
        $title = (string) $item['title'];
        $desc = Seo::cleanMetaDescription($item['summary'] ?? '', $title);
        $canonicalPath = ContentRepository::itemUrl($item);

        $htmlBody = ContentRepository::markdownToHtml($item['body_md']);

        $sectionPath = trim(str_replace('\\', '/', dirname(trim($canonicalPath, '/'))), '.');
        $bcList = SectionLabels::breadcrumbsForSection(trim($sectionPath, '/'));
        $bcList[] = ['label' => $title, 'href' => $canonicalPath];

        $bc = HtmlComponents::breadcrumbs($cfg, $bcList);

        $slot = $bc . '<article class="article prose-flow"><header class="article-head"><h1>' . Html::e($title) . '</h1></header><div class="prose">' . $htmlBody . '</div></article>';

        $articleUrl = Seo::absoluteUrl($cfg, $canonicalPath);
        $itemMtime = ContentRepository::itemMtime($item);
        $articleNode = array_filter([
            '@type' => 'Article',
            '@id' => $articleUrl . '#article',
            'headline' => $title,
            'description' => $desc,
            'inLanguage' => 'ru-RU',
            'url' => $articleUrl,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $articleUrl],
            'image' => [Seo::absoluteUrl($cfg, Seo::DEFAULT_OG_IMAGE_PATH)],
            'author' => ['@id' => Seo::siteUrl($cfg) . '/#editorial-team'],
            'publisher' => ['@id' => Seo::siteUrl($cfg) . '/#organization'],
            'dateModified' => $itemMtime > 0 ? gmdate('c', $itemMtime) : null,
            'about' => Seo::genshinVideoGameNode(),
        ], static fn ($v) => $v !== null && $v !== '');

        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, $bcList),
            $articleNode,
            Seo::editorialTeamPerson($cfg),
        ]);

        return [
            'pageTitle' => $title . ' — GenshinTop',
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
            'lastModifiedTs' => $itemMtime ?: null,
        ];
    }
}

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
<section class="callout"><h2>Топ-ап</h2><p><a href="/lootbar/kristally-sotvoreniya">Кристаллы Сотворения</a>, <a href="/lootbar/blagoslovenie-luny">Благословение Полой Луны</a>, <a href="/lootbar/promokod">промокод</a>, <a href="/lootbar/bezopasnost-i-oplata">безопасность</a>.</p></section>
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
<section class="hero">
  <p class="hero-kicker">Гайды · билды · баннеры</p>
  <h1 class="hero-title">Твой <span class="text-gradient-gold">Teyvat</span> без лишней суеты</h1>
  <p class="hero-lead">Персонажи, свежие гайды и коды — меньше гуглить, больше играть. Пополнение — <a href="/lootbar" class="link-mint" data-reach-goal="lootbar_home_hero_link">раздел LootBar</a>.</p>
  <div class="hero-actions"><a class="btn btn-primary" href="/characters">К персонажам</a><a class="btn btn-secondary" href="/guides">К гайдам</a></div>
</section>
<section class="section"><h2>Персонажи</h2><div class="grid-cards">{$cardsC}</div><p class="section-more"><a href="/characters">Все персонажи →</a></p></section>
<section class="section"><h2>Свежие гайды</h2><div class="grid-guides">{$cardsG}</div><p class="section-more"><a href="/guides">Все гайды →</a></p></section>
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
            'slot' => $slot,
            'jsonLd' => $jsonLd,
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
            $slug = (string) ($g['slug'] ?? '');
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $g['title'] ?? $slug,
                'item' => Seo::absoluteUrl($cfg, '/guides/' . $slug),
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

        return [
            'pageTitle' => 'Гайды Genshin Impact',
            'pageDescription' => 'Гайды по Genshin Impact: баннеры, патчи, промокоды, тир-листы.',
            'canonicalPath' => '/guides',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
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

        $items = [];
        foreach (array_slice($guides, 0, 48) as $i => $g) {
            $slug = (string) ($g['slug'] ?? '');
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $g['title'] ?? $slug,
                'item' => Seo::absoluteUrl($cfg, '/guides/' . $slug),
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
                    'numberOfItems' => count($guides),
                    'itemListElement' => $items,
                ],
            ],
        ]);

        return [
            'pageTitle' => $hubDef['title'],
            'pageDescription' => $desc,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function guideArticle(array $cfg, array $g): array
    {
        $slug = (string) ($g['slug'] ?? '');
        $canonicalPath = '/guides/' . $slug;
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
        if (!empty($meta['sources']) && is_array($meta['sources'])) {
            $sourcesHtml = '<section class="sources"><h2>Источники</h2><ul>';
            foreach ($meta['sources'] as $s) {
                if (!is_string($s) || $s === '') {
                    continue;
                }
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
  <p><a href="/guides/banners">Баннеры</a> · <a href="/guides/patches">Патчи</a> · <a href="/guides/tier-list">Тир-листы</a> · <a href="/guides/newbie">Новичкам</a> · <a href="/guides/codes">Промокоды</a></p>
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

        return [
            'pageTitle' => 'Персонажи Genshin Impact',
            'pageDescription' => 'Каталог персонажей Genshin Impact: стихия, оружие, редкость и ссылки на материалы.',
            'canonicalPath' => '/characters',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
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

        $listLd = [];
        foreach (array_slice($items, 0, 60) as $i => $c) {
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
                    'numberOfItems' => count($items),
                    'itemListElement' => $listLd,
                ],
            ],
        ]);

        return [
            'pageTitle' => $title,
            'pageDescription' => $description,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
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
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'Персонажи', 'href' => '/characters'],
                ['label' => $name, 'href' => $canonicalPath],
            ]),
            [
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
                'about' => [
                    '@type' => 'Thing',
                    'name' => $name,
                    'description' => self::elementRu($element) . ', ' . $weapon . '. Персонаж Genshin Impact.',
                ],
            ],
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
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function regionsIndex(array $cfg): array
    {
        $defs = regions_definitions();
        $cards = '';
        foreach ($defs as $d) {
            $slug = (string) ($d['slug'] ?? '');
            $name = (string) ($d['name'] ?? $slug);
            $cards .= '<a class="card region-card" href="/regions/' . Html::e($slug) . '"><div class="card-body"><h2 class="card-title">' . Html::e($name) . '</h2><p class="muted">' . Html::e((string) ($d['patchRange'] ?? '')) . '</p></div></a>';
        }
        $bc = HtmlComponents::breadcrumbs($cfg, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Регионы Тейвата', 'href' => '/regions'],
        ]);
        $slot = $bc . '<article class="article catalog-page"><h1>Регионы Тейвата</h1><p class="lead">Обзоры ключевых регионов Genshin Impact.</p><div class="grid-cards">' . $cards . '</div></article>';

        $items = [];
        $i = 1;
        foreach ($defs as $d) {
            $slug = (string) ($d['slug'] ?? '');
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $d['name'] ?? $slug,
                'item' => Seo::absoluteUrl($cfg, '/regions/' . $slug),
            ];
        }
        $url = Seo::absoluteUrl($cfg, '/regions');
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            [
                '@type' => 'CollectionPage',
                '@id' => $url . '#webpage',
                'name' => 'Регионы Тейвата',
                'url' => $url,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($defs),
                    'itemListElement' => $items,
                ],
            ],
        ]);

        return [
            'pageTitle' => 'Регионы Тейвата — Genshin Impact',
            'pageDescription' => 'Обзоры регионов Genshin Impact: Сумеру, Фонтейн, Натлан.',
            'canonicalPath' => '/regions',
            'slot' => $slot,
            'jsonLd' => $jsonLd,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function regionPage(array $cfg, array $def): array
    {
        $slug = (string) ($def['slug'] ?? '');
        $name = (string) ($def['name'] ?? $slug);
        $canonicalPath = '/regions/' . $slug;
        $description = (string) ($def['description'] ?? '');
        $introduction = (string) ($def['introduction'] ?? '');
        $nativeList = [];
        if (!empty($def['nativeNames']) && is_array($def['nativeNames'])) {
            foreach ($def['nativeNames'] as $nn) {
                if (is_string($nn) && $nn !== '') {
                    $nativeList[] = $nn;
                }
            }
        }
        $nativeSet = array_fill_keys($nativeList, true);
        $regionChars = ContentRepository::filterCharacters(fn ($ch) => isset($nativeSet[(string) ($ch['name'] ?? '')]));
        $charCards = implode('', array_map(fn ($ch) => HtmlComponents::characterCard($ch), $regionChars));

        $mechanicsHtml = '';
        if (!empty($def['keyMechanics']) && is_array($def['keyMechanics'])) {
            $mechanicsHtml = '<section><h2>Ключевые механики региона</h2><ul>';
            foreach ($def['keyMechanics'] as $m) {
                if (is_string($m) && $m !== '') {
                    $mechanicsHtml .= '<li>' . Html::e($m) . '</li>';
                }
            }
            $mechanicsHtml .= '</ul></section>';
        }

        $sectionsHtml = '';
        if (!empty($def['sections']) && is_array($def['sections'])) {
            foreach ($def['sections'] as $sec) {
                if (!is_array($sec)) {
                    continue;
                }
                $h = isset($sec['heading']) && is_string($sec['heading']) ? $sec['heading'] : '';
                $b = isset($sec['body']) && is_string($sec['body']) ? $sec['body'] : '';
                if ($h === '' && $b === '') {
                    continue;
                }
                $sectionsHtml .= '<section><h2>' . Html::e($h) . '</h2><p>' . Html::e($b) . '</p></section>';
            }
        }

        $bc = HtmlComponents::breadcrumbs($cfg, [
            ['label' => 'Главная', 'href' => '/'],
            ['label' => 'Регионы Тейвата', 'href' => '/regions'],
            ['label' => $name, 'href' => $canonicalPath],
        ]);

        $slot = $bc . '<article class="article prose-flow region-article">'
            . '<header class="article-head"><p class="muted">' . Html::e((string) ($def['element'] ?? '')) . ' · Архонт: ' . Html::e((string) ($def['archon'] ?? ''))
            . ' · ' . Html::e((string) ($def['patchRange'] ?? '')) . '</p>'
            . '<h1>' . Html::e($name) . '</h1><p class="lead">' . Html::e($introduction) . '</p></header>'
            . $mechanicsHtml . $sectionsHtml
            . ($charCards !== '' ? '<section><h2>Персонажи региона</h2><div class="grid-cards">' . $charCards . '</div></section>' : '')
            . '</article>';

        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'Регионы Тейвата', 'href' => '/regions'],
                ['label' => $name, 'href' => $canonicalPath],
            ]),
            [
                '@type' => 'Place',
                '@id' => $url . '#place',
                'name' => $name . ' (Genshin Impact)',
                'description' => $description,
                'url' => $url,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
                'additionalType' => 'Region',
            ],
            [
                '@type' => 'Article',
                '@id' => $url . '#article',
                'headline' => $name . ' — регион Genshin Impact',
                'description' => $description,
                'inLanguage' => 'ru-RU',
                'url' => $url,
                'author' => ['@id' => Seo::siteUrl($cfg) . '/#editorial-team'],
                'publisher' => ['@id' => Seo::siteUrl($cfg) . '/#organization'],
            ],
        ]);

        return [
            'pageTitle' => $name . ' — регион Genshin Impact: персонажи, квесты, механики',
            'pageDescription' => $description,
            'canonicalPath' => $canonicalPath,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function lootbarIndex(array $cfg): array
    {
        $topupUrl = Partners::lootbarGenshinTopupUrl('lootbar_hero');
        $howTo = Seo::howToSchema([
            'name' => 'Как получить и применить купон на LootBar.gg для Genshin Impact',
            'description' => 'Общий порядок действий для скидок на стороннем топ-апе.',
            'steps' => LootbarConfig::howToStepTexts(),
        ]);
        $faqs = [
            ['question' => 'Как купить Genshin Impact дешевле через LootBar?', 'answer' => 'Откройте страницу Genshin Impact на LootBar.gg по партнёрской ссылке, выберите пакет и оплатите. Условия зависят от региона и акций сервиса.'],
            ['question' => 'LootBar.gg — официальный магазин HoYoverse?', 'answer' => 'Нет. LootBar.gg — сторонний сервис пополнения. GenshinTop — неофициальный фан-сайт.'],
            ['question' => 'Безопасно ли пополнение через LootBar?', 'answer' => 'Обычно используется UID без пароля; проверяйте домен lootbar.gg и сохраняйте чек. Подробнее — /lootbar/bezopasnost-i-oplata.'],
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
        $slot = <<<HTML
<article class="article prose-flow lootbar-hub">
<p class="muted"><a href="/partnership-disclosure">Раскрытие партнёрства</a></p>
<h1>Пополнение Genshin Impact через LootBar.gg</h1>
<p class="lead">Партнёрский раздел: внешние ссылки помечены как <code>rel=sponsored</code>. Цены и условия — на стороне LootBar.</p>
<p><a class="btn btn-lootbar" href="{$outUrl}" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_hub_cta">Перейти к топ-апу на LootBar.gg</a></p>
<section class="callout">
  <h2>Подстраницы</h2>
  <ul class="link-list">
    <li><a href="/lootbar/kak-popolnit-genshin-impact">Как пополнить</a></li>
    <li><a href="/lootbar/promokod">Промокод</a></li>
    <li><a href="/lootbar/kristally-sotvoreniya">Кристаллы Сотворения</a></li>
    <li><a href="/lootbar/blagoslovenie-luny">Благословение Полой Луны</a></li>
    <li><a href="/lootbar/bezopasnost-i-oplata">Безопасность и оплата</a></li>
  </ul>
</section>
<section>
  <h2>FAQ</h2>
  <dl class="faq-dl">
    <dt>Как купить дешевле через LootBar?</dt><dd>Используйте партнёрскую ссылку и проверьте купоны на сайте сервиса перед оплатой.</dd>
    <dt>Это официальный магазин?</dt><dd>Нет; официальные покупки — в клиенте и у HoYoverse.</dd>
  </dl>
</section>
</article>
HTML;

        return [
            'pageTitle' => 'Пополнение Genshin Impact через LootBar.gg — GenshinTop',
            'pageDescription' => 'Партнёрский топ-ап Genshin Impact на LootBar.gg: безопасность, промокоды, кристаллы и луна.',
            'canonicalPath' => '/lootbar',
            'hideLootBarPromo' => true,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
        ];
    }

    /** @return array<string,mixed>|null */
    public static function lootbarSubpage(array $cfg, string $slug): ?array
    {
        $pages = [
            'kak-popolnit-genshin-impact' => [
                'title' => 'Как пополнить Genshin Impact через LootBar.gg — пошаговый топ-ап',
                'description' => 'Пошаговая инструкция пополнения через LootBar.gg и применения промокода.',
                'bodyIntro' => 'Общая последовательность для стороннего топ-апа. Поля на LootBar могут меняться.',
                'utm' => 'lootbar_howto',
            ],
            'promokod' => [
                'title' => 'Промокод LootBar для Genshin Impact — купоны и скидки',
                'description' => 'Где смотреть промокод LootBar и чем он отличается от кодов HoYoverse.',
                'bodyIntro' => 'Магазинный купон LootBar применяется при оплате на lootbar.gg.',
                'utm' => 'lootbar_promo',
            ],
            'kristally-sotvoreniya' => [
                'title' => 'Кристаллы Сотворения Genshin Impact на LootBar.gg',
                'description' => 'Что такое Кристаллы Сотворения и как купить пакеты через партнёрскую витрину.',
                'bodyIntro' => 'Genesis Crystals конвертируются в Камни Истока в игре. Актуальные пакеты смотрите на LootBar.',
                'utm' => 'lootbar_crystals',
            ],
            'blagoslovenie-luny' => [
                'title' => 'Благословение Полой Луны (Welkin) — Genshin Impact на LootBar',
                'description' => 'Что даёт Welkin Moon и когда это выгоднее пакетов кристаллов.',
                'bodyIntro' => 'Месячная подписка: часть наград приходит ежедневно — не забывайте заходить в игру.',
                'utm' => 'lootbar_welkin',
            ],
            'bezopasnost-i-oplata' => [
                'title' => 'Безопасность пополнения Genshin Impact и оплата',
                'description' => 'Риски стороннего топ-апа, проверка домена и куда обращаться при спорах.',
                'bodyIntro' => 'Не передавайте пароль от аккаунта сторонним сервисам; сохраняйте чеки заказов.',
                'utm' => 'lootbar_safety',
            ],
        ];
        if (!isset($pages[$slug])) {
            return null;
        }
        $p = $pages[$slug];
        $canonicalPath = '/lootbar/' . $slug;
        $topupUrl = Partners::lootbarGenshinTopupUrl($p['utm']);
        $outUrl = Html::e($topupUrl);

        $slot = '<article class="article prose-flow">'
            . '<p class="back-link"><a href="/lootbar">← Хаб LootBar</a></p>'
            . '<h1>' . Html::e($p['title']) . '</h1>'
            . '<p class="lead">' . Html::e($p['bodyIntro']) . '</p>'
            . '<p class="muted"><a href="/partnership-disclosure">Раскрытие партнёрства</a></p>'
            . '<p><a class="btn btn-lootbar" href="' . $outUrl . '" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_sub_cta">Открыть LootBar.gg</a></p>'
            . '</article>';

        $url = Seo::absoluteUrl($cfg, $canonicalPath);
        $jsonLd = Seo::jsonLdGraph([
            Seo::publisherOrganization($cfg),
            Seo::webSiteNode($cfg),
            Seo::breadcrumbListSchema($cfg, [
                ['label' => 'Главная', 'href' => '/'],
                ['label' => 'LootBar', 'href' => '/lootbar'],
                ['label' => $p['title'], 'href' => $canonicalPath],
            ]),
            [
                '@type' => 'WebPage',
                '@id' => $url . '#webpage',
                'name' => $p['title'],
                'url' => $url,
                'isPartOf' => ['@id' => Seo::siteUrl($cfg) . '/#website'],
            ],
        ]);

        return [
            'pageTitle' => $p['title'],
            'pageDescription' => $p['description'],
            'canonicalPath' => $canonicalPath,
            'hideLootBarPromo' => true,
            'slot' => $slot,
            'jsonLd' => $jsonLd,
        ];
    }
}

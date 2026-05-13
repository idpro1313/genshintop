<?php

declare(strict_types=1);

final class HtmlComponents
{
    private const CATEGORY_LABELS = [
        'banner' => 'Баннер',
        'patch' => 'Обновление',
        'newbie' => 'Новичкам',
        'codes' => 'Промокоды',
        'tier' => 'Тир-лист',
        'hardware' => 'Железо / ПК',
        'general' => 'Гайд',
    ];

    /** Rich card for /guides catalog (client filters). @param array<string,mixed> $g */
    public static function guideCatalogCard(array $g): string
    {
        $slug = (string) ($g['slug'] ?? '');
        $title = (string) ($g['title'] ?? '');
        $category = (string) ($g['category'] ?? 'general');
        $summary = isset($g['summary']) && is_string($g['summary']) ? $g['summary'] : '';
        $meta = is_array($g['meta'] ?? null) ? $g['meta'] : [];
        $fileHint = $slug . '.md';
        $topic = GuideTaxonomy::effectiveTopic($meta, $fileHint, $summary);
        $status = GuideTaxonomy::effectiveStatus($meta, $slug, $summary);
        $audience = GuideTaxonomy::effectiveAudience($meta, $summary);
        $gv = GuideTaxonomy::effectiveGameVersion($meta, $slug, $summary);
        $labelsTop = GuideTaxonomy::topicLabelsRu();
        $labelsSt = GuideTaxonomy::statusLabelsRu();
        $labelsAu = [
            'all' => 'Всем',
            'beginner' => 'Новичкам',
            'returning' => 'Вернувшимся',
            'meta' => 'Мета / бездна',
        ];
        $catLabel = Html::e(self::CATEGORY_LABELS[$category] ?? $category);
        $topicLabel = Html::e($labelsTop[$topic] ?? $topic);
        $statusLabel = Html::e($labelsSt[$status] ?? $status);
        $audienceLabel = Html::e($labelsAu[$audience] ?? $audience);
        $gvEsc = $gv ? Html::e('v' . $gv) : '';
        $haystackRaw = strtolower($title . ' ' . Seo::stripDescriptionNoise($summary));
        $haystackEsc = Html::e($haystackRaw);
        $topicEsc = Html::e($topic);
        $statusEsc = Html::e($status);

        $excerpt = Seo::cleanMetaDescription($summary, $title, 220);
        $excerptEsc = Html::e($excerpt);
        $titleEsc = Html::e($title);
        $slugEsc = Html::e($slug);

        $badges = '<span class="pill pill-mint">' . $catLabel . '</span>';
        $badges .= '<span class="pill">' . $topicLabel . '</span>';
        $badges .= '<span class="pill pill-muted">' . $statusLabel . '</span>';
        if ($audience !== 'all') {
            $badges .= '<span class="pill pill-muted">' . $audienceLabel . '</span>';
        }
        if ($gv) {
            $badges .= '<span class="pill pill-gold">' . $gvEsc . '</span>';
        }

        $timeHtml = '';
        $displayTs = null;
        foreach (['updatedAt', 'date'] as $dk) {
            if (!empty($meta[$dk]) && is_string($meta[$dk])) {
                $t = strtotime($meta[$dk]);
                if ($t !== false) {
                    $displayTs = $t;
                    break;
                }
            }
        }
        if ($displayTs) {
            $iso = gmdate('c', $displayTs);
            $ru = date('d.m.Y', $displayTs);
            $timeHtml = '<time class="guide-card-time" datetime="' . Html::e($iso) . '">' . Html::e($ru) . '</time>';
        }

        $excerptBlock = $excerpt !== '' ? '<p class="guide-card-excerpt">' . $excerptEsc . '</p>' : '';

        return <<<HTML
<a href="/guides/{$slugEsc}" class="guide-catalog-card" data-guide-card data-category="{$category}" data-topic="{$topicEsc}" data-status="{$statusEsc}" data-search-haystack="{$haystackEsc}">
  <div class="guide-card-badges">{$badges}{$timeHtml}</div>
  <h2 class="guide-card-title">{$titleEsc}</h2>
  {$excerptBlock}
</a>
HTML;
    }

    /** @param array<string,mixed> $g */
    public static function guideCard(array $g): string
    {
        return self::guideCatalogCard($g);
    }

    /** @param array<string,mixed> $c */
    public static function characterCard(array $c): string
    {
        $slugRaw = (string) ($c['slug'] ?? '');
        $nameRaw = (string) ($c['name'] ?? '');
        $elementEn = (string) ($c['element'] ?? 'Anemo');
        $weaponRaw = (string) ($c['weapon'] ?? '');
        $rarity = isset($c['rarity']) && is_numeric($c['rarity']) ? (string) (int) $c['rarity'] : '';
        $slug = Html::e($slugRaw);
        $nameEsc = Html::e($nameRaw);
        $weapon = Html::e($weaponRaw);
        $elementAttr = Html::e($elementEn);
        $ruEl = [
            'Pyro' => 'Пиро', 'Hydro' => 'Гидро', 'Electro' => 'Электро', 'Cryo' => 'Крио',
            'Anemo' => 'Анемо', 'Geo' => 'Гео', 'Dendro' => 'Дендро',
        ];
        $elementLabel = Html::e($ruEl[$elementEn] ?? $elementEn);
        $nameSearch = Html::e(mb_strtolower($nameRaw));
        $rarityAttr = Html::e($rarity);
        $elKey = strtolower($elementEn);
        $stars = '';
        if ($rarity !== '') {
            $n = (int) $rarity;
            $stars = '<span class="char-rarity">' . str_repeat('★', max(0, min(5, $n))) . '</span>';
        }
        return <<<HTML
<a class="card card-character accent-{$elKey}" href="/characters/{$slug}" data-character-card data-element="{$elementAttr}" data-weapon="{$weapon}" data-rarity="{$rarityAttr}" data-name="{$nameSearch}">
  <div class="card-body">
    <div class="char-meta"><span class="pill pill-element {$elKey}">{$elementLabel}</span>{$stars}<span class="pill pill-muted">{$weapon}</span></div>
    <h3 class="card-title">{$nameEsc}</h3>
  </div>
</a>
HTML;
    }

    /** @param list<string>|null $slugs */
    public static function guideBadgeLinks(?array $slugs): string
    {
        if ($slugs === null || $slugs === []) {
            return '';
        }
        $html = '<div class="related-strip">';
        foreach ($slugs as $s) {
            if (!is_string($s) || $s === '') {
                continue;
            }
            $slug = Html::e($s);
            $html .= '<a class="pill-link" href="/guides/' . $slug . '">' . $slug . '</a>';
        }
        $html .= '</div>';

        return $html;
    }

    /** @param list<string>|null $slugs */
    public static function characterBadgeLinks(?array $slugs): string
    {
        if ($slugs === null || $slugs === []) {
            return '';
        }
        $html = '<div class="related-strip">';
        foreach ($slugs as $s) {
            if (!is_string($s) || $s === '') {
                continue;
            }
            $slug = Html::e($s);
            $html .= '<a class="pill-link" href="/characters/' . $slug . '">' . $slug . '</a>';
        }
        $html .= '</div>';

        return $html;
    }

    public static function breadcrumbs(array $cfg, array $items): string
    {
        $html = '<nav class="breadcrumbs" aria-label="Хлебные крошки"><ol>';
        foreach ($items as $i => $it) {
            $last = $i === count($items) - 1;
            $label = Html::e($it['label']);
            if ($last) {
                $html .= '<li aria-current="page">' . $label . '</li>';
            } else {
                $href = Html::e($it['href']);
                $html .= '<li><a href="' . $href . '">' . $label . '</a></li>';
            }
        }
        $html .= '</ol></nav>';

        return $html;
    }
}

<?php

declare(strict_types=1);

/**
 * Global variables expected before include:
 * @var array<string,mixed> $cfg
 * @var string $pageTitle page title (without suffix unless contains site name)
 * @var string $pageDescription
 * @var string $canonicalPath
 * @var string $robots
 * @var string $ogType website|article
 * @var string|null $jsonLdRaw JSON string for ld+json or null
 * @var string $ogImage path /foo.png
 * @var string $ogAlt
 * @var array{publishedTime?:string,modifiedTime?:string}|null $articleTimes
 * @var bool $hideLootBarPromo
 * @var string $slot inner HTML for main
 */
$site = Seo::siteUrl($cfg);
$pageTitleRaw = $pageTitle;
$fullTitle = str_contains($pageTitleRaw, 'GenshinTop') ? $pageTitleRaw : ($pageTitleRaw . ' | GenshinTop');
$canonical = Seo::absoluteUrl($cfg, $canonicalPath);
$imagePath = $ogImage ?? Seo::DEFAULT_OG_IMAGE_PATH;
$imageUrl = Seo::absoluteUrl($cfg, $imagePath);
$imageMime = str_ends_with($imagePath, '.svg')
    ? 'image/svg+xml'
    : (str_ends_with($imagePath, '.jpg') || str_ends_with($imagePath, '.jpeg')
        ? 'image/jpeg'
        : 'image/png');
$imageAlt = $ogAlt ?? ($pageTitleRaw . ' — GenshinTop');
$ymId = (int) ($cfg['yandex_metrika_id'] ?? 109020836);
$verification = $cfg['meta_verification'] ?? [];
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#1a1a2e" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&amp;family=Onest:wght@400;500;600;700&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&amp;family=Onest:wght@400;500;600;700&amp;display=swap" media="print" onload="this.media='all'" />
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&amp;family=Onest:wght@400;500;600;700&amp;display=swap" /></noscript>
  <title><?= Html::e($fullTitle) ?></title>
  <meta name="description" content="<?= Html::e($pageDescription) ?>" />
  <meta name="robots" content="<?= Html::e($robots) ?>" />
  <link rel="canonical" href="<?= Html::e($canonical) ?>" />
<?php if (!empty($verification['yandex'])): ?>
  <meta name="yandex-verification" content="<?= Html::e((string) $verification['yandex']) ?>" />
<?php endif; ?>
<?php if (!empty($verification['google'])): ?>
  <meta name="google-site-verification" content="<?= Html::e((string) $verification['google']) ?>" />
<?php endif; ?>
<?php if (!empty($verification['mailru'])): ?>
  <meta name="mailru-domain" content="<?= Html::e((string) $verification['mailru']) ?>" />
<?php endif; ?>
  <meta property="og:title" content="<?= Html::e($fullTitle) ?>" />
  <meta property="og:description" content="<?= Html::e($pageDescription) ?>" />
  <meta property="og:url" content="<?= Html::e($canonical) ?>" />
  <meta property="og:type" content="<?= Html::e($ogType) ?>" />
  <meta property="og:site_name" content="GenshinTop" />
  <meta property="og:locale" content="ru_RU" />
  <meta property="og:image" content="<?= Html::e($imageUrl) ?>" />
  <meta property="og:image:secure_url" content="<?= Html::e($imageUrl) ?>" />
  <meta property="og:image:type" content="<?= Html::e($imageMime) ?>" />
  <meta property="og:image:width" content="<?= (string) Seo::OG_W ?>" />
  <meta property="og:image:height" content="<?= (string) Seo::OG_H ?>" />
  <meta property="og:image:alt" content="<?= Html::e($imageAlt) ?>" />
<?php if ($ogType === 'article' && !empty($articleTimes['publishedTime'])): ?>
  <meta property="article:published_time" content="<?= Html::e((string) $articleTimes['publishedTime']) ?>" />
<?php endif; ?>
<?php if ($ogType === 'article' && !empty($articleTimes['modifiedTime'])): ?>
  <meta property="article:modified_time" content="<?= Html::e((string) $articleTimes['modifiedTime']) ?>" />
<?php endif; ?>
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= Html::e($fullTitle) ?>" />
  <meta name="twitter:description" content="<?= Html::e($pageDescription) ?>" />
  <meta name="twitter:image" content="<?= Html::e($imageUrl) ?>" />
  <meta name="twitter:image:alt" content="<?= Html::e($imageAlt) ?>" />
<?php if ($jsonLdRaw): ?>
  <script type="application/ld+json"><?= $jsonLdRaw ?></script>
<?php endif; ?>
  <link rel="stylesheet" href="/css/site.css" />
  <script type="text/javascript">
(function(){
var ymId=<?= json_encode($ymId, JSON_THROW_ON_ERROR) ?>;
window.ym=window.ym||function(){(window.ym.a=window.ym.a||[]).push(arguments);};window.ym.l=1*new Date();
function loadMetrika(){if(window.__ymLoaded)return;window.__ymLoaded=true;var src='https://mc.yandex.ru/metrika/tag.js?id='+ymId;
for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===src)return;}
var k=document.createElement('script');k.async=1;k.src=src;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(k,a);
window.ym(ymId,'init',{ssr:true,webvisor:true,clickmap:true,ecommerce:'dataLayer',referrer:document.referrer,url:location.href,accurateTrackBounce:true,trackLinks:true});}
if('requestIdleCallback'in window){requestIdleCallback(loadMetrika,{timeout:4000});}
else if(document.readyState==='complete'){setTimeout(loadMetrika,1500);}
else{window.addEventListener('load',function(){setTimeout(loadMetrika,1500);});}
})();
  </script>
</head>
<body>
<noscript><div><img src="https://mc.yandex.ru/watch/<?= (int) $ymId ?>" style="position:absolute;left:-9999px" alt="" /></div></noscript>
<?php require SITE_ROOT . '/lib/header.php'; ?>
<?php if (!$hideLootBarPromo): ?>
<?php require SITE_ROOT . '/lib/lootbar_banner.php'; ?>
<?php endif; ?>
<main class="site-main">
  <?= $slot ?>
</main>
<?php require SITE_ROOT . '/lib/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-reach-goal]').forEach((el) => {
    el.addEventListener('click', () => {
      var g = el.getAttribute('data-reach-goal');
      if (!g) return;
      try { if (typeof ym !== 'undefined') ym(<?= (int) $ymId ?>, 'reachGoal', g); } catch (_) {}
    });
  });
});
</script>
</body>
</html>

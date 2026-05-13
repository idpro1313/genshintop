<?php

declare(strict_types=1);

/** @var array<string,mixed> $cfg */
$top = Partners::lootbarGenshinTopupUrl('lootbar_banner');
?>
<div class="lootbar-banner">
  <div class="lootbar-banner-wrap">
    <div class="lootbar-banner-card">
      <div class="lootbar-banner-icon-wrap" aria-hidden="true">
        <span class="lootbar-banner-icon">💰</span>
      </div>
      <div class="lootbar-banner-copy">
        <strong class="lootbar-banner-title">Скидки до 32% на топ-ап Genshin Impact!</strong>
        <p class="lootbar-banner-sub">
          Купоны 6% и 10% для новых пользователей LootBar.
          <a href="/lootbar" class="lootbar-banner-inline-link" data-reach-goal="lootbar_banner_hub_link">Раздел на GenshinTop</a>
        </p>
      </div>
      <a href="<?= Html::e($top) ?>" class="lootbar-banner-cta" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_banner_click">Подробнее</a>
    </div>
  </div>
</div>

<?php

declare(strict_types=1);

/** @var array<string,mixed> $cfg */
$top = Partners::lootbarGenshinTopupUrl('lootbar_banner');
?>
<div class="lootbar-banner">
  <div class="lootbar-banner-inner">
    <span class="lootbar-banner-icon" aria-hidden="true">💎</span>
    <div class="lootbar-banner-copy">
      <span class="lootbar-banner-badge">LootBar.gg</span>
      <p class="lootbar-banner-text">
        Скидки и промо на топ-ап Genshin Impact — партнёрский раздел.
        <a href="/lootbar" class="lootbar-banner-link">Подробнее на GenshinTop</a>
      </p>
    </div>
    <a href="<?= Html::e($top) ?>" class="lootbar-banner-cta" rel="noopener noreferrer sponsored" target="_blank" data-reach-goal="lootbar_banner_click">Получить скидку →</a>
  </div>
</div>

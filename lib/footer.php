<?php

declare(strict_types=1);

$verPath = SITE_ROOT . DIRECTORY_SEPARATOR . 'VERSION';
$version = is_readable($verPath) ? trim((string) file_get_contents($verPath)) : '';
?>
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-col">
      <div class="footer-brand">GenshinTop</div>
      <p class="footer-muted">Неофициальные гайды и справка по Genshin Impact.</p>
      <?php if ($version !== '') : ?>
        <p class="footer-version">Версия <?= Html::e($version) ?></p>
      <?php endif; ?>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Гайды</div>
      <ul class="footer-links">
        <li><a href="/guides/banners">Баннеры</a></li>
        <li><a href="/guides/patches">Патчи</a></li>
        <li><a href="/guides/codes">Промокоды</a></li>
        <li><a href="/guides/tier-list">Тир-листы</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Персонажи</div>
      <ul class="footer-links">
        <li><a href="/characters/pyro">Пиро</a></li>
        <li><a href="/characters/hydro">Гидро</a></li>
        <li><a href="/characters/dendro">Дендро</a></li>
        <li><a href="/characters">Все</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Инфо</div>
      <ul class="footer-links">
        <li><a href="/regions">Регионы</a></li>
        <li><a href="/lootbar">Пополнение</a></li>
        <li><a href="/editorial-policy">Редакция</a></li>
        <li><a href="/partnership-disclosure">Партнёрство</a></li>
      </ul>
    </div>
  </div>
</footer>

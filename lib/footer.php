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
      <div class="footer-heading">Каталоги</div>
      <ul class="footer-links">
        <li><a href="/characters">Персонажи</a></li>
        <li><a href="/guides">Гайды</a></li>
        <li><a href="/weapons">Оружие</a></li>
        <li><a href="/artifacts">Артефакты</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Мир</div>
      <ul class="footer-links">
        <li><a href="/world">Мир и лор</a></li>
        <li><a href="/world/regions">Регионы</a></li>
        <li><a href="/world/factions">Фракции</a></li>
        <li><a href="/world/lore">Лор</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Новости</div>
      <ul class="footer-links">
        <li><a href="/news">Новости</a></li>
        <li><a href="/news/events">События</a></li>
        <li><a href="/news/announcements">Анонсы</a></li>
        <li><a href="/news/banners">Баннеры</a></li>
        <li><a href="/news/patches">Патчи</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <div class="footer-heading">Инфо</div>
      <ul class="footer-links">
        <li><a href="/tools">Инструменты</a></li>
        <li><a href="/community">Сообщество</a></li>
        <li><a href="/lootbar">Пополнение</a></li>
        <li><a href="/editorial-policy">Редакция</a></li>
        <li><a href="/partnership-disclosure">Партнёрство</a></li>
      </ul>
    </div>
  </div>
</footer>

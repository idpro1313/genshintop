<?php

declare(strict_types=1);

/** @var array<string,mixed> $cfg */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = $path === null || $path === '' ? '/' : $path;
$nav = [
    ['href' => '/', 'label' => 'Главная'],
    ['href' => '/characters', 'label' => 'Персонажи'],
    ['href' => '/guides', 'label' => 'Гайды'],
    ['href' => '/weapons', 'label' => 'Оружие'],
    ['href' => '/artifacts', 'label' => 'Артефакты'],
    ['href' => '/world', 'label' => 'Мир'],
    ['href' => '/news', 'label' => 'Новости'],
    ['href' => '/tools', 'label' => 'Инструменты'],
    ['href' => '/lootbar', 'label' => 'Пополнение'],
];
?>
<header class="site-header">
  <div class="site-header-inner">
    <a href="/" class="site-logo">
      <span class="site-logo-title">GenshinTop</span>
      <span class="site-logo-domain">genshintop.ru</span>
    </a>
    <nav class="site-nav" aria-label="Основное меню">
      <?php foreach ($nav as $item) :
          $href = $item['href'];
          $active = $path === $href || ($href !== '/' && str_starts_with($path, $href . '/'));
          ?>
        <a href="<?= Html::e($href) ?>" class="site-nav-link<?= $active ? ' is-active' : '' ?>"><?= Html::e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>

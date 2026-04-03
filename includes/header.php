<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $pageDescription */

?>
<!DOCTYPE html>
<html lang="bg" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= portfolio_h($pageTitle) ?></title>
  <meta name="description" content="<?= portfolio_h($pageDescription) ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/portfolio.css" />
</head>
<body data-projects-view="cards">
  <header class="site-header">
    <div class="container nav">
      <a class="brand brand-home" href="#top">
        <span class="brand-mark" aria-hidden="true"></span>
        <span><?= portfolio_h($profile['name']) ?></span>
      </a>
      <nav class="nav-links" aria-label="Основна навигация">
        <a href="#about">За мен</a>
        <?php if (! empty($dogGalleryImages)) : ?>
          <a href="#dogs">Кучета</a>
        <?php endif; ?>
        <a href="#erp">BioMarket ERP</a>
        <a href="#projects">Проекти</a>
        <a href="#contact">Контакти</a>
      </nav>
      <div class="toolbar">
        <div class="toggle-group" role="group" aria-label="Изглед проекти">
          <button type="button" data-projects-view="cards" aria-pressed="true">Карти</button>
          <button type="button" data-projects-view="table" aria-pressed="false">Таблица</button>
        </div>
        <button type="button" class="icon-btn" data-theme-toggle aria-label="Смяна на тема">☀</button>
      </div>
    </div>
  </header>
  <main id="top">

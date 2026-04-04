<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $pageDescription */
/** @var string $canonicalUrl */
/** @var string|null $ogImageUrl */
/** @var string $metaKeywords */
/** @var string $jsonLd */

$portfolioCssPath = __DIR__ . '/../assets/css/portfolio.css';
$portfolioCssV = is_readable($portfolioCssPath) ? (string) filemtime($portfolioCssPath) : '1';

?>
<!DOCTYPE html>
<html lang="bg" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= portfolio_h($pageTitle) ?></title>
  <meta name="description" content="<?= portfolio_h($pageDescription) ?>" />
  <link rel="canonical" href="<?= portfolio_h($canonicalUrl) ?>" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="googlebot" content="index, follow" />
  <meta name="keywords" content="<?= portfolio_h($metaKeywords) ?>" />
  <meta name="author" content="<?= portfolio_h($profile['name']) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="<?= portfolio_h($profile['name']) ?>" />
  <meta property="og:title" content="<?= portfolio_h($pageTitle) ?>" />
  <meta property="og:description" content="<?= portfolio_h($pageDescription) ?>" />
  <meta property="og:url" content="<?= portfolio_h($canonicalUrl) ?>" />
  <meta property="og:locale" content="bg_BG" />
  <?php if ($ogImageUrl !== null && $ogImageUrl !== '') : ?>
  <meta property="og:image" content="<?= portfolio_h($ogImageUrl) ?>" />
  <meta property="og:image:alt" content="<?= portfolio_h($profile['name'] . ' — портфолио') ?>" />
  <?php endif; ?>
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= portfolio_h($pageTitle) ?>" />
  <meta name="twitter:description" content="<?= portfolio_h($pageDescription) ?>" />
  <?php if ($ogImageUrl !== null && $ogImageUrl !== '') : ?>
  <meta name="twitter:image" content="<?= portfolio_h($ogImageUrl) ?>" />
  <?php endif; ?>
  <meta name="theme-color" content="#0b0f18" media="(prefers-color-scheme: dark)" />
  <meta name="theme-color" content="#f4f5f7" media="(prefers-color-scheme: light)" />
  <script type="application/ld+json"><?= $jsonLd ?></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/portfolio.css?v=<?= portfolio_h($portfolioCssV) ?>" />
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

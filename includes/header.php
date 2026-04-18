<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $pageDescription */
/** @var string $canonicalUrl */
/** @var string|null $ogImageUrl */
/** @var string $metaKeywords */
/** @var string $jsonLd */

$navHrefPrefix = $navHrefPrefix ?? '';
$robotsMeta = $robotsMeta ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
$navShowCases = $navShowCases ?? false;
$navShowTestimonials = $navShowTestimonials ?? false;

$portfolioCssPath = __DIR__ . '/../assets/css/portfolio.css';
$portfolioCssMinPath = __DIR__ . '/../assets/css/portfolio.min.css';
$useCssMin = is_readable($portfolioCssMinPath);
$portfolioCssHref = $useCssMin ? 'assets/css/portfolio.min.css' : 'assets/css/portfolio.css';
$portfolioCssV = (string) filemtime($useCssMin ? $portfolioCssMinPath : $portfolioCssPath);

$brandHref = $navHrefPrefix === '' ? '#top' : portfolio_h($navHrefPrefix . '#top');
$brandLogoSrc = 'images/logo/' . rawurlencode('Sasho Dev 1-01.png');
$navFrag = static function (string $id) use ($navHrefPrefix): string {
    return $navHrefPrefix === '' ? '#' . $id : portfolio_h($navHrefPrefix . '#' . $id);
};

?>
<!DOCTYPE html>
<html
  lang="<?= portfolio_lang() === 'en' ? 'en' : 'bg' ?>"
  data-theme="dark"
  data-theme-aria-light="<?= portfolio_h(portfolio_t('theme_aria_light')) ?>"
  data-theme-aria-dark="<?= portfolio_h(portfolio_t('theme_aria_dark')) ?>"
>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= portfolio_h($pageTitle) ?></title>
  <meta name="description" content="<?= portfolio_h($pageDescription) ?>" />
  <link rel="canonical" href="<?= portfolio_h($canonicalUrl) ?>" />
  <link rel="alternate" hreflang="bg" href="<?= portfolio_h(portfolio_url_with_lang('bg')) ?>" />
  <link rel="alternate" hreflang="en" href="<?= portfolio_h(portfolio_url_with_lang('en')) ?>" />
  <link rel="alternate" hreflang="x-default" href="<?= portfolio_h(portfolio_url_with_lang('bg')) ?>" />
  <meta name="robots" content="<?= portfolio_h($robotsMeta) ?>" />
  <meta name="keywords" content="<?= portfolio_h($metaKeywords) ?>" />
  <meta name="author" content="<?= portfolio_h($profile['name']) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="<?= portfolio_h($profile['name']) ?>" />
  <meta property="og:title" content="<?= portfolio_h($pageTitle) ?>" />
  <meta property="og:description" content="<?= portfolio_h($pageDescription) ?>" />
  <meta property="og:url" content="<?= portfolio_h($canonicalUrl) ?>" />
  <meta property="og:locale" content="<?= portfolio_lang() === 'en' ? 'en_US' : 'bg_BG' ?>" />
  <?php if (portfolio_lang() === 'en') : ?>
  <meta property="og:locale:alternate" content="bg_BG" />
  <?php else : ?>
  <meta property="og:locale:alternate" content="en_US" />
  <?php endif; ?>
  <?php if ($ogImageUrl !== null && $ogImageUrl !== '') : ?>
  <meta property="og:image" content="<?= portfolio_h($ogImageUrl) ?>" />
  <meta property="og:image:width" content="<?= portfolio_h((string) ($ogImageWidth ?? 512)) ?>" />
  <meta property="og:image:height" content="<?= portfolio_h((string) ($ogImageHeight ?? 512)) ?>" />
  <meta property="og:image:alt" content="<?= portfolio_h($profile['name'] . ' — ' . portfolio_t('og_image_alt')) ?>" />
  <?php endif; ?>
  <meta name="twitter:card" content="<?= ($ogImageUrl !== null && $ogImageUrl !== '') ? 'summary_large_image' : 'summary' ?>" />
  <meta name="twitter:title" content="<?= portfolio_h($pageTitle) ?>" />
  <meta name="twitter:description" content="<?= portfolio_h($pageDescription) ?>" />
  <?php if ($ogImageUrl !== null && $ogImageUrl !== '') : ?>
  <meta name="twitter:image" content="<?= portfolio_h($ogImageUrl) ?>" />
  <?php endif; ?>
  <meta name="theme-color" content="#0b0f18" media="(prefers-color-scheme: dark)" />
  <meta name="theme-color" content="#f4f5f7" media="(prefers-color-scheme: light)" />
  <script type="application/ld+json"><?= $jsonLd ?></script>
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml" />
  <link rel="apple-touch-icon" href="assets/favicon.svg" />
  <link rel="manifest" href="manifest.webmanifest" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= portfolio_h($portfolioCssHref) ?>?v=<?= portfolio_h($portfolioCssV) ?>" />
</head>
<body data-projects-view="cards">
  <a class="skip-link" href="#main-content"><?= portfolio_h(portfolio_t('skip_content')) ?></a>
  <header class="site-header">
    <div class="container nav">
      <a class="brand brand-home" href="<?= $brandHref ?>">
        <img
          class="brand-logo"
          src="<?= portfolio_h($brandLogoSrc) ?>"
          alt=""
          decoding="async"
          fetchpriority="high"
        />
        <span><?= portfolio_h($profile['name']) ?></span>
      </a>
      <nav class="nav-links" aria-label="<?= portfolio_h(portfolio_t('nav_main')) ?>">
        <a href="<?= $navFrag('projects') ?>"><?= portfolio_h(portfolio_t('nav_projects')) ?></a>
        <a href="<?= $navFrag('github') ?>"><?= portfolio_h(portfolio_t('nav_github')) ?></a>
        <a href="<?= $navFrag('about') ?>"><?= portfolio_h(portfolio_t('nav_about')) ?></a>
        <a href="<?= $navFrag('skills') ?>"><?= portfolio_h(portfolio_t('nav_skills')) ?></a>
        <a href="<?= $navFrag('erp') ?>"><?= portfolio_h(portfolio_t('nav_erp')) ?></a>
        <?php if ($navShowCases) : ?>
          <a href="<?= $navFrag('cases') ?>"><?= portfolio_h(portfolio_t('nav_cases')) ?></a>
        <?php endif; ?>
        <?php if ($navShowTestimonials) : ?>
          <a href="<?= $navFrag('testimonials') ?>"><?= portfolio_h(portfolio_t('nav_testimonials')) ?></a>
        <?php endif; ?>
        <?php if (! empty($dogGalleryImages)) : ?>
          <a href="<?= $navFrag('dogs') ?>"><?= portfolio_h(portfolio_t('nav_dogs')) ?></a>
        <?php endif; ?>
        <a href="<?= $navFrag('contact') ?>"><?= portfolio_h(portfolio_t('nav_contact')) ?></a>
        <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
          <a href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('nav_linkedin')) ?></a>
        <?php endif; ?>
      </nav>
      <div class="toolbar">
        <div class="lang-switch" role="group" aria-label="<?= portfolio_h(portfolio_t('lang_switch_aria')) ?>">
          <a class="lang-switch__link<?= portfolio_lang() === 'bg' ? ' is-active' : '' ?>" href="<?= portfolio_h(portfolio_lang_url('bg')) ?>" hreflang="bg" lang="bg"><?= portfolio_h(portfolio_t('lang_bg')) ?></a>
          <a class="lang-switch__link<?= portfolio_lang() === 'en' ? ' is-active' : '' ?>" href="<?= portfolio_h(portfolio_lang_url('en')) ?>" hreflang="en" lang="en"><?= portfolio_h(portfolio_t('lang_en')) ?></a>
        </div>
        <div class="toggle-group" role="group" aria-label="<?= portfolio_h(portfolio_t('toolbar_projects_view')) ?>">
          <button type="button" data-projects-view="cards" aria-pressed="true"><?= portfolio_h(portfolio_t('view_cards')) ?></button>
          <button type="button" data-projects-view="table" aria-pressed="false"><?= portfolio_h(portfolio_t('view_table')) ?></button>
        </div>
        <button type="button" class="icon-btn" data-theme-toggle aria-label="<?= portfolio_h(portfolio_t('theme_toggle')) ?>">☀</button>
      </div>
    </div>
  </header>
  <main id="main-content" tabindex="-1">

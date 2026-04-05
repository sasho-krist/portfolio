<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
portfolio_load_dotenv(__DIR__ . '/.env');
require __DIR__ . '/includes/seo.php';
require __DIR__ . '/includes/i18n.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
portfolio_i18n_init();

$profile = require __DIR__ . '/data/profile.php';
$cases = require __DIR__ . '/data/cases.php';
$dogGalleryImages = portfolio_images_in_subdir('images/dogs');

$pageTitle = sprintf(portfolio_t('privacy_page_title'), $profile['name']);
$pageDescription = portfolio_t('privacy_meta_description');
$canonicalUrl = rtrim(portfolio_canonical_url(), '/') . '/privacy.php';
$ogImageUrl = null;
$metaKeywords = portfolio_t('privacy_meta_keywords');
$jsonLd = portfolio_seo_json_ld_webpage($pageTitle, $pageDescription, $canonicalUrl);
$robotsMeta = 'noindex, follow';
$navHrefPrefix = portfolio_lang() === 'en' ? 'index.php?lang=en' : 'index.php';
$navShowCases = $cases !== [];
$navShowTestimonials = ($profile['testimonials'] ?? []) !== [];
$plausibleDomain = trim((string) (getenv('PLAUSIBLE_DOMAIN') ?: ''));
$footerHomeHref = portfolio_lang() === 'en' ? 'index.php?lang=en' : 'index.php';

require __DIR__ . '/includes/header.php';
?>

    <article class="legal-page container">
      <header class="legal-header">
        <h1><?= portfolio_h(portfolio_t('privacy_h1')) ?></h1>
        <p class="muted"><?= portfolio_h(sprintf(portfolio_t('privacy_intro'), date('Y-m-d'))) ?></p>
      </header>

      <div class="legal-body">
        <h2><?= portfolio_h(portfolio_t('privacy_h2_controller')) ?></h2>
        <p>
          <?= portfolio_h(sprintf(portfolio_t('privacy_p_controller'), $profile['name'])) ?>
          <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a>.
        </p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_data')) ?></h2>
        <ul>
          <li><?= portfolio_h(portfolio_t('privacy_li_form')) ?></li>
          <li><?= portfolio_h(portfolio_t('privacy_li_logs')) ?></li>
          <li><?= portfolio_h(portfolio_t('privacy_li_chat')) ?></li>
        </ul>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_goal')) ?></h2>
        <p><?= portfolio_h(portfolio_t('privacy_p_goal')) ?></p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_retention')) ?></h2>
        <p><?= portfolio_h(portfolio_t('privacy_p_retention')) ?></p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_share')) ?></h2>
        <p><?= portfolio_h(portfolio_t('privacy_p_share')) ?></p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_cookies')) ?></h2>
        <p><?= portfolio_h(portfolio_t('privacy_p_cookies')) ?></p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_rights')) ?></h2>
        <p><?= portfolio_h(portfolio_t('privacy_p_rights')) ?></p>

        <h2><?= portfolio_h(portfolio_t('privacy_h2_contact')) ?></h2>
        <p>
          <?= portfolio_h(portfolio_t('privacy_p_contact')) ?>
          <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a>.
        </p>

        <p class="legal-back">
          <a class="btn btn-primary" href="<?= portfolio_h($footerHomeHref) ?>"><?= portfolio_h(portfolio_t('privacy_back')) ?></a>
        </p>
      </div>
    </article>

<?php
require __DIR__ . '/includes/footer.php';

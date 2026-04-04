<?php

declare(strict_types=1);

$portfolioJsPath = __DIR__ . '/../assets/js/portfolio.js';
$portfolioJsV = is_readable($portfolioJsPath) ? (string) filemtime($portfolioJsPath) : '1';
$plausibleDomain = isset($plausibleDomain) ? trim((string) $plausibleDomain) : '';

?>
  </main>
  <footer class="site-footer">
    <div class="container footer-inner">
      <small class="footer-copy">© <span id="year"></span> <?= portfolio_h($profile['name']) ?> · PHP <?= PHP_VERSION ?> · <?= portfolio_h(portfolio_lang() === 'en' ? 'Sofia' : 'София') ?></small>
      <nav class="footer-nav" aria-label="<?= portfolio_h(portfolio_t('footer_nav')) ?>">
        <?php
        $footerHome = $footerHomeHref ?? 'index.php';
        if (portfolio_lang() === 'en') {
            $footerHome .= (str_contains($footerHome, '?') ? '&' : '?') . 'lang=en';
        }
        ?>
        <a href="<?= portfolio_h($footerHome) ?>"><?= portfolio_h(portfolio_t('footer_home')) ?></a>
        <span class="footer-sep" aria-hidden="true">·</span>
        <a href="privacy.php<?= portfolio_lang() === 'en' ? '?lang=en' : '' ?>"><?= portfolio_h(portfolio_t('footer_privacy')) ?></a>
        <?php if (! empty($profile['site_repo_url']) && is_string($profile['site_repo_url']) && filter_var($profile['site_repo_url'], FILTER_VALIDATE_URL)) : ?>
          <span class="footer-sep" aria-hidden="true">·</span>
          <a href="<?= portfolio_h($profile['site_repo_url']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('footer_site_repo')) ?></a>
        <?php endif; ?>
      </nav>
    </div>
  </footer>
  <div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-hidden="true" aria-label="<?= portfolio_h(portfolio_t('lightbox_label')) ?>">
    <div class="lightbox-inner">
      <button type="button" class="lightbox-close" aria-label="<?= portfolio_h(portfolio_t('lightbox_close')) ?>">×</button>
      <button type="button" class="lightbox-nav lightbox-prev" aria-label="<?= portfolio_h(portfolio_t('lightbox_prev')) ?>">‹</button>
      <button type="button" class="lightbox-nav lightbox-next" aria-label="<?= portfolio_h(portfolio_t('lightbox_next')) ?>">›</button>
      <img class="lightbox-img" src="" alt="" />
      <p class="lightbox-caption"></p>
    </div>
  </div>
  <?php if ($plausibleDomain !== '') : ?>
  <script defer data-domain="<?= portfolio_h($plausibleDomain) ?>" src="https://plausible.io/js/script.js"></script>
  <?php endif; ?>
  <script src="assets/js/portfolio.js?v=<?= portfolio_h($portfolioJsV) ?>" defer></script>
</body>
</html>

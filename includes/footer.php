<?php

declare(strict_types=1);

$portfolioJsPath = __DIR__ . '/../assets/js/portfolio.js';
$portfolioJsV = is_readable($portfolioJsPath) ? (string) filemtime($portfolioJsPath) : '1';
$plausibleDomain = isset($plausibleDomain) ? trim((string) $plausibleDomain) : '';

?>
  </main>
  <footer class="site-footer">
    <div class="container footer-inner">
      <small class="footer-copy">© <span id="year"></span> <?= portfolio_h($profile['name']) ?> · PHP <?= PHP_VERSION ?> · София</small>
      <nav class="footer-nav" aria-label="Долна навигация">
        <a href="<?= portfolio_h($footerHomeHref ?? 'index.php') ?>">Начало</a>
        <span class="footer-sep" aria-hidden="true">·</span>
        <a href="privacy.php">Поверителност</a>
        <?php if (! empty($profile['site_repo_url']) && is_string($profile['site_repo_url']) && filter_var($profile['site_repo_url'], FILTER_VALIDATE_URL)) : ?>
          <span class="footer-sep" aria-hidden="true">·</span>
          <a href="<?= portfolio_h($profile['site_repo_url']) ?>" target="_blank" rel="noopener noreferrer">This site repo</a>
        <?php endif; ?>
      </nav>
    </div>
  </footer>
  <div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Галерия">
    <div class="lightbox-inner">
      <button type="button" class="lightbox-close" aria-label="Затвори">×</button>
      <button type="button" class="lightbox-nav lightbox-prev" aria-label="Предишна">‹</button>
      <button type="button" class="lightbox-nav lightbox-next" aria-label="Следваща">›</button>
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

<?php

declare(strict_types=1);

?>
  </main>
  <footer class="site-footer">
    <div class="container">
      <small>© <span id="year"></span> <?= portfolio_h($profile['name']) ?> · PHP <?= PHP_VERSION ?> · София</small>
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
  <script src="assets/js/portfolio.js" defer></script>
</body>
</html>

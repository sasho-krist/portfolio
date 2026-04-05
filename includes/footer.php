<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (! isset($_SESSION['chat_csrf'])) {
    $_SESSION['chat_csrf'] = bin2hex(random_bytes(16));
}
$chatCsrf = $_SESSION['chat_csrf'];
$chatEnabled = trim((string) (getenv('OPENAI_API_KEY') ?: '')) !== '';
$chatSuggestions = [];
if ($chatEnabled) {
    $chatSuggestionsFile = __DIR__ . '/../data/chat_suggestions.php';
    if (is_readable($chatSuggestionsFile)) {
        /** @var array<string, list<array{title: string, questions: list<string>}>> $chatSuggestionsAll */
        $chatSuggestionsAll = require $chatSuggestionsFile;
        $chatLang = portfolio_lang();
        $chatSuggestions = $chatSuggestionsAll[$chatLang] ?? $chatSuggestionsAll['bg'] ?? [];
    }
}
$alexJsPath = __DIR__ . '/../assets/js/alexander-chat.js';
$alexJsMinPath = __DIR__ . '/../assets/js/alexander-chat.min.js';
$useAlexMin = is_readable($alexJsMinPath);
$alexJsHref = $useAlexMin ? 'assets/js/alexander-chat.min.js' : 'assets/js/alexander-chat.js';
$alexJsV = (string) filemtime($useAlexMin ? $alexJsMinPath : $alexJsPath);

$portfolioJsPath = __DIR__ . '/../assets/js/portfolio.js';
$portfolioJsMinPath = __DIR__ . '/../assets/js/portfolio.min.js';
$useJsMin = is_readable($portfolioJsMinPath);
$portfolioJsHref = $useJsMin ? 'assets/js/portfolio.min.js' : 'assets/js/portfolio.js';
$portfolioJsV = (string) filemtime($useJsMin ? $portfolioJsMinPath : $portfolioJsPath);
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
  <?php if ($chatEnabled) : ?>
  <script type="application/json" id="alex-chat-config"><?= json_encode([
      'api' => 'api/chat.php',
      'csrf' => $chatCsrf,
      'lang' => portfolio_lang() === 'en' ? 'en' : 'bg',
      'strings' => [
          'loading' => portfolio_t('chat_loading'),
          'error_csrf' => portfolio_t('chat_error_csrf'),
          'error_empty' => portfolio_t('chat_error_empty'),
          'error_long' => portfolio_t('chat_error_long'),
          'error_rate' => portfolio_t('chat_error_rate'),
          'error_config' => portfolio_t('chat_error_config'),
          'error_network' => portfolio_t('chat_error_network'),
          'error_ssl' => portfolio_t('chat_error_ssl'),
          'auth' => portfolio_t('chat_error_auth'),
          'forbidden' => portfolio_t('chat_error_forbidden'),
          'openai_rate' => portfolio_t('chat_error_openai_rate'),
          'retry_after_hint' => portfolio_t('chat_retry_after_hint'),
          'quota' => portfolio_t('chat_error_quota'),
          'model' => portfolio_t('chat_error_model'),
          'error_api' => portfolio_t('chat_error_api'),
          'error_generic' => portfolio_t('chat_error_generic'),
      ],
  ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?></script>
  <div id="alex-chat" class="alex-chat">
    <button type="button" class="alex-chat-fab" aria-expanded="false" aria-controls="alex-chat-panel" aria-label="<?= portfolio_h(portfolio_t('chat_fab_aria')) ?>" title="<?= portfolio_h(portfolio_t('chat_fab_aria')) ?>">
      <span class="alex-chat-fab-icon" aria-hidden="true">💬</span>
    </button>
    <div id="alex-chat-panel" class="alex-chat-panel" role="dialog" aria-modal="true" aria-labelledby="alex-chat-title" hidden>
      <div class="alex-chat-head">
        <div>
          <h2 id="alex-chat-title" class="alex-chat-title"><?= portfolio_h(portfolio_t('chat_title')) ?></h2>
          <p class="alex-chat-sub"><?= portfolio_h(portfolio_t('chat_subtitle')) ?></p>
        </div>
        <button type="button" class="alex-chat-close" aria-label="<?= portfolio_h(portfolio_t('chat_close')) ?>">×</button>
      </div>
      <?php if ($chatSuggestions !== []) : ?>
      <div class="alex-chat-suggestions-wrap" id="alex-chat-suggestions-wrap">
        <div class="alex-chat-suggestions-head">
          <p class="alex-chat-suggestions-heading" id="alex-chat-suggestions-title"><?= portfolio_h(portfolio_t('chat_suggestions_heading')) ?></p>
          <button
            type="button"
            class="alex-chat-suggestions-toggle"
            aria-expanded="true"
            aria-controls="alex-chat-suggestions-body"
            data-label-hide="<?= portfolio_h(portfolio_t('chat_suggestions_hide')) ?>"
            data-label-show="<?= portfolio_h(portfolio_t('chat_suggestions_show')) ?>"
          ><?= portfolio_h(portfolio_t('chat_suggestions_hide')) ?></button>
        </div>
        <div id="alex-chat-suggestions-body" class="alex-chat-suggestions-body">
        <div class="alex-chat-suggestions-cats" role="group" aria-labelledby="alex-chat-suggestions-title">
          <?php foreach ($chatSuggestions as $chatCat) : ?>
            <?php
            if (! is_array($chatCat) || ! isset($chatCat['title'], $chatCat['questions']) || ! is_array($chatCat['questions'])) {
                continue;
            }
            ?>
          <details class="alex-chat-cat" name="alex-chat-topics">
            <summary class="alex-chat-cat-summary"><?= portfolio_h($chatCat['title']) ?></summary>
            <div class="alex-chat-cat-questions">
              <?php foreach ($chatCat['questions'] as $chatQ) : ?>
                <button type="button" class="alex-chat-suggestion"><?= portfolio_h($chatQ) ?></button>
              <?php endforeach; ?>
            </div>
          </details>
          <?php endforeach; ?>
        </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="alex-chat-messages" role="log" aria-live="polite" aria-relevant="additions"></div>
      <form class="alex-chat-form" action="#" method="post">
        <label class="sr-only" for="alex-chat-input"><?= portfolio_h(portfolio_t('chat_placeholder')) ?></label>
        <textarea id="alex-chat-input" class="alex-chat-input" name="message" rows="2" maxlength="4000" placeholder="<?= portfolio_h(portfolio_t('chat_placeholder')) ?>" required></textarea>
        <button type="submit" class="btn btn-primary alex-chat-send"><?= portfolio_h(portfolio_t('chat_send')) ?></button>
      </form>
    </div>
  </div>
  <script src="<?= portfolio_h($alexJsHref) ?>?v=<?= portfolio_h($alexJsV) ?>" defer></script>
  <?php endif; ?>
  <script src="<?= portfolio_h($portfolioJsHref) ?>?v=<?= portfolio_h($portfolioJsV) ?>" defer></script>
</body>
</html>

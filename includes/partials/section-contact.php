<?php

declare(strict_types=1);

/**
 * @var array $profile
 * @var array{ok: bool, text: string}|null $contactFlash
 * @var string $csrfContactToken
 * @var bool $hasResume
 * @var string $resumeUrl
 */

?>
    <section id="contact">
      <div class="container">
        <h2 class="section-title"><?= portfolio_h(portfolio_t('contact_title')) ?></h2>
        <div class="contact-grid">
          <div class="card">
            <?php if ($contactFlash !== null) : ?>
              <div class="contact-flash <?= $contactFlash['ok'] ? 'contact-flash--ok' : 'contact-flash--err' ?>" role="status">
                <?= portfolio_h($contactFlash['text']) ?>
              </div>
            <?php endif; ?>
            <form id="contactForm" class="contact-form" action="contact-send.php" method="post" accept-charset="UTF-8" aria-describedby="contact-privacy-note">
              <input type="hidden" name="csrf" value="<?= portfolio_h($csrfContactToken) ?>" />
              <div class="field-honeypot" aria-hidden="true">
                <label for="website"><?= portfolio_h(portfolio_t('contact_honeypot')) ?></label>
                <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off" />
              </div>
              <div class="field">
                <label for="sender_name"><?= portfolio_h(portfolio_t('contact_name')) ?></label>
                <input id="sender_name" name="sender_name" type="text" required autocomplete="name" placeholder="<?= portfolio_h(portfolio_t('contact_name_placeholder')) ?>" maxlength="200" />
              </div>
              <div class="field">
                <label for="sender_email"><?= portfolio_h(portfolio_t('contact_email_label')) ?></label>
                <input id="sender_email" name="sender_email" type="email" required autocomplete="email" placeholder="you@example.com" maxlength="254" />
              </div>
              <div class="field">
                <label for="sender_message"><?= portfolio_h(portfolio_t('contact_message')) ?></label>
                <textarea id="sender_message" name="sender_message" required placeholder="<?= portfolio_h(portfolio_t('contact_placeholder')) ?>" maxlength="8000"></textarea>
              </div>
              <p id="contact-privacy-note" class="contact-privacy">
                <?= portfolio_h(portfolio_t('contact_privacy')) ?>
                <a href="privacy.php<?= portfolio_lang() === 'en' ? '?lang=en' : '' ?>"><?= portfolio_h(portfolio_t('contact_privacy_link')) ?></a>.
              </p>
              <button type="submit" class="btn btn-primary"><?= portfolio_h(portfolio_t('contact_submit')) ?></button>
            </form>
          </div>
          <div class="card">
            <h3 style="margin-top:0"><?= portfolio_h(portfolio_t('contact_direct')) ?></h3>
            <p><strong><?= portfolio_h(portfolio_t('contact_email')) ?></strong> <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_phone')) ?></strong> <a href="tel:<?= portfolio_h(preg_replace('/\s+/', '', $profile['phone'])) ?>"><?= portfolio_h($profile['phone']) ?></a></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_location')) ?></strong> <?= portfolio_h(portfolio_profile_text($profile, 'location')) ?></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_github')) ?></strong> <a href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer">@sasho-krist</a></p>
            <?php if (! empty($profile['site_repo_url']) && is_string($profile['site_repo_url']) && filter_var($profile['site_repo_url'], FILTER_VALIDATE_URL)) : ?>
              <?php
                $repoPath = trim((string) parse_url($profile['site_repo_url'], PHP_URL_PATH), '/');
                $repoLabel = $repoPath !== '' ? $repoPath : 'GitHub';
              ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_site_repo')) ?></strong> <a href="<?= portfolio_h($profile['site_repo_url']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h($repoLabel) ?></a></p>
            <?php endif; ?>
            <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_linkedin')) ?></strong> <a href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('contact_linkedin_profile')) ?></a></p>
            <?php endif; ?>
            <?php if ($hasResume) : ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_cv')) ?></strong> <a href="<?= portfolio_h($resumeUrl) ?>" download="<?= portfolio_h(basename($resumeUrl)) ?>"><?= portfolio_h(portfolio_t('contact_cv_download')) ?></a></p>
            <?php endif; ?>
            <p class="muted" style="margin-bottom:0">
              <a class="btn btn-primary" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('contact_calendar')) ?></a>
            </p>
          </div>
        </div>
      </div>
    </section>

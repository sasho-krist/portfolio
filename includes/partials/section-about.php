<?php

declare(strict_types=1);

/** @var array $profile @var list<string> $dogGalleryImages */

?>
    <section id="about">
      <div class="container">
        <h2 class="section-title"><?= portfolio_h(portfolio_t('about_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_profile_text($profile, 'profile')) ?></p>
        <?php if (portfolio_profile_array($profile, 'about_bullets') !== []) : ?>
          <div class="card about-highlights">
            <h3 class="about-highlights__title"><?= portfolio_h(portfolio_t('about_summary')) ?></h3>
            <ul class="about-highlights__list">
              <?php foreach (portfolio_profile_array($profile, 'about_bullets') as $bullet) : ?>
                <li><?= portfolio_h((string) $bullet) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <div class="two-cols">
          <div class="card">
            <h3><?= portfolio_h(portfolio_t('about_career')) ?></h3>
            <div class="timeline">
              <?php foreach ($profile['experience'] as $job) : ?>
                <article class="timeline-item">
                  <h3><?= portfolio_h($job['company']) ?></h3>
                  <div class="timeline-meta"><?= portfolio_h($job['role']) ?> · <?= portfolio_h($job['period']) ?></div>
                  <p class="muted" style="margin:0"><?= portfolio_h($job['desc']) ?></p>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="card">
            <h3><?= portfolio_h(portfolio_t('about_education')) ?></h3>
            <?php foreach ($profile['education'] as $edu) : ?>
              <article style="margin-bottom:1.25rem">
                <strong><?= portfolio_h($edu['period']) ?></strong>
                <p style="margin:0.35rem 0 0"><?= portfolio_h($edu['degree']) ?></p>
                <p class="muted" style="margin:0.25rem 0 0"><?= portfolio_h($edu['school']) ?></p>
                <ul class="muted" style="margin:0.5rem 0 0;font-size:0.92rem">
                  <?php foreach ($edu['details'] as $d) : ?>
                    <li><?= portfolio_h($d) ?></li>
                  <?php endforeach; ?>
                </ul>
              </article>
            <?php endforeach; ?>
            <h3 style="margin-top:1.5rem"><?= portfolio_h(portfolio_t('about_languages')) ?> <span class="muted" style="font-weight:500;font-size:0.88em">· Languages</span></h3>
            <ul class="muted" style="margin:0">
              <?php foreach ($profile['languages'] as $lang => $level) : ?>
                <li><strong><?= portfolio_h($lang) ?></strong> — <?= portfolio_h($level) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="card" style="margin-top:18px">
          <h3><?= portfolio_h(portfolio_t('about_interests')) ?> <span class="muted" style="font-weight:500;font-size:0.88em">· Interests</span></h3>
          <div class="chip-row">
            <?php foreach ($profile['interests'] as $interest) : ?>
              <?php
                $chipLabel = is_array($interest) ? portfolio_interest_label($interest) : (string) $interest;
                $chipHref = is_array($interest) ? ($interest['href'] ?? null) : null;
                if ($chipHref === '#dogs' && $dogGalleryImages === []) {
                    $chipHref = null;
                }
              ?>
              <?php if ($chipHref) : ?>
                <a class="chip chip-link" href="<?= portfolio_h((string) $chipHref) ?>"><?= portfolio_h($chipLabel) ?></a>
              <?php else : ?>
                <span class="chip"><?= portfolio_h($chipLabel) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

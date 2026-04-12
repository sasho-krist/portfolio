<?php

declare(strict_types=1);

/** @var array $profile @var bool $hasProfilePhoto @var int $projectsCount @var string $heroFocus */

?>
    <section class="hero" id="top">
      <div class="container hero-grid">
        <div class="hero-main">
          <?php if ($hasProfilePhoto) : ?>
            <div class="hero-profile">
              <div class="hero-profile__clip">
                <img
                  class="hero-profile__img"
                  src="images/alexander.jpg"
                  alt="<?= portfolio_h($profile['name']) ?>"
                  width="160"
                  height="160"
                  loading="eager"
                  fetchpriority="high"
                  decoding="async"
                />
              </div>
            </div>
          <?php endif; ?>
          <div class="hero-copy">
            <span class="tag"><span class="tag-dot" aria-hidden="true"></span> <?= portfolio_h(portfolio_t('hero_tag')) ?></span>
            <p class="hero-name"><?= portfolio_h($profile['name']) ?></p>
            <h1 class="hero-title"><?= portfolio_h($profile['title']) ?></h1>
            <p class="hero-lead"><?= portfolio_h(portfolio_profile_text($profile, 'tagline')) ?></p>
            <?php if ($heroFocus !== '') : ?>
              <p class="hero-focus"><?= portfolio_h($heroFocus) ?></p>
            <?php endif; ?>
            <div class="hero-actions hero-actions--primary">
              <a class="btn btn-primary" href="#project-questionnaire-ai"><?= portfolio_h(portfolio_t('hero_view_projects')) ?></a>
              <a class="btn btn-primary" href="#contact"><?= portfolio_h(portfolio_t('hero_contact')) ?></a>
            </div>
            <div class="hero-actions hero-actions--secondary">
              <a class="btn" href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('hero_github')) ?></a>
              <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
                <a class="btn" href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('hero_linkedin')) ?></a>
              <?php endif; ?>
              <a class="btn" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('hero_schedule')) ?></a>
            </div>
            <p class="hero-stats" aria-label="<?= portfolio_h(portfolio_t('hero_stats_label')) ?>">
              <span><strong><?= (int) $projectsCount ?></strong> <?= portfolio_h(portfolio_t('hero_stats_projects')) ?></span>
              <span class="hero-stats__sep" aria-hidden="true">·</span>
              <span><strong><?= portfolio_h((string) ($profile['years_experience'] ?? '7')) ?></strong> <?= portfolio_h(portfolio_t('hero_stats_years')) ?></span>
            </p>
          </div>
        </div>
        <aside class="card quick-facts">
          <h2><?= portfolio_h(portfolio_t('hero_quick_facts')) ?></h2>
          <ul>
            <?php foreach (portfolio_profile_array($profile, 'quick_facts') as $fact) : ?>
              <li><?= portfolio_h($fact) ?></li>
            <?php endforeach; ?>
          </ul>
        </aside>
      </div>
    </section>

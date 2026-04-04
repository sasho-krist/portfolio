<?php

declare(strict_types=1);

/** @var array $profile */

?>
    <section id="skills" class="skills-section" aria-labelledby="skills-heading">
      <div class="container">
        <h2 id="skills-heading" class="section-title"><?= portfolio_h(portfolio_t('skills_title')) ?></h2>
        <div class="tech-stack-grid tech-stack-grid--merged">
          <?php foreach (portfolio_skills_cards_for_lang($profile) as $label => $text) : ?>
            <article class="card tech-stack-card">
              <h3 class="tech-stack-card__title"><?= portfolio_h((string) $label) ?></h3>
              <p class="tech-stack-card__body"><?= portfolio_h((string) $text) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
        <?php if (($profile['services'] ?? []) !== []) : ?>
          <div class="card services-card">
            <h3 class="services-card__title"><?= portfolio_h(portfolio_t('services_title')) ?></h3>
            <ul class="services-list">
              <?php foreach (portfolio_services_for_lang($profile) as $service) : ?>
                <li><?= portfolio_h((string) $service) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </section>

<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $cases */

?>
    <section id="cases" aria-labelledby="cases-heading">
      <div class="container">
        <h2 id="cases-heading" class="section-title"><?= portfolio_h(portfolio_t('cases_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_t('cases_intro')) ?></p>
        <div class="cases-grid">
          <?php foreach ($cases as $c) : ?>
            <article class="card case-card" id="case-<?= portfolio_h($c['slug']) ?>">
              <h3><?= portfolio_h($c['title']) ?></h3>
              <p class="case-context muted"><?= portfolio_h($c['context']) ?></p>
              <div class="case-block">
                <h4 class="case-label"><?= portfolio_h(portfolio_t('cases_challenge')) ?></h4>
                <p><?= portfolio_h($c['problem']) ?></p>
              </div>
              <div class="case-block">
                <h4 class="case-label"><?= portfolio_h(portfolio_t('cases_approach')) ?></h4>
                <p><?= portfolio_h($c['approach']) ?></p>
              </div>
              <p class="case-stack"><strong><?= portfolio_h(portfolio_t('cases_tech')) ?></strong> <?= portfolio_h(implode(' · ', $c['stack'])) ?></p>
              <div class="case-outcome">
                <strong><?= portfolio_h(portfolio_t('cases_outcome')) ?></strong>
                <p><?= portfolio_h($c['outcome']) ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

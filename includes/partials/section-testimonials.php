<?php

declare(strict_types=1);

/** @var array $profile */

?>
    <section id="testimonials" class="testimonials-section" aria-labelledby="testimonials-heading">
      <div class="container">
        <h2 id="testimonials-heading" class="section-title"><?= portfolio_h(portfolio_t('testimonials_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_t('testimonials_intro')) ?></p>
        <div class="testimonials-grid">
          <?php foreach ($profile['testimonials'] as $t) : ?>
            <blockquote class="card testimonial-card">
              <p class="testimonial-quote">“<?= portfolio_h($t['quote']) ?>”</p>
              <footer class="testimonial-footer">
                <cite class="testimonial-cite">
                  <span class="testimonial-name"><?= portfolio_h($t['name']) ?></span>
                  <span class="muted"> · <?= portfolio_h($t['role']) ?><?= ! empty($t['company']) ? ', ' . portfolio_h($t['company']) : '' ?></span>
                </cite>
              </footer>
            </blockquote>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

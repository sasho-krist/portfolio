<?php

declare(strict_types=1);

/** @var list<string> $dogGalleryImages */

?>
    <section id="dogs" class="dogs-section" aria-labelledby="dogs-heading">
      <div class="container">
        <h2 id="dogs-heading" class="section-title"><?= portfolio_h(portfolio_t('dogs_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_t('dogs_intro')) ?></p>
        <div class="gallery-grid gallery-grid--dogs">
          <?php foreach ($dogGalleryImages as $i => $src) : ?>
            <?php
              $fileBase = pathinfo($src, PATHINFO_FILENAME);
              $caption = $fileBase !== '' ? (string) preg_replace('/[-_]+/', ' ', $fileBase) : portfolio_t('dog_photo') . ' ' . (string) ($i + 1);
            ?>
            <figure
              class="gallery-item gallery-item--dog"
              data-lightbox-group="dogs"
              data-lightbox-index="<?= (int) $i ?>"
              data-full="<?= portfolio_h($src) ?>"
              data-caption="<?= portfolio_h($caption) ?>"
              role="button"
              tabindex="0"
              aria-label="<?= portfolio_h(portfolio_t('erp_open')) ?> <?= portfolio_h($caption) ?>"
            >
              <img src="<?= portfolio_h($src) ?>" alt="<?= portfolio_h($caption) ?>" loading="lazy" width="400" height="280" />
              <figcaption><?= portfolio_h($caption) ?></figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

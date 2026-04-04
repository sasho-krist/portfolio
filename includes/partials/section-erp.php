<?php

declare(strict_types=1);

/** @var list<string> $galleryImages @var list<string> $erpCaptions */

?>
    <section id="erp">
      <div class="container">
        <h2 class="section-title"><?= portfolio_h(portfolio_t('erp_title')) ?></h2>
        <p class="section-intro">
          <?= portfolio_h(portfolio_t('erp_intro')) ?>
        </p>
        <div class="erp-banner">
          <strong><?= portfolio_h(portfolio_t('erp_important')) ?></strong>
          <span class="muted"> <?= portfolio_h(portfolio_t('erp_banner')) ?></span>
        </div>
        <?php if ($galleryImages !== []) : ?>
          <h3 class="section-title" style="font-size:1.2rem;margin-top:8px"><?= portfolio_h(portfolio_t('erp_gallery')) ?></h3>
          <p class="section-intro" style="margin-top:0"><?= portfolio_h(portfolio_t('erp_gallery_hint')) ?></p>
          <div class="gallery-grid">
            <?php foreach ($galleryImages as $i => $src) : ?>
              <?php
                $caption = $erpCaptions[$i] ?? ('BioMarket ERP — ' . (string) ($i + 1));
              ?>
              <figure
                class="gallery-item"
                data-lightbox-group="erp"
                data-lightbox-index="<?= (int) $i ?>"
                data-full="<?= portfolio_h($src) ?>"
                data-caption="<?= portfolio_h($caption) ?>"
                role="button"
                tabindex="0"
                aria-label="<?= portfolio_h(portfolio_t('erp_open')) ?> <?= portfolio_h($caption) ?>"
              >
                <img src="<?= portfolio_h($src) ?>" alt="<?= portfolio_h($caption) ?>" loading="lazy" width="400" height="250" />
                <figcaption><?= portfolio_h($caption) ?></figcaption>
              </figure>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

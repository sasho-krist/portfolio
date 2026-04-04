<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $projects */

?>
    <section id="projects">
      <div class="container">
        <div class="projects-toolbar">
          <div>
            <h2 class="section-title" style="margin-bottom:0"><?= portfolio_h(portfolio_t('projects_title')) ?></h2>
          </div>
        </div>
        <div class="projects-grid" aria-label="<?= portfolio_h(portfolio_t('projects_aria_cards')) ?>">
          <?php foreach ($projects as $p) : ?>
            <article class="card project-card" id="project-<?= portfolio_h($p['slug']) ?>">
              <div class="project-meta">
                <?php foreach ($p['pills'] as $pill) : ?>
                  <span class="pill"><?= portfolio_h($pill) ?></span>
                <?php endforeach; ?>
              </div>
              <h3><?= portfolio_h($p['name']) ?></h3>
              <p class="project-desc"><?= portfolio_h($p['desc']) ?></p>
              <?php if (! empty($p['readme_excerpt'])) : ?>
                <p class="project-readme"><?= portfolio_h((string) $p['readme_excerpt']) ?></p>
              <?php endif; ?>
              <?php
                $hasDetail = ! empty($p['problem']) || ! empty($p['my_role']) || ! empty($p['challenge']) || ! empty($p['solution']);
              ?>
              <?php if ($hasDetail) : ?>
                <dl class="project-detail">
                  <?php if (! empty($p['problem'])) : ?>
                    <dt><?= portfolio_h(portfolio_t('projects_detail_problem')) ?></dt>
                    <dd><?= portfolio_h((string) $p['problem']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['my_role'])) : ?>
                    <dt><?= portfolio_h(portfolio_t('projects_detail_role')) ?></dt>
                    <dd><?= portfolio_h((string) $p['my_role']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['challenge'])) : ?>
                    <dt><?= portfolio_h(portfolio_t('projects_detail_challenge')) ?></dt>
                    <dd><?= portfolio_h((string) $p['challenge']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['solution'])) : ?>
                    <dt><?= portfolio_h(portfolio_t('projects_detail_solution')) ?></dt>
                    <dd><?= portfolio_h((string) $p['solution']) ?></dd>
                  <?php endif; ?>
                </dl>
              <?php endif; ?>
              <?php
                $projectShots = array_values(array_filter(
                    portfolio_project_screenshots($p),
                    static fn (array $s): bool => filter_var($s['url'], FILTER_VALIDATE_URL) !== false
                ));
              ?>
              <?php if ($projectShots !== []) : ?>
                <div class="project-shots" aria-label="<?= portfolio_h(portfolio_t('projects_shots_aria')) ?>">
                  <?php foreach ($projectShots as $si => $shot) : ?>
                    <?php
                      $alt = $p['name'] . ' — снимка ' . (string) ($si + 1);
                      if ($shot['caption'] !== '') {
                          $alt = $shot['caption'];
                      }
                    ?>
                    <figure class="project-shot">
                      <a href="<?= portfolio_h($shot['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <img
                          src="<?= portfolio_h($shot['url']) ?>"
                          alt="<?= portfolio_h($alt) ?>"
                          width="320"
                          height="200"
                          loading="lazy"
                          decoding="async"
                        />
                      </a>
                      <?php if ($shot['caption'] !== '') : ?>
                        <figcaption class="project-shot-cap"><?= portfolio_h($shot['caption']) ?></figcaption>
                      <?php endif; ?>
                    </figure>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <div class="project-actions">
                <a class="btn btn-primary" href="<?= portfolio_h($p['repo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_github')) ?></a>
                <?php if (! empty($p['demo'])) : ?>
                  <a class="btn" href="<?= portfolio_h((string) $p['demo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_demo')) ?></a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="projects-table-wrap" aria-label="<?= portfolio_h(portfolio_t('projects_aria_table')) ?>">
          <table class="projects-table">
            <thead>
              <tr>
                <th><?= portfolio_h(portfolio_t('projects_th_project')) ?></th>
                <th><?= portfolio_h(portfolio_t('projects_th_tech')) ?></th>
                <th><?= portfolio_h(portfolio_t('projects_th_desc')) ?></th>
                <th><?= portfolio_h(portfolio_t('projects_th_links')) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($projects as $p) : ?>
                <tr>
                  <td><strong><?= portfolio_h($p['name']) ?></strong></td>
                  <td><?= portfolio_h(implode(', ', $p['pills'])) ?></td>
                  <td>
                    <?= portfolio_h($p['desc']) ?>
                    <?php if (! empty($p['readme_excerpt'])) : ?>
                      <div class="projects-table-excerpt muted"><?= portfolio_h((string) $p['readme_excerpt']) ?></div>
                    <?php endif; ?>
                    <?php
                      $shotCount = count(array_filter(
                          portfolio_project_screenshots($p),
                          static fn (array $s): bool => filter_var($s['url'], FILTER_VALIDATE_URL) !== false
                      ));
                    ?>
                    <?php if ($shotCount > 0) : ?>
                      <div class="projects-table-shots">
                        <a href="#project-<?= portfolio_h($p['slug']) ?>"><?= (int) $shotCount ?> <?= portfolio_h(portfolio_t('projects_shots_link')) ?></a>
                      </div>
                    <?php endif; ?>
                    <?php
                      $bits = [];
                      if (! empty($p['problem'])) {
                          $bits[] = portfolio_t('projects_table_problem') . ' ' . $p['problem'];
                      }
                      if (! empty($p['my_role'])) {
                          $bits[] = portfolio_t('projects_table_role') . ' ' . $p['my_role'];
                      }
                      if (! empty($p['challenge']) && ! empty($p['solution'])) {
                          $bits[] = portfolio_t('projects_table_challenge_solution') . ' ' . $p['challenge'] . ' → ' . $p['solution'];
                      } elseif (! empty($p['challenge'])) {
                          $bits[] = portfolio_t('projects_table_challenge') . ' ' . $p['challenge'];
                      }
                      if ($bits !== []) {
                          echo '<div class="projects-table-detail muted">' . portfolio_h(implode(' ', $bits)) . '</div>';
                      }
                    ?>
                  </td>
                  <td>
                    <a href="<?= portfolio_h($p['repo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_github')) ?></a>
                    <?php if (! empty($p['demo'])) : ?>
                      · <a href="<?= portfolio_h((string) $p['demo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_demo')) ?></a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

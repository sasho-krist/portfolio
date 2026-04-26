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
            <?php
              $projectShots = array_values(array_filter(
                  portfolio_project_screenshots($p),
                  static fn (array $s): bool => portfolio_is_valid_project_image_url($s['url'])
              ));
              $cover = $projectShots[0] ?? null;
              $projectUrl = 'project.php?slug=' . rawurlencode((string) $p['slug']);
              if (portfolio_lang() === 'en') {
                  $projectUrl .= '&lang=en';
              }
            ?>
            <article class="card project-card" id="project-<?= portfolio_h($p['slug']) ?>">
              <a class="project-card-main" href="<?= portfolio_h($projectUrl) ?>">
                <div class="project-meta">
                  <?php foreach ($p['pills'] as $pill) : ?>
                    <span class="pill"><?= portfolio_h($pill) ?></span>
                  <?php endforeach; ?>
                </div>
                <h3><?= portfolio_h($p['name']) ?></h3>
                <?php if ($cover !== null) : ?>
                  <?php
                    $coverAlt = $cover['caption'] !== ''
                        ? $cover['caption']
                        : ($p['name'] . ' — screenshot');
                  ?>
                  <figure class="project-cover">
                    <img
                      src="<?= portfolio_h($cover['url']) ?>"
                      alt="<?= portfolio_h($coverAlt) ?>"
                      width="640"
                      height="360"
                      loading="lazy"
                      decoding="async"
                    />
                  </figure>
                <?php endif; ?>
                <p class="project-desc project-desc--compact"><?= portfolio_h($p['desc']) ?></p>
                <span class="project-open-link"><?= portfolio_h(portfolio_t('projects_open')) ?> →</span>
              </a>
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
                          static fn (array $s): bool => portfolio_is_valid_project_image_url($s['url'])
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

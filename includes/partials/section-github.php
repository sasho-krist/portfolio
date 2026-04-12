<?php

declare(strict_types=1);

/** @var array $profile */

?>
    <section id="github" class="github-section" aria-labelledby="github-heading">
      <div class="container">
        <h2 id="github-heading" class="section-title"><?= portfolio_h(portfolio_t('github_section_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_profile_text($profile, 'github_section_intro')) ?></p>
        <p class="github-section__cta">
          <a class="btn btn-primary" href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('github_profile_cta')) ?></a>
        </p>
        <?php $ghRepos = portfolio_github_repos_for_lang($profile); ?>
        <?php if ($ghRepos !== []) : ?>
          <ul class="github-repo-cards">
            <?php foreach ($ghRepos as $repo) : ?>
              <li class="github-repo-cards__item">
                <div class="github-repo-card">
                  <a class="github-repo-card__repo" href="<?= portfolio_h($repo['url']) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="github-repo-card__name"><?= portfolio_h($repo['label']) ?></span>
                    <?php if (! empty($repo['note'])) : ?>
                      <span class="github-repo-card__note"><?= portfolio_h((string) $repo['note']) ?></span>
                    <?php endif; ?>
                  </a>
                  <?php if (! empty($repo['demo'])) : ?>
                    <a class="github-repo-card__demo" href="<?= portfolio_h((string) $repo['demo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_demo')) ?></a>
                  <?php endif; ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>

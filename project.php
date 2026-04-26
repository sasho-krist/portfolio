<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
portfolio_load_dotenv(__DIR__ . '/.env');
require __DIR__ . '/includes/seo.php';
require __DIR__ . '/includes/i18n.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
portfolio_i18n_init();

$profile = require __DIR__ . '/data/profile.php';
$projects = require __DIR__ . '/data/projects.php';
$projectSlug = trim((string) ($_GET['slug'] ?? ''));
$project = portfolio_project_by_slug($projects, $projectSlug);

if ($project === null) {
    $langParam = portfolio_lang() === 'en' ? '?lang=en' : '';
    header('Location: index.php' . $langParam);
    exit;
}

$projectName = (string) ($project['name'] ?? 'Project');
$projectDesc = trim((string) ($project['desc'] ?? ''));
$pageTitle = $projectName . ' | ' . $profile['name'];
$pageDescription = $projectDesc !== '' ? $projectDesc : portfolio_profile_text($profile, 'tagline');
$canonicalUrl = portfolio_canonical_url();
$metaKeywords = $projectName . ', ' . portfolio_seo_keywords_string($profile);
$jsonLd = portfolio_seo_json_ld($profile, $canonicalUrl, null, $pageTitle, $pageDescription);
$ogImageUrl = null;
$navShowCases = false;
$navShowTestimonials = false;
$footerHomeHref = 'index.php';
$navHrefPrefix = 'index.php';

$projectShots = array_values(array_filter(
    portfolio_project_screenshots($project),
    static fn (array $s): bool => portfolio_is_valid_project_image_url($s['url'])
));

require __DIR__ . '/includes/header.php';
?>
<section class="project-page">
  <div class="container">
    <div class="project-page__back-wrap">
      <a class="btn" href="index.php<?= portfolio_lang() === 'en' ? '?lang=en' : '' ?>#projects">← <?= portfolio_h(portfolio_lang() === 'en' ? 'Back to projects' : 'Назад към проектите') ?></a>
    </div>
    <article class="card project-page__card">
      <h1 class="project-page__title"><?= portfolio_h($projectName) ?></h1>
      <?php if (! empty($project['pills']) && is_array($project['pills'])) : ?>
      <div class="project-meta">
        <?php foreach ($project['pills'] as $pill) : ?>
        <span class="pill"><?= portfolio_h((string) $pill) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php if ($projectDesc !== '') : ?>
      <p class="project-page__lead"><?= portfolio_h($projectDesc) ?></p>
      <?php endif; ?>
      <?php if (! empty($project['readme_excerpt'])) : ?>
      <p class="project-readme"><?= portfolio_h((string) $project['readme_excerpt']) ?></p>
      <?php endif; ?>

      <?php if (! empty($project['problem']) || ! empty($project['my_role']) || ! empty($project['challenge']) || ! empty($project['solution'])) : ?>
      <dl class="project-detail project-page__detail">
        <?php if (! empty($project['problem'])) : ?>
        <dt><?= portfolio_h(portfolio_t('projects_detail_problem')) ?></dt>
        <dd><?= portfolio_h((string) $project['problem']) ?></dd>
        <?php endif; ?>
        <?php if (! empty($project['my_role'])) : ?>
        <dt><?= portfolio_h(portfolio_t('projects_detail_role')) ?></dt>
        <dd><?= portfolio_h((string) $project['my_role']) ?></dd>
        <?php endif; ?>
        <?php if (! empty($project['challenge'])) : ?>
        <dt><?= portfolio_h(portfolio_t('projects_detail_challenge')) ?></dt>
        <dd><?= portfolio_h((string) $project['challenge']) ?></dd>
        <?php endif; ?>
        <?php if (! empty($project['solution'])) : ?>
        <dt><?= portfolio_h(portfolio_t('projects_detail_solution')) ?></dt>
        <dd><?= portfolio_h((string) $project['solution']) ?></dd>
        <?php endif; ?>
      </dl>
      <?php endif; ?>

      <?php if ($projectShots !== []) : ?>
      <div class="project-shots project-page__shots" aria-label="<?= portfolio_h(portfolio_t('projects_shots_aria')) ?>">
        <?php foreach ($projectShots as $si => $shot) : ?>
        <?php
          $alt = $shot['caption'] !== ''
              ? $shot['caption']
              : ($projectName . ' — screenshot ' . (string) ($si + 1));
        ?>
        <figure class="project-shot">
          <a href="<?= portfolio_h($shot['url']) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= portfolio_h($shot['url']) ?>" alt="<?= portfolio_h($alt) ?>" width="640" height="360" loading="lazy" decoding="async" />
          </a>
          <?php if ($shot['caption'] !== '') : ?>
          <figcaption class="project-shot-cap"><?= portfolio_h($shot['caption']) ?></figcaption>
          <?php endif; ?>
        </figure>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="project-actions">
        <a class="btn btn-primary" href="<?= portfolio_h((string) $project['repo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_github')) ?></a>
        <?php if (! empty($project['demo'])) : ?>
        <a class="btn" href="<?= portfolio_h((string) $project['demo']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('projects_demo')) ?></a>
        <?php endif; ?>
      </div>
    </article>
  </div>
</section>
<?php
require __DIR__ . '/includes/footer.php';

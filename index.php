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
$_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
$csrfContactToken = $_SESSION['csrf_contact'];

$profile = require __DIR__ . '/data/profile.php';
$calendarOverride = trim((string) (getenv('CALENDAR_URL') ?: ''));
if ($calendarOverride !== '' && filter_var($calendarOverride, FILTER_VALIDATE_URL)) {
    $profile['calendar'] = $calendarOverride;
}
$projects = require __DIR__ . '/data/projects.php';
$cases = require __DIR__ . '/data/cases.php';
$projectsCount = count($projects);
$resumeUrl = isset($profile['resume_url']) ? trim((string) $profile['resume_url']) : '';
$resumePath = $resumeUrl !== '' ? portfolio_base_path($resumeUrl) : '';
$hasResume = $resumeUrl !== '' && is_readable($resumePath);

$contactFlash = match ($_GET['contact'] ?? '') {
    'sent' => ['ok' => true, 'text' => portfolio_t('contact_flash_sent')],
    'fail' => ['ok' => false, 'text' => sprintf(portfolio_t('contact_flash_fail'), $profile['email'])],
    'invalid' => ['ok' => false, 'text' => portfolio_t('contact_flash_invalid')],
    default => null,
};

$pageTitle = portfolio_profile_text($profile, 'seo_title') !== ''
    ? portfolio_profile_text($profile, 'seo_title')
    : ($profile['name'] . ' | PHP & Laravel · Sofia');
$pageDescription = portfolio_profile_text($profile, 'seo_description') !== ''
    ? portfolio_profile_text($profile, 'seo_description')
    : portfolio_profile_text($profile, 'tagline');

$galleryImages = portfolio_gallery_images();
$dogGalleryImages = portfolio_images_in_subdir('images/dogs');

$profilePhotoPath = portfolio_base_path('images/alexander.jpg');
$hasProfilePhoto = is_file($profilePhotoPath) && is_readable($profilePhotoPath);

$canonicalUrl = portfolio_canonical_url();
$ogImageUrl = $hasProfilePhoto
    ? rtrim($canonicalUrl, '/') . '/images/alexander.jpg'
    : null;
$metaKeywords = portfolio_seo_keywords_string($profile);
$jsonLd = portfolio_seo_json_ld($profile, $canonicalUrl, $ogImageUrl, $pageTitle, $pageDescription);

$navShowCases = $cases !== [];
$navShowTestimonials = ($profile['testimonials'] ?? []) !== [];
$plausibleDomain = trim((string) (getenv('PLAUSIBLE_DOMAIN') ?: ''));
$footerHomeHref = 'index.php';

$erpCaptionsBg = [
    'Dashboard — ключови показатели',
    'Профил, известия и лични данни',
    'HR — график и смени',
    'Отпуски и документи',
    'Payroll — фишове и изчисления',
    'Обучения и тестове',
    'Админ — уроци и въпроси',
    'Склад — търсене и наличности',
    'Warehouse fill-rate и експорти',
    'Продуктов каталог',
    'Ценови листи по магазини',
    'PRIM — продажби',
    'PRIM — наличности',
    'Магазини и смени',
    'POS и касови сметки',
    'Sales funnel / CRM',
    'Администрация — роли и права',
];
$erpCaptionsEn = [
    'Dashboard — key metrics',
    'Profile, notifications and personal data',
    'HR — schedule and shifts',
    'Leave and documents',
    'Payroll — payslips and calculations',
    'Training and tests',
    'Admin — lessons and questions',
    'Warehouse — search and stock',
    'Warehouse fill-rate and exports',
    'Product catalogue',
    'Price lists by store',
    'PRIM — sales',
    'PRIM — stock',
    'Stores and shifts',
    'POS and receipts',
    'Sales funnel / CRM',
    'Administration — roles and permissions',
];
$erpCaptions = portfolio_lang() === 'en' ? $erpCaptionsEn : $erpCaptionsBg;

require __DIR__ . '/includes/header.php';
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
                  decoding="async"
                />
              </div>
            </div>
          <?php endif; ?>
          <div class="hero-copy">
            <span class="tag"><span class="tag-dot" aria-hidden="true"></span> <?= portfolio_h(portfolio_t('hero_tag')) ?></span>
            <h1><?= portfolio_h($profile['title']) ?></h1>
            <p class="hero-lead"><?= portfolio_h(portfolio_profile_text($profile, 'tagline')) ?></p>
            <div class="hero-actions">
              <a class="btn btn-primary" href="#projects"><?= portfolio_h(portfolio_t('hero_view_projects')) ?></a>
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

    <section id="about">
      <div class="container">
        <h2 class="section-title"><?= portfolio_h(portfolio_t('about_title')) ?></h2>
        <p class="section-intro"><?= portfolio_h(portfolio_profile_text($profile, 'profile')) ?></p>
        <?php if (portfolio_profile_array($profile, 'about_bullets') !== []) : ?>
          <div class="card about-highlights">
            <h3 class="about-highlights__title"><?= portfolio_h(portfolio_t('about_summary')) ?></h3>
            <ul class="about-highlights__list">
              <?php foreach (portfolio_profile_array($profile, 'about_bullets') as $bullet) : ?>
                <li><?= portfolio_h((string) $bullet) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <div class="two-cols">
          <div class="card">
            <h3><?= portfolio_h(portfolio_t('about_career')) ?></h3>
            <div class="timeline">
              <?php foreach ($profile['experience'] as $job) : ?>
                <article class="timeline-item">
                  <h3><?= portfolio_h($job['company']) ?></h3>
                  <div class="timeline-meta"><?= portfolio_h($job['role']) ?> · <?= portfolio_h($job['period']) ?></div>
                  <p class="muted" style="margin:0"><?= portfolio_h($job['desc']) ?></p>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="card">
            <h3><?= portfolio_h(portfolio_t('about_education')) ?></h3>
            <?php foreach ($profile['education'] as $edu) : ?>
              <article style="margin-bottom:1.25rem">
                <strong><?= portfolio_h($edu['period']) ?></strong>
                <p style="margin:0.35rem 0 0"><?= portfolio_h($edu['degree']) ?></p>
                <p class="muted" style="margin:0.25rem 0 0"><?= portfolio_h($edu['school']) ?></p>
                <ul class="muted" style="margin:0.5rem 0 0;font-size:0.92rem">
                  <?php foreach ($edu['details'] as $d) : ?>
                    <li><?= portfolio_h($d) ?></li>
                  <?php endforeach; ?>
                </ul>
              </article>
            <?php endforeach; ?>
            <h3 style="margin-top:1.5rem"><?= portfolio_h(portfolio_t('about_languages')) ?> <span class="muted" style="font-weight:500;font-size:0.88em">· Languages</span></h3>
            <ul class="muted" style="margin:0">
              <?php foreach ($profile['languages'] as $lang => $level) : ?>
                <li><strong><?= portfolio_h($lang) ?></strong> — <?= portfolio_h($level) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="card" style="margin-top:18px">
          <h3><?= portfolio_h(portfolio_t('about_interests')) ?> <span class="muted" style="font-weight:500;font-size:0.88em">· Interests</span></h3>
          <div class="chip-row">
            <?php foreach ($profile['interests'] as $interest) : ?>
              <?php
                $chipLabel = is_array($interest) ? portfolio_interest_label($interest) : (string) $interest;
                $chipHref = is_array($interest) ? ($interest['href'] ?? null) : null;
                if ($chipHref === '#dogs' && $dogGalleryImages === []) {
                    $chipHref = null;
                }
              ?>
              <?php if ($chipHref) : ?>
                <a class="chip chip-link" href="<?= portfolio_h((string) $chipHref) ?>"><?= portfolio_h($chipLabel) ?></a>
              <?php else : ?>
                <span class="chip"><?= portfolio_h($chipLabel) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

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

    <?php if ($cases !== []) : ?>
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
    <?php endif; ?>

    <?php if ($navShowTestimonials) : ?>
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
    <?php endif; ?>

    <?php if ($dogGalleryImages !== []) : ?>
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
    <?php endif; ?>

    <section id="contact">
      <div class="container">
        <h2 class="section-title"><?= portfolio_h(portfolio_t('contact_title')) ?></h2>
        <div class="contact-grid">
          <div class="card">
            <?php if ($contactFlash !== null) : ?>
              <div class="contact-flash <?= $contactFlash['ok'] ? 'contact-flash--ok' : 'contact-flash--err' ?>" role="status">
                <?= portfolio_h($contactFlash['text']) ?>
              </div>
            <?php endif; ?>
            <form id="contactForm" class="contact-form" action="contact-send.php" method="post" accept-charset="UTF-8" aria-describedby="contact-privacy-note">
              <input type="hidden" name="csrf" value="<?= portfolio_h($csrfContactToken) ?>" />
              <div class="field-honeypot" aria-hidden="true">
                <label for="website"><?= portfolio_h(portfolio_t('contact_honeypot')) ?></label>
                <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off" />
              </div>
              <div class="field">
                <label for="sender_name"><?= portfolio_h(portfolio_t('contact_name')) ?></label>
                <input id="sender_name" name="sender_name" type="text" required autocomplete="name" placeholder="<?= portfolio_h(portfolio_t('contact_name_placeholder')) ?>" maxlength="200" />
              </div>
              <div class="field">
                <label for="sender_email"><?= portfolio_h(portfolio_t('contact_email_label')) ?></label>
                <input id="sender_email" name="sender_email" type="email" required autocomplete="email" placeholder="you@example.com" maxlength="254" />
              </div>
              <div class="field">
                <label for="sender_message"><?= portfolio_h(portfolio_t('contact_message')) ?></label>
                <textarea id="sender_message" name="sender_message" required placeholder="<?= portfolio_h(portfolio_t('contact_placeholder')) ?>" maxlength="8000"></textarea>
              </div>
              <p id="contact-privacy-note" class="contact-privacy">
                <?= portfolio_h(portfolio_t('contact_privacy')) ?>
                <a href="privacy.php<?= portfolio_lang() === 'en' ? '?lang=en' : '' ?>"><?= portfolio_h(portfolio_t('contact_privacy_link')) ?></a>.
              </p>
              <button type="submit" class="btn btn-primary"><?= portfolio_h(portfolio_t('contact_submit')) ?></button>
            </form>
          </div>
          <div class="card">
            <h3 style="margin-top:0"><?= portfolio_h(portfolio_t('contact_direct')) ?></h3>
            <p><strong><?= portfolio_h(portfolio_t('contact_email')) ?></strong> <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_phone')) ?></strong> <a href="tel:<?= portfolio_h(preg_replace('/\s+/', '', $profile['phone'])) ?>"><?= portfolio_h($profile['phone']) ?></a></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_location')) ?></strong> <?= portfolio_h(portfolio_profile_text($profile, 'location')) ?></p>
            <p><strong><?= portfolio_h(portfolio_t('contact_github')) ?></strong> <a href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer">@sashokrist</a></p>
            <?php if (! empty($profile['site_repo_url']) && is_string($profile['site_repo_url']) && filter_var($profile['site_repo_url'], FILTER_VALIDATE_URL)) : ?>
              <?php
                $repoPath = trim((string) parse_url($profile['site_repo_url'], PHP_URL_PATH), '/');
                $repoLabel = $repoPath !== '' ? $repoPath : 'GitHub';
              ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_site_repo')) ?></strong> <a href="<?= portfolio_h($profile['site_repo_url']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h($repoLabel) ?></a></p>
            <?php endif; ?>
            <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_linkedin')) ?></strong> <a href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('contact_linkedin_profile')) ?></a></p>
            <?php endif; ?>
            <?php if ($hasResume) : ?>
              <p><strong><?= portfolio_h(portfolio_t('contact_cv')) ?></strong> <a href="<?= portfolio_h($resumeUrl) ?>" download="<?= portfolio_h(basename($resumeUrl)) ?>"><?= portfolio_h(portfolio_t('contact_cv_download')) ?></a></p>
            <?php endif; ?>
            <p class="muted" style="margin-bottom:0">
              <a class="btn btn-primary" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h(portfolio_t('contact_calendar')) ?></a>
            </p>
          </div>
        </div>
      </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';

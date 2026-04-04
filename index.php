<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
portfolio_load_dotenv(__DIR__ . '/.env');
require __DIR__ . '/includes/seo.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
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
    'sent' => ['ok' => true, 'text' => 'Съобщението е изпратено до пощата. Ще отговоря възможно най-скоро.'],
    'fail' => ['ok' => false, 'text' => 'Изпращането не успя (SMTP и PHP mail() на хостинга). Виж logs/mail-last-error.txt и error_log. Провери vendor/, .env, дали хостингът позволява изходящ SMTP. Изключи fallback: MAIL_FALLBACK_PHP_MAIL=0. Пиши на ' . $profile['email'] . '.'],
    'invalid' => ['ok' => false, 'text' => 'Провери полетата и опитай отново.'],
    default => null,
};

$pageTitle = (string) ($profile['seo_title'] ?? ($profile['name'] . ' | PHP & Laravel · Sofia'));
$pageDescription = (string) ($profile['seo_description'] ?? $profile['tagline']);

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

$erpCaptions = [
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
            <span class="tag"><span class="tag-dot" aria-hidden="true"></span> Отворен за backend и full stack проекти</span>
            <h1><?= portfolio_h($profile['title']) ?></h1>
            <p class="hero-lead"><?= portfolio_h($profile['tagline']) ?></p>
            <div class="hero-actions">
              <a class="btn btn-primary" href="#projects">Виж проектите</a>
              <a class="btn" href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
              <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
                <a class="btn" href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
              <?php endif; ?>
              <a class="btn" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer">Насрочи среща</a>
            </div>
            <p class="hero-stats" aria-label="Показатели">
              <span><strong><?= (int) $projectsCount ?></strong> публични проекта в GitHub</span>
              <span class="hero-stats__sep" aria-hidden="true">·</span>
              <span><strong><?= portfolio_h((string) ($profile['years_experience'] ?? '7')) ?></strong> години опит</span>
            </p>
          </div>
        </div>
        <aside class="card quick-facts">
          <h2>Накратко</h2>
          <ul>
            <?php foreach ($profile['quick_facts'] as $fact) : ?>
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
            <h2 class="section-title" style="margin-bottom:0">Проекти</h2>
          </div>
        </div>
        <div class="projects-grid" aria-label="Проекти в изглед карти">
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
                    <dt>Проблем / контекст</dt>
                    <dd><?= portfolio_h((string) $p['problem']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['my_role'])) : ?>
                    <dt>Моята роля</dt>
                    <dd><?= portfolio_h((string) $p['my_role']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['challenge'])) : ?>
                    <dt>Предизвикателство</dt>
                    <dd><?= portfolio_h((string) $p['challenge']) ?></dd>
                  <?php endif; ?>
                  <?php if (! empty($p['solution'])) : ?>
                    <dt>Подход / решение</dt>
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
                <div class="project-shots" aria-label="Снимки от GitHub README">
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
                <a class="btn btn-primary" href="<?= portfolio_h($p['repo']) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                <?php if (! empty($p['demo'])) : ?>
                  <a class="btn" href="<?= portfolio_h((string) $p['demo']) ?>" target="_blank" rel="noopener noreferrer">Демо</a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="projects-table-wrap" aria-label="Проекти в табличен изглед">
          <table class="projects-table">
            <thead>
              <tr>
                <th>Проект</th>
                <th>Технологии</th>
                <th>Описание и контекст</th>
                <th>Връзки</th>
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
                        <a href="#project-<?= portfolio_h($p['slug']) ?>"><?= (int) $shotCount ?> снимки в изглед карти</a>
                      </div>
                    <?php endif; ?>
                    <?php
                      $bits = [];
                      if (! empty($p['problem'])) {
                          $bits[] = 'Проблем: ' . $p['problem'];
                      }
                      if (! empty($p['my_role'])) {
                          $bits[] = 'Роля: ' . $p['my_role'];
                      }
                      if (! empty($p['challenge']) && ! empty($p['solution'])) {
                          $bits[] = 'Предизвикателство → решение: ' . $p['challenge'] . ' → ' . $p['solution'];
                      } elseif (! empty($p['challenge'])) {
                          $bits[] = 'Предизвикателство: ' . $p['challenge'];
                      }
                      if ($bits !== []) {
                          echo '<div class="projects-table-detail muted">' . portfolio_h(implode(' ', $bits)) . '</div>';
                      }
                    ?>
                  </td>
                  <td>
                    <a href="<?= portfolio_h($p['repo']) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                    <?php if (! empty($p['demo'])) : ?>
                      · <a href="<?= portfolio_h((string) $p['demo']) ?>" target="_blank" rel="noopener noreferrer">Демо</a>
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
        <h2 id="skills-heading" class="section-title">Умения и технологии</h2>
        <div class="tech-stack-grid tech-stack-grid--merged">
          <?php foreach (($profile['skills_cards'] ?? []) as $label => $text) : ?>
            <article class="card tech-stack-card">
              <h3 class="tech-stack-card__title"><?= portfolio_h((string) $label) ?></h3>
              <p class="tech-stack-card__body"><?= portfolio_h((string) $text) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
        <?php if (($profile['services'] ?? []) !== []) : ?>
          <div class="card services-card">
            <h3 class="services-card__title">Услуги</h3>
            <ul class="services-list">
              <?php foreach ($profile['services'] as $service) : ?>
                <li><?= portfolio_h((string) $service) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="about">
      <div class="container">
        <h2 class="section-title">За мен</h2>
        <p class="section-intro"><?= portfolio_h($profile['profile']) ?></p>
        <?php if (($profile['about_bullets'] ?? []) !== []) : ?>
          <div class="card about-highlights">
            <h3 class="about-highlights__title">Накратко</h3>
            <ul class="about-highlights__list">
              <?php foreach ($profile['about_bullets'] as $bullet) : ?>
                <li><?= portfolio_h((string) $bullet) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <div class="two-cols">
          <div class="card">
            <h3>Кариерен път</h3>
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
            <h3>Образование</h3>
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
            <h3 style="margin-top:1.5rem">Езици <span class="muted" style="font-weight:500;font-size:0.88em">· Languages</span></h3>
            <ul class="muted" style="margin:0">
              <?php foreach ($profile['languages'] as $lang => $level) : ?>
                <li><strong><?= portfolio_h($lang) ?></strong> — <?= portfolio_h($level) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="card" style="margin-top:18px">
          <h3>Интереси <span class="muted" style="font-weight:500;font-size:0.88em">· Interests</span></h3>
          <div class="chip-row">
            <?php foreach ($profile['interests'] as $interest) : ?>
              <?php
                $chipLabel = is_array($interest) ? (string) ($interest['label'] ?? '') : (string) $interest;
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
        <h2 class="section-title">BioMarket ERP</h2>
        <p class="section-intro">
          Вътрешна уеб система за веригата BioMarket / HealthStore — Laravel, HR, склад, каталог, POS, PRIM интеграции и още.
        </p>
        <div class="erp-banner">
          <strong>Важно:</strong>
          <span class="muted"> Репозиторият е за вътрешна употреба. Няма публичен линк към кода; показвам модулите и екрани чрез галерия.</span>
        </div>
        <?php if ($galleryImages !== []) : ?>
          <h3 class="section-title" style="font-size:1.2rem;margin-top:8px">Галерия от системата</h3>
          <p class="section-intro" style="margin-top:0">Клик за увеличение · стрелки или клавиатура в lightbox</p>
          <div class="gallery-grid">
            <?php foreach ($galleryImages as $i => $src) : ?>
              <?php
                $caption = $erpCaptions[$i] ?? ('BioMarket ERP — екран ' . (string) ($i + 1));
              ?>
              <figure
                class="gallery-item"
                data-lightbox-group="erp"
                data-lightbox-index="<?= (int) $i ?>"
                data-full="<?= portfolio_h($src) ?>"
                data-caption="<?= portfolio_h($caption) ?>"
                role="button"
                tabindex="0"
                aria-label="Отвори: <?= portfolio_h($caption) ?>"
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
        <h2 id="cases-heading" class="section-title">Избрани кейсове</h2>
        <p class="section-intro">Контекст, подход и резултат — без излишни детайли, с фокус върху стойността за бизнеса.</p>
        <div class="cases-grid">
          <?php foreach ($cases as $c) : ?>
            <article class="card case-card" id="case-<?= portfolio_h($c['slug']) ?>">
              <h3><?= portfolio_h($c['title']) ?></h3>
              <p class="case-context muted"><?= portfolio_h($c['context']) ?></p>
              <div class="case-block">
                <h4 class="case-label">Предизвикателство</h4>
                <p><?= portfolio_h($c['problem']) ?></p>
              </div>
              <div class="case-block">
                <h4 class="case-label">Подход</h4>
                <p><?= portfolio_h($c['approach']) ?></p>
              </div>
              <p class="case-stack"><strong>Технологии:</strong> <?= portfolio_h(implode(' · ', $c['stack'])) ?></p>
              <div class="case-outcome">
                <strong>Резултат</strong>
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
        <h2 id="testimonials-heading" class="section-title">Препоръки</h2>
        <p class="section-intro">Отзиви от колеги и партньори по проекти.</p>
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
        <h2 id="dogs-heading" class="section-title">Моите кучета</h2>
        <p class="section-intro">Клик за увеличение</p>
        <div class="gallery-grid gallery-grid--dogs">
          <?php foreach ($dogGalleryImages as $i => $src) : ?>
            <?php
              $fileBase = pathinfo($src, PATHINFO_FILENAME);
              $caption = $fileBase !== '' ? (string) preg_replace('/[-_]+/', ' ', $fileBase) : 'Снимка ' . (string) ($i + 1);
            ?>
            <figure
              class="gallery-item gallery-item--dog"
              data-lightbox-group="dogs"
              data-lightbox-index="<?= (int) $i ?>"
              data-full="<?= portfolio_h($src) ?>"
              data-caption="<?= portfolio_h($caption) ?>"
              role="button"
              tabindex="0"
              aria-label="Отвори: <?= portfolio_h($caption) ?>"
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
        <h2 class="section-title">Контакти</h2>
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
                <label for="website">Не попълвай</label>
                <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off" />
              </div>
              <div class="field">
                <label for="sender_name">Име</label>
                <input id="sender_name" name="sender_name" type="text" required autocomplete="name" placeholder="Вашето име" maxlength="200" />
              </div>
              <div class="field">
                <label for="sender_email">Имейл</label>
                <input id="sender_email" name="sender_email" type="email" required autocomplete="email" placeholder="you@example.com" maxlength="254" />
              </div>
              <div class="field">
                <label for="sender_message">Съобщение</label>
                <textarea id="sender_message" name="sender_message" required placeholder="Здравей, Александър…" maxlength="8000"></textarea>
              </div>
              <p id="contact-privacy-note" class="contact-privacy">
                Изпращайки формата, приемаш обработката на личните данни само за отговор на запитването.
                <a href="privacy.php">Политика за поверителност</a>.
              </p>
              <button type="submit" class="btn btn-primary">Изпрати</button>
            </form>
          </div>
          <div class="card">
            <h3 style="margin-top:0">Директно</h3>
            <p><strong>Имейл:</strong> <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a></p>
            <p><strong>Телефон:</strong> <a href="tel:<?= portfolio_h(preg_replace('/\s+/', '', $profile['phone'])) ?>"><?= portfolio_h($profile['phone']) ?></a></p>
            <p><strong>Локация:</strong> <?= portfolio_h($profile['location']) ?></p>
            <p><strong>GitHub:</strong> <a href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer">@sashokrist</a></p>
            <?php if (! empty($profile['site_repo_url']) && is_string($profile['site_repo_url']) && filter_var($profile['site_repo_url'], FILTER_VALIDATE_URL)) : ?>
              <?php
                $repoPath = trim((string) parse_url($profile['site_repo_url'], PHP_URL_PATH), '/');
                $repoLabel = $repoPath !== '' ? $repoPath : 'GitHub';
              ?>
              <p><strong>This site repo:</strong> <a href="<?= portfolio_h($profile['site_repo_url']) ?>" target="_blank" rel="noopener noreferrer"><?= portfolio_h($repoLabel) ?></a></p>
            <?php endif; ?>
            <?php if (! empty($profile['linkedin']) && is_string($profile['linkedin']) && filter_var($profile['linkedin'], FILTER_VALIDATE_URL)) : ?>
              <p><strong>LinkedIn:</strong> <a href="<?= portfolio_h($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer">Профил</a></p>
            <?php endif; ?>
            <?php if ($hasResume) : ?>
              <p><strong>CV:</strong> <a href="<?= portfolio_h($resumeUrl) ?>" download="<?= portfolio_h(basename($resumeUrl)) ?>">Свали PDF</a></p>
            <?php endif; ?>
            <p class="muted" style="margin-bottom:0">
              <a class="btn btn-primary" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer">Google Calendar</a>
            </p>
          </div>
        </div>
      </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';

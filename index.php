<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
$csrfContactToken = $_SESSION['csrf_contact'];

$profile = require __DIR__ . '/data/profile.php';
$projects = require __DIR__ . '/data/projects.php';

$contactFlash = match ($_GET['contact'] ?? '') {
    'sent' => ['ok' => true, 'text' => 'Съобщението е изпратено до пощата. Ще отговоря възможно най-скоро.'],
    'fail' => ['ok' => false, 'text' => 'Изпращането не успя (SMTP и PHP mail() на хостинга). Виж logs/mail-last-error.txt и error_log. Провери vendor/, .env, дали хостингът позволява изходящ SMTP. Изключи fallback: MAIL_FALLBACK_PHP_MAIL=0. Пиши на ' . $profile['email'] . '.'],
    'invalid' => ['ok' => false, 'text' => 'Провери полетата и опитай отново.'],
    default => null,
};

$pageTitle = $profile['name'] . ' — Портфолио';
$pageDescription = $profile['tagline'];

$erpDocPath = portfolio_base_path('docs/BIOMARKET-ERP.md');
$erpReadmeRaw = is_readable($erpDocPath) ? (string) file_get_contents($erpDocPath) : '';
$erpReadmeHtml = $erpReadmeRaw !== '' ? portfolio_markdown_to_html($erpReadmeRaw) : '<p class="muted">docs/BIOMARKET-ERP.md не е намерен.</p>';

$galleryImages = portfolio_gallery_images();
$dogGalleryImages = portfolio_images_in_subdir('images/dogs');

$profilePhotoPath = portfolio_base_path('images/alexander.jpg');
$hasProfilePhoto = is_file($profilePhotoPath) && is_readable($profilePhotoPath);

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

    <section class="hero">
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
              <a class="btn" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer">Насрочи среща</a>
            </div>
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

    <section id="about">
      <div class="container">
        <h2 class="section-title">За мен</h2>
        <p class="section-intro"><?= portfolio_h($profile['profile']) ?></p>
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
          <h3>Технически умения <span class="muted" style="font-weight:500;font-size:0.88em">· Technical Skills</span></h3>
          <div class="skills-grid">
            <?php foreach ($profile['skills'] as $label => $value) : ?>
              <div class="skill-pill">
                <strong><?= portfolio_h($label) ?></strong>
                <?= portfolio_h($value) ?>
              </div>
            <?php endforeach; ?>
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

    <?php if ($dogGalleryImages !== []) : ?>
    <section id="dogs" class="dogs-section" aria-labelledby="dogs-heading">
      <div class="container">
        <h2 id="dogs-heading" class="section-title">Моите кучета</h2>
        <p class="section-intro">Снимки от папка <code class="inline-code">images/dogs</code> · клик за увеличение</p>
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

    <section id="erp">
      <div class="container">
        <h2 class="section-title">BioMarket ERP</h2>
        <p class="section-intro">
          Вътрешна уеб система за веригата BioMarket / HealthStore — Laravel, HR, склад, каталог, POS, PRIM интеграции и още.
          Описанието по-долу е от docs/BIOMARKET-ERP.md (вътрешна документация за Laravel ERP).
        </p>
        <div class="erp-banner">
          <strong>Важно:</strong>
          <span class="muted"> Репозиторият е за вътрешна употреба. Няма публичен линк към кода; показвам модулите и екрани чрез галерия.</span>
        </div>
        <details class="readme-details card">
          <summary>Пълна документация (markdown)</summary>
          <div class="readme-body"><?= $erpReadmeHtml ?></div>
        </details>
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

    <section id="projects">
      <div class="container">
        <div class="projects-toolbar">
          <div>
            <h2 class="section-title" style="margin-bottom:4px">Проекти</h2>
            <p class="section-intro" style="margin:0">Избрани GitHub репозиторита · превключване карти / таблица отгоре в навигацията</p>
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
              <div class="project-actions">
                <a class="btn btn-primary" href="<?= portfolio_h($p['repo']) ?>" target="_blank" rel="noopener noreferrer">Репозиторий</a>
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
                <th>Описание</th>
                <th>Връзки</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($projects as $p) : ?>
                <tr>
                  <td><strong><?= portfolio_h($p['name']) ?></strong></td>
                  <td><?= portfolio_h(implode(', ', $p['pills'])) ?></td>
                  <td><?= portfolio_h($p['desc']) ?></td>
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
            <form id="contactForm" class="contact-form" action="contact-send.php" method="post" accept-charset="UTF-8">
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
              <button type="submit" class="btn btn-primary">Изпрати</button>
            </form>
          </div>
          <div class="card">
            <h3 style="margin-top:0">Директно</h3>
            <p><strong>Имейл:</strong> <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a></p>
            <p><strong>Телефон:</strong> <a href="tel:<?= portfolio_h(preg_replace('/\s+/', '', $profile['phone'])) ?>"><?= portfolio_h($profile['phone']) ?></a></p>
            <p><strong>Локация:</strong> <?= portfolio_h($profile['location']) ?></p>
            <p><strong>GitHub:</strong> <a href="<?= portfolio_h($profile['github']) ?>" target="_blank" rel="noopener noreferrer">@sashokrist</a></p>
            <p class="muted" style="margin-bottom:0">
              <a class="btn btn-primary" href="<?= portfolio_h($profile['calendar']) ?>" target="_blank" rel="noopener noreferrer">Google Calendar</a>
            </p>
          </div>
        </div>
      </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';

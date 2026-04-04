<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
portfolio_load_dotenv(__DIR__ . '/.env');
require __DIR__ . '/includes/seo.php';

$profile = require __DIR__ . '/data/profile.php';
$cases = require __DIR__ . '/data/cases.php';
$dogGalleryImages = portfolio_images_in_subdir('images/dogs');

$pageTitle = 'Поверителност — ' . $profile['name'];
$pageDescription = 'Как се обработват личните данни при контакт чрез формата на портфолиото и какви са правата ви по GDPR.';
$canonicalUrl = rtrim(portfolio_canonical_url(), '/') . '/privacy.php';
$ogImageUrl = null;
$metaKeywords = 'поверителност, GDPR, лични данни, контактна форма';
$jsonLd = portfolio_seo_json_ld_webpage($pageTitle, $pageDescription, $canonicalUrl);
$robotsMeta = 'noindex, follow';
$navHrefPrefix = 'index.php';
$navShowCases = $cases !== [];
$navShowTestimonials = ($profile['testimonials'] ?? []) !== [];
$plausibleDomain = trim((string) (getenv('PLAUSIBLE_DOMAIN') ?: ''));
$footerHomeHref = 'index.php';

require __DIR__ . '/includes/header.php';
?>

    <article class="legal-page container">
      <header class="legal-header">
        <h1>Политика за поверителност</h1>
        <p class="muted">Последна актуализация: <?= date('Y-m-d') ?> · Приложима към този уеб сайт и контактната форма.</p>
      </header>

      <div class="legal-body">
        <h2>Администратор на данните</h2>
        <p>
          Администратор по смисъла на Регламент (ЕС) 2016/679 (GDPR) за данните, събирани чрез този сайт, е
          <strong><?= portfolio_h($profile['name']) ?></strong>, достъпен на
          <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a>.
        </p>

        <h2>Какви данни събираме</h2>
        <ul>
          <li><strong>Контактна форма:</strong> име, имейл адрес и текст на съобщението, които изпращате доброволно.</li>
          <li><strong>Технически данни:</strong> стандартни сървърни логове (напр. IP адрес, потребителски агент, време на заявката), ако хостингът ги записва — според настройките на доставчика.</li>
        </ul>

        <h2>Цели и правно основание</h2>
        <p>
          Данните от формата се обработват, за да отговоря на вашето запитване (легитимен интерес / преддоговорни мерки по чл. 6, ал. 1, б. „б“ и „ф“ от GDPR).
          Не използвам данните за автоматизирано профилиране и не ги продавам на трети страни.
        </p>

        <h2>Срок на съхранение</h2>
        <p>
          Съобщенията се пазят само колкото е нужно за комуникацията и евентуално за доказване на кореспонденцията (обикновено до няколко месеца, освен ако законът не изисква по-дълъг срок).
        </p>

        <h2>Споделяне</h2>
        <p>
          Имейлът може да премине през доставчик на хостинг и (ако е настроено) през SMTP услуга за изпращане — само за доставка на съобщението.
        </p>

        <h2>Бисквитки и аналитика</h2>
        <p>
          Сайтът може да ползва функционални настройки (напр. тема) в браузъра. Ако бъде включена уеб аналитика (напр. Plausible), тя е конфигурирана с минимални данни; проверете настройките на домейна при доставчика на аналитиката.
        </p>

        <h2>Вашите права</h2>
        <p>
          Имате право на достъп, коригиране, изтриване, ограничаване на обработката, възражение и преносимост на данните, когато е приложимо, както и право на жалба до надзорен орган (в България: КЗЛД).
        </p>

        <h2>Контакт</h2>
        <p>
          За въпроси относно тази политика: <a href="mailto:<?= portfolio_h($profile['email']) ?>"><?= portfolio_h($profile['email']) ?></a>.
        </p>

        <p class="legal-back">
          <a class="btn btn-primary" href="index.php">← Към портфолиото</a>
        </p>
      </div>
    </article>

<?php
require __DIR__ . '/includes/footer.php';

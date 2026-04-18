<?php

declare(strict_types=1);

/**
 * Front controller: данни и настройки → partial изгледи (includes/partials/).
 */

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

$profileHeroImagePath = portfolio_base_path('images/logo' . DIRECTORY_SEPARATOR . 'Sasho Dev 1-01.png');
$profileHeroImageSrc = 'images/logo/' . rawurlencode('Sasho Dev 1-01.png');
$hasProfilePhoto = is_file($profileHeroImagePath) && is_readable($profileHeroImagePath);

$ogImageWidth = 512;
$ogImageHeight = 512;
if ($hasProfilePhoto) {
    $heroImgSize = @getimagesize($profileHeroImagePath);
    if ($heroImgSize !== false) {
        $ogImageWidth = $heroImgSize[0];
        $ogImageHeight = $heroImgSize[1];
    }
}

$canonicalUrl = portfolio_canonical_url();
$ogImageUrl = $hasProfilePhoto
    ? rtrim($canonicalUrl, '/') . '/' . $profileHeroImageSrc
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

$heroFocus = trim(portfolio_profile_text($profile, 'hero_focus'));

require __DIR__ . '/includes/header.php';

require __DIR__ . '/includes/partials/hero.php';
require __DIR__ . '/includes/partials/section-projects.php';
require __DIR__ . '/includes/partials/section-github.php';
require __DIR__ . '/includes/partials/section-about.php';
require __DIR__ . '/includes/partials/section-skills.php';
require __DIR__ . '/includes/partials/section-erp.php';

if ($navShowCases) {
    require __DIR__ . '/includes/partials/section-cases.php';
}

if ($navShowTestimonials) {
    require __DIR__ . '/includes/partials/section-testimonials.php';
}

if ($dogGalleryImages !== []) {
    require __DIR__ . '/includes/partials/section-dogs.php';
}

require __DIR__ . '/includes/partials/section-contact.php';

require __DIR__ . '/includes/footer.php';

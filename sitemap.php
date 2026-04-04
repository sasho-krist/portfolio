<?php

declare(strict_types=1);

/**
 * XML sitemap за едностраничното портфолио.
 * Подай URL към този файл в Google Search Console (напр. https://example.com/sitemap.php).
 */

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
portfolio_load_dotenv(__DIR__ . '/.env');
require __DIR__ . '/includes/seo.php';

header('Content-Type: application/xml; charset=UTF-8');

$loc = portfolio_canonical_url();
$locEsc = htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo '  <url>' . "\n";
echo '    <loc>' . $locEsc . '</loc>' . "\n";
echo '    <changefreq>weekly</changefreq>' . "\n";
echo '    <priority>1.0</priority>' . "\n";
echo '  </url>' . "\n";
echo '</urlset>';

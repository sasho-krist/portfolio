<?php

declare(strict_types=1);

/**
 * Каноничен базов URL на сайта (с наклонена черта накрая).
 * Задай SITE_URL в .env за production (точен HTTPS домейн и подпапка при нужда).
 */
function portfolio_canonical_url(): string
{
    $env = getenv('SITE_URL') ?: getenv('PORTFOLIO_SITE_URL');
    if (is_string($env) && trim($env) !== '') {
        return rtrim(trim($env), '/') . '/';
    }

    $https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = dirname(str_replace('\\', '/', $script));
    if ($dir === '/' || $dir === '.') {
        $path = '/';
    } else {
        $path = rtrim($dir, '/') . '/';
    }

    return $scheme . '://' . $host . $path;
}

/**
 * @param list<string> $extra
 */
function portfolio_seo_keywords_string(array $profile, array $extra = []): string
{
    $list = [];
    foreach ($profile['seo_keywords'] ?? [] as $k) {
        if (is_string($k) && trim($k) !== '') {
            $list[] = trim($k);
        }
    }
    foreach ($extra as $k) {
        if (is_string($k) && trim($k) !== '') {
            $list[] = trim($k);
        }
    }
    $list = array_values(array_unique($list));
    $joined = implode(', ', $list);
    if (strlen($joined) <= 512) {
        return $joined;
    }
    if (function_exists('mb_substr')) {
        return mb_substr($joined, 0, 509, 'UTF-8') . '...';
    }

    return substr($joined, 0, 509) . '...';
}

/**
 * @return list<string>
 */
function portfolio_seo_same_as(array $profile): array
{
    $out = [];
    if (! empty($profile['github']) && is_string($profile['github'])) {
        $out[] = $profile['github'];
    }
    foreach ($profile['seo_same_as'] ?? [] as $u) {
        if (is_string($u) && filter_var($u, FILTER_VALIDATE_URL)) {
            $out[] = $u;
        }
    }

    return array_values(array_unique($out));
}

/**
 * JSON-LD за Person + WebSite (schema.org). Безопасно за вграждане в <script type="application/ld+json">.
 */
function portfolio_seo_json_ld(array $profile, string $canonicalUrl, ?string $ogImageUrl, string $pageTitle, string $pageDescription): string
{
    $base = rtrim($canonicalUrl, '/');
    $personId = $base . '/#person';
    $websiteId = $base . '/#website';

    $sameAs = portfolio_seo_same_as($profile);

    $knowsAbout = [
        'PHP', 'Laravel', 'WordPress', 'MySQL', 'REST API', 'JavaScript', 'React', 'Next.js',
        'Full stack development', 'Backend development',
    ];

    $graph = [
        [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $canonicalUrl,
            'name' => $pageTitle,
            'description' => $pageDescription,
            'inLanguage' => ['bg-BG', 'en'],
            'publisher' => ['@id' => $personId],
        ],
        [
            '@type' => 'Person',
            '@id' => $personId,
            'name' => $profile['name'],
            'alternateName' => ['Alexander Keremidarov', 'Aleksander Keremidarov'],
            'jobTitle' => $profile['title'] ?? 'Web Developer',
            'description' => $pageDescription,
            'url' => $canonicalUrl,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Sofia',
                'addressCountry' => 'BG',
            ],
            'knowsAbout' => $knowsAbout,
            'sameAs' => $sameAs !== [] ? $sameAs : null,
        ],
    ];

    if ($ogImageUrl !== null && $ogImageUrl !== '') {
        $graph[1]['image'] = ['@type' => 'ImageObject', 'url' => $ogImageUrl];
    }

    if ($graph[1]['sameAs'] === null) {
        unset($graph[1]['sameAs']);
    }

    $payload = ['@context' => 'https://schema.org', '@graph' => $graph];

    $json = json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );

    return $json !== false ? $json : '{}';
}

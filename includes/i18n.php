<?php

declare(strict_types=1);

/**
 * Session must be started before portfolio_i18n_init().
 */
function portfolio_i18n_init(): void
{
    $allowed = ['bg', 'en'];
    $fromGet = $_GET['lang'] ?? null;
    if (is_string($fromGet) && in_array($fromGet, $allowed, true)) {
        $_SESSION['lang'] = $fromGet;
        setcookie('portfolio_lang', $fromGet, [
            'expires' => time() + 31536000,
            'path' => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    } elseif (! isset($_SESSION['lang'])) {
        $c = $_COOKIE['portfolio_lang'] ?? '';
        $_SESSION['lang'] = is_string($c) && in_array($c, $allowed, true) ? $c : 'bg';
    }
}

function portfolio_lang(): string
{
    $l = $_SESSION['lang'] ?? 'bg';

    return $l === 'en' ? 'en' : 'bg';
}

function portfolio_t(string $key): string
{
    /** @var array<string, array<string, string>> $bundles */
    static $bundles = null;
    if ($bundles === null) {
        $bundles = require __DIR__ . '/../data/locale.php';
    }
    $lang = portfolio_lang();
    $map = $bundles[$lang] ?? $bundles['bg'];

    return $map[$key] ?? $bundles['bg'][$key] ?? $key;
}

function portfolio_lang_url(string $lang): string
{
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    if ($script === '' || $script === '/') {
        $script = 'index.php';
    }
    $params = $_GET;
    $params['lang'] = $lang;

    return $script . '?' . http_build_query($params);
}

/**
 * Profile field with optional _en suffix.
 */
function portfolio_profile_text(array $profile, string $field): string
{
    if (portfolio_lang() === 'en') {
        $en = $profile[$field . '_en'] ?? null;
        if (is_string($en) && $en !== '') {
            return $en;
        }
    }

    return (string) ($profile[$field] ?? '');
}

/**
 * @return list<string>|array<int|string, mixed>
 */
function portfolio_profile_array(array $profile, string $field): array
{
    if (portfolio_lang() === 'en') {
        $en = $profile[$field . '_en'] ?? null;
        if (is_array($en) && $en !== []) {
            return $en;
        }
    }
    $v = $profile[$field] ?? [];

    return is_array($v) ? $v : [];
}

/**
 * @return array<string, string>
 */
function portfolio_skills_cards_for_lang(array $profile): array
{
    if (portfolio_lang() === 'en' && ! empty($profile['skills_cards_en']) && is_array($profile['skills_cards_en'])) {
        return $profile['skills_cards_en'];
    }

    return $profile['skills_cards'] ?? [];
}

/**
 * @return list<string>
 */
function portfolio_services_for_lang(array $profile): array
{
    if (portfolio_lang() === 'en' && ! empty($profile['services_en']) && is_array($profile['services_en'])) {
        return $profile['services_en'];
    }
    $s = $profile['services'] ?? [];

    return is_array($s) ? $s : [];
}

function portfolio_interest_label(array $interest): string
{
    if (portfolio_lang() === 'en' && ! empty($interest['label_en'])) {
        return (string) $interest['label_en'];
    }

    return (string) ($interest['label'] ?? '');
}

/**
 * @return list<array{label: string, url: string, note?: string}>
 */
function portfolio_github_repos_for_lang(array $profile): array
{
    if (portfolio_lang() === 'en' && ! empty($profile['github_repos_en']) && is_array($profile['github_repos_en'])) {
        return $profile['github_repos_en'];
    }
    $r = $profile['github_repos'] ?? [];

    return is_array($r) ? $r : [];
}

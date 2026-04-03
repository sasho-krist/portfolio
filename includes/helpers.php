<?php

declare(strict_types=1);

/**
 * @param non-empty-string $path
 */
function portfolio_base_path(string $path = ''): string
{
    $root = dirname(__DIR__);
    return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

/**
 * @return list<string>
 */
function portfolio_gallery_images(): array
{
    $dir = portfolio_base_path('images');
    if (! is_dir($dir)) {
        return [];
    }
    $exts = ['png', 'jpg', 'jpeg', 'webp'];
    $files = [];
    foreach ($exts as $ext) {
        $found = glob($dir . DIRECTORY_SEPARATOR . '*.' . $ext) ?: [];
        $files = array_merge($files, $found);
    }
    usort($files, static function (string $a, string $b): int {
        return strnatcasecmp(basename($a), basename($b));
    });
    $out = [];
    foreach ($files as $full) {
        $out[] = 'images/' . basename($full);
    }

    return $out;
}

/**
 * Images inside a subdirectory of the project root, e.g. "images/dogs".
 *
 * @return list<string> Web paths relative to site root (forward slashes).
 */
function portfolio_images_in_subdir(string $relativeDir): array
{
    $relativeDir = trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeDir), DIRECTORY_SEPARATOR);
    if ($relativeDir === '') {
        return [];
    }
    $dir = portfolio_base_path($relativeDir);
    if (! is_dir($dir)) {
        return [];
    }
    $exts = ['png', 'jpg', 'jpeg', 'webp'];
    $files = [];
    foreach ($exts as $ext) {
        $found = glob($dir . DIRECTORY_SEPARATOR . '*.' . $ext) ?: [];
        $files = array_merge($files, $found);
    }
    usort($files, static function (string $a, string $b): int {
        return strnatcasecmp(basename($a), basename($b));
    });
    $prefix = str_replace(DIRECTORY_SEPARATOR, '/', $relativeDir) . '/';
    $out = [];
    foreach ($files as $full) {
        $out[] = $prefix . basename($full);
    }

    return $out;
}

function portfolio_h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Very small markdown subset for README excerpts (headings, lists, bold, code, paragraphs).
 */
function portfolio_markdown_to_html(string $md): string
{
    $lines = preg_split("/\r\n|\n|\r/", $md) ?: [];
    $html = [];
    $inUl = false;
    $inCode = false;
    $codeBuf = [];

    $flushUl = static function () use (&$inUl, &$html): void {
        if ($inUl) {
            $html[] = '</ul>';
            $inUl = false;
        }
    };

    foreach ($lines as $line) {
        if (preg_match('/^```/', $line)) {
            if ($inCode) {
                $flushUl();
                $escaped = portfolio_h(implode("\n", $codeBuf));
                $html[] = '<pre><code>' . $escaped . '</code></pre>';
                $codeBuf = [];
                $inCode = false;
            } else {
                $flushUl();
                $inCode = true;
            }
            continue;
        }
        if ($inCode) {
            $codeBuf[] = $line;
            continue;
        }

        if (preg_match('/^###\s+(.+)/u', $line, $m)) {
            $flushUl();
            $html[] = '<h3>' . portfolio_h(trim($m[1])) . '</h3>';
            continue;
        }
        if (preg_match('/^##\s+(.+)/u', $line, $m)) {
            $flushUl();
            $html[] = '<h2>' . portfolio_h(trim($m[1])) . '</h2>';
            continue;
        }
        if (preg_match('/^#\s+(.+)/u', $line, $m)) {
            $flushUl();
            $html[] = '<h1>' . portfolio_h(trim($m[1])) . '</h1>';
            continue;
        }
        if (preg_match('/^-\s+(.+)/u', $line, $m)) {
            if (! $inUl) {
                $html[] = '<ul>';
                $inUl = true;
            }
            $html[] = '<li>' . portfolio_h(trim($m[1])) . '</li>';
            continue;
        }
        if (trim($line) === '') {
            $flushUl();
            continue;
        }
        $flushUl();
        $html[] = '<p>' . portfolio_h(trim($line)) . '</p>';
    }
    $flushUl();

    return implode("\n", $html);
}

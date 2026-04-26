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
        $base = basename($full);
        if (preg_match('/^alexander\./i', $base) === 1) {
            continue;
        }
        $out[] = 'images/' . $base;
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

/**
 * True for absolute http(s) URLs or bundled site paths (e.g. images/anketa/1.png).
 */
function portfolio_is_valid_project_image_url(string $url): bool
{
    if ($url === '') {
        return false;
    }
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        return true;
    }
    if (preg_match('#^(?:/|images/)#', $url) === 1) {
        return true;
    }

    return false;
}

function portfolio_h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Изпращане чрез PHP mail() (локален MTA на хостинга).
 * При неуспех: запис в logs/mail-last-error.txt и error_log. mail() рядко хвърля — try/catch за сигурност.
 */
function portfolio_send_via_php_mail(string $to, string $subject, string $bodyPlain, string $replyToEmail): bool
{
    try {
        $to = trim($to);
        if ($to === '' || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            portfolio_mail_write_last_error('PHP mail(): invalid or empty recipient');
            error_log('[portfolio mail] invalid recipient');

            return false;
        }

        $replyToEmail = str_replace(["\0", "\r", "\n"], '', trim($replyToEmail));
        if ($replyToEmail === '' || filter_var($replyToEmail, FILTER_VALIDATE_EMAIL) === false) {
            portfolio_mail_write_last_error('PHP mail(): invalid Reply-To after sanitize');
            error_log('[portfolio mail] invalid Reply-To');

            return false;
        }

        $subject = str_replace(["\0", "\r", "\n"], '', $subject);
        if ($subject === '') {
            portfolio_mail_write_last_error('PHP mail(): empty subject');
            error_log('[portfolio mail] empty subject');

            return false;
        }

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $host = preg_replace('/[^\w.-]+/', '', $host);
        if ($host === '') {
            $host = 'localhost';
        }

        $fromLine = 'Portfolio <noreply@' . $host . '>';
        $subjectHeader = function_exists('mb_encode_mimeheader')
            ? mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n")
            : $subject;

        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'From: ' . $fromLine,
            'Reply-To: <' . $replyToEmail . '>',
            'X-Mailer: PHP/' . PHP_VERSION,
        ]);

        $ok = @mail($to, $subjectHeader, $bodyPlain, $headers);
        if (! $ok) {
            $detail = 'PHP mail() returned false (recipient validated)';
            portfolio_mail_write_last_error($detail);
            error_log('[portfolio mail] ' . $detail);
        }

        return $ok;
    } catch (\Throwable $e) {
        $detail = 'PHP mail() exception: ' . $e->getMessage();
        portfolio_mail_write_last_error($detail);
        error_log('[portfolio mail] ' . $detail);

        return false;
    }
}

function portfolio_mail_write_last_error(string $detail): void
{
    $line = date('c') . ' ' . str_replace(["\r", "\n"], ' ', $detail) . PHP_EOL;
    $root = dirname(__DIR__);
    $dir = $root . DIRECTORY_SEPARATOR . 'logs';
    $file = $dir . DIRECTORY_SEPARATOR . 'mail-last-error.txt';

    $written = false;
    if ((! is_dir($dir) && @mkdir($dir, 0755, true)) || is_dir($dir)) {
        $written = @file_put_contents($file, $line, LOCK_EX) !== false;
    }

    if (! $written) {
        $tmpFile = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . 'portfolio-mail-error-' . md5($root) . '.txt';
        if (@file_put_contents($tmpFile, $line, LOCK_EX) !== false) {
            error_log('[portfolio mail] logs/ не се пише от уеб сървъра; грешката е в: ' . $tmpFile);
        }
    }
}

/**
 * Normalize project screenshot entries (string URLs or {url, caption}).
 *
 * @param array<string, mixed> $p
 * @return list<array{url: string, caption: string}>
 */
function portfolio_project_screenshots(array $p): array
{
    $raw = $p['screenshots'] ?? [];
    if (! is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (is_string($item) && $item !== '') {
            $out[] = ['url' => $item, 'caption' => ''];
        } elseif (is_array($item) && ! empty($item['url']) && is_string($item['url'])) {
            $out[] = [
                'url' => $item['url'],
                'caption' => isset($item['caption']) && is_string($item['caption']) ? $item['caption'] : '',
            ];
        }
    }

    return $out;
}

/**
 * @param list<array<string, mixed>> $projects
 * @return array<string, mixed>|null
 */
function portfolio_project_by_slug(array $projects, string $slug): ?array
{
    foreach ($projects as $project) {
        if (($project['slug'] ?? null) === $slug) {
            return $project;
        }
    }

    return null;
}

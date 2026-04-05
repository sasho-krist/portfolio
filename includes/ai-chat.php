<?php

declare(strict_types=1);

/**
 * System prompt and OpenAI chat completion helper for the portfolio assistant.
 */

function portfolio_chat_build_system_prompt(array $profile, string $lang): string
{
    $lang = $lang === 'en' ? 'en' : 'bg';
    $name = $profile['name'];
    $title = $profile['title'];
    $bio = $lang === 'en'
        ? ($profile['profile_en'] ?? $profile['profile'])
        : $profile['profile'];
    $bullets = $lang === 'en'
        ? ($profile['about_bullets_en'] ?? [])
        : ($profile['about_bullets'] ?? []);
    $bulletsText = implode("\n", array_map(static fn ($b) => '- ' . $b, $bullets));
    $cards = $lang === 'en' && ! empty($profile['skills_cards_en'])
        ? $profile['skills_cards_en']
        : ($profile['skills_cards'] ?? []);
    $skillsText = '';
    foreach ($cards as $k => $v) {
        $skillsText .= $k . ': ' . $v . "\n";
    }
    $location = $lang === 'en'
        ? ($profile['location_en'] ?? $profile['location'])
        : $profile['location'];
    $github = (string) ($profile['github'] ?? '');
    $email = (string) ($profile['email'] ?? '');
    $tagline = $lang === 'en'
        ? ($profile['tagline_en'] ?? $profile['tagline'])
        : $profile['tagline'];
    $heroFocus = $lang === 'en'
        ? ($profile['hero_focus_en'] ?? '')
        : ($profile['hero_focus'] ?? '');
    $repos = $lang === 'en' && ! empty($profile['github_repos_en'])
        ? $profile['github_repos_en']
        : ($profile['github_repos'] ?? []);
    $reposText = '';
    foreach ($repos as $r) {
        $reposText .= ($r['label'] ?? '') . ' — ' . ($r['url'] ?? '') . ' ' . ($r['note'] ?? '') . "\n";
    }

    $experienceText = '';
    foreach ($profile['experience'] ?? [] as $row) {
        if (! is_array($row)) {
            continue;
        }
        $experienceText .= ($row['period'] ?? '') . ' — ' . ($row['role'] ?? '') . ' @ ' . ($row['company'] ?? '')
            . ': ' . ($row['desc'] ?? '') . "\n";
    }

    $educationText = '';
    foreach ($profile['education'] ?? [] as $row) {
        if (! is_array($row)) {
            continue;
        }
        $details = '';
        if (! empty($row['details']) && is_array($row['details'])) {
            $details = ' ' . implode('; ', $row['details']);
        }
        $educationText .= ($row['period'] ?? '') . ': ' . ($row['degree'] ?? '') . ', ' . ($row['school'] ?? '') . $details . "\n";
    }

    $languagesText = '';
    foreach ($profile['languages'] ?? [] as $langName => $level) {
        $languagesText .= $langName . ': ' . $level . "\n";
    }

    $services = $lang === 'en' && ! empty($profile['services_en'])
        ? $profile['services_en']
        : ($profile['services'] ?? []);
    $servicesText = implode("\n", array_map(static fn ($s) => '- ' . $s, $services));

    $personalFile = __DIR__ . '/../data/ai_context_personal.php';
    $personalText = '';
    if (is_readable($personalFile)) {
        $personal = require $personalFile;
        if (is_array($personal)) {
            $personalText = trim((string) ($personal[$lang] ?? $personal['bg'] ?? ''));
        }
    }

    $replyLang = $lang === 'en' ? 'English' : 'Bulgarian';

    return <<<PROMPT
You are the portfolio assistant for {$name}. Visitors ask questions about this person — answer only using the facts below (professional block + extra personal context). If something is not in the facts, say you do not have that information and suggest using the contact form on the site. Do not invent employers, dates, or facts not listed. Be concise and professional. Reply in {$replyLang}.

Name: {$name}
Professional title: {$title}
Tagline: {$tagline}
Location: {$location}
Public GitHub profile: {$github}
Contact email (mention only if asked how to contact): {$email}

Bio:
{$bio}

About (bullets):
{$bulletsText}

Current focus (hero):
{$heroFocus}

Work experience (chronological facts):
{$experienceText}

Education:
{$educationText}

Languages:
{$languagesText}

Skills (summary):
{$skillsText}

Services offered (summary):
{$servicesText}

Featured GitHub repos (public):
{$reposText}
PROMPT
        . ($personalText !== ''
            ? "\n\nExtra personal context (facts the site owner approved for this assistant; use only when relevant to questions about life, family, hobbies, interests — do not invent beyond this block):\n{$personalText}\n"
            : '');
}

/**
 * Curl options for HTTPS to OpenAI (WAMP/Windows often need CA bundle or OPENAI_SSL_VERIFY=0 locally).
 *
 * @return array<int, mixed>
 */
function portfolio_chat_curl_options(): array
{
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_CONNECTTIMEOUT => 15,
    ];
    if (defined('CURL_IPRESOLVE_V4')) {
        $opts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
    }

    $caCandidates = array_filter([
        trim((string) (getenv('OPENAI_CAFILE') ?: '')),
        (string) (ini_get('curl.cainfo') ?: ''),
        (string) (ini_get('openssl.cafile') ?: ''),
    ]);
    $phpDir = dirname((string) PHP_BINARY);
    if ($phpDir !== '' && $phpDir !== '.') {
        $caCandidates[] = $phpDir . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'cacert.pem';
    }

    $cafile = '';
    foreach ($caCandidates as $c) {
        if ($c !== '' && is_readable($c)) {
            $cafile = $c;
            break;
        }
    }
    if ($cafile !== '') {
        $opts[CURLOPT_CAINFO] = $cafile;
    }

    $verify = getenv('OPENAI_SSL_VERIFY');
    $disableVerify = $verify !== false && $verify !== ''
        && in_array(strtolower((string) $verify), ['0', 'false', 'no'], true);
    if ($disableVerify) {
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_SSL_VERIFYHOST] = 0;
    } else {
        $opts[CURLOPT_SSL_VERIFYPEER] = true;
        $opts[CURLOPT_SSL_VERIFYHOST] = 2;
    }

    return $opts;
}

/**
 * Map OpenAI HTTP error to a short code for the UI (see locale chat_error_*).
 *
 * @param array<string, mixed> $data
 * @param array<string, string> $respHeaders Lowercase header names from curl
 */
function portfolio_chat_openai_error_code(int $httpCode, array $data, array $respHeaders = []): string
{
    $err = $data['error'] ?? null;
    $msg = '';
    $code = '';
    if (is_array($err)) {
        $msg = isset($err['message']) ? (string) $err['message'] : '';
        $code = isset($err['code']) ? (string) $err['code'] : '';
    } elseif (is_string($err)) {
        $msg = $err;
    }

    $msgLower = strtolower($msg);
    $log = sprintf('portfolio chat OpenAI HTTP %d: %s', $httpCode, $msg !== '' ? $msg : ($code !== '' ? $code : '(no body)'));
    error_log($log);
    if ($httpCode === 429) {
        $ra = $respHeaders['retry-after'] ?? '';
        if ($ra !== '') {
            error_log('portfolio chat OpenAI 429 Retry-After header: ' . $ra);
        }
    }

    if ($httpCode === 401) {
        return 'auth';
    }
    if ($httpCode === 403) {
        return 'forbidden';
    }
    // 429 често е „rate limit“, но същият код връща и „exceeded your current quota“ — тогава е billing, не RPM.
    if (
        $httpCode === 402
        || $code === 'insufficient_quota'
        || str_contains($msgLower, 'quota')
        || str_contains($msgLower, 'billing')
        || str_contains($msgLower, 'billing_hard_limit')
    ) {
        return 'quota';
    }
    if ($httpCode === 429) {
        return 'openai_rate';
    }
    if ($httpCode === 400 && (
        str_contains(strtolower($code), 'model')
        || str_contains($msgLower, 'model')
        || str_contains($msgLower, 'does not exist')
    )) {
        return 'model';
    }

    return 'api';
}

/**
 * @return array{ok: true, reply: string}|array{ok: false, error: string, retry_after?: int, openai_hint?: string}
 */
function portfolio_chat_openai_completion(string $apiKey, string $systemPrompt, string $userMessage, string $model): array
{
    $payload = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage],
        ],
        'max_tokens' => 600,
        'temperature' => 0.4,
    ];
    try {
        $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    } catch (JsonException) {
        return ['ok' => false, 'error' => 'encode'];
    }

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    if ($ch === false) {
        return ['ok' => false, 'error' => 'network'];
    }

    $requestOpts = [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => $body,
    ];
    // array_merge() renumbers numeric CURLOPT_* keys — use union (+).
    curl_setopt_array($ch, $requestOpts + portfolio_chat_curl_options());
    $respHeaders = [];
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function ($ch, $headerLine) use (&$respHeaders): int {
        $len = strlen($headerLine);
        if (str_contains($headerLine, ':')) {
            $parts = explode(':', $headerLine, 2);
            if (count($parts) === 2) {
                $respHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        return $len;
    });
    $response = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($response === false || $curlErr !== '') {
        error_log('portfolio chat curl: ' . $curlErr);
        $errLower = strtolower($curlErr);
        $isSsl = str_contains($errLower, 'ssl')
            || str_contains($errLower, 'certificate')
            || str_contains($errLower, 'curl error 60');

        return ['ok' => false, 'error' => $isSsl ? 'ssl' : 'network'];
    }

    $data = json_decode($response, true);
    if ($code < 200 || $code >= 300) {
        if (! is_array($data)) {
            error_log('portfolio chat OpenAI HTTP ' . $code . ' non-JSON: ' . substr((string) $response, 0, 400));

            return ['ok' => false, 'error' => 'api'];
        }

        $errKey = portfolio_chat_openai_error_code($code, $data, $respHeaders);
        $out = ['ok' => false, 'error' => $errKey];
        if ($code === 429) {
            $ra = isset($respHeaders['retry-after']) ? trim((string) $respHeaders['retry-after']) : '';
            if ($ra !== '' && ctype_digit($ra)) {
                $out['retry_after'] = (int) $ra;
            }
        }
        $em = $data['error'] ?? null;
        if (is_array($em) && isset($em['message'])) {
            $hint = (string) $em['message'];
            $out['openai_hint'] = function_exists('mb_substr')
                ? mb_substr($hint, 0, 320)
                : substr($hint, 0, 320);
        }

        return $out;
    }
    if (! is_array($data)) {
        return ['ok' => false, 'error' => 'invalid'];
    }
    $text = $data['choices'][0]['message']['content'] ?? '';
    if (! is_string($text) || $text === '') {
        return ['ok' => false, 'error' => 'empty'];
    }

    return ['ok' => true, 'reply' => trim($text)];
}

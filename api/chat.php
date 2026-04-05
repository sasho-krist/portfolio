<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method'], JSON_UNESCAPED_UNICODE);
    exit;
}

require __DIR__ . '/../includes/env.php';
portfolio_load_dotenv(__DIR__ . '/../.env');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw !== false ? $raw : '', true);
if (! is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'json'], JSON_UNESCAPED_UNICODE);
    exit;
}

$csrf = isset($data['csrf']) ? (string) $data['csrf'] : '';
$message = isset($data['message']) ? trim((string) $data['message']) : '';
$lang = isset($data['lang']) && $data['lang'] === 'en' ? 'en' : 'bg';

$expectedCsrf = $_SESSION['chat_csrf'] ?? '';
if ($expectedCsrf === '' || ! hash_equals($expectedCsrf, $csrf)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'csrf'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($message === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'empty'], JSON_UNESCAPED_UNICODE);
    exit;
}

$lenFn = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
if ($lenFn($message) > 4000) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'long'], JSON_UNESCAPED_UNICODE);
    exit;
}

$now = time();
$window = 3600;
$limit = 30;
$rl = $_SESSION['chat_rl'] ?? null;
if (! is_array($rl) || ($rl['reset'] ?? 0) < $now) {
    $_SESSION['chat_rl'] = ['count' => 0, 'reset' => $now + $window];
    $rl = $_SESSION['chat_rl'];
}
if (($rl['count'] ?? 0) >= $limit) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'rate'], JSON_UNESCAPED_UNICODE);
    exit;
}
$_SESSION['chat_rl']['count'] = ($rl['count'] ?? 0) + 1;

$apiKey = trim((string) (getenv('OPENAI_API_KEY') ?: ''));
if ($apiKey === '') {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'config'], JSON_UNESCAPED_UNICODE);
    exit;
}

require __DIR__ . '/../includes/ai-chat.php';
$profile = require __DIR__ . '/../data/profile.php';
$system = portfolio_chat_build_system_prompt($profile, $lang);
$model = trim((string) (getenv('OPENAI_CHAT_MODEL') ?: 'gpt-4o-mini'));
$result = portfolio_chat_openai_completion($apiKey, $system, $message, $model);

if (! $result['ok']) {
    if (isset($_SESSION['chat_rl']['count']) && (int) $_SESSION['chat_rl']['count'] > 0) {
        $_SESSION['chat_rl']['count']--;
    }
    http_response_code(502);
    $payload = ['ok' => false, 'error' => $result['error']];
    foreach (['retry_after', 'openai_hint'] as $k) {
        if (isset($result[$k])) {
            $payload[$k] = $result[$k];
        }
    }
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => true, 'reply' => $result['reply']], JSON_UNESCAPED_UNICODE);

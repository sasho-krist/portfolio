<?php

declare(strict_types=1);

/**
 * Контактна форма: получател от data/profile.php или MAIL_TO в .env.
 * SMTP: когато MAIL_MAILER=smtp (или е празно) и са зададени MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD.
 * Иначе: PHP mail().
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#contact', true, 303);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';

portfolio_load_dotenv(__DIR__ . '/.env');

$profile = require __DIR__ . '/data/profile.php';

$redirect = static function (string $query): void {
    header('Location: index.php?contact=' . $query . '#contact', true, 303);
    exit;
};

$sessionToken = $_SESSION['csrf_contact'] ?? '';
$postToken = (string) ($_POST['csrf'] ?? '');
if ($sessionToken === '' || ! hash_equals($sessionToken, $postToken)) {
    $redirect('invalid');
}

if (trim((string) ($_POST['website'] ?? '')) !== '') {
    $redirect('sent');
}

$name = trim((string) ($_POST['sender_name'] ?? ''));
$email = trim((string) ($_POST['sender_email'] ?? ''));
$message = trim((string) ($_POST['sender_message'] ?? ''));

if (strlen($name) < 1 || strlen($name) > 200 || strlen($message) < 1 || strlen($message) > 8000) {
    $redirect('invalid');
}

if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $redirect('invalid');
}

$to = trim((string) getenv('MAIL_TO'));
if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $to = $profile['email'];
}

$nameForSubject = function_exists('mb_substr') ? mb_substr($name, 0, 80, 'UTF-8') : substr($name, 0, 80);
$subject = 'Портфолио: съобщение от ' . preg_replace('/[\r\n]+/u', ' ', $nameForSubject);

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$body = "Име: {$name}\nИмейл: {$email}\nIP: {$ip}\n\n{$message}";

$mailer = strtolower(trim((string) getenv('MAIL_MAILER')));
$mailHost = trim((string) getenv('MAIL_HOST'));
$mailUser = trim((string) getenv('MAIL_USERNAME'));
$mailPass = trim((string) getenv('MAIL_PASSWORD'));

$useSmtp = ($mailer === '' || $mailer === 'smtp')
    && $mailHost !== ''
    && $mailUser !== ''
    && $mailPass !== '';

$ok = false;

if ($useSmtp) {
    if (! is_readable(__DIR__ . '/vendor/autoload.php')) {
        $ok = false;
    } else {
        require_once __DIR__ . '/vendor/autoload.php';
        require_once __DIR__ . '/includes/contact-smtp.php';
        $ok = portfolio_mail_via_smtp($to, $subject, $body, $email, $name);
    }
} else {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $host = preg_replace('/[^\w.-]+/', '', $host);
    if ($host === '') {
        $host = 'localhost';
    }

    $fromLine = 'Portfolio <noreply@' . $host . '>';
    $subjectHeader = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n")
        : 'Portfolio contact';

    $headers = implode("\r\n", [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'From: ' . $fromLine,
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . PHP_VERSION,
    ]);

    $ok = @mail($to, $subjectHeader, $body, $headers);
}

$redirect($ok ? 'sent' : 'fail');

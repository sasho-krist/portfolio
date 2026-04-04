<?php

declare(strict_types=1);

/**
 * Контактна форма: получател от data/profile.php или MAIL_TO в .env.
 * SMTP: когато MAIL_MAILER=smtp (или е празно) и са зададени MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD.
 * Иначе: PHP mail().
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/env.php';
require __DIR__ . '/includes/i18n.php';

portfolio_load_dotenv(__DIR__ . '/.env');
portfolio_i18n_init();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $back = ['lang' => 'en'];
    $url = portfolio_lang() === 'en'
        ? 'index.php?' . http_build_query($back) . '#contact'
        : 'index.php#contact';
    header('Location: ' . $url, true, 303);
    exit;
}

$profile = require __DIR__ . '/data/profile.php';

$redirect = static function (string $query): void {
    $params = ['contact' => $query];
    if (portfolio_lang() === 'en') {
        $params['lang'] = 'en';
    }
    header('Location: index.php?' . http_build_query($params) . '#contact', true, 303);
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
        portfolio_mail_write_last_error('Липсва vendor/autoload.php — на сървъра пусни: php ~/composer.phar install --no-dev');
        $ok = false;
    } else {
        require_once __DIR__ . '/vendor/autoload.php';
        require_once __DIR__ . '/includes/contact-smtp.php';
        $ok = portfolio_mail_via_smtp($to, $subject, $body, $email, $name);
        // Много хостове блокират 465 от PHP; втори опит с 587+STARTTLS (изключи с MAIL_AUTO_FALLBACK_587=0).
        if (
            ! $ok
            && getenv('MAIL_AUTO_FALLBACK_587') !== '0'
        ) {
            $p = (int) getenv('MAIL_PORT');
            $enc = strtolower(trim((string) getenv('MAIL_ENCRYPTION')));
            if ($p === 465 || $enc === 'ssl') {
                error_log('[portfolio mail] първи опит неуспешен — опит 587 + STARTTLS');
                $ok = portfolio_mail_via_smtp($to, $subject, $body, $email, $name, [
                    'MAIL_PORT' => '587',
                    'MAIL_ENCRYPTION' => 'tls',
                ]);
            }
        }
        // Някои SMTP отхвърлят принудителен AUTH LOGIN — втори опит с авто-избор (изключи с MAIL_AUTH_RETRY_PLAIN=0).
        if (
            ! $ok
            && getenv('MAIL_AUTH_RETRY_PLAIN') !== '0'
            && trim((string) getenv('MAIL_AUTH_TYPE')) !== ''
        ) {
            error_log('[portfolio mail] повторен опит без MAIL_AUTH_TYPE (автоматичен AUTH)');
            $ok = portfolio_mail_via_smtp($to, $subject, $body, $email, $name, [
                'MAIL_AUTH_TYPE' => '',
            ]);
        }
    }
} else {
    $ok = portfolio_send_via_php_mail($to, $subject, $body, $email);
}

// Външен SMTP често е блокиран от хостинга; последен опит през локалния mail() на сървъра.
if (! $ok && $useSmtp && getenv('MAIL_FALLBACK_PHP_MAIL') !== '0') {
    error_log('[portfolio mail] SMTP неуспешен — fallback към PHP mail()');
    $ok = portfolio_send_via_php_mail($to, $subject, $body, $email);
}

$redirect($ok ? 'sent' : 'fail');

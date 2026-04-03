<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Изпращане през SMTP (настройки от .env: MAIL_*).
 *
 * @param array<string, string> $override Ключове като MAIL_PORT — презаписват getenv за този опит.
 */
function portfolio_mail_via_smtp(
    string $to,
    string $subject,
    string $bodyPlain,
    string $visitorEmail,
    string $visitorName,
    array $override = [],
): bool {
    $g = static function (string $key) use ($override): string {
        if (array_key_exists($key, $override)) {
            return trim((string) $override[$key]);
        }
        $v = getenv($key);

        return $v !== false ? trim((string) $v) : '';
    };

    $user = $g('MAIL_USERNAME');
    $pass = $g('MAIL_PASSWORD');
    if ($user === '' || $pass === '') {
        return false;
    }

    $host = $g('MAIL_HOST');
    if ($host === '') {
        return false;
    }

    $port = (int) $g('MAIL_PORT');
    if ($port < 1 || $port > 65535) {
        $port = 465;
    }

    $encryption = strtolower($g('MAIL_ENCRYPTION'));
    if ($encryption === '') {
        $encryption = $port === 465 ? 'ssl' : 'tls';
    }

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->Port = $port;
        $mail->Timeout = 90;

        $ehlo = trim($g('MAIL_EHLO_HOST'));
        if ($ehlo === '' && preg_match('/@([a-z0-9.-]+\.[a-z]{2,})$/i', $user, $ehloM)) {
            $ehlo = $ehloM[1];
        }
        if ($ehlo === '') {
            $ehlo = preg_replace('/[^\w.-]+/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')) ?: 'localhost';
        }
        $mail->Hostname = $ehlo;

        $authType = strtoupper($g('MAIL_AUTH_TYPE'));
        if (in_array($authType, ['LOGIN', 'PLAIN', 'CRAM-MD5'], true)) {
            $mail->AuthType = $authType;
        }

        if ($g('MAIL_DEBUG') === '1') {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = static function (string $str, int $level): void {
                error_log('[portfolio mail] ' . trim($str));
            };
        }

        $sslRelax = $g('MAIL_SSL_RELAX') === '1';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => ! $sslRelax,
                'verify_peer_name' => ! $sslRelax,
                'allow_self_signed' => $sslRelax,
            ],
        ];

        if ($encryption === 'ssl' || $port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAutoTLS = false;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAutoTLS = true;
        }

        if ($g('MAIL_SMTP_AUTO_TLS') === '1') {
            $mail->SMTPAutoTLS = true;
        }
        if ($g('MAIL_SMTP_AUTO_TLS') === '0') {
            $mail->SMTPAutoTLS = false;
        }

        $mail->setFrom($user, 'Portfolio — контактна форма');
        $mail->addAddress($to);
        if ($visitorEmail !== '') {
            $mail->addReplyTo($visitorEmail, $visitorName !== '' ? $visitorName : $visitorEmail);
        }

        $mail->Subject = $subject;
        $mail->Body = $bodyPlain;
        $mail->isHTML(false);

        $mail->send();

        return true;
    } catch (\Throwable $e) {
        $detail = $mail->ErrorInfo . ' | ' . $e->getMessage();
        error_log('[portfolio mail] ' . $detail);
        portfolio_mail_write_last_error($detail);

        return false;
    }
}

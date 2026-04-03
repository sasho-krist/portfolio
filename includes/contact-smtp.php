<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Изпращане през SMTP (настройки от .env: MAIL_*).
 */
function portfolio_mail_via_smtp(
    string $to,
    string $subject,
    string $bodyPlain,
    string $visitorEmail,
    string $visitorName,
): bool {
    $user = trim((string) getenv('MAIL_USERNAME'));
    $pass = trim((string) getenv('MAIL_PASSWORD'));
    if ($user === '' || $pass === '') {
        return false;
    }

    $host = trim((string) getenv('MAIL_HOST'));
    if ($host === '') {
        return false;
    }

    $port = (int) getenv('MAIL_PORT');
    if ($port < 1 || $port > 65535) {
        $port = 465;
    }

    $encryption = strtolower(trim((string) getenv('MAIL_ENCRYPTION')));
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
        $mail->Timeout = 45;

        $authType = strtoupper(trim((string) getenv('MAIL_AUTH_TYPE')));
        if (in_array($authType, ['LOGIN', 'PLAIN', 'CRAM-MD5'], true)) {
            $mail->AuthType = $authType;
        }

        if (getenv('MAIL_DEBUG') === '1') {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = static function (string $str, int $level): void {
                error_log('[portfolio mail] ' . trim($str));
            };
        }

        $sslRelax = getenv('MAIL_SSL_RELAX') === '1';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => ! $sslRelax,
                'verify_peer_name' => ! $sslRelax,
                'allow_self_signed' => $sslRelax,
            ],
        ];

        if ($encryption === 'ssl' || $port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            // Вече криптирана връзка; повторен STARTTLS чупи някои сървъри (вкл. често на Windows).
            $mail->SMTPAutoTLS = false;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAutoTLS = true;
        }

        if (getenv('MAIL_SMTP_AUTO_TLS') === '1') {
            $mail->SMTPAutoTLS = true;
        }
        if (getenv('MAIL_SMTP_AUTO_TLS') === '0') {
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

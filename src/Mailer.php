<?php

declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Optional SMTP mailer. When SMTP_HOST is not set, send() returns false and
 * the app carries on (messages are still stored in the database).
 */
final class Mailer
{
    public static function configured(): bool
    {
        return Env::get('SMTP_HOST') !== null && Env::get('MAIL_FROM') !== null
            && class_exists(PHPMailer::class);
    }

    /**
     * Always sends FROM the site's own address (MAIL_FROM). A visitor's address
     * can be passed as Reply-To; we never spoof it as the sender.
     */
    public static function send(string $to, string $subject, string $html, ?string $replyTo = null, ?string $replyName = null): bool
    {
        if (!self::configured()) {
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = (string) Env::get('SMTP_HOST');
            $mail->Port = (int) Env::get('SMTP_PORT', '587');
            $encryption = strtolower((string) Env::get('SMTP_ENCRYPTION', 'tls'));
            $mail->SMTPSecure = match ($encryption) {
                'ssl', 'smtps' => PHPMailer::ENCRYPTION_SMTPS,
                'none', '' => '',
                default => PHPMailer::ENCRYPTION_STARTTLS,
            };
            $mail->SMTPAutoTLS = $encryption !== 'none';
            if (Env::get('SMTP_USERNAME') !== null) {
                $mail->SMTPAuth = true;
                $mail->Username = (string) Env::get('SMTP_USERNAME');
                $mail->Password = (string) Env::get('SMTP_PASSWORD', '');
            }
            $mail->Timeout = 10;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->setFrom((string) Env::get('MAIL_FROM'), (string) Env::get('MAIL_FROM_NAME', 'Travel'));
            $mail->addAddress($to);
            if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo, $replyName ?? '');
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = trim(html_entity_decode(strip_tags(str_replace(['<br>', '</p>'], "\n", $html))));
            $mail->send();
            return true;
        } catch (MailException $ex) {
            error_log('Mail error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}

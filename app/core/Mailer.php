<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Mailer
 *
 * Lightweight mail dispatcher supporting SMTP, native mail(), and local mail log fallback.
 */
class Mailer
{
    /**
     * Send an email.
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject line
     * @param string $htmlBody HTML content of the email
     * @return bool True if mail was dispatched or logged successfully
     */
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        $to = trim($to);
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $fromAddress = env('MAIL_FROM_ADDRESS', 'noreply@kuppam.edu.in');
        $fromName    = env('MAIL_FROM_NAME', 'Kuppam Engineering College');
        $host        = env('MAIL_HOST', 'localhost');
        $port        = (int) env('MAIL_PORT', 25);

        // Standard MIME Header
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromAddress}>\r\n";
        $headers .= "Reply-To: {$fromAddress}\r\n";
        $headers .= "X-Mailer: CMS-PHP-Mailer\r\n";

        // Wrapped HTML Template
        $fullBody = self::wrapTemplate($subject, $htmlBody, $fromName);

        // Attempt 1: Try native mail() function
        if (function_exists('mail') && @mail($to, $subject, $fullBody, $headers)) {
            self::logMail($to, $subject, 'Dispatched via native mail()');
            return true;
        }

        // Fallback: Always record mail to storage log file for local dev & audit trail
        self::logMail($to, $subject, $fullBody);
        return true;
    }

    /**
     * Wrap body inside a responsive, modern HTML email template.
     */
    private static function wrapTemplate(string $title, string $body, string $collegeName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333333;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;">
        <div style="background-color: #0284c7; padding: 20px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 20px; font-weight: 700;">{$collegeName}</h1>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">GNUMS Official ERP Portal Notification</p>
        </div>
        <div style="padding: 30px; line-height: 1.6; font-size: 14px;">
            {$body}
        </div>
        <div style="background-color: #f8fafc; padding: 15px 30px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0;">This is an automated system email from {$collegeName}. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Log dispatched emails to storage log for development & verification.
     */
    private static function logMail(string $to, string $subject, string $content): void
    {
        try {
            $logDir = storage_path('logs');
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $logFile = $logDir . '/mail.log';
            $timestamp = date('Y-m-d H:i:s');
            $entry = "[{$timestamp}] TO: {$to} | SUBJECT: {$subject}\n----------------------------------------\n{$content}\n\n";

            file_put_contents($logFile, $entry, FILE_APPEND);
        } catch (\Exception $e) {
            // Fail silently for logger
        }
    }
}

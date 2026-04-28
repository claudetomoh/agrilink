<?php
/**
 * AgriLink – lightweight mail helper
 */

class Mailer {

    public static function send(string $to, string $subject, string $html, ?string $text = null): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>',
            'Reply-To: ' . SUPPORT_EMAIL,
            'X-Mailer: PHP/' . phpversion(),
        ];

        $plainText = $text ?? trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ["\n", "\n", "\n", "\n\n"], $html)));

        if (APP_ENV === 'local') {
            error_log("[Mailer] To: {$to} | Subject: {$subject} | Text: {$plainText}");
            return true;
        }

        if (!function_exists('mail')) {
            error_log('[Mailer] PHP mail() is unavailable on this host.');
            return false;
        }

        $sent = @mail($to, $subject, $html, implode("\r\n", $headers));
        if (!$sent) {
            error_log("[Mailer] Failed to send mail to {$to} with subject {$subject}.");
        }
        return $sent;
    }

    public static function sendPasswordReset(string $to, string $name, string $resetLink): bool {
        $safeName = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $subject = APP_NAME . ' Password Reset';
        $html = "
            <div style=\"font-family:Arial,sans-serif;line-height:1.6;color:#28343e;max-width:640px;margin:0 auto;padding:24px\">
              <h2 style=\"margin:0 0 16px;color:#2c694e\">Reset your AgriLink password</h2>
              <p>Hello {$safeName},</p>
              <p>We received a request to reset your password. Use the button below to set a new one. This link expires in 60 minutes.</p>
              <p style=\"margin:24px 0\">
                <a href=\"{$safeLink}\" style=\"display:inline-block;background:#2c694e;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:999px;font-weight:700\">Reset Password</a>
              </p>
              <p>If the button does not work, copy and paste this link into your browser:</p>
              <p style=\"word-break:break-all\"><a href=\"{$safeLink}\">{$safeLink}</a></p>
              <p>If you did not request this, you can ignore this email.</p>
              <p>AgriLink Support<br><a href=\"mailto:" . SUPPORT_EMAIL . "\">" . SUPPORT_EMAIL . "</a></p>
            </div>
        ";
        $text = "Hello {$name},\n\nWe received a request to reset your AgriLink password. Use the link below within 60 minutes:\n{$resetLink}\n\nIf you did not request this, you can ignore this email.\n\nSupport: " . SUPPORT_EMAIL;

        return self::send($to, $subject, $html, $text);
    }
}
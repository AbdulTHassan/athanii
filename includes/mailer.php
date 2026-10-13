<?php
// includes/mailer.php
declare(strict_types=1);

/**
 * Aika email (Development: ajiye a file)
 */
function sendEmail(string $to, string $subject, string $htmlBody): bool {
    $storageDir = BASE_PATH . '/storage/emails';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0777, true);
    }
    
    $filename = $storageDir . '/' . date('Ymd_His') . '_' . md5($to . $subject . microtime()) . '.html';
    
    $content = "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'><title>" . htmlspecialchars($subject) . "</title></head>
    <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f3f4f6;'>
        <div style='background: #2d7d46; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
            <h1 style='color: white; margin: 0;'>" . SITE_NAME . "</h1>
        </div>
        <div style='background: white; padding: 30px; border: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;'>
            <p style='color: #666; font-size: 12px;'><strong>To:</strong> " . htmlspecialchars($to) . "</p>
            <p style='color: #666; font-size: 12px;'><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p style='color: #999; font-size: 11px;'><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
            <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
            $htmlBody
        </div>
        <p style='text-align: center; color: #999; font-size: 11px; margin-top: 20px;'>
            © " . date('Y') . " " . SITE_NAME . ". All rights reserved.
        </p>
    </body>
    </html>
    ";
    
    file_put_contents($filename, $content);
    error_log("[MAIL] To: $to | Subject: $subject | File: $filename");
    
    return true;
}

function emailTemplatePasswordReset(string $fullName, string $resetLink): string {
    return "
        <h2 style='color: #2d7d46;'>Password Reset Request</h2>
        <p>Assalamu alaikum <strong>" . htmlspecialchars($fullName) . "</strong>,</p>
        <p>Mun karɓi buƙatar sake saita password ɗinka. Idan ba kai ba ne, ka yi watsi da wannan email.</p>
        <p>Don sake saita password, danna maɓallin nan:</p>
        <p style='text-align: center; margin: 30px 0;'>
            <a href='$resetLink' style='background: #2d7d46; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>
                🔐 Reset Password
            </a>
        </p>
        <p style='background: #fef3c7; padding: 12px; border-radius: 6px; color: #92400e; font-size: 13px;'>
            ⏰ Wannan link ɗin zai ƙare nan da awa 1 (1 hour).
        </p>
        <p style='font-size: 12px; color: #666; margin-top: 20px;'>
            Idan maɓallin bai yi aiki ba, kwafi wannan link ɗin a browser:<br>
            <code style='background: #f3f4f6; padding: 4px 8px; border-radius: 4px; word-break: break-all; font-size: 11px;'>" . htmlspecialchars($resetLink) . "</code>
        </p>
    ";
}

function emailTemplateVerify(string $fullName, string $verifyLink): string {
    return "
        <h2 style='color: #2d7d46;'>Tabbatar da Email ɗinka</h2>
        <p>Assalamu alaikum <strong>" . htmlspecialchars($fullName) . "</strong>,</p>
        <p>Mun yi farin cikin ƙarfafa ka a <strong>" . SITE_NAME . "</strong>!</p>
        <p>Don tabbatar da email ɗinka, danna maɓallin nan:</p>
        <p style='text-align: center; margin: 30px 0;'>
            <a href='$verifyLink' style='background: #2d7d46; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>
                ✅ Tabbatar da Email
            </a>
        </p>
        <p style='background: #fef3c7; padding: 12px; border-radius: 6px; color: #92400e; font-size: 13px;'>
            ⏰ Wannan link ɗin zai ƙare nan da awa 24 (24 hours).
        </p>
    ";
}

function emailTemplate2FA(string $fullName, string $code): string {
    return "
        <h2 style='color: #2d7d46;'>Lambar Tabbatarwa (2FA)</h2>
        <p>Assalamu alaikum <strong>" . htmlspecialchars($fullName) . "</strong>,</p>
        <p>Lambar tabbatarwa don shiga account ɗinka ita ce:</p>
        <p style='text-align: center; margin: 30px 0;'>
            <span style='background: #2d7d46; color: white; padding: 20px 40px; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 8px; display: inline-block;'>
                $code
            </span>
        </p>
        <p style='background: #fef3c7; padding: 12px; border-radius: 6px; color: #92400e; font-size: 13px;'>
            ⏰ Wannan lambar tana aiki na minti 10 (10 minutes) kawai.
        </p>
        <p style='font-size: 12px; color: #666;'>
            Idan ba kai ba ne ya nemi wannan, ka yi watsi da wannan email kuma ka canza password ɗinka nan take.
        </p>
    ";
}
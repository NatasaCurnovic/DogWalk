<?php
require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

//helper function that concatenates the site's base address with the path
function app_url(string $path): string
{
    return rtrim(APP_BASE_URL, '/') . '/' . ltrim($path, '/');
}

function send_app_mail(string $to, string $toName, string $subject, string $body): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host= MAIL_HOST;
        $mail->SMTPAuth= true;
        $mail->Username= MAIL_USERNAME;
        $mail->Password= MAIL_PASSWORD;
        $mail->SMTPSecure= PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port= MAIL_PORT;
        $mail->Timeout= 10;
        $mail->CharSet= 'UTF-8';

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('DogWalk mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

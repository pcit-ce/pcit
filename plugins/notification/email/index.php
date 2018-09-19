<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__.'/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 4;
    $mail->isSMTP();
    $mail->Host = getenv('CI_EMAIL_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = getenv('CI_EMAIL_USERNAME');
    $mail->Password = getenv('CI_EMAIL_PASSWORD');
    $mail->SMTPSecure = getenv('CI_EMAIL_SMTP_SECURE');
    $mail->Port = getenv('CI_EMAIL_SMTP_PORT');

    $mail->setFrom(getenv('CI_EMAIL_FROM'), getenv('CI_EMAIL_FROM_NAME'));

    foreach (json_decode(getenv('CI_EMAIL_ADDRESS_JSON')) as $k => $v) {
        $mail->addAddress($k, $v);
    }

    if (getenv('CI_EMAIL_CC_JSON')) {
        foreach (json_decode(getenv('CI_EMAIL_CC_JSON'), true) as $k => $v) {
            $mail->addCC($k, $v); // 抄送
        }
    }

    if (getenv('CI_EMAIL_BCC_JSON')) {
        foreach (json_decode(getenv('CI_EMAIL_BCC_JSON'), true) as $k => $v) {
            $mail->addBCC($k, $v); // 暗抄送
        }
    }

    $mail->isHTML(true);
    $mail->Subject = getenv('CI_EMAIL_OBJECT');
    $mail->Body = getenv('CI_EMAIL_BODY');

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}

<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__.'/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = getenv('CI_EMAIL_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = getenv('CI_EMAIL_USERNAME');
    $mail->Password = getenv('CI_EMAIL_PASSWORD');
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom(getenv('CI_EMAIL_FROM'), getenv('CI_EMAIL_FROM_NAME'));

    foreach (json_decode(getenv('CI_EMAIL_ADDRESS_JSON')) as $k => $v) {
        $mail->addAddress($k, $v);
    }

    foreach (json_decode(getenv('CI_EMAIL_CC_JSON'), true) as $k) {
        $mail->addCC($k); // 抄送
    }

    foreach (json_decode(getenv('CI_EMAIL_BCC_JSON'), true) as $k) {
        $mail->addBCC($k); // 暗抄送
    }

    $mail->isHTML(true);
    $mail->Subject = getenv('CI_EMAIL_OBJECT');
    $mail->Body = getenv('CI_EMAIL_BODY');

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}

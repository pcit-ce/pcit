<?php

declare(strict_types=1);

use PCIT\Plugin\Toolkit\Core;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__.'/vendor/autoload.php';

$mail = new PHPMailer(true);
$core = new Core();

try {
    $mail->SMTPDebug = 4;
    $mail->isSMTP();
    $mail->Host = getenv('INPUT_EMAIL_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = getenv('INPUT_EMAIL_USERNAME');
    $mail->Password = getenv('INPUT_EMAIL_PASSWORD');
    $mail->SMTPSecure = getenv('INPUT_EMAIL_SMTP_SECURE');
    $mail->Port = getenv('INPUT_EMAIL_SMTP_PORT');

    $mail->setFrom(getenv('INPUT_EMAIL_FROM'), getenv('INPUT_EMAIL_FROM_NAME'));

    foreach (json_decode(getenv('INPUT_EMAIL_ADDRESS_JSON')) as $k => $v) {
        $mail->addAddress($k, $v);
    }

    if (getenv('INPUT_EMAIL_CC_JSON')) {
        foreach (json_decode(getenv('INPUT_EMAIL_CC_JSON'), true) as $k => $v) {
            $mail->addCC($k, $v); // 抄送
        }
    }

    if (getenv('INPUT_EMAIL_BCC_JSON')) {
        foreach (json_decode(getenv('INPUT_EMAIL_BCC_JSON'), true) as $k => $v) {
            $mail->addBCC($k, $v); // 暗抄送
        }
    }

    $mail->isHTML(true);
    $mail->Subject = getenv('INPUT_EMAIL_OBJECT');
    $mail->Body = getenv('INPUT_EMAIL_BODY');

    $mail->send();
    $core->debug('Message has been sent');
} catch (Exception $e) {
    $core->debug('Message could not be sent. Mailer Error: ', $mail->ErrorInfo);
}

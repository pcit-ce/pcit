<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PHPMailer\PHPMailer\PHPMailer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PHPMailerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['email'] = function () {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 4;
            $mail->isSMTP();
            $mail->Host = getenv('CI_EMAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('CI_EMAIL_USERNAME');
            $mail->Password = getenv('CI_EMAIL_PASSWORD');
            $mail->SMTPSecure = getenv('CI_EMAIL_SMTP_SECURE');
            $mail->Port = getenv('CI_EMAIL_SMTP_PORT');

            $mail->setFrom(getenv('CI_EMAIL_FROM'), getenv('CI_EMAIL_FROM_NAME'));

            return $mail;
        };
    }
}

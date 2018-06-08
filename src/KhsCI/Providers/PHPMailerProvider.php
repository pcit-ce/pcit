<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use PHPMailer\PHPMailer\PHPMailer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PHPMailerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['email'] = function () {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = getenv('CI_EMAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('CI_EMAIL_USERNAME');
            $mail->Password = getenv('CI_EMAIL_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom(getenv('CI_EMAIL_FROM'), getenv('CI_EMAIL_FROM_NAME'));

            return $mail;
        };
    }
}

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
            $mail->Host = config('email.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('email.username');
            $mail->Password = config('email.password');
            $mail->SMTPSecure = config('email.smtp_secure');
            $mail->Port = config('email.port');

            $mail->setFrom(config('email.from_address'), config('email.from_name'));

            return $mail;
        };
    }
}

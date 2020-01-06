<?php

declare(strict_types=1);

namespace App\Mail;

use Exception;
use PCIT\PCIT;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    /**
     * @var PHPMailer
     */
    private static $mail;

    public $from;

    public $subject;

    public $view;

    public $attach;

    public static function to(): void
    {
    }

    /**
     * @param array $address address and name
     * @param array $cc      address and name
     * @param array $bcc     address and name
     *
     * @throws Exception
     */
    public static function send(array $address,
                                string $subject,
                                string $body,
                                bool $html = true,
                                array $cc = [],
                                array $bcc = []): void
    {
        self::$mail = (new PCIT())->mail;

        try {
            $address && self::parseAddress($address);
            $cc && self::parseCC($cc);
            $bcc && self::parseBCC($bcc);

            self::$mail->isHTML($html);
            self::$mail->Subject = $subject;
            self::$mail->Body = $body;

            self::$mail->send();
            \Log::debug('Message has been sent');
        } catch (Exception $e) {
            \Log::debug('Message could not be sent. Mailer Error: ', self::$mail->ErrorInfo);
        } finally {
            self::$mail = null;
        }
    }

    private static function parseAddress(array $address): void
    {
        foreach ($address as $k => $v) {
            if (\is_int($k)) {
                $k = $v;
                $v = explode('@', $k)[0];
            }

            self::$mail->addAddress($k, $v);
        }
    }

    private static function parseCC(array $cc): void
    {
        foreach ($cc as $k => $v) {
            if (\is_int($k)) {
                $k = $v;
                $v = explode('@', $k)[0];
            }
            self::$mail->addCC($k, $v); // 抄送
        }
    }

    private static function parseBCC(array $bcc): void
    {
        foreach ($bcc as $k => $v) {
            if (\is_int($k)) {
                $k = $v;
                $v = explode('@', $k)[0];
            }
            self::$mail->addBCC($k, $v); // 暗抄送
        }
    }
}

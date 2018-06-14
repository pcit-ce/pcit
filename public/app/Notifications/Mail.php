<?php

declare(strict_types=1);

namespace App\Notifications;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Log;

class Mail
{
    /**
     * @param array  $address address and name
     * @param string $subject
     * @param string $body
     * @param bool   $html
     * @param array  $cc      address and name
     * @param array  $bcc     address and name
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
        $mail = (new KhsCI())->mail;

        try {
            foreach ($address as $k => $v) {
                if (is_int($k)) {
                    $k = $v;
                    $v = explode('@', $k)[0];
                }
                $mail->addAddress($k, $v);
            }

            if ($cc) {
                foreach ($cc as $k => $v) {
                    if (is_int($k)) {
                        $k = $v;
                        $v = explode('@', $k)[0];
                    }
                    $mail->addCC($k, $v); // 抄送
                }
            }

            if ($bcc) {
                foreach ($bcc as $k => $v) {
                    if (is_int($k)) {
                        $k = $v;
                        $v = explode('@', $k)[0];
                    }
                    $mail->addBCC($k, $v); // 暗抄送
                }
            }

            $mail->isHTML($html);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            Log::debug(__FILE__, __LINE__, 'Message has been sent');
        } catch (Exception $e) {
            Log::debug(
                __FILE__,
                __LINE__,
                'Message could not be sent. Mailer Error: ', $mail->ErrorInfo
            );
        }
    }
}

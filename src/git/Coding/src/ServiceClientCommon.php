<?php

declare(strict_types=1);

namespace PCIT\Coding;

use PCIT\GPI\ServiceClientCommon as GPIServiceClientCommon;

trait ServiceClientCommon
{
    use GPIServiceClientCommon;

    public function getAccessTokenUrlParameter(bool $raw = false)
    {
        $access_token = explode(' ', $this->curl->headers['x-coding-token'])[1];
        if ($raw) {
            return $access_token;
        }

        return 'access_token='.$access_token;
    }

    public function getTeamName()
    {
        return config('git.coding.oauth.team');
    }

    private function checkResultCode(string $result, bool $throw = false): void
    {
        $code = json_decode($result)->code;

        $message = 'Result Code Is Not 0, code is '.$code;

        if (0 === $code) {
            return;
        }

        \Log::debug($message);

        if ($throw) {
            throw new \Exception($message);
        }
    }
}

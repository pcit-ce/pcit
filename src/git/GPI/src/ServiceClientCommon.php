<?php

declare(strict_types=1);

namespace PCIT\GPI;

use Curl\Curl;

trait ServiceClientCommon
{
    protected Curl $curl;

    protected string $api_url;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @param $line
     */
    public function successOrFailure(int $http_code, bool $throw = false): void
    {
        $http_return_code = $this->curl->getCode();

        if ($http_code === $http_return_code) {
            return;
        }

        $message = 'Http Response Code Is Not '.$http_code.' , code is '.$http_return_code;

        \Log::emergency($message);

        if ($throw) {
            throw new \Exception($message);
        }
    }
}

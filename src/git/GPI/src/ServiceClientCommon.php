<?php

declare(strict_types=1);

namespace PCIT\GPI;

use Curl\Curl;

trait ServiceClientCommon
{
    protected $curl;

    protected $api_url;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @param $line
     *
     * @throws \Exception
     */
    private function successOrFailure(int $http_code, bool $throw = false): void
    {
        $http_return_code = $this->curl->getCode();

        $message = 'Http Response Code Is Not '.$http_return_code.' , code is '.$http_return_code;

        if ($http_code !== $http_return_code) {
            \Log::debug($message);
        }

        if ($throw) {
            throw new \Exception($message);
        }
    }
}

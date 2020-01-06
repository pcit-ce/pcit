<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service;

use Curl\Curl;

trait CICommon
{
    protected $curl;

    protected $api_url;

    /**
     * CICommon constructor.
     */
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
    private function successOrFailure(int $http_code): void
    {
        $http_return_code = $this->curl->getCode();

        if ($http_code !== $http_return_code) {
            \Log::debug('Http Return Code Is Not '.$http_code.' '.$http_return_code);
        }
    }
}

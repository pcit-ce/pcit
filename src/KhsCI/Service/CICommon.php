<?php

declare(strict_types=1);

namespace KhsCI\Service;

use Curl\Curl;
use KhsCI\Support\Log;

trait CICommon
{
    protected $curl;

    protected $api_url;

    /**
     * CICommon constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @param string $file
     * @param        $line
     * @param int    $http_code
     *
     * @throws \Exception
     */
    private function successOrFailure(string $file, $line, int $http_code): void
    {
        $http_return_code = $this->curl->getCode();

        if ($http_code !== $http_return_code) {
            Log::debug($file, $line, 'Http Return Code Is Not '.$http_code.' '.$http_return_code);
        }
    }
}

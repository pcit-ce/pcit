<?php

namespace KhsCI\Service\Issue;


use Curl\Curl;
use TencentAI\TencentAI;

class Issues
{
    /**
     * @var Curl
     */
    private static $curl;

    private static $api_url;
    /**
     * @var TencentAI
     */
    private static $tencent_ai;

    public function __construct(Curl $curl, string $api_url, TencentAI $tencent_ai)
    {
        static::$curl = $curl;

        static::$api_url = $api_url;

        static::$tencent_ai = $tencent_ai;
    }

    public function list(): void
    {
    }

    public function open(): void
    {
    }

    public function close(): void
    {
    }
}

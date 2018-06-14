<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use KhsCI\KhsCI;

class TencentAICommand
{
    /**
     * @return \TencentAI\TencentAI
     *
     * @throws \Exception
     */
    public static function get()
    {
        return (new KhsCI())->tencent_ai;
    }
}

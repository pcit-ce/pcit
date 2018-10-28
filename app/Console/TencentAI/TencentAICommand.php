<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use PCIT\PCIT;

class TencentAICommand
{
    /**
     * @return \TencentAI\TencentAI
     *
     * @throws \Exception
     */
    public static function get()
    {
        return (new PCIT())->tencent_ai;
    }
}

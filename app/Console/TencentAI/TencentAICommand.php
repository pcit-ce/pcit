<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use PCIT\PCIT;

class TencentAICommand
{
    /**
     * @return \TencentAI\TencentAI
     */
    public static function get()
    {
        return (app(PCIT::class))->tencent_ai;
    }
}

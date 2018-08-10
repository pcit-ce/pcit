<?php

declare(strict_types=1);

namespace App\Console;

use App\Notifications\WeChatTemplate;
use KhsCI\Support\Log;

class AgentCommand
{
    public function handle(): void
    {
        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        // $this->khsci->docker->system->ping(1);

        // Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        WeChatTemplate::send($build->build_key_id, $info);
    }
}

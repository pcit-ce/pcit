<?php

declare(strict_types=1);

namespace App\Http\Controllers\WeChat;

use PCIT\PCIT;

class MessageServer
{
    /**
     * @throws \Exception
     *
     * @return null|array|string
     */
    public function __invoke(PCIT $pcit)
    {
        return $pcit->wechat->server->pushHandler(function ($message): void {
        })->register();
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\WeChat;

use Exception;
use PCIT\PCIT;

class MessageServer
{
    /**
     * @return array|null|string
     *
     * @throws Exception
     */
    public function __invoke(PCIT $pcit)
    {
        return $pcit->wechat->server->pushHandler(function ($message) {
            return null;
        })->register();
    }
}

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
    public function __invoke()
    {
        $pcit = new PCIT();

        return $pcit->wechat->server->pushHandler(function ($message) {
            return null;
        })->register();
    }
}

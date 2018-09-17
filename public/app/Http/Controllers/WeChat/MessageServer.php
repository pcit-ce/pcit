<?php

declare(strict_types=1);

namespace App\Http\Controllers\WeChat;

use Exception;
use KhsCI\KhsCI;

class MessageServer
{
    /**
     * @return array|null|string
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI();

        return $khsci->wechat->server->pushHandler(function ($message) {
            return null;
        })->register();
    }
}

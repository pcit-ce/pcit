<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\OAuth;

use PCIT\Service\OAuth\CodingClient;

class CodingController extends Kernel
{
    /**
     * @var CodingClient
     */
    protected static $oauth;

    /**
     * @var CodingClient
     */
    protected static $git_type = 'coding';
}

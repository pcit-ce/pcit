<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\OAuth;

use PCIT\Service\Coding\OAuth\Client;

class CodingController extends Kernel
{
    /**
     * @var Client
     */
    protected static $oauth;

    /**
     * @var Client
     */
    protected static $git_type = 'coding';
}

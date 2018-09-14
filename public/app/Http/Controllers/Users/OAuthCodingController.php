<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use KhsCI\Service\OAuth\CodingClient;

class OAuthCodingController extends OAuthKernel
{
    use OAuthTrait;

    /**
     * @var CodingClient
     */
    protected static $oauth;

    /**
     * @var CodingClient
     */
    protected static $git_type = 'coding';
}

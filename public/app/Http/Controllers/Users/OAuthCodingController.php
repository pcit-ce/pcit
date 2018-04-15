<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use KhsCI\KhsCI;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Support\Response;

class OAuthCodingController
{
    use OAuthTrait;

    private $ci;

    public function __construct()
    {
        $this->ci = new KhsCI();
    }

    public function getLoginUrl(): void
    {
        $url = $this->ci->OAuthCoding->getLoginUrl(null);

        Response::redirect($url);
    }

    /**
     * @throws \Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon('coding', null);
    }
}

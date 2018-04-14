<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class OAuthCodingController
{
    private $khsci;

    public function __construct()
    {
        $this->khsci = new KhsCI();
    }

    public function getLoginUrl(): void
    {
        $this->khsci->OAuthCoding->getLoginUrl(null);
    }

    /**
     * @throws \Exception
     */
    public function getAccessToken(): void
    {
        $code = $_GET['code'] ?? false;

        if (false === $code) {
            throw new Exception('code not found');
            return;
        }

        $access_token = Session::get('coding.access_token')
            ?? (json_decode($this->khsci->OAuthCoding->getAccessToken((string) $code, null)))->access_token
            ?? false;

        false !== $access_token && Session::put('coding.access_token', $access_token);

        $userInfoArray = Coding::getUserInfo((string) $access_token);

        $name = $userInfoArray['name'];

        $pic = $userInfoArray['pic'];

        Session::put('coding.user', $name);
        Session::put('coding.pic', $pic);

        Response::redirect(getenv('CI_HOST').'/profile/coding/'.$name);
    }
}

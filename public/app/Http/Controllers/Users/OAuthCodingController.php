<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\Coding;
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
            ?? (json_decode($this->khsci->OAuthCoding->getAccessToken((string)$code, null)))->access_token
            ?? false;

        false !== $access_token && Session::put('coding.access_token', $access_token);

        $userInfoArray = Coding::getUserInfo((string)$access_token);

        $json = json_decode(Coding::getProjects((string)$access_token))->data ?? false;
        $num = $json->totalRow ?? false;

        for ($i = 0; $i < $num; $i++) {
            $list = ($json->list)[$i];
            $array[] = $list->owner_user_name.'/'.$list->name;
        }

        echo 'Welcome<br>'.$userInfoArray['name'].'<img src='.$userInfoArray['pic'].'><hr>';

        var_dump($array ?? []);
    }
}

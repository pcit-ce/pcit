<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use KhsCI\KhsCI;
use Exception;
use KhsCI\Service\OAuth\Coding;

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

        if ($code) {
            $access_token = $_SESSION['coding']['access_token']
                ?? (json_decode($this->khsci->OAuthCoding->getAccessToken((string)$code, null)))->access_token
                ?? false;

            false !== $access_token && $_SESSION['coding']['access_token'] = $access_token;

            $json = json_decode(Coding::getUserInfo($access_token))->data ?? false;
            $uid = $json->id ?? false;
            $name = $json->global_key ?? false;
            $pic = $json->avatar ?? false;

            $json = json_decode(Coding::getProjects((string)$access_token))->data ?? false;
            $num = $json->totalRow ?? false;

            for ($i = 0; $i < $num; ++$i) {
                $list = ($json->list)[$i];
                $array[] = $list->owner_user_name.'/'.$list->name;
            }

            echo 'Welcome<br>'.($name ?? false)."<img src=$pic>";

            echo '<hr>';

            var_dump($array ?? []);
        } else {
            throw new Exception('code not found');
        }
    }
}

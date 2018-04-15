<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Error;
use Exception;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

trait OAuthTrait
{
    /**
     * @param string      $type
     * @param null|string $state
     *
     * @return string
     *
     * @throws Exception
     */
    public function getAccessTokenCommon(string $type, ?string $state)
    {
        $code = $_GET['code'] ?? false;

        if (false === $code) {
            throw new Exception('code not found');
            return;
        }

        $obj = 'KhsCI\\Service\\OAuth\\'.ucfirst($type);

        try {
            $method = 'OAuth'.ucfirst($type);

            $access_token = Session::get($type.'.access_token')
                ?? $this->ci->$method->getAccessToken((string) $code, $state)
                ?? false;

            $typeLower = strtolower($type);

            false !== $access_token && Session::put($typeLower.'.access_token', $access_token);

            $userInfoArray = $obj::getUserInfo((string) $access_token);
        } catch (Error $e) {
            return $e->getMessage();
        }

        $uid = $userInfoArray['uid'];
        $name = $userInfoArray['name'];
        $pic = $userInfoArray['pic'];

        Session::put($typeLower.'.uid', $uid);
        Session::put($typeLower.'.username', $name);
        Session::put($typeLower.'.pic', $pic);

        Response::redirect(getenv('CI_HOST').'/profile/'.$typeLower.'/'.$name);
    }
}

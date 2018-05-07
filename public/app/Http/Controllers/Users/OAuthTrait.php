<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

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
     * @throws Exception
     */
    public function getAccessTokenCommon(string $type, ?string $state): void
    {
        $code = $_GET['code'] ?? false;

        if (false === $code) {
            throw new Exception('code not found');
        }

        try {

            $access_token = $this->oauth->getAccessToken((string)$code, $state)
                ?? false;

            $typeLower = strtolower($type);

            false !== $access_token && Session::put($typeLower.'.access_token', $access_token);

            $userInfoArray = $this->oauth::getUserInfo((string)$access_token);
        } catch (Error $e) {
            throw new Exception($e->getMessage(), 500);
        }

        $uid = $userInfoArray['uid'];
        $name = $userInfoArray['name'];
        $pic = $userInfoArray['pic'];
        $email = $userInfoArray['email'];

        Session::put($typeLower.'.uid', $uid);
        Session::put($typeLower.'.username', $name);
        Session::put($typeLower.'.pic', $pic);
        Session::put($typeLower.'.email', $email);

        Response::redirect(getenv('CI_HOST').'/profile/'.$typeLower.'/'.$name);
    }
}

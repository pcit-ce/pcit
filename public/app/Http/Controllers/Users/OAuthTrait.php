<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Error;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\Gist\GiteeClient;
use KhsCI\Service\OAuth\GitHubAppClient;
use KhsCI\Service\Users\CodingClient;
use KhsCI\Service\Users\GitHubClient;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

trait OAuthTrait
{
    /**
     * @var GitHubAppClient|CodingClient|GitHubClient|GiteeClient
     */
    protected static $oauth;

    /**
     * @param null|string $state
     *
     * @throws Exception
     */
    public function getAccessTokenCommon(?string $state): void
    {
        $code = $_GET['code'] ?? false;

        if (false === $code) {
            throw new Exception('code not found');
        }

        try {
            $access_token = static::$oauth->getAccessToken((string) $code, $state)
                ?? false;

            $git_type = self::$git_type;

            false !== $access_token && Session::put($git_type.'.access_token', $access_token);

            $khsci = new KhsCI([$git_type.'_access_token' => $access_token], $git_type);

            $userInfoArray = $khsci->user_basic_info->getUserInfo();
        } catch (Error $e) {
            throw new Exception($e->getMessage(), 500);
        }

        $uid = $userInfoArray['uid'];
        $name = $userInfoArray['name'];
        $pic = $userInfoArray['pic'];
        $email = $userInfoArray['email'];

        Session::put($git_type.'.uid', $uid);
        Session::put($git_type.'.username', $name);
        Session::put($git_type.'.pic', $pic);
        Session::put($git_type.'.email', $email);
        Session::put($git_type.'.expire', time() + 24 * 60 * 60);

        Response::redirect(getenv('CI_HOST').'/profile/'.$git_type.'/'.$name);
    }
}

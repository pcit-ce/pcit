<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Users\JWTController;
use App\User;
use PCIT\Framework\Support\Response;
use PCIT\Framework\Support\Session;
use PCIT\Support\Env;

/**
 * 个人中心.
 */
abstract class Kernel
{
    /**
     * @var string
     */
    protected $git_type;

    /**
     * 个人中心 profile.
     *
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __invoke(...$args): void
    {
        $git_type = $this->git_type;

        $username_from_web = $args[0];
        $access_token = Session::get($git_type.'.access_token');
        $username = Session::get($git_type.'.username');
        $uid = Session::get($git_type.'.uid');
        $pic = Session::get($git_type.'.pic');
        $email = Session::get($git_type.'.email');

        if (null === $username or null === $access_token) {
            Response::redirect(env('CI_HOST').'/login');

            exit;
        }

        if ($username_from_web !== $username) {
            Response::redirect('/profile/'.$git_type.'/'.$username);

            exit;
        }

        $result = JWTController::generate($git_type, $username, (int) $uid);
        $api_token = $result['token'];

        setcookie(
            $git_type.'_api_token',
            $api_token,
            time() + 24 * 60 * 60,
            '/',
            env('CI_SESSION_DOMAIN', 'ci.khs1994.com'), true
        );

        User::updateUserInfo((int) $uid, null, (string) $username, (string) $email, (string) $pic, false, $git_type);
        User::updateAccessToken((int) $uid, $access_token, $git_type);

        view('profile/index.html');
    }
}

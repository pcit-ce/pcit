<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Users\JWTController;
use App\User;
use PCIT\Support\Env;
use PCIT\Support\Response;
use PCIT\Support\Session;

abstract class Kernel
{
    /**
     * @var string
     */
    protected $git_type;

    /**
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __invoke(...$args)
    {
        $git_type = $this->git_type;

        $username_from_web = $args[0];
        $access_token = Session::get($git_type.'.access_token');
        $username = Session::get($git_type.'.username');
        $uid = Session::get($git_type.'.uid');
        $pic = Session::get($git_type.'.pic');
        $email = Session::get($git_type.'.email');

        if (null === $username or null === $access_token) {
            Response::redirect(Env::get('CI_HOST').'/login');

            exit;
        }

        if ($username_from_web !== $username) {
            Response::redirect('/profile/'.$git_type.'/'.$username);

            exit;
        }

        $api_token = JWTController::generate($git_type, $username, (int) $uid);

        setcookie(
            $git_type.'_api_token',
            $api_token,
            time() + 24 * 60 * 60,
            '',
            Env::get('CI_SESSION_DOMAIN', 'ci.khs1994.com'), true
        );

        User::updateUserInfo((int) $uid, null, (string) $username, (string) $email, (string) $pic, false, $git_type);

        User::updateAccessToken((int) $uid, $access_token, $git_type);

        return view('profile/index.html');
    }
}

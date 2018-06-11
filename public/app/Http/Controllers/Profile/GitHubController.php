<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\APITokenController;
use App\User;
use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubController
{
    protected $git_type = 'github';

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

        $api_token = APITokenController::find($git_type, $username, (int) $uid);

        setcookie(
            $git_type.'_api_token',
            $api_token,
            time() + 24 * 60 * 60,
            '',
            Env::get('CI_SESSION_DOMAIN', 'ci.khs1994.com'), true
        );

        User::updateUserInfo($git_type, (int) $uid, (string) $username, (string) $email, (string) $pic, $access_token);

        require __DIR__.'/../../../../public/profile/index.html';

        exit;
    }
}

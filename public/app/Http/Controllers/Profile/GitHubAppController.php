<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubAppController
{
    private static $git_type = 'github_app';

    /**
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __invoke(...$args)
    {
        $git_type = static::$git_type;

        $username_from_web = $args[0];
        $access_token = Session::get($git_type.'.access_token');
        $username = Session::get($git_type.'.username');
        $uid = Session::get($git_type.'.uid');
        $pic = Session::get($git_type.'.pic');
        if (null === $username or null === $access_token) {
            Response::redirect(Env::get('CI_HOST').'/login');
        }

        if ($username_from_web !== $username) {
            Response::redirect('/profile/'.$git_type.'/'.$username);
        }

        $ajax = $_GET['ajax'] ?? false;

        // ?ajax=true $ajax=true
        // cache

        if (!($ajax)) {
            // 非 ajax 请求返回静态 HTML 页面

            require __DIR__.'/../../../../public/profile/index.html';
            exit;
        }

        $sync = true;

        $sql = 'SELECT * FROM repo WHERE git_type=? AND JSON_CONTAINS(repo_admin,?)';

        $repo_array = DB::select($sql, [$git_type, "\"$uid\""]);

        foreach ($repo_array as $k) {
            $array[$k['repo_full_name']] = 1;
        }

        return [
            'code' => 200,
            'git_type' => $git_type,
            'uid' => $uid,
            'username' => $username,
            'pic' => $pic,
            'cache' => false === $sync,
            'repos' => $array,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\Service\OAuth\Coding;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class CodingController
{
    public function __invoke(...$arg): void
    {
        $uid = Session::get('coding.uid');
        $username = Session::get('coding.username');
        $arg[0] === $username && $username = $arg[0];
        $pic = Session::get('coding.pic');
        $access_token = Session::get('coding.access_token');

        $json = json_decode(Coding::getProjects((string) $access_token))->data ?? false;
        $num = $json->totalRow ?? false;
        $array = [];
        for ($i = 0; $i < $num; ++$i) {
            $list = ($json->list)[$i];
            $array[] = $list->owner_user_name.'/'.$list->name;
        }

        Response::json([
            'code' => 0,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'repos' => $array,
        ]);
    }
}

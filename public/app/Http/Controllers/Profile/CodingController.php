<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Session;

class CodingController
{
    /**
     * @param mixed ...$arg
     *
     * @return array
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
        $uid = Session::get('coding.uid');
        $username = Session::get('coding.username');
        $arg[0] === $username && $username = $arg[0];
        $pic = Session::get('coding.pic');
        $access_token = Session::get('coding.access_token');

        $khsci = new KhsCI();
        $oauth = $khsci->oauth_coding;
        $json = json_decode($oauth::getProjects((string) $access_token))->data ?? false;

        $num = $json->totalRow ?? false;
        $array = [];
        for ($i = 0; $i < $num; ++$i) {
            $list = ($json->list)[$i];
            $array[] = $list->owner_user_name.'/'.$list->name;
        }

        return [
            'code' => 200,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'repos' => $array,
        ];
    }
}

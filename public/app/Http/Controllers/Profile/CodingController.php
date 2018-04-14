<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\Service\OAuth\Coding;
use KhsCI\Support\Session;

class CodingController
{
    public function __invoke(...$arg): void
    {
        echo $arg[0];
        var_dump(Session::all());
        $access_token = Session::get('coding.access_token');
        $json = json_decode(Coding::getProjects((string) $access_token))->data ?? false;
        $num = $json->totalRow ?? false;
        $array = [];
        for ($i = 0; $i < $num; ++$i) {
            $list = ($json->list)[$i];
            $array[] = $list->owner_user_name.'/'.$list->name;
        }

        var_dump($array);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\Service\OAuth\GitHub;
use KhsCI\Support\Session;

class GitHubController
{
    public function __invoke(...$arg): void
    {
        echo $arg[0];

        $accessToken = Session::get('github.access_token');

        $array = [];

        for ($page = 1; $page <= 100; ++$page) {
            $json = GitHub::getProjects((string) $accessToken, $page);

            if ($obj = json_decode($json)) {
                for ($i = 0; $i < 30; ++$i) {
                    $obj_repo = $obj[$i] ?? false;

                    if (false === $obj_repo) {
                        break;
                    }

                    $full_name = $obj_repo->full_name ?? false;

                    $id = $obj_repo->id;

                    $array[$id] = $full_name;
                }
            } else {
                break;
            }
        }

        var_dump($array);
    }
}

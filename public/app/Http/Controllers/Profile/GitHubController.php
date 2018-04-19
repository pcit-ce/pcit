<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use ErrorException;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubController
{
    const TYPE = 'gitHub';

    /**
     * @param mixed ...$arg
     *
     * @return array
     * @throws ErrorException
     */
    public function __invoke(...$arg)
    {
        $type = static::TYPE;

        $typeLower = strtolower($type);

        $uid = Session::get($typeLower.'.uid');
        $username = Session::get($typeLower.'.username');
        $arg[0] === $username && $username = $arg[0];
        $pic = Session::get($typeLower.'.pic');
        $accessToken = Session::get($typeLower.'.access_token');

        $array = [];

        $objClass = 'KhsCI\\Service\\OAuth\\'.ucfirst($type);

        for ($page = 1; $page <= 3; ++$page) {
            try {
                $json = $objClass::getProjects((string)$accessToken, $page);
            } catch (ErrorException $e) {
                throw new ErrorException($e->getMessage(), $e->getCode());
            }

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

        return [
            'code' => 200,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'repos' => $array,
        ];
    }
}

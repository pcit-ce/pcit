<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\GitHub;

class OAuthGitHubController
{
    private $khsci;

    public function __construct()
    {
        $this->khsci = new KhsCI();
    }

    public function getLoginUrl(): void
    {
        $state = session_create_id();

        $_SESSION['github']['state'] = $state;

        $this->khsci->OAuthGitHub->getLoginUrl($state);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken()
    {
        $code = $_GET['code'];
        $getState = $_GET['state'] ?? 404;

        $state = $_SESSION['github']['state'] ?? false;

        if ($state !== $getState) {
            throw new Exception('state not same');
            return;
        }

        $accessToken = $_SESSION['github']['access_token']
            ?? $this->khsci->OAuthGitHub->getAccessToken($code, (string)$state)
            ?? false;

        false !== $accessToken && $_SESSION['github']['access_token'] = $accessToken;

        $userInfoArray = GitHub::getUserInfo((string)$accessToken);

        echo 'Welcome '.$userInfoArray['name'].'<img src='.$userInfoArray['pic'].'><hr>';

        $array = [];
        for ($page = 1; $page <= 100; $page++) {
            $json = GitHub::getProjects((string)$accessToken, $page);
            if ($obj = json_decode($json)) {
                for ($i = 0; $i < 30; $i++) {
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

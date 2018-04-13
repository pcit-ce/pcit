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
    public function getAccessToken(): void
    {
        $code = $_GET['code'];
        $state = $_SESSION['github']['state'];

        if ($state !== $_GET['state']) {
            throw new Exception('state not same');
        }

        $accessToken = $_SESSION['github']['access_token']
            ?? $this->khsci->OAuthGitHub->getAccessToken($code, $state)
            ?? false;

        false !== $accessToken && $_SESSION['github']['access_token'] = $accessToken;
        $array = [];
        for ($page = 1; $page <= 100; $page++) {
            $json = GitHub::getProjects((string)$accessToken, $page);
            if ($obj = json_decode($json)) {
                for ($i = 0; $i < 30; $i++) {
                    $obj_repo = $obj[$i] ?? false;

                    if (false === $obj_repo) {
                        break;
                    };

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

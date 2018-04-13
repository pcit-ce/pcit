<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Exception;
use KhsCI\KhsCI;

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

        echo $this->khsci->OAuthGitHub->getAccessToken($code, $state);
    }
}

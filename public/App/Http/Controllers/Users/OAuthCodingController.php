<?php

namespace App\Http\controllers\Users;

use KhsCI\KhsCI;

class OAuthCodingController
{
    private $khsci;

    public function __construct()
    {
        $config = [
            'coding' => [
                'client_id' => getenv('CODING_CLIENT_ID'),
                'client_secret' => getenv('CODING_CLIENT_SECRET'),
                'callback_url' => getenv('CODING_CALLBACK_URL'),
            ],
            'gitee' => [
                'client_id' => getenv('GITEE_CLIENT_ID'),
                'client_secret' => getenv('GITEE_CLIENT_SECRET'),
                'callback_url' => getenv('GITEE_CALLBACK_URL'),
            ],
            'github' => [
                'client_id' => getenv('GITHUB_CLIENT_ID'),
                'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
                'callback_url' => getenv('GITHUB_CALLBACK_URL'),
            ],
        ];

        $this->khsci = new KhsCI($config);
    }

    public function getLoginUrl()
    {
        $this->khsci->OAuthCoding->getLoginUrl();
    }

    public function getAccessToken()
    {
        $this->khsci->OAuthCoding->getAccessToken();
    }

}
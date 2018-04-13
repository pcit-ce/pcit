<?php

namespace App\Http\controllers\Users;

use KhsCI\KhsCI;
use Exception;

class OAuthCodingController
{
    private $khsci;

    public function __construct()
    {
        $this->khsci = new KhsCI();
    }

    public function getLoginUrl()
    {
        $this->khsci->OAuthCoding->getLoginUrl();
    }

    /**
     * @throws \Exception
     */
    public function getAccessToken()
    {
        $code = $_GET['code'] ?? false;
        if ($code) {
            echo $this->khsci->OAuthCoding->getAccessToken($code);
        } else {
            throw new Exception('code not found');
        }
    }
}
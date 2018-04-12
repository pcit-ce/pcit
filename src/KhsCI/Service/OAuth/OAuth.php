<?php

namespace KhsCI\Service\OAuth;

interface OAuth
{
    public function getLoginUrl();


    public function getAccessToken();
}
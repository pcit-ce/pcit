<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub\UserBasicInfo;

class Sender
{
    public $uid;
    public $username;
    public $pic;

    public function __construct($sender)
    {
        $this->uid = $sender->id;
        $this->username = $sender->login;
        $this->pic = $sender->avatar_url;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\User;

use PCIT\GPI\Webhooks\Context\Components\Kernel;

class Sender extends Kernel
{
    public $uid;

    public $username;

    public $pic;

    public function __construct($sender)
    {
        $this->uid = $sender->id;
        $this->username = $sender->login;
        $this->pic = $sender->avatar_url;

        parent::__construct($sender);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Parser\UserBasicInfo;

class Author
{
    public $name;

    public $username;

    public $email;

    public function __construct($author)
    {
        $this->name = $author->name;
        $this->email = $author->email;
        $this->username = $author->username;
    }
}

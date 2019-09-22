<?php

declare(strict_types=1);

namespace PCIT\GitHub\WebhooksParse\UserBasicInfo;

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

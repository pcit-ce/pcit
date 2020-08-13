<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\User;

class Author
{
    /** @var string */
    public $name;

    /** @var string */
    public $username;

    /** @var string */
    public $email;

    public function __construct($author)
    {
        $this->name = $author->name;
        $this->email = $author->email;
        $this->username = $author->username ?? '';
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\User;

/** @property string $username */
class Author
{
    public string $name;

    public ?string $email;

    public function __get(string $name)
    {
        if ('username' === $name) {
            return $this->$name ?? '';
        }
    }
}

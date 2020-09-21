<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\Author;

class Commits
{
    public string $sha;
    public string $message;
    public Author $author;
}

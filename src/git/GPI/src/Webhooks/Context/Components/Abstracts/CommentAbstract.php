<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\Abstracts;

use PCIT\GPI\Webhooks\Context\Components\User\User;

abstract class CommentAbstract
{
    public int $id;

    public User $user;

    public string $body;

    public string $author_association;
}

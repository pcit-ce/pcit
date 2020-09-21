<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\Abstracts\CommentAbstract;

class CommitComment extends CommentAbstract
{
    public string $commit_id;
}

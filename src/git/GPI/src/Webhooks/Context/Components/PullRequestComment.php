<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\Abstracts\CommentAbstract;

class PullRequestComment extends CommentAbstract
{
    public string $path;

    public int $position;

    public int $pull_request_review_id;

    public string $commit_id;
}

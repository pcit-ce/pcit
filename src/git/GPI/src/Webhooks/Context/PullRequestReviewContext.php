<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\PullRequest;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class PullRequestReviewContext extends Context
{
    public PullRequest $pull_request;

    public $review;

    public $changes;

    use ContextTrait;
}

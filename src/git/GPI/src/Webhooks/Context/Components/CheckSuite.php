<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

class CheckSuite
{
    public int $id;

    public string $head_branch;

    public string $head_sha;

    public string $status;

    public ?string $conclusion;

    public $before;

    public $after;

    /** @var PullRequests[] */
    public $pull_requests;

    public HeadCommit $head_commit;
}

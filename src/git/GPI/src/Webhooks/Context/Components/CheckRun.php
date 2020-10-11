<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

class CheckRun
{
    public int $id;

    public string $head_sha;

    public string $status;

    public ?string $conclusion;

    public string $name;

    /** @var PullRequests[] */
    public $pull_requests;

    public ?CheckSuite $check_suite;

    public string $external_id;
}

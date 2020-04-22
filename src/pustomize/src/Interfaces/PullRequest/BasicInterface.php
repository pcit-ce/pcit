<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\PullRequest;

use PCIT\GitHub\Webhooks\Parser\PullRequestContext;

/**
 * Triggered when a pull request is `assigned`, `unassigned`,
 * `labeled`, `unlabeled`,
 * `opened`, `edited`, `closed`, `reopened`,
 * `synchronize`, `ready_for_review`,
 * `locked`, `unlocked` or when a pull request review is `requested` or `removed`.
 *
 * @see https://developer.github.com/v3/activity/events/types/#pullrequestevent
 */
interface BasicInterface
{
    public function handle(PullRequestContext $context);
}

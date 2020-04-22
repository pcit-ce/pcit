<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\Issue;

use PCIT\GitHub\Webhooks\Parser\IssuesContext;

/**
 * Triggered when an issue is `opened`, `edited`, `deleted`,
 * `pinned`, `unpinned`, `closed`, `reopened`, `assigned`, `unassigned`,
 * `labeled`, `unlabeled`, `locked`, `unlocked`, `transferred`,
 * `milestoned`, or `demilestoned`.
 */
interface BasicInterface
{
    /**
     * 当 issue opened 时调用.
     */
    public function handle(IssuesContext $context);
}

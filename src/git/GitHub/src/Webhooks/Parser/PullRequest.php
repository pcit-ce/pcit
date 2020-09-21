<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\PullRequestContext;

class PullRequest
{
    /**
     * "assigned", "unassigned",
     * "review_requested", "review_request_removed",
     * "labeled", "unlabeled",
     * "opened", "synchronize", "edited", "closed", or "reopened".
     */
    public static function handle(string $webhooks_content): PullRequestContext
    {
        $pullRequestContext = new PullRequestContext([], $webhooks_content);

        \Log::info('Receive event', ['type' => 'pull request', 'action' => $pullRequestContext->action]);

        $pull_request = $pullRequestContext->pull_request;
        $event_time = $pull_request->updated_at ?? $pull_request->created_at;
        $event_time = Date::parse($event_time);

        // head 向 base 提交 PR
        $base = $pull_request->base;
        $head = $pull_request->head;

        $commit_message = $pull_request->title;

        $committer_username = $pull_request->user->login;
        $committer_uid = $pull_request->user->id;

        $branch = $base->ref;

        // 检查内外部 PR

        $internal = ($head->repo->id === $base->repo->id) ? 1 : 0;

        $pull_request_source = $head->repo->full_name;

        // 谁开启 PR
        // 谁推送的 commit 多个

        $repository = $pullRequestContext->repository;

        $pullRequestContext->rid = $base->repo->id;
        $pullRequestContext->repo_full_name = $base->repo->full_name;
        $pullRequestContext->event_time = $event_time;
        $pullRequestContext->commit_message = $commit_message;
        $pullRequestContext->commit_id = $head->sha;
        $pullRequestContext->committer_username = $committer_username;
        $pullRequestContext->committer_uid = $committer_uid;
        $pullRequestContext->pull_request_number = $pull_request->number;
        $pullRequestContext->branch = $branch;
        $pullRequestContext->internal = $internal;
        $pullRequestContext->pull_request_source = $pull_request_source;
        $pullRequestContext->owner = $repository->owner;
        $pullRequestContext->private = $repository->private;
        $pullRequestContext->pullRequestBase = $base;
        $pullRequestContext->pullRequestHead = $head;

        return $pullRequestContext;
    }
}

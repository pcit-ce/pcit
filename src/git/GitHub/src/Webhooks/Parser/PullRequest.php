<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\Components\PullRequest\Base as PullRequestBase;
use PCIT\GPI\Webhooks\Context\Components\PullRequest\Head as PullRequestHead;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
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
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'pull request', 'action' => $action]);

        $pull_request = $obj->pull_request;
        $event_time = $pull_request->updated_at ?? $pull_request->created_at;
        $event_time = Date::parse($event_time);

        // head 向 base 提交 PR
        $pull_request_base = new PullRequestBase($pull_request->base);
        $pull_request_head = new PullRequestHead($pull_request->head);

        $pull_request_base->repo = new Repository($pull_request_base->repo);
        $pull_request_base->repo->owner = new Owner($pull_request_base->repo->owner, 'User' !== $pull_request_base->repo->owner->type);

        $pull_request_head->repo = new Repository($pull_request_head->repo);
        $pull_request_base->repo->owner = new Owner($pull_request_base->repo->owner, 'User' !== $pull_request_base->repo->owner->type);

        $commit_message = $pull_request->title;
        $commit_id = $pull_request_head->sha;

        $committer_username = $pull_request->user->login;
        $committer_uid = $pull_request->user->id;

        $pull_request_number = $obj->number;
        $branch = $pull_request_base->ref;
        $installation_id = $obj->installation->id ?? null;

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        // 检查内外部 PR

        $internal = ($pull_request_head->repo->id === $pull_request_base->repo->id) ? 1 : 0;

        $pull_request_source = $pull_request_head->repo->full_name;

        // 谁开启 PR
        // 谁推送的 commit 多个

        $pullRequestContext = new PullRequestContext([], $webhooks_content);

        $pullRequestContext->installation_id = $installation_id;
        $pullRequestContext->rid = $pull_request_base->repo->id;
        $pullRequestContext->repo_full_name = $pull_request_base->repo->full_name;
        $pullRequestContext->action = $action;
        $pullRequestContext->event_time = $event_time;
        $pullRequestContext->commit_message = $commit_message;
        $pullRequestContext->commit_id = $commit_id;
        $pullRequestContext->committer_username = $committer_username;
        $pullRequestContext->committer_uid = $committer_uid;
        $pullRequestContext->pull_request_number = $pull_request_number;
        $pullRequestContext->branch = $branch;
        $pullRequestContext->internal = $internal;
        $pullRequestContext->pull_request_source = $pull_request_source;
        $pullRequestContext->owner = $repository->owner;
        $pullRequestContext->private = $repository->private;
        $pullRequestContext->pullRequestBase = $pull_request_base;
        $pullRequestContext->pullRequestHead = $pull_request_head;

        return $pullRequestContext;
    }
}

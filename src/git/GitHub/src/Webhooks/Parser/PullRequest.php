<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\PullRequestContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

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
        $pull_request_base = $pull_request->base;
        $pull_request_head = $pull_request->head;

        $rid = $pull_request_base->repo->id;
        $repo_full_name = $pull_request_base->repo->full_name;

        $commit_message = $pull_request->title;
        $commit_id = $pull_request_head->sha;

        $committer_username = $pull_request->user->login;
        $committer_uid = $pull_request->user->id;

        $pull_request_number = $obj->number;
        $branch = $pull_request->base->ref;
        $installation_id = $obj->installation->id ?? null;

        $repository = $obj->repository;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        // 检查内外部 PR

        $internal = ($pull_request_head->repo->id === $pull_request_base->repo->id) ? 1 : 0;

        $pull_request_source = $pull_request_head->repo->full_name;

        $org = ($obj->organization ?? false) ? true : false;

        // 谁开启 PR
        // 谁推送的 commit 多个

        $pullRequestContext = new PullRequestContext([], $webhooks_content);

        $pullRequestContext->installation_id = $installation_id;
        $pullRequestContext->rid = $rid;
        $pullRequestContext->repo_full_name = $repo_full_name;
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
        $pullRequestContext->account = new Account($repository_owner, $org);
        $pullRequestContext->private = $repository->private;

        return $pullRequestContext;
    }
}

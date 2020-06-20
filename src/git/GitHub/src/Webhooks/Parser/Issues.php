<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

class Issues
{
    private static $skip_list = [
        'CLAassistant',
    ];

    public static function handle(string $webhooks_content): IssuesContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'issue', 'action' => $action]);

        $issue = $obj->issue;

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $issue_id = $issue->id;
        $issue_number = $issue->number;
        $title = $issue->title;
        $body = $issue->body;

        $sender = $obj->sender;
        $sender_username = $sender->login;
        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $state = $issue->state;
        $locked = $issue->locked ?? false;
        $assignees = $issue->assignees ?? null;
        $labels = $issue->labels;
        $created_at = Date::parse($issue->created_at);
        $updated_at = Date::parse($issue->updated_at);
        $closed_at = Date::parse($issue->closed_at ?? 0);

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        $issuesContext = new IssuesContext([], $webhooks_content);

        $issuesContext->installation_id = $installation_id;
        $issuesContext->rid = $rid;
        $issuesContext->repo_full_name = $repo_full_name;
        $issuesContext->sender_username = $sender_username;
        $issuesContext->sender_uid = $sender_uid;
        $issuesContext->sender_pic = $sender_pic;
        $issuesContext->issue_id = $issue_id;
        $issuesContext->issue_number = $issue_number;
        $issuesContext->title = $title;
        $issuesContext->body = $body;
        $issuesContext->created_at = $created_at;
        $issuesContext->updated_at = $updated_at;
        $issuesContext->account = new Account($repository_owner, $org);
        $issuesContext->action = $action;
        $issuesContext->state = $state;
        $issuesContext->labels = $labels;
        $issuesContext->assignees = $assignees;
        $issuesContext->locked = $locked;
        $issuesContext->closed_at = $closed_at;

        return $issuesContext;
    }

    public static function comment(string $webhooks_content): IssueCommentContext
    {
        \Log::info('Receive issue comment event', []);

        $obj = json_decode($webhooks_content);

        $action = $obj->action;
        $comment = $obj->comment;

        $sender = $comment->user;
        $sender_username = $sender->login;

        if (strpos($sender_username, '[bot]') or \in_array($sender_username, self::$skip_list, true)) {
            \Log::info('Bot issue comment SKIP', []);

            throw new \Exception('skip', 200);
        }

        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        // gitee
        $issue = $obj->issue ?? $obj->pull_request;
        $issue_id = $issue->id;
        $issue_number = $issue->number;

        $comment_id = $comment->id;
        $body = $comment->body;

        $created_at = Date::parse($comment->created_at);
        $updated_at = Date::parse($comment->updated_at);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;
        $is_pull_request = ($issue->pull_request ?? false) ? true : false;

        // gitee
        if ($obj->pull_request ?? false) {
            $is_pull_request = true;
        }

        $issueCommentContext = new IssueCommentContext([], $webhooks_content);

        $issueCommentContext->installation_id = $installation_id;
        $issueCommentContext->rid = $rid;
        $issueCommentContext->repo_full_name = $repo_full_name;
        $issueCommentContext->sender_username = $sender_username;
        $issueCommentContext->sender_uid = $sender_uid;
        $issueCommentContext->sender_pic = $sender_pic;
        $issueCommentContext->issue_id = $issue_id;
        $issueCommentContext->issue_number = $issue_number;
        $issueCommentContext->comment_id = $comment_id;
        $issueCommentContext->body = $body;
        $issueCommentContext->created_at = $created_at;
        $issueCommentContext->updated_at = $updated_at;
        $issueCommentContext->account = new Account($repository_owner, $org);
        $issueCommentContext->action = $action;
        $issueCommentContext->is_pull_request = $is_pull_request;

        return $issueCommentContext;
    }
}

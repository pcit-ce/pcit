<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;

class IssueComment
{
    private static $skip_list = [
        'CLAassistant',
    ];

    public static function handle(string $webhooks_content): IssueCommentContext
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

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $installation_id = $obj->installation->id ?? null;

        $is_pull_request = ($issue->pull_request ?? false) ? true : false;

        // gitee
        if ($obj->pull_request ?? false) {
            $is_pull_request = true;
        }

        $issueCommentContext = new IssueCommentContext([], $webhooks_content);

        $issueCommentContext->installation_id = $installation_id;
        $issueCommentContext->rid = $repository->id;
        $issueCommentContext->repo_full_name = $repository->full_name;
        $issueCommentContext->sender_username = $sender_username;
        $issueCommentContext->sender_uid = $sender_uid;
        $issueCommentContext->sender_pic = $sender_pic;
        $issueCommentContext->issue_id = $issue_id;
        $issueCommentContext->issue_number = $issue_number;
        $issueCommentContext->comment_id = $comment_id;
        $issueCommentContext->body = $body;
        $issueCommentContext->created_at = $created_at;
        $issueCommentContext->updated_at = $updated_at;
        $issueCommentContext->owner = $repository->owner;
        $issueCommentContext->action = $action;
        $issueCommentContext->is_pull_request = $is_pull_request;

        return $issueCommentContext;
    }
}

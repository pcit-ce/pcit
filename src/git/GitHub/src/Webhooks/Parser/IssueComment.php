<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\IssueCommentContext;

class IssueComment
{
    private static $skip_list = [
        'CLAassistant',
    ];

    public static function handle(string $webhooks_content): IssueCommentContext
    {
        $issueCommentContext = new IssueCommentContext([], $webhooks_content);
        \Log::info('Receive issue comment event', []);

        $obj = json_decode($webhooks_content);

        $comment = $issueCommentContext->comment;

        $sender = $comment->user;
        $sender_username = $sender->login;

        if (strpos($sender_username, '[bot]') || \in_array($sender_username, self::$skip_list, true)) {
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

        $is_pull_request = ($issue->pull_request ?? false) ? true : false;

        // gitee
        if ($obj->pull_request ?? false) {
            $is_pull_request = true;
        }

        $repository = $issueCommentContext->repository;

        $issueCommentContext->sender_username = $sender_username;
        $issueCommentContext->sender_uid = $sender_uid;
        $issueCommentContext->sender_pic = $sender_pic;
        $issueCommentContext->issue_id = $issue_id;
        $issueCommentContext->issue_number = $issue_number;
        $issueCommentContext->comment_id = $comment_id;
        $issueCommentContext->body = $body;
        $issueCommentContext->created_at = $created_at;
        $issueCommentContext->updated_at = $updated_at;
        $issueCommentContext->is_pull_request = $is_pull_request;

        return $issueCommentContext;
    }
}

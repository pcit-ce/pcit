<?php

declare(strict_types=1);

namespace PCIT\Support\Webhooks\GitHub;

use PCIT\Support\Date;
use PCIT\Support\Log;
use PCIT\Support\Webhooks\GitHub\UserBasicInfo\Account;

class Issues
{
    private static $skip_list = [
        'CLAassistant',
    ];

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        $obj = json_decode($json_content);

        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['issue' => $action], Log::INFO);

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
        $locked = $issue->locked;
        $assignees = $issue->assignees;
        $labels = $issue->labels;
        $created_at = Date::parse($issue->created_at);
        $updated_at = Date::parse($issue->updated_at);
        $closed_at = Date::parse($issue->closed_at);

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'issue_id' => $issue_id,
            'issue_number' => $issue_number,
            'title' => $title,
            'body' => $body,
            'sender_uid' => $sender_uid,
            'sender_username' => $sender_username,
            'sender_pic' => $sender_pic,
            'state' => $state,
            'locked' => $locked,
            'assignees' => $assignees,
            'labels' => $labels,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'closed_at' => $closed_at,
            'account' => (new Account($repository_owner, $org)),
            'action' => $action,
        ];
    }

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function comment($json_content)
    {
        Log::debug(__FILE__, __LINE__, 'Receive issue comment event', [], Log::INFO);

        $obj = json_decode($json_content);

        $action = $obj->action;
        $comment = $obj->comment;

        $sender = $comment->user;
        $sender_username = $sender->login;

        if (strpos($sender_username, '[bot]') or \in_array($sender_username, self::$skip_list, true)) {
            Log::debug(__FILE__, __LINE__, 'Bot issue comment SKIP', [], Log::INFO);

            throw new \Exception('skip', 200);
        }

        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $issue = $obj->issue;
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

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'sender_username' => $sender_username,
            'sender_uid' => $sender_uid,
            'sender_pic' => $sender_pic,
            'issue_id' => $issue_id,
            'issue_number' => $issue_number,
            'comment_id' => $comment_id,
            'body' => $body,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'account' => (new Account($repository_owner, $org)),
            'action' => $action,
        ];
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\IssuesContext;

class Issues
{
    public static function handle(string $webhooks_content): IssuesContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'issue', 'action' => $action]);

        $issue = $obj->issue;

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

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

        $issuesContext = new IssuesContext([], $webhooks_content);

        $issuesContext->installation_id = $installation_id;
        $issuesContext->rid = $repository->id;
        $issuesContext->repo_full_name = $repository->full_name;
        $issuesContext->sender_username = $sender_username;
        $issuesContext->sender_uid = $sender_uid;
        $issuesContext->sender_pic = $sender_pic;
        $issuesContext->issue_id = $issue_id;
        $issuesContext->issue_number = $issue_number;
        $issuesContext->title = $title;
        $issuesContext->body = $body;
        $issuesContext->created_at = $created_at;
        $issuesContext->updated_at = $updated_at;
        $issuesContext->owner = $repository->owner;
        $issuesContext->action = $action;
        $issuesContext->state = $state;
        $issuesContext->labels = $labels;
        $issuesContext->assignees = $assignees;
        $issuesContext->locked = $locked;
        $issuesContext->closed_at = $closed_at;

        return $issuesContext;
    }
}

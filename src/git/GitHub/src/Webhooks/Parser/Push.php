<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\PushContext;
use PCIT\GPI\Webhooks\Context\TagContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Committer;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

class Push
{
    /**
     * @return PushContext|TagContext
     */
    public static function handle(string $webhooks_content): Context
    {
        \Log::info('Receive event', ['type' => 'push']);

        $obj = json_decode($webhooks_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $private = $repository->private;
        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $ref = $obj->ref;
        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            return self::tag($ref_array[2], $webhooks_content);
        }

        $branch = self::ref2branch($ref);
        $commit_id = $obj->after;
        $compare = $obj->compare;
        $head_commit = $obj->head_commit;

        if (null === $head_commit) {
            // 删除分支,也会产生一条 push 事件，此时 head commit 为 null
            throw new \Exception('skip', 200);
        }

        $commit_message = $head_commit->message;
        $commit_timestamp = Date::parse($head_commit->timestamp);

        $author = $head_commit->author;
        $committer = $head_commit->committer;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        $event_time = $commit_timestamp;
        $author = new Author($author);
        $committer = new Committer($committer);
        $account = new Account($repository_owner, $org);
        $sender = new Sender($obj->sender);

        $pushContext = new PushContext([], $webhooks_content);
        $pushContext->rid = $rid;
        $pushContext->repo_full_name = $repo_full_name;
        $pushContext->branch = $branch;
        $pushContext->commit_id = $commit_id;
        $pushContext->commit_message = $commit_message;
        $pushContext->compare = $compare;
        $pushContext->event_time = $event_time;
        $pushContext->author = $author;
        $pushContext->committer = $committer;
        $pushContext->installation_id = $installation_id;
        $pushContext->account = $account;
        $pushContext->sender = $sender;
        $pushContext->private = $private;

        return $pushContext;
    }

    public static function tag($tag, string $webhooks_content): TagContext
    {
        \Log::info('Receive event', ['type' => 'push', 'action' => 'tag']);

        $obj = json_decode($webhooks_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $private = $repository->private;

        $branch = self::ref2branch($obj->base_ref ?? 'refs/heads/master');

        $head_commit = $obj->head_commit;
        $commit_id = $head_commit->id;
        $commit_message = $head_commit->message;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $author = $head_commit->author;
        $committer = $head_commit->committer;

        $event_time = Date::parse($head_commit->timestamp);

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        $tagContext = new TagContext([], $webhooks_content);

        $tagContext->rid = $rid;
        $tagContext->repo_full_name = $repo_full_name;
        $tagContext->branch = $branch;
        $tagContext->tag = $tag;
        $tagContext->commit_id = $commit_id;
        $tagContext->commit_message = $commit_message;
        $tagContext->event_time = $event_time;
        $tagContext->author = new Author($author);
        $tagContext->committer = $committer;
        $tagContext->installation_id = $installation_id;
        $tagContext->account = new Account($repository_owner, $org);
        $tagContext->sender = new Sender($obj->sender);
        $tagContext->private = $private;

        return $tagContext;
    }

    /**
     * @return mixed
     */
    public static function ref2branch(string $ref)
    {
        $ref_array = explode('/', $ref);

        return $ref_array[2];
    }
}

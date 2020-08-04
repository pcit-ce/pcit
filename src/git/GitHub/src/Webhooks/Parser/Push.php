<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\HeadCommit;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;
use PCIT\GPI\Webhooks\Context\PushContext;
use PCIT\GPI\Webhooks\Context\TagContext;
use PCIT\GPI\Webhooks\Parser\Abstracts\PushAbstract;

class Push extends PushAbstract
{
    /**
     * @return PushContext|TagContext
     */
    public static function handle(string $webhooks_content): Context
    {
        \Log::info('Receive event', ['type' => 'push']);

        $obj = json_decode($webhooks_content);

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $ref = $obj->ref;
        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            return self::tag($ref_array[2], $webhooks_content);
        }

        $branch = self::ref2branch($ref);

        $compare = $obj->compare;
        $head_commit = new HeadCommit($obj->head_commit);

        if (null === $obj->head_commit) {
            // 删除分支,也会产生一条 push 事件，此时 head commit 为 null
            throw new \Exception('branch delete event, skip', 200);
        }

        $head_commit->timestamp_int = Date::parse($head_commit->timestamp);
        $head_commit->author = new Author($obj->head_commit->author);
        $head_commit->committer = new Committer($obj->head_commit->committer);

        $installation_id = $obj->installation->id ?? null;

        $pushContext = new PushContext([], $webhooks_content);

        $pushContext->repository = $repository;
        $pushContext->rid = $repository->id;
        $pushContext->repo_full_name = $repository->full_name;
        $pushContext->branch = $branch;
        $pushContext->head_commit = $head_commit;
        $pushContext->commit_id = $head_commit->id;
        $pushContext->commit_message = $head_commit->message;
        $pushContext->compare = $compare;
        $pushContext->event_time = $head_commit->timestamp_int;
        $pushContext->author = $head_commit->author;
        $pushContext->committer = $head_commit->committer;
        $pushContext->installation_id = $installation_id;
        $pushContext->owner = $repository->owner;
        $pushContext->sender = new Sender($obj->sender);
        $pushContext->private = $repository->private;

        return $pushContext;
    }

    public static function tag($tag, string $webhooks_content): TagContext
    {
        \Log::info('Receive event', ['type' => 'push', 'action' => 'tag']);

        $obj = json_decode($webhooks_content);

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $branch = self::ref2branch($obj->base_ref ?? 'refs/heads/master');

        $head_commit = new HeadCommit($obj->head_commit);
        if (!$obj->head_commit) {
            $context = new TagContext([], $webhooks_content);

            $context->commit_id = $obj->after;
            $context->tag = $tag;

            return $context;
        }

        $head_commit->timestamp_int = Date::parse($head_commit->timestamp);
        $head_commit->author = new Author($obj->head_commit->author);
        $head_commit->committer = new Committer($obj->head_commit->committer);

        $installation_id = $obj->installation->id ?? null;

        $tagContext = new TagContext([], $webhooks_content);

        $tagContext->rid = $repository->id;
        $tagContext->repo_full_name = $repository->full_name;
        $tagContext->branch = $branch;
        $tagContext->tag = $tag;
        $tagContext->head_commit = $head_commit;
        $tagContext->commit_id = $head_commit->id;
        $tagContext->commit_message = $head_commit->message;
        $tagContext->event_time = $head_commit->timestamp_int;
        $tagContext->author = $head_commit->author;
        $tagContext->committer = $head_commit->committer;
        $tagContext->installation_id = $installation_id;
        $tagContext->owner = $repository->owner;
        $tagContext->sender = new Sender($obj->sender);
        $tagContext->private = $repository->private;

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

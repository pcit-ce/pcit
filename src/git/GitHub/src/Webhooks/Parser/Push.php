<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context;
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
        $pushContext = new PushContext([], $webhooks_content);
        \Log::info('Receive event', ['type' => 'push']);

        $obj = json_decode($webhooks_content);

        $ref = $pushContext->ref;
        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            return self::tag($ref_array[2], $webhooks_content);
        }

        $branch = self::ref2branch($ref);

        $head_commit = $pushContext->head_commit;

        if (null === $obj->head_commit) {
            // 删除分支,也会产生一条 push 事件，此时 head commit 为 null
            throw new \Exception('branch delete event, skip', 200);
        }

        $repository = $pushContext->repository;

        $pushContext->rid = $repository->id;
        $pushContext->repo_full_name = $repository->full_name;
        $pushContext->branch = $branch;
        $pushContext->commit_id = $head_commit->id;
        $pushContext->commit_message = $head_commit->message;
        $pushContext->event_time = $head_commit->timestamp_int;
        $pushContext->author = $head_commit->author;
        $pushContext->committer = $head_commit->committer;
        $pushContext->private = $repository->private;

        return $pushContext;
    }

    public static function tag(string $tag, string $webhooks_content): TagContext
    {
        \Log::info('Receive event', ['type' => 'push', 'action' => 'tag']);

        $obj = json_decode($webhooks_content);

        $branch = self::ref2branch($obj->base_ref ?? 'refs/heads/master');

        if (!$obj->head_commit) {
            $context = new TagContext([], $webhooks_content);

            $context->commit_id = $obj->after;
            $context->tag = $tag;

            return $context;
        }

        $tagContext = new TagContext([], $webhooks_content);
        $repository = $tagContext->repository;
        $head_commit = $tagContext->head_commit;

        $tagContext->rid = $repository->id;
        $tagContext->repo_full_name = $repository->full_name;
        $tagContext->branch = $branch;
        $tagContext->tag = $tag;
        $tagContext->commit_id = $head_commit->id;
        $tagContext->commit_message = $head_commit->message;
        $tagContext->event_time = $head_commit->timestamp_int;
        $tagContext->author = $head_commit->author;
        $tagContext->committer = $head_commit->committer;
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

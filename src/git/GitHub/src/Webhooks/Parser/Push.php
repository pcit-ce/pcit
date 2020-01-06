<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Committer;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Sender;

class Push
{
    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        \Log::info('Receive event', ['push']);

        $obj = json_decode($json_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $ref = $obj->ref;
        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            return self::tag($ref_array[2], $json_content);
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
        $author = (new Author($author));
        $committer = (new Committer($committer));
        $account = (new Account($repository_owner, $org));
        $sender = (new Sender($obj->sender));

        return compact('rid', 'repo_full_name', 'branch', 'commit_id', 'commit_message',
            'compare', 'event_time', 'author', 'committer',
            'installation_id', 'account', 'sender'
        );
    }

    /**
     * @param $tag
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function tag($tag, $json_content)
    {
        \Log::info('Receive event', ['push' => 'tag']);

        $obj = json_decode($json_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

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

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'tag' => $tag,
            'commit_id' => $commit_id,
            'commit_message' => $commit_message,
            'event_time' => $event_time,
            'author' => (new Author($author)),
            'committer' => (new Committer($committer)),
            'account' => (new Account($repository_owner, $org)),
            'sender' => (new Sender($obj->sender)),
        ];
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

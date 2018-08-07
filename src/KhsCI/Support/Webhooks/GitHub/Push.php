<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

use KhsCI\Support\Date;
use KhsCI\Support\Log;

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
        Log::debug(__FILE__, __LINE__, 'Receive event', ['push'], Log::INFO);

        $obj = json_decode($json_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $username = $repository->owner->login;

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
            throw new \Exception('skip', 200);
        }

        $commit_message = $head_commit->message;
        $commit_timestamp = Date::parse($head_commit->timestamp);
        $author = $head_commit->author;
        $author_name = $author->name;
        $author_email = $author->email;
        $author_username = $author->username;

        $committer = $head_commit->committer;
        $committer_name = $committer->name;
        $committer_email = $committer->email;
        $committer_username = $committer->username;

        $installation_id = $obj->installation->id ?? null;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'commit_id' => $commit_id,
            'commit_message' => $commit_message,
            'compare' => $compare,
            'event_time' => $commit_timestamp,
            'author_name' => $author_name,
            'author_email' => $author_email,
            'author_username' => $author_username,
            'committer_name' => $committer_name,
            'committer_email' => $committer_email,
            'committer_username' => $committer_username,
            'username' => $username,
        ];
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
        Log::debug(__FILE__, __LINE__, 'Receive event', ['push' => 'tag'], Log::INFO);

        $obj = json_decode($json_content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $username = $repository->owner->login;

        $branch = self::ref2branch($obj->base_ref);

        $head_commit = $obj->head_commit;
        $commit_id = $head_commit->id;
        $commit_message = $head_commit->message;

        $author = $head_commit->author;
        $author_name = $author->name;
        $author_email = $author->email;
        $author_username = $author->username;

        $committer = $head_commit->committer;
        $committer_username = $committer->username;
        $committer_name = $committer->name;
        $committer_email = $committer->email;

        $event_time = Date::parse($head_commit->timestamp);

        $installation_id = $obj->installation->id ?? null;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'tag' => $tag,
            'commit_id' => $commit_id,
            'commit_message' => $commit_message,
            'event_time' => $event_time,
            'author_name' => $author_name,
            'author_email' => $author_email,
            'author_username' => $author_username,
            'committer_name' => $committer_name,
            'committer_email' => $committer_email,
            'committer_username' => $committer_username,
            'username' => $username,
        ];
    }

    /**
     * @param string $ref
     *
     * @return mixed
     */
    public static function ref2branch(string $ref)
    {
        $ref_array = explode('/', $ref);

        return $ref_array[2];
    }
}

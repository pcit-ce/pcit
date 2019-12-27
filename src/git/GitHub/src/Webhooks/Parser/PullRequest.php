<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\Framework\Support\Log;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;

class PullRequest
{
    /**
     * "assigned", "unassigned",
     * "review_requested", "review_request_removed",
     * "labeled", "unlabeled",
     * "opened", "synchronize", "edited", "closed", or "reopened".
     *
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

        Log::debug(null, null, 'Receive event', ['pull request' => $action], Log::INFO);

        if (!\in_array($action, ['opened', 'synchronize'], true)) {
            'assigned' === $action && $result = self::assigned($json_content);
            'labeled' === $action && $result = self::labeled($json_content);
            'unlabeled' === $action && $result = self::labeled($json_content, true);

            if (($result ?? null)) {
                return $result;
            }

            throw new \Exception('skip');
        }

        $pull_request = $obj->pull_request;
        $event_time = $pull_request->updated_at ?? $pull_request->created_at;
        $event_time = Date::parse($event_time);

        // head 向 base 提交 PR
        $pull_request_base = $pull_request->base;
        $pull_request_head = $pull_request->head;

        $rid = $pull_request_base->repo->id;
        $repo_full_name = $pull_request_base->repo->full_name;

        $commit_message = $pull_request->title;
        $commit_id = $pull_request_head->sha;

        $committer_username = $pull_request->user->login;
        $committer_uid = $pull_request->user->id;

        $pull_request_number = $obj->number;
        $branch = $pull_request->base->ref;
        $installation_id = $obj->installation->id ?? null;

        $repository = $obj->repository;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        // 检查内外部 PR

        $internal = ($pull_request_head->repo->id !== $pull_request_base->repo->id) ? 0 : 1;

        $pull_request_source = $pull_request_head->repo->full_name;

        $org = ($obj->organization ?? false) ? true : false;

        // 谁开启 PR
        // 谁推送的 commit 多个
        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'event_time' => $event_time,
            'commit_message' => $commit_message,
            'commit_id' => $commit_id,
            'committer_username' => $committer_username,
            'committer_uid' => $committer_uid,
            'pull_request_number' => $pull_request_number,
            'branch' => $branch,
            'internal' => $internal,
            'pull_request_source' => $pull_request_source,
            'account' => (new Account($repository_owner, $org)),
        ];
    }

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function labeled($json_content, bool $unlabeled = false)
    {
        Log::debug(__FILE__, __LINE__, 'Receive pull request labeled event', [], Log::INFO);

        $obj = json_decode($json_content);

        $label = $obj->label;

        $label_name = $label->name;

        if ('merge' !== $label_name) {
            return [];
        }

        $pull_request = $obj->pull_request;

        $pull_request_base = $pull_request->base;

        $rid = $pull_request_base->repo->id;
        $repo_full_name = $pull_request_base->repo->full_name;

        $pull_request_number = $obj->number;

        $repository = $obj->repository;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        if ($unlabeled) {
            Log::debug(null, null, 'Receive event', ['pull_request' => 'unlabeled'], Log::INFO);

            return [
                'installation_id' => $installation_id,
                'rid' => $rid,
                'repo_full_name' => $repo_full_name,
                'pull_request_number' => $pull_request_number,
                'label_name' => $label_name,
                'action' => 'unlabeled',
            ];
        }

        Log::debug(null, null, 'Receive event', ['pull_request' => 'labeled'], Log::INFO);

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'pull_request_number' => $pull_request_number,
            'action' => 'labeled',
            'label_name' => $label_name,
            'account' => (new Account($repository_owner, $org)),
        ];
    }

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function assigned($json_content)
    {
        Log::debug(null, null, 'Receive event', ['pull request' => 'assigned'], Log::INFO);

        $obj = json_decode($json_content);

        $pull_request = $obj->pull_request;

        $pull_request_base = $pull_request->base;

        $rid = $pull_request_base->repo->id;
        $repo_full_name = $pull_request_base->repo->full_name;

        $repository = $obj->repository;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $pull_request_number = $obj->number;

        $installation_id = $obj->installation->id ?? null;

        $org = ($obj->organization ?? false) ? true : false;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'pull_request_number' => $pull_request_number,
            'action' => 'assigned',
            'account' => (new Account($repository_owner, $org)),
        ];
    }
}

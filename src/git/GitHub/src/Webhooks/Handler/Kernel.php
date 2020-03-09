<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

class Kernel
{
    /**
     * @throws \Exception
     */
    public function ping(string $content): void
    {
        Ping::handle($content);
    }

    /**
     * push.
     *
     * 1. 首次推送到新分支，head_commit 为空
     *
     * @throws \Exception
     */
    public function push(string $json_content): void
    {
        Push::handle($json_content);
    }

    public function status(string $content)
    {
        return 200;
    }

    /**
     *  "assigned", "unassigned",
     *  "labeled",  "unlabeled",
     *  "opened",   "edited", "closed" or "reopened"
     *  "milestoned", "demilestoned".
     *
     * @throws \Exception
     */
    public function issues(string $json_content): void
    {
        Issues::handle($json_content);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @throws \Exception
     */
    public function issue_comment(string $json_content): void
    {
        Issues::comment($json_content);
    }

    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @return array|void
     *
     * @throws \Exception
     */
    public function pull_request(string $json_content)
    {
        PullRequest::handle($json_content);
    }

    /**
     * Do Nothing.
     */
    public function watch()
    {
        return 200;
    }

    /**
     * Do Nothing.
     */
    public function fork()
    {
        return 200;
    }

    /**
     * Do nothing.
     */
    public function release()
    {
        return 200;
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @return int
     *
     * @throws \Exception
     */
    public function create(string $content)
    {
        return 200;
    }

    /**
     * Delete tag or branch.
     *
     * @throws \Exception
     */
    public function delete(string $json_content): void
    {
        Delete::handle($json_content);
    }

    /**
     * action `added` `deleted` `edited` `removed`.
     *
     * @throws \Exception
     */
    public function member(string $content): void
    {
    }

    public function team_add(string $content): void
    {
        $obj = json_decode($content);

        $repository = $obj->repository;

        $rid = $repository->id;
        $username = $repository->owner->name;

        $installation_id = $obj->installation->id ?? null;
    }

    /**
     * Any time a GitHub App is installed or uninstalled.
     *
     * action:
     *
     * created 用户点击安装按钮
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @see
     *
     * @throws \Exception
     */
    public function installation(string $json_content): void
    {
        Installation::handle($json_content);
    }

    /**
     * Any time a repository is added or removed from an installation.
     *
     * action:
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     *
     * @throws \Exception
     */
    public function installation_repositories(string $json_content): void
    {
        Installation::repositories($json_content);
    }

    /**
     * @deprecated
     */
    public function integration_installation(): void
    {
    }

    /**
     * @deprecated
     */
    public function integration_installation_repositories(): void
    {
    }

    /**
     * Action.
     *
     * completed
     *
     * requested 用户推送分支，github post webhooks
     *
     * rerequested 用户点击了重新运行按钮
     *
     * @see https://developer.github.com/v3/activity/events/types/#checksuiteevent
     *
     * @throws \Exception
     */
    public function check_suite(string $json_content): void
    {
        Check::suite($json_content);
    }

    /**
     * Action.
     *
     * created updated rerequested
     *
     * @see https://developer.github.com/v3/activity/events/types/#checkrunevent
     *
     * @throws \Exception
     */
    public function check_run(string $json_content): void
    {
        Check::run($json_content);
    }

    public function content_reference(string $json_content): void
    {
        Content::handle($json_content);
    }

    /**
     * @see https://developer.github.com/v3/activity/events/types/#repositoryevent
     */
    public function repository(string $json_content): void
    {
        Repository::handle($json_content);
    }

    public function __call($name, $args): void
    {
        throw new \Exception("$name event handler not implements");
    }
}

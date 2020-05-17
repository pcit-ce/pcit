<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\Support\Git;

class Kernel
{
    public function getNamespace(string $git_type): string
    {
        Git::getClassName($git_type);

        return 'PCIT\\'.Git::getClassName($git_type).'\\Webhooks\Handler\\';
    }

    /**
     * @throws \Exception
     */
    public function ping(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Ping';

        (new $class())->handle($webhooks_content);
    }

    /**
     * push.
     *
     * 1. 首次推送到新分支，head_commit 为空
     *
     * @throws \Exception
     */
    public function push(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Push';
        (new $class())->handle($webhooks_content);
    }

    public function status(string $webhooks_content, string $git_type)
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
    public function issues(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Issues';
        (new $class())->handle($webhooks_content);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @throws \Exception
     */
    public function issue_comment(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Issues';
        (new $class())->comment($webhooks_content);
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
    public function pull_request(string $webhooks_content, string $git_type)
    {
        $class = $this->getNamespace($git_type).'PullRequest';
        (new $class())->handle($webhooks_content);
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
    public function create(string $webhooks_content, string $git_type)
    {
        return 200;
    }

    /**
     * Delete tag or branch.
     *
     * @throws \Exception
     */
    public function delete(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Delete';
        (new $class())->handle($webhooks_content);
    }

    /**
     * action `added` `deleted` `edited` `removed`.
     *
     * @throws \Exception
     */
    public function member(string $webhooks_content, string $git_type): void
    {
    }

    public function team_add(string $webhooks_content, string $git_type): void
    {
        $obj = json_decode($webhooks_content);

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
    public function installation(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Installation';
        (new $class())->handle($webhooks_content);
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
    public function installation_repositories(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Installation';
        (new $class())->repositories($webhooks_content);
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
    public function check_suite(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Check';
        (new $class())->suite($webhooks_content);
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
    public function check_run(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Check';
        (new $class())->run($webhooks_content);
    }

    public function content_reference(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Content';
        (new $class())->handle($webhooks_content);
    }

    /**
     * @see https://developer.github.com/v3/activity/events/types/#repositoryevent
     */
    public function repository(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Repository';
        (new $class())->handle($webhooks_content);
    }

    public function __call($name, $args): void
    {
        throw new \Exception("$name event handler not implements");
    }
}

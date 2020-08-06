<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\Support\Git;

/**
 * @see https://developer.github.com/webhooks/event-payloads/
 */
class Kernel
{
    public function getNamespace(?string $git_type): string
    {
        if (!$git_type) {
            return '';
        }

        Git::getClassName($git_type);

        return 'PCIT\\'.Git::getClassName($git_type).'\\Webhooks\Handler\\';
    }

    /**
     * Action.
     *
     * created updated rerequested
     *
     * @throws \Exception
     */
    public function check_run(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'CheckRun';
        (new $class())->handle($webhooks_content);
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
     * @throws \Exception
     */
    public function check_suite(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'CheckSuite';
        (new $class())->handle($webhooks_content);
    }

    public function commit_comment(string $webhooks_content, string $git_type): void
    {
    }

    public function content_reference(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Content';
        (new $class())->handle($webhooks_content);
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @throws \Exception
     *
     * @return int
     */
    public function create(string $webhooks_content, string $git_type)
    {
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

    public function deploy_key(string $webhooks_content, string $git_type): void
    {
    }

    public function deployment(string $webhooks_content, string $git_type): void
    {
    }

    public function deployment_status(string $webhooks_content, string $git_type): void
    {
    }

    public function fork(string $webhooks_content, string $git_type): void
    {
    }

    public function github_app_authorization(string $webhooks_content, string $git_type): void
    {
    }

    public function gollum(string $webhooks_content, string $git_type): void
    {
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
        $class = $this->getNamespace($git_type).'InstallationRepositories';
        (new $class())->handle($webhooks_content);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @throws \Exception
     */
    public function issue_comment(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'IssueComment';
        (new $class())->handle($webhooks_content);
    }

    /**
     * @throws \Exception
     */
    public function issues(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Issues';
        (new $class())->handle($webhooks_content);
    }

    public function label(string $webhooks_content, string $git_type): void
    {
    }

    public function marketplace_purchase(string $webhooks_content, string $git_type): void
    {
    }

    /**
     * action `added` `deleted` `edited` `removed`.
     *
     * @throws \Exception
     */
    public function member(string $webhooks_content, string $git_type): void
    {
    }

    public function membership(string $webhooks_content, string $git_type): void
    {
    }

    public function meta(string $webhooks_content, string $git_type): void
    {
    }

    public function milestone(string $webhooks_content, string $git_type): void
    {
    }

    public function organization(string $webhooks_content, string $git_type): void
    {
    }

    public function org_block(string $webhooks_content, string $git_type): void
    {
    }

    public function package(string $webhooks_content, string $git_type): void
    {
    }

    public function page_build(string $webhooks_content, string $git_type): void
    {
    }

    public function ping(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Ping';

        (new $class())->handle($webhooks_content);
    }

    public function project_card(string $webhooks_content, string $git_type): void
    {
    }

    public function project_column(string $webhooks_content, string $git_type): void
    {
    }

    public function project(string $webhooks_content, string $git_type): void
    {
    }

    public function public(string $webhooks_content, string $git_type): void
    {
    }

    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @throws \Exception
     *
     * @return array|void
     */
    public function pull_request(string $webhooks_content, string $git_type)
    {
        $class = $this->getNamespace($git_type).'PullRequest';
        (new $class())->handle($webhooks_content);
    }

    public function pull_request_review(string $webhooks_content, string $git_type): void
    {
    }

    public function pull_request_review_comment(string $webhooks_content, string $git_type): void
    {
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

    public function release(string $webhooks_content, string $git_type): void
    {
    }

    public function repository_dispatch(string $webhooks_content, string $git_type): void
    {
    }

    public function repository(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Repository';
        (new $class())->handle($webhooks_content);
    }

    public function repository_import(string $webhooks_content, string $git_type): void
    {
    }

    public function repository_vulnerability_alert(string $webhooks_content, string $git_type): void
    {
    }

    public function security_advisory(string $webhooks_content, string $git_type): void
    {
    }

    public function sponsorship(string $webhooks_content, string $git_type): void
    {
    }

    public function star(string $webhooks_content, string $git_type): void
    {
    }

    public function status(string $webhooks_content, string $git_type): void
    {
    }

    public function team(string $webhooks_content, string $git_type): void
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

    public function watch(string $webhooks_content, string $git_type): void
    {
    }

    public function __call($name, $args): void
    {
        $ns = $this->getNamespace($args[1] ?? null);
        $class = $ns.'Kernel';

        if (class_exists($class)) {
            $gpi_provider_webhooks_handler = new $class();

            if (method_exists($gpi_provider_webhooks_handler, $name)) {
                $gpi_provider_webhooks_handler->$name(...$args);

                return;
            }
        }

        throw new \Exception(($args[1] ?? null)." $name event handler not implements");
    }
}

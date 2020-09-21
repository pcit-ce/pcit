<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\GPI\Support\Git;

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

    public function check_run(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'CheckRun';
        $this->callHandler($class, $webhooks_content);
    }

    public function check_suite(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'CheckSuite';
        $this->callHandler($class, $webhooks_content);
    }

    public function commit_comment(string $webhooks_content, string $git_type): void
    {
    }

    public function content_reference(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'ContentReference';
        $this->callHandler($class, $webhooks_content);
    }

    public function create(string $webhooks_content, string $git_type): void
    {
    }

    public function delete(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Delete';
        $this->callHandler($class, $webhooks_content);
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

    public function installation(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Installation';
        $this->callHandler($class, $webhooks_content);
    }

    public function installation_repositories(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'InstallationRepositories';
        $this->callHandler($class, $webhooks_content);
    }

    public function issue_comment(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'IssueComment';

        $this->callHandler($class, $webhooks_content);
    }

    public function issues(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Issues';
        $this->callHandler($class, $webhooks_content);
    }

    public function label(string $webhooks_content, string $git_type): void
    {
    }

    public function marketplace_purchase(string $webhooks_content, string $git_type): void
    {
    }

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

        $this->callHandler($class, $webhooks_content);
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

    public function pull_request(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'PullRequest';
        $this->callHandler($class, $webhooks_content);
    }

    public function pull_request_review(string $webhooks_content, string $git_type): void
    {
    }

    public function pull_request_review_comment(string $webhooks_content, string $git_type): void
    {
    }

    public function push(string $webhooks_content, string $git_type): void
    {
        $class = $this->getNamespace($git_type).'Push';
        $this->callHandler($class, $webhooks_content);
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
        $this->callHandler($class, $webhooks_content);
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
    }

    public function watch(string $webhooks_content, string $git_type): void
    {
    }

    public function callHandler(string $class_name, string $webhooks_content): void
    {
        \call_user_func([new $class_name(), 'handle'], $webhooks_content);
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

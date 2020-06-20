<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Issue;

use App\GetAccessToken;
use App\Repo;
use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\PCIT;
use PCIT\Pustomize\Interfaces\Issue\CommentInterface;

class Comment implements CommentInterface
{
    /** @var IssueCommentContext */
    public $context;

    /** @var \PCIT\PCIT */
    public $pcit;

    public function handle(IssueCommentContext $context): void
    {
        $this->context = $context;

        $body = strtolower($context->body);

        if (!\in_array($context->action, ['created', 'comment'])) {
            return;
        }

        $this->pcit = new PCIT([], $context->git_type, GetAccessToken::byRid(
                $context->rid,
                $context->git_type
            ));

        if ('/translate-title' === $body) {
            $this->handleTitleTranslate();

            return;
        }

        if (!$this->context->is_pull_request) {
            \Log::info('this comment is not in pull request, skip');

            return;
        }

        $this->handleAutoMerge($body);
    }

    public function handleTitleTranslate(): void
    {
        $this->pcit->issue->translateTitle(
            $this->context->repo_full_name,
            (int) $this->context->issue_number,
            null,
            null
        );
    }

    public function handleAutoMerge(string $body): void
    {
        if (Repo::checkAdmin((int) $this->context->rid, (int) $this->context->sender_uid)) {
            [$body, $merge_method] = $this->handleAutoMergeBodyAndMethod($body);
            try {
                $this->merge($merge_method ?? 1);

                $this->createComment($body);
            } catch (\Throwable $e) {
                \Log::error('auto merge error: '.$e->__toString());

                $this->createComment(
                    ':disappointed_relieved: merge cannot be performed, please check status below'
                );
            }
        }
    }

    public function createComment(string $body): void
    {
        $this->pcit->issue_comments->create(
            $this->context->repo_full_name,
            (int) $this->context->issue_number,
            $body
        );
    }

    public function handleAutoMergeBodyAndMethod(string $body): array
    {
        $body = strtoupper($body);

        if (strtoupper('/LGTM') === $body) {
            $body = <<<EOF
:tada: :robot: I merge this pull_request success.
EOF;
            $merge_method = 1;

            return [$body, $merge_method];
        }

        if (strtoupper('/LGTM squash') === $body) {
            $body = <<<EOF
:tada: :robot: I squash this pull_request success.
EOF;
            $merge_method = 2;

            return [$body, $merge_method];
        }

        if (strtoupper('/LGTM rebase') === $body) {
            $body = <<<EOF
:tada: :robot: I rebase this pull_request success.
EOF;
            $merge_method = 3;

            return [$body, $merge_method];
        }

        return [];
    }

    /**
     * @param int $method 1: merge 2:squash 3:rebase
     */
    public function merge($method): void
    {
        \Log::info('merge pull_request by pcit auto');

        $repo_array = explode('/', $this->context->repo_full_name);

        $this->pcit->pull_request
            ->merge(
                $repo_array[0],
                $repo_array[1],
                (int) $this->context->issue_number,
                null,
                null,
                null,
                (int) $method
            );
        \Log::info('auto merge success, method is '.$method);
    }
}

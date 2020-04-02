<?php

declare(strict_types=1);

namespace PCIT\Pustomize\PullRequest;

use PCIT\Pustomize\Interfaces\PullRequest\AutoMergeInterface;

class AutoMerge implements AutoMergeInterface
{
    public function handle(string $body): array
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
}

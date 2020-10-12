<?php

declare(strict_types=1);

namespace PCIT\Runner\Events\Handler;

/**
 * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-syntax-for-github-actions#using-a-specific-shell
 */
class ShellHandler
{
    public function handle(string $shell = 'sh', ?array $commands = [], ?int $timeout = null): array
    {
        $timeout = $timeout ?? env('CI_STEP_TIMEOUT', 21600);

        $cmd = null;

        if ('bash' === $shell) {
            $shell = 'bash --noprofile --norc -eo pipefail';
        }

        if ('sh' === $shell) {
            $shell = 'sh -e';
        }

        if ('python' === $shell) {
        }

        if ('pwsh' === $shell) {
            $shell = 'pwsh -Command -';
        }

        if ('node' === $shell) {
            $shell = 'node -';
        }

        if ('deno' === $shell) {
            $shell = 'deno run -';
        }

        if ($shell) {
            $cmd = $commands ?
            ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' '.$shell] : null;
        }

        $entrypoint = $cmd ? ['/bin/sh', '-ec'] : null;

        return [$entrypoint, $cmd];
    }
}

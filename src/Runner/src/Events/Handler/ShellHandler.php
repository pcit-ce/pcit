<?php

declare(strict_types=1);

namespace PCIT\Runner\Events\Handler;

class ShellHandler
{
    public function handle(string $shell = 'sh', ?array $commands = []): array
    {
        $timeout = env('CI_STEP_TIMEOUT', 21600);

        $cmd = null;

        if ('bash' === $shell || 'sh' === $shell) {
            $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' '.$shell.' -e'] : null;
        }

        if ('python' === $shell) {
            $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' python'] : null;
        }

        if ('pwsh' === $shell) {
            $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' pwsh -Command -'] : null;
        }

        if ('node' === $shell) {
            $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' node -'] : null;
        }

        if ('deno' === $shell) {
            $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' deno'] : null;
        }

        // 有 commands 指令则改为 ['/bin/sh', '-c'], 否则为默认值
        // shell 在以上范围, entrypoint 为 [...], 否则为 null
        $entrypoint = $cmd ? ['/bin/sh', '-c'] : null;

        // shell 不在以上范围或未指定 run 指令，entrypoint cmd 均设为 null，使用镜像的默认值

        return [$entrypoint, $cmd];
    }
}

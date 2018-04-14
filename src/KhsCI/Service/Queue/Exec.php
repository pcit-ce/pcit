<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

class Exec
{
    public function exec($repo, $branch, $path): void
    {
        chdir($path);
        // remote origin not exists

        popen(sprintf('git remote get-url origin || git remote set-url %s', $repo), 'r');

        // remote origin exists

        popen(sprintf('git remote get-url origin && git remote set-url %s', $repo), 'r');

        popen(sprintf('git fetch origin %s ; git reset --hard origin/%s', $branch, $branch), 'r');
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Service\Repositories;

interface ContentsClientInterface
{
    public function getContents(string $repo_full_name, string $path, string $ref, bool $raw = true): string;
}

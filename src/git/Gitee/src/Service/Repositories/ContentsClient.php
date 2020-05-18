<?php

declare(strict_types=1);

namespace PCIT\Gitee\Service\Repositories;

use PCIT\GitHub\Service\Repositories\ContentsClient as GitHubContentsClient;

class ContentsClient extends GitHubContentsClient
{
    public function getContents(string $repo_full_name, string $path, string $ref, bool $raw = true): string
    {
        $result = parent::getContents($repo_full_name, $path, $ref, false);

        if (!$raw) {
            return $result;
        }

        $content = json_decode($result)->content;

        return base64_decode($content);
    }
}

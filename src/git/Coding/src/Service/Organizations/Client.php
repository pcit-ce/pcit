<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Organizations;

use PCIT\GitHub\Service\Organizations\Client as GitHubClient;

class Client extends GitHubClient
{
    public function listRepo(string $org_name, int $page = 1, ?string $type = null, ?int $per_page = null, ?string $sort = null)
    {
        return '[]';
    }
}

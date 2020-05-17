<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Organizations;

use PCIT\Coding\ServiceClientCommon;
use PCIT\GitHub\Service\Organizations\Client as GitHubClient;

/**
 * 这里的组织指 项目.
 */
class Client extends GitHubClient
{
    use ServiceClientCommon;

    public function listRepo(string $org_name, int $page = 1, ?string $type = null, ?int $per_page = null, ?string $sort = null)
    {
        $team = $this->getTeamName();

        $url = $this->api_url."/user/$team/project/$org_name/repos";

        $result = $this->curl->get($url.'?'.$this->getAccessTokenUrlParameter());

        $repos = json_decode($result)->data->depots;

        $repos_array = [];

        foreach ($repos as $repo) {
            $full_name = "$org_name/".$repo->name;

            $repos_array[] = [
                'full_name' => $full_name,
                'default_branch' => 'master',
                'permissions' => [
                    'admin' => true,
                ],
                'id' => $repo->id,
            ];
        }

        return json_encode($repos_array);
    }
}

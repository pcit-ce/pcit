<?php

declare(strict_types=1);

namespace PCIT\cODING\Service\Repositories;

use PCIT\Coding\ServiceClientCommon;
use PCIT\GPI\Service\Repositories\ContentsClientInterface;

class ContentsClient implements ContentsClientInterface
{
    use ServiceClientCommon;

    /**
     * @see https://help.coding.net/docs/project/open/oauth.html#%E8%AF%BB%E5%8F%96%E4%BB%A3%E7%A0%81%E4%BB%93%E5%BA%93%E4%B8%AD%E6%9F%90%E4%B8%AA%E6%96%87%E4%BB%B6
     */
    public function getContents(string $repo_full_name, string $path, string $ref, bool $raw = true): string
    {
        $pcit_team_name = $this->getTeamName();

        [$project_name,$depot_name] = explode('/', $repo_full_name);

        if ($raw) {
            $result = $this->curl->get("https://${pcit_team_name}.coding.net/p/$project_name/d/$depot_name/git/raw/".$ref.'/'.$path.'?'.$this->getAccessTokenUrlParameter());
            $this->successOrFailure(200, true);

            return $result;
        }

        $result = $this->curl->get($this->api_url."/user/$pcit_team_name/project/$project_name/depot/$depot_name/git/blob/$ref/$path".'?'.$this->getAccessTokenUrlParameter());

        return $result;
    }
}

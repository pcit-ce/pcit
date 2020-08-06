<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Repositories;

use Exception;
use PCIT\Coding\ServiceClientCommon;
use PCIT\GPI\Service\Repositories\WebhooksClientInterface;

class WebhooksClient implements WebhooksClientInterface
{
    use ServiceClientCommon;

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function getWebhooks(bool $raw, string $username, string $project)
    {
        $url = $this->api_url.'/user/'.$this->getTeamName().'/project/'.$username.'/git/v2/hooks';

        $json = $this->curl->get($url);

        if (true === $raw) {
            return $json;
        }

        $obj = json_decode($json);

        $code = $obj->code;

        if (0 === $code) {
            return json_encode($obj->data);
        }

        // data->hook_url

        throw new Exception('Project Not Found', 404);
    }

    /**
     * @param $data
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setWebhooks($data, string $username, string $repo, ?string $id = null)
    {
        $team = $this->getTeamName();
        $data = json_decode($data);
        $hook_url = $data->hook_url;
        $token = $data->token;
        $rid = $data->rid;

        $events = 'events=push&events=mr_pr&events=agile&events=ci&events=document&events=member&events=artifact';
        $depopt = "depots[0].depot_id=$rid&depots[0].push=true&depots[0].mr_pr=true";

        $query = http_build_query([
            'hook_url' => $hook_url,
            'type' => 'json',
            'schema' => 'coding',
            'active' => true,
            'token' => $token,
            'access_token' => $this->getAccessTokenUrlParameter(true),
        ]);
        $query = $events.'&'.$depopt.'&'.$query;

        $url = $this->api_url."/user/$team/project/$username/git/v2/hook";
        $url = $url.'?'.$query;

        return $this->curl->post($url);
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function unsetWebhooks(string $username, string $repo, string $id)
    {
        $url = sprintf(
            '/user/%s/project/%s/git/v2/hook/%s',
            $this->getTeamName(),
            $username,
            $id
        );
        $url = $this->api_url.$url.'?'.$this->getAccessTokenUrlParameter();

        return $this->curl->delete($url);
    }

    /**
     * TODO: 获取 webhooks 设置状态
     */
    public function getStatus(string $url, string $username, string $repo_name)
    {
        return 0;
    }
}

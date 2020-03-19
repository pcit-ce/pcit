<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use Curl\Curl;
use Exception;

class WebhooksClient
{
    private $api_url;

    /**
     * @var Curl
     */
    private $curl;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getWebhooks(bool $raw, string $username, string $repo)
    {
        $url = $this->api_url.'/repos/'.$username.'/'.$repo.'/hooks';

        $json = $this->curl->get($url);

        if (true === $raw) {
            return $json;
        }

        $obj = json_decode($json);

        if (null === $obj or $obj->message ?? false) {
            throw new Exception('Project Not Found', 404);
        }

        return $json;
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function getStatus(string $url, string $username, string $repo)
    {
        if ('https://api.github.com' === $this->api_url) {
            // GitHub 不能添加重复 webhooks ,这里跳过判断
            return 0;
        }

        $json = $this->getWebhooks(false, $username, $repo);

        $result = json_decode($json);

        if ($result) {
            foreach ($result as $k) {
                if ($url === $k->url) {
                    return 1;

                    break;
                }
            }
        }

        return 0;
    }

    /**
     * @param $data
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function setWebhooks($data, string $username, string $repo, ?string $id)
    {
        $url = $this->api_url.'/repos/'.$username.'/'.$repo.'/hooks';

        $json = $this->curl->post($url, $data, ['content-type' => 'application/json']);

        $obj = json_decode($json);

        if ($obj->message ?? false) {
            if ('Not Found' === $obj->message) {
                throw new Exception('Not Found, maybe you are Collaborators !', 404);
            }

            throw new Exception('Hook already exists on this repository', 422);
        }

        if (0 !== json_last_error()) {
            throw new Exception('Project Not Found', 404);
        }

        return $json;
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function unsetWebhooks(string $username, string $repo, string $id)
    {
        $url = $this->api_url.sprintf('/repos/%s/%s/hooks/%s', $username, $repo, $id);

        return $this->curl->delete($url);
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Curl\Curl;

class Wechat
{
    /**
     * @param $code
     * @param $time
     * @param $type
     * @param $repo_name
     * @param $branch
     * @param $committer
     * @param $info
     * @param $url
     *
     * @return array
     */
    public static function createTemplateContentArray($code, $time, $type, $repo_name, $branch, $committer, $info, $url)
    {
        return [
            'code' => $code,
            'url' => $url,
            'messages' => [
                'time' => $time,
                'type' => $type,
                'repo_name' => $repo_name,
                'branch' => $branch,
                'committer' => $committer,
                'info' => $info,
            ],
        ];
    }

    /**
     * Wechat constructor.
     *
     * @param string $template_id
     * @param string $wechat_user_id
     * @param Curl   $curl
     * @param array  $data
     *
     * @return array|mixed|string
     */
    public static function push(string $template_id, string $wechat_user_id, Curl $curl, array $data)
    {
        $code = $data['code'];
        $time = $data['messages']['time'];
        $type = $data['messages']['type'];
        $name = $data['messages']['repo_name'];
        $branch = $data['messages']['branch'];
        $committer = $data['messages']['committer'];
        $info = $data['messages']['info'];
        $url = $data['url'];

        $time = date('Y-m-d H:i:s', $time);

        $data = ['touser' => $wechat_user_id,
            'template_id' => $template_id,
            'url' => $url,
            'data' => ['code' => ['value' => $code, 'color' => '#173177'],
                'time' => ['value' => $time, 'color' => '#173177'],
                'type' => ['value' => $type, 'color' => '#173177'],
                'repo_name' => ['value' => $name, 'color' => '#173177'],
                'branch' => ['value' => $branch, 'color' => '#173177'],
                'committer' => ['value' => $committer, 'color' => '#173177'],
                'info' => ['value' => $info, 'color' => '#173177'],
            ],
        ];
        $data = json_encode($data);
        $curl->setHeader('content-type', 'application/json;charset=utf-8');
        $data = $curl->post('https://wechat.developer.khs1994.com/template', $data);

        return $data;
    }
}

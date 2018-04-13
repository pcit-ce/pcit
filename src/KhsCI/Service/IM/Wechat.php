<?php

declare(strict_types=1);

namespace KhsCI\Service\IM;

use Curl\Curl;

class Wechat
{
    public function __construct($config, Curl $curl, $data)
    {
        $template_id = $config['template_id'];
        $toUser = $config['user_id'];

        $code = $data['code'];
        $time = $data['messages']['time'];
        if (strlen($time) == 10) {
            $time = date('Y-m-d H:i:s', $time);
        }
        $type = $data['messages']['type'];
        $name = $data['messages']['repo_name'];
        $branch = $data['messages']['branch'];
        $committer = $data['messages']['committer'];
        $info = $data['messages']['info'];
        $url = $data['url'];
        $data = ['touser' => $toUser,
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

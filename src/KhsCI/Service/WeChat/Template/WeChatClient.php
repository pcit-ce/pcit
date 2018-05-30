<?php

namespace KhsCI\Service\WeChat\Template;

use KhsCI\KhsCI;

class WeChatClient
{
    private $template_id;

    private $template_message;

    private $openId;

    public function __construct(KhsCI $app)
    {
        $this->template_id = $app['config']['wechat']['template_id'];

        $this->template_message = $app->wechat->template_message;

        $this->openId = $app['config']['wechat']['open_id'];
    }

    /**
     * @param string      $code
     * @param string      $time
     * @param string      $event_type
     * @param string      $repo_name
     * @param string      $branch
     * @param string      $committer
     * @param string      $info
     *
     * @param string      $url
     * @param string|null $openId
     *
     * @return mixed
     */
    public function SendTemplateMessage(string $code,
                                        string $time,
                                        string $event_type,
                                        string $repo_name,
                                        string $branch,
                                        string $committer,
                                        string $info,
                                        string $url,
                                        string $openId = null)
    {
        $openId || $openId = $this->openId;

        /**
         * 结果 {{code.DATA}} 时间： {{time.DATA}} 类型： {{event_type.DATA}} 仓库： {{repo_name.DATA}} 分支： {{branch.DATA}} 推送人： {{committer.DATA}} 信息： {{info.DATA}}
         *
         */
        $array = [
            'touser' => $openId,
            'template_id' => $this->template_id,
            'url' => $url,
            'data' => [
                "code" => [
                    'value' => $code,
                    'color' => '#173177'
                ],
                "time" => [
                    'value' => $time,
                ],
                'event_type' => [
                    'value' => $event_type
                ],
                'repo_name' => [
                    'value' => $repo_name
                ],
                'branch' => [
                    'value' => $branch
                ],
                'committer' => [
                    'value' => $committer
                ],
                'info' => [
                    'value' => $info
                ]
            ]
        ];

        return $this->template_message->send($array);
    }
}

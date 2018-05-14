<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use Curl\Curl;
use Exception;
use KhsCI\Support\JSON;
use TencentAI\Error\TencentAIError;
use TencentAI\TencentAI;

class Comments
{
    /**
     * @var Curl
     */
    private static $curl;

    private static $api_url;
    /**
     * @var TencentAI
     */
    private static $tencent_ai;

    public function __construct(Curl $curl, string $api_url, TencentAI $tencent_ai)
    {
        static::$curl = $curl;

        static::$api_url = $api_url;

        static::$tencent_ai = $tencent_ai;
    }

    /**
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param string $source
     * @param bool   $enable_tencent_ai
     *
     * @return mixed
     * @throws Exception
     */
    public function create(string $repo_full_name,
                           int $issue_number,
                           string $source,
                           bool $enable_tencent_ai = true)
    {
        $url = static::$api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/comments';

        if ($enable_tencent_ai) {

            $nlp = static::$tencent_ai->nlp();

            try {
                $chat = $nlp->chat($source, (string ) $issue_number);
                $chat = JSON::beautiful(
                    json_encode($chat, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $chat = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            $translate = static::$tencent_ai->translate();

            try {
                $translate = $translate->aILabText($source);
                $translate = JSON::beautiful(
                    json_encode($translate, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $translate = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            try {
                $sem = $nlp->wordcom($source);

                $sem = JSON::beautiful(
                    json_encode($sem, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $sem = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            $data = <<<EOF
>$source

### Tencent AI 分析结果

<details>
<summary>中英互译</summary>

```json\n
$translate
```

</details>

<details>
<summary>智能闲聊</summary>

```json\n
$chat
```

</details>

<details>
<summary>语义解析</summary>

```json\n
$sem
```

</details>

EOF;
        }

        $data = [
            'body' => $data
        ];

        return static::$curl->post($url, json_encode($data));
    }
}

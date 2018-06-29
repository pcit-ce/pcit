<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use Curl\Curl;
use Exception;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;
use TencentAI\Error\TencentAIError;
use TencentAI\TencentAI;

class CommentsGitHubClient
{
    /**
     * @var Curl
     */
    private $curl;

    private $api_url;

    /**
     * @var TencentAI
     */
    private $tencent_ai;

    public function __construct(Curl $curl, string $api_url, TencentAI $tencent_ai)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;

        $this->tencent_ai = $tencent_ai;
    }

    /**
     * Create a comment.
     *
     * 201
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param string $source
     * @param bool   $enable_tencent_ai
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function create(string $repo_full_name,
                           int $issue_number,
                           string $source,
                           bool $enable_tencent_ai = true)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/comments';

        $source_show_in_md = $source;

        $data = $source;

        if ($enable_tencent_ai) {
            $nlp = $this->tencent_ai->nlp();

            $translate = $this->tencent_ai->translate();

            // 鉴定语言 default is en || support en or zh

            try {
                $lang = $translate->detect($source);
                $lang = $lang['data']['lang'] ?? 'en';
            } catch (TencentAIError $e) {
                $lang = 'en';
            }

            try {
                $translate = $translate->aILabText($source);
                $translate = JSON::beautiful(
                    json_encode($translate, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $translate = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            $translate_output = json_decode($translate, true)['data']['trans_text'] ?? null;

            $lang_show_in_md = 'Chinese';

            if ('en' === $lang) {
                $source = $translate_output;
                $lang_show_in_md = 'English';
            }

            try {
                $chat = $nlp->chat($source, (string) $issue_number);
                $chat = JSON::beautiful(
                    json_encode($chat, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $chat = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            //            try {
            //                $sem = $nlp->wordcom($source);
            //
            //                $sem = JSON::beautiful(
            //                    json_encode($sem, JSON_UNESCAPED_UNICODE));
            //            } catch (TencentAIError $e) {
            //                $sem = JSON::beautiful(
            //                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            //            }

            //            try {
            //                $pos = $nlp->wordpos($source);
            //
            //                $pos = JSON::beautiful(
            //                    json_encode($pos, JSON_UNESCAPED_UNICODE));
            //            } catch (TencentAIError $e) {
            //                $pos = JSON::beautiful(
            //                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            //            }

            //            try {
            //                $ner = $nlp->wordner($source);
            //
            //                $ner = JSON::beautiful(
            //                    json_encode($ner, JSON_UNESCAPED_UNICODE));
            //            } catch (TencentAIError $e) {
            //                $ner = JSON::beautiful(
            //                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            //            }

            try {
                $polar = $nlp->textPolar($source);

                $polar = JSON::beautiful(
                    json_encode($polar, JSON_UNESCAPED_UNICODE));
            } catch (TencentAIError $e) {
                $polar = JSON::beautiful(
                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            }

            $emoji = json_decode($polar)->data->polar ?? 0;

            if (0 === $emoji) {
                $emoji = 'smile';
            } elseif (1 === $emoji) {
                $emoji = '+1';
            }

            //            try {
            //                $seg = $nlp->wordseg($source);
            //
            //                $seg = JSON::beautiful(
            //                    json_encode($seg, JSON_UNESCAPED_UNICODE));
            //            } catch (TencentAIError $e) {
            //                $seg = JSON::beautiful(
            //                    json_encode([$e->getMessage(), $e->getCode()], JSON_UNESCAPED_UNICODE));
            //            }

            $data = <<<EOF
<blockquote>
            
$source_show_in_md

</blockquote>

$translate_output

### Tencent AI Analytic Result :$emoji:

<details>
<summary><strong>中英互译 Can't Understand $lang_show_in_md ? Please Click and See JSON content </strong></summary>

```json\n
$translate
```

</details>

<details>
<summary><strong>智能闲聊</strong></summary>

```json\n
$chat
```

</details>

<details>
<summary><strong>情感分析</strong></summary>

```json\n
$polar
```

</details>
EOF;
        }

        $data = [
            'body' => $data,
        ];

        $output = $this->curl->post($url, json_encode($data));

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code is not 201 '.$http_return_code);
        }

        return $output;
    }

    /**
     * List comments on an issue.
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name, int $issue_number)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/comments';

        return $this->curl->get($url);
    }

    /**
     * List comments in a repository.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listInRepository(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/comments';

        return $this->curl->get($url);
    }

    /**
     * Get a single comment.
     *
     * @param string $repo_full_name
     * @param int    $comment_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSingle(string $repo_full_name, int $comment_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/comments/'.$comment_id;

        return $this->curl->get($url);
    }

    /**
     * @param string $repo_full_name
     * @param int    $comment_id
     * @param string $body
     *
     * @throws Exception
     */
    public function edit(string $repo_full_name, int $comment_id, string $body): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/comments/'.$comment_id;

        $data = [
            'body' => $body,
        ];

        $this->curl->patch($url, json_encode($data));

        $http_return_code = $this->curl->getCode();

        if (200 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code is not 200 '.$http_return_code);

            throw new Exception('Edit Issue comment Error', $http_return_code);
        }
    }

    /**
     * 204.
     *
     * @param string $repo_full_name
     * @param int    $comment_id
     *
     * @throws Exception
     */
    public function delete(string $repo_full_name, int $comment_id): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/comments/'.$comment_id;

        $this->curl->delete($url);

        $http_return_code = $this->curl->getCode();

        if (204 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code Is Not 204 '.$http_return_code);

            throw new Exception('Delete Issue comment Error', $http_return_code);
        }
    }
}

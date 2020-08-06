<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Issue;

use Curl\Curl;
use PCIT\GitHub\Service\ClientCommon;
use PCIT\GPI\Service\Issue\ClientInterface;
use TencentAI\TencentAI;

class Client extends ClientCommon implements ClientInterface
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected $api_url;

    private $header = [
        'Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.symmetra-preview+json',
    ];

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
     * List all issues assigned to the authenticated user across all visible repositories including owned repositories,
     * member repositories, and organization repositories:.
     *
     * @throws \Exception
     */
    public function list()
    {
        $url = $this->api_url.'/issues';

        return $this->curl->get($url);
    }

    /**
     * List issues for a repository.
     */
    public function listRepositoryIssues(string $repo_full_name): void
    {
    }

    /**
     * Get a single issue.
     *
     * 201
     *
     * @param mixed $issue_number
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getSingle(string $repo_full_name, $issue_number)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number;

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * Create an issue.
     *
     * @param array $labels
     * @param array $assignees
     *
     * @throws \Exception
     */
    public function create(
        string $repo_full_name,
        string $title,
        string $body,
        int $milestone,
        array $labels = null,
        array $assignees = null
    ): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues';

        $this->curl->post($url, json_encode(array_filter(compact(
            'title',
            'body',
            'milestone',
            'labels',
            'assignees'
        ))), $this->header);

        $this->successOrFailure(201, true);
    }

    /**
     * Edit an issue.
     *
     * @param string $repo_full_name repo full name
     * @param string $title          issue title
     * @param string $body
     * @param string $state          State of the issue. Either open or closed.
     * @param int    $milestone
     * @param array  $labels
     * @param array  $assignees
     * @param mixed  $issue_number
     *
     * @see https://developer.github.com/v3/issues/#edit-an-issue
     *
     * @throws \Exception
     */
    public function edit(
        string $repo_full_name,
        $issue_number,
        string $title = null,
        string $body = null,
        string $state = null,
        int $milestone = null,
        array $labels = null,
        array $assignees = null
    ): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number;

        $this->curl->patch($url, json_encode(array_filter(compact(
            'title',
            'body',
            'state',
            'milestone',
            'labels',
            'assignees'
        ))), $this->header);

        $this->successOrFailure(200, true);
    }

    /**
     * Lock an issue.
     *
     * 204.
     *
     * @param string $lock_reason  The reason for locking the issue or pull request conversation. Lock will fail if
     *                             you don't use one of these reasons: off-topic too heated resolved spam
     * @param mixed  $issue_number
     *
     * @throws \Exception
     */
    public function lock(string $repo_full_name, $issue_number, string $lock_reason = null): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/lock';

        if ($lock_reason) {
            $data = [
                'locked' => true,
                'active_lock_reason' => $lock_reason,
            ];
            $this->curl->put(
                $url,
                json_encode($data),
                ['Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.sailor-v-preview+json']
            );
        } else {
            $this->curl->put(
                $url,
                null,
                ['Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.sailor-v-preview+json']
            );
        }

        $this->successOrFailure(204, true);
    }

    /**
     * Unlock an issue.
     *
     * @param mixed $issue_number
     *
     * @throws \Exception
     */
    public function unlock(string $repo_full_name, $issue_number): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/lock';

        $this->curl->delete($url);

        $this->successOrFailure(204, true);
    }

    /**
     * @param mixed $issue_number
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function timeline(string $repo_full_name, $issue_number)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/timeline';

        return $this->curl->get($url, [], ['Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.mockingbird-preview+json']);
    }

    /**
     * 检查标题是否为中文，若为中文则翻译为英文.
     *
     * @param $title
     * @param $rid
     * @param $repo_full_name
     * @param $issue_number
     *
     * @throws \Exception
     */
    public function translateTitle(
        string $repo_full_name,
        $issue_number,
        ?int $rid,
        ?string $title
    ): void {
        if (!$title) {
            // get issue title
            $result = $this->getSingle($repo_full_name, $issue_number);

            $title = json_decode($result)->title;
        }

        try {
            $result = $this->tencent_ai->translate->detect($title);

            $lang = $result['data']['lang'] ?? 'en';

            if ('zh' === $lang) {
                $result = $this->tencent_ai->translate->aILabText($title, 1);

                $title = $result['data']['trans_text'] ?? null;
            }
        } catch (\Throwable $e) {
            \Log::info($e->__toString());

            return;
        }

        if ('zh' !== $lang or null === $title) {
            return;
        }

        $this->edit($repo_full_name, $issue_number, $title);
    }
}

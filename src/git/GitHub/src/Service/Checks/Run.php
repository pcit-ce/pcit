<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Checks;

use Curl\Curl;
use Exception;
use PCIT\Framework\Support\Date;

/**
 * Class Run.
 *
 * @see https://developer.github.com/v3/checks/runs/
 */
class Run
{
    protected $header = [
        'Accept' => 'application/vnd.github.antiope-preview+json;
             application/vnd.github.machine-man-preview+json;
             application/vnd.github.speedy-preview+json',
    ];

    /**
     * @var Curl
     */
    protected $curl;

    private $api_url;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function create(RunData $run_data)
    {
        $url = $this->api_url.'/repos/'.$run_data->repo_full_name.'/check-runs';

        $data = array_filter([
            'name' => $run_data->name,
            'head_sha' => $run_data->commit_id,
            'details_url' => $run_data->details_url,
            'external_id' => $run_data->external_id,
            'status' => $run_data->status,
            'started_at' => Date::Int2ISO($run_data->started_at),
            'completed_at' => Date::Int2ISO($run_data->completed_at),
            'conclusion' => $run_data->conclusion,
            'output' => array_filter([
                'title' => $run_data->title,
                'summary' => $run_data->summary,
                'text' => $run_data->text,
                'annotations' => $run_data->annotations,
                'images' => $run_data->images,
            ]),
            'actions' => $run_data->actions,
        ]);

        $request = json_encode($data);

        $output = $this->curl->post($url, $request, $this->header);

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            \Log::debug('Http Return code is not 201 '.$http_return_code);
        }

        return $output;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function update(RunData $run_data)
    {
        $url = $this->api_url.'/repos/'.$run_data->repo_full_name.'/check-runs/'.$run_data->check_run_id;

        $data = array_filter([
            'name' => $run_data->name,
            'details_url' => $run_data->details_url,
            'external_id' => $run_data->external_id,
            'status' => $run_data->status,
            'started_at' => Date::Int2ISO($run_data->started_at),
            'completed_at' => Date::Int2ISO($run_data->completed_at),
            'conclusion' => $run_data->conclusion,
            'output' => array_filter([
                'title' => $run_data->title,
                'summary' => $run_data->summary,
                'text' => $run_data->text,
                'annotations' => $run_data->annotations,
                'images' => $run_data->images,
            ]),
            'actions' => $run_data->actions,
        ]);

        $request = json_encode($data);

        $output = $this->curl->patch($url, $request, $this->header);

        $http_return_header = $this->curl->getCode();

        if (200 !== $http_return_header) {
            \Log::debug(__FILE__, __LINE__, 'Http Return Code is not 200 '.$http_return_header);
        }

        return $output;
    }

    /**
     * List check runs for a specific ref.
     *
     * @param string $ref        Required. Can be a SHA, branch name, or tag name.
     * @param string $check_name returns check runs with the specified name
     * @param string $status     Returns check runs with the specified status. Can be one of queued, in_progress, or
     *                           completed.
     * @param string $filter     Filters check runs by their completed_at timestamp. Can be one of latest (returning
     *                           the most recent check runs) or all. Default: latest
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listSpecificRef(string $repo_full_name,
                                    string $ref,
                                    string $check_name,
                                    string $status,
                                    string $filter)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits/'.$ref.'/check-runs';

        $data = [
            'check_name' => $check_name,
            'status' => $status,
            'filter' => $filter,
        ];

        $url = $url.'?'.http_build_query($data);

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * List check runs in a check suite.
     *
     * @param string $check_name returns check runs with the specified name
     * @param string $status     Returns check runs with the specified status. Can be one of queued, in_progress, or
     *                           completed.
     * @param string $filter     Filters check runs by their completed_at timestamp. Can be one of latest (returning
     *                           the most recent check runs) or all. Default: latest
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listCheckSuite(string $repo_full_name,
                                   int $id,
                                   string $check_name,
                                   string $status,
                                   string $filter)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-suites/'.$id.'/check-rus';

        $data = [
            'check_name' => $check_name,
            'status' => $status,
            'filter' => $filter,
        ];

        $url = $url.'?'.http_build_query($data);

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * Get a single check run.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSingle(string $repo_full_name, int $check_run_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-runs/'.$check_run_id;

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * List annotations for a check run.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listAnnotations(string $repo_full_name, int $check_run_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-runs/'.$check_run_id.'/annotations';

        return $this->curl->get($url, null, $this->header);
    }
}

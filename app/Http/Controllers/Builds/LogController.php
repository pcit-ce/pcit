<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use App\Job;
use PCIT\Framework\Attributes\Route;

class LogController
{
    /**
     * Returns a single log.
     *
     * @param $job_id
     *
     * @throws \Exception
     *
     * @return array|string
     */
    @@Route('get', 'api/job/{job_id}/log')
    public function __invoke($job_id)
    {
        // return json
        if (\in_array('application/json', \Request::getAcceptableContentTypes())) {
            return $this->json($job_id);
        }

        return $this->raw($job_id);
    }

    public function getFromDB($job_id): array
    {
        $log = Job::getLog((int) $job_id);

        return json_decode($log, true, 512, JSON_THROW_ON_ERROR);
    }

    public function json($job_id)
    {
        $s3_json_file = "logs/$job_id.json";
        $store_to_s3 = false;

        if ('success' === Job::getBuildStatus((int) $job_id)) {
            try {
                \Storage::getMetadata($s3_json_file);

                return \Response::redirect(\Storage::getPresignedUrl($s3_json_file));
            } catch (\Throwable $e) {
                // 文件不存在
                $store_to_s3 = true;
            }
        }

        $log_array = [];

        // 从数据库读取
        try {
            $log_array = $this->getFromDB($job_id);

            $store_to_s3 ? \Storage::put($s3_json_file, json_encode($log_array)) : null;

            return $log_array;
        } catch (\Throwable $e) {
            if ($log_array) {
                return $log_array;
            }

            return \Response::make('not found', 404);
        }
    }

    public function raw($job_id)
    {
        $s3_raw_file = "logs/$job_id.txt";
        $store_to_s3 = false;

        if ('success' === Job::getBuildStatus((int) $job_id)) {
            try {
                \Storage::getMetadata($s3_raw_file);

                return \Response::redirect(\Storage::getPresignedUrl($s3_raw_file));
            } catch (\Throwable $e) {
                // 文件不存在
                $store_to_s3 = true;
            }
        }

        $text_plain_log = '';

        try {
            $log_array = $this->getFromDB($job_id);
        } catch (\Throwable $e) {
            return \Response::make('not found', 404);
        }

        foreach ($log_array as $step => $log) {
            $start_time = substr(explode("\n", $log)[0], 0, 30);
            $text_plain_log .= $start_time.' ##[step]'.$step."\n".$log."\n";
        }

        try {
            $store_to_s3 ? \Storage::put($s3_raw_file, $text_plain_log) : null;
        } catch (\Throwable $e) {
        }

        return \Response::make($text_plain_log, 200, [
            'Content-type' => 'text/plain',
        ]);
    }

    /**
     * Removes the contents of a log. It gets replace with the message: Log removed at 2017-02-13 16:00:00 UTC.
     *
     * @param $job_id
     *
     * @throws \Exception
     */
    @@Route('delete', 'api/job/{job_id}/log')
    public function delete($job_id)
    {
        JWTController::check(Job::getBuildKeyId((int) $job_id));

        $log = 'Log removed at '.date('c');

        Job::updateLog((int) $job_id, $log);

        $s3_json_file = "logs/$job_id.json";
        $s3_raw_file = "logs/$job_id.txt";
        \Storage::put($s3_json_file, json_encode([
            'message' => $log,
        ]));
        \Storage::put($s3_raw_file, $log);

        return \Response::make('', 204);
    }

    /**
     * 重新运行 job 时删除 S3 中的 log.
     *
     * @param mixed $job_id
     * @param mixed $build_id
     */
    public function deleteStoreInS3($job_id = 0, $build_id = 0): void
    {
        if ($job_id) {
            $this->deletejobStoreInS3($job_id);

            return;
        }

        $job_ids = Job::getByBuildKeyID((int) $build_id);

        foreach ($job_ids as $index => $job) {
            $this->deletejobStoreInS3($job['id']);
        }
    }

    public function deletejobStoreInS3($job_id): void
    {
        $s3_json_file = "logs/$job_id.json";
        $s3_raw_file = "logs/$job_id.txt";

        try {
            \Storage::delete($s3_json_file);
        } catch (\Throwable $e) {
        }

        try {
            \Storage::delete($s3_raw_file);
        } catch (\Throwable $e) {
        }
    }
}

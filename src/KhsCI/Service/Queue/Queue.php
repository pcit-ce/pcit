<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

use Docker\Container\Container;
use Docker\Docker;
use Docker\Image\Image;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CIConst;
use KhsCI\Support\DATE;
use KhsCI\Support\DB;
use KhsCI\Support\GIT;
use KhsCI\Support\HTTP;

class Queue
{
    private static $gitType;

    /**
     * @throws \Exception
     */
    public function __invoke(): void
    {
        $sql = <<<EOF
SELECT 

id,git_type,rid,commit_id,commit_message,branch 

FROM 

builds WHERE build_status=? AND event_type=? ORDER BY id DESC LIMIT 1;
EOF;

        $output = DB::select($sql, [
            CIConst::BUILD_STATUS_PENDING,
            CIConst::BUILD_EVENT_PUSH,
        ]);

        foreach ($output as $k) {
            $build_key_id = $k['id'];
            $git_type = $k['git_type'];
            $rid = $k['rid'];
            $commit_id = $k['commit_id'];
            $commit_message = $k['commit_message'];
            $branch = $k['branch'];

            self::$gitType = $git_type;
            try {

                // commit 信息跳过构建
                self::skip($commit_message);

                // 是否启用构建
                self::getRepoBuildActivateStatus($rid);

                self::run($build_key_id, $rid, $commit_id, $branch);

            } catch (Exception $e) {
                switch ($e->getMessage()) {
                    case CIConst::BUILD_STATUS_SKIP:
                        $this->setBuildStatusSkip($build_key_id);
                        break;
                    case CIConst::BUILD_STATUS_ERRORED:
                        $this->setBuildStatusErrored($build_key_id);
                        break;
                    case CIConst::BUILD_STATUS_FAILED:
                        $this->setBuildStatusFailed($build_key_id);
                        break;
                    case CIConst::BUILD_STATUS_PASSED:
                        $this->setBuildStatusPassed($build_key_id);
                        break;
                    case CIConst::BUILD_STATUS_INACTIVE:
                        $this->setBuildStatusInactive($build_key_id);
                        break;
                    default:
                        throw new Exception($e->getMessage(), 500);
                }
            }
        }
    }

    /**
     * 检查是否启用了构建.
     *
     * @param $rid
     *
     * @throws \Exception
     */
    private function getRepoBuildActivateStatus($rid): void
    {
        $gitType = self::$gitType;

        $sql = 'SELECT build_activate FROM repo WHERE rid=? AND git_type=?';

        $output = DB::select($sql, [$rid, $gitType]);

        foreach ($output as $k) {
            if (0 == $k['build_activate']) {
                throw new Exception(CIConst::BUILD_STATUS_INACTIVE, 500);
            }
        }
    }

    /**
     * 检查 commit 信息跳过构建.
     *
     * @param string $commit_message
     *
     * @throws Exception
     */
    private function skip(string $commit_message): void
    {
        $output = stripos($commit_message, '[skip ci]');
        $output2 = stripos($commit_message, '[ci skip]');

        if (false === $output && false === $output2) {
            return;
        }

        throw new Exception(CIConst::BUILD_STATUS_SKIP, 500);
    }

    /**
     * 执行构建.
     *
     * @param        $build_key_id
     * @param        $rid
     * @param string $commit_id
     * @param string $branch
     *
     * @throws Exception
     */
    private function run($build_key_id, $rid, string $commit_id, string $branch): void
    {
        $unique_id = session_create_id();

        $gitType = self::$gitType;

        $sql = 'SELECT repo_full_name FROM repo WHERE git_type=? AND rid=?';

        $output = DB::select($sql, [$gitType, $rid]);

        $repo_full_name = '';

        foreach ($output as $k) {
            $repo_full_name = $k['repo_full_name'];
        }

        $base = $repo_full_name.'/'.$commit_id;

        $url = "https://raw.githubusercontent.com/$base/.drone.yml";
        // $url = "https://ci2.khs1994.com:10000/.drone.yml";

        $output = HTTP::get($url);

        $yaml_obj = (object)yaml_parse($output);

        $yaml_to_json = json_encode($output);

        $sql = "UPDATE builds SET config=? WHERE id=? ";

        // DB::update($sql, [$yaml_to_json, $build_key_id]);

        /**
         * 变量命名尽量与 docker container run 的参数保持一致.
         *
         * 项目根目录
         */
        $workspace = $yaml_obj->workspace;

        $base_path = $workspace['base'] ?? null;

        $path = $workspace['path'] ?? $repo_full_name;

        if ('.' === $path) {
            $path = null;
        }

        /**
         * --workdir.
         */
        $workdir = $base_path.'/'.$path;

        $git_url = GIT::getUrl($gitType, $repo_full_name);

        $docker = Docker::docker(Docker::createOptionArray('127.0.0.1:2375'));

        $docker_container = $docker->container;
        $docker_image = $docker->image;

        $docker_container->setEnv([
            'DRONE_REMOTE_URL' => $git_url,
            'DRONE_WORKSPACE' => $workdir,
            'DRONE_BUILD_EVENT' => 'push',
            'DRONE_COMMIT_SHA' => $commit_id,
            'DRONE_COMMIT_REF' => 'refs/heads/'.$branch,
            'LANG' => 'en_US.UTF-8',
            'LANGUAGE' => 'en_US:en',
            'LC_ALL' => 'en_US.UTF-8',
        ]);

        $docker_container->setHostConfig(["$unique_id:$workdir"]);

        $container_id = $this->docker_container_run('plugins/git', $docker_image, $docker_container);

        $this->docker_container_logs($docker_container, $container_id, $build_key_id);

        $redis = Cache::connect();

        var_dump($redis->hGet('build_log', $build_key_id));

        $pipeline = $yaml_obj->pipeline;

        $matrix = $yaml_obj->matrix;

        foreach ($pipeline as $setup => $array) {
            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;

            $arg = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $image, $output);

            if ($arg) {
                foreach ($output[0] as $k) {

                    // ${XXX} -> md5('KHSCI')

                    $var_secret = md5('KHSCI');

                    $image = str_replace($k, $var_secret, $image);

                    $array = explode('}', $k);

                    $k = trim($array[0], '${');

                    if (in_array($k, array_keys($matrix))) {
                        $var = $matrix["$k"][0];
                    }

                    $image = str_replace($var_secret, $var, $image);
                }
            }

            var_dump($image);

            var_dump($event);

//            if ($event and in_array('push', $event)) {
//                continue;
//            }

            $content = '\n';

            for ($i = 0; $i < count($commands); ++$i) {
                $command = addslashes($commands[$i]);

                $content .= 'echo $ '.str_replace('$', '\\\\$', $command).'\n\n';

                $content .= 'echo;echo'.'\n\n';

                $content .= str_replace('$$', '$', $command).'\n\n';

                $content .= 'echo;echo'.'\n\n';
            }

            // var_dump($content);

            // var_dump(stripcslashes($content));

            $ci_script = base64_encode(stripcslashes($content));

            $docker_container->setEnv([
                'CI_SCRIPT' => $ci_script,
            ]);

            $docker_container->setHostConfig(["$unique_id:$workdir"]);

            $docker_container->setEntrypoint(['/bin/sh', '-c']);

            $docker_container->setWorkingDir($workdir);

            $cmd = ['echo $CI_SCRIPT | base64 -d | /bin/sh -e'];

            $container_id = $this->docker_container_run($image, $docker_image, $docker_container, $cmd);

            $output = $this->docker_container_logs($docker_container, $container_id, $build_key_id);

            var_dump($output);

            $redis = Cache::connect();

            var_dump($redis->hGet('build_log', $build_key_id));

            var_dump(__LINE__);

            exit;
        }

        var_dump(__LINE__);

        $services = $yaml_obj->services;

        throw new Exception(CIConst::BUILD_STATUS_PASSED, 200);
    }

    /**
     * @param     $rid
     * @param int $lastId
     *
     * @throws Exception
     */
    private function setBuildStatusInactive($rid, int $lastId = 0): void
    {
        $sql = 'UPDATE builds SET build_status=? WHERE git_type=? AND rid=? AND id>?';

        DB::update($sql, [CIConst::BUILD_STATUS_INACTIVE, self::$gitType, $rid, $lastId]);
    }


    /**
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function setBuildStatusSkip(string $build_key_id)
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_SKIP, $build_key_id]);
    }

    /**
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function setBuildStatusPending(string $build_key_id)
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_PENDING, $build_key_id]);
    }

    /**
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function setBuildStatusErrored(string $build_key_id)
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        /*
         * 更新数据库状态
         */
        DB::update($sql, [CIConst::BUILD_STATUS_ERRORED, $build_key_id]);
        /*
         * 通知 GitHub commit Status
         */

        /*
         * 微信通知
         */
    }

    /**
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function setBuildStatusFailed(string $build_key_id)
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_FAILED, $build_key_id]);
    }

    /**
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function setBuildStatusPassed(string $build_key_id)
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_PASSED, $build_key_id]);
    }

    /**
     * @param string            $image_name
     * @param Image             $docker_image
     * @param Container         $docker_container
     * @param string|array|null $cmd
     *
     * @return string
     *
     * @throws Exception
     */
    private function docker_container_run(string $image_name,
                                          Image $docker_image,
                                          Container $docker_container,
                                          $cmd = null)
    {
        $docker_image->pull($image_name);

        $output = json_decode($docker_container->create($image_name, null, $cmd));

        $id = $output->Id ?? '';

        if ('' === $id) {
            throw new Exception(CIConst::BUILD_STATUS_ERRORED, 500);
        }

        $output = $docker_container->start($id);

        if ((bool)$output) {
            throw new Exception($output, 500);
        }

        return $id;
    }

    /**
     * @param Container $docker_container
     * @param string    $container_id
     * @param string    $build_key_id
     *
     * @return array
     *
     * @throws Exception
     */
    private function docker_container_logs(Container $docker_container, string $container_id, string $build_key_id)
    {
        $redis = Cache::connect();
        $i = -1;

        $startedAt = null;
        $finishedAt = null;

        while (1) {
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($container_id))->State;

            $status = $image_status_obj->Status;

            $startedAt = $image_status_obj->StartedAt;
            $startedAt = DATE::parse($startedAt);

            $first = false;

            if ('running' === $status) {
                if (0 === $i) {
                    $first = true;
                    $since_time = $startedAt - 5;
                    $until_time = $startedAt;
                }

                if (!$first) {
                    $since_time = $until_time;
                    $until_time = $until_time + 5;
                }

                sleep(6);

                $image_log = $docker_container->logs(
                    $container_id,
                    false,
                    true,
                    true,
                    $since_time,
                    $until_time,
                    true
                );

                echo $image_log;

                continue;
            } else {

                $image_log = $docker_container->logs(
                    $container_id,
                    false,
                    true,
                    true,
                    0,
                    0,
                    true
                );

                $prev_docker_log = $redis->hget('build_log', $build_key_id);

                var_dump($prev_docker_log);

                var_dump($image_log);

                $output=$redis->hset('build_log', $build_key_id, $prev_docker_log.$image_log);

                var_dump($output);

                var_dump($build_key_id);

                var_dump($redis->hGet('build_log', $build_key_id));

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                /**
                 * 将日志存入数据库.
                 */
                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    throw new Exception(CIConst::BUILD_STATUS_ERRORED, 500);
                }

                break;
            }
        }

        return [
            'start' => $startedAt,
            'stop' => $finishedAt,
        ];
    }
}

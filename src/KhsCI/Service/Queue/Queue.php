<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

use Docker\Container\Container;
use Docker\Docker;
use Docker\Image\Image;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CIConst;
use KhsCI\Support\DB;
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

builds WHERE build_status=? AND event_type=? ORDER BY id DESC;
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

            // commit 信息跳过构建
            $skip = self::skip($commit_message);

            if ($skip) {
                $build_status_skip = CIConst::BUILD_STATUS_SKIP;
                $sql = 'UPDATE builds SET build_status=? WHERE git_type=? AND commit_id=?';
                DB::update($sql, [$build_status_skip, self::$gitType, $commit_id]);

                continue;
            }

            // 是否启用构建
            $build_activated = self::getRepoBuildActivateStatus($rid);

            if ($build_activated) {
                try {
                    self::run($build_key_id, $rid, $commit_id, $branch);
                } catch (Exception $e) {
                    switch ($e->getMessage()) {
                        case CIConst::BUILD_STATUS_ERRORED:
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
                            break;
                        default:
                            echo $e->getMessage();
                            exit;
                    }
                }
            } else {
                self::inactive($rid);
            }
            exit;
        }
    }

    /**
     * 检查是否启用了构建.
     *
     * @param $rid
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function getRepoBuildActivateStatus($rid)
    {
//        $redis = Cache::connect();
//
//        $redis->hExists(1, 2);

        $gitType = self::$gitType;

        $sql = <<<EOF
SELECT build_activate FROM repo WHERE rid=$rid AND git_type='$gitType';
EOF;
        $output = DB::select($sql);

        foreach ($output as $k) {
            if (0 == $k['build_activate']) {
                return false;
            }
        }

        return true;
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

        foreach ($output as $k) {
            $repo_full_name = $k['repo_full_name'];
        }

        $base = $repo_full_name.'/'.$commit_id;

        $url = "https://raw.githubusercontent.com/$base/.drone.yml";

        $output = HTTP::get($url);

        $yaml_obj = (object) yaml_parse($output);

        // $output = json_encode($output);

        //var_dump($output);

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

        switch ($gitType) {
            case 'github':
                $git_url = 'https://github.com/'.$repo_full_name;
                break;
            default:
                throw new Exception('Not Found', 500);
        }

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

        $output = $this->docker_container_logs($docker_container, $container_id, $build_key_id);

        $pipeline = $yaml_obj->pipeline;

        foreach ($pipeline as $setup => $array) {
            $image = $array['image'];
            // $repo = $array['repo'] ?? null;
            // $tags = $array['tags'] ?? 'latest';
            $commands = $array['commands'] ?? null;
            // $event = $array['when']['event'] ?? null;

            var_dump(count($commands));

            $temp_dir = sys_get_temp_dir().'/'.'khsci';

            if (!is_dir($temp_dir)) {
                mkdir(sys_get_temp_dir().'/'.'khsci');
            }

            $temp_file = $temp_dir.'/'.$unique_id;

            for ($i = 0; $i < count($commands); ++$i) {
                file_put_contents($temp_file, "echo + \"$commands[$i]\"\n\n", FILE_APPEND);
                file_put_contents($temp_file, "$commands[$i]\n\n", FILE_APPEND);
            }

            $ci_script = base64_encode(file_get_contents($temp_file));

            unlink($temp_file);

            $docker_container->setEnv([
                'CI_SCRIPT' => $ci_script,
            ]);

            $docker_container->setHostConfig(["$unique_id:$workdir"]);

            $docker_container->setEntrypoint(['/bin/sh', '-c']);

            $docker_container->setWorkingDir($workdir);

            $image = 'khs1994/php-fpm:7.2.5-alpine3.7';

            $cmd = ['echo $CI_SCRIPT | base64 -d | /bin/sh -e'];

            $id = $this->docker_container_run($image, $docker_image, $docker_container, $cmd);

            var_dump($id);

            exit;
        }

        $services = $yaml_obj->services;

        $matrix = $yaml_obj->matrix;

        echo 'running....';

        /**
         * 插入数据库.
         */
        $sql = '';

        DB::select($sql);
        /*
         * 更新状态
         */
        CIConst::BUILD_STATUS_ERRORED;
        CIConst::BUILD_STATUS_FAILED;
        CIConst::BUILD_STATUS_PASSED;

        /*
         * 发送通知
         */
    }

    /**
     * 检查 commit 信息跳过构建.
     *
     * @param $commit_message
     *
     * @return bool
     */
    private function skip(string $commit_message)
    {
        $output = stripos($commit_message, '[skip ci]');
        $output2 = stripos($commit_message, '[ci skip]');

        if (false === $output && false === $output2) {
            return false;
        }

        return true;
    }

    /**
     * @param     $rid
     * @param int $lastId
     *
     * @throws \Exception
     */
    private function inactive($rid, int $lastId = 0): void
    {
        $gitType = self::$gitType;

        $build_status_inactive = CIConst::BUILD_STATUS_INACTIVE;

        $sql = <<<EOF
UPDATE builds set build_status='$build_status_inactive' WHERE git_type='$gitType' AND rid='$rid' AND id>$lastId;
EOF;
        DB::update($sql);
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

        $id = $output->Id;

        $warnings = $output->Warnings;

        if (null !== $warnings) {
            throw new Exception($warnings, 500);
        }

        $output = $docker_container->start($id);

        if ((bool) $output) {
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
        while (1) {
            $git_image_status_obj = json_decode($docker_container->inspect($container_id))->State;

            $status = $git_image_status_obj->Status;

            $git_image_log = $docker_container->logs($container_id, false, true, true);

            if ('running' === $status) {
                sleep(2);
                $redis->hset('build_log', $build_key_id, $git_image_log);
            } else {
                var_dump($git_image_log);
                var_dump($build_key_id);

                $redis->hset('build_log', $build_key_id, $git_image_log);

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $git_image_status_obj->StartedAt;
                $finishedAt = $git_image_status_obj->FinishedAt;

                /**
                 * 将日志存入数据库.
                 */
                $exitCode = $git_image_status_obj->ExitCode;

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

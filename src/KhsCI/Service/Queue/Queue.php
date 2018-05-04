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
use KhsCI\Support\Log;

class Queue
{
    private static $gitType;

    /**
     * @throws Exception
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

            // commit 信息跳过构建
            self::skip($commit_message, $build_key_id);

            // 是否启用构建
            self::getRepoBuildActivateStatus($rid, $build_key_id);

            self::run($build_key_id, $rid, $commit_id, $branch);
        }
    }

    /**
     * 检查是否启用了构建.
     *
     * @param string $rid
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function getRepoBuildActivateStatus(string $rid, string $build_key_id): void
    {
        $gitType = self::$gitType;

        $sql = 'SELECT build_activate FROM repo WHERE rid=? AND git_type=?';

        $build_activate = DB::select($sql, [$rid, $gitType], true);

        if (0 == $build_activate) {
            throw new Exception(CIConst::BUILD_STATUS_INACTIVE, (int) $build_activate);
        }
    }

    /**
     * 检查 commit 信息跳过构建.
     *
     * @param string $commit_message
     * @param string $build_key_id
     *
     * @throws Exception
     */
    private function skip(string $commit_message, string $build_key_id): void
    {
        $output = stripos($commit_message, '[skip ci]');
        $output2 = stripos($commit_message, '[ci skip]');

        if (false === $output && false === $output2) {
            return;
        }

        throw new Exception(CIConst::BUILD_STATUS_SKIP, (int) $build_key_id);
    }

    /**
     * @param string $image
     * @param array  $matrix
     *
     * @return array|mixed|string
     */
    private function getImage(string $image, array $matrix)
    {
        $arg = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $image, $output);

        if ($arg) {
            foreach ($output[0] as $k) {
                // ${XXX} -> md5('KHSCI')

                $var_secret = md5('KHSCI');

                $image = str_replace($k, $var_secret, $image);

                $array = explode('}', $k);

                $k = trim($array[0], '${');

                $var = '';

                if (in_array($k, array_keys($matrix))) {
                    $var = $matrix["$k"][0];
                }

                $image = str_replace($var_secret, $var, $image);
            }
        }

        return $image;
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

        Log::connect()->debug('Create Volume '.$unique_id);
        Log::connect()->debug('Create Network '.$unique_id);

        $gitType = self::$gitType;

        $sql = 'SELECT repo_full_name FROM repo WHERE git_type=? AND rid=?';

        $repo_full_name = DB::select($sql, [$gitType, $rid], true);

        $base = $repo_full_name.'/'.$commit_id;

        $url = "https://raw.githubusercontent.com/$base/.drone.yml";
        // $url = "https://ci2.khs1994.com:10000/.drone.yml";

        $output = HTTP::get($url);

        $yaml_obj = (object) yaml_parse($output);

        $yaml_to_json = json_encode($yaml_obj);

        $sql = 'UPDATE builds SET config=? WHERE id=? ';

        DB::update($sql, [$yaml_to_json, $build_key_id]);

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
        $docker_network = $docker->network;

        $docker_network->create($unique_id);

        $docker_container
            ->setEnv([
                    'DRONE_REMOTE_URL' => $git_url,
                    'DRONE_WORKSPACE' => $workdir,
                    'DRONE_BUILD_EVENT' => 'push',
                    'DRONE_COMMIT_SHA' => $commit_id,
                    'DRONE_COMMIT_REF' => 'refs/heads/'.$branch,
                ]
            )
            ->setHostConfig(["$unique_id:$workdir"]);

        $container_id = $this->docker_container_run('plugins/git', $docker_image, $docker_container, $build_key_id);

        Log::connect()->debug('Run Container '.$commit_id);

        $this->docker_container_logs($docker_container, $container_id, $build_key_id);

        $pipeline = $yaml_obj->pipeline;

        $matrix = $yaml_obj->matrix;

        // $services = $yaml_obj->services;

        foreach ($pipeline as $setup => $array) {
            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;
            $image = $this->getImage($image, $matrix);

            Log::connect()->debug('Run Container By Image '.$image);

            if ($event) {
                if (!in_array('push', $event)) {
                    throw new Exception('Event error', $build_key_id);
                }
            }

            $content = '\n';

            for ($i = 0; $i < count($commands); ++$i) {
                $command = addslashes($commands[$i]);

                $content .= 'echo $ '.str_replace('$', '\\\\$', $command).'\n\n';

                $content .= 'echo;echo'.'\n\n';

                $content .= str_replace('$$', '$', $command).'\n\n';

                $content .= 'echo;echo'.'\n\n';
            }

            $ci_script = base64_encode(stripcslashes($content));

            $docker_container->setEnv([
                'CI_SCRIPT' => $ci_script,
            ]);

            $docker_container
                ->setHostConfig(["$unique_id:$workdir", 'tmp:/tmp'], $unique_id)
                ->setEntrypoint(['/bin/sh', '-c'])
                ->setWorkingDir($workdir);

            $cmd = ['echo $CI_SCRIPT | base64 -d | /bin/sh -e'];

            $container_id = $this->docker_container_run($image, $docker_image, $docker_container, $build_key_id, $cmd);

            Log::connect()->debug('Run Container '.$container_id);

            $this->docker_container_logs($docker_container, $container_id, $build_key_id);

            throw new Exception(CIConst::BUILD_STATUS_PASSED, (int) $build_key_id);
        }
    }

    /**
     * @param string            $image_name
     * @param Image             $docker_image
     * @param Container         $docker_container
     * @param string            $build_key_id
     * @param string|array|null $cmd
     *
     * @return string
     *
     * @throws Exception
     */
    private function docker_container_run(string $image_name,
                                          Image $docker_image,
                                          Container $docker_container,
                                          string $build_key_id,
                                          $cmd = null)
    {
        $docker_image->pull($image_name);

        try {
            $container_id = $docker_container->create($image_name, null, $cmd);
        } catch (Exception $e) {
            throw new Exception(CIConst::BUILD_STATUS_ERRORED, (int) $build_key_id);
        }

        $output = $docker_container->start($container_id);

        if ((bool) $output) {
            Log::connect()->debug('Start Container '.$container_id.' Error');

            throw new Exception(CIConst::BUILD_STATUS_ERRORED, (int) $build_key_id);
        }

        return $container_id;
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

        if ('/bin/drone-git' === json_decode($docker_container->inspect($container_id))->Path) {
            Log::connect()->debug('Drop prev logs');

            $redis->hDel('build_log', $build_key_id);
        }

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
                    $since_time = $startedAt;
                    $until_time = $startedAt;
                }

                if (!$first) {
                    $since_time = $until_time;
                    $until_time = $until_time + 1;
                }

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

                sleep(1);

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

                $redis->hset('build_log', $build_key_id, $prev_docker_log.$image_log);

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
                    throw new Exception(CIConst::BUILD_STATUS_ERRORED, (int) $build_key_id);
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

<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

use Docker\Container\Container;
use Docker\Docker;
use Docker\Image\Image;
use Exception;
use KhsCI\Support\ArrayHelper;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\DB;
use KhsCI\Support\Git;
use KhsCI\Support\HTTP;
use KhsCI\Support\Log;

class Queue
{
    /**
     * @var
     */
    private static $gitType;

    /**
     * @var
     */
    private static $build_key_id;

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $sql = <<<'EOF'
SELECT 

id,git_type,rid,commit_id,commit_message,branch 

FROM 

builds WHERE build_status=? AND event_type=? ORDER BY id DESC LIMIT 1;
EOF;

        $output = DB::select($sql, [
            CI::BUILD_STATUS_PENDING,
            CI::BUILD_EVENT_PUSH,
        ]);

        foreach ($output as $k) {
            $build_key_id = $k['id'];
            $git_type = $k['git_type'];
            $rid = $k['rid'];
            $commit_id = $k['commit_id'];
            $commit_message = $k['commit_message'];
            $branch = $k['branch'];

            self::$gitType = $git_type;

            self::$build_key_id = (int)$build_key_id;

            // commit 信息跳过构建
            self::skip($commit_message);

            // 是否启用构建
            self::getRepoBuildActivateStatus($rid);

            self::run($rid, $commit_id, $branch);
        }
    }

    /**
     * 检查是否启用了构建.
     *
     * @param string $rid
     *
     * @throws Exception
     */
    private function getRepoBuildActivateStatus(string $rid): void
    {
        $gitType = self::$gitType;

        $sql = 'SELECT build_activate FROM repo WHERE rid=? AND git_type=?';

        $build_activate = DB::select($sql, [$rid, $gitType], true);

        if (0 === $build_activate) {
            throw new Exception(CI::BUILD_STATUS_INACTIVE, (int)$build_activate);
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

        throw new Exception(CI::BUILD_STATUS_SKIP, self::$build_key_id);
    }

    /**
     * @param string $image
     * @param array  $config
     *
     * @return array|mixed|string
     */
    private function getImage(string $image, array $config)
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

                if (in_array($k, array_keys($config), true)) {
                    $var = $config["$k"];
                }

                $image = str_replace($var_secret, $var, $image);
            }
        }

        return $image;
    }

    /**
     * 执行构建.
     *
     * @param        $rid
     * @param string $commit_id
     * @param string $branch
     *
     * @throws Exception
     */
    private function run($rid, string $commit_id, string $branch): void
    {
        $gitType = self::$gitType;

        $unique_id = session_create_id();

        Log::connect()->debug('Create Volume '.$unique_id);
        Log::connect()->debug('Create Network '.$unique_id);

        $sql = 'SELECT repo_full_name FROM repo WHERE git_type=? AND rid=?';

        $repo_full_name = DB::select($sql, [$gitType, $rid], true);

        $base = $repo_full_name.'/'.$commit_id;

        $url = "https://raw.githubusercontent.com/$base/.drone.yml";

        $url = "https://ci2.khs1994.com:10000/.drone.yml";

        $yaml_obj = (object)yaml_parse(HTTP::get($url));

        $yaml_to_json = json_encode($yaml_obj);

        $sql = 'UPDATE builds SET config=? WHERE id=? ';

        DB::update($sql, [$yaml_to_json, self::$build_key_id]);

        /**
         * 解析 .drone.yml
         */
        $workspace = $yaml_obj->workspace;

        $pipeline = $yaml_obj->pipeline;

        $services = $yaml_obj->services;

        $matrix = $yaml_obj->matrix;

        $matrix = $this->parseMatrix($matrix);

        /**
         * 变量命名尽量与 docker container run 的参数保持一致.
         *
         * 项目根目录
         */

        $base_path = $workspace['base'] ?? null;

        $path = $workspace['path'] ?? $repo_full_name;

        if ('.' === $path) {
            $path = null;
        }

        /**
         * --workdir.
         */
        $workdir = $base_path.'/'.$path;

        $git_url = Git::getUrl($gitType, $repo_full_name);

        $docker = Docker::docker(Docker::createOptionArray('127.0.0.1:2375'));

        $docker_container = $docker->container;
        $docker_image = $docker->image;
        $docker_network = $docker->network;

        $docker_network->create($unique_id);

        $this->runGit(
            [
                'DRONE_REMOTE_URL' => $git_url,
                'DRONE_WORKSPACE' => $workdir,
                'DRONE_BUILD_EVENT' => 'push',
                'DRONE_COMMIT_SHA' => $commit_id,
                'DRONE_COMMIT_REF' => 'refs/heads/'.$branch,
            ], $workdir, $unique_id, $docker_container, $docker_image
        );

        foreach ($matrix as $k => $config) {

            // $this->runService($services, $unique_id, $docker);

            $this->runPipeline($pipeline, $config, $workdir, $unique_id, $docker);
        }
    }

    /**
     * @param array  $pipeline
     *
     * @param array  $config
     * @param string $work_dir
     * @param string $unique_id
     * @param Docker $docker
     *
     * @throws Exception
     */
    private function runPipeline(array $pipeline, array $config, string $work_dir, string $unique_id, Docker $docker)
    {
        foreach ($pipeline as $setup => $array) {
            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;

            $image = $this->getImage($image, $config);

            Log::connect()->debug('Run Container By Image '.$image);

            if ($event) {
                if (!in_array('push', $event, true)) {
                    throw new Exception('Event error', self::$build_key_id);
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

            $docker_container = $docker->container;

            $docker_image = $docker->image;

            $docker_container->setEnv([
                'CI_SCRIPT' => $ci_script,
            ]);

            $docker_container
                ->setHostConfig(["$unique_id:$work_dir", 'tmp:/tmp'], $unique_id)
                ->setEntrypoint(['/bin/sh', '-c'])
                ->setWorkingDir($work_dir);

            $cmd = ['echo $CI_SCRIPT | base64 -d | /bin/sh -e'];

            $container_id = $this->docker_container_run($image, $docker_image, $docker_container, $cmd);

            Log::connect()->debug('Run Container '.$container_id);

            $this->docker_container_logs($docker_container, $container_id);
        }

        throw new Exception(CI::BUILD_STATUS_PASSED, self::$build_key_id);
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

        try {
            $container_id = $docker_container->create($image_name, null, $cmd);
        } catch (Exception $e) {
            throw new Exception(CI::BUILD_STATUS_ERRORED, self::$build_key_id);
        }

        $output = $docker_container->start($container_id);

        if ((bool)$output) {
            Log::connect()->debug('Start Container '.$container_id.' Error');

            throw new Exception(CI::BUILD_STATUS_ERRORED, self::$build_key_id);
        }

        return $container_id;
    }

    /**
     * @param Container $docker_container
     * @param string    $container_id
     *
     * @return array
     *
     * @throws Exception
     */
    private function docker_container_logs(Container $docker_container, string $container_id)
    {
        $redis = Cache::connect();

        if ('/bin/drone-git' === json_decode($docker_container->inspect($container_id))->Path) {
            Log::connect()->debug('Drop prev logs');

            $redis->hDel('build_log', self::$build_key_id);
        }

        $i = -1;

        $startedAt = null;
        $finishedAt = null;

        while (1) {
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($container_id))->State;

            $status = $image_status_obj->Status;

            $startedAt = $image_status_obj->StartedAt;
            $startedAt = Date::parse($startedAt);

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

                $prev_docker_log = $redis->hget('build_log', (string)self::$build_key_id);

                $redis->hset('build_log', (string)self::$build_key_id, $prev_docker_log.$image_log);

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
                    throw new Exception(CI::BUILD_STATUS_ERRORED, self::$build_key_id);
                }

                break;
            }
        }

        return [
            'start' => $startedAt,
            'stop' => $finishedAt,
        ];
    }

    /**
     * @param array     $env
     * @param           $work_dir
     * @param           $unique_id
     * @param Container $docker_container
     * @param Image     $docker_image
     *
     * @throws Exception
     */
    private function runGit(array $env, $work_dir, $unique_id, Container $docker_container, Image $docker_image)
    {
        $docker_container
            ->setEnv($env)
            ->setHostConfig(["$unique_id:$work_dir"]);

        $container_id = $this->docker_container_run('plugins/git', $docker_image, $docker_container);

        Log::connect()->debug('Run Container '.$container_id);

        $this->docker_container_logs($docker_container, $container_id);
    }

    /**
     * @param array $matrix
     *
     * @return array
     */
    private function parseMatrix(array $matrix)
    {
        return ArrayHelper::combination($matrix);
    }

    /**
     * @param array  $service
     * @param Docker $docker
     *
     * @param string $unique_id
     *
     * @throws Exception
     */
    private function runService(array $service, string $unique_id, Docker $docker)
    {
        foreach ($service as $service_name => $array) {
            foreach ($array as $k => $v) {
                $image = $v['image'];
                $env = $v['environment'] ?? null;
                $entrypoint = $v['entrypoint'];
                $command = $v['command'];

                $docker_container = $docker->container;

                $container_id = $docker_container
                    ->setEnv($env)
                    ->setEntrypoint($entrypoint)
                    ->setHostConfig(null, $unique_id)
                    ->create($image, null, $command);

                $docker_container->start($container_id);
            }
        }
    }
}

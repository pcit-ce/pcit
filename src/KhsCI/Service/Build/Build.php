<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use App\Build as BuildDB;
use Docker\Container\Container;
use Docker\Docker;
use Docker\Image\Image;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\ArrayHelper;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\DB;
use KhsCI\Support\Git;
use KhsCI\Support\Log;

class Build
{
    private $git_type;

    private $build_key_id;

    private $unique_id;

    private $pull_id;

    private $tag_name;

    private $commit_id;

    private $commit_message;

    private $branch;

    private $event_type;

    private $config;

    private $pull_request_source;

    private $repo_full_name;

    private $git_config = [];

    private $git_image = 'plugins/git';

    private $system_env = [];

    /**
     * @param             $build_key_id
     * @param string      $git_type
     * @param             $rid
     * @param string      $commit_id
     * @param string      $commit_message
     * @param string      $branch
     * @param string      $event_type
     * @param string      $pull_request_id
     * @param string      $tag_name
     * @param null|string $config
     * @param null|string $pull_request_source
     * @param null|string $repo_full_name
     *
     * @throws CIException
     * @throws Exception
     */
    public function __invoke($build_key_id,
                             string $git_type,
                             $rid,
                             string $commit_id,
                             string $commit_message,
                             string $branch,
                             string $event_type,
                             ?string $pull_request_id = null,
                             ?string $tag_name = null,
                             ?string $config,
                             ?string $pull_request_source,
                             ?string $repo_full_name): void
    {
        try {
            $this->build_key_id = (int) $build_key_id;
            $this->config = $config;
            $this->commit_id = $commit_id;

            if ('[]' === $config) {
                throw new Exception(CI::BUILD_STATUS_PASSED);
            }

            $this->unique_id = session_create_id();
            $this->commit_message = $commit_message;
            $this->branch = $branch;
            $this->event_type = $event_type;
            $this->pull_id = $pull_request_id;
            $this->tag_name = $tag_name;
            $this->git_type = $git_type;
            $this->pull_request_source = $pull_request_source;
            $this->repo_full_name = $repo_full_name;

            Log::debug(__FILE__, __LINE__, json_encode([
                'unique_id' => $this->unique_id,
                'build_key_id' => $build_key_id,
                'event_type' => $event_type,
                'commit_id' => $commit_id,
                'pull_request_id' => $pull_request_id,
                'tag_name' => $tag_name,
                'git_type' => $git_type,
            ]));

            // 是否启用构建
            $this->getRepoBuildActivateStatus((int) $rid);

            $this->run($rid, $branch);
        } catch (\Throwable $e) {
            throw new CIException(
                $this->unique_id,
                $this->commit_id,
                $this->event_type,
                $e->getMessage(), $this->build_key_id
            );
        }
    }

    /**
     * 检查是否启用了构建.
     *
     * @param int $rid
     *
     * @throws Exception
     */
    private function getRepoBuildActivateStatus(int $rid): void
    {
        $gitType = $this->git_type;

        $sql = 'SELECT build_activate FROM repo WHERE rid=? AND git_type=?';

        $build_activate = DB::select($sql, [$rid, $gitType], true);

        if (0 === $build_activate) {
            Log::debug(__FILE__, __LINE__, $this->build_key_id.' is inactive');

            throw new Exception(CI::BUILD_STATUS_INACTIVE);
        }
    }

    /**
     * 解析 镜像名 中包含的 变量.
     *
     * @param string $image
     * @param array  $config
     *
     * @return array|mixed|string
     *
     * @throws Exception
     */
    private function parseImage(string $image, ?array $config)
    {
        Log::debug(__FILE__, __LINE__, 'Parse Image '.$image);

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
        Log::debug(__FILE__, __LINE__, 'Parse Image output is '.$image);

        return $image;
    }

    /**
     * 执行构建.
     *
     * @param        $rid
     * @param string $branch
     *
     * @throws Exception
     */
    private function run($rid, string $branch): void
    {
        $gitType = $this->git_type;
        $unique_id = $this->unique_id;
        $commit_id = $this->commit_id;
        $event_type = $this->event_type;

        Log::debug(__FILE__, __LINE__, 'Create Volume '.$unique_id);
        Log::debug(__FILE__, __LINE__, 'Create Network '.$unique_id);

        $sql = 'SELECT repo_full_name FROM repo WHERE git_type=? AND rid=?';

        $repo_full_name = DB::select($sql, [$gitType, $rid], true);

        if (!$repo_full_name) {
            throw new Exception(CI::BUILD_STATUS_ERRORED);
        }

        if (!$this->config) {
            throw new Exception(CI::BUILD_STATUS_ERRORED);
        }

        $yaml_obj = (object) json_decode($this->config, true);

        // 解析 .khsci.yml.

        $git = $yaml_obj->clone['git'] ?? null;
        $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->pipeline ?? null;
        $services = $yaml_obj->services ?? null;
        $matrix = $yaml_obj->matrix ?? null;
        $config = $yaml_obj->config ?? null;

        if ($git) {
            $depth = $git['depth'] ?? 10;
            $recursive = $git['recursive'] ?? false;
            $skip_verify = $git['skip_verify'] ?? false;
            $tags = $git['tags'] ?? false;
            $submodule_override = $git['submodule_override'] ?? null;

            $git_config = [];

            // 防止用户传入 false
            if ($depth) {
                array_push($git_config, "PLUGIN_DEPTH=$depth");
            } else {
                array_push($git_config, 'PLUGIN_DEPTH=2');
            }

            $recursive && array_push($git_config, 'PLUGIN_RECURSIVE=true');

            $skip_verify && array_push($git_config, 'PLUGIN_SKIP_VERIFY=true');

            $tags && array_push($git_config, 'PLUGIN_TAGS=true');

            $submodule_override && array_push(
                $git_config, 'PLUGIN_SUBMODULE_OVERRIDE='.json_encode($submodule_override)
            );

            $this->git_config = $git_config;
            $this->git_image = $git['image'] ?? 'plugins/git';
        }

        // 存在构建矩阵
        if ($matrix) {
            $matrix = $this->parseMatrix($yaml_obj->matrix);
        }

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

        // --workdir.
        $workdir = $base_path.'/'.$path;

        $system_env = [
            'CI=true',
            'KHSCI=true',
            'CONTINUOUS_INTEGRATION=true',

            'KHSCI_BRANCH='.$this->branch,
            'KHSCI_TAG='.$this->tag_name,
            'KHSCI_BUILD_DIR='.$workdir,
            'KHSCI_BUILD_ID='.$this->build_key_id,
            'KHSCI_COMMIT='.$this->commit_id,
            'KHSCI_COMMIT_MESSAGE='.$this->commit_message,
            'KHSCI_EVENT_TYPE='.$this->event_type,
            'KHSCI_PULL_REQUEST=false',
            'KHSCI_REPO_SLUG='.$this->repo_full_name,
        ];

        if ($this->pull_id) {
            array_merge($system_env,
                [
                    'KHSCI_PULL_REQUEST=true',
                    'KHSCI_PULL_REQUEST_BRANCH='.$this->branch,
                    'KHSCI_PULL_REQUEST_SHA='.$this->commit_id,
                    'KHSCI_PULL_REQUEST_SLUG='.$this->pull_request_source,
                ]
            );
        }

        $this->system_env = $system_env;

        Log::debug(__FILE__, __LINE__, json_encode($this->system_env));

        $docker = (new KhsCI())->docker;

        $docker_container = $docker->container;
        $docker_image = $docker->image;
        $docker_network = $docker->network;

        Log::debug(__FILE__, __LINE__, 'pull image plugins/git');

        $image_pull_output = $docker_image->pull('plugins/git');

        Log::debug(__FILE__, __LINE__, $image_pull_output);

        $docker_network->create($unique_id);

        $git_env = $this->getGitEnv($event_type, $repo_full_name, $workdir, $commit_id, $branch);
        $this->runGit($git_env, $workdir, $unique_id, $docker_container);

        // 不存在构建矩阵
        if (!$matrix) {
            $this->cancel();

            $this->runService($services, $unique_id, null, $docker);

            $this->cancel();

            $this->runPipeline($pipeline, null, $workdir, $unique_id, $docker_container, $docker_image);

            throw new Exception(CI::BUILD_STATUS_PASSED);
        }

        // 矩阵构建循环
        foreach ($matrix as $k => $config) {
            $this->cancel();

            //启动服务
            $this->runService($services, $unique_id, $config, $docker);

            $this->cancel();
            // 构建步骤
            $this->runPipeline($pipeline, $config, $workdir, $unique_id, $docker_container, $docker_image);

            // 清理
            $this->systemDelete($unique_id);
        }

        // 后续根据 throw 出的异常执行对应的操作

        throw new Exception(CI::BUILD_STATUS_PASSED);
    }

    /**
     * 检查用户是否取消了构建或重新构建.
     *
     * @throws Exception
     */
    private function cancel(): void
    {
        $output = BuildDB::getBuildStatusByBuildKeyId((int) $this->build_key_id);

        // 取消构建
        if (CI::BUILD_STATUS_CANCELED === $output) {
            throw new Exception(CI::BUILD_STATUS_CANCELED);
        }

        // 重新构建
        if (CI::BUILD_STATUS_PENDING === $output) {
            throw new Exception(CI::BUILD_STATUS_CANCELED);
        }
    }

    /**
     * @param array     $pipeline
     * @param array     $config
     * @param string    $work_dir
     * @param string    $unique_id
     * @param Container $docker_container
     * @param Image     $docker_image
     *
     * @throws Exception
     */
    private function runPipeline(array $pipeline,
                                 ?array $config,
                                 string $work_dir,
                                 string $unique_id,
                                 Container $docker_container,
                                 Image $docker_image): void
    {
        foreach ($pipeline as $setup => $array) {
            Log::debug(__FILE__, __LINE__, 'This Pipeline is '.$setup);

            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;
            $env = $array['environment'] ?? [];
            $status = $array['when']['status'] ?? null;

            if ($event) {
                if (is_string($event)) {
                    if ($this->event_type !== $event) {
                        Log::debug(
                            __FILE__,
                            __LINE__,
                            "Pipeline $event Is Not Current ".$this->event_type.'. Skip'
                        );

                        continue;
                    }
                } elseif (is_array($event) and (!in_array($this->event_type, $event, true))) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        "Pipeline Event $event not in ".implode(' | ', $event).'. skip');

                    continue;
                }
            }

            if ($status) {
                continue;
            }

            if ('ci_docker_build' === $image) {
                continue;
            }

            $image = $this->parseImage($image, $config);

            $ci_script = $this->parseCommand($setup, $image, $commands);

            $docker_container
                ->setEnv(array_merge([
                    "CI_SCRIPT=$ci_script",
                ], $env, $this->system_env))
                ->setHostConfig(["$unique_id:$work_dir", 'tmp:/tmp'], $unique_id)
                ->setEntrypoint(['/bin/sh', '-c'])
                ->setLabels(['com.khs1994.ci.pipeline' => $unique_id])
                ->setWorkingDir($work_dir);

            $cmd = ['echo $CI_SCRIPT | base64 -d | /bin/sh -e'];

            // docker.khs1994.com:1000/username/image:1.14.0

            $image_array = explode(':', $image);

            // image not include :

            $tag = null;

            if (1 !== count($image_array)) {
                $tag = $image_array[count($image_array) - 1];
            }

            $docker_image->pull($image, $tag ?? 'latest');

            $container_id = $docker_container->start($docker_container->create($image, null, $cmd));

            Log::debug(
                __FILE__,
                __LINE__,
                'Run Container By Image '.$image.', Container Id is '.$container_id
            );

            $this->docker_container_logs($docker_container, $container_id);
        }
    }

    /**
     * @param string     $setup
     * @param string     $image
     * @param array|null $commands
     *
     * @return string
     *
     * @throws Exception
     */
    private function parseCommand(string $setup, string $image, ?array $commands)
    {
        if (null === $commands) {
            return;
        }

        $content = '\n';

        $content .= 'echo;echo\n\n';

        $content .= 'echo Start Build in '.$setup.' "=>" '.$image;

        $content .= '\n\necho;echo\n\n';

        for ($i = 0; $i < count($commands); ++$i) {
            $command = addslashes($commands[$i]);

            $content .= 'echo $ '.str_replace('$', '\\\\$', $command).'\n\n';

            $content .= 'echo;echo\n\n';

            $content .= str_replace('$$', '$', $command).'\n\n';

            $content .= 'echo;echo\n\n';
        }

        $ci_script = base64_encode(stripcslashes($content));

        Log::debug(__FILE__, __LINE__, 'Command base64encode is '.$ci_script);

        return $ci_script;
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
            Log::debug(__FILE__, __LINE__, 'Drop prev logs');

            $redis->hDel('build_log', $this->build_key_id);
        }

        $i = -1;

        $startedAt = null;
        $finishedAt = null;
        $until_time = 0;

        while (1) {
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($container_id))->State;
            $status = $image_status_obj->Status;
            $startedAt = Date::parse($image_status_obj->StartedAt);

            if ('running' === $status) {
                if (0 === $i) {
                    $since_time = $startedAt;
                    $until_time = $startedAt;
                } else {
                    $since_time = $until_time;
                    $until_time = $until_time + 1;
                }

                $image_log = $docker_container->logs(
                    $container_id, false, true, true,
                    $since_time, $until_time, true
                );

                echo $image_log;

                sleep(1);

                continue;
            } else {
                $image_log = $docker_container->logs(
                    $container_id, false, true, true, 0, 0, true
                );

                $prev_docker_log = $redis->hget('build_log', (string) $this->build_key_id);

                $redis->hset(
                    'build_log',
                    (string) $this->build_key_id, $prev_docker_log.PHP_EOL.PHP_EOL.$image_log
                );

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    Log::debug(__FILE__, __LINE__, "Container $container_id ExitCode is not 0");

                    throw new Exception(CI::BUILD_STATUS_ERRORED);
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
     * @param string $event_type
     * @param string $repo_full_name
     * @param string $workdir
     * @param string $commit_id
     * @param string $branch
     *
     * @return array
     *
     * @throws Exception
     *
     * @see https://github.com/drone-plugins/drone-git
     */
    private function getGitEnv(string $event_type,
                               string $repo_full_name,
                               string $workdir,
                               string $commit_id,
                               string $branch)
    {
        $git_url = Git::getUrl($this->git_type, $repo_full_name);

        $git_env = null;

        switch ($event_type) {
            case CI::BUILD_EVENT_PUSH:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=push',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF='.'refs/heads/'.$branch,
                ], $this->git_config);

                break;
            case CI::BUILD_EVENT_PR:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=pull_request',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF=refs/pull/'.$this->pull_id.'/head',
                ], $this->git_config);

                break;
            case  CI::BUILD_EVENT_TAG:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$workdir,
                    'DRONE_BUILD_EVENT=tag',
                    'DRONE_COMMIT_SHA='.$commit_id,
                    'DRONE_COMMIT_REF=refs/tags/'.$this->tag_name,
                ], $this->git_config);

                break;
        }

        return $git_env;
    }

    /**
     * 运行 Git clone.
     *
     * @param array     $env
     * @param           $work_dir
     * @param           $unique_id
     * @param Container $docker_container
     *
     * @throws Exception
     */
    private function runGit(array $env, $work_dir, $unique_id, Container $docker_container): void
    {
        $image = $this->git_image;

        $this->git_image = 'plugins/git';

        $this->git_config = [];

        $docker_container
            ->setEnv($env)
            ->setLabels(['com.khs1994.ci.git' => $unique_id])
            ->setHostConfig(["$unique_id:$work_dir"]);

        $container_id = $docker_container->start($docker_container->create($image));

        Log::debug(
            __FILE__,
            __LINE__,
            'Run Git Clone Container By Image '.$image.', Container Id is '.$container_id
        );

        $this->docker_container_logs($docker_container, $container_id);
    }

    /**
     * 解析矩阵.
     *
     * @param array $matrix
     *
     * @return array
     */
    private function parseMatrix(array $matrix)
    {
        return ArrayHelper::combination($matrix);
    }

    /**
     * 运行服务.
     *
     * @param array  $service
     * @param string $unique_id
     * @param array  $config
     * @param Docker $docker
     *
     * @throws Exception
     */
    private function runService(?array $service, string $unique_id, ?array $config, Docker $docker): void
    {
        if (null === $service) {
            return;
        }

        foreach ($service as $service_name => $array) {
            $this->cancel();

            $image = $array['image'];
            $env = $array['environment'] ?? null;
            $entrypoint = $array['entrypoint'] ?? null;
            $command = $array['command'] ?? null;

            $image = $this->parseImage($image, $config);

            $docker_image = $docker->image;
            $docker_container = $docker->container;

            $tag = explode(':', $image)[1] ?? 'latest';

            $docker_image->pull($image, $tag);

            $container_id = $docker_container
                ->setEnv($env)
                ->setEntrypoint($entrypoint)
                ->setHostConfig(null, $unique_id)
                ->setLabels(['com.khs1994.ci.service' => $unique_id])
                ->create($image, $service_name, $command);

            $docker_container->start($container_id);

            Log::debug(
                __FILE__,
                __LINE__,
                "Run $service_name By Image $image, Container Id Is $container_id"
            );
        }
    }

    /**
     * Remove all Docker Resource.
     *
     * @param string $unique_id
     * @param bool   $last
     *
     * @throws Exception
     */
    public function systemDelete(?string $unique_id, bool $last = false): void
    {
        if (null === $unique_id) {
            return;
        }

        $label = 'com.khs1994.ci.service';

        $docker = (new KhsCI())->docker;

        $docker_container = $docker->container;

        // $docker_image = $docker->image;

        // clean container

        self::deleteContainerByLabel($docker_container, $label);

        if ('1' === $unique_id) {
            // 只清理服务，退出

            return;
        }

        // don't clean image

        // 全部构建任务结束之后才删除 volume、网络

        if ($last) {
            $docker_network = $docker->network;

            $docker_volume = $docker->volume;

            // clean all container

            self::deleteContainerByLabel($docker_container, 'com.khs1994.ci.git');

            self::deleteContainerByLabel($docker_container, 'com.khs1994.ci.pipeline');

            // clean volume

            $docker_volume->remove($unique_id);

            Log::connect()->debug('Build Stoped Delete Volume '.$unique_id);

            // clean network

            $docker_network->remove($unique_id);

            Log::connect()->debug('Build Stoped Delete Network '.$unique_id);
        }
    }

    /**
     * @param Container $container
     * @param string    $label
     *
     * @throws Exception
     */
    private function deleteContainerByLabel(Container $container, string $label): void
    {
        $output = $container->list(true, null, false, [
            'label' => $label,
        ]);

        foreach (json_decode($output) as $k) {
            $id = $k->Id;

            if (!$id) {
                continue;
            }

            Log::connect()->debug('Delete Container '.$id);

            $container->delete($id, true, true);
        }
    }

    /**
     * @param string|array $pattern
     * @param string       $subject
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function check($pattern, string $subject)
    {
        if (is_string($pattern)) {
            return self::checkString($pattern, $subject);
        }

        if (is_array($pattern)) {
            foreach ($pattern as $k) {
                if (self::checkString($k, $subject)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return bool
     */
    private static function checkString(string $pattern, string $subject)
    {
        if (preg_match('#'.$pattern.'#', $subject)) {
            return true;
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use App\Build as BuildDB;
use Docker\Container\Container;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\Log;

class Client
{
    private $git_type;

    private $build_key_id;

    private $unique_id;

    public $pull_id;

    public $tag_name;

    private $commit_id;

    private $commit_message;

    private $branch;

    private $event_type;

    private $config;

    private $pull_request_source;

    private $rid;

    private $repo_full_name;

    private $system_env = [];

    /**
     * @param int         $build_key_id
     * @param string      $git_type
     * @param int         $rid
     * @param string      $commit_id
     * @param string      $commit_message
     * @param string      $branch
     * @param string      $event_type
     * @param string      $pull_request_id
     * @param string      $tag_name
     * @param null|string $config
     * @param null|string $pull_request_source
     * @param null|string $repo_full_name
     * @param array       $env_vars
     *
     * @throws CIException
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
                             ?string $repo_full_name,
                             array $env_vars = []): void
    {
        try {
            $this->build_key_id = (int) $build_key_id;
            $this->config = $config;
            $this->commit_id = $commit_id;

            // config 不存在，。khsci.yml 文件不存在
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
            $this->rid = $rid;
            $this->repo_full_name = $repo_full_name;
            $this->system_env = array_merge($this->system_env, $env_vars);

            Log::debug(__FILE__, __LINE__, json_encode([
                'unique_id' => $this->unique_id,
                'build_key_id' => $build_key_id,
                'event_type' => $event_type,
                'commit_id' => $commit_id,
                'pull_request_id' => $pull_request_id,
                'tag_name' => $tag_name,
                'git_type' => $git_type, [], Log::EMERGENCY,
            ]));

            $this->run();
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
     * 解析 镜像名 中包含的 变量.
     *
     * @param string $image
     * @param array  $config
     *
     * @return array|mixed|string
     *
     * @throws Exception
     */
    public function parseImage(string $image, ?array $config)
    {
        Log::debug(__FILE__, __LINE__, 'Parse Image '.$image, [], Log::EMERGENCY);

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
        Log::debug(__FILE__, __LINE__, 'Parse Image output is '.$image, [], Log::EMERGENCY);

        return $image;
    }

    /**
     * 执行构建.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $branch = $this->branch;
        $unique_id = $this->unique_id;
        $commit_id = $this->commit_id;
        $event_type = $this->event_type;

        Log::debug(__FILE__, __LINE__, 'Create Volume '.$unique_id, [], Log::EMERGENCY);
        Log::debug(__FILE__, __LINE__, 'Create Network '.$unique_id, [], Log::EMERGENCY);

        if (!$this->repo_full_name) {
            throw new Exception(CI::BUILD_STATUS_ERRORED);
        }

        if (!$this->config) {
            throw new Exception(CI::BUILD_STATUS_ERRORED);
        }

        $yaml_obj = (object) json_decode($this->config, true);

        // 解析 .khsci.yml.

        $git = $yaml_obj->clone['git'] ?? null;
        // $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->pipeline ?? null;
        $services = $yaml_obj->services ?? null;
        $matrix = $yaml_obj->matrix ?? null;
        // $config = $yaml_obj->config ?? null;

        // 存在构建矩阵
        if ($matrix) {
            $matrix = MatrixClient::parseMatrix($yaml_obj->matrix);
        }

        /**
         * 变量命名尽量与 docker container run 的参数保持一致.
         *
         * 项目根目录
         */
        $base_path = $workspace['base'] ?? null;

        $path = $workspace['path'] ?? $this->repo_full_name;

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

        $this->system_env = array_merge($system_env, $this->system_env);

        Log::debug(__FILE__, __LINE__, json_encode($this->system_env), [], Log::EMERGENCY);

        $docker = (new KhsCI())->docker;

        $docker_container = $docker->container;
        $docker_image = $docker->image;
        $docker_network = $docker->network;

        Log::debug(__FILE__, __LINE__, 'pull image plugins/git', [], Log::EMERGENCY);

        $image_pull_output = $docker_image->pull('plugins/git');

        Log::debug(__FILE__, __LINE__, $image_pull_output, [], Log::EMERGENCY);

        $docker_network->create($unique_id);

        // run git
        GitClient::runGit($git,
            $this->git_type,
            $event_type,
            $this->repo_full_name,
            $workdir,
            $commit_id,
            $branch,
            $unique_id,
            $docker_container,
            $this->build_key_id
        );

        // 不存在构建矩阵
        if (!$matrix) {
            $this->cancel();

            ServicesClient::runService($services, $unique_id, null, $docker);

            $this->cancel();

            PipelineClient::runPipeline($pipeline,
                null,
                $this->event_type,
                $this->system_env,
                $workdir,
                $unique_id,
                $docker_container,
                $docker_image,
                $this->build_key_id
            );

            throw new Exception(CI::BUILD_STATUS_PASSED);
        }

        // 矩阵构建循环
        foreach ($matrix as $k => $config) {
            $this->cancel();

            //启动服务
            ServicesClient::runService($services, $unique_id, $config, $docker);

            $this->cancel();

            // 构建步骤
            PipelineClient::runPipeline($pipeline,
                $config,
                $this->event_type,
                $this->system_env,
                $workdir,
                $unique_id,
                $docker_container,
                $docker_image,
                $this->build_key_id
            );

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
    public function cancel(): void
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
     * @param string     $setup
     * @param string     $image
     * @param array|null $commands
     *
     * @return string
     *
     * @throws Exception
     */
    public function parseCommand(string $setup, string $image, ?array $commands)
    {
        if (null === $commands) {
            return null;
        }

        $content = '\n';

        $content .= 'echo;echo\n\n';

        $content .= 'echo Start Build in '.$setup.' "=>" '.$image;

        $content .= '\n\necho;echo\n\n';

        for ($i = 0; $i < count($commands); ++$i) {
            $command = addslashes($commands[$i]);

            $content .= 'echo "$ '.str_replace('$', '\\\\$', $command).'"\n\n';

            $content .= 'echo;echo\n\n';

            $content .= str_replace('$$', '$', $command).'\n\n';

            $content .= 'echo;echo\n\n';
        }

        $ci_script = base64_encode(stripcslashes($content));

        Log::debug(__FILE__, __LINE__, 'Command base64encode is '.$ci_script, [], Log::EMERGENCY);

        return $ci_script;
    }

    /**
     * @param int       $build_key_id
     * @param Container $docker_container
     * @param string    $container_id
     *
     * @return array
     *
     * @throws Exception
     */
    public function docker_container_logs(int $build_key_id, Container $docker_container, string $container_id)
    {
        $redis = Cache::connect();

        if ('/bin/drone-git' === json_decode($docker_container->inspect($container_id))->Path) {
            Log::debug(__FILE__,
                __LINE__,
                'Drop prev logs '.$build_key_id,
                [],
                Log::EMERGENCY
            );

            $redis->hDel('build_log', $build_key_id);
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

                $prev_docker_log = $redis->hget('build_log', (string) $build_key_id);

                $redis->hset(
                    'build_log',
                    (string) $build_key_id, $prev_docker_log.PHP_EOL.PHP_EOL.$image_log
                );

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    Log::debug(__FILE__, __LINE__, "Container $container_id ExitCode is $exitCode, not 0", [], Log::ERROR);

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

            Log::connect()->emergency('Build Stoped Delete Volume '.$unique_id);

            // clean network

            $docker_network->remove($unique_id);

            Log::connect()->emergency('Build Stoped Delete Network '.$unique_id);
        }
    }

    /**
     * @param Container $container
     * @param string    $label
     *
     * @throws Exception
     */
    public function deleteContainerByLabel(Container $container, string $label): void
    {
        $output = $container->list(true, null, false, [
            'label' => $label,
        ]);

        foreach (json_decode($output) as $k) {
            $id = $k->Id;

            if (!$id) {
                continue;
            }

            Log::connect()->emergency('Delete Container '.$id);

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
    public static function checkString(string $pattern, string $subject)
    {
        if (preg_match('#'.$pattern.'#', $subject)) {
            return true;
        }

        return false;
    }
}

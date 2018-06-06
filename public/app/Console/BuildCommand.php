<?php

declare(strict_types=1);

namespace App\Console;

use App\Build as BuildDB;
use App\GetAccessToken;
use App\Repo;
use App\User;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;

/**
 * @method setUniqueId($name = 'unique_id', $value)
 * @method setBuildKeyId($name = 'build_key_id', $value)
 *
 */
class BuildCommand
{
    private $commit_id;

    private $commit_message;

    private $unique_id;

    private $event_type;

    private $build_key_id;

    private $pull_request_id;

    private $rid;

    private $git_type;

    private $config;

    private $build_status;

    private $description;

    /**
     * @var KhsCI
     */
    private $khsci;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @throws Exception
     */
    public function build(): void
    {
        $this->khsci = new KhsCI();

        $queue = $this->khsci->build;

        try {
            $sql = <<<'EOF'
SELECT

id,git_type,rid,commit_id,commit_message,branch,event_type,pull_request_id,tag_name,config,check_run_id,pull_request_source

FROM

builds WHERE build_status=? AND event_type IN (?,?,?) ORDER BY id DESC;
EOF;

            $output = DB::select($sql, [
                CI::BUILD_STATUS_PENDING,
                CI::BUILD_EVENT_PUSH,
                CI::BUILD_EVENT_TAG,
                CI::BUILD_EVENT_PR,
            ]);

            $output = $output[0] ?? null;

            // 数据库没有结果，跳过构建，也就没有 build_key_id

            if (!$output) {

                return;
            }

            $output = array_values($output);

            $this->build_key_id = (int) $output[0];
            $this->pull_request_id = (int) $output[7];
            $this->rid = (int) $output[2];
            $this->commit_message = $output[4];
            $config = $output[9];
            $commit_id = $output[3];

            if ('[]' === $config) {
                throw new CIException(
                    null,
                    $commit_id,
                    null,
                    CI::BUILD_STATUS_PASSED,
                    $this->build_key_id
                );
            }

            $continue = true;

            $commit_id = '';
            $event_type = '';

            $ci_root = Env::get('CI_ROOT');

            Log::connect()->debug('====== '.$this->build_key_id.' Build Start Success ======');

            while ($ci_root) {
                $continue = false;

                Log::debug(__FILE__, __LINE__, 'KhsCI already set ci root');

                $git_type = $output[1];
                $rid = $output[2];
                $commit_id = $output[3];
                $event_type = $output[6];

                $admin = Repo::getAdmin($git_type, (int) $rid);
                $admin_array = json_decode($admin, true);

                $ci_root_array = json_decode($ci_root, true);
                $root = $ci_root_array[$git_type];

                foreach ($root as $k) {
                    $uid = User::getUid($git_type, $k);

                    if (in_array($uid, $admin_array)) {
                        $continue = true;

                        break;
                    }
                }

                break;
            }

            if (!$continue) {
                Log::debug(__FILE__, __LINE__, 'This repo is not ci root\'s repo');

                throw new CIException(
                    null,
                    $commit_id,
                    $event_type,
                    CI::BUILD_STATUS_PASSED,
                    $this->build_key_id
                );
            }

            BuildDB::updateStartAt($this->build_key_id);
            BuildDB::updateBuildStatus($this->build_key_id, CI::BUILD_STATUS_IN_PROGRESS);

            unset($output[10]);

            $this->config = JSON::beautiful($output[9]);

            if ('github_app' === $output[1]) {
                Up::updateGitHubAppChecks($this->build_key_id, null,
                    CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS,
                    time(),
                    null,
                    null,
                    null,
                    null,
                    $this->khsci->check_md->in_progress('PHP', PHP_OS, $this->config)
                );
            }

            $repo_full_name = Repo::getRepoFullName($output[1], (int) $output[2]);

            array_push($output, $repo_full_name);

            // 清理构建环境
            (new KhsCI())->build->systemDelete('1');

            $queue(...$output);
        } catch (CIException $e) {

            // 没有 build_key_id，即数据库没有待构项目，跳过
            if (!$this->build_key_id) {

                return;
            }

            $this->commit_id = $e->getCommitId();
            $this->unique_id = $e->getUniqueId();
            $this->event_type = $e->getEventType();
            $this->build_key_id = $e->getCode();
            $this->git_type = BuildDB::getGitType($this->build_key_id);

            // $e->getCode() is build key id.
            BuildDB::updateStopAt($this->build_key_id);

            self::saveLog();

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_INACTIVE:
                    $this->build_status = CI::BUILD_STATUS_INACTIVE;
                    self::setBuildStatusInactive();

                    break;
                case CI::BUILD_STATUS_FAILED:
                    $this->build_status = CI::BUILD_STATUS_FAILED;
                    self::setBuildStatusFailed();

                    break;
                case CI::BUILD_STATUS_PASSED:
                    $this->build_status = CI::BUILD_STATUS_PASSED;
                    self::setBuildStatusPassed();

                    break;
                default:
                    $this->build_status = CI::BUILD_STATUS_ERRORED;
                    self::setBuildStatusErrored();
            }

            Log::debug(__FILE__, __LINE__, $e->__toString());
        } catch (\Throwable  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString());
        } finally {

            Up::runWebhooks();

            if ($this->build_key_id && $this->build_status) {
                BuildDB::updateBuildStatus($this->build_key_id, $this->build_status);
            }

            if (Env::get('CI_WECHAT_TEMPLATE_ID', false) && $this->description) {
                self::weChatTemplate($this->description);
            }

            // 若 unique_id 不存在，则不清理 Docker 构建环境
            if ($this->unique_id) {
                $queue->systemDelete($this->unique_id, true);
            }

            if (!$this->unique_id) {

                return;
            }

            $this->autoMerge();

            Log::connect()->debug('====== '.$this->build_key_id.' Build Stopped Success ======');

            Cache::connect()->set('khsci_up_status', 0);
        }
    }

    /**
     * @throws Exception
     */
    public function autoMerge()
    {
        Log::debug(__FILE__, __LINE__, 'check auto merge');

        $build_status = $this->build_status;

        $auto_merge_method = BuildDB::isAutoMerge(
            $this->git_type,
            (int) $this->rid,
            $this->commit_id,
            $this->pull_request_id
        );

        if (($build_status === CI::BUILD_STATUS_PASSED) && $auto_merge_method) {

            Log::debug(__FILE__, __LINE__, 'already set auto merge');

            $repo_array = explode('/', Repo::getRepoFullName($this->git_type, $this->rid));

            $khsci = new KhsCI([$this->git_type.'_access_token' => GetAccessToken::getGitHubAppAccessToken($this->rid)]);

            try {
                if ($khsci->github_pull_request->isMerged($repo_array[0], $repo_array[1], $this->pull_request_id)) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        'already merged, skip'
                    );
                    return;
                }

                $commit_message = null;

                $khsci->github_pull_request
                    ->merge(
                        $repo_array[0],
                        $repo_array[1],
                        $this->pull_request_id,
                        $this->commit_message,
                        $commit_message,
                        $this->commit_id,
                        (int) $auto_merge_method
                    );
                Log::debug(__FILE__, __LINE__, 'auto merge success, method is '.$auto_merge_method);
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__, $e->__toString());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function saveLog(): void
    {
        // 日志美化
        $output = Cache::connect()->hGet('build_log', (string) $this->build_key_id);

        if (!$output) {
            Log::debug(__FILE__, __LINE__, 'Build Log empty, skip');

            return;
        }

        if (!$this->unique_id) {
            Log::debug(__FILE__, __LINE__, 'config not found, skip');
            return;
        }

        $folder_name = sys_get_temp_dir().'/.khsci';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.$this->unique_id, "$output");

        $fh = fopen($folder_name.'/'.$this->unique_id, 'r');

        Cache::connect()->del((string) $this->unique_id);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            Cache::connect()->append((string) $this->unique_id, $one_line_content);
        }

        fclose($fh);

        $log_content = Cache::connect()->get((string) $this->unique_id);

        BuildDB::updateLog($this->build_key_id, $log_content);

        // cleanup
        unlink($folder_name.'/'.$this->unique_id);

        Cache::connect()->del((string) $this->unique_id);
    }

    /**
     * @throws Exception
     */
    private function setBuildStatusInactive(): void
    {
        $this->description = 'This Repo is Inactive';

        if ('github' === $this->git_type) {
            Up::updateGitHubStatus(
                $this->build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                $this->description
            );
        }

        if ('github_app' === $this->git_type) {
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt($this->build_key_id),
                (int) BuildDB::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED,
                null,
                null,
                $this->khsci->check_md->cancelled('PHP', PHP_OS, $this->config, null),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private function setBuildStatusErrored(): void
    {
        $this->description = 'The '.Env::get('CI_NAME').' build could not complete due to an error';

        // 通知 GitHub commit Status
        if ('github' === $this->git_type) {
            Up::updateGitHubStatus(
                $this->build_key_id,
                CI::GITHUB_STATUS_ERROR,
                $this->description
            );
        }

        // GitHub App checks API
        if ('github_app' === $this->git_type) {
            $build_log = BuildDB::getLog((int) $this->build_key_id);

            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt($this->build_key_id),
                (int) BuildDB::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                $this->khsci->check_md->failure('PHP', PHP_OS, $this->config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private function setBuildStatusFailed(): void
    {
        $this->description = 'The '.Env::get('CI_NAME').' build is failed';

        if ('github' === $this->git_type) {
            Up::updateGitHubStatus(
                $this->build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                $this->description
            );
        }

        if ('github_app' === $this->git_type) {
            $build_log = BuildDB::getLog((int) $this->build_key_id);
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt($this->build_key_id),
                (int) BuildDB::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                $this->khsci->check_md->failure('PHP', PHP_OS, $this->config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private function setBuildStatusPassed(): void
    {
        $this->description = 'The '.Env::get('CI_NAME').' build passed';

        if ('github' === $this->git_type) {
            Up::updateGitHubStatus(
                $this->build_key_id,
                CI::GITHUB_STATUS_SUCCESS,
                $this->description
            );
        }

        if ('github_app' === $this->git_type) {
            $build_log = BuildDB::getLog((int) $this->build_key_id);
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt($this->build_key_id),
                (int) BuildDB::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                null,
                null,
                $this->khsci->check_md->success('PHP', PHP_OS, $this->config, $build_log),
                null,
                null
            );

            return;
        }
    }

    /**
     * @param string $info
     *
     * @throws Exception
     */
    private function weChatTemplate(string $info): void
    {
        WeChatTemplate::send($this->build_key_id, $info);
    }

    public function test()
    {
        return 1;
    }
}

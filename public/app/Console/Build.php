<?php

declare(strict_types=1);

namespace App\Console;

use App\Build as BuildDB;
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

class Build
{
    private static $commit_id;

    private static $unique_id;

    private static $event_type;

    private static $build_key_id;

    private static $git_type;

    private static $config;

    private static $build_status;

    private static $description;

    /**
     * @param mixed $unique_id
     */
    public static function setUniqueId($unique_id): void
    {
        self::$unique_id = $unique_id;
    }

    /**
     * @param mixed $build_key_id
     */
    public static function setBuildKeyId($build_key_id): void
    {
        self::$build_key_id = $build_key_id;
    }

    /**
     * @var KhsCI
     */
    private static $khsci;

    /**
     * @throws Exception
     */
    public function build(): void
    {
        self::$khsci = new KhsCI();

        $queue = self::$khsci->build;

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

            // 数据库没有结果，跳过构建

            if (!$output) {
                return;
            }

            $output = array_values($output);

            self::$build_key_id = (int) $output[0];

            $continue = true;

            $commit_id = '';
            $event_type = '';

            $ci_root = Env::get('CI_ROOT');

            Log::connect()->debug('====== '.self::$build_key_id.' Build Start Success ======');

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
                    self::$build_key_id
                );
            }

            BuildDB::updateStartAt(self::$build_key_id);
            BuildDB::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_IN_PROGRESS);

            unset($output[10]);

            self::$config = JSON::beautiful($output[9]);

            if ('github_app' === $output[1]) {
                Up::updateGitHubAppChecks(self::$build_key_id, null,
                    CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS,
                    time(),
                    null,
                    null,
                    null,
                    null,
                    self::$khsci->check_md->in_progress('PHP', PHP_OS, self::$config)
                );
            }

            $repo_full_name = Repo::getRepoFullName($output[1], (int) $output[2]);

            array_push($output, $repo_full_name);

            $queue(...$output);
        } catch (CIException $e) {
            self::$commit_id = $e->getCommitId();
            self::$unique_id = $e->getUniqueId();
            self::$event_type = $e->getEventType();
            self::$build_key_id = $e->getCode();
            self::$git_type = BuildDB::getGitType(self::$build_key_id);

            // $e->getCode() is build key id.
            BuildDB::updateStopAt(self::$build_key_id);

            self::saveLog();

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_INACTIVE:
                    self::$build_status = CI::BUILD_STATUS_INACTIVE;
                    self::setBuildStatusInactive();

                    break;
                case CI::BUILD_STATUS_FAILED:
                    self::$build_status = CI::BUILD_STATUS_FAILED;
                    self::setBuildStatusFailed();

                    break;
                case CI::BUILD_STATUS_PASSED:
                    self::$build_status = CI::BUILD_STATUS_PASSED;
                    self::setBuildStatusPassed();

                    break;
                default:
                    self::$build_status = CI::BUILD_STATUS_ERRORED;
                    self::setBuildStatusErrored();
            }

            Log::debug(__FILE__, __LINE__, $e->__toString());
        } catch (\Throwable  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString());
        } finally {
            // 若 unique_id 不存在，则不清理 Docker 构建环境
            if (!self::$unique_id) {
                return;
            }

            BuildDB::updateBuildStatus(self::$build_key_id, self::$build_status);

            self::weChatTemplate(self::$description);

            $queue::systemDelete(self::$unique_id, true);

            Cache::connect()->set('khsci_up_status', 0);

            Log::connect()->debug('======'.self::$build_key_id.' Build Stopped Success ======');

            self::$unique_id = null;
        }
    }

    /**
     * @throws Exception
     */
    public static function saveLog(): void
    {
        // 日志美化
        $output = Cache::connect()->hGet('build_log', (string) self::$build_key_id);

        if (!$output) {
            Log::debug(__FILE__, __LINE__, 'Build Log empty, skip');

            return;
        }

        $folder_name = sys_get_temp_dir().'/.khsci';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.self::$unique_id, "$output");

        $fh = fopen($folder_name.'/'.self::$unique_id, 'r');

        Cache::connect()->del((string) self::$unique_id);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            Cache::connect()->append((string) self::$unique_id, $one_line_content);
        }

        fclose($fh);

        $log_content = Cache::connect()->get((string) self::$unique_id);

        BuildDB::updateLog(self::$build_key_id, $log_content);

        // cleanup
        unlink($folder_name.'/'.self::$unique_id);

        Cache::connect()->del((string) self::$unique_id);
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusInactive(): void
    {
        self::$description = 'This Repo is Inactive';

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                self::$description
            );
        }

        if ('github_app' === self::$git_type) {
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt(self::$build_key_id),
                (int) BuildDB::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED,
                null,
                null,
                self::$khsci->check_md->cancelled('PHP', PHP_OS, self::$config, null),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusErrored(): void
    {
        self::$description = 'The '.Env::get('CI_NAME').' build could not complete due to an error';

        // 通知 GitHub commit Status
        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_ERROR,
                self::$description
            );
        }

        // GitHub App checks API
        if ('github_app' === self::$git_type) {
            $build_log = BuildDB::getLog((int) self::$build_key_id);

            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt(self::$build_key_id),
                (int) BuildDB::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                self::$khsci->check_md->failure('PHP', PHP_OS, self::$config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusFailed(): void
    {
        self::$description = 'The '.Env::get('CI_NAME').' build is failed';

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                self::$description
            );
        }

        if ('github_app' === self::$git_type) {
            $build_log = BuildDB::getLog((int) self::$build_key_id);
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt(self::$build_key_id),
                (int) BuildDB::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                self::$khsci->check_md->failure('PHP', PHP_OS, self::$config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusPassed(): void
    {
        self::$description = 'The '.Env::get('CI_NAME').' build passed';

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_SUCCESS,
                self::$description
            );
        }

        if ('github_app' === self::$git_type) {
            $build_log = BuildDB::getLog((int) self::$build_key_id);
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) BuildDB::getStartAt(self::$build_key_id),
                (int) BuildDB::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                null,
                null,
                self::$khsci->check_md->success('PHP', PHP_OS, self::$config, $build_log),
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
    private static function weChatTemplate(string $info): void
    {
        WeChatTemplate::send(self::$build_key_id, $info);
    }

    public function test()
    {
        return 1;
    }
}

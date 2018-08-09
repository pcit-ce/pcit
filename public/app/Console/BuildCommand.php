<?php

declare(strict_types=1);

namespace App\Console;

use App\Build;
use App\GetAccessToken;
use App\Job;
use App\Mail\Mail;
use App\Notifications\WeChatTemplate;
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

class BuildCommand
{
    private $commit_id;

    private $commit_message;

    private $unique_id;

    private $event_type;

    private $build_key_id;

    private $pull_request_number;

    private $tag;

    private $rid;

    private $repo_full_name;

    private $git_type;

    private $config;

    private $build_status;

    private $description;

    private $branch;

    // repo config

    private $build_pushes;

    private $build_pull_requests;

    private $maximum_number_of_builds;

    private $auto_cancel_branch_builds;

    private $auto_cancel_pull_request_builds;

    /**
     * @var KhsCI
     */
    private $khsci;

    public function __set($name, $value): void
    {
        $this->$name = $value;
    }

    /**
     * @throws Exception
     */
    public function build(): void
    {
        $this->khsci = new KhsCI();

        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        $this->khsci->docker->system->ping(1);

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        try {
            // get build info
            $output = array_values($this->getBuildDB());

            $build = $this->khsci->build;
            $build_cleanup = $this->khsci->build_cleanup;

            // check ci root
            $this->checkCIRoot();

            // update build status in progress
            $this->setStatusInProgress();

            // push repo full name
            array_push($output, $this->repo_full_name = Repo::getRepoFullName($this->git_type, (int) $this->rid));

            // push env and unique_id
            array_push($output, $this->getEnv());

            // clear build environment
            $build_cleanup->systemDelete(null, false, true);

            // exec build
            $build(...$output);
        } catch (CIException $e) {
            if (01404 === $e->getCode()) {
                // 数据库不存在项目，跳出
                $this->build_key_id = 01404;

                return;
            }

            // $e->getCode() is build key id.
            Build::updateStopAt($this->build_key_id);

            // save build log
            $this->saveLog();

            Log::debug(__FILE__, __LINE__, $e->__toString(), [], Log::INFO);

            // update build status
            $this->updateBuildStatus($e->getMessage());
        } catch (\Throwable  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString(), [], Log::ERROR);

            $this->updateBuildStatus(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            // 出现其他错误
        } finally {
            Up::runWebhooks();

            if (01404 === $this->build_key_id) {
                // 数据库不存在项目，跳出
                Log::debug(__FILE__, __LINE__, 'Docker Build stop by BuildDB empty');

                return;
            }

            $this->build_key_id && $this->build_status && Build::updateBuildStatus($this->build_key_id, $this->build_status);

            $build_cleanup->systemDelete($this->unique_id, true);

            // wechat
            Env::get('CI_WECHAT_TEMPLATE_ID', false) && $this->description &&
            $this->weChatTemplate($this->description);

            // mail pr skip
            CI::BUILD_EVENT_PR !== $this->event_type && $this->sendEMail();

            // check pr auto merge
            // $this->autoMerge();

            Log::connect()->emergency('====== '.$this->build_key_id.' Build Stopped Success ======');

            Cache::connect()->set('khsci_up_status', 0);
        }
    }

    /**
     * @param string $build_stats
     *
     * @throws Exception
     */
    private function updateBuildStatus(string $build_stats): void
    {
        switch ($build_stats) {
            case 'inactive':
                $this->build_status = 'inactive';
                $this->setBuildStatusInactive();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE;
                $this->setBuildStatusFailed();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS;
                $this->setBuildStatusPassed();

                break;
            default:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED;
                $this->setBuildStatusErrored();
        }
    }

    /**
     * get user set build env.
     *
     * @return array
     *
     * @throws Exception
     */
    private function getEnv()
    {
        $env = [];

        $env_array = \App\Env::list($this->git_type, $this->rid);

        foreach ($env_array as $k) {
            $name = $k['name'];
            $value = $k['value'];

            $env[] = $name.'='.$value;
        }

        return $env;
    }

    /**
     * @throws Exception
     */
    private function getRepoConfig(): void
    {
        $array = Repo::getConfig($this->git_type, $this->rid);

        $this->build_pushes = $array['build_pushes'];
        $this->build_pull_requests = $array['build_pull_requests'];
        $this->maximum_number_of_builds = $array['maximum_number_of_builds'];
        $this->auto_cancel_branch_builds = $array['auto_cancel_branch_builds'];
        $this->auto_cancel_pull_request_builds = $array['auto_cancel_pull_request_builds'];
    }

    /**
     * PR 构建不通知.
     *
     * 邮件通知，谁 commit 通知谁
     *
     * @throws Exception
     */
    private function sendEMail(): void
    {
        if (!Env::get('CI_EMAIL_PASSWORD', false)) {
            Log::debug(__FILE__, __LINE__, 'mail settings not found, send Mail skip', [], Log::INFO);

            return;
        }

        $committer_email = Build::getCommitterEmail((int) $this->build_key_id);
        $committer_name = Build::getCommitterName((int) $this->build_key_id);

        $repo_full_name = Repo::getRepoFullName($this->git_type, (int) $this->rid);
        $build_status_changed = Build::buildStatusIsChanged((int) $this->rid, $this->branch);

        $email_list = [];
        $on_success = null;
        $on_failure = null;

        $config = (object) json_decode($this->config, true);

        $email = $config->notifications['email'] ?? null;
        if ($email) {
            // email 指令存在
            $recipients = $email['recipients'] ?? null;

            if ($recipients) {
                // recipients 指令存在
                $on_success = $email['on_success'] ?? null;
                $on_failure = $email['on_failure'] ?? null;
                is_array($recipients) && $email_list = $recipients;
                is_string($recipients) && $email_list = [$recipients];
            } else {
                // email 指令只包含 email 列表
                $email_list = $email;
            }
        }

        if (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $this->build_status) {
            // 构建成功
            if ('never' === $on_success) {
                return;
            }
        } else {
            // 构建失败
            if ('never' === $on_failure) {
                return;
            }
        }

        if (!(null === $on_success && null === $on_failure && $build_status_changed)) {
            return;
        }

        $subject = ucfirst($this->build_status).' : '.$repo_full_name.'#'.
            $this->build_key_id.' ('.$this->branch.'-'.substr($this->commit_id, 0, 7).')';

        $body = ''.$subject;

        $address = $email_list;

        $address = array_merge($address, [$committer_email => $committer_name]);

        Mail::send(array_unique($address), $subject, $body);
    }

    /**
     * @throws Exception
     */
    private function getBuildDB()
    {
        $sql = <<<'EOF'
SELECT

id,git_type,rid,commit_id,commit_message,branch,event_type,
pull_request_number,tag,config

FROM

builds WHERE 1=(SELECT build_activate FROM repo WHERE repo.rid=builds.rid AND repo.git_type=builds.git_type LIMIT 1) 

AND build_status=? AND event_type IN (?,?,?) AND config !='[]' ORDER BY id DESC LIMIT 1;
EOF;

        $output = DB::select($sql, [
            'pending',
            CI::BUILD_EVENT_PUSH,
            CI::BUILD_EVENT_TAG,
            CI::BUILD_EVENT_PR,
        ]);

        $output = $output[0] ?? null;

        // 数据库没有结果，跳过构建，也就没有 build_key_id

        if (!$output) {
            throw new CIException('Build not Found, skip', 01404);
        }

        $output = array_values($output);

        list($build_key_id,
            $this->git_type,
            $rid,
            $this->commit_id,
            $this->commit_message,
            $this->branch,
            $this->event_type,
            $pull_request_number,
            $this->tag,
            $this->config) = $output;

        if (!$this->config) {
            throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
        }

        $this->build_key_id = (int) $build_key_id;
        $this->rid = (int) $rid;
        $this->pull_request_number = (int) $pull_request_number;

        $this->unique_id = session_create_id();

        $this->getRepoConfig();

        if (!$this->build_pull_requests and CI::BUILD_EVENT_PR === $this->event_type) {
            // don't build pr
        }

        $this->config = JSON::beautiful($this->config);

        Log::connect()->emergency('====== '.$this->build_key_id.' Build Start Success ======');

        return $output;
    }

    /**
     * @throws Exception
     */
    private function checkCIRoot(): void
    {
        $ci_root = Env::get('CI_ROOT');

        while ($ci_root) {
            Log::debug(__FILE__, __LINE__, 'KhsCI already set ci root', [], Log::INFO);

            $admin = Repo::getAdmin($this->git_type, (int) $this->rid);
            $admin_array = json_decode($admin, true);

            $ci_root_array = json_decode($ci_root, true);
            $root = $ci_root_array[$this->git_type];

            foreach ($root as $k) {
                $uid = User::getUid($this->git_type, $k);

                if (in_array($uid, $admin_array)) {
                    Log::debug(__FILE__, __LINE__, 'This repo is ci root\'s repo, building...', [], Log::INFO);

                    return;
                }
            }

            Log::debug(__FILE__, __LINE__, 'This repo is not ci root\'s repo, skip', [], Log::WARNING);

            throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS, $this->build_key_id);
        }
    }

    /**
     * @throws Exception
     */
    public function autoMerge(): void
    {
        Log::debug(__FILE__, __LINE__, 'check auto merge', [], Log::INFO);

        $build_status = $this->build_status;

        $khsci = new KhsCI([$this->git_type.'_access_token' => GetAccessToken::getGitHubAppAccessToken($this->rid)]);

        $auto_merge_label = $khsci->issue_labels->listLabelsOnIssue($this->repo_full_name, $this->pull_request_number);

        // TODO
        $auto_merge_method = '';

        if ((CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $build_status) && $auto_merge_label) {
            Log::debug(__FILE__, __LINE__, 'already set auto merge', [], Log::INFO);

            $repo_array = explode('/', Repo::getRepoFullName($this->git_type, $this->rid));

            try {
                if ($khsci->pull_request->isMerged($repo_array[0], $repo_array[1], $this->pull_request_number)) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        'already merged, skip', [], Log::WARNING
                    );

                    return;
                }

                $commit_message = null;

                $khsci->pull_request
                    ->merge(
                        $repo_array[0],
                        $repo_array[1],
                        $this->pull_request_number,
                        $this->commit_message,
                        $commit_message,
                        $this->commit_id,
                        (int) $auto_merge_method
                    );
                Log::debug(__FILE__, __LINE__, 'auto merge success, method is '.$auto_merge_method, [], Log::INFO);
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
            Log::debug(__FILE__, __LINE__, 'Build Log empty, skip', [], Log::WARNING);

            return;
        }

        if (!$this->unique_id) {
            Log::debug(__FILE__, __LINE__, 'config not found, skip', [], Log::WARNING);

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

        Job::updateLog($this->build_key_id, $log_content);

        // cleanup
        unlink($folder_name.'/'.$this->unique_id);

        Cache::connect()->del((string) $this->unique_id);
    }

    /**
     * @throws Exception
     */
    private function setStatusInProgress(): void
    {
        Build::updateStartAt($this->build_key_id);
        Build::updateBuildStatus($this->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS);

        if ('github' === $this->git_type) {
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
    }

    /**
     * @throws Exception
     */
    private function setBuildStatusInactive(): void
    {
        $this->description = 'This Repo is Inactive';

        if ('github' === $this->git_type) {
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
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

        // GitHub App checks API
        if ('github' === $this->git_type) {
            $build_log = Job::getLog((int) $this->build_key_id);

            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
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
            $build_log = Job::getLog((int) $this->build_key_id);
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
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
            $build_log = Job::getLog((int) $this->build_key_id);
            Up::updateGitHubAppChecks(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
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

    public function __call($name, $arguments): void
    {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
        }
    }
}

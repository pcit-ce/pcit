<?php

declare(strict_types=1);

namespace App\Console;

use App\Build as BuildDB;
use App\Build;
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

    private $branch;

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

        $build = $this->khsci->build;

        try {
            $output = array_values($this->getBuildDB());

            Log::connect()->debug('====== '.$this->build_key_id.' Build Start Success ======');

            $this->checkCIRoot();

            unset($output[10]);

            $this->setStatusInProgress();

            $repo_full_name = Repo::getRepoFullName($this->git_type, (int) $this->rid);

            array_push($output, $repo_full_name);

            // 清理构建环境
            $build->systemDelete('1');
            $build(...$output);
        } catch (CIException $e) {
            // 没有 build_key_id，即数据库没有待构项目，跳过

            $this->build_key_id = $e->getCode();

            if (01404 === $this->build_key_id) {
                // 数据库不存在项目，跳出
                return;
            }

            $this->commit_id = $e->getCommitId();
            $this->unique_id = $e->getUniqueId();
            $this->event_type = $e->getEventType();
            $this->git_type = BuildDB::getGitType($this->build_key_id);

            // $e->getCode() is build key id.
            BuildDB::updateStopAt($this->build_key_id);

            $this->saveLog();

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_INACTIVE:
                    $this->build_status = CI::BUILD_STATUS_INACTIVE;
                    $this->setBuildStatusInactive();

                    break;
                case CI::BUILD_STATUS_FAILED:
                    $this->build_status = CI::BUILD_STATUS_FAILED;
                    $this->setBuildStatusFailed();

                    break;
                case CI::BUILD_STATUS_PASSED:
                    $this->build_status = CI::BUILD_STATUS_PASSED;
                    $this->setBuildStatusPassed();

                    break;
                default:
                    $this->build_status = CI::BUILD_STATUS_ERRORED;
                    $this->setBuildStatusErrored();
            }

            Log::debug(__FILE__, __LINE__, $e->__toString());
        } catch (\Throwable  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString());
        } finally {
            Up::runWebhooks();

            if (01404 === $this->build_key_id) {
                // 数据库不存在项目，跳出
                return;
            }

            $this->build_key_id && $this->build_status &&
            BuildDB::updateBuildStatus($this->build_key_id, $this->build_status);

            Env::get('CI_WECHAT_TEMPLATE_ID', false) && $this->description &&
            $this->weChatTemplate($this->description);

            $build->systemDelete($this->unique_id, true);

            if (!$this->unique_id) {

                return;
            }

            $this->autoMerge();

            $this->sendEMail();

            Log::connect()->debug('====== '.$this->build_key_id.' Build Stopped Success ======');

            Cache::connect()->set('khsci_up_status', 0);
        }
    }

    /**
     * @throws Exception
     */
    private function sendEMail()
    {
        $build_status_changed = Build::buildStatusIsChanged((int) $this->rid, $this->branch);

        $email_list = null;

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

                $email_list = $recipients;
            } else {
                $email_list = $email;
            }
        }

        if (CI::BUILD_STATUS_PASSED === $this->build_status) {
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

        if (!(is_null($on_success) && is_null($on_failure && $build_status_changed))) {

            return;
        }

        // 构建成功

        $subject = 'user/repo#build_id(branch-commit_id)';

        $body = '';

        try {
            $mail = ($this->khsci)->mail;

            foreach (json_decode(getenv('CI_EMAIL_ADDRESS_JSON')) as $k => $v) {
                $mail->addAddress($k, $v);
            }

            foreach (json_decode(getenv('CI_EMAIL_CC_JSON'), true) as $k) {
                $mail->addCC($k); // 抄送
            }

            foreach (json_decode(getenv('CI_EMAIL_BCC_JSON'), true) as $k) {
                $mail->addBCC($k); // 暗抄送
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            Log::debug(__FILE__, __LINE__, 'Message has been sent');
        } catch (Exception $e) {
            Log::debug(
                __FILE__,
                __LINE__,
                'Message could not be sent. Mailer Error: ', $mail->ErrorInfo
            );
        }

    }

    /**
     * @throws Exception
     */
    private function getBuildDB()
    {

        $sql = <<<'EOF'
SELECT

id,git_type,rid,commit_id,commit_message,branch,event_type,pull_request_id,tag_name,config,check_run_id,pull_request_source

FROM

builds WHERE build_status=? AND event_type IN (?,?,?) AND config !='[]' ORDER BY id DESC;
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
            throw new CIException(
                null,
                null,
                null,
                'Build not Found, skip',
                01404
            );
        }

        $output = array_values($output);

        $this->git_type = $output[1];
        $this->branch = $output[5];
        $this->build_key_id = (int) $output[0];
        $this->pull_request_id = (int) $output[7];
        $this->rid = (int) $output[2];
        $this->commit_message = $output[4];
        $this->config = $output[9];
        $this->commit_id = $output[3];

        $this->config = JSON::beautiful($this->config);

        return $output;
    }

    /**
     * @throws Exception
     */
    private function checkCIRoot(): void
    {
        $ci_root = Env::get('CI_ROOT');

        while ($ci_root) {

            Log::debug(__FILE__, __LINE__, 'KhsCI already set ci root');

            $admin = Repo::getAdmin($this->git_type, (int) $this->rid);
            $admin_array = json_decode($admin, true);

            $ci_root_array = json_decode($ci_root, true);
            $root = $ci_root_array[$this->git_type];

            foreach ($root as $k) {
                $uid = User::getUid($this->git_type, $k);

                if (in_array($uid, $admin_array)) {

                    return;
                }
            }

            throw new CIException(
                null,
                $this->commit_id,
                $this->event_type,
                CI::BUILD_STATUS_PASSED,
                $this->build_key_id
            );
        }
    }

    /**
     * @throws Exception
     */
    private function setStatusInProgress()
    {
        BuildDB::updateStartAt($this->build_key_id);
        BuildDB::updateBuildStatus($this->build_key_id, CI::BUILD_STATUS_IN_PROGRESS);

        if ('github_app' === $this->git_type) {
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
    public function autoMerge(): void
    {
        Log::debug(__FILE__, __LINE__, 'check auto merge');

        $build_status = $this->build_status;

        $auto_merge_method = BuildDB::isAutoMerge(
            $this->git_type,
            (int) $this->rid,
            $this->commit_id,
            $this->pull_request_id
        );

        if ((CI::BUILD_STATUS_PASSED === $build_status) && $auto_merge_method) {
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

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
        }
    }
}

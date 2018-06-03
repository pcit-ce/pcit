<?php

declare(strict_types=1);

namespace App\Console;

use App\Build;
use App\Console\Build as BuildDaemon;
use App\GetAccessToken;
use App\Issue;
use App\Repo;
use App\User;
use Error;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Git;
use KhsCI\Support\HTTP;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;
use TencentAI\TencentAI;

class Up
{
    private $git_type;

    private $config_array;

    private $cache_key_up_status = 'khsci_up_status';

    private $cache_key_github_issue = 'github_issue';

    /**
     * @throws Exception
     */
    public function up(): void
    {
        try {
            if (1 === Cache::connect()->get($this->cache_key_up_status)) {
                // 设为 1 说明有一个任务在运行，休眠之后跳过循环
                echo '.WO';
                sleep(10);

                return;
            }

            Cache::connect()->set($this->cache_key_up_status, 1);

            // 从 Webhooks 缓存中拿出数据，进行处理

            $this->webhooks();

            // Docker 构建队列

            $docker_build_skip = false;

            try {
                Http::get(Env::get('CI_DOCKER_HOST').'/info');
            } catch (\Throwable $e) {
                $docker_build_skip = true;
            }

            if (!$docker_build_skip) {
                echo 'Connect Docker Daemon Success, Docker Build Running...';

                $build = new BuildDaemon();

                $build->build();
            }
            echo '[W]';
        } catch (Exception | Error $e) {
            $msg = $e->getMessage();
            $code = $e->getCode();
            $file = $e->getFile();
            $line = $e->getLine();

            $errormsg = json_encode([
                'msg' => $msg,
                'code' => $code,
                'file' => $file,
                'line' => $line,
            ]);

            Log::connect()->debug($errormsg);
            echo $errormsg.'[E]';
        } finally {
            $this->closeResource();
        }
    }

    public function closeResource(): void
    {
        DB::close();
        Cache::close();
        HTTP::close();
        Log::close();
        TencentAI::close();
    }

    /**
     * @param int    $build_key_id Build table primary id
     * @param string $state        Default is pending
     * @param string $description
     *
     * @throws Exception
     */
    public static function updateGitHubStatus(int $build_key_id,
                                              string $state = null,
                                              string $description = null): void
    {
        $rid = Build::getRid($build_key_id);

        $repo_full_name = Repo::getRepoFullName('github', (int) $rid);

        list($repo_prefix, $repo_name) = explode('/', $repo_full_name);

        $build_output_array = Build::find($build_key_id);

        $khsci = new KhsCI(['github_access_token' => GetAccessToken::byRepoFullName($repo_full_name)]);

        $khsci->repo_status->create(
            $repo_prefix,
            $repo_name,
            $build_output_array['commit_id'],
            $state ?? 'pending',
            Env::get('CI_HOST').'/github/'.$repo_full_name.'/builds/'.$build_key_id,
            'continuous-integration/'.Env::get('CI_NAME').'/'.$build_output_array['event_type'],
            $description ?? 'The analysis or builds is pending'
        );

        $log_message = 'Create GitHub commit Status '.$build_key_id;

        Log::debug(__FILE__, __LINE__, 'Create GitHub commit Status '.$log_message);
    }

    /**
     * @param int         $build_key_id
     * @param string|null $name
     * @param string      $status
     * @param int         $started_at
     * @param int         $completed_at
     * @param string      $conclusion
     * @param string|null $title
     * @param string      $summary
     * @param string      $text
     * @param array|null  $annotations  [$annotation, $annotation2]
     * @param array|null  $images       [$image, $image2]
     * @param array|null  $actions      [$action]
     * @param bool        $force_create 默认请款下若 check_run_id 已存在，则更新此 check_run_id
     *                                  若设为 true 则新建一个 check_run ,适用于第三方服务完成状态展示
     *                                  或是没有过程，直接完成的构建
     *
     * @throws Exception
     */
    public static function updateGitHubAppChecks(int $build_key_id,
                                                 string $name = null,
                                                 string $status = null,
                                                 int $started_at = null,
                                                 int $completed_at = null,
                                                 string $conclusion = null,
                                                 string $title = null,
                                                 string $summary = null,
                                                 string $text = null,
                                                 array $annotations = null,
                                                 array $images = null,
                                                 array $actions = null,
                                                 bool $force_create = false): void
    {
        $rid = Build::getRid((int) $build_key_id);

        $repo_full_name = Repo::getRepoFullName('github_app', (int) $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_app_access_token' => $access_token], 'github_app');

        $output_array = Build::find((int) $build_key_id);

        $branch = $output_array['branch'];

        $commit_id = $output_array['commit_id'];

        $event_type = $output_array['event_type'];

        $details_url = Env::get('CI_HOST').'/github_app/'.$repo_full_name.'/builds/'.$build_key_id;

        $config = JSON::beautiful(Build::getConfig((int) $build_key_id));

        $status = $status ?? CI::GITHUB_CHECK_SUITE_STATUS_QUEUED;

        $status_use_in_title = $status;

        if (CI::BUILD_STATUS_IN_PROGRESS === $status) {
            $status_use_in_title = 'in Progress';
        }

        $name = $name ?? 'Build Event is '.ucfirst($event_type).' '.ucfirst($status_use_in_title);

        $title = $title ?? Env::get('CI_NAME').' Build is '.ucfirst($status_use_in_title);

        $summary = $summary ?? 'This Repository Build Powered By [KhsCI](https://github.com/khs1994-php/khsci)';

        $text = $text ?? $khsci->check_md->queued('PHP', PHP_OS, $config);

        $check_run_id = Build::getCheckRunId((int) $build_key_id);

        if ($check_run_id and !$force_create) {
            $output = $khsci->check_run->update(
                $repo_full_name, $check_run_id, $name, $branch, $commit_id, $details_url,
                (string) $build_key_id, $status, $started_at ?? time(),
                $completed_at, $conclusion, $title, $summary, $text, $annotations, $images, $actions
            );
        } else {
            $output = $khsci->check_run->create(
                $repo_full_name, $name, $branch, $commit_id, $details_url, (string) $build_key_id, $status,
                $started_at ?? time(),
                $completed_at, $conclusion, $title, $summary, $text, $annotations, $images, $actions
            );
        }

        $log_message = 'Create GitHub App Check Run '.$build_key_id;

        Log::debug(__FILE__, __LINE__, $log_message);

        echo $log_message;

        Build::updateCheckRunId(json_decode($output)->id ?? null, $build_key_id);
    }

    /**
     * @param int    $rid
     * @param string $commit_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getConfig(int $rid, string $commit_id)
    {
        $repo_full_name = Repo::getRepoFullName($this->git_type, $rid);

        $url = Git::getRawUrl($this->git_type, $repo_full_name, $commit_id, '.khsci.yml');

        $yaml_file_content = HTTP::get($url);

        if (!$yaml_file_content) {
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id not include .khsci.yml");

            return [];
        }

        $config = yaml_parse($yaml_file_content);

        if (!$config) {
            Log::debug(__FILE__, __LINE__, "$repo_full_name $commit_id .khsci.yml parse error");

            return [];
        }

        return $config;
    }

    /**
     * @param mixed $git_type
     */
    public function setGitType($git_type): void
    {
        $this->git_type = $git_type;
    }

    /**
     * @throws Exception
     */
    private function webhooks(): void
    {
        $webhooks = (new KhsCI())->webhooks;

        $json_raw = $webhooks->getCache();

        if (!$json_raw) {
            return;
        }

        list($git_type, $event_type, $json) = json_decode($json_raw, true);

        if ('aliyun_docker_registry' === $git_type) {
            $this->aliyunDockerRegistry($json);

            return;
        }

        $this->git_type = $git_type;

        try {
            $this->$event_type($json);

            $webhooks->pushSuccessCache($json_raw);

            return;
        } catch (Error | Exception $e) {
            $webhooks->pushErrorCache($json_raw);

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $content
     *
     * @throws Exception
     */
    private function aliyunDockerRegistry(string $content): void
    {
        $obj = json_decode($content);

        $aliyun_docker_registry_name = $obj->repository->repo_full_name;

        $aliyun_docker_registry_tagname = $obj->push_data->tag;

        $aliyun_docker_registry = [];

        require __DIR__.'/../../config/config.php';

        if (array_key_exists($aliyun_docker_registry_name, $aliyun_docker_registry)) {
            $git_repo_full_name = $aliyun_docker_registry["$aliyun_docker_registry_name"];

            $name = 'Aliyun Docker Registry Push '.$aliyun_docker_registry_name.':'.$aliyun_docker_registry_tagname;

            echo $name;

            Log::debug(__FILE__, __LINE__, $name);

            $this->updateGitHubAppChecks(
                (int) Build::getCurrentBuildKeyId(
                    'github_app', (int) Repo::getRid(
                    'github_app', ...explode('/', (string) $git_repo_full_name)
                )
                ),
                $name,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time(),
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                'Aliyun Docker Registry Push',
                'Build Docker image Success',
                (new KhsCi())->check_md->aliyunDockerRegistry(
                    'PHP',
                    PHP_OS,
                    JSON::beautiful($content)
                ),
                null,
                null,
                null,
                true
            );
        }

        return;
    }

    /**
     * 需要更新状态的，存入缓存队列.
     *
     * @param int $last_insert_id
     *
     * @throws Exception
     */
    private function updateStatus(int $last_insert_id): void
    {
        if ('github_app' === $this->git_type) {
            if ($this->config_array) {
                $this->updateGitHubAppChecks($last_insert_id);
            } else {
                $this->updateGitHubAppChecks($last_insert_id,
                    null,
                    CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                    time(),
                    time(),
                    CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                    null,
                    null,
                    (new KhsCI())->check_md->action_required('PHP', PHP_OS, '{}',
                        'This repo not include .khsci.yml file')
                );

                Build::updateBuildStatus($last_insert_id, CI::BUILD_STATUS_SKIP);
            }

            return;
        }

        if ($this->config_array) {
            $this->updateGitHubStatus($last_insert_id);
        } else {
            Build::updateBuildStatus($last_insert_id, CI::BUILD_STATUS_SKIP);
        }
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public function ping(string $content)
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id ?? 0;

        $event_time = time();

        $sql = <<<'EOF'
INSERT builds(

git_type,event_type,rid,event_time,request_raw

) VALUES(?,?,?,?,?);
EOF;
        $data = [
            $this->git_type, __FUNCTION__, $rid, $event_time, $content,
        ];

        return DB::insert($sql, $data);
    }

    /**
     * push.
     *
     * 1. 首次推送到新分支，head_commit 为空
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function push(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref = $obj->ref;

        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            $this->tag($ref_array[2], $content);

            return;
        }

        $branch = $this->ref2branch($ref);

        $commit_id = $obj->after;

        $compare = $obj->compare;

        $head_commit = $obj->head_commit;

        if (null === $head_commit) {
            return;
        }

        $commit_message = $head_commit->message;

        $commit_timestamp = Date::parse($head_commit->timestamp);

        $author = $head_commit->author;
        $committer_name = $author->name;
        $committer_email = $author->email;
        $committer_username = $author->username;

        // $committer = $head_commit->committer;
        // $committer_name = $committer->name;
        // $committer_email = $committer->email;
        // $committer_username = $committer->username;

        $installation_id = $obj->installation->id ?? null;

        $sql = <<<'EOF'
INSERT INTO builds(

git_type,branch,compare,commit_id,
commit_message,committer_name,committer_email,committer_username,
rid,created_at,config

) VALUES(?,?,?,?,?,?,?,?,?,?,?);
EOF;
        $config_array = $this->getConfig($rid, $commit_id);

        $config = json_encode($config_array);

        $data = [
            $this->git_type, $branch, $compare, $commit_id,
            $commit_message, $committer_name, $committer_email, $committer_username,
            $rid, $commit_timestamp, $config,
        ];

        $last_insert_id = DB::insert($sql, $data);

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        $this->config_array = $config_array;

        if ($this->skip($commit_message, (int) $last_insert_id, $branch, $config)) {
            return;
        }

        $this->updateStatus((int) $last_insert_id);
    }

    /**
     * 检查 commit 信息跳过构建. branch 匹配构建
     *
     * @param string $commit_message
     * @param int    $build_key_id
     *
     * @param string $branch
     * @param string $config
     *
     * @return bool
     *
     * @throws Exception
     */
    private function skip(string $commit_message, int $build_key_id, string $branch = null, string $config = null)
    {
        // check commit message
        if (preg_match('#(\[skip ci\])|(\[ci skip\])#i', $commit_message)) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' is skip by commit message');

            return true;
        }

        $yaml_obj = (object) json_decode($config, true);

        $branches = $yaml_obj->branches ?? null;

        if (null === $branches) {

            return false;
        }

        $branches_exclude = $branches->exclude ?? [];

        $branches_include = $branches->include ?? [];

        if ([] === $branches_exclude and [] === $branches_include) {

            return false;
        }

        // 匹配排除分支
        if ($branches_exclude) {
            if ((new KhsCI())->build::check($branches_exclude, $branch)) {

                $message = "config exclude $branch, build skip";

                Log::debug(__FILE__, __LINE__, $message);

                echo $message;

                return true;
            }
        }

        // 匹配包含分支
        if ($branches_include) {

            if ((new KhsCI())->build::check($branches_include, $branch)) {

                $message = "config include $branch, building";

                Log::debug(__FILE__, __LINE__, $message);

                echo $message;

                return false;
            }
        }

        return false;
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    private function writeSkipToDB(int $build_key_id)
    {
        Build::updateBuildStatus($build_key_id, CI::BUILD_STATUS_SKIP);
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public function status(string $content)
    {
        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type

) VALUES(?,?);
EOF;

        return DB::insert($sql, [
                $this->git_type, __FUNCTION__,
            ]
        );
    }

    /**
     *  "assigned", "unassigned",
     *  "labeled",  "unlabeled",
     *  "opened",   "edited", "closed" or "reopened"
     *  "milestoned", "demilestoned",.
     *
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public function issues(string $content)
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $issue = $obj->issue;

        $rid = $obj->repository->id;

        $issue_id = $issue->id;
        $issue_number = $issue->number;
        $title = $issue->title;
        $body = $issue->body;

        $sender = $obj->sender;
        $sender_username = $sender->login;
        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $state = $issue->state;
        $locked = $issue->locked;
        $assignees = $issue->assignees;
        $labels = $issue->labels;
        $created_at = Date::parse($issue->created_at);
        $updated_at = Date::parse($issue->updated_at);
        $closed_at = Date::parse($issue->closed_at);

        $installation_id = $obj->installation->id ?? null;

        if (in_array($action, ['opened', 'edited', 'closed' or 'reopened'])) {
            $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,issue_number,action,title,body,sender_username,sender_uid,sender_pic,
state,locked,created_at,closed_at,updated_at

) VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;

            $last_insert_id = DB::insert($sql, [
                    $this->git_type, $rid, $issue_id, $issue_number, $action, $title, $body,
                    $sender_username, $sender_uid, $sender_pic,
                    $state, (int) $locked,
                    $created_at, $closed_at, $updated_at,
                ]
            );
        }

        if ($assignees) {
            foreach ($assignees as $k) {
                Issue::updateAssignees($k, $this->git_type, $issue_id);
            }
        }

        if ($labels) {
            foreach ($labels as $k) {
                Issue::updateLabels($k, $this->git_type, $issue_id);
            }
        }

        if ('opened' !== $action) {
            return $last_insert_id;
        }

        $repo_full_name = Repo::getRepoFullName($this->git_type, $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_app_access_token' => $access_token], 'github_app');

        $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        return $last_insert_id;
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function issue_comment(string $content): void
    {
        $obj = json_decode($content);

        $action = $obj->action;
        $comment = $obj->comment;

        $sender = $comment->user;
        $sender_username = $sender->login;

        if (strpos($sender_username, '[bot]')) {
            echo 'Bot issue comment SKIP';

            return;
        }

        $sender_uid = $sender->id;
        $sender_pic = $sender->avatar_url;

        $issue = $obj->issue;
        $issue_id = $issue->id;
        $issue_number = $issue->number;

        $comment_id = $comment->id;
        $body = $comment->body;

        $created_at = Date::parse($comment->created_at);
        $updated_at = Date::parse($comment->updated_at);

        $rid = $obj->repository->id;

        $repo_full_name = Repo::getRepoFullName($this->git_type, $rid);
        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $installation_id = $obj->installation->id ?? null;

        $khsci = new KhsCI(['github_app_access_token' => $access_token], 'github_app');

        if ('edited' === $action) {
            echo 'Edit Issue Comment SKIP';

            return;
        }

        if ('deleted' === $action) {
            $output = Issue::comment_deleted(
                $this->git_type,
                $issue_id,
                $comment_id,
                $updated_at
            );

            if (1 === $output) {
                $debug_info = 'Delete Issue Comment SUCCESS';

                Log::debug(__FILE__, __LINE__, $debug_info);

                echo $debug_info;
            }

            return;
        }

        $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,comment_id,issue_number,body,sender_username,
sender_uid,sender_pic,created_at

) VALUES(null,?,?,?,?,?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
                $this->git_type, $rid, $issue_id, $comment_id, $issue_number, $body,
                $sender_username, $sender_uid, $sender_pic, $created_at,
            ]
        );

        Cache::connect()->lPush($this->cache_key_github_issue, $last_insert_id);

        $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        $debug_info = 'Create Bot Issue Comment By Issue Comment ADD';

        Log::debug(__FILE__, __LINE__, $debug_info);

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        echo $debug_info;
    }

    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function pull_request(string $content): void
    {
        $obj = json_decode($content);

        $action = $obj->action;

        if (!in_array($action, ['opened', 'synchronize'])) {
            return;
        }

        $pull_request = $obj->pull_request;

        $event_time = $pull_request->updated_at ?? $pull_request->created_at;

        $event_time = Date::parse($event_time);

        // head 向 base 提交 PR
        $pull_request_base = $pull_request->base;
        $pull_request_head = $pull_request->head;

        $rid = $pull_request_base->repo->id;

        $commit_message = $pull_request->title;
        $commit_id = $pull_request_head->sha;

        $committer_username = $pull_request->user->login;

        $pull_request_id = $obj->number;

        $branch = $pull_request->base->ref;

        $installation_id = $obj->installation->id ?? null;

        // 检查内外部 PR

        $internal = true;

        if ($pull_request_head->repo->id !== $pull_request_base->repo->id) {
            $internal = false;
        }

        $pull_request_source = $pull_request_head->repo->full_name;

        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,created_at,action,
commit_id,commit_message,committer_username,pull_request_id,
branch,rid,config,internal,pull_request_source

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?);

EOF;
        $config_array = $this->getConfig($rid, $commit_id);

        $config = json_encode($config_array);

        $last_insert_id = DB::insert($sql, [
                $this->git_type, __FUNCTION__, $event_time, $action,
                $commit_id, $commit_message, $committer_username, $pull_request_id,
                $branch, $rid, $config, $internal, $pull_request_source,
            ]
        );

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        $this->config_array = $config_array;

        if ($this->skip($commit_message, (int) $last_insert_id)) {
            return;
        }

        $this->updateStatus((int) $last_insert_id);
    }

    /**
     * @param string $tag
     * @param string $content
     *
     * @throws Exception
     */
    public function tag(string $tag, string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $branch = $this->ref2branch($obj->base_ref);

        $head_commit = $obj->head_commit;
        $commit_id = $head_commit->id;
        $commit_message = $head_commit->message;

        $committer = $head_commit->author;
        $committer_username = $committer->username;
        $committer_name = $committer->name;
        $committer_email = $committer->email;

        $event_time = Date::parse($head_commit->timestamp);

        $installation_id = $obj->installation->id ?? null;

        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,branch,tag_name,
commit_id,commit_message,committer_name,committer_email,
committer_username,rid,created_at,config

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?);
EOF;
        $config_array = $this->getConfig($rid, $commit_id);

        $config = json_encode($config_array);

        $last_insert_id = DB::insert($sql, [
            $this->git_type, __FUNCTION__, $branch, $tag,
            $commit_id, $commit_message, $committer_name, $committer_email,
            $committer_username, $rid, $event_time, $config,
        ]);

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        $this->config_array = $config_array;

        $this->updateStatus((int) $last_insert_id);
    }

    /**
     * Do Nothing.
     *
     * @param string $content
     *
     * @return array
     */
    public function watch(string $content)
    {
        return [
            'code' => 200,
        ];
    }

    /**
     * Do Nothing.
     *
     * @param string $content
     *
     * @return array
     */
    public function fork(string $content)
    {
        return [
            'code' => 200,
        ];
    }

    /**
     * @param string $content
     *
     * @return array
     */
    public function release(string $content)
    {
        return [
            'code' => 200,
        ];
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function create(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref_type = $obj->ref_type;

        $installation_id = $obj->installation->id ?? null;

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        switch ($ref_type) {
            case 'branch':
                $branch = $obj->ref;
        }
    }

    /**
     * Delete tag or branch.
     *
     * @param string $content
     *
     * @return int
     *
     * @throws Exception
     */
    public function delete(string $content)
    {
        $obj = json_decode($content);

        $ref_type = $obj->ref_type;

        $rid = $obj->repository->id;

        $installation_id = $obj->installation->id ?? null;

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        if ('branch' === $ref_type) {
            $sql = 'DELETE FROM builds WHERE git_type=? AND branch=? AND rid=?';

            return DB::delete($sql, [$this->git_type, $obj->ref, $rid]);
        } else {
            return 0;
        }
    }

    /**
     * @param string $ref
     *
     * @return mixed
     */
    public function ref2branch(string $ref)
    {
        $ref_array = explode('/', $ref);

        return $ref_array[2];
    }

    /**
     * @param string $content
     */
    public function member(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $installation_id = $obj->installation->id ?? null;
    }

    /**
     * @param string $content
     */
    public function team_add(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $installation_id = $obj->installation->id ?? null;
    }

    /**
     * Any time a GitHub App is installed or uninstalled.
     *
     * action:
     *
     * created 用户点击安装按钮
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @see
     *
     * @param string $content
     *
     * @return int
     *
     * @throws Exception
     */
    public function installation(string $content)
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $installation_id = $obj->installation->id;

        // 可视为仓库管理员.

        $sender = $obj->sender;

        $sender_id = $sender->id;

        $sender_username = $sender->login;

        $email = null;

        $pic = $sender->avatar_url;

        $accessToken = null;

        User::updateUserInfo('github_app', $sender_id, $sender_username, $email, $pic, $accessToken);

        if ('created' === $action) {
            $repo = $obj->repositories;

            return $this->installation_action_created($installation_id, $repo, $sender_id);
        }

        return $this->installation_action_deleted($installation_id);
    }

    /**
     * @param int   $installation_id
     * @param array $repo
     * @param int   $sender_id
     *
     * @return int
     *
     * @throws Exception
     */
    private function installation_action_created(int $installation_id, array $repo, int $sender_id)
    {
        foreach ($repo as $k) {
            // 仓库信息存入 repo 表
            $rid = $k->id;

            $repo_full_name = $k->full_name;

            list($repo_prefix, $repo_name) = explode('/', $repo_full_name);

            $sql = <<<EOF
INSERT INTO repo(

id,git_type,rid,repo_prefix,repo_name,repo_full_name,repo_admin,default_branch,installation_id,last_sync

) VALUES(null,?,?,?,?,?,JSON_ARRAY(?),'master',?,?)

EOF;

            $last_insert_id = DB::insert($sql, [
                    'github_app', $rid, $repo_prefix, $repo_name, $repo_full_name, $sender_id, $installation_id, time(),
                ]
            );
        }

        return $last_insert_id;
    }

    /**
     * @param int $installation_id
     *
     * @return int
     *
     * @throws Exception
     */
    private function installation_action_deleted(int $installation_id)
    {
        $sql = 'DELETE FROM repo WHERE git_type=? AND installation_id=?';

        $output = DB::delete($sql, [
            'github_app', $installation_id,
        ]);

        return $output;
    }

    /**
     * Any time a repository is added or removed from an installation.
     *
     * action:
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     *
     * @param string $content
     *
     * @return int
     *
     * @throws Exception
     */
    public function installation_repositories(string $content)
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $installation_id = $obj->installation->id;

        $repo_type = 'repositories_'.$action;

        $repo = $obj->$repo_type;

        $sender = $obj->sender->id;

        if ('added' === $action) {
            return $this->installation_action_created($installation_id, $repo, $sender);
        }

        return $this->installation_repositories_action_removed($installation_id, $repo);
    }

    /**
     * @param int   $installation_id
     * @param array $repo
     *
     * @return int
     *
     * @throws Exception
     */
    private function installation_repositories_action_removed(int $installation_id, array $repo)
    {
        foreach ($repo as $k) {
            $rid = $k->id;

            $sql = 'DELETE FROM repo WHERE installation_id=? AND rid=?';

            $output = DB::delete($sql, [$installation_id, $rid]);
        }

        return $output;
    }

    /**
     * @deprecated
     */
    public function integration_installation(): void
    {
    }

    /**
     * @deprecated
     */
    public function integration_installation_repositories(): void
    {
    }

    /**
     * Action.
     *
     * completed
     *
     * requested 用户推送分支，github post webhooks
     *
     * rerequested 用户点击了重新运行按钮
     *
     *
     * @see https://developer.github.com/v3/activity/events/types/#checksuiteevent
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function check_suite(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $action = $obj->action;

        $check_suite = $obj->check_suite;

        $check_suite_id = $check_suite->id;

        $branch = $check_suite->head_branch;

        $commit_id = $check_suite->head_sha;

        $installation_id = $obj->installation->id ?? null;

        $sql = <<<EOF
INSERT INTO builds(
action,event_type,git_type,check_suites_id,branch,commit_id
) VALUES (?,?,?,?,?,?);
EOF;

        //        $last_insert_id = DB::insert($sql, [
        //            $action, __FUNCTION__, $this->git_type, $check_suite_id, $branch, $commit_id,
        //        ]);

        if ('rerequested' === $action) {
            $check_run_id = '';
        }

        // Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);
    }

    /**
     * Action.
     *
     * created updated rerequested
     *
     * @see https://developer.github.com/v3/activity/events/types/#checkrunevent
     *
     * @param string $content
     *
     * @throws Exception
     */
    public function check_run(string $content): void
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $installation_id = $obj->installation->id ?? null;

        $rid = $obj->repository->id;

        if ('rerequested' === $action or 'requested_action' === $action) {
            $check_run = $obj->check_run;

            $check_run_id = $check_run->id;

            $commit_id = $check_run->head_sha;

            $external_id = $check_run->external_id;

            $check_suite = $check_run->check_suite;

            $check_suite_id = $check_suite->id;

            $branch = $check_suite->head_branch;

            switch ($action) {
                case 'rerequested':

                    break;

                case 'requested_action':
                    // 用户点击了按钮，CI 推送修复补丁

                    break;
            }
        } else {
            return;
        }

        Repo::updateGitHubInstallationIdByRid((int) $rid, (int) $installation_id);

        self::updateGitHubAppChecks((int) $external_id);

        Build::updateBuildStatus((int) $external_id, 'pending');
    }
}

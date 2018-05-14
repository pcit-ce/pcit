<?php

declare(strict_types=1);

namespace App\Console;

use App\Build;
use App\GetAccessToken;
use App\Issue;
use App\Repo;
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

class Up
{
    private static $git_type;

    private static $cache_key_up_status = 'khsci_up_status';

    private static $cache_key_github_app_checks = 'github_app_checks';

    private static $cache_key_github_commit_status = 'github_commit_status';

    private static $cache_key_github_issue = 'github_issue';

    /**
     * @throws Exception
     */
    public static function up(): void
    {
        while (1) {
            try {
                if (1 === Cache::connect()->get(self::$cache_key_up_status)) {
                    // 设为 1 说明有一个任务在运行，休眠之后跳过循环
                    echo "...";

                    sleep(10);
                    continue;
                }

                Cache::connect()->set(self::$cache_key_up_status, 1);

                // Queue::queue();

                // 从 Webhooks 缓存中拿出数据，进行处理

                // 处理 commit status GitHub OAuth
                $build_key_id = Cache::connect()->rPop(self::$cache_key_github_commit_status);

                if ($build_key_id) {
                    self::updateGitHubStatus((int) $build_key_id);
                }

                // 处理 Check Run GitHub App Only
                $build_key_id = Cache::connect()->rpop(self::$cache_key_github_app_checks);

                if ($build_key_id) {
                    self::updateGitHubAppChecks((int) $build_key_id);
                }

                self::webhooks();

                echo "...";

                DB::close();
                Cache::close();

                sleep(2);
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

                echo $errormsg;
            }
        }
    }

    /**
     * @param int    $build_key_id
     * @param string $state
     *
     * @param string $description
     *
     * @throws Exception
     */
    public static function updateGitHubStatus(int $build_key_id,
                                              string $state = 'pending',
                                              string $description = null
    ): void
    {
        $rid = Build::getRid($build_key_id);

        $repo_full_name = Repo::getRepoFullName('github', (int) $rid);

        list($repo_prefix, $repo_name) = explode('/', $repo_full_name);

        $build_output_array = Build::find($build_key_id);

        $khsci = new KhsCI(['github_access_token' => GetAccessToken::byRepoFullName($repo_full_name)]);

        $output = $khsci->repo_status->create(
            $repo_prefix,
            $repo_name,
            $build_output_array['commit_id'],
            $state,
            Env::get('CI_HOST').'/github/'.$repo_full_name.'/builds/'.$build_key_id,
            'continuous-integration/'.Env::get('CI_NAME').'/'.$build_output_array['event_type'],
            $description ?? null
        );

        Log::connect()->debug($output);

        var_dump($output);

        Cache::connect()->set(self::$cache_key_up_status, 0);
    }

    /**
     * @param int         $build_key_id
     * @param string|null $name
     *
     * @param string      $status
     * @param int         $started_at
     * @param int         $completed_at
     * @param string      $conclusion
     * @param string|null $title
     * @param string      $summary
     *
     * @param string      $text
     * @param array|null  $annotations
     * @param array       $images
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
                                                 array $images = null
    ): void
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

        $language = 'PHP';

        $os = PHP_OS_FAMILY;

        $config = yaml_parse(
            HTTP::get(Git::getRawUrl('github', $repo_full_name, $commit_id, '.drone.yml'))
        );

        $config = JSON::beautiful(json_encode($config));

        $status = $status ?? CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS;

        $name = $name ?? 'Build Event is '.ucfirst($event_type).' '.ucfirst($status);

        $title = $title ?? Env::get('CI_NAME').' Build is '.ucfirst($status);

        $summary = $summary ?? 'This Repository Build Powered By [KhsCI](https://github.com/khs1994-php/khsci)';

        $text = $text ?? <<<EOF
# About KhsCI

**China First Support GitHub Checks API CI/CD System Powered By Docker and Tencent AI**

# Try KhsCI ?

Please See [KhsCI Support Docs](https://github.com/khs1994-php/khsci/tree/master/docs)

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary>Build Configuration</summary>

```json
$config
```

</details>
EOF;

        $output = $khsci->check_run->create(
            $repo_full_name, $name, $branch, $commit_id, $details_url, (string) $build_key_id, $status,
            $started_at ?? time(),
            $completed_at, $conclusion, $title, $summary, $text, $annotations, $images
        );

        Log::connect()->debug($output);

        var_dump($output);

        Build::updateCheckRunId(json_decode($output)->id ?? null, $build_key_id);

        Cache::connect()->set(self::$cache_key_up_status, 0);
    }

    /**
     * @throws Exception
     */
    private static function webhooks(): void
    {
        $khsci = new KhsCI();

        $webhooks = $khsci->webhooks;

        $json_raw = $webhooks->getCache();

        if (!$json_raw) {
            return;
        }

        list($git_type, $event_type, $json) = json_decode($json_raw, true);

        self::$git_type = $git_type;

        try {
            self::$event_type($json);

            $webhooks->pushSuccessCache($json_raw);

            return;
        } catch (Error | Exception $e) {
            $webhooks->pushErrorCache($json_raw);

            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 需要更新状态的，存入缓存队列
     *
     * @param int $last_insert_id
     *
     * @throws Exception
     */
    private static function pushCache(int $last_insert_id): void
    {
        if ('github_app' === static::$git_type) {
            Cache::connect()->lPush(self::$cache_key_github_app_checks, $last_insert_id);

            return;
        }

        Cache::connect()->lPush(self::$cache_key_github_commit_status, $last_insert_id);
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public static function ping(string $content)
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
            static::$git_type, __FUNCTION__, $rid, $event_time, $content,
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
    public static function push(string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref = $obj->ref;

        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            self::tag($ref_array[2], $content);

            return;
        }

        $branch = self::ref2branch($ref);

        $commit_id = $obj->after;

        $compare = $obj->compare;

        $head_commit = $obj->head_commit;

        if (null === $head_commit) {
            return;
        }

        $commit_message = $head_commit->message;

        $commit_timestamp = Date::parse($head_commit->timestamp);

        $committer = $head_commit->committer;
        $committer_name = $committer->name;
        $committer_email = $committer->email;
        $committer_username = $committer->username;

        $sql = <<<'EOF'
INSERT builds(

git_type,event_type,ref,branch,tag_name,compare,commit_id,commit_message,
committer_name,committer_email,committer_username,
rid,event_time,build_status,request_raw

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;
        $data = [
            static::$git_type, __FUNCTION__, $ref, $branch, null, $compare, $commit_id,
            $commit_message, $committer_name, $committer_email, $committer_username,
            $rid, $commit_timestamp, CI::BUILD_STATUS_PENDING, $content,
        ];

        $last_insert_id = DB::insert($sql, $data);

        self::pushCache((int) $last_insert_id);
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public static function status(string $content)
    {
        $sql = <<<'EOF'
INSERT builds(

git_type,event_type,request_raw

) VALUES(?,?,?);
EOF;

        return DB::insert($sql, [
                static::$git_type, __FUNCTION__, $content,
            ]
        );
    }

    /**
     *  "assigned", "unassigned",
     *  "labeled",  "unlabeled",
     *  "opened",   "edited", "closed" or "reopened"
     *  "milestoned", "demilestoned",
     *
     * @param string $content
     *
     * @return string
     * @throws Exception
     */
    public static function issues(string $content)
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

        if (in_array($action, ["opened", "edited", "closed" or "reopened"])) {


            $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,issue_number,action,title,body,sender_username,sender_uid,sender_pic,
state,locked,created_at,closed_at,updated_at

) VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;

            $last_insert_id = DB::insert($sql, [
                    static::$git_type, $rid, $issue_id, $issue_number, $action, $title, $body,
                    $sender_username, $sender_uid, $sender_pic,
                    $state, (int) $locked,
                    $created_at, $closed_at, $updated_at
                ]
            );
        }

        if ($assignees) {
            foreach ($assignees as $k) {
                Issue::updateAssignees($k, static::$git_type, $issue_id);
            }
        }

        if ($labels) {
            foreach ($labels as $k) {
                Issue::updateLabels($k, static::$git_type, $issue_id);
            }
        }

        $repo_full_name = Repo::getRepoFullName(static::$git_type, $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_app_access_token' => $access_token], 'github_app');

        $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        return $last_insert_id;
    }

    /**
     * "created", "edited", or "deleted"
     *
     * @param string $content
     *
     *
     * @throws Exception
     */
    public static function issue_comment(string $content): void
    {
        $obj = json_decode($content);

        $action = $obj->action;
        $comment = $obj->comment;

        $sender = $comment->user;
        $sender_username = $sender->login;

        if (strpos($sender_username, '[bot]')) {
            echo "Bot skip";

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

        $repo_full_name = Repo::getRepoFullName(static::$git_type, $rid);
        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);
        $khsci = new KhsCI(['github_app_access_token' => $access_token], 'github_app');

        if ('edited' === $action) {
            $output = Issue::comment_edited(
                static::$git_type,
                $issue_id,
                $comment_id,
                $updated_at,
                $body
            );

            $output = $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

            var_dump($output);

            return;
        }

        if ('deleted' === $action) {
            $output = Issue::comment_deleted(
                static::$git_type,
                $issue_id,
                $comment_id,
                $updated_at
            );

            var_dump($output);

            return;
        }

        $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,comment_id,issue_number,body,sender_username,
sender_uid,sender_pic,created_at

) VALUES(null,?,?,?,?,?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
                static::$git_type, $rid, $issue_id, $comment_id, $issue_number, $body,
                $sender_username, $sender_uid, $sender_pic, $created_at
            ]
        );

        Cache::connect()->lPush(static::$cache_key_github_issue, $last_insert_id);

        $output = $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        var_dump($output);
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
    public static function pull_request(string $content): void
    {
        $obj = json_decode($content);

        $action = $obj->action;

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

        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,event_time,request_raw,action,commit_id,commit_message,committer_username,
pull_request_id,branch,rid,build_status

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?);

EOF;
        $last_insert_id = DB::insert($sql, [
                static::$git_type, __FUNCTION__, $event_time, $content, $action, $commit_id, $commit_message,
                $committer_username, $pull_request_id, $branch, $rid, CI::BUILD_STATUS_PENDING,
            ]
        );

        self::pushCache((int) $last_insert_id);
    }

    /**
     * @param string $tag
     * @param string $content
     *
     * @throws Exception
     */
    public static function tag(string $tag, string $content): void
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref = $obj->ref;

        $branch = self::ref2branch($obj->base_ref);

        $head_commit = $obj->head_commit;
        $commit_id = $head_commit->id;
        $commit_message = $head_commit->message;

        $committer = $head_commit->author;
        $committer_username = $committer->username;
        $committer_name = $committer->name;
        $committer_email = $committer->email;

        $event_time = Date::parse($head_commit->timestamp);

        $sql = <<<'EOF'
INSERT builds(

git_type,event_type,ref,branch,tag_name,commit_id,commit_message,committer_name,committer_email,
committer_username,rid,event_time,build_status,request_raw

) VALUES(
?,?,?,?,?,?,?,?,?,?,?,?,?,?
);
EOF;

        $last_insert_id = DB::insert($sql, [
            static::$git_type, __FUNCTION__, $ref, $branch, $tag, $commit_id, $commit_message, $committer_name,
            $committer_email, $committer_username, $rid, $event_time, CI::BUILD_STATUS_PENDING, $content,
        ]);

        self::pushCache((int) $last_insert_id);
    }

    /**
     * Do Nothing.
     *
     * @param string $content
     *
     * @return array
     */
    public static function watch(string $content)
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
    public static function fork(string $content)
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
    public static function release(string $content)
    {
        return [
            'code' => 200
        ];
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @param string $content
     */
    public static function create(string $content): void
    {
        $obj = json_decode($content);

        $ref_type = $obj->ref_type;

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
    public static function delete(string $content)
    {
        $obj = json_decode($content);

        $ref_type = $obj->ref_type;

        $rid = $obj->repository->id;

        if ('branch' === $ref_type) {
            $sql = 'DELETE FROM builds WHERE git_type=? AND branch=? AND rid=?';

            return DB::delete($sql, [static::$git_type, $obj->ref, $rid]);
        } else {
            return 0;
        }
    }

    /**
     * @param string $ref
     *
     * @return mixed
     */
    public static function ref2branch(string $ref)
    {
        $ref_array = explode('/', $ref);

        return $ref_array[2];
    }

    /**
     * @param string $content
     */
    public static function member(string $content): void
    {
    }

    /**
     * @param string $content
     */
    public static function team_add(string $content): void
    {
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
    public static function installation(string $content)
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $installation_id = $obj->installation->id;

        // 可视为仓库管理员.

        $sender_id = $obj->sender->id;

        if ('created' === $action) {
            $repo = $obj->repositories;

            return self::installation_action_created($installation_id, $repo, $sender_id);
        }

        return self::installation_action_deleted($installation_id);
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
    private static function installation_action_created(int $installation_id, array $repo, int $sender_id)
    {
        foreach ($repo as $k) {
            // 仓库信息存入 repo 表
            $rid = $k->id;

            $repo_full_name = $k->full_name;

            list($repo_prefix, $repo_name) = explode('/', $repo_full_name);

            $sql = <<<EOF
INSERT INTO repo(

id,git_type,rid,repo_prefix,repo_name,repo_full_name,repo_admin,default_branch,installation_id,last_sync

) VALUES(null,'github_app',?,?,?,?,JSON_ARRAY(?),'master',?,?)

EOF;

            $output = DB::insert($sql, [
                    $rid, $repo_prefix, $repo_name, $repo_full_name, $sender_id, $installation_id, time(),
                ]
            );
        }

        return $output;
    }

    /**
     * @param int $installation_id
     *
     * @return int
     *
     * @throws Exception
     */
    private static function installation_action_deleted(int $installation_id)
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
            return self::installation_action_created($installation_id, $repo, $sender);
        }

        return self::installation_repositories_action_removed($installation_id, $repo);
    }

    /**
     * @param int   $installation_id
     * @param array $repo
     *
     * @return int
     *
     * @throws Exception
     */
    private static function installation_repositories_action_removed(int $installation_id, array $repo)
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
    public static function integration_installation(): void
    {
    }

    /**
     * @deprecated
     */
    public static function integration_installation_repositories(): void
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
     * @return array
     *
     * @throws Exception
     */
    public static function check_suite(string $content)
    {
        $obj = json_decode($content);

        $action = $obj->action;

        $check_suite = $obj->check_suite;

        $check_suite_id = $check_suite->id;

        $branch = $check_suite->head_branch;

        $commit_id = $check_suite->head_sha;

        $sql = <<<EOF
INSERT INTO builds(
action,event_type,git_type,check_suites_id,branch,commit_id
) VALUES (?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
            $action, __FUNCTION__, self::$git_type, $check_suite_id, $branch, $commit_id,
        ]);

        if ('rerequested' === $action) {
            $check_run_id = '';
        }

        return ['build_key_id' => $last_insert_id];
    }

    /**
     * Action.
     *
     * created updated rerequested
     *
     * @see https://developer.github.com/v3/activity/events/types/#checkrunevent
     */
    public static function check_run(string $content): void
    {
        $obj = json_encode($content);

        $action = $obj->action;

        if ('rerequested' === $action) {

            $check_run = $obj->check_run;

            $check_run_id = $check_run->id;

            $commit_id = $check_run->head_sha;

            $external_id = $check_run->external_id;

            $check_suite = $obj->check_suite;

            $check_suite_id = $check_suite->id;

            $branch = $check_suite->head_branch;

        }

        return;

    }
}

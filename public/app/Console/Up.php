<?php

/** @noinspection PhpUnusedLocalVariableInspection */

declare(strict_types=1);

namespace App\Console;

use App\Build;
use App\GetAccessToken;
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

    private static $cache_key = 'khsci_up_status';

    /**
     * @throws Exception
     */
    public static function up(): void
    {
        while (1) {
            try {
                if (1 === Cache::connect()->get(self::$cache_key)) {
                    echo "Wait sleep 2s ...\n\n";

                    sleep(2);

                    continue;
                }

                Cache::connect()->set(self::$cache_key, 1);

                // Queue::queue();

                $build_key_id = Cache::connect()->rPop('github_status');

                if ($build_key_id) {
                    self::updateGitHubStatus((int) $build_key_id);
                }

                $build_key_id = Cache::connect()->rpop('github_app_checks');

                if ($build_key_id) {
                    self::updateGitHubAppChecks((int) $build_key_id);
                }

                self::webhooks();

                echo "Finished sleep 2s ...\n\n";

                sleep(2);
            } catch (Exception | Error $e) {
                $errormsg = $e->getMessage().' || '.$e->getCode().PHP_EOL;
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

        Cache::connect()->set(self::$cache_key, 0);
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
                                                 int $started_at,
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

        $installation_id = Repo::getGitHubInstallationIdByRid((int) $rid);

        $khsci = new KhsCI();

        $access_token = $khsci->github_apps_installations->getAccessToken(
            (int) $installation_id,
            __DIR__.'/../../private_key/'.Env::get('CI_GITHUB_APP_PRIVATE_FILE')
        );

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

        $name = $name ?? 'Build Event is '.$event_type.' '.ucfirst($event_type);

        $status = $status ?? CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS;

        $title = $title ?? Env::get('CI_NAME').' Build is '.ucfirst($status);

        $summary = $summary ?? 'This Repository Build Powered By [KhsCI](https://github.com/khs1994-php/khsci)';

        $text = $text ?? <<<EOF
# About KhsCI ?

China First Support GitHub Checks API CI/CD System Powered By Docker and Tencent AI

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
            $repo_full_name, $name, $branch, $commit_id, $details_url, $build_key_id, $status,
            $started_at ?? time(),
            $completed_at, $conclusion, $title, $summary, $text, $annotations, $images
        );

        Log::connect()->debug($output);

        var_dump($output);

        $sql = 'UPDATE builds SET check_run_id=? WHERE id=?';

        DB::update($sql, [json_decode($output)->id ?? null, $build_key_id]);

        Cache::connect()->set('khsci_up_status', 0);
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
     * @param int $last_insert_id
     *
     * @throws Exception
     */
    private static function pushCache(int $last_insert_id): void
    {
        if ('github_app' === static::$git_type) {
            Cache::connect()->lPush('github_app_checks', $last_insert_id);

            return;
        }

        Cache::connect()->lPush('github_status', $last_insert_id);
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

        $rid = $obj->repository->id;

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
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public static function issues(string $content)
    {
        $obj = json_decode($content);
        /**
         * opened.
         */
        $action = $obj->action;
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
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public static function issue_comment(string $content)
    {
        $obj = json_decode($content);

        /**
         * created.
         */
        $action = $obj->action;

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
INSERT builds(

git_type,event_type,request_raw,action,commit_id,commit_message,committer_username,
pull_request_id,branch,rid,build_status

) VALUES(?,?,?,?,?,?,?,?,?,?,?);

EOF;
        $last_insert_id = DB::insert($sql, [
                static::$git_type, __FUNCTION__, $content, $action, $commit_id, $commit_message,
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
     * @param $content
     *
     * @return array
     */
    public static function watch($content)
    {
        $obj = json_decode($content);

        // started.

        $action = $obj->action;

        return [
            'code' => 200,
        ];
    }

    /**
     * Do Nothing.
     *
     * @param $content
     *
     * @return array
     */
    public static function fork($content)
    {
        $obj = json_decode($content);

        $forkee = $obj->forkee;

        return [
            'code' => 200,
        ];
    }

    public static function release(string $content): void
    {
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

        return 0;
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

        return DB::delete($sql, [
            'github_app', $installation_id,
        ]);
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

            DB::delete($sql, [$installation_id, $rid]);
        }

        return 0;
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
        }

        return ['build_key_id' => $last_insert_id];
    }

    /**
     * Action.
     *
     * created
     *
     * updated
     *
     * rerequested
     *
     * @see https://developer.github.com/v3/activity/events/types/#checkrunevent
     */
    public static function check_run(): void
    {
    }
}

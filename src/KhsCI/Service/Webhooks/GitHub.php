<?php

/** @noinspection PhpUnusedLocalVariableInspection */

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

use Error;
use Exception;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Request;

/**
 * Class GitHub.
 *
 * @see https://developer.github.com/webhooks/#events
 */
class GitHub
{
    private static $git_type = 'github';

    private static $access_token;

    public function __construct(string $access_token = null)
    {
        self::$access_token = $access_token;
    }

    /**
     * @throws Exception
     *
     * @return array
     */
    public function __invoke()
    {
        date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

        $signature = Request::getHeader('X-Hub-Signature');
        $type = Request::getHeader('X-Github-Event') ?? 'undefined';
        $content = file_get_contents('php://input');
        $secret = Env::get('CI_WEBHOOKS_TOKEN') ?? md5('khsci');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        // return $this->$type($content);

        if ($github_hash === $serverHash) {
            try {
                return $this->$type($content);
            } catch (Error | Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        throw new Exception('', 402);
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
     * @return string|array
     *
     * @throws Exception
     */
    public function push(string $content)
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref = $obj->ref;

        $ref_array = explode('/', $ref);

        if ('tags' === $ref_array[1]) {
            return $this->tag($ref_array[2], $content);
        }

        $branch = $this->ref2branch($ref);

        $commit_id = $obj->after;

        $compare = $obj->compare;

        $head_commit = $obj->head_commit;

        if (null === $head_commit) {
            return 0;
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

        $lastId = DB::insert($sql, $data);

        $sql = 'SELECT repo_full_name FROM repo WHERE git_type=? AND rid=?';

        $repo_full_name = DB::select($sql, [static::$git_type, $rid], true);

        $github_status = CI::GITHUB_STATUS_PENDING;

        $target_url = Env::get('CI_HOST').'/github/'.$repo_full_name.'/builds/'.$lastId;

        list($username, $repo_name) = explode('/', $repo_full_name);

        return [
            'rid' => $rid, [
                $username, $repo_name, $commit_id, $github_status, $target_url,
                'The analysis or builds is pending',
                'continuous-integration/'.Env::get('CI_NAME').'/'.__FUNCTION__,
            ],
        ];
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
    public function issues(string $content)
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
    public function issue_comment(string $content)
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
     * @return string
     *
     * @throws Exception
     */
    public function pull_request(string $content)
    {
        $obj = json_decode($content);

        // $event_time = '';

        $action = $obj->action;

        $pull_request = $obj->pull_request;

        $rid = $pull_request->base->repo->id;

        $commit_message = $pull_request->title;

        $commit_id = $pull_request->head->sha;

        $committer_username = $pull_request->user->login;

        $pull_request_id = $obj->number;

        $branch = $pull_request->base->ref;

        $sql = <<<'EOF'
INSERT builds(

git_type,event_type,request_raw,action,commit_id,commit_message,committer_username,
pull_request_id,branch,rid,build_status

) VALUES(?,?,?,?,?,?,?,?,?,?,?);

EOF;

        return DB::insert($sql, [
                static::$git_type, __FUNCTION__, $content, $action, $commit_id, $commit_message, $committer_username,
                $pull_request_id, $branch, $rid, CI::BUILD_STATUS_PENDING,
            ]
        );
    }

    /**
     * @param string $tag
     * @param string $content
     *
     * @return string
     *
     * @throws Exception
     */
    public function tag(string $tag, string $content)
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $ref = $obj->ref;

        $branch = $this->ref2branch($obj->base_ref);

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

        $last_id = DB::insert($sql, [
            static::$git_type, __FUNCTION__, $ref, $branch, $tag, $commit_id, $commit_message, $committer_name,
            $committer_email, $committer_username, $rid, $event_time, CI::BUILD_STATUS_PENDING, $content,
        ]);

        return $last_id;
    }

    /**
     * Do Nothing.
     *
     * @param $content
     *
     * @return array
     */
    public function watch($content)
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
    public function fork($content)
    {
        $obj = json_decode($content);

        $forkee = $obj->forkee;

        return [
            'code' => 200,
        ];
    }

    public function release(string $content): void
    {
    }

    /**
     * Create "repository", "branch", or "tag".
     *
     * @param string $content
     */
    public function create(string $content): void
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
    public function delete(string $content)
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
    }

    /**
     * @param string $content
     */
    public function team_add(string $content): void
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
    public function installation(string $content)
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
     * requested
     *
     * rerequested
     *
     *
     * @see https://developer.github.com/v3/activity/events/types/#checksuiteevent
     */
    public function check_suite(): void
    {
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
    public function check_run(): void
    {
    }
}

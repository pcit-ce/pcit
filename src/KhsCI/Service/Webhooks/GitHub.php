<?php

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

use Error;
use Exception;
use KhsCI\Support\CIConst;
use KhsCI\Support\DATE;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Request;

class GitHub
{
    /**
     * @throws Exception
     *
     * @return array
     */
    public function __invoke()
    {
        $signature = Request::getHeader('X-Hub-Signature');
        $type = Request::getHeader('X-Github-Event') ?? 'undefined';
        $content = file_get_contents('php://input');
        $secret = Env::get('WEBHOOKS_TOKEN') ?? md5('khsci');

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

        throw new \Exception('', 402);
    }

    /**
     * @param string $sql
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function insertDB(string $sql, array $data)
    {
        $pdo = DB::connect();

        $stmt = $pdo->prepare($sql);

        $stmt->execute($data);

        if ('00000' === $stmt->errorCode()) {
            return $pdo->lastInsertId();
        }

        $error_message = $stmt->errorInfo();

        throw new Exception($error_message[2] ?? '', 500);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function ping($content)
    {
        $obj = json_decode($content);

        $rid = $obj->repository->id;

        $event_time = time();

        $sql = <<<EOF
INSERT builds(

git_type,event_type,rid,event_time,request_raw

) VALUES(?,?,?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $rid, $event_time, $content
        ];

        return $this->insertDB($sql, $data);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function push($content)
    {
        $obj = json_decode($content);

        $ref = $obj->ref;

        $branch = explode('/', $ref)[2];

        $commit_id = $obj->after;

        $compare = $obj->compare;

        $head_commit = $obj->head_commit;

        $commit_message = $head_commit->message;

        $commit_timestamp = DATE::parse($head_commit->timestamp);

        $committer = $head_commit->committer;

        $committer_name = $committer->name;

        $committer_email = $committer->email;

        $committer_username = $committer->username;

        $rid = $obj->repository->id;

        $sql = <<<EOF
INSERT builds(

git_type,event_type,ref,branch,tag_name,compare,commit_id,commit_message,
committer_name,committer_email,committer_username,
rid,event_time,build_status,request_raw

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $ref, $branch, null, $compare, $commit_id,
            $commit_message, $committer_name, $committer_email, $committer_username,
            $rid, $commit_timestamp, CIConst::BUILD_STATUS_PENDING, $content
        ];
        return $this->insertDB($sql, $data);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function status($content)
    {
        $sql = <<<EOF
INSERT builds(

git_type,event_type,request_raw

) VALUES(?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $content
        ];
        return $this->insertDB($sql, $data);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function issues($content)
    {
        $obj = json_decode($content);
        /**
         * opened
         */
        $action = $obj->action;
        $sql = <<<EOF
INSERT builds(

git_type,event_type,request_raw

) VALUES(?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $content
        ];
        return $this->insertDB($sql, $data);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function issue_comment($content)
    {
        $obj = json_decode($content);

        /**
         * created
         */
        $action = $obj->action;

        $sql = <<<EOF
INSERT builds(

git_type,event_type,request_raw

) VALUES(?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $content
        ];

        return $this->insertDB($sql, $data);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function pull_request($content)
    {
        $obj = json_decode($content);

        /**
         * review_requested
         * assigned
         * labeled
         * synchronize
         */
        $action = $obj->action;

        $sql = <<<EOF
INSERT builds(

git_type,event_type,request_raw

) VALUES(?,?,?);
EOF;
        $data = [
            'github', __FUNCTION__, $content
        ];
        return self::insertDB($sql, $data);
    }

    /**
     * Do Nothing
     *
     * @param $content
     * @return array
     */
    public function watch($content)
    {
        $obj = json_decode($content);

        /**
         * started
         *
         */
        $action = $obj->action;

        return [
            'code' => 200,
        ];
    }

    /**
     * Do Nothing
     *
     * @param $content
     * @return array
     */
    public function fork($content)
    {
        $obj = json_decode($content);

        $forkee = $obj->forkee;

        return [
            "code" => 200
        ];
    }
}

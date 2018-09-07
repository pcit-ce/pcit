<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Issue extends DBModel
{
    /**
     * @param string $git_type
     * @param int    $issue_id
     * @param int    $comment_id
     * @param int    $updated_at
     * @param string $body
     *
     * @return int
     *
     * @throws Exception
     */
    public static function comment_edited(int $issue_id,
                                          int $comment_id,
                                          int $updated_at,
                                          string $body,
                                          string $git_type = 'github')
    {
        $sql = 'UPDATE issues SET body=?,updated_at=? WHERE git_type=? AND issue_id=? AND comment_id=?';

        return DB::update($sql, [
            $body,
            $updated_at,
            $git_type,
            $issue_id,
            $comment_id,
        ]);
    }

    /**
     * @param string $git_type
     * @param int    $issue_id
     * @param int    $comment_id
     * @param int    $deleted_at
     *
     * @return int
     *
     * @throws Exception
     */
    public static function comment_deleted(int $issue_id,
                                           int $comment_id,
                                           int $deleted_at,
                                           string $git_type = 'github')
    {
        $sql = 'UPDATE issues SET deleted_at=? WHERE git_type=? AND issue_id=? AND comment_id=?';

        return DB::update($sql, [
            $deleted_at,
            $git_type,
            $issue_id,
            $comment_id,
        ]);
    }

    /**
     * @param array  $labels
     * @param string $git_type
     * @param int    $issue_id
     *
     * @throws Exception
     */
    public static function updateLabels(array $labels,
                                        string $git_type,
                                        int $issue_id): void
    {
        DB::beginTransaction();

        foreach ($labels as $k) {
            $sql = 'UPDATE issues SET labels=JSON_MERGE_PRESERVE(labels,?) WHERE git_type=? AND issue_id=?';
            DB::update($sql, [$k, $git_type, $issue_id]);
        }

        DB::commit();
    }

    /**
     * @param array  $assignees
     * @param string $git_type
     * @param int    $issue_id
     *
     * @throws Exception
     */
    public static function updateAssignees(array $assignees,
                                           string $git_type,
                                           int $issue_id): void
    {
        DB::beginTransaction();

        foreach ($assignees as $k) {
            $sql = 'UPDATE issues SET assignees=JSON_MERGE_PRESERVE(labels,?) WHERE git_type=? AND issue_id=?';

            DB::update($sql, [$assignees, $git_type, $issue_id]);
        }

        DB::commit();
    }

    /**
     * @param $git_type
     * @param $rid
     * @param $issue_id
     * @param $issue_number
     * @param $action
     * @param $title
     * @param $body
     * @param $sender_username
     * @param $sender_uid
     * @param $sender_pic
     * @param $state
     * @param $locked
     * @param $created_at
     * @param $closed_at
     * @param $updated_at
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insert($rid,
                                  $issue_id,
                                  $issue_number,
                                  $action,
                                  $title,
                                  $body,
                                  $sender_username,
                                  $sender_uid,
                                  $sender_pic,
                                  $state,
                                  $locked,
                                  $created_at,
                                  $closed_at,
                                  $updated_at,
                                  string $git_type = 'github')
    {
        $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,issue_number,action,title,body,sender_username,sender_uid,sender_pic,
state,locked,created_at,closed_at,updated_at

) VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
                'github', $rid, $issue_id, $issue_number, $action, $title, $body,
                $sender_uid, $state, (int) $locked, $created_at, $closed_at, $updated_at,
            ]
        );

        User::updateUserInfo($sender_uid, null, $sender_username, null, $sender_pic, false, $git_type);

        return $last_insert_id;
    }

    /**
     * @param $git_type
     * @param $rid
     * @param $issue_id
     * @param $comment_id
     * @param $issue_number
     * @param $body
     * @param $sender_uid
     * @param $created_at
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insertComment($rid,
                                         $issue_id,
                                         $comment_id,
                                         $issue_number,
                                         $body,
                                         $sender_uid,
                                         $created_at,
                                         $git_type = 'github')
    {
        $sql = <<<'EOF'
INSERT INTO issues(

id,git_type,rid,issue_id,comment_id,issue_number,body,
sender_uid,created_at

) VALUES(null,?,?,?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
                $git_type, $rid, $issue_id, $comment_id, $issue_number, $body,
                $sender_uid, $created_at,
            ]
        );

        return $last_insert_id;
    }
}

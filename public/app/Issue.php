<?php

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
     * @throws Exception
     */
    public static function comment_edited(string $git_type,
                                          int $issue_id,
                                          int $comment_id,
                                          int $updated_at,
                                          string $body)
    {
        $sql = 'UPDATE issues SET body=?,updated_at=? WHERE git_type=? AND issue_id=? AND comment_id=?';

        return DB::update($sql, [
            $body,
            $updated_at,
            $git_type,
            $issue_id,
            $comment_id
        ]);
    }

    /**
     * @param string $git_type
     * @param int    $issue_id
     * @param int    $comment_id
     * @param int    $deleted_at
     *
     * @return int
     * @throws Exception
     */
    public static function comment_deleted(string $git_type,
                                           int $issue_id,
                                           int $comment_id,
                                           int $deleted_at)
    {
        $sql = 'UPDATE issues SET deleted_at=? WHERE git_type=? AND issue_id=? AND comment_id=?';

        return DB::update($sql, [
            $deleted_at,
            $git_type,
            $issue_id,
            $comment_id
        ]);
    }

    public static function updateLabels(string $label,
                                        string $git_type,
                                        int $issue_id)
    {
        $sql = 'UPDATE issues SET labels=JSON_MERGE_PRESERVE(labels,?) WHERE git_type=? AND issue_id=?';
    }

    public static function updateAssignees(string $assignees,
                                           string $git_type,
                                           int $issue_id)
    {
        $sql = 'UPDATE issues SET assignees=JSON_MERGE_PRESERVE(labels,?) WHERE git_type=? AND issue_id=?';
    }
}

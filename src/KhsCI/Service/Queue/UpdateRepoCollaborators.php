<?php

namespace KhsCI\Service\Queue;

use KhsCI\Support\DB;

/**
 * 后台更新已经启用构建的 Repo 的管理员和协作者信息
 *
 */
class UpdateRepoCollaborators
{
    /**
     * @param string $access_token
     *
     * @throws \Exception
     */
    public function __invoke(string $access_token)
    {
        $sql = 'SELECT id,git_type,repo_full_name FROM repo WHERE build_activate=?';

        $output = DB::select($sql, [1]);

        foreach ($output as $k => $array) {

            $json = '';

            $json_array = json_decode($json);

            foreach ($json_array as $k) {
                if ($k->permissions->admin ?? $k->permission->admin) {
                    $admin_array[] = $k->id;
                } else {
                    $collaborators_array[] = $k->id;
                }
            }

            $sql = 'UPDATE repo SET repo_admin="[]",repo_collaborators="[]" WHERE id=?';

            DB::update($sql, [$id]);

        }
    }
}

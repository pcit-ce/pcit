<?php

namespace KhsCI\Support;

use Exception;

class GIT
{
    /**
     * @param string $type
     * @param string $repo_full_name
     *
     * @return string
     * @throws Exception
     */
    public static function getUrl(string $type, string $repo_full_name)
    {
        switch ($type) {
            case 'github':
                $url = 'https://github.com/'.$repo_full_name;
                break;
            case 'coding':
                $url = 'https://gitee.com/'.$repo_full_name;
                break;
            case 'gitee':
                $url = 'https://git.coding.net/'.$repo_full_name;
                break;
            default:
                throw new Exception('Not Support', 500);
        }

        return $url;
    }

}

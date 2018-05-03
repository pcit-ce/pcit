<?php

namespace KhsCI\Support;

use Exception;

class GIT
{
    /**
     * @param string $type
     * @param string $repo_full_name
     *
     * @throws Exception
     */
    public static function getUrl(string $type, string $repo_full_name)
    {
        switch ($type) {
            case 'github':
                $url = 'https://github.com/'.$repo_full_name;
                break;
            case 'coding':
                $url = '';
                break;
            case 'gitee':
                $url = '';
                break;
            default:
                throw new Exception('Not Support', 500);
        }

        return $url;
    }

}

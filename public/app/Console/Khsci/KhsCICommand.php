<?php

namespace App\Console\Khsci;


class KhsCICommand
{
    public static function check(string $endpoints_url, string $git_type)
    {

    }

    public static function getConfigFileName()
    {
        if ('WINNT' === PHP_OS) {
            $home = 'C:/Users/'.exec('echo %username%');
        } else {
            $home = exec('echo $HOME');
        }

        $folder_name = $home.'/.khsci';

        if (!is_dir($folder_name)) {
            mkdir($folder_name);
        }

        return $file_name = $folder_name.'/config.json';
    }
}

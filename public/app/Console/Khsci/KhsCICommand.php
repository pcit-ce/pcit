<?php

declare(strict_types=1);

namespace App\Console\Khsci;

use Exception;
use KhsCI\Support\Env;
use Symfony\Component\Console\Input\InputOption;

class KhsCICommand
{
    public static function check(string $endpoints_url, string $git_type): void
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

    public static function getGitTypeOptionArray()
    {
        return ['git_type',
            'g',
            InputOption::VALUE_OPTIONAL,
            'Git Type',
            'github_app',
        ];
    }

    public static function getAPIEndpointOptionArray()
    {
        return [
            'api-endpoint',
            'e',
            InputOption::VALUE_OPTIONAL,
            'KhsCI API server to talk to', Env::get('CI_HOST', 'https://ci.khs1994.com'),
        ];
    }

    /**
     * @return bool
     */
    public static function configExists()
    {
        return file_exists(self::getConfigFileName());
    }

    /**
     * @param string $endpoints_url
     * @param string $git_type
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function get(string $endpoints_url, string $git_type)
    {
        if (!self::configExists()) {
            throw new Exception('Not Found', 404);
        }

        $array = json_decode(file_get_contents(self::getConfigFileName()), true);

        $token = $array['endpoints'][$endpoints_url][$git_type];

        if ($token) {
            return $token;
        }

        throw new Exception('Not Found', 404);
    }
}

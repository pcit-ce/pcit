<?php

declare(strict_types=1);

namespace App\Console\KhsCI;

use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class KhsCICommand
{
    private static $curl;

    public static function check(string $endpoints_url, string $git_type): void
    {
    }

    public static function getConfigFileName()
    {
        if ('WINNT' === PHP_OS) {
            $home = getenv('USERPROFILE');
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
            'github',
        ];
    }

    public static function getRepoOptionArray()
    {
        return [
            'repo',
            'r',
            InputOption::VALUE_REQUIRED,
            'Repository to use (will try to detect from current git clone) <comment>[example: "khs1994-php/khsci"]</comment>',
        ];
    }

    public static function getAPIEndpointOptionArray()
    {
        return [
            'api-endpoint',
            'e',
            InputOption::VALUE_OPTIONAL,
            'KhsCI API server to talk to',
            Env::get('CI_HOST', 'https://ci.khs1994.com'),
        ];
    }

    public static function getRawOptionArray()
    {
        return [
            'raw',
            null,
            InputOption::VALUE_NONE,
            'output raw',
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
     * @param InputInterface $input
     * @param bool           $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getToken(InputInterface $input, bool $header = true)
    {
        if (!self::configExists()) {
            throw new Exception('Not Found', 404);
        }

        $array = json_decode(file_get_contents(self::getConfigFileName()), true);

        $git_type = $input->getOption('git_type');

        $endpoints_url = $input->getOption('api-endpoint');

        $token = $array['endpoints'][$endpoints_url][$git_type] ?? null;

        if (!$token) {
            throw new Exception('Please exec login command first');
        }

        if ($header) {
            return ['Authorization' => "token $token"];
        }

        if ($token) {
            return $token;
        }

        throw new Exception('Not Found', 404);
    }

    private static function getCurl()
    {
        if (!(self::$curl instanceof Curl)) {
            self::$curl = new Curl();
        }

        return self::$curl;
    }

    /**
     * @param InputInterface $input
     * @param string         $entrypoint
     * @param string         $data
     * @param bool           $auth
     * @param int            $target_code
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function HttpGet(InputInterface $input,
                                   string $entrypoint,
                                   ?string $data,
                                   bool $auth = false,
                                   int $target_code = 200)
    {
        $endpoints_url = $input->getOption('api-endpoint');

        $header = [];

        $auth && $header = self::getToken($input);

        $url = $endpoints_url.'/api/'.$entrypoint;

        $output = self::getCurl()->get($url, $data, $header);

        if ($target_code === self::getCurl()->getCode()) {
            return $output;
        }

        throw new Exception($output, 500);
    }

    /**
     * @param InputInterface $input
     * @param string         $entrypoint
     * @param string         $data
     * @param bool           $auth
     * @param bool           $json
     * @param int            $target_code
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function HttpPost(InputInterface $input,
                                    string $entrypoint,
                                    ?string $data,
                                    bool $auth = false,
                                    bool $json = false,
                                    int $target_code = 200)
    {
        $endpoints_url = $input->getOption('api-endpoint');

        $header = [];

        $auth && $header = self::getToken($input);
        $json && $header = array_merge($header, ['Content-Type' => 'application/json']);

        $output = self::getCurl()->post($endpoints_url.'/api/'.$entrypoint, $data, $header);

        if ($target_code === self::getCurl()->getCode()) {
            return $output;
        }

        throw new Exception($output, 500);
    }

    /**
     * @param InputInterface $input
     * @param string         $entrypoint
     * @param string         $data
     * @param bool           $auth
     * @param int            $target_code
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function HttpDelete(InputInterface $input,
                                      string $entrypoint,
                                      ?string $data,
                                      bool $auth = false,
                                      int $target_code = 200)
    {
        $endpoints_url = $input->getOption('api-endpoint');

        $header = [];

        $auth && $header = self::getToken($input);

        $output = self::getCurl()->delete($endpoints_url.'/api/'.$entrypoint, $data, $header);

        if ($target_code === self::getCurl()->getCode()) {
            return $output;
        }

        throw new Exception($output, 500);
    }

    /**
     * @param InputInterface $input
     * @param string         $entrypoint
     * @param string         $data
     * @param bool           $auth
     * @param bool           $json
     * @param int            $target_code
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function HttpPatch(InputInterface $input,
                                     string $entrypoint,
                                     ?string $data,
                                     bool $auth = false,
                                     bool $json = false,
                                     int $target_code = 200)
    {
        $endpoints_url = $input->getOption('api-endpoint');

        $header = [];

        $auth && $header = self::getToken($input);
        $json && $header = array_merge($header, ['Content-Type' => 'application/json']);

        $url = $endpoints_url.'/api/'.$entrypoint;

        $output = self::getCurl()->patch($url, $data, $header);

        if ($target_code === self::getCurl()->getCode()) {
            return $output;
        }

        throw new Exception($output, 500);
    }

    /**
     * 通过调用系统 git 命令获取仓库名称 -r.
     *
     * @return bool|mixed|string
     */
    public static function getGitByExecCommand()
    {
        ob_start();
        $system_result = system('git remote get-url origin', $return);
        ob_end_clean();

        if (0 !== $return) {
            return false;
        }

        if (preg_match('/@/', $system_result)) {
            $result_array = explode(':', $system_result);
            $result = array_pop($result_array);

            return $result;
        }

        $result_array = explode('/', $system_result);

        $repo = array_pop($result_array);
        $username = array_pop($result_array);
        $result = $username.'/'.$repo;

        return $result;
    }

    /**
     * @param InputInterface $input
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function existsRepoOption(InputInterface $input)
    {
        $repo = $input->getOption('repo') ?? self::getGitByExecCommand();

        if ($repo) {
            return $repo;
        }

        throw new Exception(
            'Please specify the repo name via the -r option (e.g. khsci <command> -r <owner>/<repo>)',
            500
        );
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Service\Build;

use Exception;
use PCIT\Support\Log;

class Parse
{
    /**
     * 解析 镜像名 中包含的 变量.
     *
     * @param string $image
     * @param array  $config
     *
     * @return array|mixed|string
     *
     * @throws Exception
     */
    public static function image(string $image, ?array $config)
    {
        Log::debug(__FILE__, __LINE__, 'Parse Image '.$image, [], Log::EMERGENCY);

        $arg = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $image, $output);

        if ($arg) {
            foreach ($output[0] as $k) {
                // ${XXX} -> md5('pcit')

                $var_secret = md5('pcit');

                $image = str_replace($k, $var_secret, $image);

                $array = explode('}', $k);

                $k = trim($array[0], '${');

                $var = '';

                if (\in_array($k, array_keys($config), true)) {
                    $var = $config["$k"];
                }

                $image = str_replace($var_secret, $var, $image);
            }
        }
        Log::debug(__FILE__, __LINE__, 'Parse Image output is '.$image, [], Log::EMERGENCY);

        return $image;
    }

    /**
     * @param string     $setup
     * @param string     $image
     * @param array|null $commands
     *
     * @return string
     *
     * @throws Exception
     */
    public static function command(string $setup, string $image, ?array $commands)
    {
        if (null === $commands) {
            return null;
        }

        $content = '\n';

        $content .= 'echo;echo\n\necho "==>" Pipeline '.$setup.' Run On "=>" '.$image;

        $content .= '\n\nsleep 0.1;echo;echo\n\nset -x\n\n';

        for ($i = 0; $i < \count($commands); ++$i) {
            $command = addslashes($commands[$i]);

            $content .= $command;

            $content .= '\n\n';
        }

        // var_dump(stripcslashes($content));

        $ci_script = base64_encode(stripcslashes($content));

        Log::debug(__FILE__, __LINE__, 'Command base64encode is '.$ci_script, [], Log::EMERGENCY);

        return $ci_script;
    }
}

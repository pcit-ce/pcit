<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Exception;
use KhsCI\Support\Log;

class ParseClient
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
                // ${XXX} -> md5('KHSCI')

                $var_secret = md5('KHSCI');

                $image = str_replace($k, $var_secret, $image);

                $array = explode('}', $k);

                $k = trim($array[0], '${');

                $var = '';

                if (in_array($k, array_keys($config), true)) {
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

        $content .= 'echo;echo\n\n';

        $content .= 'echo Start Build in '.$setup.' "=>" '.$image;

        $content .= '\n\necho;echo\n\n';

        for ($i = 0; $i < count($commands); ++$i) {
            $command = addslashes($commands[$i]);

            $content .= 'echo "$ '.str_replace('$', '\\\\$', $command).'"\n\n';

            $content .= 'echo;echo\n\n';

            $content .= str_replace('$$', '$', $command).'\n\n';

            $content .= 'echo;echo\n\n';
        }

        $ci_script = base64_encode(stripcslashes($content));

        Log::debug(__FILE__, __LINE__, 'Command base64encode is '.$ci_script, [], Log::EMERGENCY);

        return $ci_script;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Builder;

use Exception;
use PCIT\Support\Log;

class Parse
{
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

    /**
     * @param string $text
     * @param        $env  <pre> ['K=V','K2=V2'] <pre>
     *
     * @return string
     */
    public static function text(string $text, $env)
    {
        $pregResult = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $text, $array);

        if (!$pregResult) {
            return $text;
        }

        $result = &$text;

        foreach ($array[0] as $prekey) {
            $varName = trim($prekey, '$');
            $varName = trim($varName, '{');
            $varName = trim($varName, '}');

            $fromEnv = preg_grep("/$varName=*/", $env);

            if (!$fromEnv) {
                continue;
            }

            $varValue = explode('=', array_values($fromEnv)[0])[1];

            $result = str_replace($prekey, $varValue, $result);
        }

        return $result;
    }
}

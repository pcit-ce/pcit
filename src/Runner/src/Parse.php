<?php

declare(strict_types=1);

namespace PCIT\Builder;

class Parse
{
    /**
     * @param $env <pre> ['K=V','K2=V2'] <pre>
     *
     * @return string
     */
    public static function text(string $text, $env)
    {
        $pregResultInt = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $text, $pregResultArray);

        if (!$pregResultInt) {
            return $text;
        }

        $result = &$text;

        foreach ($pregResultArray[0] as $prekey) {
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

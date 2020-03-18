<?php

declare(strict_types=1);

namespace PCIT\Runner\Events\Handler;

class TextHandler
{
    /**
     * replace $text '${VAR}' with $env ['VAR=value'] 或 ['VAR'=>'value'].
     *
     * @param array $env ['VAR=value'] 或 ['VAR'=>'value']
     */
    public function handle(string $text, ?array $env): string
    {
        $env = (new EnvHandler())->obj2array($env);

        $pregResultInt = preg_match_all('/\${[0-9a-zA-Z_-]*\}/', $text, $pregResultArray);

        if (!$pregResultInt || !$env) {
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

    /**
     * replaced array item ${VAR} with $env.
     *
     * @param array<string> $arr ['k=${VAR}']
     */
    public function handleArray(?array $arr, ?array $env): array
    {
        if (!$arr) {
            return [];
        }

        $arr_replaced = [];

        foreach ($arr as $item) {
            $arr_replaced[] = $this->handle($item, $env);
        }

        return $arr_replaced;
    }
}

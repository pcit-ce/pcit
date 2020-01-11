<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\Runner\Parser\TextHandler as TextParser;

class EnvHandler
{
    /**
     * @param $pre_env ['k'=>'v']
     * @param $env      SystemEnv ['k=v']
     * @param string $prefix  key prefix
     * @param bool   $replace replace '-' with '_' on key
     *
     * @return array ['k=v']
     *
     * @throws Exception
     */
    public function handle($pre_env, $env, string $prefix = '', bool $replace = false): array
    {
        if (!$pre_env) {
            return [];
        }
        $text = json_encode($pre_env);

        // ${} 变量替换
        $result = (new TextParser())->handle($text, $env);

        $pre_env = json_decode($result, true);

        foreach ($pre_env as $key => $value) {
            $new_value = null;

            if (\is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k) {
                        // is obj
                        $value = json_encode($value);
                        break;
                    } else {
                        // is array
                        $value = $this->arrayHandler($value);
                        break;
                    }
                }
            }

            $value = $new_value ?? $value;

            if (\is_bool($value)) {
                $value = true === $value ? 'true' : 'false';
            }

            if ($replace) {
                $key = strtoupper(str_replace('-', '_', $key));
            }

            if ($prefix) {
                $key = $prefix.'_'.$key;
            }

            $env[] = $key.'='.$value;
        }

        return $env;
    }

    public function arrayHandler($value)
    {
        if (\is_string($value)) {
            return $value;
        }
        $new_value = null;
        foreach ($value as $k) {
            $new_value .= $k.',';
        }

        return rtrim($new_value, ',');
    }
}

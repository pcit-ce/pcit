<?php

declare(strict_types=1);

namespace PCIT\Runner\Events\Handler;

class EnvHandler
{
    /**
     * @param array  $env_with_var ['k'=>'${K}'] 或 ['k=${K}']
     * @param array  $env          SystemEnv ['k'=>'v'] 或 ['k=v']
     * @param string $prefix       key prefix
     * @param bool   $replace      replace '-' with '_' on key
     *
     * @throws \Exception
     *
     * @return array ['k=v']
     */
    public function handle($env_with_var, $env, string $prefix = '', bool $replace = false): array
    {
        if (!$env_with_var) {
            return [];
        }

        $env = $this->obj2array($env);
        $text = json_encode($this->array2obj($env_with_var));

        // ${} 变量替换
        $result = (new TextHandler())->handle($text, $env);

        $pre_env = json_decode($result, true);

        foreach ($pre_env as $key => $value) {
            $new_value = null;

            if (\is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k) {
                        // is obj
                        $value = json_encode($value);

                        break;
                    }
                    // is array
                    $value = $this->array2str($value);

                    break;
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

    /**
     * conver [a,b,c] to a,b,c.
     *
     * @param mixed $value
     */
    public function array2str($value): string
    {
        if (\is_string($value)) {
            return $value;
        }

        $new_array = [];

        foreach ($value as $item) {
            $item = str_replace('%', '%25', $item);
            $item = str_replace(',', '%2C', $item);
            $new_array[] = $item;
        }

        return implode(',', $new_array);
    }

    /**
     * conver ['k'=>'v'] to ['k=v'].
     */
    public function obj2array(?array $env)
    {
        if (!$env) {
            return $env;
        }

        $env_after = [];

        foreach ($env as $k => $v) {
            if (0 === $k) {
                return $env;
            }

            $env_after[] = $k.'='.$v;
        }

        return $env_after;
    }

    /**
     * conver ['k=v','k2=v2'] to ['k'=>'v','k2'=>'v2'].
     */
    public function array2obj(?array $env)
    {
        if (!$env) {
            return $env;
        }

        $env_after = [];

        foreach ($env as $k => $v) {
            if (0 !== $k) {
                return $env;
            }

            break;
        }

        foreach ($env as $k => $v) {
            [$key,$value] = explode('=', $v, 2);

            $env_after[$key] = $value;
        }

        return $env_after;
    }
}

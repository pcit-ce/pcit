<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\Runner\Parse;

class PluginHandler
{
    /**
     * @param $settings
     * @param $env      SystemEnv
     *
     * @throws Exception
     */
    public function handle($settings, $env): array
    {
        $text = json_encode($settings);

        // ${} 变量替换
        $result = Parse::text($text, $env);

        $settings = json_decode($result, true);

        foreach ($settings as $key => $value) {
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

            $key = str_replace('-', '_', $key);

            $env[] = 'INPUT_'.strtoupper($key).'='.$value;
        }

        return $env;
    }

    public function arrayHandler($value)
    {
        return (new EnvHandler())->arrayHandler($value);
    }
}

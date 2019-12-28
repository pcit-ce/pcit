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
            $value = \is_array($value) ? json_encode($value) : $value;
            $key = str_replace('-', '_', $key);

            if (\is_bool($value)) {
                $value = true === $value ? 'true' : 'false';
            }

            $env[] = 'INPUT_'.strtoupper($key).'='.$value;
        }

        return $env;
    }
}

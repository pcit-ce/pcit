<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

use PCIT\Builder\Parse;

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
            $env[] = 'INPUT_'.strtoupper($key).'='.$value;
        }

        return $env;
    }
}

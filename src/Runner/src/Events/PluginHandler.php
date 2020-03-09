<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

class PluginHandler
{
    /**
     * @param $settings ['k'=>'v']
     * @param $env      SystemEnv ['k=v']
     *
     * @throws \Exception
     */
    public function handleSettings($settings, $env): array
    {
        return (new EnvHandler())->handle($settings, $env, 'INPUT', true);
    }
}

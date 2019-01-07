<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Events;

use PCIT\Support\DB;

/**
 * 获取服务的默认配置.
 */
class ServiceDefault
{
    private static $service;

    public static function handle($service)
    {
        self::$service = $service;

        $image = self::getImage();
        $env = self::getEnv();
        $entrypoint = self::getEntrypoint();
        $commands = self::getCommands();

        return compact('image', 'env', 'entrypoint', 'commands');
    }

    /**
     * @param string $type
     *
     * @return string|array|null
     *
     * @throws \Exception
     */
    private static function kernel(string $type)
    {
        $sql = "SELECT $type FROM default_services WHERE service=?";
        $preResult = DB::select($sql, [self::$service]);

        try {
            $result = $preResult[0][$type];

            // $result = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
            $output = json_decode($result, true, 512);

            if (null === $output) {
                throw new \Exception('');
            }

            return $output === [] ? null : $output;
            // } catch (\JsonException $e) {
        } catch (\Throwable $e) {
            return $result;
        }
    }

    private static function getImage(): string
    {
        return self::kernel('image');
    }

    private static function getEnv(): ?array
    {
        return self::kernel('env');
    }

    private static function getEntrypoint(): ?array
    {
        return self::kernel('entrypoint');
    }

    private static function getCommands(): ?array
    {
        return self::kernel('commands');
    }
}
